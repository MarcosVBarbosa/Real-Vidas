<?php

include('../verifyConnection.php');
$pageActive = "Faturamento";
$acessoPermissoes = validAcesso(json_decode(base64_decode($_SESSION["acessoPermissoes"]), true), $pageActive);

if ($acessoPermissoes["isAcesso"] == 0) {
    $type = base64_encode("error");
    $msg  = base64_encode("Usuário sem permissão para acessar este conteúdo!");
    header('Location: ./?type=' . $type . '&msg=' . $msg);
    exit;
}
include('../connectDb.php');
$tabela = "";
$totalGeral = 0;

if (empty($_GET["ref"])) {
    header('Location: ./');
    exit;
}


$query = "
SELECT os.pkId,DATE_FORMAT(os.dataAgendada,'%d/%m/%Y') dataAgendada,DATE_FORMAT(os.chegadaOrigem,'%H:%i') chegadaOrigem,DATE_FORMAT(os.saidaOrigem,'%H:%i') saidaOrigem,os.qtdeHoraParada,os.valorHoraParada,os.totalHoraParada,os.paciente,os.nrCartao,os.convenio,os.valorRemocao,os.valorPercurso,os.solicitante,
CONCAT_WS(' - ' , e.nome , e.cidade) nomeOrigem,
f.status,f.formaPgto,MONTHNAME(f.dataHora) nomeMes,DATE_FORMAT(f.dataHora,'%Y') ano,DATE_FORMAT(f.dataVencimento,'%d/%m/%Y') dataVencimento,
(
    SELECT JSON_ARRAY(
        razaoSocial,
        CASE 
            WHEN LENGTH(cpfCnpj) = 11 THEN 
                CONCAT(
                    SUBSTRING(cpfCnpj, 1, 3), '.', 
                    SUBSTRING(cpfCnpj, 4, 3), '.', 
                    SUBSTRING(cpfCnpj, 7, 3), '-', 
                    SUBSTRING(cpfCnpj, 10, 2)
                )
            WHEN LENGTH(cpfCnpj) = 14 THEN 
                CONCAT(
                    SUBSTRING(cpfCnpj, 1, 2), '.', 
                    SUBSTRING(cpfCnpj, 3, 3), '.', 
                    SUBSTRING(cpfCnpj, 6, 3), '/', 
                    SUBSTRING(cpfCnpj, 9, 4), '-', 
                    SUBSTRING(cpfCnpj, 13, 2)
                )
            ELSE cpfCnpj
        END
    )
    FROM rv_clientes
    LEFT JOIN rv_ordemServico ON rv_clientes.pkId = fkCliente
    WHERE rv_ordemServico.pkId = os.pkId
    LIMIT 1
) AS clienteInfo
,
(
    SELECT GROUP_CONCAT(CONCAT_WS(' - ' , e.nome , e.cidade) SEPARATOR '<br>x<br>')
    FROM rv_estabelecimentos e
    LEFT JOIN rv_ordemServicoDestino osd ON (e.pkId = osd.fkDestino)
    WHERE osd.fkOrdemServico = os.pkId
) nomeDestino,
(
    SELECT GROUP_CONCAT(DATE_FORMAT(osd.horaChegada,'%H:%i') SEPARATOR '<br> <br>')
    FROM rv_ordemServicoDestino osd
    WHERE osd.fkOrdemServico = os.pkId
) chegadaDestino,
(
    SELECT GROUP_CONCAT(DATE_FORMAT(osd.horaSaida,'%H:%i') SEPARATOR '<br> <br>')
    FROM rv_ordemServicoDestino osd
    WHERE osd.fkOrdemServico = os.pkId
) saidaDestino
FROM rv_faturamento f
LEFT JOIN rv_faturamentoOS fos ON (f.pkId = fos.fkFaturamento)
LEFT JOIN rv_ordemServico os ON (fos.fkOrdemServico = os.pkId)
LEFT JOIN rv_estabelecimentos e ON (os.fkOrigem = e.pkId)
WHERE f.pkId = " . base64_decode($_GET["ref"]);

