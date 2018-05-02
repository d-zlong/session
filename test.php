<?php
require_once './MySessionHandler.php';
$handler = new MySessionHandler();
ini_set('session.save_handler', 'user');
session_set_save_handler($handler, true);

session_start();
$_SESSION['name'] = 'test3';
$_SESSION['mobile'] = '18652736865';

var_dump($_SESSION);
