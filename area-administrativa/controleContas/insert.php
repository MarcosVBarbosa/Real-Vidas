<?php

$pageActive = 'Controle Contas';
include('../verifyConnection.php');
$acessoPermissoes = validAcesso(json_decode(base64_decode($_SESSION["acessoPermissoes"]), true), $pageActive);

if ($acessoPermissoes["isAcesso"] == 0) {
    $type = base64_encode("error");
    $msg  = base64_encode("Usuário sem permissão para acessar este conteúdo!");
    header('Location: ../?msg=' . $msg . '&type=' . $type);
    exit;
}
include('../connectDb.php');

$curl = curl_init();

$pesquisa = $_GET['id'] !== '' ? "?id=" . $_GET['id'] : '';

curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://realvidas.com/area-administrativa/api/rv_contasFixa/' . $pesquisa,
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

// Decodifica a resposta em array associativo
$response = json_decode($response, true);

if ($response['result']['pkId'] > 0) {
    $totalRegistros[] = $response['result'] ?? [];
} else {
    $totalRegistros = $response['result'] ?? [];
}

if (!empty($_GET["ref"])) {
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://realvidas.com/area-administrativa/api/rv_controleContas/?id=' . base64_decode($_GET["ref"]),
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

    $row = $response['result'] ?? null;

    // echo "<script> console.log(" . json_encode($row) . "); </script>";

    // Verifica se foi encontrado
    if (empty($row)) {
        $type = base64_encode('danger');
        $msg = base64_encode('Registro não encontrado!');
        header('Location: ./?msg=' . $msg . '&type=' . $type);
        exit;
    }

    $method = 'PUT';
    $proximoId =  str_pad(base64_decode($_GET["ref"]), 5, '0', STR_PAD_LEFT);
    $isButton = validButtonSubmit($acessoPermissoes['isPermissao'], 'Editar');
} else {
    $proximoId = str_pad(base64_decode($_GET["proximoId"]), 5, '0', STR_PAD_LEFT);
    $method = 'POST';
    $isButton = validButtonSubmit($acessoPermissoes['isPermissao'], 'Salvar');
}

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo $titleSystem ?></title>
    <link rel="icon" href="../dist/img/favicon.png" />
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../dist/css/adminlte.min.css">
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <style>
        .custom-file-label::after {
            content: "Selecionar"
        }
    </style>
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        <?php include('../header.php') ?>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <?php include('../sideBar.php') ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Controle de Contas</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../">Home</a></li>
                                <li class="breadcrumb-item active">Controle</li>
                            </ol>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <!-- general form elements -->
                            <div class="card card-default color-palette-box">
                                <div class="card-header">
                                    <h3 class="card-title">Cadastro</h3>
                                </div>
                                <!-- /.card-header -->
                                <!-- form start -->
                                <form id="formContas">
                                    <input type="hidden" name="pkId" id="pkId" value="<?php echo $row['pkId']; ?>">
                                    <div class="card-body">

                                        <div class="row">
                                            <div class="col-12 col-sm-2">
                                                <div class="form-group">
                                                    <label for="txtNome">Cod:</label>
                                                    <div class="input-group input-group-sm" style="width:100%;">
                                                        <input type="text" id="id" class="form-control float-right" placeholder="Pesquisar" style="height: 38px;" value="<?php echo isset($_GET["id"]) ? htmlspecialchars($_GET["id"]) : ''; ?>">
                                                        <div class="input-group-append">
                                                            <button type="button" onclick="getCod()" class="btn btn-default"><i class="fas fa-search"></i></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-10">
                                                <div class="form-group">
                                                    <label for="txtNome">Conta</label>
                                                    <select class="form-control" id="fkContaFixa" name="fkContaFixa">
                                                        <option value=""> -- Selecione -- </option>
                                                        <?php
                                                        foreach ($totalRegistros as $row1) { ?>
                                                            <option <?php echo $row['fkContaFixa'] == $row1['pkId'] || count($totalRegistros) == 1 ? "selected" : ""; ?> value="<?php echo $row1['pkId']; ?>"><?php echo  str_pad($row1['pkId'], 5, '0', STR_PAD_LEFT)  . " - " . $row1['nome']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 col-md-2">
                                                <div class="form-group">
                                                    <label for="txtDataNascimento">Débito Automatico</label>
                                                    <select name="debAutomatico" id="debAutomatico" class="form-control">
                                                        <option value="1" <?php echo $row['status'] == "1" ? "selected" : ""; ?>>Sim</option>
                                                        <option value="0" <?php echo $row['status'] == "0" ? "selected" : ""; ?>>Não</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md">
                                                <div class="form-group">
                                                    <label for="dataRecebido">Data Recebido</label>
                                                    <input type="date" class="form-control" id="dataRecebido" name="dataRecebido" value="<?php echo $row['dataRecebido']; ?>" placeholder="Data Recebido">
                                                </div>
                                            </div>
                                            <div class="col-12 col-md">
                                                <div class="form-group">
                                                    <label for="dataVenc">Data Vencimento</label>
                                                    <input type="date" class="form-control" id="dataVenc" name="dataVenc" value="<?php echo $row['dataVenc']; ?>" placeholder="Data Vencimento">
                                                </div>
                                            </div>
                                            <div class="col-12 col-md">
                                                <div class="form-group">
                                                    <label for="dataPrevistaPag">Data Prev. Pgto.</label>
                                                    <input type="date" class="form-control" id="dataPrevistaPag" name="dataPrevistaPag" value="<?php echo $row['dataPrevistaPag'] ?>" placeholder="Data Previsto Pag">
                                                </div>
                                            </div>
                                            <div class="col-12 col-md">
                                                <div class="form-group">
                                                    <label for="valorConta">Valor da Conta <?php echo $row->valorConta; ?></label>
                                                    <input type="number" step="0,00" class="form-control" id="valorConta" name="valorConta" value="<?php echo $row['valorConta'] ?>" placeholder="0,00">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>

                                <!-- /.card-body -->

                                <div class="card-footer">
                                    <a href="./" class="btn btn-default">Cancelar</a>
                                    <?php if ($isButton) {
                                        echo '<button type="button" onclick="rv_controleContas()" class="btn btn-primary">Salvar</button>';
                                    }; ?>
                                </div>
                                </form>
                            </div>
                            <!-- /.card -->

                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.row -->
                </div><!-- /.container-fluid -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
        <?php include('../footer.php') ?>

    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="../plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- InputMask -->
    <script src="../dist/js/jquery.mask.min.js"></script>
    <!-- bs-custom-file-input -->
    <script src="../plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
    <!-- AdminLTE App -->
    <script src="../dist/js/adminlte.min.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="../dist/js/demo.js"></script>
    <script>
        $(document).ready(function() {
            bsCustomFileInput.init();
        });


        function getCod() {
            const pesquisa = document.getElementById('id').value;
            window.location.href = `insert.php?id=${pesquisa}`;
        }

        function rv_controleContas() {
            const body = {
                fkContaFixa: document.getElementById('fkContaFixa').value || '',
                debAutomatico: document.getElementById('debAutomatico').value || '',
                dataRecebido: document.getElementById('dataRecebido').value || '',
                dataVenc: document.getElementById('dataVenc').value || '',
                dataPrevistaPag: document.getElementById('dataPrevistaPag').value || '',
                valorConta: document.getElementById('valorConta').value || '',
                pkId: document.getElementById('pkId').value || '',
            }

            const method = body.pkId ? 'PUT' : 'POST';

            try {
                $.ajax({
                    url: `https://realvidas.com/area-administrativa/api/rv_controleContas/`,
                    type: method,
                    contentType: 'application/json',
                    data: JSON.stringify(body),
                    success: function(response) {
                        // console.log('Status atualizado com sucesso:', response);
                        window.location.href = 'https://realvidas.com/area-administrativa/controleContas/?msg=<?php echo base64_encode("Registro inserido com sucesso!"); ?>&type=<?php echo base64_encode("success"); ?>';
                    },
                    error: function(xhr, status, error) {
                        console.error('Erro ao atualizar status:', xhr.responseText);
                        // window.location.href = 'https://realvidas.com/area-administrativa/controleContas/?msg=<?php echo base64_encode("Algo deu errado!"); ?>&type=<?php echo base64_encode("danger"); ?>';
                    }
                });
            } catch (error) {
                console.error('Erro ao obter o novo status:', error);
            }
        }
    </script>
</body>

</html>