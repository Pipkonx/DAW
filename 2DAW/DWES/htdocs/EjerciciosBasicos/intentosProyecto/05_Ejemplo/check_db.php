<?php

$databasePath = 'c:\\Users\\Rafael\\Desktop\\DAW\\2DAW\\DWES\\htdocs\\EjerciciosBasicos\\intentosProyecto\\05_Ejemplo\\database\\database.sqlite';

try {
    $pdo = new PDO('sqlite:' . $databasePath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $result = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='sessions';");
    $tableExists = $result->fetchColumn();

    if ($tableExists) {
        echo "Table 'sessions' exists.\n";
        $schemaResult = $pdo->query("PRAGMA table_info(sessions);");
        $schema = $schemaResult->fetchAll(PDO::FETCH_ASSOC);
        echo "Schema for 'sessions' table:\n";
        print_r($schema);
    } else {
        echo "Table 'sessions' does not exist.\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

?>