<?php

include('../verifyConnection.php');
$pageActive = "Faturamento";
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
        $query = "INSERT INTO rv_faturamento (status,dataHora,dataVencimento,formaPgto,taxaNF,notaFiscal) VALUES
        ('" . mysqli_real_escape_string($connecta, trim($_POST["txtStatus"])) . "',
        '" . mysqli_real_escape_string($connecta, trim($_POST["txtDataHora"])) . "',
        '" . mysqli_real_escape_string($connecta, trim($_POST["txtDataVencimento"])) . "',
        '" . mysqli_real_escape_string($connecta, trim($_POST["txtFormaPgto"])) . "',
        '" . mysqli_real_escape_string($connecta, trim($_POST["txtTaxaNF"])) . "',
        '" . mysqli_real_escape_string($connecta, trim($_POST["txtNF"])) . "'
        )";

        mysqli_query($connecta, $query);
        $id_faturamento = mysqli_insert_id($connecta);
    } else {
        $query = "UPDATE rv_faturamento SET
        status = '" . mysqli_real_escape_string($connecta, trim($_POST["txtStatus"])) . "',
        dataVencimento = '" . mysqli_real_escape_string($connecta, trim($_POST["txtDataVencimento"])) . "',
        formaPgto = '" . mysqli_real_escape_string($connecta, trim($_POST["txtFormaPgto"])) . "',
        taxaNF = '" . mysqli_real_escape_string($connecta, trim($_POST["txtTaxaNF"])) . "',
        notaFiscal = '" . mysqli_real_escape_string($connecta, trim($_POST["txtNF"])) . "'
        WHERE pkId = " . base64_decode($_POST["pkId"]);
        $id_faturamento = base64_decode($_POST["pkId"]);
        mysqli_query($connecta, $query);
    }

    // UPLOAD NF PDF
    if ($_FILES["txtCaminhoNF"]["error"] <> 4) {

        $extensao = explode('.', $_FILES["txtCaminhoNF"]["name"]);
        $extensao = end($extensao);
        $nomeArquivo = sha1($_FILES["txtCaminhoNF"]["tmp_name"] . time()) . "." . $extensao;
        move_uploaded_file($_FILES["txtCaminhoNF"]["tmp_name"], "arquivos/" . $nomeArquivo);
        mysqli_query($connecta, "UPDATE rv_faturamento SET caminhoNF = '" . $nomeArquivo . "' WHERE pkId = " . $id_faturamento);
    }

    // UPLOAD BOLETO PDF
    if ($_FILES["txtCaminhoBoleto"]["error"] <> 4) {

        $extensao = explode('.', $_FILES["txtCaminhoBoleto"]["name"]);
        $extensao = end($extensao);
        $nomeArquivo = sha1($_FILES["txtCaminhoBoleto"]["tmp_name"] . time()) . "." . $extensao;
        move_uploaded_file($_FILES["txtCaminhoBoleto"]["tmp_name"], "arquivos/" . $nomeArquivo);
        mysqli_query($connecta, "UPDATE rv_faturamento SET caminhoBoleto = '" . $nomeArquivo . "' WHERE pkId = " . $id_faturamento);
    }

    //echo $query;exit;

    $sql = "DELETE FROM rv_faturamentoOS WHERE fkFaturamento = " . $id_faturamento;
    $rs = mysqli_query($connecta, $sql);

    $sql = "INSERT INTO rv_faturamentoOS (fkFaturamento,fkOrdemServico) VALUES ";

    for ($i = 0; $i < count($_POST["ordemServico"]); $i++) {
        if (!empty($_POST["ordemServico"][$i])) {
            $sql .= "($id_faturamento,
            " . $_POST["ordemServico"][$i] . "
            ),";
        }
    }

    $sql = substr($sql, 0, -1);
    $result = mysqli_query($connecta, $sql);

    if ($result) {
        $type = base64_encode("success");
        $msg = base64_encode("Registro salvo com sucesso!");
    } else {
        $type = base64_encode("danger");
        $msg = base64_encode("Falha ao salvar o registro! Por favor tente mais tarde.");
    }
}

header('Location: insert.php?ref=' . base64_encode($id_faturamento) . '&type=' . $type . '&msg=' . $msg);
exit;
