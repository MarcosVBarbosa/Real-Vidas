<?php
include('../valida_token.php');

// Verifica autorização definida no valida_token.php
if (!$authorization) {
    http_response_code(401);
    echo json_encode(['status' => 'fail', 'result' => 'Token não autorizado']);
    exit;
}

if ($method == 'GET') {
    require('get.php');
} elseif ($method == 'POST') {
    require('post.php');
} elseif ($method == 'PUT') {
    require('put.php');
} elseif ($method == 'PATCH') {
    require('patch.php');
} elseif ($method == 'DELETE') {
    require('delete.php');
} else {
    http_response_code(405);
    echo json_encode(['status' => 'fail', 'result' => 'Método não permitido']);
}