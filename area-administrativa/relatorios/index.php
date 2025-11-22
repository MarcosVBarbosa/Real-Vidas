<?php
include('../verifyConnection.php');
$pageActive = "Relatórios";
$acessoPermissoes = validAcesso(json_decode(base64_decode($_SESSION["acessoPermissoes"]), true), $pageActive);

if ($acessoPermissoes["isAcesso"] == 0) {
  $type = base64_encode("error");
  $msg  = base64_encode("Usuário sem permissão para acessar este conteúdo!");
header('Location: ../?msg=' . $msg . '&type=' . $type);
  exit;
}
include('../connectDb.php');


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
      <?php
      if (!empty($_GET['msg']) and !empty($_GET['type'])) {
        if (base64_decode($_GET['type']) == 'success') {
          $title = "Sucesso!";
          $alert = "alert-success";
        }
        if (base64_decode($_GET['type']) == 'info') {
          $title = "Informação!";
          $alert = "alert-info";
        }
        if (base64_decode($_GET['type']) == 'danger') {
          $title = "Erro!";
          $alert = "alert-danger";
        }
        if (base64_decode($_GET['type']) == 'warning') {
          $title = "Aviso!";
          $alert = "alert-warning";
        }
      ?>
        <div class="container">
          <div class="row">
            <div class="col-12">
              <div class="alert <?php echo $alert ?> alert-dismissible" style="margin-top:10px">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h5><i class="icon fas fa-ban"></i> <?php echo $title ?></h5>
                <?php echo base64_decode($_GET["msg"]) ?>
              </div>
            </div>
          </div>
        </div>
      <?php } ?>
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1>Relatórios</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="../">Home</a></li>
                <li class="breadcrumb-item active">Relatórios</li>
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
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Lista de cadastros </h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body table-responsive p-0" style="height: 540px;">
                  <table class="table table-head-fixed table-striped table-bordered text-nowrap table-sm">
                    <thead>
                      <tr>
                        <th>Relatórios</th>
                        <th>Clientes</th>
                        <th>Período</th>
                        <th>Tipo</th>
                        <th style="width:90px"> </th>
                      </tr>
                    </thead>
                    <tbody>
                      <form action="exp-relatorio.php" method="post" target="_blank">
                        <tr>
                          <td>Despesas Operacionais</td>
                          <td>
                            <select class="form-control" name="selCliente" required>
                              <option value="Todos"> Todos </option>
                              <?php
                              $sql = "
                           SELECT pkId , razaoSocial
                           FROM rv_clientes
                           WHERE ativo = 'S'
                           ORDER BY razaoSocial
                           ";
                              $rs = mysqli_query($connecta, $sql);
                              while ($row = mysqli_fetch_object($rs)) {
                                echo '<option value="' . base64_encode($row->pkId . '-' . $row->razaoSocial) . '"> ' . $row->razaoSocial . ' </option>';
                              }
                              ?>
                            </select>
                          </td>
                          <td>
                            <select class="form-control" name="selPeriodo" required>
                              <option value="Anual"> Anual </option>
                              <?php
                              for ($i = 2020; $i <= (date("Y") + 1); $i++) {
                                if ($i == date("Y")) {
                                  $selected = "selected";
                                } else {
                                  $selected = "";
                                }
                                echo '<option ' . $selected . ' value="Mensal - ' . $i . '"> Mensal - ' . $i . ' </option>';
                              } ?>
                            </select>
                          </td>
                          <td>
                            <select class="form-control" name="selTipo" required>
                              <option value="Quantidade"> Quantidade </option>
                              <option value="Valores"> Valores R$ </option>
                            </select>
                          </td>
                          <td style="padding-right:.75rem">
                            <input type="hidden" name="tipoRelatorio" value="Despesas_Operacionais">
                            <button type="submit" class="btn btn-success"> <i class="fas fa-file-excel"></i> </button>
                          </td>
                        </tr>
                      </form>
                      <form action="exp-relatorio.php" method="post" target="_blank">
                        <tr>
                          <td>Serviços por Cliente</td>
                          <td>
                            <select class="form-control" name="selCliente" required>
                              <option value="Todos"> Todos </option>
                              <?php
                              $sql = "
                           SELECT pkId , razaoSocial
                           FROM rv_clientes
                           WHERE ativo = 'S'
                           ORDER BY razaoSocial
                           ";
                              $rs = mysqli_query($connecta, $sql);
                              while ($row = mysqli_fetch_object($rs)) {
                                echo '<option value="' . base64_encode($row->pkId . '-' . $row->razaoSocial) . '"> ' . $row->razaoSocial . ' </option>';
                              }
                              ?>
                            </select>
                          </td>
                          <td>
                            <select class="form-control" name="selPeriodo" required>
                              <option value="Anual"> Anual </option>
                              <?php
                              for ($i = 2020; $i <= (date("Y") + 1); $i++) {
                                if ($i == date("Y")) {
                                  $selected = "selected";
                                } else {
                                  $selected = "";
                                }
                                echo '<option ' . $selected . ' value="Mensal - ' . $i . '"> Mensal - ' . $i . ' </option>';
                              } ?>
                            </select>
                          </td>
                          <td>
                            <select class="form-control" name="selTipo" required>
                              <option value="Quantidade"> Quantidade </option>
                              <option value="Valores"> Valores R$ </option>
                            </select>
                          </td>
                          <td style="padding-right:.75rem">
                            <input type="hidden" name="tipoRelatorio" value="Servicos_Clientes">
                            <button type="submit" class="btn btn-success"> <i class="fas fa-file-excel"></i> </button>
                          </td>
                        </tr>
                      </form>
                    </tbody>
                  </table>
                </div>
                <!-- /.card-body -->
              </div>
              <!-- /.card -->
            </div>
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
  <!-- AdminLTE App -->
  <script src="../dist/js/adminlte.min.js"></script>
  <!-- AdminLTE for demo purposes -->
  <script src="../dist/js/demo.js"></script>
</body>

</html>