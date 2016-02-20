<?php
error_reporting(0);
session_start();

define(DB_USER, 'root');
define(DB_PASS, 'c2h5oh');
define(DB_BASE, 'test');

// мин.набор. фунц. БД и шаблонов
require_once('core/fns-min.php');

// из GET тянем роутер. Все идет через .htaccess
$_GET['route'] ? $_GET['route'] : $_GET['route'] = 'main';

// из урлы тянем название контроллера
$controller = current(explode("/", $_GET['route']));

// проверяем наличие контроллера
if(!file_exists('controller/' . $controller . '.php'))
{
	// если че, что ругаемся
	$controller = '404';
}

// понеслась
require('controller/' . $controller . '.php');
echo Controller::exec();