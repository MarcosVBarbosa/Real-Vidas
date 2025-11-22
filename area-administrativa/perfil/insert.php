<?php

include('../verifyConnection.php');
$pageActive = 'Perfil';
$acessoPermissoes = validAcesso(json_decode(base64_decode($_SESSION["acessoPermissoes"]), true), $pageActive);

if ($acessoPermissoes["isAcesso"] == 0) {
    $type = base64_encode("error");
    $msg  = base64_encode("Usuário sem permissão para acessar este conteúdo!");
    header('Location: ../?msg=' . $msg . '&type=' . $type);
    exit;
}
include('../connectDb.php');

$isButton = false;
if (!empty($_GET["ref"])) {

    $query = "SELECT rPf.pkId,rPf.permissao_valores,rPf.nome, JSON_ARRAYAGG(rAc.acesso) as acessos FROM rv_perfil rPf JOIN rv_acesso rAc ON rAc.fkPerfil = rPf.pkId WHERE rPf.ativo = 'S' AND rPf.pkId = " . mysqli_real_escape_string($connecta, base64_decode($_GET["ref"]));

    $rs = mysqli_query($connecta, $query);
    if (mysqli_num_rows($rs) > 0) {
        $row = mysqli_fetch_object($rs);
        $row->acessos = json_decode($row->acessos, true);
    } else {
        $type = base64_encode('danger');
        $msg = base64_encode('Registro não encontrado!');
        header('Location: ./?msg=' . $msg . '&type=' . $type);
        exit;
    }

    $isButton = validButtonSubmit($acessoPermissoes['isPermissao'], 'Editar');
} else {
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
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
    <!-- daterange picker -->
    <link rel="stylesheet" href="../plugins/daterangepicker/daterangepicker.css">
    <!-- iCheck for checkboxes and radio inputs -->
    <link rel="stylesheet" href="../plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Bootstrap Color Picker -->
    <link rel="stylesheet" href="../plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="../plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <!-- Select2 -->
    <link rel="stylesheet" href="../plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="../plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
    <!-- Bootstrap4 Duallistbox -->
    <link rel="stylesheet" href="../plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css">
    <!-- BS Stepper -->
    <link rel="stylesheet" href="../plugins/bs-stepper/css/bs-stepper.min.css">
    <!-- dropzonejs -->
    <link rel="stylesheet" href="../plugins/dropzone/min/dropzone.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../dist/css/adminlte.min.css?v=3.2.0">
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
                            <h1>Perfil</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../">Home</a></li>
                                <li class="breadcrumb-item active">Perfil</li>
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
                                    <h3 class="card-title">Informações</h3>
                                </div>
                                <!-- /.card-header -->
                                <!-- form start -->
                                <form method="post" action="save.php">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12 d-flex align-items-center">
                                                <div class="form-group col">
                                                    <label for="txtNome">Nome</label>
                                                    <input type="text" class="form-control" required id="txtNome" name="txtNome" value="<?php echo $row->nome; ?>" placeholder="Nome completo">
                                                </div>
                                                <div class="form-group col-2 mt-4">
                                                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                                        <input type="checkbox" class="custom-control-input" id="permissao_valores" name="permissao_valores" value="1" <?php echo $row->permissao_valores == 1 ? 'checked' : '' ?>>
                                                        <label class="custom-control-label" for="permissao_valores">Visualizar Valores</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                            if (empty($_GET["ref"])) {
                                                $options = [
                                                    "Página Inicial",
                                                    "Ordem Serviço",
                                                    "Faturamento",
                                                    "Clientes",
                                                    "Colaboradores",
                                                    "Estabelecimentos",
                                                    "Estoque/Custos",
                                                    "Maletas de Medicação",
                                                    "Manutenções",
                                                    "Contas",
                                                    "Controle Contas",
                                                    "Fornecedores",
                                                    "Categorias Fornecedores",
                                                    "Serviços",
                                                    "vtr",
                                                    "Usuários",
                                                    "Perfil",
                                                    "Relatórios"
                                                ];
                                                echo '<div class="col-12">';
                                                echo '    <div class="form-group">';
                                                echo '        <label>Acesso</label>';
                                                echo '        <select class="select2bs4" multiple="multiple" name="multAcessos[]" value="' . htmlspecialchars($row->permissoes) . '" data-placeholder="Selecione" style="width: 100%;">';

                                                foreach ($options as $option) {
                                                    $selected = in_array($option, $row->acessos) ? 'selected' : '';
                                                    echo "<option value='" . htmlspecialchars($option) . "' $selected>" . htmlspecialchars($option) . "</option>";
                                                }

                                                echo '        </select>';
                                                echo '    </div>';
                                                echo '</div>';
                                            }
                                            ?>

                                        </div>
                                    </div>
                                    <!-- /.card-body -->

                                    <div class="card-footer">
                                        <input type="hidden" name="pkId" value="<?php echo $_GET["ref"]; ?>">
                                        <a href="./" class="btn btn-default">Cancelar</a>
                                        <?php if ($isButton) {
                                            echo '<button type="submit" class="btn btn-primary">Salvar</button>';
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
    <!-- Select2 -->
    <script src="../plugins/select2/js/select2.full.min.js"></script>
    <!-- Bootstrap4 Duallistbox -->
    <script src="../plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js"></script>
    <!-- InputMask -->
    <script src="../plugins/moment/moment.min.js"></script>
    <script src="../plugins/inputmask/jquery.inputmask.min.js"></script>
    <!-- date-range-picker -->
    <script src="../plugins/daterangepicker/daterangepicker.js"></script>
    <!-- bootstrap color picker -->
    <script src="../plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="../plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
    <!-- Bootstrap Switch -->
    <script src="../plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
    <!-- BS-Stepper -->
    <script src="../plugins/bs-stepper/js/bs-stepper.min.js"></script>
    <!-- dropzonejs -->
    <script src="../plugins/dropzone/min/dropzone.min.js"></script>
    <!-- AdminLTE App -->
    <script src="../dist/js/adminlte.min.js?v=3.2.0"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="../dist/js/demo.js"></script>
    <!-- Page specific script -->
    <script>
        $(function() {

            $('.select2').select2();

            //Initialize Select2 Elements
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            })

            var options = {
                onKeyPress: function(cpf, ev, el, op) {
                    var masks = ['000.000.000-000', '00.000.000/0000-00'];
                    $('#txtCNPJ').mask((cpf.length > 14) ? masks[1] : masks[0], op);
                }
            }

            $('#txtCNPJ').val().length > 11 ? $('#txtCNPJ').mask('00.000.000/0000-00', options) : $('#txtCNPJ').mask('000.000.000-00#', options);

        });
    </script>
</body>

</html>