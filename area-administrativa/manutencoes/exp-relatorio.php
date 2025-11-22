<?php
include('../verifyConnection.php');
$pageActive = 'Manutenções';
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
$cont = 0;

$query = "
SELECT LPAD(m.pkId,5,0) pkId,DATE_FORMAT(m.data , '%d/%m/%Y') AS data,m.nome,m.kmAtual,m.kmLimite,IF(m.realizado='S','Realizado','Atrasada') realizado,(m.valor + m.valorPeca) valorTotal,
v.nome AS veiculo
FROM rv_manutencoes AS m
INNER JOIN rv_vtr AS v ON (m.fkVtr = v.pkId)
WHERE m.ativo = 'S'
AND v.ativo = 'S'
ORDER BY pkId DESC
";
$rs = mysqli_query($connecta, $query);
if (mysqli_num_rows($rs) > 0) {
    while ($row = mysqli_fetch_object($rs)) {
        $conteudo .= "
        <tr>
        <td> $row->pkId </td>
        <td> $row->data </td>
        <td> $row->veiculo </td>
        <td> $row->nome </td>
        <td>" . number_format($row->kmAtual, 0, '', '.') . "</td>
        <td>" . number_format($row->kmLimite, 0, '', '.') . "</td>
        <td>" . number_format($row->valorTotal, 2, ',', '.') . "</td>
        <td>" . $row->realizado . "</td>
        </tr>
        ";
    }
}

$tabela = '
<tr style="height:55px">
    <td colspan="1" style="text-align:center; vertical-align:middle"> <img src="https://realvidas.com.br/wp-content/uploads/2020/07/logo-realvidas-head.png" width="200"> </td>
    <td colspan="7" style="color:#990000;font-size:small;text-align:center;height:50px;vertical-align:middle;font-weight:bold" > RELATÓRIO DE MANUTENÇÕES </td>
</tr>
    <tr>
    <td colspan="8" style="color:#0000FF;font-size:small;text-align:center;height:30px;vertical-align:middle;" > </td>
</tr>
<tr>
    <td width="60" style="font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;vertical-align:middle">COD</td>
    <td width="100" style="font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;vertical-align:middle">DATA</td>
    <td width="180" style="font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;vertical-align:middle">VTR</td>
    <td width="350" style="font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;vertical-align:middle">MANUTENÇÕES</td>
    <td width="90" style="font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;vertical-align:middle">KM REALIZADO</td> 
    <td width="90" style="font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;vertical-align:middle">KM LIMITE</td>
    <td width="90" style="font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;vertical-align:middle">R$ TOTAL</td>
    <td width="100" style="font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;vertical-align:middle">STATUS</td>
</tr>
' . $conteudo . '
';

header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Content-type: application/x.msexcel");
header("Content-Type: text/html; charset=UTF-8", true);
header("Content-type: application/force-download");
header("Content-Disposition: attachment; filename=\"Relatório Manutenções_" . date('Ydm') . ".xls\"");

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