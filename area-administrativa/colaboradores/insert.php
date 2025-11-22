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
$isButton = false;

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://realvidas.com/area-administrativa/api/rv_servicos/',
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
$dadosServicos = $response01['result'];

foreach ($dadosServicos as $row2) {
  $listaServicos .=  '<option value="' . $row2['pkId'] . '">' . $row2['pkId'] . ' - ' . $row2['nome'] . '</option>';
}

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://realvidas.com/area-administrativa/api/rv_tipoServicos/',
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

if (!empty($response02['result']) && isset($response02['result'][0]['pkId']) && $response02['result'][0]['pkId'] > 0) {
  $dadosTipoServicos  = $response02['result'];
} else {
  $dadosTipoServicos[] = $response02['result'];
}

if (!empty($_GET["ref"])) {
  $curl = curl_init();

  $_SESSION['fkColaborador'] = base64_decode($_GET["ref"]);

  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://realvidas.com/area-administrativa/api/rv_colaboradoresServico/?fkColaborador=' .  base64_decode($_GET["ref"]),
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
    $rowsColaboradoresServico = $response01['result'];
  } else {
    $rowsColaboradoresServico[] = $response01['result'];
  }

  $query = "
  SELECT * 
  FROM rv_colaboradores 
  WHERE ativo = 'S' 
  AND pkId = " . mysqli_real_escape_string($connecta, base64_decode($_GET["ref"]));
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
                      <div class="col-12 col-sm-6">
                        <div class="form-group">
                          <label for="txtNome">Nome</label>
                          <input type="text" class="form-control" required id="txtNome" name="txtNome" value="<?php echo $row->nome; ?>" placeholder="Nome">
                        </div>
                      </div>
                      <div class="col-12 col-sm-3">
                        <div class="form-group">
                          <label for="txtTipoColaborador">Tipo Colaborador</label>
                          <select required id="txtTipoColaborador" name="txtTipoColaborador" class="form-control">
                            <option value="">-- Selecione --</option>
                            <?php
                            $sql0 = "SELECT * FROM rv_tipoColaboradores WHERE ativo = 'S' ORDER BY nome";
                            $rs0 = mysqli_query($connecta, $sql0);
                            if (mysqli_num_rows($rs0) > 0) {
                              while ($row0 = mysqli_fetch_object($rs0)) {
                                if ($row->fkTipoColaborador == $row0->pkId) {
                                  $selected = "selected";
                                } else {
                                  $selected = "";
                                }
                                echo '<option ' . $selected . ' value="' . $row0->pkId . '">' . $row0->nome . '</option>';
                              }
                            }
                            ?>
                          </select>
                        </div>
                      </div>
                      <div class="col-12 col-sm-3">
                        <div class="form-group">
                          <label for="txtStatus">Status</label>
                          <select required id="txtStatus" name="txtStatus" class="form-control">
                            <option <?php echo $row->status == "A" ? "selected" : ""; ?> value="A">Ativo</option>
                            <option <?php echo $row->status == "I" ? "selected" : ""; ?> value="I">Inativo</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-12 col-sm-6">
                        <div class="form-group">
                          <label for="txtRG">RG</label>
                          <input type="text" class="form-control" id="txtRG" name="txtRG" value="<?php echo $row->rg; ?>" placeholder="RG" data-mask="00.000.000-A">
                        </div>
                      </div>
                      <div class="col-12 col-sm-6">
                        <div class="form-group">
                          <label for="txtDataNascimento">Data Nascimento</label>
                          <input type="date" class="form-control" id="txtDataNascimento" name="txtDataNascimento" value="<?php echo $row->dataNascimento; ?>" placeholder="Data Nascimento">
                        </div>
                      </div>
                      <div class="col-12 col-sm-6">
                        <div class="form-group">
                          <label for="txtCPF">CPF</label>
                          <input type="text" class="form-control" required id="txtCPF" name="txtCPF" value="<?php echo $row->cpf; ?>" placeholder="CPF" data-mask="000.000.000-00">
                        </div>
                      </div>
                      <div class="col-12 col-sm-6">
                        <div class="form-group">
                          <label for="txtPix">Pix</label>
                          <input type="text" class="form-control" id="txtPix" name="txtPix" value="<?php echo $row->pix; ?>" placeholder="Pix">
                        </div>
                      </div>
                      <div class="col-12 col-sm-3">
                        <div class="form-group">
                          <label for="txtNrDoc">Nr Documento</label>
                          <input type="text" class="form-control" required id="txtNrDoc" name="txtNrDoc" value="<?php echo $row->nrDoc; ?>" placeholder="Número do documento">
                        </div>
                      </div>
                      <div class="col-12 col-sm-3">
                        <div class="form-group">
                          <label for="txtTipoDoc">Tipo Documento</label>
                          <select required id="txtTipoDoc" name="txtTipoDoc" class="form-control">
                            <option value="">-- Selecione --</option>
                            <option <?php echo $row->tipoDoc == "CRM" ? "selected" : ""; ?> value="CRM">CRM</option>
                            <option <?php echo $row->tipoDoc == "Coren" ? "selected" : ""; ?> value="Coren">Coren</option>
                            <option <?php echo $row->tipoDoc == "CNH" ? "selected" : ""; ?> value="CNH">CNH</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-12 col-sm-3">
                        <div class="form-group">
                          <label for="txtNrDoc">UF Documento</label>
                          <input type="text" class="form-control text-uppercase" required id="txtUFDoc" name="txtUFDoc" value="<?php echo $row->ufDoc; ?>" placeholder="UF do documento" maxlength="2">
                        </div>
                      </div>
                      <div class="col-12 col-sm-3">
                        <div class="form-group">
                          <label for="txtValidadeDoc">Validade Documento</label>
                          <input type="date" class="form-control" id="txtValidadeDoc" name="txtValidadeDoc" value="<?php echo $row->validadeDoc; ?>" placeholder="Validade do documento">
                        </div>
                      </div>
                      <div class="col-12 col-sm-6">
                        <div class="form-group">
                          <label for="txtBanco">Banco</label>
                          <input type="text" class="form-control" required id="txtBanco" name="txtBanco" value="<?php echo $row->banco; ?>" placeholder="Nome do Banco">
                        </div>
                      </div>
                      <div class="col-12 col-sm-6">
                        <div class="form-group">
                          <label for="txtTipoConta">Tipo de Conta</label>
                          <input type="text" class="form-control" required id="txtTipoConta" name="txtTipoConta" value="<?php echo $row->tipoConta; ?>" placeholder="Tipo de conta">
                        </div>
                      </div>
                      <div class="col-12 col-sm-6">
                        <div class="form-group">
                          <label for="txtNrAgencia">Nr Agência</label>
                          <input type="text" class="form-control" required id="txtNrAgencia" name="txtNrAgencia" value="<?php echo $row->nrAgencia; ?>" placeholder="Número da agência">
                        </div>
                      </div>
                      <div class="col-12 col-sm-6">
                        <div class="form-group">
                          <label for="txtNrConta">Nr Conta</label>
                          <input type="text" class="form-control" required id="txtNrConta" name="txtNrConta" value="<?php echo $row->nrConta; ?>" placeholder="Nr da Conta">
                        </div>
                      </div>
                      <div class="col-12 col-sm-6">
                        <div class="form-group">
                          <label for="txtEndereco">Endereço</label>
                          <input type="text" required id="txtEndereco" name="txtEndereco" value="<?php echo $row->endereco; ?>" placeholder="Logradouro" class="form-control">
                        </div>
                      </div>
                      <div class="col-12 col-sm-6">
                        <div class="form-group">
                          <label for="txtComplemento">Complemento</label>
                          <input type="text" class="form-control" id="txtComplemento" name="txtComplemento" value="<?php echo $row->complemento; ?>" placeholder="Complemento">
                        </div>
                      </div>
                      <div class="col-12 col-sm-6">
                        <div class="form-group">
                          <label for="txtBairro">Bairro</label>
                          <input type="text" class="form-control" required id="txtBairro" name="txtBairro" value="<?php echo $row->bairro; ?>" placeholder="Bairro">
                        </div>
                      </div>
                      <div class="col-12 col-sm-6">
                        <div class="form-group">
                          <label for="txtCidade">Cidade</label>
                          <input type="text" class="form-control" required id="txtCidade" name="txtCidade" value="<?php echo $row->cidade; ?>" placeholder="Cidade">
                        </div>
                      </div>
                      <div class="col-12 col-sm-6">
                        <div class="form-group">
                          <label for="txtCidade">CEP</label>
                          <input type="text" class="form-control" required id="txtCEP" name="txtCEP" value="<?php echo $row->cep; ?>" placeholder="00000-000" data-mask="00000-000">
                        </div>
                      </div>
                      <div class="col-12 col-sm-6">
                        <div class="form-group">
                          <label for="txtEstado">Estado</label>
                          <select required id="txtEstado" name="txtEstado" class="form-control">
                            <option value="">-- Selecione --</option>
                            <option <?php echo $row->estado == "AC" ? "selected" : ""; ?> value="AC">Acre</option>
                            <option <?php echo $row->estado == "AL" ? "selected" : ""; ?> value="AL">Alagoas</option>
                            <option <?php echo $row->estado == "AP" ? "selected" : ""; ?> value="AP">Amapá</option>
                            <option <?php echo $row->estado == "AM" ? "selected" : ""; ?> value="AM">Amazonas</option>
                            <option <?php echo $row->estado == "BA" ? "selected" : ""; ?> value="BA">Bahia</option>
                            <option <?php echo $row->estado == "CE" ? "selected" : ""; ?> value="CE">Ceará</option>
                            <option <?php echo $row->estado == "DF" ? "selected" : ""; ?> value="DF">Distrito Federal</option>
                            <option <?php echo $row->estado == "ES" ? "selected" : ""; ?> value="ES">Espírito Santo</option>
                            <option <?php echo $row->estado == "GO" ? "selected" : ""; ?> value="GO">Goiás</option>
                            <option <?php echo $row->estado == "MA" ? "selected" : ""; ?> value="MA">Maranhão</option>
                            <option <?php echo $row->estado == "MT" ? "selected" : ""; ?> value="MT">Mato Grosso</option>
                            <option <?php echo $row->estado == "MS" ? "selected" : ""; ?> value="MS">Mato Grosso do Sul</option>
                            <option <?php echo $row->estado == "MG" ? "selected" : ""; ?> value="MG">Minas Gerais</option>
                            <option <?php echo $row->estado == "PA" ? "selected" : ""; ?> value="PA">Pará</option>
                            <option <?php echo $row->estado == "PB" ? "selected" : ""; ?> value="PB">Paraíba</option>
                            <option <?php echo $row->estado == "PR" ? "selected" : ""; ?> value="PR">Paraná</option>
                            <option <?php echo $row->estado == "PE" ? "selected" : ""; ?> value="PE">Pernambuco</option>
                            <option <?php echo $row->estado == "PI" ? "selected" : ""; ?> value="PI">Piauí</option>
                            <option <?php echo $row->estado == "RJ" ? "selected" : ""; ?> value="RJ">Rio de Janeiro</option>
                            <option <?php echo $row->estado == "RN" ? "selected" : ""; ?> value="RN">Rio Grande do Norte</option>
                            <option <?php echo $row->estado == "RS" ? "selected" : ""; ?> value="RS">Rio Grande do Sul</option>
                            <option <?php echo $row->estado == "RO" ? "selected" : ""; ?> value="RO">Rondônia</option>
                            <option <?php echo $row->estado == "RR" ? "selected" : ""; ?> value="RR">Roraima</option>
                            <option <?php echo $row->estado == "SC" ? "selected" : ""; ?> value="SC">Santa Catarina</option>
                            <option <?php echo $row->estado == "SP" ? "selected" : ""; ?> value="SP">São Paulo</option>
                            <option <?php echo $row->estado == "SE" ? "selected" : ""; ?> value="SE">Sergipe</option>
                            <option <?php echo $row->estado == "TO" ? "selected" : ""; ?> value="TO">Tocantins</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-12 col-sm-6">
                        <div class="form-group">
                          <label for="txtAssinatura">Assinatura</label>
                          <input type="file" class="form-control" id="txtAssinatura" name="txtAssinatura" accept="image/*" placeholder="Assinatura">
                        </div>
                      </div>
                      <div class="col-12 col-sm-6">
                        <div class="form-group">
                          <label for="txtAssinaturaOld"></label>
                          <input type="hidden" class="form-control" id="txtAssinaturaOld" name="txtAssinaturaOld" value="<?php echo $row->assinatura; ?>">
                          <?php
                          if (!empty($row->assinatura) && file_exists('assinaturas/' . $row->assinatura)) {
                            echo '
                              <img src="assinaturas/' . $row->assinatura . '" alt="Assinatura" class="img-fluid" style="max-width: 200px;">
                            ';
                          }
                          ?>
                        </div>
                      </div>
                    </div>
                  </div>
                  <!-- /.card-body -->
                  <!-- INICIO DA TABELA PARA INSERIR PRODUTOS -->
                  <!-- <div class="row">
                    <div class="col-12">
                      <table class="table table-bordered" id="tabelaServicos">
                        <thead>
                          <tr>
                            <th width="50%">Serviço</th>
                            <th>Valor</th>
                            <th> <button class="btn btn-info btn-sm" type="button" onclick="AddRow()"><i class="fas fa-plus"> </i> Serviço</button> </th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          if (!empty($_GET["ref"])) {
                            $sql1 = "SELECT * FROM rv_colaboradoresServico WHERE fkColaborador = " . base64_decode($_GET["ref"]);
                            $rs1 = mysqli_query($connecta, $sql1);
                            while ($row1 = mysqli_fetch_object($rs1)) {
                          ?>
                              <tr>
                                <td>
                                  <select class="form-control" name="servico[]">
                                    <option value=""> -- Selecione -- </option>
                                    <?php
                                    $sql0 = "SELECT pkId,nome FROM rv_servicos WHERE ativo = 'S' ORDER BY nome";
                                    $rs0 = mysqli_query($connecta, $sql0);
                                    while ($row2 = mysqli_fetch_object($rs0)) {
                                      if ($row2->pkId == $row1->fkServico) {
                                        $selected = "selected";
                                      } else {
                                        $selected = "";
                                      }
                                      echo '<option ' . $selected . ' value="' . $row2->pkId . '">' . $row2->nome . '</option>';
                                    } ?>
                                  </select>
                                <td>
                                  <input type="text" class="form-control" name="valor[]" value="<?php echo $row1->valorHora ?>">
                                </td>
                                <td><button class="btn btn-danger btn-sm" onclick="RemoveRow(this)" type="button"><i class="fas fa-trash"></i></button></td>
                              </tr>
                            <?php
                            }
                          } else { ?>
                            <tr>
                              <td>
                                <select class="form-control" name="servico[]">
                                  <?php echo $listaServicos; ?>
                                </select>
                              </td>
                              <td>
                                <input type="text" class="form-control" name="valor[]">
                              </td>
                              <td> </td>
                            </tr>
                          <?php } ?>
                        </tbody>
                      </table>
                    </div>
                  </div> -->
                  <!-- FIM DA TABELA PARA INSERIR PRODUTOS -->

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

              <div class="card card-default color-palette-box">
                <div class="card-header">
                  <h3 class="card-title">Tipos</h3>
                </div>
                <div class="card-body">
                  <div class="col-12">
                    <table class="table table-bordered" id="tabelaServicos">
                      <thead>
                        <tr>
                          <th width="75%">Serviço</th>
                          <th style="width: 15%;">VALORES</th>
                          <th style="width: 10%;">
                            <button class="btn btn-success btn-sm w-100" type="button" onclick="AddRow()">Adicionar</button>
                          </th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php

                        if (!empty($rowsColaboradoresServico[0]) && is_array($rowsColaboradoresServico[0])) {
                          $i = 0;
                          foreach ($rowsColaboradoresServico as $row1) {
                            $i++;
                        ?>
                            <tr id='<?php echo $i; ?>'>
                              <td>
                                <select class="form-control" onchange="loadTipos(this, <?php echo $i; ?>)" id="servico_<?php echo $row1['pkId']; ?>" name="servicos[<?php echo $i; ?>]">
                                  <option value=""> -- Selecione -- </option>
                                  <?php
                                  foreach ($dadosServicos as $row2) {
                                    if ($row2['pkId'] == $row1['fkServico']) {
                                      $selected = "selected";
                                    } else {
                                      $selected = "";
                                    }
                                    echo '<option ' . $selected . ' value="' . $row2['pkId'] . '"> ' . $row2['nome'] . '</option>';
                                  }
                                  ?>
                                </select>
                              </td>
                              <!-- <td>
                                <select class="form-control" id="tipoServico_<?php echo $i; ?>" name="tipoServico_[<?php echo $i; ?>]">
                                  <option value=""> -- Selecione -- </option>
                                  <?php foreach ($dadosTipoServicos as $row3) {
                                    if ($row3['fkServico'] == $row1['fkServico']) {
                                      $selected2 = ($row3['pkId'] == $row1['fkTipoServico']) ? "selected" : "";
                                      echo '<option ' . $selected2 . ' value="' . $row3['pkId'] . '">' . $row3['nome'] . '</option>';
                                    }
                                  } ?>
                                </select>
                              </td> -->

                              <td> <input type="number" step="0.01" class="form-control" placeholder="R$" name="valorHora[<?php echo $i; ?>]" value="<?php echo $row1['valorHora']; ?>"> </td>
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

    let rowIndex = document.querySelectorAll('#tabelaServicos tbody tr').length + 1;

    AddRow = function() {
      var newRow = $("<tr>").attr("id", rowIndex);
      var cols = "";

      cols += '<td>';
      cols += `<select class="form-control servico" name="servicos[]" onchange="loadTipos(this, ${rowIndex})">`;
      cols += '<option value=""> -- Selecione -- </option>';
      cols += `<?php echo $listaServicos ?>`;
      cols += '</select>';
      cols += '</td>';

      // cols += '<td>';
      // cols += `<select class="form-control tipo-servico" name="tipoServico[]" id="tipoServico_${rowIndex}">`;
      // cols += '<option value=""> -- Selecione -- </option>';
      // cols += '</select>';
      // cols += '</td>';

      cols += '<td><input type="text" class="form-control" name="valorHora[]"></td>';

      cols += '<td class="d-flex justify-content-between">';
      cols += '<button class="btn btn-info btn-sm" onclick="InsertRow(this)" type="button"><i class="fas fa-check"></i></button>';
      cols += '<button class="btn btn-danger btn-sm" onclick="RemoveRow(this)" type="button"><i class="fas fa-trash"></i></button>';
      cols += '</td>';

      newRow.append(cols);
      $("#tabelaServicos").append(newRow);

      rowIndex++;
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
        const fkServico = document.getElementsByName(`servicos[${id}]`)[0];
        const pkId = fkServico.id?.split('_')[1];
        // const fkTipoServico = document.getElementsByName(`tipoServico_[${id}]`)[0];
        const valorHora = document.getElementsByName(`valorHora[${id}]`)[0];

        const body = {
          fkServico: fkServico.value,
          // fkTipoServico: fkTipoServico.value,
          valorHora: valorHora.value,
          id: pkId
        }

        const response = await $.ajax({
          url: `https://realvidas.com/area-administrativa/api/rv_colaboradoresServico/`,
          type: 'PATCH',
          contentType: 'application/json',
          data: JSON.stringify(body)
        });

      } catch (err) {
        console.error('Erro no fluxo completo:', err);
      }
    }
    async function InsertRow(button) {
      const tr = $(button).closest('tr');
      const fkServico = tr.find('select[name="servicos[]"]').val();
      // const fkTipoServico = tr.find('select[name="tipoServico[]"]').val();
      const valorHora = tr.find('input[name="valorHora[]"]').val();

      try {
        const body = {
          fkServico: fkServico,
          // fkTipoServico: fkTipoServico,
          valorHora: valorHora,
          fkColaborador: String('<?php echo base64_decode($_GET["ref"]); ?>')
        };

        const response = await $.ajax({
          url: `https://realvidas.com/area-administrativa/api/rv_colaboradoresServico/`,
          type: 'POST',
          contentType: 'application/json',
          data: JSON.stringify(body)
        });

        window.location.href = './insert.php?ref=<?php echo $_GET["ref"]; ?>&msg=<?php echo base64_encode("Registro inserido com sucesso!"); ?>&type=<?php echo base64_encode("success"); ?>';

      } catch (error) {
        console.error('Erro ao inserir:', error);
      }
    }


    async function DeleteRow(id) {
      try {
        const fkServico = document.getElementsByName(`servicos[${id}]`)[0];
        const pkId = fkServico.id?.split('_')[1];

        const body = {
          id: pkId
        }

        const response = await $.ajax({
          url: `https://realvidas.com/area-administrativa/api/rv_colaboradoresServico/`,
          type: 'DELETE',
          contentType: 'application/json',
          data: JSON.stringify(body)
        });

        window.location.href = './insert.php?ref=<?php echo $_GET["ref"]; ?>&msg=<?php echo base64_encode("Registro inserido com sucesso!"); ?>&type=<?php echo base64_encode("success"); ?>';

      } catch (err) {
        console.error('Erro no fluxo completo:', err);
      }
    }

    async function loadTipos(selectServico, rowIndex) {
      try {
        const fkServico = selectServico.value;
        if (!fkServico) return;

        const response = await $.ajax({
          url: `https://realvidas.com/area-administrativa/api/rv_tipoServicos/?fkServico=${fkServico}`,
          type: 'GET',
          contentType: 'application/json',
        });

        const tipos = response?.result || [];
        if (tipos) {
          const $tipoSelect = $(`#tipoServico_${rowIndex}`);

          // Limpa e adiciona a opção padrão
          $tipoSelect.empty().append('<option value="">Selecione um tipo de serviço</option>');

          // Adiciona as novas opções
          tipos.forEach(function(tipo) {
            $tipoSelect.append(
              $('<option>', {
                value: tipo.pkId,
                text: tipo.nome
              })
            );
          });
        } else {
          // Limpa e adiciona a opção padrão
          $tipoSelect.empty().append('<option value="">Selecione um tipo de serviço</option>');

        }
      } catch (err) {
        console.error('Erro no fluxo completo:', err);
      }
    }



    // AddRow = function() {
    //   var newRow = $("<tr>");
    //   var cols = "";
    //   cols += '<td>';
    //   cols += '<select class="form-control" name="servico[]">';
    //   cols += '<?php echo $listaServicos; ?>';
    //   cols += '</select>';
    //   cols += '</td>';
    //   cols += '<td><input type="text" class="form-control" name="valor[]"></td>';
    //   cols += '<td>';
    //   cols += '<button class="btn btn-danger btn-sm" onclick="RemoveRow(this)" type="button"><i class="fas fa-trash"></i></button>';
    //   cols += '</td>';
    //   newRow.append(cols);
    //   $("#tabelaServicos").append(newRow);
    //   return false;
    // };
    // RemoveRow = function(item) {
    //   var tr = $(item).closest('tr');
    //   tr.fadeOut(400, function() {
    //     tr.remove();
    //   });
    //   return false;
    // }
  </script>
</body>

</html>