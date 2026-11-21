<?php

namespace App\Config;

use Dotenv\Dotenv;
use PDO;
use RuntimeException;

class DatabaseConnectionFactory
{
    public static function getDriver(): string
    {
        self::loadEnvironment();

        return strtolower(self::getEnvironmentVariable('DB_CONNECTION', 'sqlite'));
    }

    public static function create(): PDO
    {
        $projectRoot = dirname(__DIR__, 2);
        self::loadEnvironment();

        $driver = self::getDriver();

        if ($driver === 'sqlite') {
            $databasePath = self::getEnvironmentVariable('DB_SQLITE_PATH', 'database/login.db');
            if ($databasePath === '') {
                throw new RuntimeException('DB_SQLITE_PATH cannot be empty.');
            }

            if (!str_starts_with($databasePath, '/') && !preg_match('/^[A-Za-z]:[\\\\\\/]/', $databasePath)) {
                $databasePath = $projectRoot . '/' . $databasePath;
            }

            $dsn = 'sqlite:' . $databasePath;
            $username = null;
            $password = null;
        } elseif ($driver === 'mysql') {
            $host = self::getEnvironmentVariable('DB_HOST', '127.0.0.1');
            $port = filter_var(
                self::getEnvironmentVariable('DB_PORT', '3306'),
                FILTER_VALIDATE_INT,
                ['options' => ['min_range' => 1, 'max_range' => 65535]]
            );
            $database = self::getEnvironmentVariable('DB_DATABASE', '');
            $username = self::getEnvironmentVariable('DB_USERNAME', '');
            $password = self::getEnvironmentVariable('DB_PASSWORD', '');

            if ($host === '' || $port === false || $database === '' || $username === '') {
                throw new RuntimeException('MySQL requires valid DB_HOST, DB_PORT, DB_DATABASE, and DB_USERNAME settings.');
            }

            $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";
        } else {
            throw new RuntimeException("Unsupported DB_CONNECTION '{$driver}'. Use 'sqlite' or 'mysql'.");
        }

        return new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }

    private static function loadEnvironment(): void
    {
        Dotenv::createImmutable(dirname(__DIR__, 2))->safeLoad();
    }

    private static function getEnvironmentVariable(string $name, string $default): string
    {
        $value = $_SERVER[$name] ?? $_ENV[$name] ?? getenv($name);

        return is_string($value) ? $value : $default;
    }
}
