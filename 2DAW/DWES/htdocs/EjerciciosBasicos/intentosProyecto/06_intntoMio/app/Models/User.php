<?php

namespace App\Models;

use App\Db\DB;

class User
{
    public static function findByUsername($username)
    {
        $db = DB::getInstance();
        return $db->fetch("SELECT * FROM users WHERE username = ?", [$username]);
    }

    public static function all()
    {
        $db = DB::getInstance();
        return $db->fetchAll("SELECT * FROM users");
    }

    public static function create($data)
    {
        $db = DB::getInstance();
        $db->query("INSERT INTO users (username, password_hash, role) VALUES (?, ?, ?)", [
            $data['username'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['role']
        ]);
        return $db->lastInsertId();
    }
}
