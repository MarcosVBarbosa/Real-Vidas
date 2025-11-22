<?php

include('../verifyConnection.php');
$pageActive = "Perfil";

$acessoPermissoes = validAcesso(json_decode(base64_decode($_SESSION["acessoPermissoes"]), true), $pageActive);

if ($acessoPermissoes["isAcesso"] == 0) {
    $type = base64_encode("error");
    $msg  = base64_encode("Usuário sem permissão para acessar este conteúdo!");
    header('Location: ../?msg=' . $msg . '&type=' . $type);
    exit;
}
include('../connectDb.php');

$linkRequest = explode('/', $_SERVER["HTTP_REFERER"]);
$linkRequest = end($linkRequest);

if ($_SERVER["HTTP_HOST"] == $linkUrl) {
    // Configurar a conexão com UTF-8mb4
    mysqli_set_charset($connecta, 'utf8mb4');

    // Verificação e inserção ou atualização
    if (empty($_POST["pkId"])) {
        // Primeiro INSERT na tabela rv_perfil
        $query = "
            INSERT INTO rv_perfil (nome) VALUES
            (
                '" . mysqli_real_escape_string($connecta, trim($_POST["txtNome"])) . "'
            )";

        $result = mysqli_query($connecta, $query);

        if ($result) {
            // Capturar o ID gerado automaticamente
            $newId = mysqli_insert_id($connecta);

            // Array de acessos
            $acessos = $_POST["multAcessos"];

            // Verifica se o array de acessos está vazio ou sem posições
            if (empty($acessos) || count($acessos) === 0) {
                $acessos[] = "Página Inicial"; // Adiciona "Página Inicial" na primeira posição
            }

            // Construir query para múltiplos INSERTs
            $values = [];
            foreach ($acessos as $acesso) {
                $values[] = "(" . intval($newId) . ", '" . mysqli_real_escape_string($connecta, $acesso) . "')";
            }

            $secondQuery = "
                INSERT INTO rv_acesso (fkPerfil, acesso) VALUES
                " . implode(", ", $values);

            $secondResult = mysqli_query($connecta, $secondQuery);

            if ($secondResult) {
                $type = base64_encode("success");
                $msg = base64_encode("Registro salvo com sucesso e acessos criados!");
            } else {
                $type = base64_encode("warning");
                $msg = base64_encode("Registro salvo, mas falha ao criar os acessos!");
            }
        } else {
            $type = base64_encode("danger");
            $msg = base64_encode("Falha ao salvar o registro! Por favor tente mais tarde.");
        }
    } else {
        // Atualização na tabela rv_perfil
        $query = "
            UPDATE rv_perfil SET
                nome = '" . mysqli_real_escape_string($connecta, trim($_POST["txtNome"])) . "',
                permissao_valores = '" . mysqli_real_escape_string($connecta, (empty($_POST["permissao_valores"]) ? 0 : trim($_POST["permissao_valores"]))) . "'
            WHERE pkId = " . base64_decode($_POST["pkId"]);


        $result = mysqli_query($connecta, $query);

        if ($result) {
            $type = base64_encode("success");
            $msg = base64_encode("Registro atualizado com sucesso!");
        } else {
            $type = base64_encode("danger");
            $msg = base64_encode("Falha ao atualizar o registro! Por favor tente mais tarde.");
        }
    }
}

header('Location: ./?type=' . $type . '&msg=' . $msg);
exit;
