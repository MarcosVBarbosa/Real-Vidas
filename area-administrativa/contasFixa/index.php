<?php
$pageActive = 'Contas';
include('../verifyConnection.php');

$acessoPermissoes = validAcesso(json_decode(base64_decode($_SESSION["acessoPermissoes"]), true), $pageActive);


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

$curl = curl_init();

$pesquisa = $_GET['id'] !== '' ? "?id=" . $_GET['id'] : '';

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://realvidas.com/area-administrativa/api/rv_contasFixa/' . $pesquisa,
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
              <h1>Contas</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="../">Home</a></li>
                <li class="breadcrumb-item active">Contas</li>
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
                  <h3 class="card-title">Lista de contas <a href="insert.php?proximoId=<?php echo base64_encode($totalRegistros[0]['proximoId']); ?>" class="btn btn-primary btn-xs"><i class="fas fa-plus"></i> Novo</a></h3>
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
                      <tr>
                        <th style="width:85px;">Cod</th>
                        <th style="width:200px">Nome</th>
                        <th style="width:50%">Descrição</th>
                        <th style="width:100px">Status</th>
                        <th style="width:90px"> </th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      if (count($totalRegistros) > 0) {
                        foreach ($totalRegistros as $row) { ?>
                          <tr style="text-align: center; text-transform: uppercase;">
                            <td style="text-align: start; padding-top:10px"><?php echo str_pad($row['pkId'], 5, '0', STR_PAD_LEFT); ?></td>
                            <td style="padding-top:10px"><?php echo $row['nome']; ?></td>
                            <td style="padding-top:10px"><?php echo $row['descricao']; ?></td>
                            <td style="text-align: start;">
                              <select name="txtStatus" class="form-control" onchange="setStatusConta(this, '<?php echo $row['pkId']; ?>')">
                                <option value="1" <?php echo $row['status'] == "1" ? "selected" : ""; ?>>Ativo</option>
                                <option value="0" <?php echo $row['status'] == "0" ? "selected" : ""; ?>>Inativo</option>
                              </select>
                            </td>
                            <td style="padding-right:.75rem;padding-top:8px">
                              <a href="insert.php?ref=<?php echo base64_encode($row['pkId']); ?>" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit"></i>
                              </a>
                              <?php
                              if (validButtonSubmit($acessoPermissoes['isPermissao'], 'Remover')) {
                                echo "<a href='javascript:void(0)' onclick=\"if(confirm('Deseja realmente excluir esse registro?')) { onDeleteConta(" . $row["pkId"] . "); }\" class='btn btn-sm btn-outline-danger'>
                                        <i class='fas fa-trash'></i>
                                      </a>";
                              }
                              ?>
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
          url: 'https://realvidas.com/area-administrativa/api/rv_contasFixa/',
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

    function setStatusConta(selectElement, idContasFixa) {

      const novoStatus = selectElement.value;

      if (!idContasFixa || !novoStatus) {
        console.error('ID do Contas Fixa ou novo status não fornecido.');
        return;
      }

      const body = {
        status: novoStatus,
        id: idContasFixa
      };

      console.log(body)
      try {
        $.ajax({
          url: `https://realvidas.com/area-administrativa/api/rv_contasFixa/`,
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