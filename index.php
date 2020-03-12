<?php
include_once './database/database.php';
include_once './api/characterController.php';

header('Content-Type: application/json; charset=UTF-8');

$uri = parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH);

$uri = explode('/',$uri);

if($uri[2] !== 'api' || $uri[3] !== 'characters'){
    header('HTTP/1.1 404 Not Found');
    exit();
}

$ressource =  $uri[3];

$keywords = isset($_GET['s']) ? $_GET['s'] : null;

$page = isset($_GET['page']) ? $_GET['page'] : null;

$userId = null;
if(isset($uri[4])){
    $userId = (int) $uri[4];
}

if($uri[3] == 'characters'){
    $requestMethod = $_SERVER['REQUEST_METHOD'];

    $dbConnection = (new Database())->getConnection();
    
    $characterController = new CharacterController($dbConnection, $requestMethod, $userId, $keywords, $page);
    
    $characterController->processRequest();
}





