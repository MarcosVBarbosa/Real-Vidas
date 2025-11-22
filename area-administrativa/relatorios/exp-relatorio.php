<?php

include('../verifyConnection.php');
$pageActive = "Relatórios";
$acessoPermissoes = validAcesso(json_decode(base64_decode($_SESSION["acessoPermissoes"]), true), $pageActive);

if ($acessoPermissoes["isAcesso"] == 0) {
    $type = base64_encode("error");
    $msg  = base64_encode("Usuário sem permissão para acessar este conteúdo!");
    header('Location: https://' . $_SERVER['HTTP_HOST'] . '/area-administrativa-developer/index.php?msg=' . $msg . '&type=' . $type);
    exit;
}
include('../connectDb.php');
$tabela = "";
$query = "";
$conteudo = "";
$cont = 0;

if (empty($_POST["selCliente"]) and empty($_POST["selPeriodo"]) and empty($_POST["tipoRelatorio"])) {
    $type = base64_encode('danger');
    $msg = base64_encode('Falha! Não foi possível gerar o relatório.');
    header('Location: ./?msg=' . $msg . '&type=' . $type);
    exit;
}

if ($_POST["selCliente"] == "Todos") {
    $whereCliente = "";
    $tituloCliente = "TODOS";
} else {
    $cliente = base64_decode($_POST["selCliente"]);
    $cliente = explode('-', $cliente);
    $tituloCliente = $cliente[1];
    $whereCliente = " AND fkCliente = " . $cliente[0];
}

if ($_POST["selPeriodo"] == "Anual") {
} else {
    $data = explode(" - ", $_POST["selPeriodo"]);
    $data = $data[1];
}

