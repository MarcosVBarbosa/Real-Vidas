<?php
$pageActive = "Colaboradores";

include('../verifyConnection.php');
$acessoPermissoes = validAcesso(json_decode(base64_decode($_SESSION["acessoPermissoes"]), true), $pageActive);

if ($acessoPermissoes["isAcesso"] == 0) {
    $type = base64_encode("error");
    $msg  = base64_encode("Usuário sem permissão para acessar este conteúdo!");
    header('Location: ./?type='.$type.'&msg='.$msg);
    exit;
}

include('../connectDb.php');
$tabela = "";

if (empty($_POST["dataInicio"]) or empty($_POST["dataFim"]) or empty($_POST["colaboradores"])) {
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

if ($_POST["colaboradores"] == "Todos") {
    $where = "";
} else {
    $where = " AND cos.fkColaborador = " . $_POST["colaboradores"];
}

$query = "
SELECT o.pkId,o.nrOrdemServico,DATE_FORMAT(o.dataAgendada,'%d/%m/%Y') dataAgendada,s.nome servico,
co.nome nomeColaborador,cos.qtdeHora,cos.valorHora,(cos.qtdeHora * cos.valorHora) totalHora,cos.ajudaCusto
FROM rv_ordemServico o
INNER JOIN rv_colaboradoresOS cos ON (cos.fkOrdemServico = o.pkId $where)
LEFT JOIN rv_colaboradores co ON (co.pkId = cos.fkColaborador)
LEFT JOIN rv_servicos s ON (s.pkId = o.fkServico)
WHERE o.ativo = 'S'
AND (dataAgendada >= '" . $_POST["dataInicio"] . "' AND dataAgendada <= '" . $_POST["dataFim"] . "')
ORDER BY pkId DESC
";

$rs = mysqli_query($connecta, $query);
if (mysqli_num_rows($rs) > 0) {
    $totalGeral = 0;
    while ($row = mysqli_fetch_object($rs)) {
        $totalGeral = $totalGeral + ($row->totalHora + $row->ajudaCusto);
        $tabela .= '
        <tr>
            <td style="text-align: center"> ' . $row->nrOrdemServico . '</td>
            <td style="text-align: left"> ' . $row->nomeColaborador . '</td>
            <td style="text-align: center"> ' . $row->dataAgendada . '</td>
            <td style="text-align: center"> ' . $row->servico . '</td>
            <td style="text-align: center"> ' . number_format($row->qtdeHora, 2, ',', '.') . '</td>
            <td style="text-align: center"> ' . number_format($row->valorHora, 2, ',', '.') . '</td>
            <td style="text-align: center"> ' . number_format($row->totalHora, 2, ',', '.') . '</td>
            <td style="text-align: center"> ' . number_format($row->ajudaCusto, 2, ',', '.') . '</td>
            <td style="text-align: center"> R$ ' . number_format(($row->totalHora + $row->ajudaCusto), 2, ',', '.') . '</td>
        </tr>';
    }
}

$conteudo = "
<table width='100%'>
    <tr>
        <td style='padding: 0'>
            <table style='width: 100%; border-collapse: collapse;' border='1' cellspacing='0' cellpadding='2'>
                <tr>
                    <td style='width: 250px; text-align: center; vertical-align: middle'> <img src='https://realvidas.com.br/area-administrativa/imagens/logo-real-vidas.png' width='70'></td>
                    <td valign='middle' style='text-align:center'> <h1>RELATÓRIO COLABORADORES</h1> <br> <p style=''>Período: " . date("d/m/Y", strtotime($_POST["dataInicio"])) . " até " . date("d/m/Y", strtotime($_POST["dataFim"])) . "</p> </td>
                </tr>
            </table>
            <table style='width: 100%; border-collapse: collapse;' border='1' cellspacing='0' cellpadding='2'>
                <tr>
                    <td colspan='9'> &nbsp; </td>
                </tr>
                <tr style='background: #CCCCCC;'>
                    <td width='50' style='font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;vertical-align:middle'>NR OS</td>
                    <td width='400' style='font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;vertical-align:middle'>COLABORADOR</td>
                    <td width='100' style='font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;vertical-align:middle'>DATA EXECUTADA</td>
                    <td width='180' style='font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;vertical-align:middle'>TIPO SERVIÇO</td> 
                    <td width='65' style='font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;vertical-align:middle'>QTDE HORA</td>
                    <td width='65' style='font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;vertical-align:middle'>VALOR HORA</td>
                    <td width='66' style='font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;vertical-align:middle'>R$ VALOR</td>
                    <td width='65' style='font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;vertical-align:middle'>AJUDA DE CUSTO</td>
                    <td width='100' style='font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;vertical-align:middle'>R$ TOTAL</td>
                </tr>
                
                $tabela
                
                <tr style='background: #efefef'>
                    <td colspan='8' style='text-align: right; font-weight: bold; padding-right: 10px'> TOTAL GERAL </td>
                    <td colspan='2' style='font-weight: bold; text-align: center'> R$ " . number_format($totalGeral, 2, ',', '.') . " </td>
                </tr>
                
            </table>
        </td>
    </tr>
</table>
";

$html = "
<html>
<body>

$conteudo

</body>
</html>
";

$footer = "
<table width='100%' border='0'>
    <tr>
        <td style='text-align: center'><h5>REAL VIDAS - Remoções e Emergências 24h | (12) 3522-1128 | (12) 9 9123-3435 <img src='../faturamento/logo-whatsapp.png' width='7'> | faleconosco@realvidas.com.br</h5></td>
    </tr>
</table>
";

//echo $html;exit;

include "../mpdf60/mpdf.php";
$mpdf = new mPDF('utf-8', 'A4-L', 6, 'MS Serif', 10, 10, 20, 10);
$mpdf->SetDisplayMode('fullpage');
//$mpdf->SetHTMLHeader('<img src="cabecalho-orcamento.jpg">','O',true);
//$mpdf->SetHTMLFooter('<img src="rodape-orcamento.jpg">');
ob_clean();
$mpdf->SetHTMLHeader($header, 'O', true);
$mpdf->SetHTMLFooter($footer);
$mpdf->WriteHTML($html);
$mpdf->Output("Relatório_Colaboradores_" . date("Ymd") . ".pdf", "I");

exit;
