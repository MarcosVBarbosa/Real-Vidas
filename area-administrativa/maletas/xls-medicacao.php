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

header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Content-type: application/x.msexcel");
header("Content-Type: text/html; charset=UTF-8", true);
header("Content-type: application/force-download");
header("Content-Disposition: attachment; filename=\"Relatorio_Medicacao_" . date('Ydm') . ".xls\"");

?>
<html>

<head>
    <meta charset="utf-8">
</head>

<body>
    <table border="1">
        <thead>
            <tr style="height:100px">
                <td colspan="5" style="color:#990000;font-size:small;text-align:center;height:50px;vertical-align:middle;font-weight:bold">
                    <img src="https://realvidas.com.br/area-administrativa/imagens/logo-real-vidas.png" height="95">
                    RELATÓRIO DE MEDICAMENTOS
                </td>
            </tr>
            <tr>
                <th width="250">Maleta</th>
                <th width="300">Medicação</th>
                <th width="50">Qtde</th>
                <th width="70">Validade</th>
                <th width="100">Lote</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = "
        SELECT m.* ,
        d.nome despesa ,
        md.qtde, DATE_FORMAT(STR_TO_DATE(md.dataValidade,'%Y-%m'), '%b/%Y') dataValidade, md.lote,
        (
        SELECT COUNT(pkId)
        FROM rv_maletasDespesas
        WHERE fkMaleta = m.pkId
        ) total
        FROM rv_maletas m
        LEFT JOIN rv_maletasDespesas md ON (m.pkId = md.fkMaleta)
        LEFT JOIN rv_despesas d ON (md.fkDespesa = d.pkId)
        WHERE m.ativo = 'S'
        ORDER BY m.nome
        ";
            $rs = mysqli_query($connecta, $query);
            if (mysqli_num_rows($rs) > 0) {
                $nome = '';
                while ($row = mysqli_fetch_object($rs)) {
                    if ($nome == '' || $nome != $row->nome) {
                        echo '
                    <tr>
                        <td rowspan="' . $row->total . '" style="vertical-align:middle">' . $row->nome . '</td>
                        <td>' . $row->despesa . '</td>
                        <td style="text-align:center">' . $row->qtde . '</td>
                        <td style="text-align:center">' . $row->dataValidade . '</td>
                        <td style="text-align:center">' . $row->lote . '</td>
                    </tr>
                    ';
                        $nome = $row->nome;
                    } else {
                        echo '
                    <tr>
                        <td>' . $row->despesa . '</td>
                        <td style="text-align:center">' . $row->qtde . '</td>
                        <td style="text-align:center">' . $row->dataValidade . '</td>
                        <td style="text-align:center">' . $row->lote . '</td>
                    </tr>
                    ';
                    }
                }
            } ?>
        </tbody>
    </table>
</body>

</html>