$rs = mysqli_query($connecta, "set lc_time_names = 'pt_BR'");
$rs = mysqli_query($connecta, $query);

if (mysqli_num_rows($rs) == 0) {
    $type = base64_encode('danger');
    $msg = base64_encode('Registro não encontrado!');
    header('Location: ./?msg=' . $msg . '&type=' . $type);
    exit;
}

while ($row = mysqli_fetch_object($rs)) {

    $totalGeral = $totalGeral + ($row->valorRemocao + $row->valorPercurso + $row->totalHoraParada);
    $clienteInfo = json_decode($row->clienteInfo, true);
    $nomeMes = $row->nomeMes;
    $ano = $row->ano;
    $dataVencimento = $row->dataVencimento;

    $tabela .= "
    <tr>
        <td style='vertical-align: middle; text-align: center'> $row->dataAgendada </td>
        <td style='vertical-align: middle; text-align: center'> $row->solicitante </td>
        <td style='vertical-align: middle; text-align: center'> $row->paciente </td>
        <td style='vertical-align: middle; text-align: center'> $row->convenio </td>
        <td style='vertical-align: middle; text-align: center'> $row->nrCartao </td>
        <td style='vertical-align: middle; text-align: center'> $row->nomeOrigem <br>x<br> $row->nomeDestino</td>
        <td style='vertical-align: middle; text-align: center'> $row->chegadaOrigem <br> <br>$row->chegadaDestino </td>
        <td style='vertical-align: middle; text-align: center'> $row->saidaOrigem <br> <br>$row->saidaDestino </td>
        <td style='vertical-align: middle; text-align: center'> R$ " . number_format($row->valorHoraParada, 2, ',', '.') . " </td>
        <td style='vertical-align: middle; text-align: center'> " . number_format($row->qtdeHoraParada, 2, ',', '.') . " </td>
        <td style='vertical-align: middle; text-align: center'> R$ " . number_format($row->valorRemocao, 2, ',', '.') . " </td>
        <td style='vertical-align: middle; text-align: center'> R$ " . number_format($row->valorPercurso, 2, ',', '.') . " </td>
        <td style='vertical-align: middle; text-align: center'> R$ " . number_format($row->totalHoraParada, 2, ',', '.') . " </td>
        <td style='vertical-align: middle; text-align: center'> R$ " . number_format(($row->valorRemocao  + $row->valorPercurso +  $row->totalHoraParada), 2, ',', '.') . " </td>
        <td style='vertical-align: middle; text-align: center'> $row->status </td>
    </tr>
    
    ";
}

