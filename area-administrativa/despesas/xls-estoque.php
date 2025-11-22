<?php
include('../verifyConnection.php');
$pageActive = "Estoque/Custos";
$acessoPermissoes = validAcesso(json_decode(base64_decode($_SESSION["acessoPermissoes"]), true), $pageActive);

if ($acessoPermissoes["isAcesso"] == 0) {
    $type = base64_encode("error");
    $msg  = base64_encode("Usuário sem permissão para acessar este conteúdo!");
    header('Location: https://' . $_SERVER['HTTP_HOST'] . '/area-administrativa-developer/index.php?msg=' . $msg . '&type=' . $type);
    exit;
}

include('../connectDb.php');

$dataHoje = date('Y-m-d');

header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Content-type: application/x.msexcel");
header("Content-Type: text/html; charset=UTF-8", true);
header("Content-type: application/force-download");
header("Content-Disposition: attachment; filename=\"Relatorio_Estoque_Custos_" . date('Ydm') . ".xls\"");

?>
<html>

<head>
    <meta charset="utf-8">
</head>

<body>
    <table border="1">
        <thead>
            <tr style="height:100px">
                <td colspan="4" style="color:#990000;font-size:small;text-align:center;height:50px;vertical-align:middle;font-weight:bold">
                    <img src="https://realvidas.com.br/area-administrativa/imagens/logo-real-vidas.png" height="95">
                    RELATÓRIO DE ESTOQUE
                </td>
            </tr>
            <tr>
                <th width="80">Cod</th>
                <th width="400">Descrição</th>
                <th width="50">Qtde</th>
                <th width="100">Valor (R$)</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = "
            SELECT LPAD(pkId,5,0) pkId,nome,valor,qtde
            FROM rv_despesas
            WHERE ativo = 'S'
            $whereS
            ORDER BY nome
            ";
            $rs = mysqli_query($connecta, $query);
            if (mysqli_num_rows($rs) > 0) {
                $nome = '';
                while ($row = mysqli_fetch_object($rs)) {
                    echo '
                    <tr>
                        <td>' . $row->pkId . '</td>
                        <td>' . $row->nome . '</td>
                        <td style="text-align:center">' . $row->qtde . '</td>
                        <td style="text-align:center">' . number_format($row->valor, 2, ',', '.') . '</td>
                    </tr>
                    ';
                }
            } ?>
        </tbody>
    </table>
</body>

</html>