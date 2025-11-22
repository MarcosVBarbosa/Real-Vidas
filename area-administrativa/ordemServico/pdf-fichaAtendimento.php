<?php

include('../verifyConnection.php');
$pageActive = "Ordem Serviço";
$acessoPermissoes = validAcesso(json_decode(base64_decode($_SESSION["acessoPermissoes"]), true), $pageActive);

if ($acessoPermissoes["isAcesso"] == 0) {
    $type = base64_encode("error");
    $msg  = base64_encode("Usuário sem permissão para acessar este conteúdo!");
    header('Location: ./?type=' . $type . '&msg=' . $msg);
    exit;
}
include('../connectDb.php');

include "../mpdf60/mpdf.php";

$mpdf = new mPDF();
$mpdf->SetDisplayMode('fullpage');

$nrOrdemServico = base64_decode($_GET['nrOrdemServico']);

$sql = "
     SELECT 
 		rv_ordemServico.*, ficha.*, IF(ficha.numberIdade = 0,'0 anos', ficha.numberIdade) AS numberIdade,
 		rv_ordemServico.nrOrdemServico,rv_ordemServico.fkVTR,DATE_FORMAT(rv_ordemServico.dataSolicitada, '%d/%m/%Y') AS dataSolicitadaFormatada,rv_ordemServico.solicitante,
 		rv_ordemServico.paciente,rv_ordemServico.convenio,rv_ordemServico.nrCartao,
        rv_vtr.nome vtr,
 		DATE_FORMAT(rv_ordemServico.dataHoraInicio, '%H:%i') AS dataHoraInicioFormatada,
 		DATE_FORMAT(rv_ordemServico.dataHoraFim, '%H:%i') AS dataHoraFimFormatada,
 		DATE_FORMAT(rv_ordemServico.chegadaOrigem, '%H:%i') AS chegadaOrigemFormatada,
 		DATE_FORMAT(rv_ordemServico.saidaOrigem, '%H:%i') AS saidaOrigemFormatada,
 		JSON_ARRAYAGG(
         JSON_OBJECT( 
			 'km',rv_ordemServicoDestino.distancia,
			 'horaChegada' ,DATE_FORMAT(rv_ordemServicoDestino.horaChegada, '%H:%i'),
			 'horaSaida',DATE_FORMAT(rv_ordemServicoDestino.horaSaida, '%H:%i')
			 )
     ) AS detalhes,
     CONCAT(rv_estabelecimentosOrigem.nome, ' - ', rv_estabelecimentosOrigem.cidade, ' | ', rv_estabelecimentosOrigem.estado) AS origem,
     JSON_ARRAYAGG(CONCAT(rv_estabelecimentosDestino.nome, ' - ', rv_estabelecimentosDestino.cidade, ' | ', rv_estabelecimentosDestino.estado)) AS destinos,
     rv_clientes.razaoSocial,
     JSON_ARRAYAGG(
     		JSON_OBJECT(
	     		'tipo',co.fkTipoColaborador,
				'colaborador',co.nome,
				'nrDoc',co.nrDoc,
				'tipoDoc',co.tipoDoc,
				'cpf',co.cpf,
                'assinatura',co.assinatura
			)
     ) colaboradores
    FROM 
        rv_ordemServico
    JOIN 
        rv_clientes ON rv_clientes.pkId = rv_ordemServico.fkCliente
    LEFT JOIN 
        rv_ordemServicoDestino ON rv_ordemServicoDestino.fkOrdemServico = rv_ordemServico.pkId
    LEFT JOIN 
        rv_estabelecimentos AS rv_estabelecimentosOrigem ON rv_estabelecimentosOrigem.pkId = rv_ordemServico.fkOrigem
    LEFT JOIN 
        rv_estabelecimentos AS rv_estabelecimentosDestino ON rv_estabelecimentosDestino.pkId = rv_ordemServicoDestino.fkDestino
    LEFT JOIN 
        rv_fichaAtendimento AS ficha ON ficha.fkOS = rv_ordemServico.pkId
    LEFT JOIN 
            rv_colaboradoresOS col ON col.fkOrdemServico = rv_ordemServico.pkId
    LEFT JOIN 
            rv_colaboradores co ON co.pkId = col.fkColaborador
    LEFT JOIN 
            rv_vtr ON rv_vtr.pkId = rv_ordemServico.fkVtr
    WHERE 
        rv_ordemServico.pkId = $nrOrdemServico
    GROUP BY 
        rv_ordemServico.nrOrdemServico;
";
// echo $sql;exit;
$query = mysqli_query($connecta, $sql);

$row = mysqli_fetch_array($query);
$row['trauma'] = json_decode($row['trauma'], true);
$row['queimadura'] = json_decode($row['queimadura'], true);
$row['sinaisVitais'] = json_decode($row['sinaisVitais'], true);
$row['colaboradores'] = json_decode($row['colaboradores'], true);

// Dados médico
$row['medico'] = '';
$row['medicoAssinatura'] = '';
// Dados condutor
$row['condutor'] = '';
$row['condutorAssinatura'] = '';
// Dados enfermeiro
$row['enfermeiro'] = '';
$row['enfermeiroAssinatura'] = '';

foreach ($row['colaboradores'] as $key => $value) {
    foreach ($value as $key0 => $value0) {
        // Verifica se é MÉDICO
        if ($key0 == 'tipo' && $value0 == 1) {
            if (!empty($value['assinatura'])) {
                $row['medicoAssinatura'] = "<p><img src='../colaboradores/assinaturas/" . $value['assinatura'] . "' alt='Assinatura' style='max-width: 100px; max-height: 100px;'></p>";
            }
            $row['medico'] = '<span style="border-top: 1px solid black">' . $value['colaborador'] . '</span><br>' . $value['tipoDoc'] . '/'  . $value['UFDoc'] . ' ' . $value['nrDoc'];
        }
        // Verifica se é CONDUTOR
        elseif ($key0 == 'tipo' && $value0 == 5) {
            if (!empty($value['assinatura'])) {
                $row['condutorAssinatura'] = "<p><img src='../colaboradores/assinaturas/" . $value['assinatura'] . "' alt='Assinatura' style='max-width: 100px; max-height: 100px;'></p>";
            }
            $row['condutor'] = '<span style="border-top: 1px solid black">' . $value['colaborador'] . '</span><br>' . $value['tipoDoc'] . '/'  . $value['UFDoc'] . ' ' . $value['nrDoc'];
        }
        // Verifica se é ENFERMEIRO
        elseif ($key0 == 'tipo' && ($value0 == 2 || $value0 == 3 || $value0 == 4)) {
            if (!empty($value['assinatura'])) {
                $row['enfermeiroAssinatura'] = "<p><img src='../colaboradores/assinaturas/" . $value['assinatura'] . "' alt='Assinatura' style='max-width: 100px; max-height: 100px;'></p>";
            }
            $row['enfermeiro'] = '<span style="border-top: 1px solid black">' . $value['colaborador'] . '</span><br>' . $value['tipoDoc'] . '/'  . $value['UFDoc'] . ' ' . $value['nrDoc'];
        }
    }
}

// Decodificar o campo 'destinos' (JSON) para trabalhar como array PHP
$destinos = json_decode($row['destinos'], true); // 'true' para decodificar como array associativo
$detalhesDestinos = json_decode($row['detalhes'], true);

function addX2($valorId, $value)
{
    if ($valorId == $value) {
        return "&nbsp;x&nbsp;";
    } else {
        return "&nbsp;&nbsp;&nbsp;&nbsp;";
    }
}

function addX($valor)
{
    if ($valor ==  1) {
        return "&nbsp;x&nbsp;";
    } else {
        return "&nbsp;&nbsp;&nbsp;&nbsp;";
    }
}


