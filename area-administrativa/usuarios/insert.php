<?php

include('../verifyConnection.php');
$pageActive = "Usuários";

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
    $query = "SELECT * FROM rv_administradores WHERE ativo = 'S' AND pkId = " . mysqli_real_escape_string($connecta, base64_decode($_GET["ref"]));
    // echo $query;exit;
    $rs = mysqli_query($connecta, $query);
    if (mysqli_num_rows($rs) > 0) {
        $row = mysqli_fetch_object($rs);
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
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../dist/css/adminlte.min.css">
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
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
                            <h1>Perfis</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../">Home</a></li>
                                <li class="breadcrumb-item active">Perfis</li>
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
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="txtNome">Nome</label>
                                                    <input type="text" class="form-control" required id="txtNome" name="txtNome" value="<?php echo $row->nome; ?>" placeholder="Nome completo">
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-4">
                                                <div class="form-group">
                                                    <label for="txtEmail">E-mail</label>
                                                    <input type="email" class="form-control" required id="txtEmail" name="txtEmail" value="<?php echo $row->email; ?>" placeholder="E-mail de acesso">
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-4">
                                                <div class="form-group">
                                                    <label for="txtSenha">Senha</label>
                                                    <input type="password" class="form-control" name="txtSenha" id="txtSenha" placeholder="Senha">
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-4">
                                                <div class="form-group">
                                                    <label for="txtPerfil">Perfil</label>
                                                    <select required id="txtPerfil" name="txtPerfil" class="form-control">
                                                        <option value="">-- Selecione --</option>
                                                        <?php
                                                        $sql = "
                                                        SELECT pkId, nome
                                                        FROM rv_perfil
                                                        WHERE ativo = 'S'
                                                        AND pkId > 1
                                                        ORDER BY nome
                                                        ";
                                                        $query = mysqli_query($connecta, $sql);
                                                        while ($row2 = mysqli_fetch_object($query)) {
                                                            $selected = "";
                                                            if ($row->fkPerfil == $row2->pkId) {
                                                                $selected = "selected";
                                                            }
                                                            echo "<option $selected value='" . $row2->pkId . "'>" . $row2->nome . "</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
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
    <!-- InputMask -->
    <script src="../dist/js/jquery.mask.min.js"></script>
    <!-- AdminLTE App -->
    <script src="../dist/js/adminlte.min.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="../dist/js/demo.js"></script>
    <script>
        $(function() {
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