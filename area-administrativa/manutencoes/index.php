<?php
include('../verifyConnection.php');
$pageActive = 'Manutenções';
$acessoPermissoes = validAcesso(json_decode(base64_decode($_SESSION["acessoPermissoes"]), true), $pageActive);

if ($acessoPermissoes["isAcesso"] == 0) {
  $type = base64_encode("error");
  $msg  = base64_encode("Usuário sem permissão para acessar este conteúdo!");
header('Location: ../?msg=' . $msg . '&type=' . $type);
  exit;
}
include('../connectDb.php');

if (!empty($_GET["s"])) {
  $whereS = " AND (nome LIKE '%" . mysqli_real_escape_string($connecta, $_GET["s"]) . "%' 
                    OR pkId = '" . mysqli_real_escape_string($connecta, $_GET["s"]) . "') ";
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

$rs = mysqli_query($connecta, "SELECT COUNT(pkId) totalRegistros FROM rv_manutencoes WHERE ativo = 'S' $whereS");
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
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <div class="container-fluid">
          <?php
          if (!empty($_GET['msg']) && !empty($_GET['type'])) {
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

            <div class="row">
              <div class="col-12">
                <div class="alert <?php echo $alert ?> alert-dismissible" style="margin-top:10px">
                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                  <h5><i class="icon fas fa-ban"></i> <?php echo $title ?></h5>
                  <?php echo base64_decode($_GET["msg"]) ?>
                </div>
              </div>
            </div>

          <?php } ?>
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1>Manutenções</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="../">Home</a></li>
                <li class="breadcrumb-item active">Manutenções</li>
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
                  <h3 class="card-title">
                    Lista de cadastros
                    <a href="insert.php" class="btn btn-primary btn-xs"><i class="fas fa-plus"></i> Novo</a>
                    <a href="exp-relatorio.php" class="btn btn-success btn-xs" target="_blank"><i class="fas fa-file-excel"></i> Exportar</a>
                  </h3>
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
                <!-- /.card-header -->
                <div class="card-body table-responsive p-0" style="height: 540px;">
                  <table class="table table-head-fixed table-striped table-bordered text-nowrap table-sm">
                    <thead>
                      <tr>
                        <th style="width:85px">Cod</th>
                        <th>Data</th>
                        <th>VTR</th>
                        <th>Manutenções</th>
                        <th>KM Realizado</th>
                        <th>KM Limite</th>
                        <th>R$ Total</th>
                        <th>Status</th>
                        <th style="width:90px"> </th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $query = "
                    SELECT LPAD(m.pkId,5,0) pkId,DATE_FORMAT(m.data , '%d/%m/%Y') AS data,m.nome,m.kmAtual,m.kmLimite,m.realizado,(m.valor + m.valorPeca) valorTotal,
                    v.nome AS veiculo
                    FROM rv_manutencoes AS m
                    INNER JOIN rv_vtr AS v ON (m.fkVtr = v.pkId)
                    WHERE m.ativo = 'S'
                    AND v.ativo = 'S'
                    $whereS
                    ORDER BY m.nome
                    LIMIT $inicio,$max";
                      $rs = mysqli_query($connecta, $query);
                      if (mysqli_num_rows($rs) > 0) {
                        while ($row = mysqli_fetch_object($rs)) { ?>
                          <tr>
                            <td><?php echo $row->pkId; ?></td>
                            <td><?php echo $row->data; ?></td>
                            <td><?php echo $row->veiculo; ?></td>
                            <td><?php echo $row->nome; ?></td>
                            <td><?php echo number_format($row->kmAtual, 0, '', '.'); ?></td>
                            <td><?php echo number_format($row->kmLimite, 0, '', '.'); ?></td>
                            <td>R$ <?php echo number_format($row->valorTotal, 2, ',', '.'); ?></td>
                            <td><?php echo $row->realizado == 'S' ? '<span class="badge badge-success"><i class="fas fa-check-double"></i> Realizada</span>' : '<span class="badge badge-warning"><i class="fas fa-clock"></i> Atrasada</span>'; ?></td>
                            <td style="padding-right:.75rem">
                              <a href="insert.php?ref=<?php echo base64_encode($row->pkId) ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                              <a href="javascript:void(0)" onclick="if(confirm('Deseja realmente excluir esse registro?')) { window.location='remove.php?ref=<?php echo base64_encode($row->pkId); ?>'}" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></a>
                              <?php
                              if ($row->realizado == 'N') { ?>
                                <?php
                                if (validButtonSubmit($acessoPermissoes['isPermissao'], 'Remover')) {
                                  echo '<a  title="Remover"  href="javascript:void(0)" onclick="if(confirm(\'Deseja realmente excluir esse registro?\')) { window.location=\'remove.php?ref=' . base64_encode($row->pkId) . '}" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></a>';
                                }
                                ?>
                              <?php
                              } else { ?>
                                <a title="Desconfirmar manutenção" href="realizacao.php?realizado=N&ref=<?php echo base64_encode($row->pkId) ?>" class="btn btn-sm btn-danger"><i class="fas fa-thumbs-down"></i></a>
                              <?php } ?>
                            </td>
                        <?php
                        }
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
</body>

</html>