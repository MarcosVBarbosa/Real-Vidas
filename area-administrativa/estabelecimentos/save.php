<?php
include('../verifyConnection.php');
$pageActive = "Estabelecimentos";
$acessoPermissoes = validAcesso(json_decode(base64_decode($_SESSION["acessoPermissoes"]), true), $pageActive);

if ($acessoPermissoes["isAcesso"] == 0) {
    $type = base64_encode("error");
    $msg  = base64_encode("Usuário sem permissão para acessar este conteúdo!");
    header('Location: ./?type='.$type.'&msg='.$msg);
    exit;
}

include('../connectDb.php');

$linkRequest = explode('/', $_SERVER["HTTP_REFERER"]);
$linkRequest = end($linkRequest);

if ($_SERVER["HTTP_HOST"] == $linkUrl) {

    if (empty($_POST["pkId"])) {
        $where = "";
    } else {
        $where = "
        AND pkId <> " . mysqli_real_escape_string($connecta, trim(base64_decode($_POST["pkId"]))) . "
        ";
    }

    $sql = "
    SELECT pkId
    FROM rv_estabelecimentos
    WHERE ativo = 'S'
    AND nome LIKE '" . mysqli_real_escape_string($connecta, trim($_POST["txtNome"])) . "'
    $where
    ";
    // echo $sql;exit;
    $query = mysqli_query($connecta, $sql);

    if (mysqli_num_rows($query) > 0) {
        $type = base64_encode("danger");
        $msg = base64_encode("Já existe um Estabelecimento cadastrado com este nome.");
        header('Location: ./?type=' . $type . '&msg=' . $msg);
        exit;
    }

    if (empty($_POST["pkId"])) {
        $query = "INSERT INTO rv_estabelecimentos (nome,cidade,estado) VALUES
        ('" . mysqli_real_escape_string($connecta, trim($_POST["txtNome"])) . "',
        '" . mysqli_real_escape_string($connecta, trim($_POST["txtCidade"])) . "',
        '" . mysqli_real_escape_string($connecta, trim($_POST["txtEstado"])) . "')";
    } else {
        $query = "UPDATE rv_estabelecimentos SET
        nome = '" . mysqli_real_escape_string($connecta, trim($_POST["txtNome"])) . "',
        cidade = '" . mysqli_real_escape_string($connecta, trim($_POST["txtCidade"])) . "',
        estado = '" . mysqli_real_escape_string($connecta, trim($_POST["txtEstado"])) . "'
        WHERE pkId = " . base64_decode($_POST["pkId"]);
    }
    //echo $query;exit;
    $result = mysqli_query($connecta, $query);

    if ($result) {
        $type = base64_encode("success");
        $msg = base64_encode("Registro salvo com sucesso!");
    } else {
        $type = base64_encode("danger");
        $msg = base64_encode("Falha ao salvar o registro! Por favor tente mais tarde.");
    }
}

header('Location: ./?type=' . $type . '&msg=' . $msg);
exit;
