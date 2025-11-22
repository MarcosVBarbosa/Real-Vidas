<?php

$pageActive = 'Fornecedores';
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
        CURLOPT_URL => 'https://realvidas.com/area-administrativa/api/rv_fornecedores/?id=' . base64_decode($_GET["ref"]),
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
                                    <h3 class="card-title">Fornecedor</h3>
                                </div>
                                <!-- /.card-header -->
                                <!-- form start -->
                                <form id="form">
                                    <input type="hidden" name="pkId" id="pkId" value="<?php echo $row['pkId'] ?? ''; ?>">

                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="razaoSocial">Razão Social</label>
                                                    <input type="text" class="form-control" required id="razaoSocial" name="razaoSocial" value="<?php echo $row['razaoSocial'] ?? ''; ?>" placeholder="Razão Social">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="nomeFantasia">Nome Fantasia</label>
                                                    <input type="text" class="form-control" id="nomeFantasia" name="nomeFantasia" value="<?php echo $row['nomeFantasia'] ?? ''; ?>" placeholder="Nome Fantasia">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-sm-8">
                                                <div class="form-group">
                                                    <label for="endereco">Endereço</label>
                                                    <input type="text" class="form-control" id="endereco" name="endereco" value="<?php echo $row['endereco'] ?? ''; ?>" placeholder="Endereço">
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label for="numero">Número</label>
                                                    <input type="text" class="form-control" id="numero" name="numero" value="<?php echo $row['numero'] ?? ''; ?>" placeholder="Número">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-sm-8">
                                                <div class="form-group">
                                                    <label for="pontoReferencia">Ponto de Referência</label>
                                                    <input type="text" class="form-control" id="pontoReferencia" name="pontoReferencia" value="<?php echo $row['pontoReferencia'] ?? ''; ?>" placeholder="Ponto de Referência">
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label for="categoriaFornecedor">Categoria Fornecedor</label>
                                                    <select required id="categoriaFornecedor" name="categoriaFornecedor" class="form-control">
                                                        <option value="">-- Selecione --</option>
                                                        <?php
                                                        // Inicializa cURL
                                                        $curl = curl_init();

                                                        curl_setopt_array($curl, array(
                                                            CURLOPT_URL => 'https://realvidas.com/area-administrativa/api/rv_categoriasFornecedores/',
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
                                                                $nomeCategoria = $categoria['nome'] ?? '';

                                                                // Verifica se essa categoria está selecionada
                                                                $selected = (isset($row['categoriaFornecedor']) && $row['categoriaFornecedor'] === $nomeCategoria) ? 'selected' : '';

                                                                echo '<option value="' . htmlspecialchars($nomeCategoria) . '" ' . $selected . '>' . htmlspecialchars($nomeCategoria) . '</option>';
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
                                            <div class="col-sm-5">
                                                <div class="form-group">
                                                    <label for="cidade">Cidade</label>
                                                    <input type="text" class="form-control" id="cidade" name="cidade" value="<?php echo $row['cidade'] ?? ''; ?>" placeholder="Cidade">
                                                </div>
                                            </div>
                                            <div class="col-sm-2">
                                                <div class="form-group">
                                                    <label for="estado">Estado</label>
                                                    <select required id="estado" name="estado" class="form-control">
                                                        <option value="">-- Selecione --</option>
                                                        <option <?php echo $row->estado == "AC" ? "selected" : ""; ?> value="AC">Acre</option>
                                                        <option <?php echo $row->estado == "AL" ? "selected" : ""; ?> value="AL">Alagoas</option>
                                                        <option <?php echo $row->estado == "AP" ? "selected" : ""; ?> value="AP">Amapá</option>
                                                        <option <?php echo $row->estado == "AM" ? "selected" : ""; ?> value="AM">Amazonas</option>
                                                        <option <?php echo $row->estado == "BA" ? "selected" : ""; ?> value="BA">Bahia</option>
                                                        <option <?php echo $row->estado == "CE" ? "selected" : ""; ?> value="CE">Ceará</option>
                                                        <option <?php echo $row->estado == "DF" ? "selected" : ""; ?> value="DF">Distrito Federal</option>
                                                        <option <?php echo $row->estado == "ES" ? "selected" : ""; ?> value="ES">Espírito Santo</option>
                                                        <option <?php echo $row->estado == "GO" ? "selected" : ""; ?> value="GO">Goiás</option>
                                                        <option <?php echo $row->estado == "MA" ? "selected" : ""; ?> value="MA">Maranhão</option>
                                                        <option <?php echo $row->estado == "MT" ? "selected" : ""; ?> value="MT">Mato Grosso</option>
                                                        <option <?php echo $row->estado == "MS" ? "selected" : ""; ?> value="MS">Mato Grosso do Sul</option>
                                                        <option <?php echo $row->estado == "MG" ? "selected" : ""; ?> value="MG">Minas Gerais</option>
                                                        <option <?php echo $row->estado == "PA" ? "selected" : ""; ?> value="PA">Pará</option>
                                                        <option <?php echo $row->estado == "PB" ? "selected" : ""; ?> value="PB">Paraíba</option>
                                                        <option <?php echo $row->estado == "PR" ? "selected" : ""; ?> value="PR">Paraná</option>
                                                        <option <?php echo $row->estado == "PE" ? "selected" : ""; ?> value="PE">Pernambuco</option>
                                                        <option <?php echo $row->estado == "PI" ? "selected" : ""; ?> value="PI">Piauí</option>
                                                        <option <?php echo $row->estado == "RJ" ? "selected" : ""; ?> value="RJ">Rio de Janeiro</option>
                                                        <option <?php echo $row->estado == "RN" ? "selected" : ""; ?> value="RN">Rio Grande do Norte</option>
                                                        <option <?php echo $row->estado == "RS" ? "selected" : ""; ?> value="RS">Rio Grande do Sul</option>
                                                        <option <?php echo $row->estado == "RO" ? "selected" : ""; ?> value="RO">Rondônia</option>
                                                        <option <?php echo $row->estado == "RR" ? "selected" : ""; ?> value="RR">Roraima</option>
                                                        <option <?php echo $row->estado == "SC" ? "selected" : ""; ?> value="SC">Santa Catarina</option>
                                                        <option <?php echo $row->estado == "SP" ? "selected" : ""; ?> value="SP">São Paulo</option>
                                                        <option <?php echo $row->estado == "SE" ? "selected" : ""; ?> value="SE">Sergipe</option>
                                                        <option <?php echo $row->estado == "TO" ? "selected" : ""; ?> value="TO">Tocantins</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-5">
                                                <div class="form-group">
                                                    <label for="email">E-mail</label>
                                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo $row['email'] ?? ''; ?>" placeholder="email@exemplo.com">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label for="telefoneFixo">Telefone Fixo</label>
                                                    <input type="text" class="form-control" id="telefoneFixo" name="telefoneFixo" value="<?php echo $row['telefoneFixo'] ?? ''; ?>" placeholder="(99) 9999-9999" data-mask="(00)0000-0000">
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label for="celular">Celular</label>
                                                    <input type="text" class="form-control" id="celular" name="celular" value="<?php echo $row['celular'] ?? ''; ?>" placeholder="(99) 99999-9999" data-mask="(00)00000-0000">
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label for="whatsapp">WhatsApp</label>
                                                    <input type="text" class="form-control" id="whatsapp" name="whatsapp" value="<?php echo $row['whatsapp'] ?? ''; ?>" placeholder="(99) 99999-9999" data-mask="(00)00000-0000">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>

                                <div class="card-footer">
                                    <a href="./" class="btn btn-default">Cancelar</a>
                                    <?php if ($isButton) {
                                        echo '<button type="button" onclick="rv_fornecedores()" class="btn btn-primary">Salvar</button>';
                                    }; ?>
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
        function rv_fornecedores() {
            const body = {
                pkId: document.getElementById('pkId').value || '',
                razaoSocial: document.getElementById('razaoSocial').value || '',
                nomeFantasia: document.getElementById('nomeFantasia').value || '',
                endereco: document.getElementById('endereco').value || '',
                numero: document.getElementById('numero').value || '',
                pontoReferencia: document.getElementById('pontoReferencia').value || '',
                categoriaFornecedor: document.getElementById('categoriaFornecedor').value || '',
                cidade: document.getElementById('cidade').value || '',
                estado: document.getElementById('estado').value || '',
                telefoneFixo: document.getElementById('telefoneFixo').value || '',
                celular: document.getElementById('celular').value || '',
                whatsapp: document.getElementById('whatsapp').value || '',
                email: document.getElementById('email').value || ''
            };

            const method = body.pkId ? 'PUT' : 'POST';

            $.ajax({
                url: `https://realvidas.com/area-administrativa/api/rv_fornecedores/`,
                type: method,
                contentType: 'application/json',
                data: JSON.stringify(body),
                success: function(response) {
                    window.location.href = './?msg=<?php echo base64_encode("Registro inserido/atualizado com sucesso!"); ?>&type=<?php echo base64_encode("success"); ?>';
                },
                error: function(xhr, status, error) {
                    window.location.href = './?msg=<?php echo base64_encode("Algo deu errado!"); ?>&type=<?php echo base64_encode("danger"); ?>';
                }
            });
        }
    </script>
</body>

</html>