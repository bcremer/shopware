<?php

/**
 * @param $dbConfig
 * @return string
 */
function buildConnectionString($dbConfig)
{
    if (!isset($dbConfig['host']) || empty($dbConfig['host'])) {
        $dbConfig['host'] = 'localhost';
    }

    $connectionSettings = array(
        'host=' . $dbConfig['host'],
    );

    if (!empty($dbConfig['socket'])) {
        $connectionSettings[] = 'unix_socket=' . $dbConfig['socket'];
    }

    if (!empty($dbConfig['port'])) {
        $connectionSettings[] = 'port=' . $dbConfig['port'];
    }

    $connectionString = implode(';', $connectionSettings);

    return $connectionString;
}

/**
 * @param array $dbConfig
 * @return PDO
 */
function createConnection(array $dbConfig)
{
    $password = isset($dbConfig['password']) ? $dbConfig['password'] : '';
    $connectionString = buildConnectionString($dbConfig);

    try {
        $conn = new PDO('mysql:' . $connectionString, $dbConfig['username'], $password,
            array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'"));
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        // Reset sql_mode "STRICT_TRANS_TABLES" that will be default in MySQL 5.6
        $conn->exec('SET @@session.sql_mode = ""');
    } catch (PDOException $e) {
        echo 'Could not connect to database: ' . $e->getMessage();
        exit(1);
    }

    return $conn;
}
