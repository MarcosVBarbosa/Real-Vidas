<?php

ob_start();

if (isset($_POST["txtEmail"]) and isset($_POST["txtSenha"])) {

	include("connectDb.php");

	$sql = "
		SELECT 
			rv_administradores.*,
			JSON_ARRAYAGG(
				JSON_OBJECT(
					rAc.acesso, rAc.permissoes
				)
			) AS acessoPermissoes,
			rPe.permissao_valores
		FROM 
			rv_administradores 
			JOIN rv_perfil rPe ON rPe.pkId = rv_administradores.fkPerfil
			JOIN rv_acesso rAc ON rAc.fkPerfil = rPe.pkId
		WHERE
			email = '" . mysqli_real_escape_string($connecta, trim($_POST["txtEmail"])) . "' AND senha = '" . mysqli_real_escape_string($connecta, sha1(trim($_POST["txtSenha"]))) . "' 
			AND rv_administradores.ativo = 'S'
			AND rAc.ativo = 'S'
		GROUP BY	
			rv_administradores.pkId
		LIMIT 
			1
	";

	$query = mysqli_query($connecta, $sql) or die(mysqli_connect_error());

	// $query = mysqli_query($connecta, "SELECT rv_administradores.*,rvP.permissoes, rvP.acessos FROM rv_administradores JOIN rv_perfis rvP ON  rvP.pkId = rv_administradores.fkPerfis WHERE email = '" . mysqli_real_escape_string($connecta, trim($_POST["txtEmail"])) . "' AND senha = '" . mysqli_real_escape_string($connecta, sha1(trim($_POST["txtSenha"]))) . "' LIMIT 1") or die(mysqli_connect_error());
	if (mysqli_num_rows($query) > 0) {

		if ($_POST["ckbLembrar"] == 'S') {
			setcookie("txtEmail", trim($_POST["txtEmail"]));
			setcookie("txtSenha", trim($_POST["txtSenha"]));
		} else {
			setcookie("txtEmail", null);
			setcookie("txtSenha", null);
		}

		$administradores = mysqli_fetch_assoc($query);
		$time = time();
		session_start();
		$_SESSION["username"] = base64_encode($_POST["txtEmail"]);
		$_SESSION["token"] = base64_encode($time . "&" . $administradores["nome"] . "&" . $administradores["pkId"] . "&" . $administradores["permissao_valores"]);
		$_SESSION["acessoPermissoes"] = base64_encode($administradores["acessoPermissoes"]);

		header('Location: https://' . $_SERVER['HTTP_HOST'] . '/area-administrativa/');
		exit;
	} else {

		$type = base64_encode("danger");
		$msg = base64_encode("Este login e senha são inválidos! Por favor digite novamente.");
		header('Location: https://' . $_SERVER['HTTP_HOST'] . '/area-administrativa/userLogin.php?msg=' . $msg . '&type=' . $type);
		exit;
	}
}

session_unset();
session_destroy();
header('Location: https://' . $_SERVER['HTTP_HOST'] . "/area-administrativa/");
exit;
