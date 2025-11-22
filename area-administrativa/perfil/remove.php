<?php
include('../verifyConnection.php');
$pageActive = 'Perfil';
$acessoPermissoes = validAcesso(json_decode(base64_decode($_SESSION["acessoPermissoes"]), true), $pageActive);

if ($acessoPermissoes["isAcesso"] == 0) {
    $type = base64_encode("error");
    $msg  = base64_encode("Usuário sem permissão para acessar este conteúdo!");
    header('Location: ../?msg=' . $msg . '&type=' . $type);
    exit;
}

include('../connectDb.php');

if (!empty($_GET["ref"])) {
    $query = "UPDATE rv_perfil SET ativo = 'N' WHERE pkId = " . base64_decode($_GET["ref"]);
    $result = mysqli_query($connecta, $query);
    if ($result) {
        $type = base64_encode('success');
        $msg = base64_encode('Registro removido com sucesso!');
    } else {
        $type = base64_encode('danger');
        $msg = base64_encode('Falha ao remover o registro! Por favor tente mais tarde.');
    }
}

header('Location: ./?type=' . $type . '&msg=' . $msg);
exit;
