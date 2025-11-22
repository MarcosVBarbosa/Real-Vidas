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

$tabela = "";
$query = "";
$conteudo = "";
$cont = 1;


$query = "
SELECT LPAD(pkId,5,0) pkId,nome,cidade,estado
FROM rv_estabelecimentos
WHERE ativo = 'S'
ORDER BY nome
";
$rs = mysqli_query($connecta, $query);
if (mysqli_num_rows($rs) > 0) {
    while ($row = mysqli_fetch_object($rs)) {
        $conteudo .= "
        <tr>
        <td> $cont </td>
        <td> $row->nome </td>
        <td> $row->cidade </td>
        <td> $row->estado </td>
        </tr>
        ";
        $cont++;
    }
}

$tabela = '
<tr style="height:55px">
    <td colspan="1" style="text-align:center; vertical-align:middle"> <img src="https://realvidas.com.br/wp-content/uploads/2020/07/logo-realvidas-head.png" width="200"> </td>
    <td colspan="3" style="color:#990000;font-size:small;text-align:center;height:50px;vertical-align:middle;font-weight:bold" > RELATÓRIO DE ESTABELECIMENTOS </td>
</tr>
    <tr>
    <td colspan="4" style="color:#0000FF;font-size:small;text-align:center;height:30px;vertical-align:middle;" > </td>
</tr>
<tr>
    <td width="60" align="center" style="font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;vertical-align:middle">COD</td>
    <td width="400" style="font-size:x-small; text-align:left;font-weight:bold;background-color:#FFFFCC;vertical-align:middle">NOME</td>
    <td width="220" style="font-size:x-small; text-align:left;font-weight:bold;background-color:#FFFFCC;vertical-align:middle">CIDADE</td>
    <td width="100" align="center" style="font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;vertical-align:middle">ESTADO</td>
</tr>
' . $conteudo . '
';

header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Content-type: application/x.msexcel");
header("Content-Type: text/html; charset=UTF-8", true);
header("Content-type: application/force-download");
header("Content-Disposition: attachment; filename=\"Relatório Estabelecimentos_" . date('Ydm') . ".xls\"");

?>

<html>

<head>
    <meta charset="utf-8">
</head>

<body>
    <table style="font-size: x-small" border="1">
        <tr></tr>
        <?php
        echo $tabela;
        ?>
    </table>
</body>

</html>