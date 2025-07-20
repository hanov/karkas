<?php
error_reporting(0);
session_start();

define('DB_PATH', 'database.sqlite');

// мин.набор. фунц. БД и шаблонов
require_once('core/fns-min.php');

if(!$_GET['route'] && $_SERVER['REQUEST_URI'] != "/"){
	$_GET['route'] = ltrim( $_SERVER['REQUEST_URI'], "/");
}

// из GET тянем роутер. Все идет через .htaccess
$_GET['route'] ? $_GET['route'] : $_GET['route'] = 'main';

// [REQUEST_URI] => /ajax/primer 

//echo "<pre>";
//print_r($_SERVER);

// // Create users table if it doesn't exist
// query("CREATE TABLE IF NOT EXISTS users (
//     id INTEGER PRIMARY KEY AUTOINCREMENT,
//     name TEXT NOT NULL,
//     email TEXT NOT NULL UNIQUE
// )");

// // Insert sample data
// query("INSERT OR IGNORE INTO users (name, email) VALUES ('John', 'john@example.com')");


//$users = q_array("SELECT * FROM users");
//print_r($users);


// из урлы тянем название контроллера
$controller = current(explode("/", $_GET['route']));

//echo $controller."12312";

// проверяем наличие контроллера
if(!file_exists('controller/' . $controller . '.php'))
{
	// если че, что ругаемся
	$controller = '404';
}

// понеслась
require('controller/' . $controller . '.php');
$controller_instance = new Controller();
echo $controller_instance->exec();
