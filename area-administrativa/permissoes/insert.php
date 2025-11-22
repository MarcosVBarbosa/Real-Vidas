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

    $sql = "
        SELECT 
            LPAD(rv_acesso.pkId,5,0) pkId,rv_acesso.acesso,rv_acesso.permissoes
        FROM 
            rv_acesso
        WHERE 
            rv_acesso.pkId = " . mysqli_real_escape_string($connecta, base64_decode($_GET["ref"]));

    $rs = mysqli_query($connecta,  $sql);

    if (mysqli_num_rows($rs) > 0) {
        $row = mysqli_fetch_object($rs);
        $row->permissoes = json_decode($row->permissoes, true);
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
                            <h1>Permissões</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../">Home</a></li>
                                <li class="breadcrumb-item"><a href="../perfil">Perfil</a></li>
                                <li class="breadcrumb-item"><a href="./?ref=<?php echo $_GET["pkPerfil"]; ?>">Permissões</a></li>
                                <li class="breadcrumb-item active">Novo</li>
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
                                <form method="post" action="save.php" id="acessoForm">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>Acesso</label>
                                                    <select class="form-control" name="acesso" data-placeholder="Selecione" style="width: 100%;">
                                                        <option value="">-- Selecione --</option>
                                                        <option value="Página Inicial" <?php echo "Página Inicial" == $row->acesso ? "selected" : ""; ?>>Página Inicial</option>
                                                        <option value="Ordem Serviço" <?php echo "Ordem Serviço" == $row->acesso ? "selected" : ""; ?>>Ordem Serviço</option>
                                                        <option value="Faturamento" <?php echo "Faturamento" == $row->acesso ? "selected" : ""; ?>>Faturamento</option>
                                                        <option value="Clientes" <?php echo "Clientes" == $row->acesso ? "selected" : ""; ?>>Clientes</option>
                                                        <option value="Colaboradores" <?php echo "Colaboradores" == $row->acesso ? "selected" : ""; ?>>Colaboradores</option>
                                                        <option value="Estabelecimentos" <?php echo "Estabelecimentos" == $row->acesso ? "selected" : ""; ?>>Estoque/Custos</option>
                                                        <option value="Estoque/Custos" <?php echo "Estoque/Custos" == $row->acesso ? "selected" : ""; ?>>Estabelecimentos</option>
                                                        <option value="Maletas de Medicação" <?php echo "Maletas de Medicação" == $row->acesso ? "selected" : ""; ?>>Maletas de Medicação</option>
                                                        <option value="Manutenções" <?php echo "Manutenções" == $row->acesso ? "selected" : ""; ?>>Manutenções</option>
                                                        <option value="Contas" <?php echo "Contas" == $row->acesso ? "selected" : ""; ?>>Contas</option>
                                                        <option value="Controle Contas" <?php echo "Controle Contas" == $row->acesso ? "selected" : ""; ?>>Controle Contas</option>
                                                        <option value="Fornecedores" <?php echo "Fornecedores" == $row->acesso ? "selected" : ""; ?>>Fornecedores</option>
                                                        <option value="Categorias Fornecedores" <?php echo "Categorias Fornecedores" == $row->acesso ? "selected" : ""; ?>>Categorias Fornecedores</option>
                                                        <option value="Serviços" <?php echo "Serviços" == $row->acesso ? "selected" : ""; ?>>Serviços</option>
                                                        <option value="vtr" <?php echo "vtr" == $row->acesso ? "selected" : ""; ?>>vtr</option>
                                                        <option value="Usuários" <?php echo "Usuários" == $row->acesso ? "selected" : ""; ?>>Usuários</option>
                                                        <option value="Perfil" <?php echo "Perfil" == $row->acesso ? "selected" : ""; ?>>Perfil</option>
                                                        <option value="Relatórios" <?php echo "Relatórios" == $row->acesso ? "selected" : ""; ?>>Relatórios</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>Permissões</label>
                                                    <label for=""><?php echo $row->permissoes; ?></label>
                                                    <select class="select2bs4" multiple="multiple" name="multPermissoes[]" data-placeholder="Selecione" style="width: 100%;">
                                                        <option <?php echo in_array('Salvar', $row->permissoes) ? 'selected' : ''; ?>>Salvar</option>
                                                        <option <?php echo in_array('Editar', $row->permissoes) ? 'selected' : ''; ?>>Editar</option>
                                                        <option <?php echo in_array('Remover', $row->permissoes) ? 'selected' : ''; ?>>Remover</option>
                                                    </select>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.card-body -->

                                    <div class="card-footer">
                                        <input type="hidden" name="pkId" value="<?php echo $_GET["ref"]; ?>">
                                        <input type="hidden" name="pkPerfil" value="<?php echo $_GET["pkPerfil"]; ?>">
                                        <a href="./?ref=<?php echo $_GET["pkPerfil"]; ?>" class="btn btn-default">Cancelar</a>
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