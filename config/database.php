<?php

class Database
{
    private $host;
    private $db;
    private $user;
    private $password;
    private $charset = 'utf8mb4';
    private $connection;

    public function __construct()
    {
        // Load from .env file or use Constants from config.php
        $this->host = getenv('DB_HOST') ?: (defined('DB_HOST') ? DB_HOST : 'localhost');
        $this->db = getenv('DB_NAME') ?: (defined('DB_NAME') ? DB_NAME : 'Bright_Database');
        $this->user = getenv('DB_USER') ?: (defined('DB_USER') ? DB_USER : 'root');
        $this->password = getenv('DB_PASSWORD') ?: (defined('DB_PASS') ? DB_PASS : '');
    }

    public function connect()
    {
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->db . ';charset=' . $this->charset;

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            $this->connection = new PDO($dsn, $this->user, $this->password, $options);
            return $this->connection;
        } catch (PDOException $e) {
            die('Connection Error: ' . $e->getMessage());
        }
    }

    public function disconnect()
    {
        $this->connection = null;
    }
}
