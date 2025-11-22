<?php
include('../verifyConnection.php');
$pageActive = "Ordem Serviço";
$acessoPermissoes = validAcesso(json_decode(base64_decode($_SESSION["acessoPermissoes"]), true), $pageActive);
$token = explode('&', base64_decode($_SESSION["token"]));

if ($acessoPermissoes["isAcesso"] == 0) {
  $type = base64_encode("error");
  $msg  = base64_encode("Usuário sem permissão para acessar este conteúdo!");
  header('Location: ../?msg=' . $msg . '&type=' . $type);
  exit;
}
include('../connectDb.php');

$token = explode("&", base64_decode($_SESSION["token"]));
$isVisualizarValores = in_array($token[3], [1]);

$dataHoje = date('Y-m-d');

if (!empty($_GET["s"])) {
  $whereS = " AND (c.razaoSocial LIKE '%" . mysqli_real_escape_string($connecta, $_GET["s"]) . "%' 
                    OR o.solicitante LIKE '%" . mysqli_real_escape_string($connecta, $_GET["s"]) . "%'
                    OR o.nrOrdemServico LIKE '%" . mysqli_real_escape_string($connecta, $_GET["s"]) . "%'
                    OR DATE_FORMAT(o.dataAgendada,'%d/%m/%Y') LIKE '%" . mysqli_real_escape_string($connecta, $_GET["s"]) . "%'
                    OR s.nome LIKE '%" . mysqli_real_escape_string($connecta, $_GET["s"]) . "%'
                    OR c.razaoSocial LIKE '%" . mysqli_real_escape_string($connecta, $_GET["s"]) . "%'
                    OR o.pkId = '" . mysqli_real_escape_string($connecta, $_GET["s"]) . "') ";
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
$rs = mysqli_query($connecta, "SELECT COUNT(o.pkId) totalRegistros FROM rv_ordemServico o INNER JOIN rv_clientes c ON (o.fkCliente = c.pkId) INNER JOIN rv_servicos s ON (s.pkId = o.fkServico) WHERE o.ativo = 'S' $whereS");
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
      if (!empty($alert)) {
        echo $alert;
      }
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
              <h1>Ordem de Serviço </h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="../">Home</a></li>
                <li class="breadcrumb-item active">Ordem de Serviço</li>
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
                  <h3 class="card-title">Lista de cadastros <a href="insert.php" class="btn btn-primary btn-xs"><i class="fas fa-plus"></i> Novo</a></h3>
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
                    <form method="post" action="exp-ordemServico.php">
                      <div class="row">
                        <div class="col-12 col-sm-3">
                          <input type="date" name="dataInicio" class="form-control float-right" value="<?php echo date('Y-m-d', strtotime('-1 monthy')); ?>">
                        </div>
                        <div class="col-12 col-sm-3">
                          <input type="date" name="dataFim" class="form-control float-right" value="<?php echo date('Y-m-d', strtotime('+1 monthy')); ?>">
                        </div>
                        <div class="col-12 col-sm-3">
                          <button type="submit" class="btn btn-success"><i class="fas fa-file-excel"></i> Exportar</button>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body table-responsive p-0" style="height: 700px;">
                  <table class="table table-head-fixed table-striped table-bordered text-nowrap table-sm">
                    <thead>
                      <tr>
                        <th style="width:85px">Cod</th>
                        <th>Fat</th>
                        <th>NF</th>
                        <th>Cliente</th>
                        <th>Paciente</th>
                        <th>Data</th>
                        <th>Vencimento</th>
                        <th>Serviço</th>
                        <th>Valor R$</th>
                        <th>Finalizada</th>
                        <th>Status</th>
                        <th style="width:90px"> </th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      // $query = "
                      // SELECT o.pkId,f.pkId id_faturamento,f.dataVencimento,f.status,f.notaFiscal,o.nrOrdemServico,DATE_FORMAT(o.dataAgendada,'%d/%m/%Y') dataAgendada,c.razaoSocial nomeCliente,s.nome servico,(o.valorRemocao + o.totalHoraParada) valorOS,o.ativo, o.solicitante
                      // FROM rv_ordemServico o
                      // LEFT JOIN rv_faturamentoOS fos ON (fos.fkOrdemServico = o.pkId)
                      // LEFT JOIN rv_faturamento f ON (f.pkId = fos.fkFaturamento AND f.ativo = 'S')
                      // LEFT JOIN rv_clientes c ON (o.fkCliente = c.pkId)
                      // LEFT JOIN rv_servicos s ON (s.pkId = o.fkServico)
                      // WHERE o.ativo = 'S'
                      // $whereS
                      // GROUP BY o.pkId
                      // ORDER BY o.pkId DESC
                      // LIMIT $inicio,$max
                      // ";
                      $query = "
                      SELECT o.pkId, IF(o.finalizada = 1, 'SIM', 'NÃO') finalizada,o.finalizada as finalizada2,
                      (
                        SELECT GROUP_CONCAT(f.pkId SEPARATOR '<br>')
                        FROM rv_faturamentoOS fos
                        JOIN rv_faturamento f ON (f.pkId = fos.fkFaturamento AND f.ativo = 'S' AND fos.fkOrdemServico = o.pkId)
                      ) id_faturamento,
                      (
                        SELECT GROUP_CONCAT(f.dataVencimento SEPARATOR '<br>')
                        FROM rv_faturamentoOS fos
                        JOIN rv_faturamento f ON (f.pkId = fos.fkFaturamento AND f.ativo = 'S' AND fos.fkOrdemServico = o.pkId)
                      ) dataVencimento,
                      (
                        SELECT GROUP_CONCAT(f.status SEPARATOR '<br>')
                        FROM rv_faturamentoOS fos
                        JOIN rv_faturamento f ON (f.pkId = fos.fkFaturamento AND f.ativo = 'S' AND fos.fkOrdemServico = o.pkId)
                      ) status,
                      (
                        SELECT GROUP_CONCAT(f.notaFiscal SEPARATOR '<br>')
                        FROM rv_faturamentoOS fos
                        JOIN rv_faturamento f ON (f.pkId = fos.fkFaturamento AND f.ativo = 'S' AND fos.fkOrdemServico = o.pkId)
                      ) notaFiscal,
                      o.nrOrdemServico,DATE_FORMAT(o.dataAgendada,'%d/%m/%Y') dataAgendada,UPPER(c.razaoSocial) as nomeCliente,s.nome servico,(o.valorRemocao + o.valorPercurso + o.totalHoraParada) valorOS,o.ativo, UPPER(o.paciente) AS paciente 
                      FROM rv_ordemServico o
                      LEFT JOIN rv_clientes c ON (o.fkCliente = c.pkId)
                      LEFT JOIN rv_servicos s ON (s.pkId = o.fkServico)
                      WHERE o.ativo = 'S'
                      $whereS
                      GROUP BY o.pkId
                      ORDER BY o.pkId DESC
                      LIMIT $inicio,$max
                      ";
                      $rs = mysqli_query($connecta, $query);
                      if (mysqli_num_rows($rs) > 0) {
                        while ($row = mysqli_fetch_object($rs)) {

                          $status = $row->status;

                          if ($row->status == 'Em Aberto') {
                            if (strtotime($dataHoje) > strtotime($row->dataVencimento)) {
                              mysqli_query($connecta, "UPDATE rv_faturamento SET status = 'Pendente' WHERE pkId = " . $row->id_faturamento);
                              $row->$status = 'Pendente';
                            }
                          }

                          if ($row->status == 'Pago') {
                            $linhaStatus = 'text-success';
                          } elseif ($row->status == 'Pendente') {
                            $linhaStatus = 'text-danger';
                          } else {
                            $linhaStatus = '';
                          }

                          if ($row->ativo == 'N') {
                            $linha = "background: #f8d7da;color: #dc3545";
                            $row->status = "Cancelado";
                          } else {
                            $linha = "";
                          }

                      ?>
                          <tr style="<?php echo $linha; ?>">
                            <td style="vertical-align: middle;"><?php echo $row->nrOrdemServico; ?></td>
                            <td style="vertical-align: middle;"><?php echo $row->id_faturamento; ?></td>
                            <td style="vertical-align: middle;"><?php echo $row->notaFiscal; ?></td>
                            <td style="vertical-align: middle;"><?php echo $row->nomeCliente; ?></td>
                            <td style="vertical-align: middle;"><?php echo $row->paciente; ?></td>
                            <td style="vertical-align: middle;"><?php echo $row->dataAgendada; ?></td>
                            <td style="vertical-align: middle;"><?php echo $row->dataVencimento != '' ? date('d/m/Y', strtotime($row->dataVencimento)) : ''; ?></td>
                            <td style="vertical-align: middle;"><?php echo $row->servico; ?></td>
                            <td style="vertical-align: middle;"><?php echo $isVisualizarValores ? number_format($row->valorOS, 2, ',', '.') : "*****"; ?></td>
                            <td style="vertical-align: middle;"><?php echo $row->finalizada; ?></td>
                            <td style="vertical-align: middle;" class="<?php echo $linhaStatus; ?>"><?php echo $row->status; ?></td>
                            <td style="vertical-align: middle;padding-right:.75rem">

                              <?php
                              if ($row->finalizada2 == 1) {
                                if ($token[2] == 2 || $token[2] == 1) {
                                  echo  "<a href='insert.php?ref=" . base64_encode($row->pkId) . "' class='btn btn-sm btn-outline-primary'><i class='fas fa-edit'></i></a>";
                                  echo '<a title="Remover" href="javascript:void(0)" onclick="if(confirm(\'Deseja realmente excluir esse registro?\')) { window.location=\'remove.php?ref=' . base64_encode($row->pkId) . '\'}" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></a>';
                                } else {
                                  echo  "<a href='' class='btn btn-sm btn-outline-primary' style='pointer-events: none;opacity: 0.5;'><i class='fas fa-edit'></i></a>";
                                  echo  "<a href='' class='btn btn-sm btn-outline-danger' style='pointer-events: none;opacity: 0.5;'><i class='fas fa-trash'></i></a>";
                                }
                              } else {
                                echo  "<a href='insert.php?ref=" . base64_encode($row->pkId) . "' class='btn btn-sm btn-outline-primary'><i class='fas fa-edit'></i></a>";
                                if ($row->ativo == 'S') {
                                  if (validButtonSubmit($acessoPermissoes['isPermissao'], 'Remover')) {
                                    echo '<a title="Remover" href="javascript:void(0)" onclick="if(confirm(\'Deseja realmente excluir esse registro?\')) { window.location=\'remove.php?ref=' . base64_encode($row->pkId) . '\'}" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></a>';
                                  }
                                } else {
                                  if (validButtonSubmit($acessoPermissoes['isPermissao'], 'Remover')) {
                                    echo '<a title="Remover" href="javascript:void(0)" onclick="if(confirm(\'Deseja realmente excluir esse registro?\')) { window.location=\'remove.php?ref=' . base64_encode($row->pkId) . '\'}" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></a>';
                                  }
                                }
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
</body>

</html>