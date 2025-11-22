<?php
$pageActive = 'Controle Contas';
include('../verifyConnection.php');

$acessoPermissoes = validAcesso(json_decode(base64_decode($_SESSION["acessoPermissoes"]), true), $pageActive);

date_default_timezone_set('America/Sao_Paulo');

if ($acessoPermissoes["isAcesso"] == 0) {
  $type = base64_encode("error");
  $msg  = base64_encode("Usuário sem permissão para acessar este conteúdo!");
  header('Location: ../?msg=' . $msg . '&type=' . $type);
  exit;
}

include('../connectDb.php');

// ITENS POR PÁGINA
$pagina = isset($_GET["pag"]) ? (int)$_GET["pag"] : 1;

// quantidade de resultados por página
$max = 15;
// quantidade de links para esquerda e direita
$qtd = 3;

$inicio = ($pagina - 1) * $max;

$pesquisa = $_GET['id'] !== '' ? "?id=" . $_GET['id'] : '';

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://realvidas.com/area-administrativa/api/rv_controleContas/' . $pesquisa,
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

// echo "<script> console.log(" . json_encode($totalRegistros) . "); </script>";


$totalCount = count($totalRegistros);
$pgs = ceil($totalCount / $max);
$dadosPaginados = array_slice($totalRegistros, $inicio, $max);

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
        $typeDecoded = base64_decode($_GET['type']);
        switch ($typeDecoded) {
          case 'success':
            $title = "Sucesso!";
            $alert = "alert-success";
            break;
          case 'info':
            $title = "Informação!";
            $alert = "alert-info";
            break;
          case 'danger':
            $title = "Erro!";
            $alert = "alert-danger";
            break;
          case 'warning':
            $title = "Aviso!";
            $alert = "alert-warning";
            break;
          default:
            $title = "";
            $alert = "";
        }
      ?>
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
              <div class="alert <?php echo $alert ?> alert-dismissible" style="margin-top:10px">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h5><i class="icon fas fa-ban"></i> <?php echo $title ?></h5>
                <?php echo htmlspecialchars(base64_decode($_GET["msg"])) ?>
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
              <h1>Controle</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="../">Home</a></li>
                <li class="breadcrumb-item active">Controle Contas</li>
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
                  <h3 class="card-title">Controle contas <a href="insert.php" class="btn btn-primary btn-xs"><i class="fas fa-plus"></i> Novo</a></h3>
                  <div class="card-tools">
                    <form method="get">
                      <div class="input-group input-group-sm" style="width:150px;">
                        <input type="text" name="id" class="form-control float-right" placeholder="Pesquisar" value="<?php echo isset($_GET["id"]) ? htmlspecialchars($_GET["id"]) : ''; ?>">
                        <div class="input-group-append">
                          <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body table-responsive p-0" style="height: 65vh;">
                  <table class="table table-head-fixed table-striped table-bordered text-nowrap table-sm">
                    <thead>
                      <tr style="text-align: center; text-transform: uppercase;">
                        <th style="width:25%px">Nome</th>
                        <th style="width:10%">Recebido</th>
                        <th style="width:10%">Vencimento</th>
                        <th style="width:10%">Prev. Pgto.</th>
                        <th style="width:10%">Déb. Auto</th>
                        <th style="width:10%">Pagamento</th>
                        <th style="width:10%">Valor</th>
                        <th style="width:10%;">Status</th>
                        <th style="width:90px"> </th>
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
                            <td><?php echo $row['nome']; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($row['dataRecebido'])); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($row['dataVenc'])); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($row['dataPrevistaPag'])); ?></td>
                            <td><?php echo $row['debAutomatico'] == '1' ? "Sim" : "Não"; ?></td>
                            <td><?php echo  $dataPag ?></td>
                            <td><?php echo $row['valorConta']; ?></td>
                            <td><?php echo  $status; ?></td>
                            <td style="padding-right:.75rem">
                              <a href="insert.php?ref=<?php echo base64_encode($row['pkId']) ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                              <?php if ($dataPag == '-') { ?>
                                <button type="button" onclick="rv_controleContas(<?php echo $row['pkId']; ?>,'true')" class="btn btn-sm btn-outline-success">
                                  <i class="fas fa-thumbs-up"></i>
                                </button>
                                <?php
                                if (validButtonSubmit($acessoPermissoes['isPermissao'], 'Remover')) {
                                  echo "<a href='javascript:void(0)' onclick=\"if(confirm('Deseja realmente excluir esse registro?')) { onDeleteConta(" . $row["pkId"] . "); }\" class='btn btn-sm btn-outline-danger'>
                                        <i class='fas fa-trash'></i>
                                      </a>";
                                }
                                ?>
                              <?php } else { ?>
                                <button type="button" onclick="rv_controleContas(<?php echo $row['pkId']; ?>,'false')" class="btn btn-sm btn-outline-danger">
                                  <i class="fa fa-minus"></i>
                                </button>
                                <button type="button" disabled class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                              <?php } ?>
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
                <div class="card-footer clearfix">
                  <ul class="pagination pagination-sm m-0 float-right">
                    <?php
                    $menos = $pagina - 1;
                    $mais = $pagina + 1;

                    if ($pgs > 1) {
                      if ($menos > 0) {
                        echo "<li class=\"page-item\"><a class=\"page-link\" href=\"?pag=$menos\">&laquo;</a></li>";
                      } else {
                        echo "<li class=\"page-item disabled\"><a class=\"page-link\">&laquo;</a></li>";
                      }

                      $anterior = max(1, $pagina - $qtd);
                      $posterior = min($pgs, $pagina + $qtd);

                      for ($i = $anterior; $i <= $posterior; $i++) {
                        if ($i != $pagina) {
                          echo "<li class=\"page-item\"><a class=\"page-link\" href=\"?pag=$i\">$i</a></li>";
                        } else {
                          echo "<li class=\"page-item active\"><a class=\"page-link\">$i</a></li>";
                        }
                      }

                      if ($mais <= $pgs) {
                        echo "<li class=\"page-item\"><a class=\"page-link\" href=\"?pag=$mais\">&raquo;</a></li>";
                      } else {
                        echo "<li class=\"page-item disabled\"><a class=\"page-link\">&raquo;</a></li>";
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
    function onDeleteConta(id) {
      try {
        // Verifica se o ID é válido
        if (!id || isNaN(id) || parseInt(id) <= 0) {
          console.error('ID inválido para exclusão:', id);
          alert('ID inválido. Não foi possível excluir o registro.');
          return;
        }

        $.ajax({
          url: 'https://realvidas.com/area-administrativa/api/rv_controleContas/',
          type: 'DELETE',
          contentType: 'application/json',
          data: JSON.stringify({
            'id': id
          }),
          success: function(response) {
            // console.log('Registro excluído com sucesso:', response);
            window.location.href = './?msg=<?php echo base64_encode("Registro excluído com sucesso!"); ?>&type=<?php echo base64_encode("success"); ?>';
          },
          error: function(xhr, status, error) {
            // console.error('Erro ao atualizar status:', xhr.responseText);
            window.location.href = './?msg=<?php echo base64_encode("Erro ao excluir registro!"); ?>&type=<?php echo base64_encode("danger"); ?>';
          }
        });
      } catch (err) {
        console.error('Erro inesperado ao tentar excluir:', err);
      }
    }

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