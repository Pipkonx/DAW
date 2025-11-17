<?php

namespace App\Models;


require_once __DIR__ . '/../../seeders/database.php';

class Finanzas {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function registrarMovimiento(int $idUsuario, string $tipo, float $monto, ?string $descripcion, string $fecha): array {
        $tipo = strtolower($tipo);
        if (!in_array($tipo, ['ingreso', 'gasto'], true)) {
            return ['success' => false, 'error' => 'Tipo inválido'];
        }
        $stmt = $this->db->prepare('INSERT INTO finanzas (id_usuario, tipo, monto, descripcion, fecha_registro) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$idUsuario, $tipo, $monto, $descripcion, $fecha]);
        $id = (int)$this->db->lastInsertId();
        return ['success' => true, 'id' => $id];
    }

    public function obtenerMovimientosPorUsuario(int $idUsuario): array {
        $stmt = $this->db->prepare('SELECT id, tipo, monto, descripcion, fecha_registro FROM finanzas WHERE id_usuario = ? ORDER BY fecha_registro DESC');
        $stmt->execute([$idUsuario]);
        $rows = $stmt->fetchAll();
        return array_map(function ($r) {
            return [
                'id' => (int)$r['id'],
                'tipo' => $r['tipo'],
                'monto' => (float)$r['monto'],
                'descripcion' => $r['descripcion'],
                'fecha_registro' => $r['fecha_registro'],
            ];
        }, $rows);
    }

    public function obtenerResumenAnual(int $idUsuario, int $anio): array {
        $stmt = $this->db->prepare(
            'SELECT 
                SUM(CASE WHEN tipo = "ingreso" THEN monto ELSE 0 END) AS ingresos,
                SUM(CASE WHEN tipo = "gasto" THEN monto ELSE 0 END) AS gastos
             FROM finanzas
             WHERE id_usuario = ? AND YEAR(fecha_registro) = ?'
        );
        $stmt->execute([$idUsuario, $anio]);
        $res = $stmt->fetch();
        $ingresos = (float)($res['ingresos'] ?? 0);
        $gastos = (float)($res['gastos'] ?? 0);
        $ahorro = $ingresos - $gastos;
        return [
            'ingresos' => $ingresos,
            'gastos' => $gastos,
            'ahorro' => $ahorro
        ];
    }

    public function obtenerResumenMensual(int $idUsuario, int $anio): array {
        $stmt = $this->db->prepare(
            'SELECT MONTH(fecha_registro) AS mes,
                    SUM(CASE WHEN tipo = "ingreso" THEN monto ELSE 0 END) AS ingresos,
                    SUM(CASE WHEN tipo = "gasto" THEN monto ELSE 0 END) AS gastos
             FROM finanzas
             WHERE id_usuario = ? AND YEAR(fecha_registro) = ?
             GROUP BY MONTH(fecha_registro)
             ORDER BY mes'
        );
        $stmt->execute([$idUsuario, $anio]);
        $rows = $stmt->fetchAll();
        // Mapear meses 1..12
        $mensual = array_fill(1, 12, ['ingresos' => 0.0, 'gastos' => 0.0]);
        foreach ($rows as $r) {
            $mes = (int)$r['mes'];
            $mensual[$mes] = [
                'ingresos' => (float)$r['ingresos'],
                'gastos' => (float)$r['gastos'],
            ];
        }
        return $mensual;
    }
}