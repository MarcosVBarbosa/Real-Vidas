<?php
include('../verifyConnection.php');
$pageActive = "Ordem Serviço";
$acessoPermissoes = validAcesso(json_decode(base64_decode($_SESSION["acessoPermissoes"]), true), $pageActive);

if ($acessoPermissoes["isAcesso"] == 0) {
  $type = base64_encode("error");
  $msg  = base64_encode("Usuário sem permissão para acessar este conteúdo!");
  header('Location: ./?type='.$type.'&msg='.$msg);
  exit;
}
include('../connectDb.php');

$linkRequest = explode('/',$_SERVER["HTTP_REFERER"]);
$linkRequest = end($linkRequest);

if($_SERVER["HTTP_HOST"]==$linkUrl) {
    
    $id_os = base64_decode($_GET["ref"]);
    $id_maleta = base64_decode($_GET["m"]);
    $id_despesa = base64_decode($_GET["d"]);
    $qtde = base64_decode($_GET["q"]);
    
    $query = "
    SELECT pkId
    FROM rv_maletasDespesas
    WHERE fkMaleta = $id_maleta
    AND fkDespesa = $id_despesa
    ";
    // echo $query;exit;
    $result = mysqli_query($connecta,$query);
    
    if(mysqli_num_rows($result) > 0) {
    
        $query = "
        UPDATE rv_despesasOS SET
        confirmado = '1'
        WHERE fkOrdemServico = $id_os
        AND fkDespesa = $id_despesa;
        UPDATE rv_maletasDespesas SET
        qtde = (qtde - $qtde)
        WHERE fkMaleta = $id_maleta
        AND fkDespesa = $id_despesa
        ";
    
        // echo $query;exit;
        $result = mysqli_multi_query($connecta,$query);
        if($result) {
            $type = base64_encode("success");
            $msg = base64_encode("Despesa confirmada com sucesso!");
        
        } else {
            $type = base64_encode("danger");
            $msg = base64_encode("Falha ao salvar o registro! Por favor tente mais tarde.");
        }
    } else {
        $type = base64_encode("danger");
        $msg = base64_encode("Despesa não encontrada na Maleta do VTR!");
    }
}

header('Location: insert.php?ref='.base64_encode($id_os).'&type='.$type.'&msg='.$msg);
exit;

?>