<?php
$pageActive = "Colaboradores";

include('../verifyConnection.php');
$acessoPermissoes = validAcesso(json_decode(base64_decode($_SESSION["acessoPermissoes"]), true), $pageActive);

if ($acessoPermissoes["isAcesso"] == 0) {
  $type = base64_encode("error");
  $msg  = base64_encode("Usuário sem permissão para acessar este conteúdo!");
  header('Location: ../?msg=' . $msg . '&type=' . $type);
  exit;
}

include('../connectDb.php');

if (!empty($_GET["s"])) {
  $whereS = " AND (c.nome LIKE '%" . mysqli_real_escape_string($connecta, $_GET["s"]) . "%'
                    OR tc.nome = '" . mysqli_real_escape_string($connecta, $_GET["s"]) . "'
                    OR c.pkId = '" . mysqli_real_escape_string($connecta, $_GET["s"]) . "') ";
} else {
  $whereS = "";
}

//ITENS POR PÁGINA
if (!isset($_GET["pag"])) {
  $pagina = 1;
} else {
  $pagina = $_GET["pag"];
}

//quantidade de resultados por página
$max = 15;
//quantidade de links para esquerda e direita
$qtd = 3;

$inicio = $pagina - 1;
$inicio = $max * $inicio;

$rs = mysqli_query($connecta, "SELECT COUNT(c.pkId) totalRegistros FROM rv_colaboradores c LEFT JOIN rv_tipoColaboradores tc ON (c.fkTipoColaborador = tc.pkId) WHERE c.ativo = 'S' $whereS");
$row = mysqli_fetch_object($rs);
$totalRegistros = $row->totalRegistros;

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
              <h1>Colaboradores</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="../">Home</a></li>
                <li class="breadcrumb-item active">Colaboradores</li>
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
                  <h3 class="card-title">Lista de cadastros <a href="insert.php" class="btn btn-primary btn-xs"><i class="fas fa-plus"></i> Novo</a> <a href="exp-colaboradores2.php" class="btn btn-success btn-xs"><i class="fas fa-plus"></i> Exporta</a></h3>
                  <div class="card-tools">
                    <form method="get">
                      <div class="input-group input-group-sm" style="width:150px;">
                        <input type="text" name="s" class="form-control float-right" placeholder="Pesquisar" value="<?php echo $_GET["s"] ?>">
                        <div class="input-group-append">
                          <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>
                <div class="card-header">
                  <div class="container-fluid">
                    <form id="expForm" method="post" action="exp-colaboradores.php" target="_blank">
                      <div class="row">
                        <div class="col-12 col-md-3">
                          <input type="date" name="dataInicio" class="form-control" value="<?php echo date('Y-m-d', strtotime('-1 monthy')); ?>">
                        </div>
                        <div class="col-12 col-md-3">
                          <input type="date" name="dataFim" class="form-control" value="<?php echo date('Y-m-d', strtotime('+1 monthy')); ?>">
                        </div>
                        <div class="col-12 col-md-3">
                          <select name="colaboradores" class="form-control">
                            <option value="Todos"> Todos </option>
                            <?php
                            $query = "SELECT c.pkId,CONCAT_WS(' | ',c.nome,tc.nome) nome
                            FROM rv_colaboradores c
                            LEFT JOIN rv_tipoColaboradores tc ON (c.fkTipoColaborador = tc.pkId)
                            WHERE c.ativo = 'S'
                            ORDER BY c.nome
                            ";
                            $rs = mysqli_query($connecta, $query);
                            if (mysqli_num_rows($rs) > 0) {
                              while ($row = mysqli_fetch_object($rs)) {
                                echo '<option value="' . $row->pkId . '"> ' . $row->nome . ' </option>';
                              }
                            } ?>
                          </select>
                        </div>
                        <input type="hidden" id="tipoExp" name="tipoExp">
                        <div class="col-12 col-md-3">
                          <div class="btn-group">
                            <button type="button" class="btn btn-secondary">Exportar</button>
                            <button type="button" class="btn btn-secondary dropdown-toggle dropdown-icon" data-toggle="dropdown">
                              <span class="sr-only">Lista Suspensa</span>
                            </button>
                            <div class="dropdown-menu" role="menu">
                              <a class="dropdown-item" href="#" onClick="expExcel();"> <i class="fas fa-file-excel"></i> Excel</a>
                              <a class="dropdown-item" href="#" onClick="expPDF();"> <i class="fas fa-file-pdf"></i> PDF</a>
                            </div>
                          </div>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body table-responsive p-0" style="height: 540px;">
                  <table class="table table-head-fixed table-striped table-bordered text-nowrap table-sm">
                    <thead>
                      <tr>
                        <th style="width:85px">Cod</th>
                        <th>Nome</th>
                        <th>Tipo Colaborador</th>
                        <th>Validade Documento</th>
                        <th style="width:110px">Status</th>
                        <th style="width:90px"> </th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $query = "SELECT c.pkId as id, LPAD(c.pkId,5,0) pkId,c.nome,tc.nome tipoColaborador,DATE_FORMAT(c.validadeDoc, '%d/%m/%Y') validadeDoc,c.status
                            FROM rv_colaboradores c
                            LEFT JOIN rv_tipoColaboradores tc ON (c.fkTipoColaborador = tc.pkId)
                            WHERE c.ativo = 'S'
                            $whereS
                            ORDER BY c.nome
                            LIMIT $inicio,$max";
                      $rs = mysqli_query($connecta, $query);
                      if (mysqli_num_rows($rs) > 0) {
                        while ($row = mysqli_fetch_object($rs)) { ?>
                          <tr>
                            <td><?php echo $row->pkId; ?></td>
                            <td><?php echo $row->nome; ?></td>
                            <td><?php echo $row->tipoColaborador; ?></td>
                            <td><?php echo $row->validadeDoc; ?></td>
                            <td>
                              <select name="txtStatus" class="form-control" onchange="setStatus(this, '<?php echo $row->id; ?>')">
                                <option value="A" <?php echo $row->status == "A" ? "selected" : ""; ?>>Ativo</option>
                                <option value="I" <?php echo $row->status == "I" ? "selected" : ""; ?>>Inativo</option>
                              </select>

                            </td>
                            <td style="padding-right:.75rem">
                              <a href="insert.php?ref=<?php echo base64_encode($row->pkId) ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                              <?php
                              if (validButtonSubmit($acessoPermissoes['isPermissao'], 'Remover')) {
                                echo '<a href="javascript:void(0)" onclick="if(confirm(\'Deseja realmente excluir esse registro?\')) { window.location=\'remove.php?ref=' . base64_encode($row->pkId) . '\'; }" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></a>';
                              }
                              ?>
                            </td>
                          </tr>
                      <?php }
                      } ?>
                    </tbody>
                  </table>
                </div>
                <!-- /.card-body -->
                <div class="card-footer clearfix">
                  <ul class="pagination pagination-sm m-0 float-right">

                    <?php
                    // Calculando pagina anterior
                    $menos = $pagina - 1;
                    // Calculando pagina posterior
                    $mais = $pagina + 1;
                    $pgs = ceil($totalRegistros / $max);
                    if ($pgs > 1) {
                      if ($menos > 0) {
                        echo "<li class=\"page-item\"><a class=\"page-link\" href=\"?codigo=" . $_GET['codigo'] . "&pag=$menos\">&laquo;</a></li>";
                      } else {
                        echo "<li class=\"page-item\"><a class=\"page-link\">&laquo;</a></li>";
                      }
                      if (($pagina - $qtd) < 1) $anterior = 1;
                      else $anterior = $pagina - $qtd;

                      if (($pagina + $qtd) > $pgs)
                        $posterior = $pgs;
                      else
                        $posterior = $pagina + $qtd;

                      for ($i = $anterior; $i <= $posterior; $i++)
                        if ($i != $pagina)
                          echo "<li class=\"page-item\"><a class=\"page-link\" href=\"?codigo=" . $_GET['codigo'] . "&pag=" . ($i) . "\">$i</a></li>";
                        else
                          echo "<li class=\"page-item active\"><a class=\"page-link\">" . $i . "</a></li>";

                      if ($mais <= $pgs) {
                    ?>
                        <li class="page-item"><a class="page-link" href="?ref=<?php echo $_GET['ref'] ?>&pag=<? echo $mais; ?>">&raquo;</a></li>
                      <?php
                      } else {
                      ?>
                        <li class="page-item"><a class="page-link">&raquo;</a></li>
                    <?php
                      }
                    }

                    ?>

                  </ul>
                </div>
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

  <script>
    function expExcel() {
      document.getElementById("tipoExp").value = "excel";
      document.getElementById("expForm").submit();
    }

    function expPDF() {
      document.getElementById("tipoExp").value = "pdf";
      document.getElementById("expForm").submit();
    }

    function setStatus(selectElement, idColaborador) {
      const novoStatus = selectElement.value;

      if (!idColaborador || !novoStatus) {
        console.error('ID do colaborador ou novo status não fornecido.');
        return;
      }

      const body = {
        status: novoStatus, // alterna entre "A" e "I"
        id: idColaborador
      };

      try {
        $.ajax({
          url: `https://realvidas.com/area-administrativa/api/rv_colaboradores/`,
          type: 'PATCH',
          contentType: 'application/json',
          data: JSON.stringify(body),
          success: function(response) {
            // console.log('Status atualizado com sucesso:', response);
            window.location.href = './?msg=<?php echo base64_encode("Registro ao atualizado com sucesso!"); ?>&type=<?php echo base64_encode("success"); ?>';
          },
          error: function(xhr, status, error) {
            // console.error('Erro ao atualizar status:', xhr.responseText);
            window.location.href = './?msg=<?php echo base64_encode("Erro na tentativa de atualizar registro!"); ?>&type=<?php echo base64_encode("danger"); ?>';
          }
        });
      } catch (error) {
        console.error('Erro ao obter o novo status:', error);
      }
    }
  </script>

</body>

</html>