// HTML da primeira página com marca d'água
$htmlPage1 = '
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>PDF com mPDF - Página 1</title>
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: "Source Sans Pro", sans-serif;
            font-size: 10px;
            background-image: url("../imagens/logo-opaco.png");
            background-repeat: no-repeat;
            background-position: center;
        }
        .watermark {
            position: absolute;
            top: 0;
            left: -7px;
            width: 98%;
            height: 100%;
            background-image: url(\'https://realvidas.com/area-administrativa-developer/imagens/logo-opaco.png\');
            background-size: 90%;
            background-repeat: no-repeat;
            background-position: center;
            opacity: 0.1; /* Simula transparência */
            z-index: -1;
        }
        .container {
            width: 100%;
            padding: 15px;
            margin-right: auto;
            margin-left: auto;
        }
        .row{
            width:95%;
        }

        .checkbox {
            display: inline-block;
            background: white;
            width: 10px;
            height: 10px;
            border: 2px solid #000;
            margin-right: 5px;
            text-align: center;
            font-size: 12px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="watermark container">
        <table class="row" style="border-collapse: collapse;">
            <thead>
                <tr>
                    <th scope="col" style="width: 100%;"></th>
                
                </tr>
            </thead>
            <tbody style="padding: 0px;">
                <tr>
                    <td style="border-right: 2px solid black; border-left: 2px solid black; border-top: 2px solid black; padding: 0px;">
                       <table style="border-collapse: collapse; width: 100%;">
                            <tbody>
                                <tr>
                                    <td style="text-align: center; border-right: 2px solid black; width: 80%">
                                        <h2>Ficha de Acompanhamento</h2>
                                    </td>
                                    <td style="width: 20%">
                                        <table style="width: 100%; border-collapse: collapse;">
                                            <tbody>
                                                <tr style="width: 100%;">
                                                    <td style="border-bottom: 2px solid black; font-weight: bold;">
                                                        OS: ' . $row['nrOrdemServico'] . '
                                                    </td>
                                                </tr>
                                                <tr style="width: 100%;">
                                                    <td style="font-weight: bold;">
                                                        VTR: ' . $row['vtr'] . '
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td style="border-right: 2px solid black; border-left: 2px solid black; border-top: 2px solid black; padding: 5px;">
                       <table style="border-collapse: collapse; width: 100%;">
                            <tbody>
                                <tr style="margin: 100px;">
                                    <td style="text-align: start; width: 14%">
                                        <span class="checkbox">' . addX($row['checkboxBasico']) . '</span>
                                        <span>BÁSICA&nbsp;</span>
                                    </td>
                                    <td style="text-align: start; width: 25% padding: 3px">
                                        <span class="checkbox">' . addX($row['checkboxExames']) . '</span>
                                        <span>EXAMES&nbsp;</span>
                                    </td>
                                    <td style="text-align: start; width: 21% padding: 3px">  
                                        <span class="checkbox">' . addX($row['checkboxUtiAdulto']) . '</span>
                                        <span>UTI ADULTO&nbsp;</span>
                                    </td>
                                    <td style="text-align: start; width: 20% padding: 3px">
                                       <span class="checkbox">' . addX($row['checkboxUtiNeo']) . '</span>
                                        <span>UTI NEO&nbsp;</span>
                                    </td>
                                    <td style="text-align: start; width: 16% padding: 3px">
                                      <span class="checkbox">' . addX($row['checkboxEventos']) . '</span>
                                        <span>EVENTOS&nbsp;</span>
                                    </td>
                                    <td style="text-align: start; width: 19% padding: 3px">
                                        <span class="checkbox">' . addX($row['checkboxParticular']) . '</span>
                                        <span>PARTICULAR&nbsp;</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: start; width: 14%">
                                        <span class="checkbox">' . addX($row['checkboxAph']) . '</span>
                                        <span>APH&nbsp;</span>
                                    </td>
                                    <td style="text-align: start; width: 25%">
                                        <span class="checkbox">' . addX($row['checkboxTrasfHospitalar']) . '</span>
                                        <span>TRASF. HOSPITALAR&nbsp;</span>
                                    </td>
                                    <td style="text-align: start; width: 21%">  
                                        <span class="checkbox">' . addX($row['checkboxAltaHospitalar']) . '</span>
                                        <span>ALTA HOSPITALAR&nbsp;</span>
                                    </td>
                                    <td style="text-align: start; width: 20%">
                                        <span class="checkbox">' . addX($row['checkboxInternacao']) . '</span>
                                        <span>INTERNAÇÃO&nbsp;</span>
                                    </td>
                                    <td style="text-align: start; width: 16%">
                                        <span class="checkbox">' . addX($row['checkboxSivInt']) . '</span>
                                        <span>SIV - INT&nbsp;</span>
                                    </td>
                                    <td style="text-align: start; width: 19%">
                                       
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>

                <tr>
                     <td style="border-right: 2px solid black; border-left: 2px solid black; border-top: 2px solid black; padding: 0px;">
                       <table style="border-collapse: collapse; width: 100%;">
                            <tbody>
                                <tr>
                                    <td style="text-align: center;  width: 15% font-weight: bold; ">
                                        Tipos de Exames
                                    </td>
                                    <td style=" padding: 5px; width: 57%;">
                                        <table style="border-collapse: collapse; width: 100%;">
                                                <tbody>
                                                    <tr>
                                                        <td style="text-align: start; width: 15%">
                                                            <span class="checkbox">' . addX($row['checkboxCate']) . '</span>
                                                            <span>CATE&nbsp;</span>
                                                        </td>
                                                        <td style="text-align: start; width: 25%">
                                                            <span class="checkbox">' . addX($row['checkboxTomo']) . '</span>
                                                            <span>TOMO&nbsp;</span>
                                                        </td>
                                                        <td style="text-align: start; width: 15%">  
                                                            <span class="checkbox">' . addX($row['checkboxRx']) . '</span>
                                                            <span>RX&nbsp;</span>
                                                        </td>
                                                        <td style="text-align: start; width: 19%">
                                                        <span class="checkbox">' . addX($row['checkboxCprs']) . '</span>
                                                            <span>CPRS&nbsp;</span>
                                                        </td>
                                                        <td style="text-align: start; width: 22%">
                                                        <span class="checkbox">' . addX($row['checkboxPetScam']) . '</span>
                                                            <span>PET SCAM&nbsp;</span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="text-align: start; width: 15%">
                                                            <span class="checkbox">' . addX($row['checkboxGtt']) . '</span>
                                                            <span>GTT&nbsp;</span>
                                                        </td>
                                                        <td style="text-align: start; width: 25%">
                                                            <span class="checkbox">' . addX($row['checkboxTqtTqm']) . '</span>
                                                            <span>TQT /TQM&nbsp;</span>
                                                        </td>
                                                        <td style="text-align: start; width: 15%">  
                                                            <span class="checkbox">' . addX($row['checkboxUs']) . '</span>
                                                            <span>US&nbsp;</span>
                                                        </td>
                                                        <td style="text-align: start; width: 19%">
                                                            <span class="checkbox">' . addX($row['checkboxRmn']) . '</span>
                                                            <span>RMN&nbsp;</span>
                                                        </td>
                                                        <td style="text-align: start; width: 22%">
                                                            <span class="checkbox">' . addX($row['checkboxTcHip']) . '</span>
                                                            <span>T. C . HIPER&nbsp;</span>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                        <td style="width: 28%;height: auto;">
                                            <span style="font-weight: bold;">Outro:</span>  
                                            <p style="font-size: 10px; margin: 0; padding: 5px; word-wrap: break-word; overflow-wrap: break-word;">' . $row['txtOutrosExames'] . '</p>
                                        </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td style="border-right: 2px solid black; border-left: 2px solid black; border-top: 2px solid black; padding: 10px;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tbody>
                                <tr style="width: 100%;">
                                    <td style="">
                                         <table style="width: 100%; border-collapse: collapse;">
                                            <tbody>
                                                <tr style="width: 100%;">
                                                    <td style="width:auto;">
                                                        Empresa:
                                                    </td>
                                                    <td style="border-bottom: 1px solid black;">
                                                        ' . $row['razaoSocial'] . '
                                                    </td>
                                                    <td style="width:auto;">
                                                        Solicitante:
                                                    </td>
                                                    <td style="border-bottom: 1px solid black;">
                                                         ' . $row['solicitante'] . '
                                                    </td>
                                                    <td style="width:auto;">
                                                        Data:
                                                    </td>
                                                    <td style="border-bottom: 1px solid black;">
                                                          ' . $row['dataSolicitadaFormatada'] . '
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>   
                                    </td>
                                </tr>
                                <tr style="width: 100%;">
                                    <td style="">
                                       <table style="width: 100%; border-collapse: collapse;">
                                            <tbody>
                                                <tr style="width: 100%;">
                                                    <td style="width:auto;">
                                                        Origem:
                                                    </td>
                                                    <td style="border-bottom: 1px solid black; font-size: 10px;">
                                                        ' . $row['origem'] . '
                                                    </td>
                                                    <td style="width:auto;">
                                                        X
                                                    </td>
                                                    <td style="border-bottom: 1px solid black; font-size: 10px;">
                                                        ' . $destinos[0] . '
                                                    </td>
                                                    <td style="width:auto;">
                                                        X
                                                    </td>
                                                    <td style="border-bottom: 1px solid black; font-size: 10px;">
                                                         ' . $destinos[1] . '
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>   
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td style="border-right: 2px solid black; border-left: 2px solid black; border-top: 2px solid black; padding: 10px;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tbody>
                                <tr style="width: 100%;">
                                    <td style="">
                                         <table style="width: 100%; border-collapse: collapse;">
                                            <tbody>
                                                <tr style="width: 100%;">
                                                    <td style="width:auto;">
                                                        Nome:
                                                    </td>
                                                    <td style="border-bottom: 1px solid black;">
                                                        ' . $row['paciente'] . '
                                                    </td>
                                                    <td style="width:auto;">
                                                        Idade: 
                                                    </td>
                                                    <td style="border-bottom: 1px solid black;">
                                                        ' . $row['numberIdade'] . ' 
                                                    </td>
                                                    <td style="width:auto;">
                                                        Contato:
                                                    </td>
                                                    <td style="border-bottom: 1px solid black;">
                                                         ' . $row['txtContato'] . ' 
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>   
                                    </td>
                                </tr>
                                <tr style="width: 100%;">
                                    <td style="">
                                       <table style="width: 100%; border-collapse: collapse;">
                                            <tbody>
                                                <tr style="width: 100%;">
                                                    <td style="width:auto;">
                                                        Sexo:
                                                    </td>
                                                    <td style="width:auto;">
                                                        <span class="checkbox">' . addX2(1, $row['selectSexo'])  . '</span>
                                                        <span>Masculino&nbsp;</span>
                                                    </td>
                                                    <td style="width:auto;">
                                                        <span class="checkbox">' . addX2(2, $row['selectSexo'])  . '</span>
                                                        <span>Feminino&nbsp;</span>
                                                    </td>
                                                    <td style="width:auto;">
                                                       
                                                    </td>
                                                    <td style="width:auto;">
                                                        
                                                    </td>
                                                    <td style="width:auto;">
                                                      
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>   
                                    </td>
                                </tr>
                                <tr style="width: 100%;">
                                    <td style="">
                                       <table style="width: 100%; border-collapse: collapse;">
                                            <tbody>
                                                <tr style="width: 100%;">
                                                    <td style="width:auto;">
                                                        SUS/CONVENIO:
                                                    </td>
                                                    <td style="border-bottom: 1px solid black; font-size: 10px;">
                                                        ' . $row['convenio'] . '
                                                    </td>
                                                    <td style="width:auto;">
                                                       NºCARTEIRINHA:
                                                    </td>
                                                    <td style="border-bottom: 1px solid black; font-size: 10px;">
                                                         ' . $row['nrCartao'] . '
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>   
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td style="border-right: 2px solid black; border-left: 2px solid black; border-top: 2px solid black; padding: 0px;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead >
                                <tr style="border-bottom: 1px solid black;">
                                    <th scope="col" style="background-color: rgba(160, 160, 160, 0.5); border-right: 1px solid black; text-align: center; border-bottom: 1px solid black;">Saída
                                        Base
                                    </th>
                                    <th scope="col" style="background-color: rgba(160, 160, 160, 0.5);  border-right: 1px solid black; text-align: center; border-bottom: 1px solid black;">Origem
                                    </th>
                                    <th scope="col" style="background-color: rgba(160, 160, 160, 0.5);  border-right: 1px solid black; text-align: center; border-bottom: 1px solid black;">Destino
                                    </th>
                                    <th scope="col" style="background-color: rgba(160, 160, 160, 0.5);border-right: 1px solid black; text-align: center; border-bottom: 1px solid black;">Destino
                                    </th>
                                    <th scope="col" style="background-color: rgba(160, 160, 160, 0.5); text-align: center; border-bottom: 1px solid black;">Chegada
                                        Base
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="border-right: 1px solid black; padding: 0px;">
                                            <span class=""> &nbsp; Km: </span> ' . $row['kmInicial'] . '
                                    </td>
                                    <td style="border-right: 1px solid black; padding: 0px;">
                                        <table style="width: 100%; border-collapse: collapse;">
                                            <tbody>
                                                <tr style="width: 100%;">
                                                    <td style="border-bottom: 1px solid black;">
                                                         <span >Km:&nbsp;</span> ' . $row['kmOrigem'] . '
                                                    </td>
                                                </tr>
                                                <tr style="width: 100%;">
                                                    <td >
                                                        <span >Hora Chegada:&nbsp;</span> ' . $row['chegadaOrigemFormatada'] . ' 
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                    <td style="border-right: 1px solid black; padding: 0px;">
                                        <table style="width: 100%; border-collapse: collapse;">
                                            <tbody>
                                                <tr style="width: 100%;">
                                                    <td style="border-bottom: 1px solid black;">
                                                         <span >Km:&nbsp;</span>' . $detalhesDestinos[0]['km'] . ' 
                                                    </td>
                                                </tr>
                                                <tr style="width: 100%;">
                                                    <td style="">
                                                        <span >Hora Chegada:&nbsp;</span> ' . $detalhesDestinos[0]['horaChegada'] . ' 
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                    <td style="border-right: 1px solid black; padding: 0px;">
                                        <table style="width: 100%; border-collapse: collapse;">
                                            <tbody>
                                                <tr style="width: 100%;">
                                                    <td style="border-bottom: 1px solid black; ">
                                                         <span >Km:&nbsp;</span> ' . $detalhesDestinos[1]['km'] . '
                                                    </td>
                                                </tr>
                                                <tr style="width: 100%;">
                                                    <td style="">
                                                        <span >Hora Chegada:&nbsp;</span> ' . $detalhesDestinos[1]['horaChegada'] . ' 
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                    <td style=" padding: 0px;">
                                        <span class=""> &nbsp; Km:</span> ' . $row['kmFinal'] . ' 
                                    </td>
                                </tr>
                                <tr>
                                    <td style="border-right: 1px solid black; border-top: 1px solid black; padding: 0px;">
                                        <span class=""> &nbsp; Hora: </span> ' . $row['dataHoraInicioFormatada'] . ' 
                                        <br>
                                        <br>
                                        <br>
                                        <br>                                       
                                        <br>                                       
                                        <br>                                       
                                        <br>                                       
                                    </td>
                                    <td style="border-right: 1px solid black; border-top: 1px solid black; padding: 0px;">
                                        <table style="width: 100%; border-collapse: collapse;">
                                            <tbody>
                                                <tr style="width: 100%;">
                                                    <td style="border-bottom: 1px solid black; ">
                                                         <span >Hora Saída:&nbsp;</span>  ' . $row['saidaOrigemFormatada'] . ' 
                                                    </td>
                                                </tr>
                                                <tr style="width: 100%;">
                                                    <td >
                                                        <span >Visto Origem:&nbsp;</span>
                                                        <br>
                                                        <br>
                                                        <br>
                                                        <br>
                                                        <br>
                                                        <br>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                    <td style="border-right: 1px solid black; border-top: 1px solid black; padding: 0px;">
                                        <table style="width: 100%; border-collapse: collapse;">
                                            <tbody>
                                                <tr style="width: 100%;">
                                                    <td style="border-bottom: 1px solid black; ">
                                                         <span >Hora Saída:&nbsp;</span>' . $detalhesDestinos[0]['horaSaida'] . ' 
                                                    </td>
                                                </tr>
                                                <tr style="width: 100%;">
                                                    <td>
                                                        <span >Visto Destino:&nbsp;</span>
                                                        <br>
                                                        <br>
                                                        <br>
                                                        <br>
                                                        <br>
                                                        <br>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                    <td style="border-right: 1px solid black; border-top: 1px solid black; padding: 0px;">
                                        <table style="width: 100%; border-collapse: collapse;">
                                            <tbody>
                                                <tr style="width: 100%;">
                                                    <td style="border-bottom: 1px solid black; ">
                                                         <span >Hora Saída:&nbsp;</span>' . $detalhesDestinos[1]['horaSaida'] . ' 
                                                    </td>
                                                </tr>
                                                <tr style="width: 100%;">
                                                    <td>
                                                        <span >Visto Destino:&nbsp;</span>
                                                        <br>
                                                        <br>
                                                        <br>
                                                        <br>
                                                        <br>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                    <td style=" border-top: 1px solid black; padding: 0px;">
                                        <span class=""> &nbsp; Hora: </span>' . $row['dataHoraFimFormatada'] . '
                                        <br>
                                        <br>
                                        <br>
                                        <br>
                                        <br>
                                        <br>
                                        <br>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td style="border-right: 2px solid black; border-left: 2px solid black; border-top: 2px solid black; padding: 5px;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tbody>
                                <tr style="width: 100%;">
                                    <td style=" font-weight: bold; width: 10%">
                                        Neurológica
                                    </td>
                                    <td style="text-align: start; width: 15%">
                                        &nbsp;<span class="checkbox">' . addX($row['checkboxLucido']) . '</span>
                                        Lúcido
                                    </td>
                                    <td style="text-align: start; width: 15%">
                                        &nbsp;<span class="checkbox">' . addX($row['checkboxOrientado']) . '</span>
                                        Orientado
                                    </td>
                                    <td style="text-align: start; width: 15%">
                                        &nbsp;<span class="checkbox">' . addX($row['checkboxConsciente']) . '</span>
                                        Consciênte
                                    </td>
                                    <td style="text-align: start; width: 15%">
                                        &nbsp;<span class="checkbox">' . addX($row['checkboxConfuso']) . '</span>
                                        Confuso
                                    </td>
                                    <td style="text-align: start; width: 15%">
                                        &nbsp;<span class="checkbox">' . addX($row['checkboxComunicativo']) . '</span>
                                        Comunicativo
                                    </td>
                                    <td style="text-align: start; width: 15%">
                                        &nbsp;<span class="checkbox">' . addX($row['checkboxNaoVerbaliza']) . '</span>
                                        Não Verbaliza
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td style="border-right: 2px solid black; border-left: 2px solid black; border-top: 2px solid black; padding: 5px;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tbody>
                                <tr style="width: 100%;">
                                    <td style=" font-weight: bold; width: 10%">
                                        Pupilas
                                    </td>
                                    <td style="text-align: start; width: 15%">
                                        &nbsp;<span class="checkbox">' . addX($row['checkboxIsocoricas']) . '</span>
                                        Isocóricas 
                                    </td>
                                    <td style="text-align: start; width: 15%">
                                        &nbsp;<span class="checkbox">' . addX($row['checkboxAnisocoricas']) . '</span>
                                        Anisocorica
                                    </td>
                                    <td style="text-align: start; width: 15%">
                                        &nbsp;<span class="checkbox">' . addX($row['checkboxMidriatricas']) . '</span>
                                        Midriátrica
                                    </td>
                                    <td style="text-align: start; width: 15%">
                                        &nbsp;<span class="checkbox">' . addX($row['checkboxMiotica']) . '</span>
                                        Miotica 
                                    </td>
                                    <td style="text-align: start; width: 15%">
                                        &nbsp;<span class="checkbox">' . addX($row['checkboxDE']) . '</span>
                                        D ou E
                                    </td>
                                    <td style="text-align: start; width: 15%">
                                        &nbsp;<span class="checkbox">' . addX($row['checkboxMaior']) . '</span>
                                        É Maior
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                 
                <tr>
                    <td style="border-right: 2px solid black; border-left: 2px solid black; border-top: 2px solid black; padding: 5px;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tbody>
                                <tr style="width: 100%;">
                                    <td style=" font-weight: bold; width: 11%">
                                        Respitatória
                                    </td>
                                    <td style="text-align: start; width: 15%">
                                        &nbsp;<span class="checkbox">' . addX($row['checkboxEupneico']) . '</span>
                                        Eupnéico  
                                    </td>
                                    <td style="text-align: start; width: 15%">
                                        &nbsp;<span class="checkbox">' . addX($row['checkboxTaquipneico']) . '</span>
                                        Taquipneico  
                                    </td>
                                    <td style="text-align: start; width: 15%">
                                        &nbsp;<span class="checkbox">' . addX($row['checkboxBradipneico']) . '</span>
                                        Bradipneico
                                    </td>
                                    <td style="text-align: start; width: 15%">
                                        &nbsp;<span class="checkbox">' . addX($row['checkboxDispneico']) . '</span>
                                        Dispneico  
                                    </td>
                                    <td style="text-align: start; width: 15%">
                                        &nbsp;<span class="checkbox">' . addX($row['checkboxApneia']) . '</span>
                                        Apneia  
                                    </td>
                                    <td style="text-align: start; width: 14%">
                                        
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                
                <tr>
                    <td style="border-right: 2px solid black; border-left: 2px solid black; border-top: 2px solid black; padding: 5px;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tbody>
                                <tr style="width: 100%;">
                                    <td style=" font-weight: bold; width: 10%">
                                        Circulatório
                                    </td>
                                    <td style="text-align: start; width: 15%">
                                        &nbsp;<span class="checkbox">' . addX($row['checkboxNormocardico']) . '</span>
                                        Normocárdico 
                                    </td>
                                    <td style="text-align: start; width: 15%">
                                        &nbsp;<span class="checkbox">' . addX($row['checkboxTarquicardico']) . '</span>
                                        Tarquicardico  
                                    </td>
                                    <td style="text-align: start; width: 15%">
                                        &nbsp;<span class="checkbox">' . addX($row['checkboxBradicardico']) . '</span>
                                        Bradicardico
                                    </td>
                                    <td style="text-align: start; width: 15%">
                                        &nbsp;<span class="checkbox">' . addX($row['checkboxFiliforme']) . '</span>
                                        Filiforme 
                                    </td>
                                    <td style="text-align: start; width: 15%">
                                       
                                    </td>
                                    <td style="text-align: start; width: 15%">
                                       
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                
                <tr>
                    <td style="border-right: 2px solid black; border-left: 2px solid black; border-top: 2px solid black; padding: 5px;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tbody>
                                <tr style="width: 100%;">
                                    <td style=" font-weight: bold; width: 10%">
                                        Abdomem
                                    </td>
                                    <td style="text-align: start; width: 15%">
                                        &nbsp;<span class="checkbox">' . addX($row['checkboxPlano']) . '</span>
                                        Plano  
                                    </td>
                                    <td style="text-align: start; width: 15%">
                                        &nbsp;<span class="checkbox">' . addX($row['checkboxGloboso']) . '</span>
                                        Globoso    
                                    </td>
                                    <td style="text-align: start; width: 15%">
                                        &nbsp;<span class="checkbox">' . addX($row['checkboxEscavado']) . '</span>
                                        Escavado 
                                    </td>
                                    <td style="text-align: start; width: 15%">
                                        &nbsp;<span class="checkbox">' . addX($row['checkboxFlacido']) . '</span>
                                        Flácido  
                                    </td>
                                    <td style="text-align: start; width: 15%">
                                        &nbsp;<span class="checkbox">' . addX($row['checkboxEmAventa']) . '</span>
                                        Em Avental  
                                    </td>
                                    <td style="text-align: start; width: 15%">
                                        &nbsp;<span class="checkbox">' . addX($row['checkboxGravidico']) . '</span>
                                        Gravidico   
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                
                <tr>
                    <td style="border-right: 2px solid black; border-left: 2px solid black; border-top: 2px solid black; padding: 5px;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tbody>
                                <tr style="width: 100%;">
                                    <td style="border-right: 2px solid black;">
                                        <table style="width: 100%; border-collapse: collapse;">
                                            <tbody>
                                                <tr style="width: 100%;">
                                                    <td style="font-weight: bold; width: 40%">
                                                       &nbsp;&nbsp; Membros Superior
                                                    </td>
                                                    <td style="text-align: start; width: 30%">
                                                        &nbsp;<span class="checkbox">' . addX2(1, $row['selectMembrosSuperior']) . '</span>
                                                        Alterados   
                                                    </td>
                                                    <td style="text-align: start; width: 30%">
                                                        &nbsp;<span class="checkbox">' . addX2(2, $row['selectMembrosSuperior'])  . '</span>
                                                        Não Alterados  
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                    <td>
                                        <table style="width: 100%; border-collapse: collapse;">
                                            <tbody>
                                                <tr style="width: 100%;">
                                                    <td style="font-weight: bold; width: 40%">
                                                       &nbsp;&nbsp; Membros Inferiores
                                                    </td>
                                                    <td style="text-align: start; width: 30%">
                                                        &nbsp;<span class="checkbox">' . addX2(1, $row['selectMembrosInferior']) . '</span>
                                                        Alterados   
                                                    </td>
                                                    <td style="text-align: start; width: 30%">
                                                        &nbsp;<span class="checkbox">' . addX2(2, $row['selectMembrosInferior'])  . '</span>
                                                        Não Alterados  
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td style="border-right: 2px solid black; border-left: 2px solid black; border-top: 2px solid black; padding: 5px;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tbody>
                                <tr style="width: 100%;">
                                    <td style="text-align: center; font-weight: bold;">
                                        Sinais Vitais
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td style="border-right: 2px solid black; border-left: 2px solid black; border-top: 2px solid black; padding: 0px;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                 <tr style="border-bottom: 2px solid black;">
                                    <th scope="col" style="border-right: 2px solid black; text-align: center; border-bottom: 2px solid black;">HORA</th>
                                    <th scope="col" style="border-right: 2px solid black; text-align: center; border-bottom: 2px solid black;">PA</th>
                                    <th scope="col" style="border-right: 2px solid black; text-align: center; border-bottom: 2px solid black;">FC</th>
                                    <th scope="col" style="border-right: 2px solid black; text-align: center; border-bottom: 2px solid black;">FR</th>
                                    <th scope="col" style="border-right: 2px solid black; text-align: center; border-bottom: 2px solid black;">GLASGOW</th>
                                    <th scope="col" style="border-right: 2px solid black; text-align: center; border-bottom: 2px solid black;">TEMP</th>
                                    <th scope="col" style="border-right: 2px solid black; text-align: center; border-bottom: 2px solid black;">Sat O2</th>
                                    <th scope="col" style="black; text-align: center; border-bottom: 2px solid black;">Hgt</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px;text-align:center">
                                        ' . $row['sinaisVitais'][0][0] . '
                                    </td>
                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px;text-align:center">
                                        ' . $row['sinaisVitais'][0][1] . '
                                    </td>
                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px;text-align:center">
                                        ' . $row['sinaisVitais'][0][2] . '
                                    </td>
                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px;text-align:center">
                                       ' . $row['sinaisVitais'][0][3] . '
                                    </td>
                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px;text-align:center">
                                      ' . $row['sinaisVitais'][0][4] . '
                                    </td>
                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px;text-align:center">
                                       ' . $row['sinaisVitais'][0][5] . '
                                    </td>
                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px;text-align:center">
                                       ' . $row['sinaisVitais'][0][6] . '
                                    </td>
                                    <td style="border-bottom: 2px solid black; padding: 0px;text-align:center">
                                       ' . $row['sinaisVitais'][0][7] . '
                                    </td>
                                </tr>
                                  <tr>
                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px;text-align:center">
                                        ' . $row['sinaisVitais'][1][0] . '
                                    </td>
                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px;text-align:center">
                                        ' . $row['sinaisVitais'][1][1] . '
                                    </td>
                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px;text-align:center">
                                        ' . $row['sinaisVitais'][1][2] . '
                                    </td>
                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px;text-align:center">
                                       ' . $row['sinaisVitais'][1][3] . '
                                    </td>
                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px;text-align:center">
                                      ' . $row['sinaisVitais'][1][4] . '
                                    </td>
                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px;text-align:center">
                                       ' . $row['sinaisVitais'][1][5] . '
                                    </td>
                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px;text-align:center">
                                       ' . $row['sinaisVitais'][1][6] . '
                                    </td>
                                    <td style="border-bottom: 2px solid black; padding: 0px;text-align:center">
                                       ' . $row['sinaisVitais'][1][7] . '
                                    </td>
                                </tr>
                                <tr>
                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px;text-align:center">
                                        ' . $row['sinaisVitais'][2][0] . '
                                    </td>
                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px;text-align:center">
                                        ' . $row['sinaisVitais'][2][1] . '
                                    </td>
                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px;text-align:center">
                                        ' . $row['sinaisVitais'][2][2] . '
                                    </td>
                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px;text-align:center">
                                       ' . $row['sinaisVitais'][2][3] . '
                                    </td>
                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px;text-align:center">
                                      ' . $row['sinaisVitais'][2][4] . '
                                    </td>
                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px;text-align:center">
                                       ' . $row['sinaisVitais'][2][5] . '
                                    </td>
                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px;text-align:center">
                                       ' . $row['sinaisVitais'][2][6] . '
                                    </td>
                                    <td style="border-bottom: 2px solid black; padding: 0px;text-align:center">
                                       ' . $row['sinaisVitais'][2][7] . '
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>


                <tr>
                    <td style="border-right: 2px solid black; border-left: 2px solid black; border-top: 2px solid black; padding: 0px;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tbody>
                                <tr style="width: 100%;">
                                    <td style="border-right: 2px solid black; width: 27%;">
                                        <table style="width: 100%; border-collapse: collapse;">
                                            <tbody>
                                                <tr style="width: 100%;">
                                                    <td style=" font-weight: bold;">
                                                       Abertura ocular:
                                                    </td>
                                                    <br>
                                                    <br>
                                                </tr>
                                                <tr style="width: 100%;">
                                                    <td style="font-weight: bold;">
                                                        <table style="width: 100%; border-collapse: collapse;">
                                                            <tbody>
                                                                <tr style="width: 100%;">
                                                                    <td style="width: 50%;">
                                                                        &nbsp;<span class="checkbox">' . addX($row['checkboxEspontanea']) . '</span>
                                                                        Espontânea 4<br>
                                                                        &nbsp;<span class="checkbox">' . addX($row['checkboxVoz4']) . '</span>
                                                                        voz 3
                                                                    </td>
                                                                    <td style="width: 50%;">
                                                                        &nbsp;<span class="checkbox">' . addX($row['checkboxDor2']) . '</span>
                                                                        A dor 2 <br>
                                                                        &nbsp;<span class="checkbox">' . addX($row['checkboxNenhuma1']) . '</span>
                                                                        Nenhuma 1
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                    <br>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                    <td style="border-right: 2px solid black;width: 26%;">
                                        <table style="width: 100%; border-collapse: collapse;">
                                            <tbody>
                                                <tr style="width: 100%;">
                                                    <td style=" font-weight: bold;">
                                                       Resposta Verbal:
                                                    </td>
                                                </tr>
                                               <tr style="width: 100%;">
                                                    <td style="font-weight: bold;">
                                                        <table style="width: 100%; border-collapse: collapse;">
                                                            <tbody>
                                                                <tr style="width: 100%;">
                                                                    <td style="width: 50%;">
                                                                        &nbsp;<span class="checkbox">' . addX($row['checkboxOrientada5']) . '</span>
                                                                        Orientada 5<br>
                                                                        &nbsp;<span class="checkbox">' . addX($row['checkboxConfusa4']) . '</span>
                                                                        Confusa 4 <br>
                                                                        &nbsp;<span class="checkbox">' . addX($row['checkboxPalavras3']) . '</span>
                                                                        Palavras Improórias 3<br>
                                                                        &nbsp;<span class="checkbox">' . addX($row['checkboxPalavras2']) . '</span>                                                                    
                                                                        Palavras Incomprêencivas 2
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                    <td style="width: 45%;">
                                        <table style="width: 100%; border-collapse: collapse;">
                                            <tbody>
                                                <tr style="width: 100%;">
                                                    <td style=" font-weight: bold;">
                                                       Resposta Motora: 
                                                    </td>
                                                    <br>
                                                </tr>
                                                <tr style="width: 100%;">
                                                    <td style="font-weight: bold;">
                                                        <table style="width: 100%; border-collapse: collapse;">
                                                            <tbody>
                                                                <tr style="width: 100%;">
                                                                    <td style="width: 50%;">
                                                                        &nbsp;<span class="checkbox">' . addX($row['checkboxObdece6']) . '</span>
                                                                        Obdece comandos 6<br>
                                                                        &nbsp;<span class="checkbox">' . addX($row['checkboxLocaliza5']) . '</span>
                                                                        Localiza Dor 5<br>
                                                                        &nbsp;<span class="checkbox">' . addX($row['checkboxMovimentos4']) . '</span>                                                                        
                                                                        Movimentos de retardia 4
                                                                    </td>
                                                                    <td style="width: 50%;">
                                                                        &nbsp;<span class="checkbox">' . addX($row['checkboxFlexao3']) . '</span>
                                                                        Flexão Anormal 3<br>
                                                                        &nbsp;<span class="checkbox">' . addX($row['checkboxExtensao2']) . '</span>
                                                                        Extensão Anormal 2<br>
                                                                        &nbsp;<span class="checkbox">' . addX($row['checkboxNenhuma']) . '</span>
                                                                        Nenhuma
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                    <br>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td style="border-right: 2px solid black; border-left: 2px solid black; border-top: 2px solid black; padding: 0px;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tbody>
                                <tr style="width: 100%;">
                                    <td style="border-right: 2px solid black; font-weight: bold; width: auto; text-align: center;">
                                        T<br>R<br>A<br>U<br>M<br>A
                                    </td>
                                    <td style="width: 95%;">
                                        <table style="width: 100%; border-collapse: collapse;">
                                            <thead>
                                                <tr style="border-bottom: 2px solid black;">
                                                    <th scope="col-1"  style="border-right: 2px solid black; text-align: center; border-bottom: 2px solid black;">Descrição</th>
                                                    <th scope="col-1"  style="border-right: 2px solid black; text-align: center; border-bottom: 2px solid black;">Crâ</th>
                                                    <th scope="col-1"  style="border-right: 2px solid black; text-align: center; border-bottom: 2px solid black;">Face</th>
                                                    <th scope="col-2"  style="border-right: 2px solid black; text-align: center; border-bottom: 2px solid black;">Col</th>
                                                    <th scope="col-2"  style="border-right: 2px solid black; text-align: center; border-bottom: 2px solid black;">Tór</th>
                                                    <th scope="col-1"  style="border-right: 2px solid black; text-align: center; border-bottom: 2px solid black;">Bac</th>
                                                    <th scope="col-2"  style="border-right: 2px solid black; text-align: center; border-bottom: 2px solid black;">Msd</th>
                                                    <th scope="col-2"  style="border-right: 2px solid black; text-align: center; border-bottom: 2px solid black;">Mse</th>
                                                    <th scope="col-2"  style="border-right: 2px solid black; text-align: center; border-bottom: 2px solid black;">Mid</th>
                                                    <th scope="col-2"  style=" text-align: center; border-bottom: 2px solid black;">Mie</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; width: auto;">
                                                        Escoriação
                                                    </td>
                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; text-align: center; font-weight: bold;">
                                                        ' . $row['trauma'][0][1] . '
                                                    </td>
                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; text-align: center; font-weight: bold;">
                                                        ' . $row['trauma'][0][2] . '
                                                    </td>
                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; text-align: center; font-weight: bold;">
                                                        ' . $row['trauma'][0][3] . '
                                                    </td>
                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; text-align: center; font-weight: bold;">
                                                        ' . $row['trauma'][0][4] . '
                                                    </td>
                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; text-align: center; font-weight: bold;">
                                                        ' . $row['trauma'][0][5] . '
                                                    </td>
                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; text-align: center; font-weight: bold;">
                                                        ' . $row['trauma'][0][6] . '
                                                    </td>
                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; text-align: center; font-weight: bold;">
                                                        ' . $row['trauma'][0][7] . '
                                                    </td>
                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; text-align: center; font-weight: bold;">
                                                        ' . $row['trauma'][0][8] . '
                                                    </td>
                                                    <td style=" border-bottom: 2px solid black; padding: 0px; text-align: center; font-weight: bold;">
                                                        ' . $row['trauma'][0][9] . '
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px;">
                                                        Cont
                                                    </td>
                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; text-align: center; font-weight: bold;">
                                                        ' . $row['trauma'][1][1] . '
                                                    </td>
                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; text-align: center; font-weight: bold;">
                                                        ' . $row['trauma'][1][2] . '
                                                    </td>
                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; text-align: center; font-weight: bold;">
                                                        ' . $row['trauma'][1][3] . '
                                                    </td>
                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; text-align: center; font-weight: bold;">
                                                        ' . $row['trauma'][1][4] . '
                                                    </td>
                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; text-align: center; font-weight: bold;">
                                                        ' . $row['trauma'][1][5] . '
                                                    </td>
                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; text-align: center; font-weight: bold;">
                                                        ' . $row['trauma'][1][6] . '
                                                    </td>
                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; text-align: center; font-weight: bold;">
                                                        ' . $row['trauma'][1][7] . '
                                                    </td>
                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; text-align: center; font-weight: bold;">
                                                        ' . $row['trauma'][1][8] . '
                                                    </td>
                                                    <td style=" border-bottom: 2px solid black; padding: 0px; text-align: center; font-weight: bold;">
                                                        ' . $row['trauma'][1][9] . '
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px;">
                                                        Ferim
                                                    </td>
                                                   <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; text-align: center; font-weight: bold;">
                                                        ' . $row['trauma'][2][1] . '
                                                    </td>
                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; text-align: center; font-weight: bold;">
                                                        ' . $row['trauma'][2][2] . '
                                                    </td>
                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; text-align: center; font-weight: bold;">
                                                        ' . $row['trauma'][2][3] . '
                                                    </td>
                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; text-align: center; font-weight: bold;">
                                                        ' . $row['trauma'][2][4] . '
                                                    </td>
                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; text-align: center; font-weight: bold;">
                                                        ' . $row['trauma'][2][5] . '
                                                    </td>
                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; text-align: center; font-weight: bold;">
                                                        ' . $row['trauma'][2][6] . '
                                                    </td>
                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; text-align: center; font-weight: bold;">
                                                        ' . $row['trauma'][2][7] . '
                                                    </td>
                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; text-align: center; font-weight: bold;">
                                                        ' . $row['trauma'][2][8] . '
                                                    </td>
                                                    <td style=" border-bottom: 2px solid black; padding: 0px; text-align: center; font-weight: bold;">
                                                        ' . $row['trauma'][2][9] . '
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px;">
                                                        Luxa
                                                    </td>
                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; text-align: center; font-weight: bold;">
                                                        ' . $row['trauma'][3][1] . '
                                                    </td>
                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; text-align: center; font-weight: bold;">
                                                        ' . $row['trauma'][3][2] . '
                                                    </td>
                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; text-align: center; font-weight: bold;">
                                                        ' . $row['trauma'][3][3] . '
                                                    </td>
                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; text-align: center; font-weight: bold;">
                                                        ' . $row['trauma'][3][4] . '
                                                    </td>
                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; text-align: center; font-weight: bold;">
                                                        ' . $row['trauma'][3][5] . '
                                                    </td>
                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; text-align: center; font-weight: bold;">
                                                        ' . $row['trauma'][3][6] . '
                                                    </td>
                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; text-align: center; font-weight: bold;">
                                                        ' . $row['trauma'][3][7] . '
                                                    </td>
                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; text-align: center; font-weight: bold;">
                                                        ' . $row['trauma'][3][8] . '
                                                    </td>
                                                    <td style=" border-bottom: 2px solid black; padding: 0px; text-align: center; font-weight: bold;">
                                                        ' . $row['trauma'][3][9] . '
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px;">
                                                        Frat
                                                    </td>
                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; text-align: center; font-weight: bold;">
                                                        ' . $row['trauma'][4][1] . '
                                                    </td>
                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; text-align: center; font-weight: bold;">
                                                        ' . $row['trauma'][4][2] . '
                                                    </td>
                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; text-align: center; font-weight: bold;">
                                                        ' . $row['trauma'][4][3] . '
                                                    </td>
                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; text-align: center; font-weight: bold;">
                                                        ' . $row['trauma'][4][4] . '
                                                    </td>
                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; text-align: center; font-weight: bold;">
                                                        ' . $row['trauma'][4][5] . '
                                                    </td>
                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; text-align: center; font-weight: bold;">
                                                        ' . $row['trauma'][4][6] . '
                                                    </td>
                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; text-align: center; font-weight: bold;">
                                                        ' . $row['trauma'][4][7] . '
                                                    </td>
                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; text-align: center; font-weight: bold;">
                                                        ' . $row['trauma'][4][8] . '
                                                    </td>
                                                    <td style=" border-bottom: 2px solid black; padding: 0px; text-align: center; font-weight: bold;">
                                                        ' . $row['trauma'][4][9] . '
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px;">
                                                        Lace
                                                    </td>
                                                     <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; text-align: center; font-weight: bold;">
                                                        ' . $row['trauma'][5][1] . '
                                                    </td>
                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; text-align: center; font-weight: bold;">
                                                        ' . $row['trauma'][5][2] . '
                                                    </td>
                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; text-align: center; font-weight: bold;">
                                                        ' . $row['trauma'][5][3] . '
                                                    </td>
                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; text-align: center; font-weight: bold;">
                                                        ' . $row['trauma'][5][4] . '
                                                    </td>
                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; text-align: center; font-weight: bold;">
                                                        ' . $row['trauma'][5][5] . '
                                                    </td>
                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; text-align: center; font-weight: bold;">
                                                        ' . $row['trauma'][5][6] . '
                                                    </td>
                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; text-align: center; font-weight: bold;">
                                                        ' . $row['trauma'][5][7] . '
                                                    </td>
                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; text-align: center; font-weight: bold;">
                                                        ' . $row['trauma'][5][8] . '
                                                    </td>
                                                    <td style=" border-bottom: 2px solid black; padding: 0px; text-align: center; font-weight: bold;">
                                                        ' . $row['trauma'][5][9] . '
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td style="border-right: 2px solid black;  padding: 0px;">
                                                        Secç
                                                    </td>
                                                    <td style="border-right: 2px solid black; padding: 0px; text-align: center; font-weight: bold;">
                                                        ' . $row['trauma'][6][1] . '
                                                    </td>
                                                    <td style="border-right: 2px solid black; padding: 0px; text-align: center; font-weight: bold;">
                                                        ' . $row['trauma'][6][2] . '
                                                    </td>
                                                    <td style="border-right: 2px solid black; padding: 0px; text-align: center; font-weight: bold;">
                                                        ' . $row['trauma'][6][3] . '
                                                    </td>
                                                    <td style="border-right: 2px solid black; padding: 0px; text-align: center; font-weight: bold;">
                                                        ' . $row['trauma'][6][4] . '
                                                    </td>
                                                    <td style="border-right: 2px solid black;padding: 0px; text-align: center; font-weight: bold;">
                                                        ' . $row['trauma'][6][5] . '
                                                    </td>
                                                    <td style="border-right: 2px solid black;padding: 0px; text-align: center; font-weight: bold;">
                                                        ' . $row['trauma'][6][6] . '
                                                    </td>
                                                    <td style="border-right: 2px solid black; padding: 0px; text-align: center; font-weight: bold;">
                                                        ' . $row['trauma'][6][7] . '
                                                    </td>
                                                    <td style="border-right: 2px solid black; padding: 0px; text-align: center; font-weight: bold;">
                                                        ' . $row['trauma'][6][8] . '
                                                    </td>
                                                    <td style="padding: 0px; text-align: center; font-weight: bold;">
                                                        ' . $row['trauma'][6][9] . '
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                         
                    </td>
                </tr>

                <tr>
                    <td style="border-right: 2px solid black; border-left: 2px solid black; border-top: 2px solid black; padding: 0px;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tbody>
                                <tr style="width: 100%;">
                                    <td style="width: 5%; border-right: 2px solid black;">
                                        <br>
                                    </td>
                                    <td style="width: 40%;border-right: 2px solid black;">
                                         <table style="width: 100%; border-collapse: collapse;">
                                            <tbody>
                                                <tr style="width: 100%;">
                                                    <td style="border-bottom: 2px solid black; font-weight: bold; text-align: center;">
                                                        
                                                        QUEIMADURAS
                                                       
                                                    </td>
                                                </tr>
                                                <tr style="width: 100% ;padding: 0px;">
                                                    <td style="padding: 0px;">
                                                          <table style="width: 100%; border-collapse: collapse;">
                                                            <thead>
                                                                <tr style="border-bottom: 2px solid black;">
                                                                    <th scope="col-1"  style="border-right: 2px solid black; text-align: center; border-bottom: 2px solid black;"></th>
                                                                    <th scope="col-1"  style="border-right: 2px solid black; text-align: center; border-bottom: 2px solid black;">Cab</th>
                                                                    <th scope="col-1"  style="border-right: 2px solid black; text-align: center; border-bottom: 2px solid black;">Pesc</th>
                                                                    <th scope="col-2"  style="border-right: 2px solid black; text-align: center; border-bottom: 2px solid black;">T Ant</th>
                                                                    <th scope="col-2"  style="border-right: 2px solid black; text-align: center; border-bottom: 2px solid black;">Tpos</th>
                                                                    <th scope="col-1"  style="border-right: 2px solid black; text-align: center; border-bottom: 2px solid black;">Perín</th>
                                                                    <th scope="col-2"  style="border-right: 2px solid black; text-align: center; border-bottom: 2px solid black;">Msd</th>
                                                                    <th scope="col-2"  style="border-right: 2px solid black; text-align: center; border-bottom: 2px solid black;">Mse</th>
                                                                    <th scope="col-2"  style="border-right: 2px solid black; text-align: center; border-bottom: 2px solid black;">Mid</th>
                                                                    <th scope="col-2"  style=" text-align: center; border-bottom: 2px solid black;">Mie</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; text-align: center;">
                                                                        |
                                                                    </td>
                                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; font-weight: bold; text-align: center;">
                                                                        ' . $row['queimadura'][0][0] . '
                                                                    </td>
                                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; font-weight: bold; text-align: center;">
                                                                        ' . $row['queimadura'][0][1] . '
                                                                    </td>
                                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; font-weight: bold; text-align: center;">
                                                                        ' . $row['queimadura'][0][2] . '
                                                                    </td>
                                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; font-weight: bold; text-align: center;">
                                                                        ' . $row['queimadura'][0][3] . '
                                                                    </td>
                                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; font-weight: bold; text-align: center;">
                                                                        ' . $row['queimadura'][0][4] . '
                                                                    </td>
                                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; font-weight: bold; text-align: center;">
                                                                        ' . $row['queimadura'][0][5] . '
                                                                    </td>
                                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; font-weight: bold; text-align: center;">
                                                                        ' . $row['queimadura'][0][6] . '
                                                                    </td>
                                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; font-weight: bold; text-align: center;">
                                                                        ' . $row['queimadura'][0][7] . '
                                                                    </td>
                                                                    <td style=" border-bottom: 2px solid black; padding: 0px; font-weight: bold; text-align: center;">
                                                                        ' . $row['queimadura'][0][8] . '
                                                                    </td>
                                                                </tr>

                                                                <tr>
                                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; text-align: center;">
                                                                        ||
                                                                    </td>
                                                                     <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; font-weight: bold; text-align: center;">
                                                                        ' . $row['queimadura'][1][0] . '
                                                                    </td>
                                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; font-weight: bold; text-align: center;">
                                                                        ' . $row['queimadura'][1][1] . '
                                                                    </td>
                                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; font-weight: bold; text-align: center;">
                                                                        ' . $row['queimadura'][1][2] . '
                                                                    </td>
                                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; font-weight: bold; text-align: center;">
                                                                        ' . $row['queimadura'][1][3] . '
                                                                    </td>
                                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; font-weight: bold; text-align: center;">
                                                                        ' . $row['queimadura'][1][4] . '
                                                                    </td>
                                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; font-weight: bold; text-align: center;">
                                                                        ' . $row['queimadura'][1][5] . '
                                                                    </td>
                                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; font-weight: bold; text-align: center;">
                                                                        ' . $row['queimadura'][1][6] . '
                                                                    </td>
                                                                    <td style="border-right: 2px solid black; border-bottom: 2px solid black; padding: 0px; font-weight: bold; text-align: center;">
                                                                        ' . $row['queimadura'][1][7] . '
                                                                    </td>
                                                                    <td style=" border-bottom: 2px solid black; padding: 0px; font-weight: bold; text-align: center;">
                                                                        ' . $row['queimadura'][1][8] . '
                                                                    </td>
                                                                </tr>

                                                                <tr>
                                                                    <td style="border-right: 2px solid black; padding: 0px; text-align: center; width: 10%;">
                                                                        |||
                                                                    </td>
                                                                    <td style="border-right: 2px solid black; padding: 0px; width: 10%; font-weight: bold; text-align: center;">
                                                                        ' . $row['queimadura'][2][0] . '
                                                                    </td>
                                                                    <td style="border-right: 2px solid black;  padding: 0px; width: 10%; font-weight: bold; text-align: center;">
                                                                        ' . $row['queimadura'][2][0] . '
                                                                    </td>
                                                                    <td style="border-right: 2px solid black;  padding: 0px; width: 10%; font-weight: bold; text-align: center;">
                                                                        ' . $row['queimadura'][2][0] . '
                                                                    </td>
                                                                    <td style="border-right: 2px solid black;  padding: 0px; width: 10%; font-weight: bold; text-align: center;">
                                                                        ' . $row['queimadura'][2][0] . '
                                                                    </td>
                                                                    <td style="border-right: 2px solid black;  padding: 0px; width: 10%; font-weight: bold; text-align: center;">
                                                                        ' . $row['queimadura'][2][0] . '
                                                                    </td>
                                                                    <td style="border-right: 2px solid black;  padding: 0px; width: 10%; font-weight: bold; text-align: center;">
                                                                        ' . $row['queimadura'][2][0] . '
                                                                    </td>
                                                                    <td style="border-right: 2px solid black; padding: 0px; width: 10%; font-weight: bold; text-align: center;">
                                                                        ' . $row['queimadura'][2][0] . '
                                                                    </td>
                                                                    <td style="border-right: 2px solid black;  padding: 0px; width: 10%; font-weight: bold; text-align: center;">
                                                                        ' . $row['queimadura'][2][0] . '
                                                                    </td>
                                                                    <td style=" padding: 0px; font-weight: bold; text-align: center;">
                                                                        ' . $row['queimadura'][2][0] . '
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>   
                                    </td>
                                    <td style="width: 55%;">
                                        <img src="https://realvidas.com/area-administrativa/imagens/img-corp-ficha.png" style="width: 420px; height: 90px;;" alt="" srcset="">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td style="border-right: 2px solid black; border-left: 2px solid black; border-top: 2px solid black; padding: 5px;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tbody>
                                <tr style="width: 100%;">
                                    <td style="text-align: center; font-weight: bold;">
                                        Consentimento para transporte de Pacientes
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td style="border-right: 2px solid black; border-left: 2px solid black; border-top: 2px solid black; padding: 5px;">
                        <p >
                            Declaro que fui orientado pela equipe da ambulancia e médicos verbalmente sobre o quadro clínico do paciente.
                            Estou ciente e orientado do quadro clínico do paciente para transporte e dos riscos envolvidos no transporte, podendo o
                            paciente apresentar quadro clínico de instabilidade durante o percurso.
                            Estou ciente das intervenções necessárias da equipe médica para a segurança do transporte, e dos eventuais riscos que
                            envolvam a viagem que será realizada
                        </p>
                    </td>
                </tr>

                <tr>
                    <td style="border-right: 2px solid black; border-left: 2px solid black; border-top: 2px solid black; border-bottom: 2px solid black; padding: 0px;">
                        <table style="border-collapse: collapse; width: 100%;">
                            <tbody>
                                <tr>
                                    <td style="width: 80%;">
                                        <table style="width: 100%; border-collapse: collapse;">
                                            <tbody>
                                                <tr style="width: 100%;">
                                                    <td style="border-bottom: 2px solid black; border-right: 2px solid black;">
                                                       Nome 
                                                    </td>
                                                </tr>
                                                <tr style="width: 100%;">
                                                    <td style="border-right: 2px solid black;">
                                                        RG/CPF
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                    <td style=" width: 20%">
                                        Assinatura
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>
';

// HTML da segunda página
$htmlPage2 = '
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>PDF com mPDF - Página 2</title>
     <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: "Source Sans Pro", sans-serif;
            font-size: 10px;
            background-image: url("../imagens/logo-opaco.png");
            background-repeat: no-repeat;
            background-position: center;
        }
        .watermark {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url(\'https://realvidas.com/area-administrativa-developer/imagens/logo-opaco.png\');
            background-size: 90%;
            background-repeat: no-repeat;
            background-position: center;
            opacity: 0.1; /* Simula transparência */
            z-index: -1;
        }
        .container {
            width: 100%;
            width-max: 100vw;
            padding: 15px;
            margin-right: auto;
            margin-left: auto;
        }
        .row{
            width:95%;
        }

        .checkbox {
            display: inline-block;
            width: 10px;
            height: 10px;
            border: 2px solid #000;
            margin-right: 5px;
            text-align: center;
            font-size: 12px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="watermark container">
     <table class="row" style="border-collapse: collapse;">
            <thead>
                <tr>
                    <th scope="col" style="width: 100%;"></th>
                </tr>
            </thead>
            <tbody style="padding: 0px;">
                <tr>
                    <td style="border-right: 2px solid black; border-left: 2px solid black; border-top: 2px solid black; padding: 0px;">
                       <table style="border-collapse: collapse; width: 100%;">
                            <tbody>
                                <tr>
                                    <td style="text-align: center; width: 100% font-weight: bold; ">
                                        <h1>Observações Médicas / Enfermeiros</h1>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>

                <tr >
                    <td style="border-right: 2px solid black; border-left: 2px solid black; height: 550px; padding: 10px 50px; vertical-align: top">
                       <p style="font-size: 14px; text-align: left;">' . $row['txtObsMedicaEnfermeiros'] . '</p>
                    </td>
                </tr>

                <tr>
                    <td style="border-right: 2px solid black; border-left: 2px solid black; border-top: 2px solid black; padding: 0px;">
                       <table style="border-collapse: collapse; width: 100%;">
                            <tbody>
                                <tr>
                                    <td style="text-align: center; width: 100% font-weight: bold; ">
                                        <h1>Materiais Utilizados</h1>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td style="border-right: 2px solid black; border-left: 2px solid black;  height: 200px; padding: 0px 50px; vertical-align: top">
                        <p style="font-size: 14px; text-align: left;">' . $row['txtMateriaisUtilizados'] . '</p>
                    </td>
                </tr>


                <tr>
                    <td style="border-right: 2px solid black; border-left: 2px solid black; border-top: 2px solid black; padding: 0px;">
                       <table style="border-collapse: collapse; width: 100%;">
                            <tbody>
                                <tr>
                                    <td style="text-align: center; width: 100% font-weight: bold; width: 30% border-right: 2px solid black; border-bottom: 2px solid black;">
                                       <table style="border-collapse: collapse; width: 100%;">
                                            <tbody>
                                                <tr>
                                                    <td style="text-align: start; width: 100%;">
                                                        <span style="font-weight: bold; border-right: 2px solid black;"> Pedagios</span> ' . $row['txtPedagios'] . '
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                    <td style="text-align: center; width: 100% font-weight: bold; width: 30% border-right: 2px solid black; border-bottom: 2px solid black;">
                                       <table style="border-collapse: collapse; width: 100%;">
                                            <tbody>
                                                <tr>
                                                    <td style="text-align: start; width: 100%;  border-left: 2px solid black;">
                                                        <span style="font-weight: bold;"> Outros:</span> ' . $row['txtOutros'] . '
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                    <td style="text-align: center; width: 100% font-weight: bold; width: 25% border-right: 2px solid black; border-left: 2px solid black; border-bottom: 2px solid black;">
                                       <table style="border-collapse: collapse; width: 100%;">
                                            <tbody>
                                                <tr>
                                                    <td style="text-align: center; font-weight: bold; border-right: 2px solid black;">
                                                       Refeição / Lanche
                                                    </td>
                                                    <td style="text-align: center; width: auto; font-weight: bold; ">
                                                        Sim
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                    <td style="text-align: center; width: 100% font-weight: bold; width: 25% border-right: 2px solid black; bold; border-left: 2px solid black; border-bottom: 2px solid black;">
                                       <table style="border-collapse: collapse; width: 100%;">
                                            <tbody>
                                                <tr>
                                                    <td style="text-align: center; font-weight: bold; border-right: 2px solid black;">
                                                        ' . addX2(1, $row['selectRefeicaoLanche']) . '
                                                    </td>
                                                    <td style="text-align: center; width: auto; font-weight: bold; bold; border-right: 2px solid black; ">
                                                        Não
                                                    </td>
                                                    <td style="text-align: center; font-weight: ">
                                                        ' . addX2(2, $row['selectRefeicaoLanche']) . '
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <td style="text-align: center; width: 100% font-weight: bold; width: 30% border-right: 2px solid black; ">
                                       <table style="border-collapse: collapse; width: 100%;">
                                            <tbody>
                                                <tr>
                                                    <td style="text-align: center;width: 40%; font-weight: bold; border-right: 2px solid black; padding: 40px 0px">
                                                        Oxigênio
                                                    </td>
                                                    <td style="text-align: center; width: auto; font-weight: bold;width: 60%;">
                                                         <table style="border-collapse: collapse; width: 100%;">
                                                            <tbody>
                                                                <tr>
                                                                    <td style="text-align: start; font-weight: bold; border-bottom: 2px solid black; padding: 4px 0">
                                                                        01 - Grande 
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="text-align: start; font-weight: bold; border-bottom: 2px solid black; padding: 4px 0">
                                                                        02 - Grande
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="text-align: start; font-weight: bold; border-bottom: 2px solid black; padding: 4px 0">
                                                                        03 - Pequeno
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="text-align: start; font-weight: bold;  padding: 4px 0">
                                                                        04 - Pegueno
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>

                                    <td style="text-align: center; width: 100% font-weight: bold; width: 30% border-right: 2px solid black; ">
                                       <table style="border-collapse: collapse; width: 100%;">
                                            <tbody>
                                                <tr>
                                                    <td style="text-align: start; width: 100% font-weight: bold; border-left: 2px solid black;">
                                                         <table style="border-collapse: collapse; width: 100%;">
                                                            <tbody>
                                                                <tr>
                                                                    <td style="text-align: center; font-weight: bold; border-bottom: 2px solid black; padding: 3px">
                                                                        <span class="checkbox">' . addX2(1, $row['selectGrandeUm']) . '</span>
                                                                        Cheio 
                                                                    </td>
                                                                    <td style="text-align: center; font-weight: bold; border-bottom: 2px solid black; padding: 3px">
                                                                        <span class="checkbox">' . addX2(2, $row['selectGrandeUm']) . '</span>
                                                                        Vazio  
                                                                    </td>
                                                                    <td style="text-align: center; font-weight: bold; border-bottom: 2px solid black; padding: 3px">
                                                                        <span class="checkbox">' . addX2(3, $row['selectGrandeUm']) . '</span>
                                                                        Em uso 
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="text-align: center; font-weight: bold; border-bottom: 2px solid black; padding: 3px">
                                                                        <span class="checkbox">' . addX2(1, $row['selectGrandeDois']) . '</span>
                                                                        Cheio 
                                                                    </td>
                                                                    <td style="text-align: center; font-weight: bold; border-bottom: 2px solid black; padding: 3px">
                                                                        <span class="checkbox">' . addX2(2, $row['selectGrandeDois']) . '</span>
                                                                        Vazio  
                                                                    </td>
                                                                    <td style="text-align: center; font-weight: bold; border-bottom: 2px solid black; padding: 3px">
                                                                        <span class="checkbox">' . addX2(3, $row['selectGrandeDois']) . '</span>
                                                                        Em uso 
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="text-align: center; font-weight: bold; border-bottom: 2px solid black; padding: 3px">
                                                                        <span class="checkbox">' . addX2(1, $row['selectPequenoTres']) . '</span>
                                                                        Cheio 
                                                                    </td>
                                                                    <td style="text-align: center; font-weight: bold; border-bottom: 2px solid black; padding: 3px">
                                                                        <span class="checkbox">' . addX2(1, $row['selectPequenoTres']) . '</span>
                                                                        Vazio  
                                                                    </td>
                                                                    <td style="text-align: center; font-weight: bold; border-bottom: 2px solid black; padding: 3px">
                                                                        <span class="checkbox">' . addX2(1, $row['selectPequenoTres']) . '</span>
                                                                        Em uso 
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="text-align: center; font-weight: bold; padding: 3px">
                                                                        <span class="checkbox">' . addX2(1, $row['selectPequenoQuatro']) . '</span>
                                                                        Cheio 
                                                                    </td>
                                                                    <td style="text-align: center; font-weight: bold; padding: 3px">
                                                                        <span class="checkbox">' . addX2(2, $row['selectPequenoQuatro']) . '</span>
                                                                        Vazio  
                                                                    </td>
                                                                    <td style="text-align: center; font-weight: bold; padding: 3px">
                                                                        <span class="checkbox">' . addX2(3, $row['selectPequenoQuatro']) . '</span>
                                                                        Em uso 
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>

                                    <td style="text-align: center; width: 100% font-weight: bold; width: 25% border-right: 2px solid black; border-left: 2px solid black;">
                                       <table style="border-collapse: collapse; width: 100%;">
                                            <tbody>
                                                <tr>
                                                    <td style="text-align: center; font-weight: bold; border-right: 2px solid black; border-bottom: 2px solid black; width:auto ; padding: 11px">
                                                       ' . $row['txtgrandeUmBarValeuOutros'] . '
                                                    </td>
                                                    <td style="text-align: center; width: auto; font-weight: bold;  border-bottom: 2px solid black; ">
                                                       Quantos Bar Utilizou
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align: center; font-weight: bold; border-right: 2px solid black; width:auto;  border-bottom: 2px solid black;  padding: 11px">
                                                       ' . $row['grandeDoisBarValeu'] . '
                                                    </td>
                                                    <td style="text-align: center; width: auto; font-weight: bold;  border-bottom: 2px solid black; ">
                                                       Quantos Bar Utilizou
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align: center; font-weight: bold; border-right: 2px solid black; width:auto ;  border-bottom: 2px solid black;  padding:11px">
                                                       ' . $row['pequenoTresBarValeu'] . '
                                                    </td>
                                                    <td style="text-align: center; width: auto; font-weight: bold;  border-bottom: 2px solid black; ">
                                                       Quantos Bar Utilizou
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align: center; font-weight: bold; border-right: 2px solid black; width:auto ;  padding: 11px">
                                                      ' . $row['pequenoQuatroBarValeu'] . '
                                                    </td>
                                                    <td style="text-align: center; width: auto; font-weight: bold; ">
                                                       Quantos Bar Utilizou
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>

                                    <td style="text-align: center; width: 100% font-weight: bold; width: 10% border-right: 2px solid black; bold; border-left: 2px solid black;">
                                       <table style="border-collapse: collapse; width: 100%;">
                                            <tbody>
                                                <tr>
                                                    <td style="text-align: center; font-weight: bold; border-bottom: 2px solid black; width:100%; padding: 11px">
                                                      ' . $row['grandeUmBarQuantidade'] . '
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align: center; font-weight: bold; border-bottom: 2px solid black; width:100%; padding:11px">
                                                       ' . $row['grandeDoisBarQuantidade'] . '
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align: center; font-weight: bold; border-bottom: 2px solid black; width:100%; padding:11px">
                                                       ' . $row['pequenoTresBarQuantidade'] . '
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align: center; font-weight: bold; width:100%; padding: 11px">
                                                       ' . $row['pequenoQuatroBarQuantidade'] . '
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>                                
                            </tbody>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td style="border-right: 2px solid black; border-left: 2px solid black; border-top: 2px solid black;  border-bottom: 2px solid black; padding: 15px;25px;">
                       <table style="width: 100%; border-collapse: collapse;">
                            <tbody>
                                <tr style="width: 100%;">
                                    <td style="width:33%;border: 2px solid black;">
                                        <table style="width: 100%; border-collapse: collapse;">
                                            <tbody>
                                                <tr style="width: 100%;">
                                                    <td style="border-bottom: 2px solid black;">
                                                       Médico Responsável<br><br><br><br>
                                                       ' . $row['medicoAssinatura'] . '
                                                       ' . $row['medico'] . '
                                                       <br>
                                                    </td>
                                                </tr>
                                                <tr style="width: 100%;">
                                                    <td style="">
                                                        Data _____/____/____ 
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                    <td style="width:33%;border: 2px solid black;">
                                        <table style="width: 100%; border-collapse: collapse;">
                                            <tbody>
                                                <tr style="width: 100%;">
                                                    <td style="border-bottom: 2px solid black;">
                                                       Enfermagem Responsável<br><br><br><br>
                                                       ' . $row['enfermeiroAssinatura'] . '
                                                       ' . $row['enfermeiro'] . '
                                                       <br>
                                                    </td>
                                                </tr>
                                                <tr style="width: 100%;">
                                                    <td style="">
                                                        Data _____/____/____ 
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                    <td style="width:33%;border: 2px solid black;">
                                        <table style="width: 100%; border-collapse: collapse;">
                                            <tbody>
                                                <tr style="width: 100%;">
                                                    <td style="border-bottom: 2px solid black;">
                                                       Condutor Socorrista<br><br><br><br>
                                                       ' . $row['condutorAssinatura'] . '
                                                       ' . $row['condutor'] . '
                                                       <br>
                                                    </td>
                                                </tr>
                                                <tr style="width: 100%;">
                                                    <td style="">
                                                        Data _____/____/____ 
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>
';

// Adicionando conteúdo ao PDF
$mpdf->WriteHTML($htmlPage1); // Gera a primeira página
$mpdf->AddPage(); // Adiciona uma nova página
$mpdf->WriteHTML($htmlPage2); // Gera a segunda página
$mpdf->Output("Ficha Atendimento nr$row[nrOrdemServico].pdf", "I"); // Faz o download do PDF com o nome especificado
