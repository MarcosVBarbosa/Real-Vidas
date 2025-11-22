<?php

$pageActive = "Serviços";

include('../verifyConnection.php');
$acessoPermissoes = validAcesso(json_decode(base64_decode($_SESSION["acessoPermissoes"]), true), $pageActive);

if ($acessoPermissoes["isAcesso"] == 0) {
  $type = base64_encode("error");
  $msg  = base64_encode("Usuário sem permissão para acessar este conteúdo!");
  header('Location: ../?msg=' . $msg . '&type=' . $type);
  exit;
}

include('../connectDb.php');
$isButton = false;

if ($_GET["ref"]) {
  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://realvidas.com/area-administrativa/api/rv_tipoServicos/?fkServico=' .  base64_decode($_GET["ref"]),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
  ));

  $response01 = curl_exec($curl);
  curl_close($curl);

  $response01 = json_decode($response01, true);

  if (!empty($response01['result']) && isset($response01['result'][0]['pkId']) && $response01['result'][0]['pkId'] > 0) {
    $rowsTable = $response01['result'];
  } else {
    $rowsTable[] = $response01['result'];
  }

  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://realvidas.com/area-administrativa/api/rv_servicos/?id=' .  base64_decode($_GET["ref"]),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
  ));

  $response02 = curl_exec($curl);

  curl_close($curl);

  $response02 = json_decode($response02, true);
  $row = $response02['result'];
  $method = "PUT";
  $isButton = validButtonSubmit($acessoPermissoes['isPermissao'], 'Editar');
} else {
  $method = "POST";
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
  <style>
    .custom-file-label::after {
      content: "Selecionar"
    }
  </style>
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
              <h1>Serviços</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="../">Home</a></li>
                <li class="breadcrumb-item active">Serviços</li>
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
                <form id="form">
                  <input type="hidden" name="pkId" id="pkId" value="<?php echo $row['pkId']; ?>">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-12 col-md-10">
                        <div class="form-group">
                          <label for="txtNome">Nome</label>
                          <input type="text" class="form-control" required id="nome" name="nome" value="<?php echo $row['nome']; ?>" placeholder="Nome">
                        </div>
                      </div>
                      <div class="col-12 col-sm-2">
                        <div class="form-group">
                          <label for="txtStatus">Status</label>
                          <select required id="ativo" name="ativo" class="form-control">
                            <option value="">-- Selecione --</option>
                            <option <?php echo $row['ativo'] == "S" ? "selected" : ""; ?> value="S">Ativo</option>
                            <option <?php echo $row['ativo'] == "N" ? "selected" : ""; ?> value="N">Inativo</option>
                          </select>
                        </div>
                      </div>
                    </div>
                  </div>
                  <!-- /.card-body -->

                  <div class="card-footer">
                    <input type="hidden" name="pkId" value="<?php echo $_GET["ref"] ?? ''; ?>">
                    <a href="./" class="btn btn-default">Cancelar</a>
                    <?php if ($isButton) {
                      echo '<button type="button" onclick="rv_servico()" class="btn btn-primary">Salvar</button>';
                    } ?>
                  </div>
                </form>
              </div>
              <?php if (base64_decode($_GET["ref"])) { ?>
                <div class="card card-default color-palette-box">
                  <div class="card-header">
                    <h3 class="card-title">Tipos</h3>
                  </div>
                  <div class="card-body">
                    <div class="col-12">
                      <table class="table table-bordered" id="tabelaServicos">
                        <thead>
                          <tr>
                            <th width="80%">Serviço</th>
                            <!-- <th style="width: 15%;">VALORES</th> -->
                            <th style="width: 10%;">
                              <button class="btn btn-success btn-sm w-100" type="button" onclick="AddRow()">Adicionar</button>
                            </th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php

                          if (!empty($rowsTable[0]) && is_array($rowsTable[0])) {
                            $i = 0;
                            foreach ($rowsTable as $row1) {
                              $i++;
                          ?>
                              <tr id='<?php echo $i; ?>'>
                                <td> <input type="text" class="form-control" placeholder="Nome Serviço" id="<?php echo $row1['pkId']; ?>" name="servicos[<?php echo $i; ?>]" value="<?php echo $row1['nome']; ?>"> </td>
                                <!-- <td> <input type="number" step="0.01" class="form-control" placeholder="R$" name="valor[<?php echo $i; ?>]" value="<?php echo $row1['valor']; ?>"> </td> -->
                                <td class="d-flex justify-content-between">
                                  <button class="btn btn-success btn-sm" onclick="UpdateRow('<?php echo $i; ?>')" type="button">
                                    <i class="fas fa-pen"></i>
                                  </button>
                                  <button class="btn btn-danger btn-sm" onclick="DeleteRow('<?php echo $i; ?>')" type="button">
                                    <i class="fas fa-trash"></i>
                                  </button>
                                </td>
                              </tr>
                          <?php
                            }
                          }
                          ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              <?php } ?>
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
  <!-- bs-custom-file-input -->
  <script src="../plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
  <!-- AdminLTE App -->
  <script src="../dist/js/adminlte.min.js"></script>
  <!-- AdminLTE for demo purposes -->
  <script src="../dist/js/demo.js"></script>
  <script>
    $(document).ready(function() {
      bsCustomFileInput.init();
    });

    AddRow = function() {
      var newRow = $("<tr>");
      var cols = "";
      cols += '<td><input type="text" class="form-control" name="servicos[]"></td>';
      //  cols += '<td><input type="text" class="form-control" name="valor[]"></td>';
      cols += '<td class="d-flex justify-content-between">';
      cols += '<button class="btn btn-info btn-sm" onclick="InsertRow(this)" type="button"><i class="fas fa-check"></i></button>';
      cols += '<button class="btn btn-danger btn-sm" onclick="RemoveRow(this)" type="button"><i class="fas fa-trash"></i></button>';
      cols += '</td>';
      newRow.append(cols);
      $("#tabelaServicos").append(newRow);
      return false;
    };

    RemoveRow = function(item) {
      var tr = $(item).closest('tr');
      tr.fadeOut(400, function() {
        tr.remove();
      });
      return false;
    }

    async function UpdateRow(id) {
      try {
        const nomeInput = document.getElementsByName(`servicos[${id}]`)[0];
        // const valorInput = document.getElementsByName(`valor[${id}]`)[0];

        const body = {
          nome: nomeInput.value,
          // valor: valorInput.value,
          id: nomeInput.id
        }

        const response = await $.ajax({
          url: `https://realvidas.com/area-administrativa/api/rv_tipoServicos/`,
          type: 'PATCH',
          contentType: 'application/json',
          data: JSON.stringify(body)
        });

      } catch (err) {
        console.error('Erro no fluxo completo:', err);
        // window.location.href = './?msg=<?php echo base64_encode("Algo deu errado!"); ?>&type=<?php echo base64_encode("danger"); ?>';
      }

    }

    async function InsertRow(button) {
      const tr = $(button).closest('tr');
      const nome = tr.find('input[name="servicos[]"]').val();
      // const valor = tr.find('input[name="valor[]"]').val();

      try {
        const body = {
          nome: nome,
          // valor: valor,
          fkServico: String('<?php echo  base64_decode($_GET["ref"]) ? base64_decode($_GET["ref"]) : 0 ?>')
        };



        const response = await $.ajax({
          url: `https://realvidas.com/area-administrativa/api/rv_tipoServicos/`,
          type: 'POST',
          contentType: 'application/json',
          data: JSON.stringify(body)
        });
        // console.log('Response:', response);
        window.location.href = './insert.php?ref=<?php echo $_GET["ref"]; ?>&msg=<?php echo base64_encode("Registro inserido com sucesso!"); ?>&type=<?php echo base64_encode("success"); ?>';

      } catch (error) {
        console.error('Erro ao inserir:', error);
      }
    }

    async function DeleteRow(id) {
      try {
        const nomeInput = document.getElementsByName(`servicos[${id}]`)[0];
        // const valorInput = document.getElementsByName(`valor[${id}]`)[0];

        const body = {
          id: nomeInput.id
        }

        const response = await $.ajax({
          url: `https://realvidas.com/area-administrativa/api/rv_tipoServicos/`,
          type: 'DELETE',
          contentType: 'application/json',
          data: JSON.stringify(body)
        });

        window.location.href = './insert.php?ref=<?php echo $_GET["ref"]; ?>&msg=<?php echo base64_encode("Registro inserido com sucesso!"); ?>&type=<?php echo base64_encode("success"); ?>';

      } catch (err) {
        console.error('Erro no fluxo completo:', err);
        // window.location.href = './?msg=<?php echo base64_encode("Algo deu errado!"); ?>&type=<?php echo base64_encode("danger"); ?>';
      }
    }


    async function rv_servico() {
      const nome = document.getElementById('nome').value;
      const ativo = document.getElementById('ativo').value;
      const pkIdInput = document.getElementById('pkId').value;

      const bodyService = {
        nome,
        ativo
      };
      if (pkIdInput) bodyService.pkId = pkIdInput;

      const method = pkIdInput ? 'PUT' : 'POST';

      try {
        const responseEstab = await $.ajax({
          url: `https://realvidas.com/area-administrativa/api/rv_servicos/`,
          type: method,
          contentType: 'application/json',
          data: JSON.stringify(bodyService)
        });

        // const pkId = pkIdInput || responseEstab.pkId;

        // await $.ajax({
        //   url: `https://realvidas.com/area-administrativa/api/rv_tipoServicos/`,
        //   type: 'DELETE',
        //   contentType: 'application/json',
        //   data: JSON.stringify({
        //     'fkServico': pkId
        //   })
        // });

        // const tiposServicos = [];
        // const responseTipoServicos = [];

        // const nomeEls = document.getElementsByName('nome[]');
        // const valorEls = document.getElementsByName('valor[]');

        // for (let i = 0; i < nomeEls.length; i++) {
        //   tiposServicos.push({
        //     fkServico: pkId,
        //     valor: valorEls[i].value || '0',
        //     nome: nomeEls[i].value
        //   });
        // }

        // if (tiposServicos.length > 0) {
        //   for (let i = 0; i < tiposServicos.length; i++) {
        //     const body = tiposServicos[i];
        //     await $.ajax({
        //       url: `https://realvidas.com/area-administrativa/api/rv_tipoServicos/`,
        //       type: 'POST',
        //       contentType: 'application/json',
        //       data: JSON.stringify(body)
        //     })
        //   }
        // }

        window.location.href = './?msg=<?php echo base64_encode("Registro inserido com sucesso!"); ?>&type=<?php echo base64_encode("success"); ?>';

      } catch (err) {
        console.error('Erro no fluxo completo:', err);
        // window.location.href = './?msg=<?php echo base64_encode("Algo deu errado!"); ?>&type=<?php echo base64_encode("danger"); ?>';
      }
    }
  </script>
</body>

</html>