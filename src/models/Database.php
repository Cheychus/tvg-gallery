<?php

namespace models;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;

    /**
     * Singleton for Database object
     * @return PDO - sqlite pdo object
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            try {
                self::$instance = new PDO('sqlite:' . __DIR__ . '/../../database/imageDatabase.db');
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                echo "Datenbankfehler: " . $e->getMessage();
                exit;
            }
        }
        return self::$instance;
    }
}