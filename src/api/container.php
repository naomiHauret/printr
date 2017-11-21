<?php
    require "/config/database.php";
    $container = $app->getContainer();

    try {
        $pdo = new PDO($lib . ':host=' . $host . ';dbname=' . $name . ';charset=utf8', $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $container['db'] = $pdo;
    }
    catch(PDOException $exception) {
        die($exception->getMessage());
    }