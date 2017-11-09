<?php

                	date_default_timezone_set('America/New_York');

                	$dbHost = 'localhost';
                	$dbUser = 'root';
                	$dbPwd = 'root';
                	$dbName = 'smartSale-v2';

                	# connect
$dsn = $dsn = "mysql:host=$dbHost;charset=utf8";
$pdo = new PDO($dsn, $dbUser, $dbPwd);

# create new Database
//$pdo->query("CREATE DATABASE IF NOT EXISTS `$dbName` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci");

# use db
//$pdo->query("USE `$dbName`");

                ?>