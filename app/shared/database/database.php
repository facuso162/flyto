<?php

namespace App\Shared\Database;

use PDO;
use App\Shared\Config\Env;

require_once __DIR__ . '/../config/env.php';

class Database
{
    public static function getConnection(): PDO {
        $host = Env::env('DB_HOST', 'localhost');
        $db = Env::env('DB_NAME', 'flyto');
        $user = Env::env('DB_USER', 'root');
        $pass = Env::env('DB_PASS', '');
        $charset = 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        return new PDO($dsn, $user, $pass, $options);
    }
}
