<?php
require_once "config/Database.php";

class Task
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll()
    {
        $stmt = $this->db->query("SELECT * FROM tareas ORDER BY fecha_creacion DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByOperario($operario)
    {
        $stmt = $this->db->prepare("SELECT * FROM tareas WHERE operario = ?");
        $stmt->execute([$operario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM tareas WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $sql = "INSERT INTO tareas (nif_cif, persona_contacto, telefono, descripcion, correo, direccion, poblacion, codigo_postal, provincia, estado, operario, fecha_realizacion, anotaciones_antes)
                VALUES (:nif_cif, :persona_contacto, :telefono, :descripcion, :correo, :direccion, :poblacion, :codigo_postal, :provincia, :estado, :operario, :fecha_realizacion, :anotaciones_antes)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    public function update($id, $data)
    {
        $sql = "UPDATE tareas SET persona_contacto=:persona_contacto, telefono=:telefono, descripcion=:descripcion,
                correo=:correo, direccion=:direccion, poblacion=:poblacion, codigo_postal=:codigo_postal,
                provincia=:provincia, estado=:estado, fecha_realizacion=:fecha_realizacion,
                anotaciones_antes=:anotaciones_antes, anotaciones_despues=:anotaciones_despues,
                fichero_resumen=:fichero_resumen, fotos=:fotos WHERE id=:id";
        $stmt = $this->db->prepare($sql);
        $data['id'] = $id;
        return $stmt->execute($data);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM tareas WHERE id=?");
        return $stmt->execute([$id]);
    }
}
