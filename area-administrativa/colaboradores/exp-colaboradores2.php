<?php
$pageActive = "Colaboradores";

include('../verifyConnection.php');
$acessoPermissoes = validAcesso(json_decode(base64_decode($_SESSION["acessoPermissoes"]), true), $pageActive);

if ($acessoPermissoes["isAcesso"] == 0) {
    $type = base64_encode("error");
    $msg  = base64_encode("Usuário sem permissão para acessar este conteúdo!");
    header('Location: ./?type=' . $type . '&msg=' . $msg);
    exit;
}

include('../connectDb.php');

// Configuração de headers para exportação Excel
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"Relatorio_Colaboradores_" . date('Ymd') . ".xls\"");
header("Content-Type: text/html; charset=UTF-8", true);

// Chamada à API
$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://realvidas.com/area-administrativa/api/rv_colaboradores/',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
));

$response = curl_exec($curl);
curl_close($curl);

$response = json_decode($response, true);

$response = $response['result'];

// Geração da tabela
$tabela = '';
if (is_array($response)) {
    foreach ($response as $row) {
        // Formatar data de nascimento (de Y-m-d para d/m/Y)
        $dataNascimento = '';
        if (!empty($row['dataNascimento']) && $row['dataNascimento'] !== '0000-00-00') {
            $dataNascimento = date('d/m/Y', strtotime($row['dataNascimento']));
        }

        // Aplicar máscara conforme tipoDoc
        $tipoDoc = strtoupper(trim($row['tipoDoc']));
        $nrDoc = trim($row['nrDoc']);

        switch ($tipoDoc) {
            case 'CRM':
                $nrDocFormatado = 'CRM/' . $nrDoc;
                break;
            case 'CNH':
                $nrDocFormatado = str_pad($nrDoc, 8, '0', STR_PAD_LEFT);
                break;
            case 'COREN':
                // Exemplo: Coren/SP 12345
                $uf = isset($row['uf']) ? strtoupper($row['uf']) : 'UF';
                $nrDocFormatado = 'Coren/' . $uf . ' ' . $nrDoc;
                break;
            default:
                $nrDocFormatado = $nrDoc;
                break;
        }

        $tabela .= '
            <tr>
                <td style="text-align: center">' . htmlspecialchars($row['nome']) . '</td>
                <td style="text-align: center">' . htmlspecialchars($row['rg']) . '</td>
                <td style="text-align: center">' . htmlspecialchars($row['cpf']) . '</td>
                <td style="text-align: center">' . $dataNascimento . '</td>
                <td style="text-align: center">' . htmlspecialchars($nrDocFormatado) . '</td>
                <td style="text-align: center">' . htmlspecialchars($row['especialidade']) . '</td>
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
        <tr>
            <td colspan="10" style="color:#990000;font-size:small;text-align:center;height:80px;vertical-align:middle;font-weight:bold">
                <img src="https://realvidas.com/area-administrativa/imagens/logo-real-vidas.png" width="80">
                RELATÓRIO DE COLABORADORES
            </td>
        </tr>
        <td colspan="10" style="color:#0000FF;font-size:small;text-align:center;height:30px;vertical-align:middle;">
            Data do Relatório: <?php echo date('d/m/Y'); ?>
        </td>
        <tr>
            <td width="50" style="font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;">NOME</td>
            <td width="400" style="font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;">RG</td>
            <td width="100" style="font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;">CPF</td>
            <td width="100" style="font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;">DATA NASCIMENTO</td>
            <td width="65" style="font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;">NR</td>
            <td width="120" style="font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;">ESPECIALIDADE</td>
        </tr>
        <?php echo $tabela; ?>
    </table>
</body>

</html>