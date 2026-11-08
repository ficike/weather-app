<?php

declare(strict_types=1);

namespace ADS\TakeHome\Tests;

use ADS\TakeHome\Framework\Config\Config;
use ADS\TakeHome\Framework\Database\MySQL;
use PDO;
use PHPUnit\Framework\TestCase;
use RuntimeException;

abstract class DatabaseIntegrationTestCase extends TestCase
{
    protected PDO $pdo;
    protected MySQL $db;

    protected function setUp(): void
    {
        $database = Config::load(__DIR__ . '/../config')->get('database.mysql');
        $host = $database['host'];
        $schema = $database['schema'] . '_test';
        $username = $database['username'];
        $password = $database['password'];
        $port = $database['port'];

        if (preg_match('/^weather_test[a-zA-Z0-9_]*$/', $schema) !== 1) {
            throw new RuntimeException('Az integrációs tesztek csak weather_test kezdetű adatbázissémát használhatnak.');
        }

        $server = new PDO(
            "mysql:host={$host};port={$port};charset=utf8mb4",
            $username,
            $password,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION],
        );
        $server->exec("CREATE DATABASE IF NOT EXISTS `{$schema}` CHARACTER SET utf8mb4 COLLATE utf8mb4_hungarian_ci");
        $this->pdo = new PDO(
            "mysql:host={$host};port={$port};dbname={$schema};charset=utf8mb4",
            $username,
            $password,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION],
        );
        $this->db = new MySQL($host, $schema, $username, $password, $port);

        $this->pdo->exec('CREATE TABLE IF NOT EXISTS cities (
            id int primary key,
            name varchar(255) not null,
            region enum("Dunántúl", "Közép-Magyarország", "Észak és Alföld") not null
        )');
        $this->pdo->exec('CREATE TABLE IF NOT EXISTS maximum_temperatures (
            city_id int not null, date bigint not null, max_temp float not null
        )');
        $this->pdo->exec('CREATE TABLE IF NOT EXISTS minimum_temperatures (
            city_id int not null, date bigint not null, min_temp float not null
        )');
        $this->pdo->exec('CREATE TABLE IF NOT EXISTS precipitation (
            city_id int not null, date bigint not null, precipitation float not null
        )');
    }

    protected function seedWeatherData(): void
    {
        $this->cleanWeatherData();

        $this->pdo->exec("INSERT INTO cities (id, name, region) VALUES (987654321, 'Integrációs tesztváros', 'Dunántúl')");
        $this->pdo->exec("INSERT INTO maximum_temperatures (city_id, date, max_temp) VALUES
            (987654321, 20240101, 10), (987654321, 20240102, 20)");
        $this->pdo->exec("INSERT INTO minimum_temperatures (city_id, date, min_temp) VALUES
            (987654321, 20240101, 0), (987654321, 20240102, 4)");
        $this->pdo->exec("INSERT INTO precipitation (city_id, date, precipitation) VALUES
            (987654321, 20240101, 2), (987654321, 20240102, 6)");
    }

    protected function cleanWeatherData(): void
    {
        $this->pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
        try {
            $this->pdo->exec('TRUNCATE TABLE precipitation');
            $this->pdo->exec('TRUNCATE TABLE minimum_temperatures');
            $this->pdo->exec('TRUNCATE TABLE maximum_temperatures');
            $this->pdo->exec('TRUNCATE TABLE cities');
        } finally {
            $this->pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
        }
    }
}
