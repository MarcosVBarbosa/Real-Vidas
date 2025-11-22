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

$linkRequest = explode('/', $_SERVER["HTTP_REFERER"]);
$linkRequest = end($linkRequest);
$queryFaturamento1 = "";
$queryFaturamento2 = "";

$tempoExcedente = 0;

if ($_SERVER["HTTP_HOST"] == $linkUrl) {

    if (!empty($_POST["destino"]) && count($_POST["destino"]) > 0) {
        $destino = $_POST["destino"];
        $fkServico = $_POST["txtServico"];
        array_unshift($destino, $_POST["txtOrigem"]);

        $totalPercurso = 0;
        for ($i = 0; $i < count($destino) - 1; $i++) {
            $fkOrigem = $destino[$i];
            $fkDestino = $destino[$i + 1];

            $curl = curl_init();
            // $debugs .= '  https://realvidas.com/area-administrativa/api/rv_estabelecimentosDestino/?fkEstabelecimento=' . $fkOrigem . '&fkDestino=' . $fkDestino . '&fkServico=' . $fkServico;
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://realvidas.com/area-administrativa/api/rv_estabelecimentosDestino/?fkEstabelecimento=' . $fkOrigem . '&fkDestino=' . $fkDestino . '&fkServico=' . $fkServico,
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
            $result  = $response['result'] ?? [];

            if (!empty($result) && isset($result['ida'])) {
                $totalPercurso += $result['ida'];
                // $debugs .= " - Ida:" . $result['ida'];
            }
        }
    }

    if (empty($_POST["pkId"])) {

        if ($dadosConexao[3] == 1) {
            $queryFaturamento1 = ",valorRemocao,valorPercurso,despesaOperacional,fkVTR,valorDiesel,qtdeHoraParada,valorHoraParada,limiteHoraParada";
            $queryFaturamento2 = "
            ,'" . mysqli_real_escape_string($connecta, trim($_POST["txtValorRemocao"])) . "'
            ,'" . mysqli_real_escape_string($connecta, trim($totalPercurso)) . "'
            ,'" . mysqli_real_escape_string($connecta, trim($_POST["txtDespesaOperacional"])) . "'
            ,'" . mysqli_real_escape_string($connecta, trim($_POST["txtVTR"])) . "'
            ,'" . mysqli_real_escape_string($connecta, trim($_POST["txtValorDiesel"])) . "'
            ,'" . mysqli_real_escape_string($connecta, trim($_POST["txtQtdeHoraParada"])) . "'
            ,'" . mysqli_real_escape_string($connecta, trim($_POST["txtValorHoraParada"])) . "'
            ,'" . mysqli_real_escape_string($connecta, trim($_POST["txtLimiteHoraParada"])) . "'
            ";
        }

        $query = "INSERT INTO rv_ordemServico (nrOrdemServico,finalizada,aliquota,fkServico,fkTipoServico,dataSolicitada,dataAgendada,trajeto,fkCliente,fkOrigem,chegadaOrigem,saidaOrigem,kmOrigem,kmInicial,kmFinal,paciente,convenio,nrCartao,solicitante,dataHoraInicio,dataHoraFim $queryFaturamento1) VALUES
        (
        (SELECT (MAX(o.nrOrdemServico) + 1) FROM rv_ordemServico o WHERE o.ativo = 'S'),
        '" . mysqli_real_escape_string($connecta, trim($_POST["txtFinalizada"])) . "',
        '" . mysqli_real_escape_string($connecta, trim($_POST["txtAliquota"])) . "',
        '" . mysqli_real_escape_string($connecta, trim($_POST["txtServico"])) . "',
        '" . mysqli_real_escape_string($connecta, trim($_POST["txtTipoServico"])) . "',
        '" . mysqli_real_escape_string($connecta, trim($_POST["txtDataSolicitada"])) . "',
        '" . mysqli_real_escape_string($connecta, trim($_POST["txtDataAgendada"])) . "',
        '" . mysqli_real_escape_string($connecta, trim($_POST["txtTrajeto"])) . "',
        '" . mysqli_real_escape_string($connecta, trim($_POST["txtCliente"])) . "',
        '" . mysqli_real_escape_string($connecta, trim($_POST["txtOrigem"])) . "',
        '" . mysqli_real_escape_string($connecta, trim($_POST["txtChegadaOrigem"])) . "',
        '" . mysqli_real_escape_string($connecta, trim($_POST["txtSaidaOrigem"])) . "',
        '" . mysqli_real_escape_string($connecta, trim($_POST["txtKmOrigem"])) . "',
        '" . mysqli_real_escape_string($connecta, trim($_POST["txtKmInicial"])) . "',
        '" . mysqli_real_escape_string($connecta, trim($_POST["txtKmFinal"])) . "',
        '" . mysqli_real_escape_string($connecta, trim($_POST["txtPaciente"])) . "',
        '" . mysqli_real_escape_string($connecta, trim($_POST["txtConvenio"])) . "',
        '" . mysqli_real_escape_string($connecta, trim($_POST["txtNrCartao"])) . "',
        '" . mysqli_real_escape_string($connecta, trim($_POST["txtSolicitante"])) . "',
        '" . mysqli_real_escape_string($connecta, trim($_POST["txtDataHoraInicio"])) . "',
        '" . mysqli_real_escape_string($connecta, trim($_POST["txtDataHoraFim"])) . "'
        $queryFaturamento2)";

        mysqli_query($connecta, $query);
        $id_orcamento = mysqli_insert_id($connecta);
    } else {

        if ($dadosConexao[3] == 1) {
            $queryFaturamento1 = "
            ,valorRemocao = '" . mysqli_real_escape_string($connecta, trim($_POST["txtValorRemocao"])) . "'
            ,valorPercurso = '" . mysqli_real_escape_string($connecta, trim($totalPercurso)) . "'
            ,despesaOperacional = '" . mysqli_real_escape_string($connecta, trim($_POST["txtDespesaOperacional"])) . "'
            ,fkVTR = '" . mysqli_real_escape_string($connecta, trim($_POST["txtVTR"])) . "'
            ,valorDiesel = '" . mysqli_real_escape_string($connecta, trim($_POST["txtValorDiesel"])) . "'
            ,qtdeHoraParada = '" . mysqli_real_escape_string($connecta, trim($_POST["txtQtdeHoraParada"])) . "'
            ,valorHoraParada = '" . mysqli_real_escape_string($connecta, trim($_POST["txtValorHoraParada"])) . "'
            ,limiteHoraParada = '" . mysqli_real_escape_string($connecta, trim($_POST["txtLimiteHoraParada"])) . "'
            ";
        }

        $query = "UPDATE rv_ordemServico SET
        finalizada = '" . mysqli_real_escape_string($connecta, trim($_POST["txtFinalizada"])) . "',
        aliquota = '" . mysqli_real_escape_string($connecta, trim($_POST["txtAliquota"])) . "',
        fkServico = '" . mysqli_real_escape_string($connecta, trim($_POST["txtServico"])) . "',
        fkTipoServico = '" . mysqli_real_escape_string($connecta, trim($_POST["txtTipoServico"])) . "',
        dataSolicitada = '" . mysqli_real_escape_string($connecta, trim($_POST["txtDataSolicitada"])) . "',
        dataAgendada = '" . mysqli_real_escape_string($connecta, trim($_POST["txtDataAgendada"])) . "',
        trajeto = '" . mysqli_real_escape_string($connecta, trim($_POST["txtTrajeto"])) . "',
        fkCliente = '" . mysqli_real_escape_string($connecta, trim($_POST["txtCliente"])) . "',
        fkOrigem = '" . mysqli_real_escape_string($connecta, trim($_POST["txtOrigem"])) . "',
        chegadaOrigem = '" . mysqli_real_escape_string($connecta, trim($_POST["txtChegadaOrigem"])) . "',
        saidaOrigem = '" . mysqli_real_escape_string($connecta, trim($_POST["txtSaidaOrigem"])) . "',
        kmOrigem = '" . mysqli_real_escape_string($connecta, trim($_POST["txtKmOrigem"])) . "',
        kmInicial = '" . mysqli_real_escape_string($connecta, trim($_POST["txtKmInicial"])) . "',
        kmFinal = '" . mysqli_real_escape_string($connecta, trim($_POST["txtKmFinal"])) . "',
        paciente = '" . mysqli_real_escape_string($connecta, trim($_POST["txtPaciente"])) . "',
        convenio = '" . mysqli_real_escape_string($connecta, trim($_POST["txtConvenio"])) . "',
        nrCartao = '" . mysqli_real_escape_string($connecta, trim($_POST["txtNrCartao"])) . "',
        solicitante = '" . mysqli_real_escape_string($connecta, trim($_POST["txtSolicitante"])) . "',
        dataHoraInicio = '" . mysqli_real_escape_string($connecta, trim($_POST["txtDataHoraInicio"])) . "',
        dataHoraFim = '" . mysqli_real_escape_string($connecta, trim($_POST["txtDataHoraFim"])) . "'
        $queryFaturamento1
        WHERE pkId = " . base64_decode($_POST["pkId"]);
        $id_orcamento = base64_decode($_POST["pkId"]);
        mysqli_query($connecta, $query);
    }

    // UPLOAD FICHA PDF
    if ($_FILES["txtCaminhoFicha"]["error"] <> 4) {

        $extensao = explode('.', $_FILES["txtCaminhoFicha"]["name"]);
        $extensao = end($extensao);
        $nomeArquivo = sha1($_FILES["txtCaminhoFicha"]["tmp_name"] . time()) . "." . $extensao;
        move_uploaded_file($_FILES["txtCaminhoFicha"]["tmp_name"], "arquivos/" . $nomeArquivo);
        mysqli_query($connecta, "UPDATE rv_ordemServico SET caminhoFicha = '" . $nomeArquivo . "' WHERE pkId = " . $id_orcamento);
    }

    // UPLOAD FICHA PDF
    if ($_FILES["txtCaminhoGuia"]["error"] <> 4) {

        $extensao = explode('.', $_FILES["txtCaminhoGuia"]["name"]);
        $extensao = end($extensao);
        $nomeArquivo = sha1($_FILES["txtCaminhoGuia"]["tmp_name"] . time()) . "." . $extensao;
        move_uploaded_file($_FILES["txtCaminhoGuia"]["tmp_name"], "arquivos/" . $nomeArquivo);
        mysqli_query($connecta, "UPDATE rv_ordemServico SET caminhoGuia = '" . $nomeArquivo . "' WHERE pkId = " . $id_orcamento);
    }

    // echo strtotime($_POST["txtSaidaOrigem"]) . "-" . strtotime($_POST["txtChegadaOrigem"]);exit;
    $dataDiff = (strtotime($_POST["txtSaidaOrigem"]) - strtotime($_POST["txtChegadaOrigem"]));
    $tempoExcedente = $tempoExcedente + ($dataDiff / 60);

    //echo $query;exit;

    if (count($_POST["destino"]) > 0) {

        $sql = "INSERT INTO rv_ordemServicoDestino (fkOrdemServico,fkDestino,horaChegada,horaSaida,distancia) VALUES ";

        for ($i = 0; $i < count($_POST["destino"]); $i++) {

            if ($i == 0) {
                $id_destino = $_POST["txtOrigem"];
            } else {
                $id_destino = $_POST["destino"][($i - 1)];
            }
            if (!empty($_POST["destino"][$i])) {
                //(SELECT distancia FROM rv_estabelecimentosDestino WHERE fkEstabelecimento = $id_destino AND fkDestino = ".$_POST["destino"][$i].")
                $sql .= "($id_orcamento,
                '" . $_POST["destino"][$i] . "',
                '" . $_POST["horaChegada"][$i] . "',
                '" . $_POST["horaSaida"][$i] . "',
                '" . $_POST["distancia"][$i] . "'
                ),";

                if (strtotime($_POST["horaSaida"][$i]) < strtotime($_POST["horaChegada"][$i])) {
                    echo "
                    <script>
                    alert('Data Final do Destiono não pode ser menor do que a Data Inicial do Destino!');
                    history.back();
                    </script>
                    ";
                    exit;
                }

                $dataDiff = (strtotime($_POST["horaSaida"][$i]) - strtotime($_POST["horaChegada"][$i]));
                $tempoExcedente = $tempoExcedente + ($dataDiff / 60);
            }
        }

        $sql0 = "DELETE FROM rv_ordemServicoDestino WHERE fkOrdemServico = " . $id_orcamento;
        $rs = mysqli_query($connecta, $sql0);

        $sql = substr($sql, 0, -1);
        $rs = mysqli_query($connecta, $sql);
    }

    // if (count($_POST["colaborador"]) > 0) {
    //     $sql = "DELETE FROM rv_colaboradoresOS WHERE fkOrdemServico = " . $id_orcamento;
    //     $rs = mysqli_query($connecta, $sql);

    //     $sql = "INSERT INTO rv_colaboradoresOS (fkOrdemServico,fkColaborador,qtdeHora,ajudaCusto,valorHora) VALUES ";

    //     for ($i = 0; $i < count($_POST["colaborador"]); $i++) {
    //         if (!empty($_POST["colaborador"][$i])) {
    //             $sql .= "('$id_orcamento',
    //             '" . $_POST["colaborador"][$i] . "',
    //             '" . $_POST["qtdeHora"][$i] . "',
    //             '" . $_POST["ajudaCusto"][$i] . "',
    //             (SELECT valorHora FROM rv_colaboradoresServico WHERE fkColaborador = " . $_POST["colaborador"][$i] . " AND fkServico = " . $_POST["txtServico"] . ")
    //             ),";
    //         }
    //     }

    //     $sql = substr($sql, 0, -1);
    //     $rs = mysqli_query($connecta, $sql);
    // }

    if (count($_POST["colaborador"]) == 0 && !empty($_POST["pkId"])) {
        // Apaga os anteriores
        $sql = "DELETE FROM rv_colaboradoresOS WHERE fkOrdemServico = " . intval($id_orcamento);
        mysqli_query($connecta, $sql);
    } else  if (count($_POST["colaborador"]) > 0) {
        // Apaga os anteriores
        $sql = "DELETE FROM rv_colaboradoresOS WHERE fkOrdemServico = " . intval($id_orcamento);
        mysqli_query($connecta, $sql);

        $valores = [];

        for ($i = 0; $i < count($_POST["colaborador"]); $i++) {
            $colab = intval($_POST["colaborador"][$i]);
            $qtdeHora = floatval($_POST["qtdeHora"][$i]);
            $ajudaCusto = !empty($_POST["ajudaCusto"][$i]) ? floatval($_POST["ajudaCusto"][$i]) : '"0,00"';
            $fkServico = intval($_POST["txtServico"]);

            // Busca valorHora
            $sqlValor = "SELECT valorHora FROM rv_colaboradoresServico WHERE fkColaborador = $colab AND fkServico = $fkServico LIMIT 1";
            $resValor = mysqli_query($connecta, $sqlValor);
            $valorHora = 0;
            if ($rowValor = mysqli_fetch_assoc($resValor)) {
                $valorHora = floatval($rowValor['valorHora']);
            }

            $valores[] = "($id_orcamento, $colab, $qtdeHora, $ajudaCusto, $valorHora)";
        }

        if (!empty($valores)) {
            $sqlInsert = "INSERT INTO rv_colaboradoresOS (fkOrdemServico, fkColaborador, qtdeHora, ajudaCusto, valorHora) VALUES " . implode(",", $valores);
            mysqli_query($connecta, $sqlInsert);
        }
    }

    // CASO NÃO DESEJE NENHUMA DESPESA, SERÁ EXCLUIDO TODOS VÁLIDA APENAS EM CASO DE ATUALIZAÇÃO
    if (count($_POST["despesa"]) == 0 && !empty($_POST["pkId"])) {
        $sql = "DELETE FROM rv_despesasOS WHERE fkOrdemServico = " . $id_orcamento;
        $rs = mysqli_query($connecta, $sql);
    } else if (count($_POST["despesa"]) > 0) {

        $sql = "DELETE FROM rv_despesasOS WHERE fkOrdemServico = " . $id_orcamento;
        $rs = mysqli_query($connecta, $sql);

        $sql = "INSERT INTO rv_despesasOS (fkOrdemServico,fkDespesa,qtde,valor) VALUES ";

        for ($i = 0; $i < count($_POST["despesa"]); $i++) {
            if (!empty($_POST["despesa"][$i])) {
                $sql .= "($id_orcamento,
                '" . $_POST["despesa"][$i] . "',
                '" . $_POST["qtde"][$i] . "',
                (SELECT valor FROM rv_despesas WHERE pkId = '" . $_POST["despesa"][$i] . "')
                ),";
            }
        }

        $sql = substr($sql, 0, -1);
        $rs = mysqli_query($connecta, $sql);
    }


    // ATUALIZA KM PERCORRIDO, GASTO DIESEL E DESPESA OPERACIONAL

    // CALCULA IDA OU IDA E VOLTA
    if ($_POST["txtTrajeto"] == "Ida") {
        $trajeto = 1;
    } else {
        $trajeto = 2;
    }

    $tempoExcedente = ($tempoExcedente - $_POST["txtLimiteHoraParada"]) * 60;
    if ($tempoExcedente < 0) {
        $tempoExcedente = 0;
    }
    //TRANSFORMA HORAS EM DECIMAL
    $tempoExcedente = (gmdate("H", $tempoExcedente)) + (gmdate("i", $tempoExcedente) / 60);


    $query = "
    UPDATE rv_ordemServico SET kmPercorrido = (kmFinal - kmInicial),
    qtdeHoraParada = $tempoExcedente,
    totalHoraParada = (qtdeHoraParada * valorHoraParada),
    mediaDiesel = (SELECT consumo FROM rv_vtr WHERE pkId = '" . mysqli_real_escape_string($connecta, trim($_POST["txtVTR"])) . "'),
    gastoDiesel = (kmPercorrido / mediaDiesel * valorDiesel),
    despesaOperacional = (
        (COALESCE(gastoDiesel,0))
        +
        (SELECT COALESCE(SUM(ajudaCusto + (qtdeHora * valorHora)),0) FROM rv_colaboradoresOS WHERE fkOrdemServico = $id_orcamento)
        +
        (SELECT COALESCE(SUM(qtde * valor),0) FROM rv_despesasOS WHERE fkOrdemServico = $id_orcamento)
    )
    WHERE pkId = $id_orcamento
    ";
    $result = mysqli_query($connecta, $query);


    if ($result) {
        $type = base64_encode("success");
        $msg = base64_encode("Registro salvo com sucesso! $debugs ");
    } else {
        $type = base64_encode("danger");
        $msg = base64_encode("Falha ao salvar o registro! Por favor tente mais tarde.");
    }
}

header('Location: insert.php?ref=' . base64_encode($id_orcamento) . '&type=' . $type . '&msg=' . $msg);
exit;
