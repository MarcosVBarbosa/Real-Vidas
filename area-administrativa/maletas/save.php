<?php
include('../verifyConnection.php');
$pageActive = "Maletas de Medicação";
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

        $query = "
        INSERT INTO rv_maletas (nome,fkVtr) VALUES
        (
            '" . mysqli_real_escape_string($connecta, trim($_POST["txtNome"])) . "' , '" . mysqli_real_escape_string($connecta, trim($_POST["txtVtr"])) . "'
        )";

        mysqli_query($connecta, $query);
        $id_maleta = mysqli_insert_id($connecta);
    } else {

        $query = "
        UPDATE rv_maletas SET
        nome = '" . mysqli_real_escape_string($connecta, trim($_POST["txtNome"])) . "',
        fkVtr = '" . mysqli_real_escape_string($connecta, trim($_POST["txtVtr"])) . "'
        WHERE pkId = " . base64_decode($_POST["pkId"]);
        $id_maleta = base64_decode($_POST["pkId"]);
        mysqli_query($connecta, $query);
    }

    if (count($_POST["despesa"]) > 0) {
        $sql = "DELETE FROM rv_maletasDespesas
        WHERE fkMaleta = $id_maleta";
        $rs = mysqli_query($connecta, $sql);

        $sql = "INSERT INTO rv_maletasDespesas (fkMaleta,fkDespesa,qtde,dataValidade,lote) VALUES ";
        for ($i = 0; $i < count($_POST["despesa"]); $i++) {
            if (!empty($_POST["despesa"][$i])) {
                $sql .= "($id_maleta,
                '" . $_POST["despesa"][$i] . "',
                '" . $_POST["qtde"][$i] . "',
                '" . $_POST["dataValidade"][$i] . "',
                '" . $_POST["lote"][$i] . "'
            ),";
            }
        }

        $sql = substr($sql, 0, -1);
        $rs = mysqli_query($connecta, $sql);
    }

    if ($rs) {
        $type = base64_encode("success");
        $msg = base64_encode("Registro salvo com sucesso!");
    } else {
        $type = base64_encode("danger");
        $msg = base64_encode("Falha ao salvar o registro! Por favor tente mais tarde.");
    }
}

header('Location: insert.php?ref=' . base64_encode($id_maleta) . '&type=' . $type . '&msg=' . $msg);
exit;