// INICIO RELATORIO DESPESAS OPERACIONAIS
if ($_POST["tipoRelatorio"] == "Despesas_Operacionais") {

    // RELATÓRIO ANUAL
    if ($_POST["selPeriodo"] == "Anual") {

        for ($i = 2020; $i <= (date("Y") + 1); $i++) {
            $cont++;
            if ($_POST["selTipo"] == "Valores") {
                $colaboradores .= "
                    (SELECT COALESCE(SUM((c.qtdeHora * c.valorHora) + c.ajudaCusto),0)
                    FROM rv_colaboradoresOS c
                    INNER JOIN rv_ordemServico o ON (c.fkOrdemServico = o.pkId)
                    WHERE o.ativo = 'S'
                    AND DATE_FORMAT(o.dataAgendada, '%Y') = '$i'
                    $whereCliente
                    ) '$cont',";
                $impostos .= "
                    (SELECT COALESCE(SUM(((o.valorRemocao + o.totalHoraParada) * (f.taxaNF / 100))),0)
                    FROM rv_ordemServico o
                    INNER JOIN rv_faturamentoOS fos ON (o.pkId = fos.fkOrdemServico)
                    INNER JOIN rv_faturamento f ON (f.pkId = fos.fkFaturamento)
                    WHERE o.ativo = 'S'
                    AND DATE_FORMAT(o.dataAgendada, '%Y') = '$i'
                    $whereCliente
                    ) '$cont',";
                $diesel .= "
                    (SELECT COALESCE(SUM(o.gastoDiesel),0)
                    FROM rv_ordemServico o
                    WHERE o.ativo = 'S'
                    AND DATE_FORMAT(o.dataAgendada, '%Y') = '$i'
                    $whereCliente
                    ) '$cont',";
                $despesas .= "
                    (
                    SELECT COALESCE(SUM(dos.qtde * dos.valor),0)
                    FROM rv_despesasOS dos
                    INNER JOIN rv_ordemServico o ON (dos.fkOrdemServico = o.pkId)
                    WHERE dos.fkDespesa = d.pkId
                    AND DATE_FORMAT(o.dataAgendada, '%Y') = '$i'
                    $whereCliente
                    ) '$cont',";
            } elseif ($_POST["selTipo"] == "Quantidade") {
                $colaboradores .= "
                    (SELECT COUNT(c.pkId)
                    FROM rv_colaboradoresOS c
                    INNER JOIN rv_ordemServico o ON (c.fkOrdemServico = o.pkId)
                    WHERE o.ativo = 'S'
                    AND DATE_FORMAT(o.dataAgendada, '%Y') = '$i'
                    $whereCliente
                    ) '$cont',";
                $impostos .= "
                    (SELECT COUNT(o.pkId)
                    FROM rv_ordemServico o
                    INNER JOIN rv_faturamentoOS fos ON (o.pkId = fos.fkOrdemServico)
                    INNER JOIN rv_faturamento f ON (f.pkId = fos.fkFaturamento)
                    WHERE o.ativo = 'S'
                    AND DATE_FORMAT(o.dataAgendada, '%Y') = '$i'
                    $whereCliente
                    ) '$cont',";
                $diesel .= "
                    (SELECT COUNT(o.pkId)
                    FROM rv_ordemServico o
                    WHERE o.ativo = 'S'
                    AND DATE_FORMAT(o.dataAgendada, '%Y') = '$i'
                    $whereCliente
                    ) '$cont',";
                $despesas .= "
                    (
                    SELECT COUNT(dos.pkId)
                    FROM rv_despesasOS dos
                    INNER JOIN rv_ordemServico o ON (dos.fkOrdemServico = o.pkId)
                    WHERE dos.fkDespesa = d.pkId
                    AND DATE_FORMAT(o.dataAgendada, '%Y') = '$i'
                    $whereCliente
                    ) '$cont',";
            }
        }

        $query = "
        SELECT 'COLABORADORES' , 
        " . substr($colaboradores, 0, -1) . "
        
        UNION
        
        SELECT 'IMPOSTOS' , 
        " . substr($impostos, 0, -1) . "
        
        UNION
        
        SELECT 'DIESEL' , 
        " . substr($diesel, 0, -1) . "
        
        UNION
        
        (SELECT UPPER(d.nome) 'COLABORADORES' ,
        " . substr($despesas, 0, -1) . "
        FROM rv_despesas d
        WHERE d.ativo = 'S'
        ORDER BY d.nome)
        ";

        $rs = mysqli_query($connecta, $query);
        if (mysqli_num_rows($rs) > 0) {
            while ($row = mysqli_fetch_array($rs)) {
                $conteudo .= '
                    <tr>
                      <td> ' . $row["COLABORADORES"] . ' </td>
                ';
                for ($i = 1; $i <= $cont; $i++) {
                    $conteudo .= '<td> ' . number_format($row[$i], 2, ',', '.') . ' </td>';
                }
                $conteudo .= '</tr>';
            }
        }

        $tabela = '
        <tr style="height:55px">
            <td colspan="1" style="text-align:center; vertical-align:middle"> <img src="https://realvidas.com.br/wp-content/uploads/2020/07/logo-realvidas-head.png" width="200"> </td>
            <td colspan="' . $cont . '" style="color:#990000;font-size:small;text-align:center;height:50px;vertical-align:middle;font-weight:bold" > RELATÓRIO DE DESPESA OPERACIONAL </td>
        </tr>
        <tr>
            <td colspan="' . ($cont + 1) . '" style="color:#0000FF;font-size:small;text-align:center;height:30px;vertical-align:middle;" > Cliente: ' . $tituloCliente . ' <br> ' . $_POST["selPeriodo"] . ' </td>
        </tr>
        <tr>
            <td width="230" style="font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;vertical-align:middle">DESCRIÇÃO</td>
        ';
        for ($i = 0; $i < $cont; $i++) {
            $tabela .= '<td width="90" style="font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;vertical-align:middle"> ' . ($i + 2020) . ' </td>';
        }
        $tabela .= '</tr>
        ' . $conteudo . '
        ';
    }
    // RELATÓRIO MENSAL
    else {
        if ($_POST["selTipo"] == "Valores") {
            $query = "
            SELECT 'COLABORADORES' , 
            (SELECT COALESCE(SUM((c.qtdeHora * c.valorHora) + c.ajudaCusto),0)
            FROM rv_colaboradoresOS c
            INNER JOIN rv_ordemServico o ON (c.fkOrdemServico = o.pkId)
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '01'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'jan',
            (SELECT COALESCE(SUM((c.qtdeHora * c.valorHora) + c.ajudaCusto),0)
            FROM rv_colaboradoresOS c
            INNER JOIN rv_ordemServico o ON (c.fkOrdemServico = o.pkId)
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '02'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'fev',
            (SELECT COALESCE(SUM((c.qtdeHora * c.valorHora) + c.ajudaCusto),0)
            FROM rv_colaboradoresOS c
            INNER JOIN rv_ordemServico o ON (c.fkOrdemServico = o.pkId)
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '03'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'mar',
            (SELECT COALESCE(SUM((c.qtdeHora * c.valorHora) + c.ajudaCusto),0)
            FROM rv_colaboradoresOS c
            INNER JOIN rv_ordemServico o ON (c.fkOrdemServico = o.pkId)
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '04'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'abr',
            (SELECT COALESCE(SUM((c.qtdeHora * c.valorHora) + c.ajudaCusto),0)
            FROM rv_colaboradoresOS c
            INNER JOIN rv_ordemServico o ON (c.fkOrdemServico = o.pkId)
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '05'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'mai',
            (SELECT COALESCE(SUM((c.qtdeHora * c.valorHora) + c.ajudaCusto),0)
            FROM rv_colaboradoresOS c
            INNER JOIN rv_ordemServico o ON (c.fkOrdemServico = o.pkId)
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '06'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'jun',
            (SELECT COALESCE(SUM((c.qtdeHora * c.valorHora) + c.ajudaCusto),0)
            FROM rv_colaboradoresOS c
            INNER JOIN rv_ordemServico o ON (c.fkOrdemServico = o.pkId)
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '07'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'jul',
            (SELECT COALESCE(SUM((c.qtdeHora * c.valorHora) + c.ajudaCusto),0)
            FROM rv_colaboradoresOS c
            INNER JOIN rv_ordemServico o ON (c.fkOrdemServico = o.pkId)
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '08'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'ago',
            (SELECT COALESCE(SUM((c.qtdeHora * c.valorHora) + c.ajudaCusto),0)
            FROM rv_colaboradoresOS c
            INNER JOIN rv_ordemServico o ON (c.fkOrdemServico = o.pkId)
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '09'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'set',
            (SELECT COALESCE(SUM((c.qtdeHora * c.valorHora) + c.ajudaCusto),0)
            FROM rv_colaboradoresOS c
            INNER JOIN rv_ordemServico o ON (c.fkOrdemServico = o.pkId)
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '10'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'out',
            (SELECT COALESCE(SUM((c.qtdeHora * c.valorHora) + c.ajudaCusto),0)
            FROM rv_colaboradoresOS c
            INNER JOIN rv_ordemServico o ON (c.fkOrdemServico = o.pkId)
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '11'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'nov',
            (SELECT COALESCE(SUM((c.qtdeHora * c.valorHora) + c.ajudaCusto),0)
            FROM rv_colaboradoresOS c
            INNER JOIN rv_ordemServico o ON (c.fkOrdemServico = o.pkId)
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '12'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'dez'
            
            UNION
            
            SELECT 'IMPOSTOS' , 
            (SELECT COALESCE(SUM(((o.valorRemocao + o.totalHoraParada) * (f.taxaNF / 100))),0)
            FROM rv_ordemServico o
            INNER JOIN rv_faturamentoOS fos ON (o.pkId = fos.fkOrdemServico)
            INNER JOIN rv_faturamento f ON (f.pkId = fos.fkFaturamento)
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '01'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'jan',
            (SELECT COALESCE(SUM(((o.valorRemocao + o.totalHoraParada) * (f.taxaNF / 100))),0)
            FROM rv_ordemServico o
            INNER JOIN rv_faturamentoOS fos ON (o.pkId = fos.fkOrdemServico)
            INNER JOIN rv_faturamento f ON (f.pkId = fos.fkFaturamento)
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '02'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'fev',
            (SELECT COALESCE(SUM(((o.valorRemocao + o.totalHoraParada) * (f.taxaNF / 100))),0)
            FROM rv_ordemServico o
            INNER JOIN rv_faturamentoOS fos ON (o.pkId = fos.fkOrdemServico)
            INNER JOIN rv_faturamento f ON (f.pkId = fos.fkFaturamento)
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '03'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'mar',
            (SELECT COALESCE(SUM(((o.valorRemocao + o.totalHoraParada) * (f.taxaNF / 100))),0)
            FROM rv_ordemServico o
            INNER JOIN rv_faturamentoOS fos ON (o.pkId = fos.fkOrdemServico)
            INNER JOIN rv_faturamento f ON (f.pkId = fos.fkFaturamento)
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '04'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'abr',
            (SELECT COALESCE(SUM(((o.valorRemocao + o.totalHoraParada) * (f.taxaNF / 100))),0)
            FROM rv_ordemServico o
            INNER JOIN rv_faturamentoOS fos ON (o.pkId = fos.fkOrdemServico)
            INNER JOIN rv_faturamento f ON (f.pkId = fos.fkFaturamento)
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '05'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'mai',
            (SELECT COALESCE(SUM(((o.valorRemocao + o.totalHoraParada) * (f.taxaNF / 100))),0)
            FROM rv_ordemServico o
            INNER JOIN rv_faturamentoOS fos ON (o.pkId = fos.fkOrdemServico)
            INNER JOIN rv_faturamento f ON (f.pkId = fos.fkFaturamento)
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '06'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'jun',
            (SELECT COALESCE(SUM(((o.valorRemocao + o.totalHoraParada) * (f.taxaNF / 100))),0)
            FROM rv_ordemServico o
            INNER JOIN rv_faturamentoOS fos ON (o.pkId = fos.fkOrdemServico)
            INNER JOIN rv_faturamento f ON (f.pkId = fos.fkFaturamento)
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '07'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'jul',
            (SELECT COALESCE(SUM(((o.valorRemocao + o.totalHoraParada) * (f.taxaNF / 100))),0)
            FROM rv_ordemServico o
            INNER JOIN rv_faturamentoOS fos ON (o.pkId = fos.fkOrdemServico)
            INNER JOIN rv_faturamento f ON (f.pkId = fos.fkFaturamento)
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '08'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'ago',
            (SELECT COALESCE(SUM(((o.valorRemocao + o.totalHoraParada) * (f.taxaNF / 100))),0)
            FROM rv_ordemServico o
            INNER JOIN rv_faturamentoOS fos ON (o.pkId = fos.fkOrdemServico)
            INNER JOIN rv_faturamento f ON (f.pkId = fos.fkFaturamento)
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '09'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'set',
            (SELECT COALESCE(SUM(((o.valorRemocao + o.totalHoraParada) * (f.taxaNF / 100))),0)
            FROM rv_ordemServico o
            INNER JOIN rv_faturamentoOS fos ON (o.pkId = fos.fkOrdemServico)
            INNER JOIN rv_faturamento f ON (f.pkId = fos.fkFaturamento)
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '10'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'out',
            (SELECT COALESCE(SUM(((o.valorRemocao + o.totalHoraParada) * (f.taxaNF / 100))),0)
            FROM rv_ordemServico o
            INNER JOIN rv_faturamentoOS fos ON (o.pkId = fos.fkOrdemServico)
            INNER JOIN rv_faturamento f ON (f.pkId = fos.fkFaturamento)
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '11'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'nov',
            (SELECT COALESCE(SUM(((o.valorRemocao + o.totalHoraParada) * (f.taxaNF / 100))),0)
            FROM rv_ordemServico o
            INNER JOIN rv_faturamentoOS fos ON (o.pkId = fos.fkOrdemServico)
            INNER JOIN rv_faturamento f ON (f.pkId = fos.fkFaturamento)
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '12'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'dez'
            
            UNION
            
            SELECT 'DIESEL' , 
            (SELECT COALESCE(SUM(o.gastoDiesel),0)
            FROM rv_ordemServico o
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '01'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'jan',
            (SELECT COALESCE(SUM(o.gastoDiesel),0)
            FROM rv_ordemServico o
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '02'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'fev',
            (SELECT COALESCE(SUM(o.gastoDiesel),0)
            FROM rv_ordemServico o
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '03'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'mar',
            (SELECT COALESCE(SUM(o.gastoDiesel),0)
            FROM rv_ordemServico o
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '04'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'abr',
            (SELECT COALESCE(SUM(o.gastoDiesel),0)
            FROM rv_ordemServico o
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '05'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'mai',
            (SELECT COALESCE(SUM(o.gastoDiesel),0)
            FROM rv_ordemServico o
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '06'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'jun',
            (SELECT COALESCE(SUM(o.gastoDiesel),0)
            FROM rv_ordemServico o
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '07'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'jul',
            (SELECT COALESCE(SUM(o.gastoDiesel),0)
            FROM rv_ordemServico o
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '08'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'ago',
            (SELECT COALESCE(SUM(o.gastoDiesel),0)
            FROM rv_ordemServico o
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '09'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'set',
            (SELECT COALESCE(SUM(o.gastoDiesel),0)
            FROM rv_ordemServico o
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '10'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'out',
            (SELECT COALESCE(SUM(o.gastoDiesel),0)
            FROM rv_ordemServico o
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '11'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'nov',
            (SELECT COALESCE(SUM(o.gastoDiesel),0)
            FROM rv_ordemServico o
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '12'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'dez'
            
            UNION
            
            (SELECT UPPER(d.nome) 'COLABORADORES' ,
            (
            SELECT COALESCE(SUM(dos.qtde * dos.valor),0)
            FROM rv_despesasOS dos
            INNER JOIN rv_ordemServico o ON (dos.fkOrdemServico = o.pkId)
            WHERE dos.fkDespesa = d.pkId
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '01'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'jan',
            (
            SELECT COALESCE(SUM(dos.qtde * dos.valor),0)
            FROM rv_despesasOS dos
            INNER JOIN rv_ordemServico o ON (dos.fkOrdemServico = o.pkId)
            WHERE dos.fkDespesa = d.pkId
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '02'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'fev',
            (
            SELECT COALESCE(SUM(dos.qtde * dos.valor),0)
            FROM rv_despesasOS dos
            INNER JOIN rv_ordemServico o ON (dos.fkOrdemServico = o.pkId)
            WHERE dos.fkDespesa = d.pkId
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '03'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'mar',
            (
            SELECT COALESCE(SUM(dos.qtde * dos.valor),0)
            FROM rv_despesasOS dos
            INNER JOIN rv_ordemServico o ON (dos.fkOrdemServico = o.pkId)
            WHERE dos.fkDespesa = d.pkId
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '04'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'abr',
            (
            SELECT COALESCE(SUM(dos.qtde * dos.valor),0)
            FROM rv_despesasOS dos
            INNER JOIN rv_ordemServico o ON (dos.fkOrdemServico = o.pkId)
            WHERE dos.fkDespesa = d.pkId
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '05'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'mai',
            (
            SELECT COALESCE(SUM(dos.qtde * dos.valor),0)
            FROM rv_despesasOS dos
            INNER JOIN rv_ordemServico o ON (dos.fkOrdemServico = o.pkId)
            WHERE dos.fkDespesa = d.pkId
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '06'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'jun',
            (
            SELECT COALESCE(SUM(dos.qtde * dos.valor),0)
            FROM rv_despesasOS dos
            INNER JOIN rv_ordemServico o ON (dos.fkOrdemServico = o.pkId)
            WHERE dos.fkDespesa = d.pkId
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '07'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'jul',
            (
            SELECT COALESCE(SUM(dos.qtde * dos.valor),0)
            FROM rv_despesasOS dos
            INNER JOIN rv_ordemServico o ON (dos.fkOrdemServico = o.pkId)
            WHERE dos.fkDespesa = d.pkId
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '08'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'ago',
            (
            SELECT COALESCE(SUM(dos.qtde * dos.valor),0)
            FROM rv_despesasOS dos
            INNER JOIN rv_ordemServico o ON (dos.fkOrdemServico = o.pkId)
            WHERE dos.fkDespesa = d.pkId
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '09'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'set',
            (
            SELECT COALESCE(SUM(dos.qtde * dos.valor),0)
            FROM rv_despesasOS dos
            INNER JOIN rv_ordemServico o ON (dos.fkOrdemServico = o.pkId)
            WHERE dos.fkDespesa = d.pkId
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '10'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'out',
            (
            SELECT COALESCE(SUM(dos.qtde * dos.valor),0)
            FROM rv_despesasOS dos
            INNER JOIN rv_ordemServico o ON (dos.fkOrdemServico = o.pkId)
            WHERE dos.fkDespesa = d.pkId
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '11'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'nov',
            (
            SELECT COALESCE(SUM(dos.qtde * dos.valor),0)
            FROM rv_despesasOS dos
            INNER JOIN rv_ordemServico o ON (dos.fkOrdemServico = o.pkId)
            WHERE dos.fkDespesa = d.pkId
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '12'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'dez'
            FROM rv_despesas d
            WHERE d.ativo = 'S'
            ORDER BY d.nome)
            ";
        } elseif ($_POST["selTipo"] == "Quantidade") {
            $query = "
            SELECT 'COLABORADORES' , 
            (SELECT COUNT(c.pkId)
            FROM rv_colaboradoresOS c
            INNER JOIN rv_ordemServico o ON (c.fkOrdemServico = o.pkId)
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '01'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'jan',
            (SELECT COUNT(c.pkId)
            FROM rv_colaboradoresOS c
            INNER JOIN rv_ordemServico o ON (c.fkOrdemServico = o.pkId)
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '02'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'fev',
            (SELECT COUNT(c.pkId)
            FROM rv_colaboradoresOS c
            INNER JOIN rv_ordemServico o ON (c.fkOrdemServico = o.pkId)
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '03'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'mar',
            (SELECT COUNT(c.pkId)
            FROM rv_colaboradoresOS c
            INNER JOIN rv_ordemServico o ON (c.fkOrdemServico = o.pkId)
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '04'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'abr',
            (SELECT COUNT(c.pkId)
            FROM rv_colaboradoresOS c
            INNER JOIN rv_ordemServico o ON (c.fkOrdemServico = o.pkId)
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '05'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'mai',
            (SELECT COUNT(c.pkId)
            FROM rv_colaboradoresOS c
            INNER JOIN rv_ordemServico o ON (c.fkOrdemServico = o.pkId)
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '06'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'jun',
            (SELECT COUNT(c.pkId)
            FROM rv_colaboradoresOS c
            INNER JOIN rv_ordemServico o ON (c.fkOrdemServico = o.pkId)
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '07'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'jul',
            (SELECT COUNT(c.pkId)
            FROM rv_colaboradoresOS c
            INNER JOIN rv_ordemServico o ON (c.fkOrdemServico = o.pkId)
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '08'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'ago',
            (SELECT COUNT(c.pkId)
            FROM rv_colaboradoresOS c
            INNER JOIN rv_ordemServico o ON (c.fkOrdemServico = o.pkId)
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '09'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'set',
            (SELECT COUNT(c.pkId)
            FROM rv_colaboradoresOS c
            INNER JOIN rv_ordemServico o ON (c.fkOrdemServico = o.pkId)
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '10'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'out',
            (SELECT COUNT(c.pkId)
            FROM rv_colaboradoresOS c
            INNER JOIN rv_ordemServico o ON (c.fkOrdemServico = o.pkId)
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '11'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'nov',
            (SELECT COUNT(c.pkId)
            FROM rv_colaboradoresOS c
            INNER JOIN rv_ordemServico o ON (c.fkOrdemServico = o.pkId)
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '12'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'dez'
            
            UNION
            
            SELECT 'IMPOSTOS' , 
            (SELECT COUNT(o.pkId)
            FROM rv_ordemServico o
            INNER JOIN rv_faturamentoOS fos ON (o.pkId = fos.fkOrdemServico)
            INNER JOIN rv_faturamento f ON (f.pkId = fos.fkFaturamento)
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '01'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'jan',
            (SELECT COUNT(o.pkId)
            FROM rv_ordemServico o
            INNER JOIN rv_faturamentoOS fos ON (o.pkId = fos.fkOrdemServico)
            INNER JOIN rv_faturamento f ON (f.pkId = fos.fkFaturamento)
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '02'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'fev',
            (SELECT COUNT(o.pkId)
            FROM rv_ordemServico o
            INNER JOIN rv_faturamentoOS fos ON (o.pkId = fos.fkOrdemServico)
            INNER JOIN rv_faturamento f ON (f.pkId = fos.fkFaturamento)
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '03'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'mar',
            (SELECT COUNT(o.pkId)
            FROM rv_ordemServico o
            INNER JOIN rv_faturamentoOS fos ON (o.pkId = fos.fkOrdemServico)
            INNER JOIN rv_faturamento f ON (f.pkId = fos.fkFaturamento)
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '04'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'abr',
            (SELECT COUNT(o.pkId)
            FROM rv_ordemServico o
            INNER JOIN rv_faturamentoOS fos ON (o.pkId = fos.fkOrdemServico)
            INNER JOIN rv_faturamento f ON (f.pkId = fos.fkFaturamento)
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '05'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'mai',
            (SELECT COUNT(o.pkId)
            FROM rv_ordemServico o
            INNER JOIN rv_faturamentoOS fos ON (o.pkId = fos.fkOrdemServico)
            INNER JOIN rv_faturamento f ON (f.pkId = fos.fkFaturamento)
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '06'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'jun',
            (SELECT COUNT(o.pkId)
            FROM rv_ordemServico o
            INNER JOIN rv_faturamentoOS fos ON (o.pkId = fos.fkOrdemServico)
            INNER JOIN rv_faturamento f ON (f.pkId = fos.fkFaturamento)
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '07'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'jul',
            (SELECT COUNT(o.pkId)
            FROM rv_ordemServico o
            INNER JOIN rv_faturamentoOS fos ON (o.pkId = fos.fkOrdemServico)
            INNER JOIN rv_faturamento f ON (f.pkId = fos.fkFaturamento)
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '08'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'ago',
            (SELECT COUNT(o.pkId)
            FROM rv_ordemServico o
            INNER JOIN rv_faturamentoOS fos ON (o.pkId = fos.fkOrdemServico)
            INNER JOIN rv_faturamento f ON (f.pkId = fos.fkFaturamento)
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '09'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'set',
            (SELECT COUNT(o.pkId)
            FROM rv_ordemServico o
            INNER JOIN rv_faturamentoOS fos ON (o.pkId = fos.fkOrdemServico)
            INNER JOIN rv_faturamento f ON (f.pkId = fos.fkFaturamento)
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '10'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'out',
            (SELECT COUNT(o.pkId)
            FROM rv_ordemServico o
            INNER JOIN rv_faturamentoOS fos ON (o.pkId = fos.fkOrdemServico)
            INNER JOIN rv_faturamento f ON (f.pkId = fos.fkFaturamento)
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '11'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'nov',
            (SELECT COUNT(o.pkId)
            FROM rv_ordemServico o
            INNER JOIN rv_faturamentoOS fos ON (o.pkId = fos.fkOrdemServico)
            INNER JOIN rv_faturamento f ON (f.pkId = fos.fkFaturamento)
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '12'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'dez'
            
            UNION
            
            SELECT 'DIESEL' , 
            (SELECT COUNT(o.pkId)
            FROM rv_ordemServico o
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '01'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'jan',
            (SELECT COUNT(o.pkId)
            FROM rv_ordemServico o
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '02'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'fev',
            (SELECT COUNT(o.pkId)
            FROM rv_ordemServico o
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '03'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'mar',
            (SELECT COUNT(o.pkId)
            FROM rv_ordemServico o
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '04'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'abr',
            (SELECT COUNT(o.pkId)
            FROM rv_ordemServico o
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '05'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'mai',
            (SELECT COUNT(o.pkId)
            FROM rv_ordemServico o
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '06'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'jun',
            (SELECT COUNT(o.pkId)
            FROM rv_ordemServico o
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '07'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'jul',
            (SELECT COUNT(o.pkId)
            FROM rv_ordemServico o
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '08'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'ago',
            (SELECT COUNT(o.pkId)
            FROM rv_ordemServico o
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '09'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'set',
            (SELECT COUNT(o.pkId)
            FROM rv_ordemServico o
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '10'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'out',
            (SELECT COUNT(o.pkId)
            FROM rv_ordemServico o
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '11'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'nov',
            (SELECT COUNT(o.pkId)
            FROM rv_ordemServico o
            WHERE o.ativo = 'S'
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '12'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'dez'
            
            UNION
            
            (SELECT UPPER(d.nome) 'COLABORADORES' ,
            (
            SELECT COUNT(dos.pkId)
            FROM rv_despesasOS dos
            INNER JOIN rv_ordemServico o ON (dos.fkOrdemServico = o.pkId)
            WHERE dos.fkDespesa = d.pkId
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '01'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'jan',
            (
            SELECT COUNT(dos.pkId)
            FROM rv_despesasOS dos
            INNER JOIN rv_ordemServico o ON (dos.fkOrdemServico = o.pkId)
            WHERE dos.fkDespesa = d.pkId
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '02'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'fev',
            (
            SELECT COUNT(dos.pkId)
            FROM rv_despesasOS dos
            INNER JOIN rv_ordemServico o ON (dos.fkOrdemServico = o.pkId)
            WHERE dos.fkDespesa = d.pkId
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '03'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'mar',
            (
            SELECT COUNT(dos.pkId)
            FROM rv_despesasOS dos
            INNER JOIN rv_ordemServico o ON (dos.fkOrdemServico = o.pkId)
            WHERE dos.fkDespesa = d.pkId
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '04'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'abr',
            (
            SELECT COUNT(dos.pkId)
            FROM rv_despesasOS dos
            INNER JOIN rv_ordemServico o ON (dos.fkOrdemServico = o.pkId)
            WHERE dos.fkDespesa = d.pkId
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '05'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'mai',
            (
            SELECT COUNT(dos.pkId)
            FROM rv_despesasOS dos
            INNER JOIN rv_ordemServico o ON (dos.fkOrdemServico = o.pkId)
            WHERE dos.fkDespesa = d.pkId
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '06'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'jun',
            (
            SELECT COUNT(dos.pkId)
            FROM rv_despesasOS dos
            INNER JOIN rv_ordemServico o ON (dos.fkOrdemServico = o.pkId)
            WHERE dos.fkDespesa = d.pkId
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '07'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'jul',
            (
            SELECT COUNT(dos.pkId)
            FROM rv_despesasOS dos
            INNER JOIN rv_ordemServico o ON (dos.fkOrdemServico = o.pkId)
            WHERE dos.fkDespesa = d.pkId
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '08'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'ago',
            (
            SELECT COUNT(dos.pkId)
            FROM rv_despesasOS dos
            INNER JOIN rv_ordemServico o ON (dos.fkOrdemServico = o.pkId)
            WHERE dos.fkDespesa = d.pkId
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '09'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'set',
            (
            SELECT COUNT(dos.pkId)
            FROM rv_despesasOS dos
            INNER JOIN rv_ordemServico o ON (dos.fkOrdemServico = o.pkId)
            WHERE dos.fkDespesa = d.pkId
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '10'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'out',
            (
            SELECT COUNT(dos.pkId)
            FROM rv_despesasOS dos
            INNER JOIN rv_ordemServico o ON (dos.fkOrdemServico = o.pkId)
            WHERE dos.fkDespesa = d.pkId
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '11'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'nov',
            (
            SELECT COUNT(dos.pkId)
            FROM rv_despesasOS dos
            INNER JOIN rv_ordemServico o ON (dos.fkOrdemServico = o.pkId)
            WHERE dos.fkDespesa = d.pkId
            $whereCliente
            AND DATE_FORMAT(o.dataAgendada, '%m') = '12'
            AND DATE_FORMAT(o.dataAgendada, '%Y') = '$data'
            ) 'dez'
            FROM rv_despesas d
            WHERE d.ativo = 'S'
            ORDER BY d.nome)
            ";
        }

        $rs = mysqli_query($connecta, $query);
        if (mysqli_num_rows($rs) > 0) {
            while ($row = mysqli_fetch_object($rs)) {
                $conteudo .= '
                    <tr>
                      <td> ' . $row->COLABORADORES . ' </td>
                      <td> ' . number_format($row->jan, 2, ',', '.') . ' </td>
                      <td> ' . number_format($row->fev, 2, ',', '.') . ' </td>
                      <td> ' . number_format($row->mar, 2, ',', '.') . ' </td>
                      <td> ' . number_format($row->abr, 2, ',', '.') . ' </td>
                      <td> ' . number_format($row->mai, 2, ',', '.') . ' </td>
                      <td> ' . number_format($row->jun, 2, ',', '.') . ' </td>
                      <td> ' . number_format($row->jul, 2, ',', '.') . ' </td>
                      <td> ' . number_format($row->ago, 2, ',', '.') . ' </td>
                      <td> ' . number_format($row->set, 2, ',', '.') . ' </td>
                      <td> ' . number_format($row->out, 2, ',', '.') . ' </td>
                      <td> ' . number_format($row->nov, 2, ',', '.') . ' </td>
                      <td> ' . number_format($row->dez, 2, ',', '.') . ' </td>
                    </tr>
                ';
            }
        }

        $tabela = '
        <tr style="height:55px">
            <td colspan="1" style="text-align:center; vertical-align:middle"> <img src="https://realvidas.com.br/wp-content/uploads/2020/07/logo-realvidas-head.png" width="200"> </td>
            <td colspan="12" style="color:#990000;font-size:small;text-align:center;height:50px;vertical-align:middle;font-weight:bold" > RELATÓRIO DE DESPESA OPERACIONAL </td>
        </tr>
            <tr>
            <td colspan="13" style="color:#0000FF;font-size:small;text-align:center;height:30px;vertical-align:middle;" > Cliente: ' . $tituloCliente . ' <br> ' . $_POST["selPeriodo"] . ' </td>
        </tr>
        <tr>
            <td width="230" style="font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;vertical-align:middle">DESCRIÇÃO</td>
            <td width="90" style="font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;vertical-align:middle">JANEIRO</td>
            <td width="90" style="font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;vertical-align:middle">FEVEREIRO</td>
            <td width="90" style="font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;vertical-align:middle">MARÇO</td> 
            <td width="90" style="font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;vertical-align:middle">ABRIL</td>
            <td width="90" style="font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;vertical-align:middle">MAIO</td>
            <td width="90" style="font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;vertical-align:middle">JUNHO</td>
            <td width="90" style="font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;vertical-align:middle">JULHO</td>
            <td width="90" style="font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;vertical-align:middle">AGOSTO</td>
            <td width="90" style="font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;vertical-align:middle">SETEMBRO</td>
            <td width="90" style="font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;vertical-align:middle">OUTUBRO</td>
            <td width="90" style="font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;vertical-align:middle">NOVEMBRO</td>
            <td width="90" style="font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;vertical-align:middle">DEZEMBRO</td>
        </tr>
        ' . $conteudo . '
        ';
    }
}
// FIM RELATÓRIO DESPESAS OPERACIONAIS

// INICIO RELATORIO SERVIÇOS
elseif ($_POST["tipoRelatorio"] == "Servicos_Clientes") {

    // RELATÓRIO ANUAL
    if ($_POST["selPeriodo"] == "Anual") {

        for ($i = 2020; $i <= (date("Y") + 1); $i++) {
            $cont++;
            if ($_POST["selTipo"] == "Valores") {
                $servicos .= "
                    (
                    SELECT COALESCE(SUM(os.valorRemocao + os.totalHoraParada),0)
                    FROM rv_ordemServico os
                    WHERE os.ativo = 'S'
                    $whereCliente
                    AND os.fkServico = s.pkId
                    AND DATE_FORMAT(os.dataAgendada, '%Y') = '$i'
                    ) '$cont',";
            } elseif ($_POST["selTipo"] == "Quantidade") {
                $servicos .= "
                    (
                    SELECT COUNT(os.pkId)
                    FROM rv_ordemServico os
                    WHERE os.ativo = 'S'
                    $whereCliente
                    AND os.fkServico = s.pkId
                    AND DATE_FORMAT(os.dataAgendada, '%Y') = '$i'
                    ) '$cont',";
            }
        }

        $query = "
            SELECT s.nome,
            " . substr($servicos, 0, -1) . "
            FROM rv_servicos s
            WHERE s.ativo = 'S'
            ORDER BY s.nome
        ";

        $rs = mysqli_query($connecta, $query);
        if (mysqli_num_rows($rs) > 0) {
            while ($row = mysqli_fetch_array($rs)) {
                $conteudo .= '
                    <tr>
                      <td> ' . $row["nome"] . ' </td>
                ';
                for ($i = 1; $i <= $cont; $i++) {
                    $conteudo .= '<td> ' . number_format($row[$i], 2, ',', '.') . ' </td>';
                }
                $conteudo .= '</tr>';
            }
        }

        $tabela = '
        <tr style="height:55px">
            <td colspan="1" style="text-align:center; vertical-align:middle"> <img src="https://realvidas.com.br/wp-content/uploads/2020/07/logo-realvidas-head.png" width="200"> </td>
            <td colspan="' . $cont . '" style="color:#990000;font-size:small;text-align:center;height:50px;vertical-align:middle;font-weight:bold" > RELATÓRIO DE SERVIÇOS </td>
        </tr>
        <tr>
            <td colspan="' . ($cont + 1) . '" style="color:#0000FF;font-size:small;text-align:center;height:30px;vertical-align:middle;" > Cliente: ' . $tituloCliente . ' <br> ' . $_POST["selPeriodo"] . ' </td>
        </tr>
        <tr>
            <td width="230" style="font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;vertical-align:middle">DESCRIÇÃO</td>
        ';
        for ($i = 0; $i < $cont; $i++) {
            $tabela .= '<td width="90" style="font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;vertical-align:middle"> ' . ($i + 2020) . ' </td>';
        }
        $tabela .= '</tr>
        ' . $conteudo . '
        ';
    }
    // RELATÓRIO MENSAL
    else {
        if ($_POST["selTipo"] == "Valores") {
            $query = "
            SELECT s.nome,
            (SELECT COALESCE(SUM(os.valorRemocao + os.totalHoraParada),0)
            FROM rv_ordemServico os
            WHERE os.ativo = 'S'
            AND os.fkServico = s.pkId
            AND DATE_FORMAT(os.dataAgendada, '%m') = '01'
            AND DATE_FORMAT(os.dataAgendada, '%Y') = '$data'
            $whereCliente
            ) 'jan',
            (SELECT COALESCE(SUM(os.valorRemocao + os.totalHoraParada),0)
            FROM rv_ordemServico os
            WHERE os.ativo = 'S'
            AND os.fkServico = s.pkId
            AND DATE_FORMAT(os.dataAgendada, '%m') = '02'
            AND DATE_FORMAT(os.dataAgendada, '%Y') = '$data'
            $whereCliente
            ) 'fev',
            (SELECT COALESCE(SUM(os.valorRemocao + os.totalHoraParada),0)
            FROM rv_ordemServico os
            WHERE os.ativo = 'S'
            AND os.fkServico = s.pkId
            AND DATE_FORMAT(os.dataAgendada, '%m') = '03'
            AND DATE_FORMAT(os.dataAgendada, '%Y') = '$data'
            $whereCliente
            ) 'mar',
            (SELECT COALESCE(SUM(os.valorRemocao + os.totalHoraParada),0)
            FROM rv_ordemServico os
            WHERE os.ativo = 'S'
            AND os.fkServico = s.pkId
            AND DATE_FORMAT(os.dataAgendada, '%m') = '04'
            AND DATE_FORMAT(os.dataAgendada, '%Y') = '$data'
            $whereCliente
            ) 'abr',
            (SELECT COALESCE(SUM(os.valorRemocao + os.totalHoraParada),0)
            FROM rv_ordemServico os
            WHERE os.ativo = 'S'
            AND os.fkServico = s.pkId
            AND DATE_FORMAT(os.dataAgendada, '%m') = '05'
            AND DATE_FORMAT(os.dataAgendada, '%Y') = '$data'
            $whereCliente
            ) 'mai',
            (SELECT COALESCE(SUM(os.valorRemocao + os.totalHoraParada),0)
            FROM rv_ordemServico os
            WHERE os.ativo = 'S'
            AND os.fkServico = s.pkId
            AND DATE_FORMAT(os.dataAgendada, '%m') = '06'
            AND DATE_FORMAT(os.dataAgendada, '%Y') = '$data'
            $whereCliente
            ) 'jun',
            (SELECT COALESCE(SUM(os.valorRemocao + os.totalHoraParada),0)
            FROM rv_ordemServico os
            WHERE os.ativo = 'S'
            AND os.fkServico = s.pkId
            AND DATE_FORMAT(os.dataAgendada, '%m') = '07'
            AND DATE_FORMAT(os.dataAgendada, '%Y') = '$data'
            $whereCliente
            ) 'jul',
            (SELECT COALESCE(SUM(os.valorRemocao + os.totalHoraParada),0)
            FROM rv_ordemServico os
            WHERE os.ativo = 'S'
            AND os.fkServico = s.pkId
            AND DATE_FORMAT(os.dataAgendada, '%m') = '08'
            AND DATE_FORMAT(os.dataAgendada, '%Y') = '$data'
            $whereCliente
            ) 'ago',
            (SELECT COALESCE(SUM(os.valorRemocao + os.totalHoraParada),0)
            FROM rv_ordemServico os
            WHERE os.ativo = 'S'
            AND os.fkServico = s.pkId
            AND DATE_FORMAT(os.dataAgendada, '%m') = '09'
            AND DATE_FORMAT(os.dataAgendada, '%Y') = '$data'
            $whereCliente
            ) 'set',
            (SELECT COALESCE(SUM(os.valorRemocao + os.totalHoraParada),0)
            FROM rv_ordemServico os
            WHERE os.ativo = 'S'
            AND os.fkServico = s.pkId
            AND DATE_FORMAT(os.dataAgendada, '%m') = '10'
            AND DATE_FORMAT(os.dataAgendada, '%Y') = '$data'
            $whereCliente
            ) 'out',
            (SELECT COALESCE(SUM(os.valorRemocao + os.totalHoraParada),0)
            FROM rv_ordemServico os
            WHERE os.ativo = 'S'
            AND os.fkServico = s.pkId
            AND DATE_FORMAT(os.dataAgendada, '%m') = '11'
            AND DATE_FORMAT(os.dataAgendada, '%Y') = '$data'
            $whereCliente
            ) 'nov',
            (SELECT COALESCE(SUM(os.valorRemocao + os.totalHoraParada),0)
            FROM rv_ordemServico os
            WHERE os.ativo = 'S'
            AND os.fkServico = s.pkId
            AND DATE_FORMAT(os.dataAgendada, '%m') = '12'
            AND DATE_FORMAT(os.dataAgendada, '%Y') = '$data'
            $whereCliente
            ) 'dez'
            FROM rv_servicos s
            WHERE s.ativo = 'S'
            ORDER BY s.nome
            ";
        } elseif ($_POST["selTipo"] == "Quantidade") {
            $query = "
            SELECT s.nome,
            (SELECT COUNT(os.pkId)
            FROM rv_ordemServico os
            WHERE os.ativo = 'S'
            AND os.fkServico = s.pkId
            AND DATE_FORMAT(os.dataAgendada, '%m') = '01'
            AND DATE_FORMAT(os.dataAgendada, '%Y') = '$data'
            $whereCliente
            ) 'jan',
            (SELECT COUNT(os.pkId)
            FROM rv_ordemServico os
            WHERE os.ativo = 'S'
            AND os.fkServico = s.pkId
            AND DATE_FORMAT(os.dataAgendada, '%m') = '02'
            AND DATE_FORMAT(os.dataAgendada, '%Y') = '$data'
            $whereCliente
            ) 'fev',
            (SELECT COUNT(os.pkId)
            FROM rv_ordemServico os
            WHERE os.ativo = 'S'
            AND os.fkServico = s.pkId
            AND DATE_FORMAT(os.dataAgendada, '%m') = '03'
            AND DATE_FORMAT(os.dataAgendada, '%Y') = '$data'
            $whereCliente
            ) 'mar',
            (SELECT COUNT(os.pkId)
            FROM rv_ordemServico os
            WHERE os.ativo = 'S'
            AND os.fkServico = s.pkId
            AND DATE_FORMAT(os.dataAgendada, '%m') = '04'
            AND DATE_FORMAT(os.dataAgendada, '%Y') = '$data'
            $whereCliente
            ) 'abr',
            (SELECT COUNT(os.pkId)
            FROM rv_ordemServico os
            WHERE os.ativo = 'S'
            AND os.fkServico = s.pkId
            AND DATE_FORMAT(os.dataAgendada, '%m') = '05'
            AND DATE_FORMAT(os.dataAgendada, '%Y') = '$data'
            $whereCliente
            ) 'mai',
            (SELECT COUNT(os.pkId)
            FROM rv_ordemServico os
            WHERE os.ativo = 'S'
            AND os.fkServico = s.pkId
            AND DATE_FORMAT(os.dataAgendada, '%m') = '06'
            AND DATE_FORMAT(os.dataAgendada, '%Y') = '$data'
            $whereCliente
            ) 'jun',
            (SELECT COUNT(os.pkId)
            FROM rv_ordemServico os
            WHERE os.ativo = 'S'
            AND os.fkServico = s.pkId
            AND DATE_FORMAT(os.dataAgendada, '%m') = '07'
            AND DATE_FORMAT(os.dataAgendada, '%Y') = '$data'
            $whereCliente
            ) 'jul',
            (SELECT COUNT(os.pkId)
            FROM rv_ordemServico os
            WHERE os.ativo = 'S'
            AND os.fkServico = s.pkId
            AND DATE_FORMAT(os.dataAgendada, '%m') = '08'
            AND DATE_FORMAT(os.dataAgendada, '%Y') = '$data'
            $whereCliente
            ) 'ago',
            (SELECT COUNT(os.pkId)
            FROM rv_ordemServico os
            WHERE os.ativo = 'S'
            AND os.fkServico = s.pkId
            AND DATE_FORMAT(os.dataAgendada, '%m') = '09'
            AND DATE_FORMAT(os.dataAgendada, '%Y') = '$data'
            $whereCliente
            ) 'set',
            (SELECT COUNT(os.pkId)
            FROM rv_ordemServico os
            WHERE os.ativo = 'S'
            AND os.fkServico = s.pkId
            AND DATE_FORMAT(os.dataAgendada, '%m') = '10'
            AND DATE_FORMAT(os.dataAgendada, '%Y') = '$data'
            $whereCliente
            ) 'out',
            (SELECT COUNT(os.pkId)
            FROM rv_ordemServico os
            WHERE os.ativo = 'S'
            AND os.fkServico = s.pkId
            AND DATE_FORMAT(os.dataAgendada, '%m') = '11'
            AND DATE_FORMAT(os.dataAgendada, '%Y') = '$data'
            $whereCliente
            ) 'nov',
            (SELECT COUNT(os.pkId)
            FROM rv_ordemServico os
            WHERE os.ativo = 'S'
            AND os.fkServico = s.pkId
            AND DATE_FORMAT(os.dataAgendada, '%m') = '12'
            AND DATE_FORMAT(os.dataAgendada, '%Y') = '$data'
            $whereCliente
            ) 'dez'
            FROM rv_servicos s
            WHERE s.ativo = 'S'
            ORDER BY s.nome
            ";
        }
        $rs = mysqli_query($connecta, $query);
        if (mysqli_num_rows($rs) > 0) {
            while ($row = mysqli_fetch_object($rs)) {
                $conteudo .= '
                    <tr>
                      <td> ' . $row->nome . ' </td>
                      <td> ' . number_format($row->jan, 2, ',', '.') . ' </td>
                      <td> ' . number_format($row->fev, 2, ',', '.') . ' </td>
                      <td> ' . number_format($row->mar, 2, ',', '.') . ' </td>
                      <td> ' . number_format($row->abr, 2, ',', '.') . ' </td>
                      <td> ' . number_format($row->mai, 2, ',', '.') . ' </td>
                      <td> ' . number_format($row->jun, 2, ',', '.') . ' </td>
                      <td> ' . number_format($row->jul, 2, ',', '.') . ' </td>
                      <td> ' . number_format($row->ago, 2, ',', '.') . ' </td>
                      <td> ' . number_format($row->set, 2, ',', '.') . ' </td>
                      <td> ' . number_format($row->out, 2, ',', '.') . ' </td>
                      <td> ' . number_format($row->nov, 2, ',', '.') . ' </td>
                      <td> ' . number_format($row->dez, 2, ',', '.') . ' </td>
                    </tr>
                ';
            }
        }

        $tabela = '
        <tr style="height:55px">
            <td colspan="1" style="text-align:center; vertical-align:middle"> <img src="https://realvidas.com.br/wp-content/uploads/2020/07/logo-realvidas-head.png" width="200"> </td>
            <td colspan="12" style="color:#990000;font-size:small;text-align:center;height:50px;vertical-align:middle;font-weight:bold" > RELATÓRIO DE SERVIÇOS </td>
        </tr>
            <tr>
            <td colspan="13" style="color:#0000FF;font-size:small;text-align:center;height:30px;vertical-align:middle;" > Cliente: ' . $tituloCliente . ' <br> ' . $_POST["selPeriodo"] . ' </td>
        </tr>
        <tr>
            <td width="230" style="font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;vertical-align:middle">DESCRIÇÃO</td>
            <td width="90" style="font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;vertical-align:middle">JANEIRO</td>
            <td width="90" style="font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;vertical-align:middle">FEVEREIRO</td>
            <td width="90" style="font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;vertical-align:middle">MARÇO</td> 
            <td width="90" style="font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;vertical-align:middle">ABRIL</td>
            <td width="90" style="font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;vertical-align:middle">MAIO</td>
            <td width="90" style="font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;vertical-align:middle">JUNHO</td>
            <td width="90" style="font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;vertical-align:middle">JULHO</td>
            <td width="90" style="font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;vertical-align:middle">AGOSTO</td>
            <td width="90" style="font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;vertical-align:middle">SETEMBRO</td>
            <td width="90" style="font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;vertical-align:middle">OUTUBRO</td>
            <td width="90" style="font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;vertical-align:middle">NOVEMBRO</td>
            <td width="90" style="font-size:x-small; text-align:center;font-weight:bold;background-color:#FFFFCC;vertical-align:middle">DEZEMBRO</td>
        </tr>
        ' . $conteudo . '
        ';
    }
}
// FIM RELATÓRIO SERVIÇOS

header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Content-type: application/x.msexcel");
header("Content-Type: text/html; charset=UTF-8", true);
header("Content-type: application/force-download");
header("Content-Disposition: attachment; filename=\"Relatorio_" . $_POST["tipoRelatorio"] . "_" . date('Ydm') . ".xls\"");

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