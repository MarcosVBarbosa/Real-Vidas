<?php

$pageActive = "Clientes";
include('../verifyConnection.php');
$acessoPermissoes = validAcesso(json_decode(base64_decode($_SESSION["acessoPermissoes"]), true), $pageActive);

if ($acessoPermissoes["isAcesso"] == 0) {
    $type = base64_encode("error");
    $msg  = base64_encode("Usuário sem permissão para acessar este conteúdo!");
    header('Location: ./?type=' . $type . '&msg=' . $msg);
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
    FROM rv_clientes
    WHERE ativo = 'S'
    AND cpfCnpj LIKE '" . mysqli_real_escape_string($connecta, trim($_POST["txtCNPJ"])) . "'
    $where
    ";
    // echo $sql;exit;
    $query = mysqli_query($connecta, $sql);

    if (mysqli_num_rows($query) > 0) {
        $type = base64_encode("danger");
        $msg = base64_encode("Já existe um Cliente cadastrado com este CNPJ.");
        header('Location: ./?type=' . $type . '&msg=' . $msg);
        exit;
    }

    if (empty($_POST["pkId"])) {
        $query = "
        INSERT INTO rv_clientes (razaoSocial,nomeFantasia,cpfCnpj,email,endereco,complemento,bairro,cidade,cep,estado,telefone,celular,limiteHoraParada,responsavel) VALUES
        ('" . mysqli_real_escape_string($connecta, trim($_POST["txtRazaoSocial"])) . "',
        '" . mysqli_real_escape_string($connecta, trim($_POST["txtNomeFantasia"])) . "',
        '" . mysqli_real_escape_string($connecta, trim($_POST["txtCNPJ"])) . "',
        '" . mysqli_real_escape_string($connecta, trim($_POST["txtEmail"])) . "',
        '" . mysqli_real_escape_string($connecta, trim($_POST["txtEndereco"])) . "',
        '" . mysqli_real_escape_string($connecta, trim($_POST["txtComplemento"])) . "',
        '" . mysqli_real_escape_string($connecta, trim($_POST["txtBairro"])) . "',
        '" . mysqli_real_escape_string($connecta, trim($_POST["txtCidade"])) . "',
        '" . mysqli_real_escape_string($connecta, trim($_POST["txtCEP"])) . "',
        '" . mysqli_real_escape_string($connecta, trim($_POST["txtEstado"])) . "',
        '" . mysqli_real_escape_string($connecta, trim($_POST["txtTelefone"])) . "',
        '" . mysqli_real_escape_string($connecta, trim($_POST["txtCelular"])) . "',
        '" . mysqli_real_escape_string($connecta, trim($_POST["txtLimiteHoraParada"])) . "',
        '" . mysqli_real_escape_string($connecta, trim($_POST["txtResponsavel"])) . "')
        ";
    } else {
        $query = "
        UPDATE rv_clientes SET
        razaoSocial = '" . mysqli_real_escape_string($connecta, trim($_POST["txtRazaoSocial"])) . "',
        nomeFantasia = '" . mysqli_real_escape_string($connecta, trim($_POST["txtNomeFantasia"])) . "',
        cpfCnpj = '" . mysqli_real_escape_string($connecta, trim($_POST["txtCNPJ"])) . "',
        email = '" . mysqli_real_escape_string($connecta, trim($_POST["txtEmail"])) . "',
        endereco = '" . mysqli_real_escape_string($connecta, trim($_POST["txtEndereco"])) . "',
        complemento = '" . mysqli_real_escape_string($connecta, trim($_POST["txtComplemento"])) . "',
        bairro = '" . mysqli_real_escape_string($connecta, trim($_POST["txtBairro"])) . "',
        cidade = '" . mysqli_real_escape_string($connecta, trim($_POST["txtCidade"])) . "',
        cep = '" . mysqli_real_escape_string($connecta, trim($_POST["txtCEP"])) . "',
        estado = '" . mysqli_real_escape_string($connecta, trim($_POST["txtEstado"])) . "',
        telefone = '" . mysqli_real_escape_string($connecta, trim($_POST["txtTelefone"])) . "',
        celular = '" . mysqli_real_escape_string($connecta, trim($_POST["txtCelular"])) . "',
        limiteHoraParada = '" . mysqli_real_escape_string($connecta, trim($_POST["txtLimiteHoraParada"])) . "',
        responsavel = '" . mysqli_real_escape_string($connecta, trim($_POST["txtResponsavel"])) . "'
        WHERE pkId = " . base64_decode($_POST["pkId"]);
    }
    // echo $query;exit;

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
