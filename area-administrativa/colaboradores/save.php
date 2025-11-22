<?php
$pageActive = "Colaboradores";

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

    $where = "";
    // VERIFICA SE CADASTRO JÁ EXISTE
    if (!empty($_POST["pkId"])) {
        $where = "
        AND pkId <> " . intval(base64_decode($_POST["pkId"])) . "
        ";
    }
    $sql = "
    SELECT pkId
    FROM rv_colaboradores
    WHERE (
        cpf = '" . mysqli_real_escape_string($connecta, trim($_POST["txtCPF"])) . "'
        OR (nrDoc = '" . mysqli_real_escape_string($connecta, trim($_POST["txtNrDoc"])) . "' AND tipoDoc = '" . mysqli_real_escape_string($connecta, trim($_POST["txtTipoDoc"])) . "')
    )
    AND ativo = 'S'
    $where
    ";
    // echo $sql;exit;
    $query = mysqli_query($connecta, $sql);

    if (mysqli_num_rows($query) > 0) {
        $type = base64_encode("danger");
        $msg = base64_encode("Já existe outro registro com estes Dados.");

        header('Location: ./?type=' . $type . '&msg=' . $msg);
        exit;
    }

    if ($_FILES['txtAssinatura']['name'] != "") {
        $ext = pathinfo($_FILES['txtAssinatura']['name'], PATHINFO_EXTENSION); // Obtendo a extensão do arquivo
        $new_name = md5(time()) . '.' . $ext; //Definindo um novo nome para o arquivo
        $dir = 'assinaturas/'; //Diretório para uploads
        move_uploaded_file($_FILES['txtAssinatura']['tmp_name'], $dir . $new_name); //Fazer upload do arquivo
        if (!empty($_POST["txtAssinaturaOld"]) && file_exists($dir . $_POST["txtAssinaturaOld"])) {
            unlink($dir . $_POST["txtAssinaturaOld"]); // Exclui a assinatura antiga
        }
    } else {
        $new_name = $_POST["txtAssinaturaOld"];
    }

    if (empty($_POST["pkId"])) {
        $query = "
        INSERT INTO rv_colaboradores (nome,pix,rg,dataNascimento,cpf,fkTipoColaborador,nrDoc,tipoDoc,ufDoc,validadeDoc,banco,tipoConta,nrAgencia,nrConta,endereco,complemento,bairro,cidade,cep,estado,assinatura,status) VALUES
        ('" . mysqli_real_escape_string($connecta, trim($_POST["txtNome"])) . "',
        '" . mysqli_real_escape_string($connecta, trim($_POST["txtPix"])) . "',
        '" . mysqli_real_escape_string($connecta, trim($_POST["txtRG"])) . "',
        '" . mysqli_real_escape_string($connecta, trim($_POST["txtDataNascimento"])) . "',
        '" . mysqli_real_escape_string($connecta, trim($_POST["txtCPF"])) . "',
        '" . mysqli_real_escape_string($connecta, trim($_POST["txtTipoColaborador"])) . "',
        '" . mysqli_real_escape_string($connecta, trim($_POST["txtNrDoc"])) . "',
        '" . mysqli_real_escape_string($connecta, trim($_POST["txtTipoDoc"])) . "',
        '" . mysqli_real_escape_string($connecta, strtoupper(trim($_POST["txtUFDoc"]))) . "',
        '" . mysqli_real_escape_string($connecta, trim($_POST["txtValidadeDoc"])) . "',
        '" . mysqli_real_escape_string($connecta, trim($_POST["txtBanco"])) . "',
        '" . mysqli_real_escape_string($connecta, trim($_POST["txtTipoConta"])) . "',
        '" . mysqli_real_escape_string($connecta, trim($_POST["txtNrAgencia"])) . "',
        '" . mysqli_real_escape_string($connecta, trim($_POST["txtNrConta"])) . "',
        '" . mysqli_real_escape_string($connecta, trim($_POST["txtEndereco"])) . "',
        '" . mysqli_real_escape_string($connecta, trim($_POST["txtComplemento"])) . "',
        '" . mysqli_real_escape_string($connecta, trim($_POST["txtBairro"])) . "',
        '" . mysqli_real_escape_string($connecta, trim($_POST["txtCidade"])) . "',
        '" . mysqli_real_escape_string($connecta, trim($_POST["txtCEP"])) . "',
        '" . mysqli_real_escape_string($connecta, trim($_POST["txtEstado"])) . "',
        '" . mysqli_real_escape_string($connecta, $new_name) . "',
        '" . mysqli_real_escape_string($connecta, trim($_POST["txtStatus"])) . "'
        )";

        $result = mysqli_query($connecta, $query);
        $id_colaborador = mysqli_insert_id($connecta);
    } else {
        $query = "
        UPDATE rv_colaboradores SET
        nome = '" . mysqli_real_escape_string($connecta, trim($_POST["txtNome"])) . "',
        pix = '" . mysqli_real_escape_string($connecta, trim($_POST["txtPix"])) . "',
        rg = '" . mysqli_real_escape_string($connecta, trim($_POST["txtRG"])) . "',
        dataNascimento = '" . mysqli_real_escape_string($connecta, trim($_POST["txtDataNascimento"])) . "',
        cpf = '" . mysqli_real_escape_string($connecta, trim($_POST["txtCPF"])) . "',
        fkTipoColaborador = '" . mysqli_real_escape_string($connecta, trim($_POST["txtTipoColaborador"])) . "',
        nrDoc = '" . mysqli_real_escape_string($connecta, trim($_POST["txtNrDoc"])) . "',
        tipoDoc = '" . mysqli_real_escape_string($connecta, trim($_POST["txtTipoDoc"])) . "',
        ufDoc = '" . mysqli_real_escape_string($connecta, strtoupper(trim($_POST["txtUFDoc"]))) . "',
        validadeDoc = '" . mysqli_real_escape_string($connecta, trim($_POST["txtValidadeDoc"])) . "',
        banco = '" . mysqli_real_escape_string($connecta, trim($_POST["txtBanco"])) . "',
        tipoConta = '" . mysqli_real_escape_string($connecta, trim($_POST["txtTipoConta"])) . "',
        nrAgencia = '" . mysqli_real_escape_string($connecta, trim($_POST["txtNrAgencia"])) . "',
        nrConta = '" . mysqli_real_escape_string($connecta, trim($_POST["txtNrConta"])) . "',
        endereco = '" . mysqli_real_escape_string($connecta, trim($_POST["txtEndereco"])) . "',
        complemento = '" . mysqli_real_escape_string($connecta, trim($_POST["txtComplemento"])) . "',
        bairro = '" . mysqli_real_escape_string($connecta, trim($_POST["txtBairro"])) . "',
        cidade = '" . mysqli_real_escape_string($connecta, trim($_POST["txtCidade"])) . "',
        cep = '" . mysqli_real_escape_string($connecta, trim($_POST["txtCEP"])) . "',
        estado = '" . mysqli_real_escape_string($connecta, trim($_POST["txtEstado"])) . "',
        assinatura = '" . mysqli_real_escape_string($connecta, $new_name) . "',
        status = '" . mysqli_real_escape_string($connecta, trim($_POST["txtStatus"])) . "'
        WHERE pkId = " . intval(base64_decode($_POST["pkId"]));

        $id_colaborador = intval(base64_decode($_POST["pkId"]));
        $result = mysqli_query($connecta, $query);
    }
    // echo $query;exit;

    // $sql = "DELETE FROM rv_colaboradoresServico WHERE fkColaborador = " . $id_colaborador;
    // $rs = mysqli_query($connecta, $sql);

    // $sql = "INSERT INTO rv_colaboradoresServico (fkColaborador,fkServico,valorHora) VALUES ";

    // for ($i = 0; $i < count($_POST["servico"]); $i++) {
    //     if (!empty($_POST["servico"][$i])) {
    //         $sql .= "($id_colaborador,
    //         '" . $_POST["servico"][$i] . "',
    //         '" . $_POST["valor"][$i] . "'
    //         ),";
    //     }
    // }

    // $sql = substr($sql, 0, -1);
    // $rs = mysqli_query($connecta, $sql);

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
