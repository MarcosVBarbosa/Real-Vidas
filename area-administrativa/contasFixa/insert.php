<?php

$pageActive = 'Contas';
include('../verifyConnection.php');
$acessoPermissoes = validAcesso(json_decode(base64_decode($_SESSION["acessoPermissoes"]), true), $pageActive);

if ($acessoPermissoes["isAcesso"] == 0) {
    $type = base64_encode("error");
    $msg  = base64_encode("Usuário sem permissão para acessar este conteúdo!");
    header('Location: ../?msg=' . $msg . '&type=' . $type);
    exit;
}
include('../connectDb.php');

if (!empty($_GET["ref"])) {
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://realvidas.com/area-administrativa/api/rv_contasFixa/?id=' . base64_decode($_GET["ref"]),
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
                            <h1>Conta</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../">Home</a></li>
                                <li class="breadcrumb-item active">Conta</li>
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
                                    <h3 class="card-title">Informações da Conta </h3>
                                </div>
                                <!-- /.card-header -->
                                <!-- form start -->
                                <form id="form">
                                    <input type="hidden" name="pkId" id="pkId" value="<?php echo $row['pkId']; ?>">
                                    <div class="card-body">

                                        <div class="row">
                                            <div class="col-12 col-sm-1">
                                                <div class="form-group">
                                                    <label for="txtNome">Cod:</label>
                                                    <input type="text" readonly disabled class="form-control" required id="cod" name="cod" value="<?php echo $proximoId; ?>" placeholder="Descrição">
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-7">
                                                <div class="form-group">
                                                    <label for="txtNome">Nome</label>
                                                    <input type="text" class="form-control" required id="nome" name="nome" value="<?php echo $row['nome']; ?>" placeholder="Nome">
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-4">
                                                <div class="form-group">
                                                    <label for="fkFornecedor">Categoria Fornecedor</label>
                                                    <select required id="fkFornecedor" name="fkFornecedor" class="form-control">
                                                        <option value="">-- Selecione --</option>
                                                        <?php
                                                        // Inicializa cURL
                                                        $curl = curl_init();

                                                        curl_setopt_array($curl, array(
                                                            CURLOPT_URL => 'https://realvidas.com/area-administrativa/api/rv_fornecedores/',
                                                            CURLOPT_RETURNTRANSFER => true,
                                                            CURLOPT_ENCODING => '',
                                                            CURLOPT_MAXREDIRS => 10,
                                                            CURLOPT_TIMEOUT => 0,
                                                            CURLOPT_FOLLOWLOCATION => true,
                                                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                                            CURLOPT_CUSTOMREQUEST => 'GET',
                                                        ));

                                                        $responseCategorias = curl_exec($curl);
                                                        curl_close($curl);

                                                        $responseCategorias = json_decode($responseCategorias, true);

                                                        if (!empty($responseCategorias['result']) && is_array($responseCategorias['result'])) {
                                                            foreach ($responseCategorias['result'] as $categoria) {
                                                                // Supondo que a categoria tem um campo 'nome'
                                                                $nomeCategoria = $categoria['nomeFantasia'] ?? '';
                                                                $pkId = $categoria['pkId'] ?? '';

                                                                // Verifica se essa categoria está selecionada
                                                                $selected = (isset($row['fkFornecedor']) && $row['fkFornecedor'] === $pkId) ? 'selected' : '';

                                                                echo '<option value="' . htmlspecialchars($pkId) . '" ' . $selected . '>' . htmlspecialchars($nomeCategoria) . '</option>';
                                                            }
                                                        } else {
                                                            echo '<option disabled>Categoria não encontrada</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 col-sm-12">
                                                <div class="form-group">
                                                    <label for="txtNome">Descrição</label>
                                                    <textarea class="form-control" required id="descricao" name="descricao" rows="3" placeholder="Descrição"><?php echo $row['descricao']; ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>

                                <!-- /.card-body -->

                                <div class="card-footer">
                                    <a href="./" class="btn btn-default">Cancelar</a>
                                    <?php if ($isButton) {
                                        echo '<button type="button" onclick="rv_contasFixa()" class="btn btn-primary">Salvar</button>';
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

        function rv_contasFixa() {
            const body = {
                descricao: document.getElementById('descricao').value || '',
                nome: document.getElementById('nome').value || '',
                fkFornecedor: document.getElementById('fkFornecedor').value || '',
                pkId: document.getElementById('pkId').value || '',
            }

            const method = body.pkId ? 'PUT' : 'POST';

            try {
                $.ajax({
                    url: `https://realvidas.com/area-administrativa/api/rv_contasFixa/`,
                    type: method,
                    contentType: 'application/json',
                    data: JSON.stringify(body),
                    success: function(response) {
                        // console.log('Status atualizado com sucesso:', response);s
                        window.location.href = './?msg=<?php echo base64_encode("Registro inserido com sucesso!"); ?>&type=<?php echo base64_encode("success"); ?>';
                    },
                    error: function(xhr, status, error) {
                        // console.error('Erro ao atualizar status:', xhr.responseText);
                        window.location.href = './?msg=<?php echo base64_encode("Algo deu errado!"); ?>&type=<?php echo base64_encode("danger"); ?>';
                    }
                });
            } catch (error) {
                console.error('Erro ao obter o novo status:', error);
            }
        }
    </script>
</body>

</html>