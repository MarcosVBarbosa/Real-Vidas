<?php

include_once("functions.php");

$allowedOrigins = array(
    ''
);

if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $allowedOrigins)) {
    $http_origin = $_SERVER['HTTP_ORIGIN'];
} else {
    $http_origin = "";
}

// header("Access-Control-Allow-Origin: $http_origin");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// AUTHORIZARION RESTRICT
$authorization = false;

// REQUEST TOKEN
@$token = $_SERVER['HTTP_AUTHORIZATION'] ?? null;

// REQUEST METHOD
$method = $_SERVER['REQUEST_METHOD'];

// REQUEST BODY
$json = json_decode(file_get_contents('php://input'), true) ?? null;

// VERIFICA SE O TOKEN FOI DECLARADO
// if (!empty($token)) {
if (true) {
    if (true) {
        $authorization = true;
        include_once("conexao.php");
    } else {
        echo json_encode([
            'status' => 'fail',
            'result' => 'Token inválido!'
        ]);
        exit;
    }
} else {
    echo json_encode([
        'status' => 'fail',
        'result' => 'O envio do Token é obrigatório!'
    ]);
    exit;
}
