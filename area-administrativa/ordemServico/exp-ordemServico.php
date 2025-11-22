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
$tabela = "";

if (empty($_POST["dataInicio"]) and empty($_POST["dataFim"])) {
    $type = base64_encode('danger');
    $msg = base64_encode('Falha! Não foi possível gerar o relatório.');
    header('Location: ./?msg=' . $msg . '&type=' . $type);
    exit;
} else {
    if (strtotime($_POST["dataFim"]) < strtotime($_POST["dataInicio"])) {
        $type = base64_encode('danger');
        $msg = base64_encode('Falha! O campo DATA INÍCIO não pode ser superior ao campo DATA FIM.');
        header('Location: ./?msg=' . $msg . '&type=' . $type);
        exit;
    }
}

header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Content-type: application/x.msexcel");
header("Content-Type: text/html; charset=UTF-8", true);
header("Content-type: application/force-download");
header("Content-Disposition: attachment; filename=\"Relatorio_OS_" . date('Ydm') . ".xls\"");

$query = "
SELECT o.pkId,o.nrOrdemServico,DATE_FORMAT(o.dataAgendada,'%d/%m/%Y') dataAgendada,c.razaoSocial nomeCliente,s.nome servico,o.solicitante,o.trajeto,o.valorRemocao,o.totalHoraParada,
(
  SELECT COALESCE((SUM(qtdeHora * valorHora) + ajudaCusto),0)
  FROM rv_colaboradoresOS
  WHERE fkOrdemServico = o.pkId
) totalColaborador,
(
  SELECT COALESCE(SUM(valor),0)
  FROM rv_despesasOS
  WHERE fkOrdemServico = o.pkId
) totalDespesa,
(
  SELECT status
  FROM rv_faturamento
  INNER JOIN rv_faturamentoOS ON (rv_faturamento.pkId = rv_faturamentoOS.fkFaturamento)
  WHERE fkOrdemServico = o.pkId
  LIMIT 1
) status
FROM rv_ordemServico o
LEFT JOIN rv_clientes c ON (o.fkCliente = c.pkId)
LEFT JOIN rv_servicos s ON (s.pkId = o.fkServico)
WHERE o.ativo = 'S'
AND (dataAgendada >= '" . $_POST["dataInicio"] . "' AND dataAgendada <= '" . $_POST["dataFim"] . "')
ORDER BY pkId DESC
";

$rs = mysqli_query($connecta, $query);
if (mysqli_num_rows($rs) > 0) {
    while ($row = mysqli_fetch_object($rs)) {
        $tabela .= '
        <tr>
            <td style="text-align: center"> ' . $row->nrOrdemServico . '</td>
            <td style="text-align: left"> ' . $row->nomeCliente . '</td>
            <td style="text-align: center"> ' . $row->dataAgendada . '</td>
            <td style="text-align: center"> ' . $row->servico . '</td>
            <td style="text-align: center"> ' . $row->solicitante . '</td>
            <td style="text-align: center"> ' . $row->trajeto . '</td>
            <td style="text-align: center"> ' . number_format($row->totalColaborador, 2, ',', '.') . '</td>
            <td style="text-align: center"> ' . number_format($row->totalDespesa, 2, ',', '.') . '</td>
            <td style="text-align: center"> ' . number_format($row->valorRemocao, 2, ',', '.') . '</td>
            <td style="text-align: center"> ' . number_format($row->totalHoraParada, 2, ',', '.') . '</td>
            <td style="text-align: center"> ' . number_format(($row->valorRemocao + $row->totalHoraParada), 2, ',', '.') . '</td>
            <td style="text-align: center"> ' . $row->status . '</td>
        </tr>';
    }
}

?>

<html>

<head>
    <meta charset="utf-8">
</head>

<body>
    <table style="font-size: x-small" border="1">
        <tr></tr>
        <tr style="height:155px">
            <td colspan="12" style="color:#990000;font-size:small;text-align:center;height:50px;vertical-align:middle;font-weight:bold">
                <img src="https://realvidas.com.br/area-administrativa/imagens/logo-real-vidas.png" width="200">
                RELATÓRIO DE ORDEM DE SERVIÇO
            </td>
        </tr>
        <tr>
            <td colspan="12" style="color:#0000FF;font-size:small;text-align:center;height:30px;vertical-align:middle;"> Período: <?php echo date('d/m/Y', strtotime($_POST["dataInicio"])) . ' até ' . date('d/m/Y', strtotime($_POST["dataFim"])); ?></td>
        </tr>
        <tr>
            <td width="50" style="font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;vertical-align:middle">NR OS</td>
            <td width="450" style="font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;vertical-align:middle">CLIENTE</td>
            <td width="100" style="font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;vertical-align:middle">DATA AGENDADA</td>
            <td width="180" style="font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;vertical-align:middle">SERVIÇO</td>
            <td width="180" style="font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;vertical-align:middle">SOLICITANTE</td>
            <td width="90" style="font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;vertical-align:middle">TRAJETO</td>
            <td width="100" style="font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;vertical-align:middle">R$ COLABORADOR</td>
            <td width="100" style="font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;vertical-align:middle">R$<br>DESPESAS</td>
            <td width="100" style="font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;vertical-align:middle">VALOR REMOÇÃO</td>
            <td width="100" style="font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;vertical-align:middle">TOTAL R$ PARADA</td>
            <td width="100" style="font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;vertical-align:middle">R$ REMOÇÃO + TOTAL PARADA</td>
            <td width="100" style="font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;vertical-align:middle">STATUS</td>
        </tr>
        <?php
        echo $tabela;
        ?>
    </table>
</body>

</html>