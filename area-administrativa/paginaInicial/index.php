<?php
include('../verifyConnection.php');
$pageActive = 'Página Inicial';
$acessoPermissoes = validAcesso(json_decode(base64_decode($_SESSION["acessoPermissoes"]), true), $pageActive);

date_default_timezone_set('America/Sao_Paulo');

if ($acessoPermissoes["isAcesso"] == 0) {
  $type = base64_encode("error");
  $msg  = base64_encode("Usuário sem permissão para acessar este conteúdo!");
  header('Location: ./?msg=' . $msg . '&type=' . $type);
  exit;
}
include('../connectDb.php');

// VERIFICA SE HÁ ATUALIZAÇÃO NA ALÍQUOTA
if ($_POST["idTaxa"]) {

  $taxa = str_replace(',', '.', trim($_POST["taxa"]));

  $sql = "
  UPDATE rv_aliquotas SET
  taxa = '" . mysqli_real_escape_string($connecta, $taxa) . "'
  WHERE pkId = " . mysqli_real_escape_string($connecta, trim($_POST["idTaxa"])) . "
  ";
  $query = mysqli_query($connecta, $sql);
  if ($query) {
    $_GET["type"] = base64_encode("success");
    $_GET["msg"] = base64_encode("Registro salvo com sucesso!");
  }
}

$queryTotal = mysqli_query($connecta, "SELECT COUNT(pkId) totalClientes , (SELECT COUNT(pkId) FROM rv_colaboradores WHERE ativo = 'S') totalColaboradores , (SELECT COUNT(pkId) FROM rv_estabelecimentos WHERE ativo = 'S') totalEstabelecimentos , (SELECT COUNT(pkId) FROM rv_ordemServico WHERE ativo = 'S') totalOS FROM rv_clientes WHERE ativo = 'S'");
$resultTotal = mysqli_fetch_assoc($queryTotal);


$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://realvidas.com/area-administrativa/api/rv_controleContas/',
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

// Decodifica a resposta em array associativo
$response = json_decode($response, true);

if ($response['result']['pkId'] > 0) {
  $totalRegistros[] = $response['result'] ?? [];
} else {
  $totalRegistros = $response['result'] ?? [];
}

