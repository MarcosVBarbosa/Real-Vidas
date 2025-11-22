<?php

include('../verifyConnection.php');
$pageActive = "Maletas de Medicação";
$acessoPermissoes = validAcesso(json_decode(base64_decode($_SESSION["acessoPermissoes"]), true), $pageActive);

if ($acessoPermissoes["isAcesso"] == 0) {
  $type = base64_encode("error");
  $msg  = base64_encode("Usuário sem permissão para acessar este conteúdo!");
header('Location: ../?msg=' . $msg . '&type=' . $type);
  exit;
}

include('../connectDb.php');

if (!empty($_GET["ref"])) {
  $query = "SELECT m.*
    FROM rv_maletas AS m
    WHERE m.ativo = 'S' 
    AND m.pkId = " . mysqli_real_escape_string($connecta, base64_decode($_GET["ref"]));
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

$listaDespesas = '<option value=""> -- Selecione -- </option>';
$sql0 = "SELECT pkId,nome
FROM rv_despesas
WHERE ativo = 'S'
ORDER BY nome";
$rs0 = mysqli_query($connecta, $sql0);
while ($row1 = mysqli_fetch_object($rs0)) {
  $listaDespesas .= '<option value="' . $row1->pkId . '">' . $row1->nome . '</option>';
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
  <!-- Select2 -->
  <link rel="stylesheet" href="../plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="../plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
  <!-- summernote -->
  <link rel="stylesheet" href="../plugins/summernote/summernote-bs4.css">
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
        <div class="container-fluid">
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
              <h1>Maletas de Medicações</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="../">Home</a></li>
                <li class="breadcrumb-item active">Maletas de Medicações</li>
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
                <form method="post" action="save.php" enctype="multipart/form-data">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="txtNome">Nome</label>
                          <input type="text" class="form-control" id="txtNome" name="txtNome" value="<?php echo $row->nome; ?>" required>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="txtNome">VTR</label>
                          <select class="form-control" id="txtVtr" name="txtVtr" required>
                            <option value="">-- Selecione --</option>
                            <?php
                            $sql0 = "
                              SELECT *
                              FROM rv_vtr
                              WHERE ativo = 'S'
                              ";
                            $query0 = mysqli_query($connecta, $sql0);
                            while ($row0 = mysqli_fetch_object($query0)) {
                              $selected = "";
                              if ($row0->pkId == $row->fkVtr) {
                                $selected = "selected";
                              }
                              echo '<option ' . $selected . ' value="' . $row0->pkId . '">' . $row0->nome . '</option>';
                            }
                            ?>
                          </select>
                        </div>
                      </div>
                      <!-- FIM DA TABELA ORIGEM -->
                    </div>
                    <!-- INICIO DA TABELA PARA INSERIR PRODUTOS -->
                    <div class="row">
                      <div class="col-12">
                        <div class="table-responsive">
                          <table class="table table-bordered" id="tabelaDespesas">
                            <thead>
                              <tr>
                                <th width="40%">Medicação</th>
                                <th>Qtde</th>
                                <th>Validade</th>
                                <th width="20%">Lote</th>
                                <th> <button class="btn btn-info btn-sm" type="button" onclick="AddRow()"><i class="fas fa-plus"> </i> </button> </th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php
                              if (!empty($_GET["ref"])) {
                                $sql1 = "SELECT * FROM rv_maletasDespesas WHERE fkMaleta = " . base64_decode($_GET["ref"]);
                                $rs1 = mysqli_query($connecta, $sql1);
                                $contador = 1;
                                while ($row1 = mysqli_fetch_object($rs1)) {
                              ?>
                                  <tr>
                                    <td>
                                      <select class="form-control select2" name="despesa[]">
                                        <option value=""> -- Selecione -- </option>
                                        <?php
                                        $sql0 = "SELECT pkId,nome FROM rv_despesas WHERE ativo = 'S' ORDER BY nome";
                                        $rs0 = mysqli_query($connecta, $sql0);
                                        while ($row2 = mysqli_fetch_object($rs0)) {
                                          if ($row2->pkId == $row1->fkDespesa) {
                                            $selected = "selected";
                                          } else {
                                            $selected = "";
                                          }
                                          echo '<option ' . $selected . ' value="' . $row2->pkId . '">' . $row2->nome . '</option>';
                                        } ?>
                                      </select>
                                    <td>
                                      <input type="text" class="form-control" name="qtde[]" value="<?php echo $row1->qtde ?>">
                                    </td>
                                    <td>
                                      <input type="month" class="form-control" name="dataValidade[]" value="<?php echo $row1->dataValidade ?>">
                                    </td>
                                    <td>
                                      <input type="text" class="form-control" name="lote[]" value="<?php echo $row1->lote ?>">
                                    </td>
                                    <td><button class="btn btn-danger btn-sm" onclick="RemoveRow(this)" type="button"><i class="fas fa-trash"></i></button></td>
                                  </tr>
                                <?php
                                }
                              } else { ?>
                                <tr>
                                  <td>
                                    <select class="form-control" name="despesa[]">
                                      <?php echo $listaDespesas; ?>
                                    </select>
                                  </td>
                                  <td>
                                    <input type="text" class="form-control" name="qtde[]">
                                  </td>
                                  <td>
                                    <input type="month" class="form-control" name="dataValidade[]">
                                  </td>
                                  <td>
                                    <input type="text" class="form-control" name="lote[]">
                                  </td>
                                  <td><button class="btn btn-danger btn-sm" onclick="RemoveRow(this)" type="button"><i class="fas fa-trash"></i></button></td>
                                </tr>
                              <?php } ?>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                    <!-- FIM DA TABELA PARA INSERIR PRODUTOS -->
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
  <!-- bs-custom-file-input -->
  <script src="../plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
  <!-- Summernote -->
  <script src="../plugins/summernote/summernote-bs4.min.js"></script>
  <!-- AdminLTE App -->
  <script src="../dist/js/adminlte.min.js"></script>
  <!-- AdminLTE for demo purposes -->
  <script src="../dist/js/demo.js"></script>
  <script>
    $(function() {

      //Initialize Select2 Elements
      $('.select2').select2();

      AddRow = function() {
        var newRow = $("<tr>");
        var cols = "";
        cols += '<td>';
        cols += '<select class="form-control select2" name="despesa[]">';
        cols += '<?php echo $listaDespesas ?>';
        cols += '</select>';
        cols += '</td>';
        cols += '<td><input type="text" class="form-control" name="qtde[]"></td>';
        cols += '<td><input type="month" class="form-control" name="dataValidade[]"></td>';
        cols += '<td><input type="text" class="form-control" name="lote[]"></td>';
        cols += '<td>';
        cols += '<button class="btn btn-danger btn-sm" onclick="RemoveRow(this)" type="button"><i class="fas fa-trash"></i></button>';
        cols += '</td>';
        newRow.append(cols);
        $("#tabelaDespesas").prepend(newRow);
        return false;
      };
      RemoveRow = function(item) {
        var tr = $(item).closest('tr');
        tr.fadeOut(400, function() {
          tr.remove();
        });
        return false;
      }

    });
  </script>
</body>

</html>