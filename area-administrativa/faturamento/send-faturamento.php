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

require("../PHPMailer-master/src/PHPMailer.php");
require("../PHPMailer-master/src/SMTP.php");

if (!empty($_GET["ref"])) {

    $query = "
    SELECT os.pkId,os.nrOrdemServico,DATE_FORMAT(os.dataAgendada,'%d/%m/%Y') dataAgendada,DATE_FORMAT(os.chegadaOrigem,'%H:%i') chegadaOrigem,DATE_FORMAT(os.saidaOrigem,'%H:%i') saidaOrigem,os.qtdeHoraParada,os.valorHoraParada,os.totalHoraParada,os.paciente,os.nrCartao,os.convenio,os.valorRemocao,os.valorPercurso,os.caminhoFicha,
    CONCAT_WS(' - ' , e.nome , e.cidade) nomeOrigem,
    f.status,f.formaPgto,f.prazoPgto,MONTHNAME(f.dataHora) nomeMes,DATE_FORMAT(f.dataHora,'%Y') ano,f.prazoPgto,f.caminhoNF,f.caminhoBoleto,
    (
        SELECT CONCAT(razaoSocial, '|' , email)
        FROM rv_clientes
        LEFT JOIN rv_ordemServico ON (rv_clientes.pkId = fkCliente)
        WHERE rv_ordemServico.pkId = os.pkId
        LIMIT 1
    ) infoCliente,
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

    $caminhoNF = "";
    $caminhoBoleto = "";
    $caminhoFicha = [];

    while ($row = mysqli_fetch_object($rs)) {

        if (!empty($row->caminhoNF)) {
            $caminhoNF = '../faturamento/arquivos/' . $row->caminhoNF;
        }
        if (!empty($row->caminhoBoleto)) {
            $caminhoBoleto = '../faturamento/arquivos/' . $row->caminhoBoleto;
        }
        if (!empty($row->caminhoFicha)) {
            $caminhoFicha[] = $row->nrOrdemServico . "|../ordemServico/arquivos/" . $row->caminhoFicha;
        }

        $totalGeral = $totalGeral + ($row->valorRemocao + $row->valorPercurso + $row->totalHoraParada);
        $infoCliente = explode('|', $row->infoCliente);
        $nomeCliente = $infoCliente[0];
        $emailCliente = $infoCliente[1];
        $nomeMes = $row->nomeMes;
        $ano = $row->ano;
        $prazoPgto = $row->prazoPgto;

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
                        <td rowspan='2' style='text-align: center; vertical-align: middle'> <img src='https://realvidas.com.br/wp-content/uploads/2020/07/logo-realvidas-head.png' width='200'></td>
                        <td rowspan='2' valign='middle' style='text-align:center'> <strong style='font-size: 12px;'>$nomeCliente</strong></td>
                        <td> <span style='font-size: xx-small; border-bottom:none; text-align: left'>MÊS</span> <br><br> <span style='text-transform: capitalize; font-weight: bold;'>$nomeMes</span></td>
                        <td> <span style='font-size: xx-small; border-bottom:none; text-align: left'>ANO</span> <br><br> <strong>$ano</strong></td>
                    </tr>
                    <tr>
                        <td colspan='2'> <span style='font-size: xx-small'>FATURA A PAGAR EM</span> <br><br> <span style='text-transform: capitalize; font-weight: bold'>$prazoPgto</span></td>
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

    include "../mpdf60/mpdf.php";
    $mpdf = new mPDF('utf-8', 'A4-L', 6, 'MS Serif', 10, 10, 20, 10);
    $mpdf->SetDisplayMode('fullpage');
    //$mpdf->SetHTMLHeader('<img src="cabecalho-orcamento.jpg">','O',true);
    //$mpdf->SetHTMLFooter('<img src="rodape-orcamento.jpg">');
    ob_clean();
    $mpdf->SetHTMLHeader($header, 'O', true);
    $mpdf->SetHTMLFooter($footer);
    $mpdf->WriteHTML($html);
    $mpdf->Output("arquivos/Faturamento No " . base64_decode($_GET["ref"]) . ".pdf", "F");

    $body = "
    <html>
    <body>
    <p>Prezado cliente!</p>
    <p>Segue anexo demonstrativo de faturamento, juntamente com a Nota Fiscal e Boleto Bancário.</p>
    <p>Agradecemos pela confiança.</p>
    <p><b>Real Vidas Remoções</b></p>
    <p>Tel: (12) 3522-1128<br>
    Cel: (12) 9 9123-3435 - DISK AMBULÂNCIA
    <br>
    <a href='https://realvidas.com.br/' target='_blank'>www.realvidas.com.br</a></p>
    <p><img src='https://realvidas.com.br/wp-content/uploads/2020/07/logo-realvidas-head.png' width='150'></p>
    </body>
    </html>
    ";

    //echo $html;exit;

    $mail = new PHPMailer\PHPMailer\PHPMailer();
    $mail->IsSMTP(); // enable SMTP
    $mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only
    $mail->SMTPAuth = true; // authentication enabled
    $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
    $mail->Host = "smtp.hostinger.com";
    $mail->Port = 465; // or 587
    $mail->IsHTML(true);
    $mail->Username = "sistema@realvidas.com.br";
    $mail->Password = "Real@09925";
    $mail->SetFrom("sistema@realvidas.com.br");
    $mail->Subject = "Notificação de Faturamento - Real Vidas";
    $mail->Body = $body;
    //$mail->AddAddress("glauco_parquet@hotmail.com");
    // $mail->AddAddress("marcos-vinicius1014@hotmail.com");

    $emailCliente = explode(";", $emailCliente);
    foreach ($emailCliente as $email) {
        $mail->AddAddress($email);
    }
    $mail->AddBCC("comercial.realvidas@gmail.com");
    $mail->ConfirmReadingTo = "carlos.financeiro@realvidas.com.br"; //this is the command to request for read receipt. The read receipt email will send to the email address.
    $mail->CharSet = "UTF-8";
    $mail->AddReplyTo('carlos.financeiro@realvidas.com.br', 'Carlos Marcondes');

    $mail->AddAttachment("arquivos/Faturamento No " . base64_decode($_GET["ref"]) . ".pdf");
    if (!empty($caminhoNF)) {
        $mail->AddAttachment($caminhoNF, "Nota Fiscal.pdf");
    }
    if (!empty($caminhoBoleto)) {
        $mail->AddAttachment($caminhoBoleto, "Boleto.pdf");
    }
    foreach ($caminhoFicha as $ficha) {
        $nomeFicha = explode("|", $ficha);
        $mail->AddAttachment($nomeFicha[1], "Ficha Atendimento Nº" . $nomeFicha[0] . ".pdf");
    }

    if (!$mail->Send()) {
        $type = base64_encode('danger');
        $msg = base64_encode('Falha ao enviar notificação ao cliente! Por favor tente mais tarde.' . $mail->ErrorInfo);
    } else {
        // MUDA STATUS DE ENVIADO
        mysqli_query($connecta, "UPDATE rv_faturamento SET enviado = 'S' WHERE pkId = " . base64_decode($_GET["ref"]));
        $type = base64_encode('success');
        $msg = base64_encode('Notificação enviada com sucesso!');
    }
}
@unlink("arquivos/Faturamento No " . base64_decode($_GET["ref"]) . ".pdf");

header('Location: ./?type=' . $type . '&msg=' . $msg);
exit;