?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo $titleSystem ?></title>
  <link rel="icon" type="image/png" href="../imagens/favicon.png" />
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bbootstrap 4 -->
  <link rel="stylesheet" href="../plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="../plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- JQVMap -->
  <link rel="stylesheet" href="../plugins/jqvmap/jqvmap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../dist/css/adminlte.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="../plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="../plugins/daterangepicker/daterangepicker.css">
  <!-- summernote -->
  <link rel="stylesheet" href="../plugins/summernote/summernote-bs4.css">
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
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0 text-dark">Página Inicial</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item active"><a href="./">Home</a></li>
              </ol>
            </div><!-- /.col -->
          </div><!-- /.row -->
        </div><!-- /.container-fluid -->
      </div>
      <!-- /.content-header -->

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          <!-- Small boxes (Stat box) -->
          <div class="row">
            <div class="col-lg-3 col-6">
              <!-- small box -->
              <div class="small-box bg-info">
                <div class="inner">
                  <h3><?php echo $resultTotal["totalClientes"] ?></h3>
                  <p>Clientes</p>
                </div>
                <div class="icon">
                  <i class="ion ion-person-add"></i>
                </div>
                <a href="../clientes/" class="small-box-footer">Ver todos <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
              <!-- small box -->
              <div class="small-box bg-success">
                <div class="inner">
                  <h3><?php echo $resultTotal["totalOS"] ?></h3>
                  <p>Ordem de Serviço</p>
                </div>
                <div class="icon">
                  <i class="ion ion-cash"></i>
                </div>
                <a href="../ordemServico/" class="small-box-footer">Ver todos <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
              <!-- small box -->
              <div class="small-box bg-danger">
                <div class="inner">
                  <h3><?php echo $resultTotal["totalColaboradores"] ?></h3>
                  <p>Colaboradores</p>
                </div>
                <div class="icon">
                  <i class="fa fa-briefcase-medical"></i>
                </div>
                <a href="../colaboradores/" class="small-box-footer">Ver todos <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
              <!-- small box -->
              <div class="small-box bg-warning">
                <div class="inner">
                  <h3><?php echo $resultTotal["totalEstabelecimentos"] ?></h3>
                  <p>Estabelecimentos</p>
                </div>
                <div class="icon">
                  <i class="fa fa-map-marked"></i>
                </div>
                <a href="../estabelecimentos/" class="small-box-footer">Ver todos <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
            <!-- ./col -->
          </div>
          <!-- /.row -->
          <!-- Main row -->
          <div class="row">
            <div class="col-12 col-md-12">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Alíquotas</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body table-responsive p-0" style="height:auto;">
                  <table class="table table-head-fixed table-striped table-bordered text-nowrap table-sm">
                    <thead>
                      <tr>
                        <th>Mês Vigente</th>
                        <th style="width:120px;">Taxa</th>
                        <th style="width:80px"> </th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $query = "
                      SELECT pkId , DATE_FORMAT(mesVigente, '%b/%Y') mesVigente , taxa
                      FROM rv_aliquotas
                      WHERE date_format(mesVigente, '%b/%Y') = date_format(Curdate(), '%b/%Y')
                      ORDER BY mesVigente DESC
                      ";
                      $rs = mysqli_query($connecta, $query);
                      if (mysqli_num_rows($rs) > 0) {
                        $row = mysqli_fetch_object($rs);
                        echo '
                          <form method="post">
                            <tr>
                            <td>' . $row->mesVigente . '</td>
                            <td>
                            <div class="input-group">
                              <input type="hidden" value="' . $row->pkId . '" name="idTaxa">
                              <input required type="text" class="form-control" value="' . number_format($row->taxa, 2, ',', '.') . '" name="taxa">
                              <div class="input-group-append">
                                <span class="input-group-text">%</span>
                              </div>
                            </div>
                            <td style="text-align:center;padding-right:0.3rem">
                              <button type="submit" title="Salvar" class="btn btn-sm btn-outline-primary"><i class="fas fa-save"></i></button>
                            </td>
                            </tr>
                          </form>
                          ';
                      } else {
                        $sql = "
                            INSERT INTO rv_aliquotas (mesVigente, taxa) VALUES (DATE_FORMAT(Curdate(), '%Y-%m-01'), 0.00)
                          ";
                        $query = mysqli_query($connecta, $sql);
                        if ($query) {
                          echo "<script>location.reload();</script>";
                        }
                      } ?>
                    </tbody>
                  </table>
                </div>
                <!-- /.card-body -->
              </div>
              <!-- /.card -->
            </div>
          </div>
          <div class="row">
            <div class="col-12 col-md-12">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Controle</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body table-responsive p-0" style="height:auto;">
                  <table class="table table-head-fixed table-striped table-bordered text-nowrap table-sm">
                    <thead>
                      <tr>
                        <th style="width:30px; text-align: center; vertical-align: middle;">Cod</th>
                        <th style="width:10%; text-align: center; vertical-align: middle;">Conta</th>
                        <th style="width:35%; text-align: center; vertical-align: middle;">Fornecedor</th>
                        <th style="width:50px; text-align: center; vertical-align: middle;">Recebido</th>
                        <th style="width:50px; text-align: center; vertical-align: middle;">Vencimento</th>
                        <th style="width:50px; text-align: center; vertical-align: middle;">Prev. Pgto.</th>
                        <th style="width:50px; text-align: center; vertical-align: middle;">Déb. Auto</th>
                        <th style="width:50px; text-align: center; vertical-align: middle;">Pagamento</th>
                        <th style="width:50px; text-align: center; vertical-align: middle;">Valor</th>
                        <th style="width:10%; text-align: center; vertical-align: middle;">Status</th>
                        <th style="width:90px; text-align: center; vertical-align: middle;"></th>
                      </tr>
                    </thead>

                    <tbody>
                      <?php
                      if (count($totalRegistros) > 0) {
                        foreach ($totalRegistros as $row) {

                          $dataPag = '';
                          $dataHoje = new DateTime();
                          $dataHoje->setTime(0, 0); // Zera a hora

                          $dataPag = !empty($row['dataPag']) ? new DateTime($row['dataPag']) : null;
                          $dataVenc = !empty($row['dataVenc']) ? new DateTime($row['dataVenc']) : null;

                          if ($dataPag) {
                            $status = '<span class="badge badge-success"><i class="fas fa-check-double"></i> Conta paga</span>';
                            $dataPag = date('d/m/Y', strtotime($row['dataPag']));
                          } elseif ($dataVenc && $dataHoje <= $dataVenc) {
                            $status = '<span class="badge badge-info"><i class="fas fa-clock"></i> Conta no prazo</span>';
                            $dataPag = '-';
                          } elseif ($dataVenc && $dataHoje > $dataVenc) {
                            $status = '<span class="badge badge-danger"><i class="fas fa-clock"></i> Atrasada</span>';
                            $dataPag = '-';
                          } else {
                            $status = '<span class="badge badge-secondary">Indefinido</span>';
                            $dataPag = '-';
                          }

                      ?>
                          <tr style="text-align: center; text-transform: uppercase;">
                            <td><?php echo $row['pkIdF']; ?></td>
                            <td><?php echo $row['nome']; ?></td>
                            <td><?php echo $row['nomeFantasia'] ? $row['nomeFantasia'] : " - "; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($row['dataRecebido'])); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($row['dataVenc'])); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($row['dataPrevistaPag'])); ?></td>
                            <td><?php echo $row['debAutomatico'] == '1' ? "Sim" : "Não"; ?></td>
                            <td><?php echo  $dataPag ?></td>
                            <td><?php echo $row['valorConta']; ?></td>
                            <td><?php echo  $status; ?></td>
                            <td style="padding-right:.75rem">
                              <button type="button" onclick="rv_controleContas(<?php echo $row['pkId']; ?>,
                                <?php if ($dataPag == '-') { ?>
                                  'true')"
                                class="btn btn-sm btn-outline-info">
                                <i class='fas fa-thumbs-up'></i>
                              <?php } else { ?>
                                'false')"
                                class="btn btn-sm btn-outline-danger">
                                <i class='fas fa-minus'></i>
                              <?php } ?>
                              </button>
                            </td>
                          </tr>
                      <?php
                        }
                      } else {
                        echo '<tr><td colspan="9" class="text-center">Nenhum registro encontrado!</td></tr>';
                      }
                      ?>
                    </tbody>
                  </table>
                </div>
                <!-- /.card-body -->
              </div>
              <!-- /.card -->
            </div>
          </div>
          <div class="row">
            <!-- Left col -->
            <div class="col-12 col-md">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Próximas manutenções</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body table-responsive p-0" style="height:auto;">
                  <table class="table table-head-fixed table-striped table-bordered text-nowrap table-sm">
                    <thead>
                      <tr>
                        <th>VTR</th>
                        <th>Manutenções</th>
                        <th>KM Atual</th>
                        <th>KM Limite</th>
                        <th>KM Restante</th>
                        <th>Status</th>
                        <th style="width:80px"> </th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $query = "
                        SELECT * , (kmLimite - kmAtual) AS kmRestante FROM (
                          SELECT LPAD(m.pkId,5,0) pkId,m.nome,m.kmLimite,m.realizado,
                          v.nome AS veiculo , 
                          (
                            SELECT MAX(kmFinal)
                            FROM rv_ordemServico
                            WHERE fkVtr = v.pkId
                            AND ativo = 'S'
                          ) AS kmAtual ,
                          (
                            SELECT COUNT(pkId)
                            FROM rv_manutencoes
                            WHERE fkVtr = v.pkId
                            AND ativo = 'S'
                            AND realizado = 'N'
                          ) AS qtde
                          FROM rv_manutencoes AS m
                          INNER JOIN rv_vtr AS v ON (m.fkVtr = v.pkId)
                          WHERE m.ativo = 'S'
                          AND v.ativo = 'S'
                          ANd m.realizado = 'N'
                        ) AS r
                        ORDER BY veiculo , (kmLimite - kmAtual)
                        ";
                      $rs = mysqli_query($connecta, $query);
                      if (mysqli_num_rows($rs) > 0) {
                        $veiculo = '';
                        while ($row = mysqli_fetch_object($rs)) {

                          if ($row->kmRestante >= 500) {
                            $status = '<span class="badge badge-success"> Manutenção em dia </span>';
                          } elseif ($row->kmRestante > 0) {
                            $status = '<span class="badge badge-warning"> Manutenção está próxima </span>';
                          } else {
                            $status = '<span class="badge badge-danger"> Manutenção vencida </span>';
                          }

                          if ($veiculo == '' || $veiculo != $row->veiculo) {
                            echo '
                              <tr>
                              <td rowspan="' . $row->qtde . '" style="vertical-align:middle">' . $row->veiculo . '</td>
                              <td >' . $row->nome . '</td>
                              <td >' . number_format($row->kmAtual, 0, '', '.') . '</td>
                              <td >' . number_format($row->kmLimite, 0, '', '.') . '</td>
                              <td >' . number_format($row->kmRestante, 0, '', '.') . '</td>
                              <td >' . $status . '</td>
                              <td  style="text-align:center">
                                <a title="Confirmar manutenção" href="../manutencoes/realizacao.php?realizado=S&ref=' . base64_encode($row->pkId) . '" class="btn btn-sm btn-outline-info"><i class="fas fa-thumbs-up"></i></a>
                              </td>
                              </tr>
                              ';
                            $veiculo = $row->veiculo;
                          } else {
                            echo '
                              <tr>
                              <td style="padding-left: 0.3rem;">' . $row->nome . '</td>
                              <td>' . number_format($row->kmAtual, 0, '', '.') . '</td>
                              <td>' . number_format($row->kmLimite, 0, '', '.') . '</td>
                              <td>' . number_format($row->kmRestante, 0, '', '.') . '</td>
                              <td>' . $status . '</td>
                              <td style="text-align:center">
                                <a title="Confirmar manutenção" href="../manutencoes/realizacao.php?realizado=S&ref=' . base64_encode($row->pkId) . '" class="btn btn-sm btn-outline-info"><i class="fas fa-thumbs-up"></i></a>
                              </td>
                              </tr>
                              ';
                          }

                      ?>
                      <?php
                        }
                      } else {
                        echo '<tr>
                            <td colspan="7" style="text-align:center">Nenhuma manutenção agendada</td>
                            </tr>
                            ';
                      } ?>
                    </tbody>
                  </table>
                </div>
              </div>
              <!-- /.card -->
            </div>
            <!-- /.Left col -->
          </div>
          <!-- /.row (main row) -->
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
  <!-- jQuery UI 1.11.4 -->
  <script src="../plugins/jquery-ui/jquery-ui.min.js"></script>
  <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
  <script>
    $.widget.bridge('uibutton', $.ui.button)
  </script>

  <!-- Bootstrap 4 -->
  <script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- ChartJS -->
  <script src="../plugins/chart.js/Chart.min.js"></script>
  <!-- Sparkline -->
  <script src="../plugins/sparklines/sparkline.js"></script>
  <!-- JQVMap -->
  <script src="../plugins/jqvmap/jquery.vmap.min.js"></script>
  <script src="../plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
  <!-- jQuery Knob Chart -->
  <script src="../plugins/jquery-knob/jquery.knob.min.js"></script>
  <!-- daterangepicker -->
  <script src="../plugins/moment/moment.min.js"></script>
  <script src="../plugins/daterangepicker/daterangepicker.js"></script>
  <!-- Tempusdominus Bootstrap 4 -->
  <script src="../plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
  <!-- Summernote -->
  <script src="../plugins/summernote/summernote-bs4.min.js"></script>
  <!-- overlayScrollbars -->
  <script src="../plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
  <!-- AdminLTE App -->
  <script src="../dist/js/adminlte.js"></script>
  <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
  <script src="../dist/js/pages/dashboard.js"></script>
  <!-- AdminLTE for demo purposes -->
  <script src="../dist/js/demo.js"></script>

  <script>
    function rv_controleContas(pkId, isDataPag) {
      if (!pkId) {
        console.error('ID não fornecido.');
        return;
      }

      const body = {
        dataPag: isDataPag == 'true' ? new Date().toISOString().split('T')[0] : '',
        id: pkId
      };

      $.ajax({
        url: `https://realvidas.com/area-administrativa/api/rv_controleContas/`,
        type: 'PATCH',
        contentType: 'application/json',
        data: JSON.stringify(body),
        success: function(response) {
          window.location.href = './?msg=<?php echo base64_encode("Registro inserido com sucesso!"); ?>&type=<?php echo base64_encode("success"); ?>';
        },
        error: function(xhr, status, error) {
          window.location.href = './?msg=<?php echo base64_encode("Algo deu errado!"); ?>&type=<?php echo base64_encode("danger"); ?>';
        }
      });
    }
  </script>
</body>

</html>