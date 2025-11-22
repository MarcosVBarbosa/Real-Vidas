<?php

ob_start();
session_start();

$titleSystem = "Real Vidas | Área Administrativa";
$urlSystem = "https://" . $_SERVER["HTTP_HOST"] . "/area-administrativa/";
$linkUrl = "realvidas.com";
$encoding = mb_internal_encoding();

function validAcesso($acessoPermissoes, $pages)
{
	foreach ($acessoPermissoes as $permissoes) {
		if (isset($permissoes[$pages])) {
			return [
				"isAcesso" => true,
				"isPermissao" => $permissoes[$pages]
			];
		}
	}

	return [
		"isAcesso" => false,
		"isPermissao" => []
	];
}

function validButtonSubmit($acessoPermissoes, $method)
{
	$acessoPermissoes = json_decode($acessoPermissoes, true);
	if (in_array($method, $acessoPermissoes)) {
		return	$isButton = true;
	} else {
		return 	$isButton = false;
	}
}

if (isset($_SESSION["token"])) {

	$dadosConexao = explode("&", base64_decode($_SESSION["token"]));

	$timeLogin = $dadosConexao[0];
	$timeAtual = time();
	$limiteLogin = 3000; //segundos

	if ($timeAtual <= ($timeLogin + $limiteLogin)) {
		$_SESSION["token"] = base64_encode($timeAtual . "&" . $dadosConexao[1] . "&" . $dadosConexao[2] . "&" . $dadosConexao[3]);
	} else {

		$type = base64_encode('danger');
		$msg = base64_encode('Sua sessão foi expirada! Por favor realize o login novamente.');
		session_start();
		session_unset();
		session_destroy();
		header('Location: ' . $urlSystem . 'userLogin.php?msg=' . $msg . '&type=' . $type);
		exit;
	}
} else {
	header('Location: ' . $urlSystem . 'userLogin.php');
	exit;
}