$conteudo = "
<table width='100%'>
    <tr>
        <td style='padding: 0'>
            <table style='width: 100%; border-collapse: collapse;' border='1' cellspacing='0' cellpadding='2'>
                <tr>
                    <td rowspan='2' style='width: 250px; text-align: center; vertical-align: middle'> <img src='https://realvidas.com/area-administrativa/imagens/logo-real-vidas.png' width='70'></td>
                    <td rowspan='2' valign='middle' style='text-align:center'> <strong style='font-size: 12px;'>$clienteInfo[0]</strong> <br>  $clienteInfo[1]</td>
                    <td style='width: 100px'> <span style='font-size: xx-small; border-bottom:none; text-align: left'>MÊS</span> <br><br> <span style='text-transform: capitalize; font-weight: bold;'>$nomeMes</span></td>
                    <td style='width: 100px'> <span style='font-size: xx-small; border-bottom:none; text-align: left'>ANO</span> <br><br> <strong>$ano</strong></td>
                </tr>
                <tr>
                    <td colspan='2'> <span style='font-size: xx-small'>FATURA A PAGAR EM</span> <br><br> <span style='text-transform: capitalize; font-weight: bold'>$dataVencimento</span></td>
                </tr>
            </table>
            
            <table style='width: 100%; border-collapse: collapse;' border='1' cellspacing='0' cellpadding='2'>
                <tr style='background: #CCCCCC;'>
                    <td rowspan='2' style='vertical-align: middle; text-align: center; font-weight: bold' width='50'>Data</td>
                    <td rowspan='2' style='vertical-align: middle; text-align: center; font-weight: bold'>Solicitante</td>
                    <td rowspan='2' style='vertical-align: middle; text-align: center; font-weight: bold'>Paciente</td>
                    <td rowspan='2' style='vertical-align: middle; text-align: center; font-weight: bold'>Convênio/SUS</td>
                    <td rowspan='2' style='vertical-align: middle; text-align: center; font-weight: bold' width='60'>Nº Beneficiário</td>
                    <td rowspan='2' style='vertical-align: middle; text-align: center; font-weight: bold'>Origem/Destino</td>
                    <td colspan='4' style='vertical-align: middle; text-align: center; font-weight: bold'>Horas</td>
                    <td colspan='3' style='vertical-align: middle; text-align: center; font-weight: bold'>R$ Remoção + R$ Percurso + R$ Hora Parada</td>
                    <td rowspan='2' style='vertical-align: middle; text-align: center; font-weight: bold' width='60'>Status Faturamento</td>
                </tr>
                <tr style='background: #CCCCCC; font-weight: bold'>
                    <td colspan='1' style='vertical-align: middle; text-align: center; font-weight: bold' width='50'>Chegada</td>
                    <td colspan='1' style='vertical-align: middle; text-align: center; font-weight: bold' width='50'>Saída</td>
                    <td colspan='1' style='vertical-align: middle; text-align: center; font-weight: bold' width='50'>R$ Hora Parada</td>
                    <td colspan='1' style='vertical-align: middle; text-align: center; font-weight: bold' width='50'>Qtde Hora Parada</td>
                    <td colspan='1' style='vertical-align: middle; text-align: center; font-weight: bold' width='55'>Valor Remoção</td>
                    <td colspan='1' style='vertical-align: middle; text-align: center; font-weight: bold' width='55'>Valor Percurso</td>
                    <td colspan='1' style='vertical-align: middle; text-align: center; font-weight: bold' width='55'>Valor Hr Parada</td>
                    <td colspan='1' style='vertical-align: middle; text-align: center; font-weight: bold' width='55'>Valor Total</td>
                </tr>
                
                $tabela
                
                <tr style='background: #efefef'>
                    <td colspan='12' style='text-align: right; font-weight: bold; padding-right: 10px'> Total </td>
                    <td colspan='3' style='font-weight: bold; text-align: center'> R$ " . number_format($totalGeral, 2, ',', '.') . " </td>
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

$header = "
<table width='100%' border='0'>
    <tr>
        <td style='text-align: center'><h1>RELATÓRIO DE REMOÇÃO</h1></td>
    </tr>
</table>
";

$footer = "
<table width='100%' border='0'>
    <tr>
        <td style='text-align: center'><h5>REAL VIDAS - Remoções e Emergências 24h | (12) 3522-1128 | (12) 9 9123-3435 <img src='logo-whatsapp.png' width='7'> | faleconosco@realvidas.com.br</h5></td>
    </tr>
</table>
";


// echo $html;exit;
include "../mpdf60/mpdf.php";
$mpdf = new mPDF('utf-8', 'A4-L', 6, 'MS Serif', 10, 10, 20, 10);
$mpdf->SetDisplayMode('fullpage');
//$mpdf->SetHTMLHeader('<img src="cabecalho-orcamento.jpg">','O',true);
//$mpdf->SetHTMLFooter('<img src="rodape-orcamento.jpg">');
ob_clean();
$mpdf->SetHTMLHeader($header, 'O', true);
$mpdf->SetHTMLFooter($footer);
$mpdf->WriteHTML($html);
$mpdf->Output("Faturamento No " . base64_decode($_GET["ref"]) . ".pdf", "I");

exit;
