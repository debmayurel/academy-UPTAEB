<?php

declare(strict_types=1);

use App\Config\DatabaseConnectionFactory;

require dirname(__DIR__) . '/vendor/autoload.php';

$driver = DatabaseConnectionFactory::getDriver();
$pdo = DatabaseConnectionFactory::create();
$schemaFile = $driver === 'mysql' ? 'schema.mysql.sql' : 'schema.sql';
$schemaPath = __DIR__ . '/' . $schemaFile;
$schema = file_get_contents($schemaPath);

if ($schema === false) {
    throw new RuntimeException("Unable to read database schema: {$schemaPath}");
}

$pdo->exec($schema);

$email = 'admin@beba.com';
$findUser = $pdo->prepare('SELECT 1 FROM users WHERE email = :email');
$findUser->execute(['email' => $email]);

if ($findUser->fetchColumn() === false) {
    $seedUser = $pdo->prepare(
        'INSERT INTO users (email, password_hash) VALUES (:email, :password_hash)'
    );
    $seedUser->execute([
        'email' => $email,
        'password_hash' => password_hash('123456', PASSWORD_DEFAULT),
    ]);
}

fwrite(STDOUT, "Database initialized using {$driver}.\n");
