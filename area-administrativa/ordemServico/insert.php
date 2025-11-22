<?php
include('../verifyConnection.php');
$pageActive = "Ordem Serviço";
$acessoPermissoes = validAcesso(json_decode(base64_decode($_SESSION["acessoPermissoes"]), true), $pageActive);

if ($acessoPermissoes["isAcesso"] == 0) {
  $type = base64_encode("error");
  $msg  = base64_encode("Usuário sem permissão para acessar este conteúdo!");
  header('Location: ../?msg=' . $msg . '&type=' . $type);
  exit;
}
include('../connectDb.php');


$token = explode("&", base64_decode($_SESSION["token"]));
$isVisualizarValores = in_array($token[3], [1, 2]);

$fkTipoServico = 'null';

if (!empty($_GET["ref"])) {
  $query = "
    SELECT o.*,
    ((o.valorRemocao + o.valorPercurso + o.totalHoraParada) - o.despesaOperacional) lucro, 
    ((o.valorRemocao + o.valorPercurso + o.totalHoraParada - o.despesaOperacional) - ((o.valorRemocao + o.valorPercurso + o.totalHoraParada) * (o.aliquota / 100))) lucroReal,
    ((o.valorRemocao + o.valorPercurso + o.totalHoraParada) * (o.aliquota / 100)) as aliquotaReal,
    f.*
    FROM rv_ordemServico o
    LEFT JOIN rv_fichaAtendimento f ON (f.fkOs = o.pkId)
    WHERE o.ativo = 'S' AND o.pkId = " . mysqli_real_escape_string($connecta, base64_decode($_GET["ref"])) . "
    ";
  $rs = mysqli_query($connecta, $query);

  if (mysqli_num_rows($rs) > 0) {
    $row = mysqli_fetch_object($rs);
    $row->queimadura = json_decode($row->queimadura, true);
    $row->sinaisVitais = json_decode($row->sinaisVitais, true);
    $row->trauma = json_decode($row->trauma, true);
    $fkTipoServico =  $row->fkTipoServico;
    $observacao = $row->observacao;
    $totalGeral = $row->totalGeral;
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

$listaDestinos = '<option value=""> -- Selecione -- </option>';
$sql0 = "SELECT pkId,CONCAT(nome,' - ',cidade,' | ',estado) nome FROM rv_estabelecimentos WHERE ativo = 'S' ORDER BY nome";
$rs0 = mysqli_query($connecta, $sql0);
while ($row1 = mysqli_fetch_object($rs0)) {
  $listaDestinos .= '<option value="' . $row1->pkId . '">' . $row1->nome . '</option>';
}

$listaColaboradores = '<option value=""> -- Selecione -- </option>';
$sql0 = "SELECT c.pkId,CONCAT(c.nome,' | ',tc.nome) nome 
FROM rv_colaboradores c
LEFT JOIN rv_tipoColaboradores tc ON (tc.pkId = c.fkTipoColaborador)
WHERE c.ativo = 'S' and c.status = 'A'
ORDER BY c.nome";
$rs0 = mysqli_query($connecta, $sql0);
while ($row1 = mysqli_fetch_object($rs0)) {
  $listaColaboradores .= '<option value="' . $row1->pkId . '">' . $row1->nome . '</option>';
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
  <style>
    .hidden {
      display: none;
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
              <h1>Ordem Serviço</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="../">Home</a></li>
                <li class="breadcrumb-item active">Ordem Serviço</li>
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
                  <h3 class="card-title">Informações <a href="insert.php" class="btn btn-primary btn-xs"><i class="fas fa-plus"></i> Novo</a></h3>
                  <?php
                  if ($row->nrOrdemServico > 0) {
                    echo '<button type="button" class="btn btn-default btn-xs" style="margin-left: 5px;margin-top: -2px;" data-toggle="modal" data-target="#modal-default">
                            <i class="fa fa-file-image-o" aria-hidden="true"></i>  Ficha Atendimento
                            </button>';
                  }
                  ?>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form method="post" action="save.php" enctype="multipart/form-data">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-12 col-sm-2">
                        <div class="form-group">
                          <label for="txtNrOS">Nº OS</label>
                          <input type="text" class="form-control" id="txtNrOS" name="txtNrOS" value="<?php echo $row->nrOrdemServico; ?>" readonly>
                        </div>
                      </div>
                      <div class="col-12 col-sm-7">
                        <div class="form-group">
                          <label for="txtCliente">Cliente</label>
                          <select class="form-control select2" name="txtCliente" required>
                            <option value="">-- Selecione --</option>
                            <?php
                            $query = mysqli_query($connecta, "SELECT pkId,razaoSocial,limiteHoraParada FROM rv_clientes WHERE ativo = 'S' ORDER BY razaoSocial");
                            while ($result = mysqli_fetch_object($query)) { ?>

                              <option <?php
                                      echo $row->fkCliente == $result->pkId ? 'selected' : ''; ?> limiteTolerancia='<?php echo $result->limiteHoraParada; ?>' value="<?php echo $result->pkId; ?>"><?php echo $result->razaoSocial; ?></option>
                            <?php } ?>
                          </select>
                        </div>
                      </div>
                      <div class="col-12 col-sm-3">
                        <div class="form-group">
                          <label for="txtCliente">Finalizada</label>
                          <select class="form-control select2" name="txtFinalizada" required>
                            <option selected value="">-- Selecione --</option>
                            <option <?php echo $row->finalizada == 1 ? 'selected' : ''; ?> value="1"> Sim </option>
                            <option <?php echo $row->finalizada == 0 ? 'selected' : ''; ?> value="0"> Não </option>
                          </select>
                        </div>
                      </div>
                      <div class="col-12 col-sm-4">
                        <div class="form-group">
                          <label for="txtSolicitante">Solicitante</label>
                          <div class="input-group">
                            <input type="text" class="form-control" id="txtSolicitante" name="txtSolicitante" value="<?php echo $row->solicitante; ?>" required>
                          </div>
                        </div>
                      </div>
                      <div class="col-12 col-md-8 d-flex">
                        <div class="col-12 col-md-4">
                          <div class="form-group">
                            <label for="txtServico">Serviço</label>
                            <div class="input-group">
                              <select class="form-control select2" name="txtServico" required onchange="addSelectTipoServico(this)">
                                <option value="">-- Selecione --</option>
                                <?php
                                $query = mysqli_query($connecta, "SELECT pkId,nome FROM rv_servicos WHERE ativo = 'S' ORDER BY nome");
                                while ($result = mysqli_fetch_object($query)) { ?>
                                  <option <?php echo $row->fkServico == $result->pkId ? 'selected' : ''; ?> value="<?php echo $result->pkId; ?>"><?php echo $result->nome; ?></option>
                                <?php } ?>
                              </select>
                            </div>
                          </div>
                        </div>
                        <div class="col-12 col-md-4">
                          <div class="form-group">
                            <label for="txtServico">Tipo</label>
                            <div class="input-group">
                              <select id='selectTipoServico' class="form-control select2" name="txtTipoServico" required onchange="setValorRemocao(this)">
                                <option value="">Selecione um tipo de serviço</option>
                              </select>
                            </div>
                          </div>
                        </div>
                        <div class="col-12 col-md-4">
                          <div class="form-group">
                            <label for="txtVTR">VTR</label>
                            <select class="form-control" id="txtVTR" name="txtVTR">
                              <option value=""> -- Selecione -- </option>
                              <?php
                              $sql0 = mysqli_query($connecta, "SELECT a.* FROM rv_vtr a WHERE a.ativo = 'S' ORDER BY a.nome");
                              while ($row0 = mysqli_fetch_object($sql0)) {
                                $selected = "";
                                if ($row->fkVTR == $row0->pkId) {
                                  $selected = "selected";
                                  $idMaleta = $row0->idMaleta;
                                }
                              ?>
                                <option <?php echo $selected; ?> value="<?php echo $row0->pkId; ?>"><?php echo $row0->nome . " | " . $row0->consumo; ?></option>
                              <?php } ?>
                            </select>
                          </div>
                        </div>
                      </div>
                      <div class="col-12 col-sm-6">
                        <div class="form-group">
                          <label for="txtDataSolicitada">Data Solicitada</label>
                          <input type="date" class="form-control" id="txtDataSolicitada" name="txtDataSolicitada" value="<?php echo empty($row->dataSolicitada) ? date("Y-m-d") : $row->dataSolicitada; ?>">
                        </div>
                      </div>
                      <div class="col-12 col-sm-6">
                        <div class="form-group">
                          <label for="txtDataAgendada">Data Agendada</label>
                          <input type="date" class="form-control" id="txtDataAgendada" name="txtDataAgendada" value="<?php echo empty($row->dataAgendada) ? date("Y-m-d") : $row->dataAgendada; ?>">
                        </div>
                      </div>
                      <div class="col-12 col-sm-6">
                        <div class="form-group">
                          <label for="txtTrajeto">Trajeto</label>
                          <select class="form-control select2" id="txtTrajeto" name="txtTrajeto" required>
                            <option value=""> -- Selecione -- </option>
                            <option <?php echo $row->trajeto == "Ida" ? "selected" : ""; ?> value="Ida">Ida</option>
                            <option <?php echo $row->trajeto == "Ida e Volta" ? "selected" : ""; ?> value="Ida e Volta">Ida e Volta</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-12 col-sm-6">
                        <div class="form-group">
                          <label for="txtPaciente">Paciente</label>
                          <div class="input-group">
                            <input type="text" class="form-control" id="txtPaciente" name="txtPaciente" value="<?php echo $row->paciente; ?>" required>
                          </div>
                        </div>
                      </div>
                      <div class="col-12 col-sm-6">
                        <div class="form-group">
                          <label for="txtConvenio">Convênio/SUS</label>
                          <div class="input-group">
                            <input type="text" class="form-control" id="txtConvenio" name="txtConvenio" value="<?php echo $row->convenio; ?>" required>
                          </div>
                        </div>
                      </div>
                      <div class="col-12 col-sm-6">
                        <div class="form-group">
                          <label for="txtNrCartao">Nº Cartão</label>
                          <div class="input-group">
                            <input type="text" class="form-control" id="txtNrCartao" name="txtNrCartao" value="<?php echo $row->nrCartao; ?>" required>
                          </div>
                        </div>
                      </div>
                      <div class="col-12 col-sm-6">
                        <div class="form-group">
                          <label for="txtCaminhoFicha">Anexar Ficha</label>
                          <?php
                          if (validButtonSubmit($acessoPermissoes['isPermissao'], 'Editar') || empty($row->caminhoFicha)) {
                            echo '
                              <div class="input-group">
                                <div class="custom-file">
                                  <input type="file" class="custom-file-input" id="txtCaminhoFicha" name="txtCaminhoFicha" accept="application/pdf">
                                  <label class="custom-file-label" for="txtCaminhoFicha">Selecione a Ficha</label>
                                </div>
                              </div>
                            ';
                          }
                          ?>
                          <?php
                          if (!empty($row->caminhoFicha)) {
                            echo '<a href="arquivos/' . $row->caminhoFicha . '" target="_blank">Visualizar PDF <i class="fas fa-file-pdf"> </i></a>';
                            echo ' | ';
                            if (validButtonSubmit($acessoPermissoes['isPermissao'], 'Remover')) {
                              echo '<a class="text-danger" href="delete.php?ref=' . base64_encode($row->caminhoFicha) . '&refOrdemServico=' . $_GET["ref"] . '">Remover PDF <i class="fas fa-eraser"> </i></a>';
                            }
                          } ?>
                        </div>
                      </div>

                      <div class="col-12 col-sm-6">
                        <div class="form-group">
                          <label for="txtCaminhoGuia">Anexar Guia</label>
                          <?php
                          if (validButtonSubmit($acessoPermissoes['isPermissao'], 'Editar') || empty($row->caminhoGuia)) {
                            echo '
                              <div class="input-group">
                                <div class="custom-file">
                                  <input type="file" class="custom-file-input" id="txtCaminhoGuia" name="txtCaminhoGuia" accept="application/pdf">
                                  <label class="custom-file-label" for="txtCaminhoGuia">Selecione a Guia</label>
                                </div>
                              </div>
                            ';
                          }
                          ?>
                          <?php
                          if (!empty($row->caminhoGuia)) {
                            echo '<a href="arquivos/' . $row->caminhoGuia . '" target="_blank">Visualizar PDF <i class="fas fa-file-pdf"> </i></a>';
                            echo ' | ';
                            if (validButtonSubmit($acessoPermissoes['isPermissao'], 'Remover')) {
                              echo '<a class="text-danger" href="delete.php?ref=' . base64_encode($row->caminhoGuia) . '&refOrdemServico=' . $_GET["ref"] . '">Remover PDF <i class="fas fa-eraser"> </i></a>';
                            }
                          } ?>
                        </div>
                      </div>

                      <hr class="col-11" style="background: #CCC; height: 5px;">

                      <div class="col-12 col-sm-3">
                        <div class="form-group">
                          <label for="txtDataHoraInicio">Hora Início</label>
                          <input type="datetime-local" class="form-control" id="txtDataHoraInicio" name="txtDataHoraInicio" value="<?php echo $row->dataHoraInicio == "" ?  date("Y-m-d\TH:i") : date("Y-m-d\TH:i", strtotime($row->dataHoraInicio)); ?>">
                        </div>
                      </div>
                      <div class="col-12 col-sm-3">
                        <div class="form-group">
                          <label for="txtKmInicial">KM Inicial</label>
                          <input type="number" class="form-control" id="txtKmInicial" name="txtKmInicial" value="<?php echo $row->kmInicial; ?>">
                        </div>
                      </div>
                      <div class="col-12 col-sm-3">
                        <div class="form-group">
                          <label for="txtDataHoraFim">Hora Fim</label>
                          <input type="datetime-local" class="form-control" id="txtDataHoraFim" name="txtDataHoraFim" value="<?php echo $row->dataHoraInicio == "" ?  date("Y-m-d\TH:i", strtotime('+1 hour')) : date("Y-m-d\TH:i", strtotime($row->dataHoraFim)); ?>">
                        </div>
                      </div>
                      <div class="col-12 col-sm-3">
                        <div class="form-group">
                          <label for="txtKmFinal">KM Final</label>
                          <input type="number" class="form-control" id="txtKmFinal" name="txtKmFinal" value="<?php echo $row->kmFinal; ?>">
                        </div>
                      </div>
                      <div class="col-12">
                        <!-- INICIO DA TABELA ORIGEM -->
                        <div class="row">
                          <div class="col-12">
                            <div class="table-responsive">
                              <table class="table table-bordered">
                                <thead>
                                  <tr>
                                    <th>Origem / Cidade</th>
                                    <th>Chegada</th>
                                    <th>Saída</th>
                                    <th>KM</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <tr>
                                    <td>
                                      <select class="form-control select2" name="txtOrigem">
                                        <option value="">-- Selecione --</option>
                                        <?php
                                        $query = mysqli_query($connecta, "SELECT pkId,CONCAT(nome,' - ',cidade,' | ',estado) nome FROM rv_estabelecimentos WHERE ativo = 'S' ORDER BY nome");
                                        while ($result = mysqli_fetch_object($query)) { ?>
                                          <option <?php echo $row->fkOrigem == $result->pkId ? 'selected' : ''; ?> value="<?php echo $result->pkId; ?>"><?php echo $result->nome; ?></option>
                                        <?php } ?>
                                      </select>
                                    </td>
                                    <td>
                                      <input type="datetime-local" class="form-control" id="txtChegadaOrigem" name="txtChegadaOrigem" value="<?php echo $row->chegadaOrigem == "" ?  date("Y-m-d\TH:i") : date("Y-m-d\TH:i", strtotime($row->chegadaOrigem)); ?>">
                                    </td>
                                    <td>
                                      <input type="datetime-local" class="form-control" id="txtSaidaOrigem" name="txtSaidaOrigem" value="<?php echo $row->saidaOrigem == "" ?  date("Y-m-d\TH:i") : date("Y-m-d\TH:i", strtotime($row->saidaOrigem)); ?>">
                                    </td>
                                    <td>
                                      <input type="number" class="form-control" id="txtKmOrigem" name="txtKmOrigem" value="<?php echo $row->kmOrigem; ?>">
                                    </td>
                                  </tr>
                                </tbody>
                              </table>
                            </div>
                          </div>
                        </div>
                      </div>
                      <!-- FIM DA TABELA ORIGEM -->
                      <div class="col-12">
                        <!-- INICIO DA TABELA PARA INSERIR DESTINOS -->
                        <div class="row">
                          <div class="col-12">
                            <div class="table-responsive">
                              <table class="table table-bordered" id="tabelaDestinos">
                                <thead>
                                  <tr>
                                    <th>Destino / Cidade</th>
                                    <th>Chegada</th>
                                    <th>Saída</th>
                                    <th>KM</th>
                                    <th width="110"> <button class="btn btn-info btn-sm" type="button" onclick="AddRowD()"><i class="fas fa-plus"> </i> </button> </th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <?php
                                  if (!empty($_GET["ref"])) {
                                    $sql1 = "SELECT * FROM rv_ordemServicoDestino WHERE fkOrdemServico = " . base64_decode($_GET["ref"]);
                                    $rs1 = mysqli_query($connecta, $sql1);
                                    while ($row1 = mysqli_fetch_object($rs1)) {
                                  ?>
                                      <tr>
                                        <td>
                                          <select class="form-control select2" name="destino[]" onchange="setSelectDestino(this)">>
                                            <option value=""> -- Selecione -- </option>
                                            <?php
                                            $sql0 = "SELECT pkId,CONCAT(nome,' - ',cidade,' | ',estado) nome FROM rv_estabelecimentos WHERE ativo = 'S' ORDER BY nome";
                                            $rs0 = mysqli_query($connecta, $sql0);
                                            while ($row2 = mysqli_fetch_object($rs0)) {
                                              if ($row2->pkId == $row1->fkDestino) {
                                                $selected = "selected";
                                              } else {
                                                $selected = "";
                                              }
                                              echo '<option ' . $selected . ' value="' . $row2->pkId . '">' . $row2->nome . '</option>';
                                            } ?>
                                          </select>
                                        </td>
                                        <td>
                                          <input type="datetime-local" class="form-control" name="horaChegada[]" value="<?php echo $row1->horaChegada == "" ?  date("Y-m-d\TH:i") : date("Y-m-d\TH:i", strtotime($row1->horaChegada)); ?>">
                                        </td>
                                        <td>
                                          <input type="datetime-local" class="form-control horaSaida" name="horaSaida[]" value="<?php echo $row1->horaSaida == "" ?  date("Y-m-d\TH:i") : date("Y-m-d\TH:i", strtotime($row1->horaSaida)); ?>">
                                        </td>
                                        <td>
                                          <input type="text" class="form-control" name="distancia[]" value="<?php echo $row1->distancia ?>">
                                        </td>
                                        <td><button class="btn btn-danger btn-sm" onclick="RemoveRow(this)" type="button"><i class="fas fa-trash"></i></button></td>
                                      </tr>
                                    <?php
                                    }
                                  } else { ?>
                                    <tr>
                                      <td>
                                        <select class="form-control select2" name="destino[]">
                                          <?php echo $listaDestinos; ?>
                                        </select>
                                      </td>
                                      <td>
                                        <input type="datetime-local" class="form-control" name="horaChegada[]">
                                      </td>
                                      <td>
                                        <input type="datetime-local" class="form-control horaSaida" name="horaSaida[]">
                                      </td>
                                      <td>
                                        <input type="text" class="form-control" name="distancia[]">
                                      </td>
                                      <td><button class="btn btn-danger btn-sm" onclick="RemoveRow(this)" type="button"><i class="fas fa-trash"></i></button></td>
                                    </tr>
                                  <?php } ?>
                                </tbody>
                              </table>
                            </div>
                          </div>
                        </div>
                        <!-- FIM DA TABELA PARA INSERIR DESTINOS -->
                      </div>

                      <?php
                      if ($isVisualizarValores == 1) { ?>

                        <hr class="col-11" style="background: #CCC; height: 5px;">

                        <div class="col-12 col-sm-3">
                          <div class="form-group">
                            <label for="txtQtdeHoraParada">Qtde Hora Parada</label>
                            <input type="number" class="form-control" id="txtQtdeHoraParada" name="txtQtdeHoraParada" value="<?php echo $row->qtdeHoraParada; ?>" readonly>
                          </div>
                        </div>
                        <div class="col-12 col-sm-3">
                          <div class="form-group">
                            <label for="txtValorHoraParada">R$ Hora Parada</label>
                            <input type="text" class="form-control" id="txtValorHoraParada" name="txtValorHoraParada" value="<?php echo $row->valorHoraParada; ?>">
                          </div>
                        </div>
                        <div class="col-12 col-sm-3">
                          <div class="form-group">
                            <label for="txtLimiteHoraParada">Limite Tolerância</label>
                            <input type="number" min="0" class="form-control" id="txtLimiteHoraParada" name="txtLimiteHoraParada" value="<?php echo  $row->limiteHoraParada; ?>">
                          </div>
                        </div>
                        <div class="col-12 col-sm-3">
                          <div class="form-group">
                            <label for="txtTotalHoraParada">R$ Total Parada</label>
                            <input type="text" class="form-control" id="txtTotalHoraParada" name="txtTotalHoraParada" value="<?php echo number_format($row->totalHoraParada, 2, ',', '.'); ?>" readonly>
                          </div>
                        </div>
                        <!-- <div class="col-12 col-sm-3">
                          <div class="form-group">
                            <label for="txtVTR">VTR</label>
                            <select class="form-control" id="txtVTR" name="txtVTR">
                              <option value=""> -- Selecione -- </option>
                              <?php
                              // $sql0 = mysqli_query($connecta, "SELECT a.* FROM rv_vtr a WHERE a.ativo = 'S' ORDER BY a.nome");
                              // while ($row0 = mysqli_fetch_object($sql0)) {
                              //   $selected = "";
                              //   if ($row->fkVTR == $row0->pkId) {
                              //     $selected = "selected";
                              //     $idMaleta = $row0->idMaleta;
                              //   }
                              ?>
                                <option <?php //echo $selected; 
                                        ?> value="<?php //echo $row0->pkId; 
                                                  ?>"><?php //echo $row0->nome . " | " . $row0->consumo; 
                                                      ?></option>
                              <?php //} 
                              ?>
                            </select>
                          </div>
                        </div> -->
                        <div class="col-12 col-sm-4">
                          <div class="form-group">
                            <label for="txtKmPercorrido">KM Percorrido</label>
                            <input type="text" class="form-control" id="txtKmPercorrido" name="txtKmPercorrido" value="<?php echo $row->kmPercorrido; ?>" readonly>
                          </div>
                        </div>
                        <div class="col-12 col-sm-4">
                          <div class="form-group">
                            <label for="txtMediaDiesel">Média Diesel</label>
                            <input type="text" class="form-control" id="txtMediaDiesel" name="txtMediaDiesel" value="<?php echo $row->mediaDiesel; ?>" readonly>
                          </div>
                        </div>
                        <div class="col-12 col-sm-4">
                          <div class="form-group">
                            <label for="txtValorDiesel">Valor Diesel</label>
                            <input type="text" class="form-control" id="txtValorDiesel" name="txtValorDiesel" value="<?php echo $row->valorDiesel; ?>">
                          </div>
                        </div>
                        <div class="col-12 col-sm-3 <?php echo $isVisualizarValores ? '' : 'hidden'; ?>">
                          <div class="form-group">
                            <label for="txtValorGasto">Valor Gasto Diesel</label>
                            <input type="text" class="form-control" id="txtValorGasto" name="txtValorGasto" value="<?php echo number_format($row->gastoDiesel, 2, ',', '.'); ?>" readonly>
                          </div>
                        </div>
                        <div class="col-12 col-sm-3 <?php echo $isVisualizarValores ? '' : 'hidden'; ?>">
                          <div class="form-group">
                            <label for="txtValorRemocao">Valor Remoção</label>
                            <input type="text" class="form-control" id="txtValorRemocao" name="txtValorRemocao" value="<?php echo $row->valorRemocao; ?>">
                          </div>
                        </div>
                        <div class="col-12 col-sm-3 <?php echo $isVisualizarValores ? '' : 'hidden'; ?>">
                          <div class="form-group">
                            <label for="txtValorPercurso">Valor Percurso</label>
                            <input type="text" disabled class="form-control" id="txtValorPercurso" name="txtValorPercurso" value="<?php echo $row->valorPercurso; ?>">
                          </div>
                        </div>
                        <div class="col-12 col-sm-3 <?php echo $isVisualizarValores ? '' : 'hidden'; ?>">
                          <div class="form-group">
                            <label for="txtDespesaOperacional">Despesa Operacional</label>
                            <input type="text" class="form-control" id="txtDespesaOperacional" name="txtDespesaOperacional" value="<?php echo number_format($row->despesaOperacional, 2, ',', '.'); ?>" readonly>
                          </div>
                        </div>
                        <div class="col-12 col-sm-3 <?php echo $isVisualizarValores ? '' : 'hidden'; ?>">
                          <div class="form-group">
                            <label for="txtLucro">% Lucro</label>
                            <input type="text" class="form-control <?php echo $row->lucro > 0 ? 'text-success' : 'text-danger'; ?>" id="txtLucro" name="txtLucro" value="<?php echo $row->lucro == 0 ? '0' : number_format((($row->lucro * 100) / ($row->valorRemocao + $row->valorPercurso + $row->totalHoraParada)), 2, ',', '.') . '%'; ?>" readonly>
                          </div>
                        </div>
                        <div class="col-12 col-sm-6 <?php echo $isVisualizarValores ? '' : 'hidden'; ?>">
                          <div class="form-group">
                            <label for="txtTotalGeral">R$ Remoção + R$ Percurso + R$ Hora Parada</label>
                            <input type="text" class="form-control" id="txtTotalGeral" name="txtTotalGeral" value="<?php echo number_format(($row->valorRemocao + $row->valorPercurso + $row->totalHoraParada), 2, ',', '.'); ?>" readonly>
                          </div>
                        </div>
                        <div class="col-12 col-sm-6 <?php echo $isVisualizarValores ? '' : 'hidden'; ?>">
                          <div class="form-group">
                            <label for="txtLucro">R$ Lucro</label>
                            <input type="text" class="form-control <?php echo $row->lucro > 0 ? 'text-success' : 'text-danger'; ?>" id="txtLucro" name="txtLucro" value="<?php echo number_format($row->lucro, 2, ',', '.'); ?>" readonly>
                          </div>
                        </div>
                        <div class="col-12 col-sm-3 <?php echo $isVisualizarValores ? '' : 'hidden'; ?>">
                          <div class="form-group">
                            <label for="txtAliquota">% Alíquota</label>
                            <input type="text" class="form-control" id="txtAliquota" name="txtAliquota" value="<?php echo number_format($row->aliquota, 2, ',', '.'); ?>">
                          </div>
                        </div>
                        <div class="col-12 col-sm-3 <?php echo $isVisualizarValores ? '' : 'hidden'; ?>">
                          <div class="form-group">
                            <label for="txtAliquotaReal">R$ Alíquota</label>
                            <input type="text" class="form-control" id="txtAliquotaReal" name="txtAliquotaReal" value="<?php echo number_format($row->aliquotaReal, 2, ',', '.'); ?>">
                          </div>
                        </div>
                        <div class="col-12 col-sm-6 <?php echo $isVisualizarValores ? '' : 'hidden'; ?>">
                          <div class="form-group">
                            <label for="txtLucroReal">R$ Lucro Real</label>
                            <input type="text" class="form-control <?php echo $row->lucroReal > 0 ? 'text-success' : 'text-danger'; ?>" id="txtLucroReal" name="txtLucroReal" value="<?php echo number_format($row->lucroReal, 2, ',', '.'); ?>" readonly>
                          </div>
                        </div>
                      <?php } ?>
                    </div>
                    <!-- INICIO DA TABELA PARA INSERIR PRODUTOS -->
                    <div class="row <?php echo $isVisualizarValores ? '' : 'hidden'; ?>">
                      <div class="col-12 col-sm-6">
                        <div class="table-responsive">
                          <table class="table table-bordered" id="tabelaColaboradores">
                            <thead>
                              <tr>
                                <th width="40%">Colaborador</th>
                                <th>Qtde Horas</th>
                                <th>Ajuda Custo</th>
                                <?php
                                if ($isVisualizarValores == 1) { ?>
                                  <th>R$</th>
                                <?php } ?>
                                <th> <button class="btn btn-info btn-sm" type="button" onclick="AddRow()"><i class="fas fa-plus"> </i> </button> </th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php
                              if (!empty($_GET["ref"])) {
                                $sql1 = "SELECT * FROM rv_colaboradoresOS WHERE fkOrdemServico = " . base64_decode($_GET["ref"]);
                                // echo $sql1;exit;
                                $rs1 = mysqli_query($connecta, $sql1);
                                while ($row1 = mysqli_fetch_object($rs1)) {
                              ?>
                                  <tr>
                                    <td>
                                      <select class="form-control select2" name="colaborador[]">
                                        <option value=""> -- Selecione -- </option>
                                        <?php
                                        $sql0 = "
                                                        SELECT c.pkId,IF(c.status = 'A', CONCAT(c.nome,' | ',tc.nome), CONCAT('(INATIVO) - ', c.nome,' | ',tc.nome)) nome 
                                                        FROM rv_colaboradores c
                                                        LEFT JOIN rv_tipoColaboradores tc ON (tc.pkId = c.fkTipoColaborador)
                                                        WHERE c.ativo = 'S'
                                                        ORDER BY c.status , c.nome
                                                        ";
                                        $rs0 = mysqli_query($connecta, $sql0);
                                        while ($row2 = mysqli_fetch_object($rs0)) {
                                          if ($row2->pkId == $row1->fkColaborador) {
                                            $selected = "selected";
                                          } else {
                                            $selected = "";
                                          }
                                          echo '<option ' . $selected . ' value="' . $row2->pkId . '">' . $row2->nome . '</option>';
                                        } ?>
                                      </select>
                                    <td>
                                      <input type="text" class="form-control" name="qtdeHora[]" value="<?php echo $row1->qtdeHora ?>">
                                    </td>
                                    <td>
                                      <input type="text" class="form-control" name="ajudaCusto[]" value="<?php echo $row1->ajudaCusto ?>">
                                    </td>
                                    <?php
                                    if ($isVisualizarValores == 1) { ?>
                                      <th><?php echo number_format((($row1->qtdeHora * $row1->valorHora) + $row1->ajudaCusto), 2, ',', '.'); ?></th>
                                    <?php } ?>
                                    <td><button class="btn btn-danger btn-sm" onclick="RemoveRow(this)" type="button"><i class="fas fa-trash"></i></button></td>
                                  </tr>
                                <?php
                                }
                              } else { ?>
                                <tr>
                                  <td>
                                    <select class="form-control select2" name="colaborador[]">
                                      <?php echo $listaColaboradores; ?>
                                    </select>
                                  </td>
                                  <td>
                                    <input type="text" class="form-control" name="qtdeHora[]">
                                  </td>
                                  <td>
                                    <input type="text" class="form-control" name="ajudaCusto[]">
                                  </td>
                                  <?php
                                  if ($isVisualizarValores == 1) { ?>
                                    <th>R$ 0,00</th>
                                  <?php } ?>
                                  <td><button class="btn btn-danger btn-sm" onclick="RemoveRow(this)" type="button"><i class="fas fa-trash"></i></button></td>
                                </tr>
                              <?php } ?>
                            </tbody>
                          </table>
                        </div>
                      </div>
                      <div class="col-12 col-sm-6">
                        <div class="table-responsive">
                          <table class="table table-bordered" id="tabelaDespesas">
                            <thead>
                              <tr>
                                <th width="40%">Despesa</th>
                                <th>Qtde</th>
                                <?php
                                if ($isVisualizarValores == 1) { ?>
                                  <th>R$</th>
                                <?php } ?>
                                <th> <button class="btn btn-info btn-sm" type="button" onclick="AddRow2()"><i class="fas fa-plus"> </i> </button> </th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php
                              if (!empty($_GET["ref"])) {
                                $sql1 = "SELECT * FROM rv_despesasOS WHERE fkOrdemServico = " . base64_decode($_GET["ref"]);
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
                                    <?php
                                    if ($isVisualizarValores == 1) { ?>
                                      <th><?php echo number_format(($row1->qtde * $row1->valor), 2, ',', '.'); ?></th>
                                    <?php } ?>
                                    <td>
                                      <?php
                                      if ($row1->confirmado == 0) { ?>
                                        <a title="Confirmar medicação" href="confirmacao.php?ref=<?php echo $_GET["ref"] ?>&d=<?php echo base64_encode($row1->fkDespesa) ?>&m=<?php echo base64_encode($idMaleta) ?>&q=<?php echo base64_encode($row1->qtde) ?>" class="btn btn-sm btn-outline-info"><i class="fas fa-thumbs-up"></i></a>
                                      <?php } else { ?>
                                        <a title="Medicação confirmada" href="#" class="btn btn-sm btn-success disabled"><i class="fas fa-check-double"></i></a>
                                      <?php } ?>
                                      <button class="btn btn-danger btn-sm" onclick="RemoveRow(this)" type="button"><i class="fas fa-trash"></i></button>
                                    </td>
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
                                  <?php
                                  if ($isVisualizarValores == 1) { ?>
                                    <th>R$ 0,00</th>
                                  <?php } ?>
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
                      echo '<button type="submit" class="btn btn-primary"> Salvar</button>';
                    }; ?>
                  </div>


                </form>
              </div>
              <!-- /.card -->

              <!--modal-ficha-atendimento -->
              <div class="modal fade" id="modal-default">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h4 class="modal-title">Ficha Atendimento</h4>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                    <div class="modal-body">
                      <form id="fichaAtendimentoForm" action="save-fichaAtendimento.php" method="post">
                        <div class="row mb-2">
                          <div class="col-12 d-flex justify-content-between">
                            <div class="col-12 m-0 p-0">
                              <label class="">Serviço</label>
                              <div class="form-group col-12 m-0 p-0 d-flex justify-content-between">
                                <div class="form-check">
                                  <input class="form-check-input" type="checkbox" id="checkboxBasico" name="checkboxBasico" value="1" <?php echo $row->checkboxBasico == 1 ? 'checked' : ''; ?>>
                                  <label class="form-check-label" for=" checkboxServico">BÁSICA</label>
                                </div>
                                <div class="form-check">
                                  <input class="form-check-input" type="checkbox" id="checkboxExames" name="checkboxExames" value="1" <?php echo $row->checkboxExames == 1 ? 'checked' : ''; ?>>
                                  <label class="form-check-label">EXAMES</label>
                                </div>
                                <div class="form-check">
                                  <input class="form-check-input" type="checkbox" id="checkboxUtiAdulto" name="checkboxUtiAdulto" value="1" <?php echo $row->checkboxUtiAdulto == 1 ? 'checked' : ''; ?>>
                                  <label class="form-check-label">UTI ADULTO</label>
                                </div>
                                <div class="form-check">
                                  <input class="form-check-input" type="checkbox" id="checkboxInternacao" name="checkboxInternacao" value="1" <?php echo $row->checkboxInternacao == 1 ? 'checked' : ''; ?>>
                                  <label class="form-check-label">INTERNAÇÃO</label>
                                </div>
                                <div class="form-check">
                                  <input class="form-check-input" type="checkbox" id="checkboxAph" name="checkboxAph" value="1" <?php echo $row->checkboxAph == 1 ? 'checked' : ''; ?>>
                                  <label class="form-check-label">APH</label>
                                </div>
                                <div class="form-check">
                                  <input class="form-check-input" type="checkbox" id="checkboxTrasfHospitalar" name="checkboxTrasfHospitalar" value="1" <?php echo $row->checkboxTrasfHospitalar == 1 ? 'checked' : ''; ?>>
                                  <label class="form-check-label">TRASF. HOSPITALAR</label>
                                </div>
                                <div class="form-check">
                                  <input class="form-check-input" type="checkbox" id="checkboxUtiNeo" name="checkboxUtiNeo" value="1" <?php echo $row->checkboxUtiNeo == 1 ? 'checked' : ''; ?>>
                                  <label class="form-check-label">UTI NEO</label>
                                </div>
                                <div class="form-check">
                                  <input class="form-check-input" type="checkbox" id="checkboxEventos" name="checkboxEventos" value="1" <?php echo $row->checkboxEventos == 1 ? 'checked' : ''; ?>>
                                  <label class="form-check-label">EVENTOS</label>
                                </div>
                                <div class="form-check">
                                  <input class="form-check-input" type="checkbox" id="checkboxParticular" name="checkboxParticular" value="1" <?php echo $row->checkboxParticular == 1 ? 'checked' : ''; ?>>
                                  <label class="form-check-label">PARTICULAR</label>
                                </div>
                                <div class="form-check">
                                  <input class="form-check-input" type="checkbox" id="checkboxSivInt" name="checkboxSivInt" value="1" <?php echo $row->checkboxSivInt == 1 ? 'checked' : ''; ?>>
                                  <label class="form-check-label">SIV - INT</label>
                                </div>
                                <div class="form-check">
                                  <input class="form-check-input" type="checkbox" id="checkboxAltaHospitalar" name="checkboxAltaHospitalar" value="1" <?php echo $row->checkboxAltaHospitalar == 1 ? 'checked' : ''; ?>>
                                  <label class="form-check-label">ALTA HOSPITALAR</label>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>

                        <hr class="col-12 p-0 m-0 my-3" style="background: #CCC; height: 5px;">

                        <div class="row mb-2">
                          <div class="col-12 d-flex justify-content-between">
                            <div class="col-8 m-0 p-0">
                              <label class="">Tipo Exames</label>
                              <div class="form-group col-12 d-flex m-0 p-0 justify-content-between">
                                <div class="form-check">
                                  <input class="form-check-input" type="checkbox" id="checkboxCate" name="checkboxCate" value="1" <?php echo $row->checkboxCate == 1 ? 'checked' : ''; ?>>
                                  <label class="form-check-label">CATE</label>
                                </div>
                                <div class="form-check">
                                  <input class="form-check-input" type="checkbox" id="checkboxTomo" name="checkboxTomo" value="1" <?php echo $row->checkboxTomo == 1 ? 'checked' : ''; ?>>
                                  <label class="form-check-label">TOMO</label>
                                </div>
                                <div class="form-check">
                                  <input class="form-check-input" type="checkbox" id="checkboxRx" name="checkboxRx" value="1" <?php echo $row->checkboxRx == 1 ? 'checked' : ''; ?>>
                                  <label class="form-check-label">RX</label>
                                </div>
                                <div class="form-check">
                                  <input class="form-check-input" type="checkbox" id="checkboxGtt" name="checkboxGtt" value="1" <?php echo $row->checkboxGtt == 1 ? 'checked' : ''; ?>>
                                  <label class="form-check-label">GTT</label>
                                </div>
                                <div class="form-check">
                                  <input class="form-check-input" type="checkbox" id="checkboxCprs" name="checkboxCprs" value="1" <?php echo $row->checkboxCprs == 1 ? 'checked' : ''; ?>>
                                  <label class="form-check-label">CPRS</label>
                                </div>
                                <div class="form-check">
                                  <input class="form-check-input" type="checkbox" id="checkboxPetScam" name="checkboxPetScam" value="1" <?php echo $row->checkboxPetScam == 1 ? 'checked' : ''; ?>>
                                  <label class="form-check-label">PET SCAM</label>
                                </div>
                                <div class="form-check">
                                  <input class="form-check-input" type="checkbox" id="checkboxTcHip" name="checkboxTcHip" value="1" <?php echo $row->checkboxTcHip == 1 ? 'checked' : ''; ?>>
                                  <label class="form-check-label">T. C . HIPER</label>
                                </div>
                                <div class="form-check">
                                  <input class="form-check-input" type="checkbox" id="checkboxTqtTqm" name="checkboxTqtTqm" value="1" <?php echo $row->checkboxTqtTqm == 1 ? 'checked' : ''; ?>>
                                  <label class="form-check-label">TQT/TQM</label>
                                </div>
                                <div class="form-check">
                                  <input class="form-check-input" type="checkbox" id="checkboxUs" name="checkboxUs" value="1" <?php echo $row->checkboxUs == 1 ? 'checked' : ''; ?>>
                                  <label class="form-check-label">US</label>
                                </div>
                                <div class="form-check">
                                  <input class="form-check-input" type="checkbox" id="checkboxRmn" name="checkboxRmn" value="1" <?php echo $row->checkboxRmn == 1 ? 'checked' : ''; ?>>
                                  <label class="form-check-label">RMN</label>
                                </div>
                              </div>
                            </div>
                            <div class="col-4 m-0 p-0">
                              <!-- <label for="txtOutrosExames">Outros Exames</label> -->
                              <div class="form-group col-12">
                                <input class="form-control mt-3" placeholder="Outros Exames" name="txtOutrosExames" value="<?php echo $row->outrosExames; ?>">
                              </div>
                            </div>
                          </div>
                        </div>

                        <hr class="col-12 p-0 m-0 my-3" style="background: #CCC; height: 5px;">

                        <div class="row mb-2">
                          <div class="col-12 d-flex justify-content-between">
                            <div class="col-2 m-0 p-0">
                              <label for="txtOutrosExames">Idade</label>
                              <div class="form-group">
                                <input type="number" class="form-control" name="numberIdade" value="<?php echo $row->numberIdade; ?>">
                              </div>
                            </div>
                            <div class="col-2 m-0 p-0">
                              <label for="txtOutrosExames">Contato</label>
                              <div class="form-group">
                                <input type="text" class="form-control" name="txtContato" value="<?php echo $row->txtContato; ?>">
                              </div>
                            </div>
                            <div class="col-2 m-0 p-0">
                              <label for="txtOutrosExames">Sexo</label>
                              <select name="selectSexo" class="form-control">
                                <option selected value="0"></option>
                                <option value="1" <?php echo $row->selectSexo == 1 ? 'selected' : ''; ?>>Masculino</option>
                                <option value="2" <?php echo $row->selectSexo == 2 ? 'selected' : ''; ?>>Feminino</option>
                              </select>
                            </div>
                            <div class="col-2 m-0 p-0">
                              <label for="txtOutrosExames">Membros Superior</label>
                              <select name="selectMembrosSuperior" class="form-control">
                                <option selected value="0"></option>
                                <option value="1" <?php echo $row->selectMembrosSuperior == 1 ? 'selected' : ''; ?>>Alterados</option>
                                <option value="2" <?php echo $row->selectMembrosSuperior == 2 ? 'selected' : ''; ?>>Não Alterados</option>
                              </select>
                            </div>
                            <div class="col-2 m-0 p-0">
                              <label for="txtOutrosExames"> Membros Inferiores</label>
                              <select name="selectMembrosInferior" class="form-control">
                                <option selected value="0"></option>
                                <option value="1" <?php echo $row->selectMembrosInferior == 1 ? 'selected' : ''; ?>>Alterados</option>
                                <option value="2" <?php echo $row->selectMembrosInferior == 2 ? 'selected' : ''; ?>>Não Alterados</option>
                              </select>
                            </div>
                          </div>
                        </div>

                        <hr class="col-12 p-0 m-0 my-3" style="background: #CCC; height: 5px;">

                        <div class="row mb-2  d-flex justify-content-center">
                          <div class="col-12 text-center"> <label>Sinais Vitais</label> </div>
                          <br>
                          <div class="col-12">
                            <div class="col-12 d-flex justify-content-between align-items-center mb-2">
                              <div class="col text-center">HORA</div>
                              <div class="col text-center">PA</div>
                              <div class="col text-center">FC</div>
                              <div class="col text-center">FR</div>
                              <div class="col text-center">GLASGOW</div>
                              <div class="col text-center">TEMP</div>
                              <div class="col text-center">Sat O2</div>
                              <div class="col text-center">Hgt</div>
                            </div>
                            <div class="col-12 d-flex justify-content-between align-items-center mb-2">
                              <div class="col"><input type="time" name="sinaisVitais[0][0]" class="form-control text-center text-uppercase" value="<?php echo $row->sinaisVitais[0][0]; ?>"></div>
                              <div class="col"><input type="text" name="sinaisVitais[0][1]" class="form-control text-center text-uppercase" value="<?php echo $row->sinaisVitais[0][1]; ?>"></div>
                              <div class="col"><input type="text" name="sinaisVitais[0][2]" class="form-control text-center text-uppercase" value="<?php echo $row->sinaisVitais[0][2]; ?>"></div>
                              <div class="col"><input type="text" name="sinaisVitais[0][3]" class="form-control text-center text-uppercase" value="<?php echo $row->sinaisVitais[0][3]; ?>"></div>
                              <div class="col"><input type="text" name="sinaisVitais[0][4]" class="form-control text-center text-uppercase" value="<?php echo $row->sinaisVitais[0][4]; ?>"></div>
                              <div class="col"><input type="text" name="sinaisVitais[0][5]" class="form-control text-center text-uppercase" value="<?php echo $row->sinaisVitais[0][5]; ?>"></div>
                              <div class="col"><input type="text" name="sinaisVitais[0][6]" class="form-control text-center text-uppercase" value="<?php echo $row->sinaisVitais[0][6]; ?>"></div>
                              <div class="col"><input type="text" name="sinaisVitais[0][7]" class="form-control text-center text-uppercase" value="<?php echo $row->sinaisVitais[0][7]; ?>"></div>
                            </div>
                            <div class="col-12 d-flex justify-content-between align-items-center mb-2">
                              <div class="col"><input type="time" name="sinaisVitais[1][0]" class="form-control text-center text-uppercase" value="<?php echo $row->sinaisVitais[1][0]; ?>"></div>
                              <div class="col"><input type="text" name="sinaisVitais[1][1]" class="form-control text-center text-uppercase" value="<?php echo $row->sinaisVitais[1][1]; ?>"></div>
                              <div class="col"><input type="text" name="sinaisVitais[1][2]" class="form-control text-center text-uppercase" value="<?php echo $row->sinaisVitais[1][2]; ?>"></div>
                              <div class="col"><input type="text" name="sinaisVitais[1][3]" class="form-control text-center text-uppercase" value="<?php echo $row->sinaisVitais[1][3]; ?>"></div>
                              <div class="col"><input type="text" name="sinaisVitais[1][4]" class="form-control text-center text-uppercase" value="<?php echo $row->sinaisVitais[1][4]; ?>"></div>
                              <div class="col"><input type="text" name="sinaisVitais[1][5]" class="form-control text-center text-uppercase" value="<?php echo $row->sinaisVitais[1][5]; ?>"></div>
                              <div class="col"><input type="text" name="sinaisVitais[1][6]" class="form-control text-center text-uppercase" value="<?php echo $row->sinaisVitais[1][6]; ?>"></div>
                              <div class="col"><input type="text" name="sinaisVitais[1][7]" class="form-control text-center text-uppercase" value="<?php echo $row->sinaisVitais[1][7]; ?>"></div>
                            </div>
                            <div class="col-12 d-flex justify-content-between align-items-center mb-2">
                              <div class="col"><input type="time" name="sinaisVitais[2][0]" class="form-control text-center text-uppercase" value="<?php echo $row->sinaisVitais[2][0]; ?>"></div>
                              <div class="col"><input type="text" name="sinaisVitais[2][1]" class="form-control text-center text-uppercase" value="<?php echo $row->sinaisVitais[2][1]; ?>"></div>
                              <div class="col"><input type="text" name="sinaisVitais[2][2]" class="form-control text-center text-uppercase" value="<?php echo $row->sinaisVitais[2][2]; ?>"></div>
                              <div class="col"><input type="text" name="sinaisVitais[2][3]" class="form-control text-center text-uppercase" value="<?php echo $row->sinaisVitais[2][3]; ?>"></div>
                              <div class="col"><input type="text" name="sinaisVitais[2][4]" class="form-control text-center text-uppercase" value="<?php echo $row->sinaisVitais[2][4]; ?>"></div>
                              <div class="col"><input type="text" name="sinaisVitais[2][5]" class="form-control text-center text-uppercase" value="<?php echo $row->sinaisVitais[2][5]; ?>"></div>
                              <div class="col"><input type="text" name="sinaisVitais[2][6]" class="form-control text-center text-uppercase" value="<?php echo $row->sinaisVitais[2][6]; ?>"></div>
                              <div class="col"><input type="text" name="sinaisVitais[2][7]" class="form-control text-center text-uppercase" value="<?php echo $row->sinaisVitais[2][7]; ?>"></div>
                            </div>
                          </div>
                        </div>

                        <hr class="col-12 p-0 m-0 my-3" style="background: #CCC; height: 5px;">

                        <div class="row mb-2">
                          <div class=" col-12 d-flex justify-content-around">
                            <!-- Neurológica -->
                            <div class="form-group">
                              <label for="">Neurológica</label><br>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkboxLucido" name="checkboxLucido" value="1" <?php echo $row->checkboxLucido == 1 ? 'checked' : ''; ?>>
                                <label class="form-check-label">Lúcido</label>
                              </div>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkboxOrientado" name="checkboxOrientado" value="1" <?php echo $row->checkboxOrientado == 1 ? 'checked' : ''; ?>>
                                <label class="form-check-label">Orientado</label>
                              </div>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkboxConsciente" name="checkboxConsciente" value="1" <?php echo $row->checkboxConsciente == 1 ? 'checked' : ''; ?>>
                                <label class="form-check-label">Consciênte</label>
                              </div>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkboxConfuso" name="checkboxConfuso" value="1" <?php echo $row->checkboxConfuso == 1 ? 'checked' : ''; ?>>
                                <label class="form-check-label">Confuso</label>
                              </div>
                              <div class="form-check ">
                                <input class="form-check-input" type="checkbox" id="checkboxComunicativo" name="checkboxComunicativo" value="1" <?php echo $row->checkboxComunicativo == 1 ? 'checked' : ''; ?>>
                                <label class="form-check-label">Comunicativo</label>
                              </div>
                              <div class="form-check ">
                                <input class="form-check-input" type="checkbox" id="checkboxNaoVerbaliza" name="checkboxNaoVerbaliza" value="1" <?php echo $row->checkboxNaoVerbaliza == 1 ? 'checked' : ''; ?>>
                                <label class="form-check-label">Não Verbaliza</label>
                              </div>
                            </div>
                            <!-- ./Neurológica -->

                            <!-- Pupilas -->
                            <div class="form-group">
                              <label for="">Pupilas</label><br>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkboxIsocoricas" name="checkboxIsocoricas" value="1" <?php echo $row->checkboxIsocoricas == 1 ? 'checked' : ''; ?>>
                                <label class="form-check-label">Isocóricas</label>
                              </div>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkboxAnisocoricas" name="checkboxAnisocoricas" value="1" <?php echo $row->checkboxAnisocoricas == 1 ? 'checked' : ''; ?>>
                                <label class="form-check-label">Anisocorica</label>
                              </div>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkboxMidriatricas" name="checkboxMidriatricas" value="1" <?php echo $row->checkboxMidriatricas == 1 ? 'checked' : ''; ?>>
                                <label class="form-check-label">Midriátrica</label>
                              </div>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkboxMiotica" name="checkboxMiotica" value="1" <?php echo $row->checkboxMiotica == 1 ? 'checked' : ''; ?>>
                                <label class="form-check-label">Miotica</label>
                              </div>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkboxDE" name="checkboxDE" value="1" <?php echo $row->checkboxDE == 1 ? 'checked' : ''; ?>>
                                <label class="form-check-label">D ou E</label>
                              </div>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkboxMaior" name="checkboxMaior" value="1" <?php echo $row->checkboxMaior == 1 ? 'checked' : ''; ?>>
                                <label class="form-check-label">É Maior</label>
                              </div>
                            </div>
                            <!-- ./Pupilas -->

                            <!-- Respitatória -->
                            <div class="form-group">
                              <label for="">Respitatória</label><br>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkboxEupneico" name="checkboxEupneico" value="1" <?php echo $row->checkboxEupneico == 1 ? 'checked' : ''; ?>>
                                <label class="form-check-label">Eupnéico</label>
                              </div>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkboxTaquipneico" name="checkboxTaquipneico" value="1" <?php echo $row->checkboxTaquipneico == 1 ? 'checked' : ''; ?>>
                                <label class="form-check-label">Taquipneico</label>
                              </div>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkboxBradipneico" name="checkboxBradipneico" value="1" <?php echo $row->checkboxBradipneico == 1 ? 'checked' : ''; ?>>
                                <label class="form-check-label">Bradipneico</label>
                              </div>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkboxDispneico" name="checkboxDispneico" value="1" <?php echo $row->checkboxDispneico == 1 ? 'checked' : ''; ?>>
                                <label class="form-check-label">Dispneico</label>
                              </div>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkboxApneia" name="checkboxApneia" value="1" <?php echo $row->checkboxApneia == 1 ? 'checked' : ''; ?>>
                                <label class="form-check-label">Apneia</label>
                              </div>
                            </div>
                            <!-- ./Respitatória -->

                            <!-- Circulatório -->
                            <div class="form-group">
                              <label for="">Circulatório</label>
                              <div class="form-check ">
                                <input class="form-check-input" type="checkbox" id="checkboxNormocardico" name="checkboxNormocardico" value="1" <?php echo $row->checkboxNormocardico == 1 ? 'checked' : ''; ?>>
                                <label class="form-check-label">Normocárdico</label>
                              </div>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkboxTarquicardico" name="checkboxTarquicardico" value="1" <?php echo $row->checkboxTarquicardico == 1 ? 'checked' : ''; ?>>
                                <label class="form-check-label">Tarquicardico</label>
                              </div>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkboxBradicardico" name="checkboxBradicardico" value="1" <?php echo $row->checkboxBradicardico == 1 ? 'checked' : ''; ?>>
                                <label class="form-check-label">Bradicardico</label>
                              </div>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkboxFiliforme" name="checkboxFiliforme" value="1" <?php echo $row->checkboxFiliforme == 1 ? 'checked' : ''; ?>>
                                <label class="form-check-label">Filiforme</label>
                              </div>
                            </div>

                            <!-- ./Circulatório -->

                            <!-- Abdomem -->
                            <div class="form-group">
                              <label for="">Abdomem</label>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkboxPlano" name="checkboxPlano" value="1" <?php echo $row->checkboxPlano == 1 ? 'checked' : ''; ?>>
                                <label class="form-check-label">Plano</label>
                              </div>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkboxGloboso" name="checkboxGloboso" value="1" <?php echo $row->checkboxGloboso == 1 ? 'checked' : ''; ?>>
                                <label class="form-check-label">Globoso</label>
                              </div>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkboxEscavado" name="checkboxEscavado" value="1" <?php echo $row->checkboxEscavado == 1 ? 'checked' : ''; ?>>
                                <label class="form-check-label">Escavado</label>
                              </div>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkboxFlacido" name="checkboxFlacido" value="1" <?php echo $row->checkboxFlacido == 1 ? 'checked' : ''; ?>>
                                <label class="form-check-label">Flácido</label>
                              </div>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkboxEmAventa" name="checkboxEmAventa" value="1" <?php echo $row->checkboxEmAventa == 1 ? 'checked' : ''; ?>>
                                <label class="form-check-label">Em Aventa</label>
                              </div>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkboxGravidico" name="checkboxGravidico" value="1" <?php echo $row->checkboxGravidico == 1 ? 'checked' : ''; ?>>
                                <label class="form-check-label">Gravidico</label>
                              </div>
                            </div>
                            <!-- ./Abdomem -->

                            <!-- Abertura ocular: -->
                            <div class="form-group">
                              <label for="">Abertura ocular:</label><br>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkboxEspontanea" name="checkboxEspontanea" value="1" <?php echo $row->checkboxEspontanea == 1 ? 'checked' : ''; ?>>
                                <label class="form-check-label">Espontânea 4</label>
                              </div>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkboxVoz4" name="checkboxVoz4" value="1" <?php echo $row->checkboxVoz4 == 1 ? 'checked' : ''; ?>>
                                <label class="form-check-label">Voz 4</label>
                              </div>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkboxDor2" name="checkboxDor2" value="1" <?php echo $row->checkboxDor2 == 1 ? 'checked' : ''; ?>>
                                <label class="form-check-label">A dor 2</label>
                              </div>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkboxNenhuma1" name="checkboxNenhuma1" value="1" <?php echo $row->checkboxNenhuma1 == 1 ? 'checked' : ''; ?>>
                                <label class="form-check-label">Nenhuma 1</label>
                              </div>
                            </div>
                            <!-- ./Abertura ocular: -->

                            <!-- Resposta Verbal -->
                            <div class="form-group">
                              <label for="">Resposta Verbal:</label><br>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkboxOrientada5" name="checkboxOrientada5" value="1" <?php echo $row->checkboxOrientada5 == 1 ? 'checked' : ''; ?>>
                                <label class="form-check-label">Orientada 5</label>
                              </div>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkboxConfusa4" name="checkboxConfusa4" value="1" <?php echo $row->checkboxConfusa4 == 1 ? 'checked' : ''; ?>>
                                <label class="form-check-label">Confusa 4</label>
                              </div>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkboxPalavras3" name="checkboxPalavras3" value="1" <?php echo $row->checkboxPalavras3 == 1 ? 'checked' : ''; ?>>
                                <label class="form-check-label">Palavras Improórias 3</label>
                              </div>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkboxPalavras2" name="checkboxPalavras2" value="1" <?php echo $row->checkboxPalavras2 == 1 ? 'checked' : ''; ?>>
                                <label class="form-check-label">Palavras Incomprêencivas 2</label>
                              </div>
                            </div>
                            <!-- ./Resposta Verbal -->

                            <!-- Resposta Motora -->
                            <div class="form-group">
                              <label for="">Resposta Motora:</label><br>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkboxObdece6" name="checkboxObdece6" value="1" <?php echo $row->checkboxObdece6 == 1 ? 'checked' : ''; ?>>
                                <label class="form-check-label">Obdece comandos 6</label>
                              </div>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkboxLocaliza5" name="checkboxLocaliza5" value="1" <?php echo $row->checkboxLocaliza5 == 1 ? 'checked' : ''; ?>>
                                <label class="form-check-label">Localiza Dor 5</label>
                              </div>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkboxMovimentos4" name="checkboxMovimentos4" value="1" <?php echo $row->checkboxMovimentos4 == 1 ? 'checked' : ''; ?>>
                                <label class="form-check-label">Movimentos de retardia 4</label>
                              </div>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkboxFlexao3" name="checkboxFlexao3" value="1" <?php echo $row->checkboxFlexao3 == 1 ? 'checked' : ''; ?>>
                                <label class="form-check-label">Flexão Anormal 3</label>
                              </div>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkboxExtensao2" name="checkboxExtensao2" value="1" <?php echo $row->checkboxExtensao2 == 1 ? 'checked' : ''; ?>>
                                <label class="form-check-label">Extensão Anormal 2</label>
                              </div>
                              <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkboxNenhuma" name="checkboxNenhuma" value="1" <?php echo $row->checkboxNenhuma == 1 ? 'checked' : ''; ?>>
                                <label class="form-check-label">Nenhuma</label>
                              </div>
                            </div>
                            <!-- ./Resposta Motora -->
                          </div>
                        </div>

                        <hr class="col-12 p-0 m-0 my-3" style="background: #CCC; height: 5px;">

                        <div class="row mb-2">
                          <div class="col-12 d-flex justify-content-between">
                            <div class="col-12 col-sm-3">
                              <div class="form-group">
                                <label for="txtPedagios">Pedagios</label>
                                <input type="text" class="form-control" id="txtPedagios" name="txtPedagios" value="<?php echo $row->pedagios; ?>">
                              </div>
                            </div>
                            <div class="col-12 col-sm-7">
                              <div class="form-group">
                                <label for="txtOutros">Outros</label>
                                <input type="text" class="form-control" id="txtOutros" name="txtOutros" value="<?php echo $row->outros; ?>">
                              </div>
                            </div>
                            <div class="col-12 col-sm-2">
                              <div class="form-group">
                                <label>Refeição / Lanche</label>
                                <select name="selectRefeicaoLanche" class="form-control">
                                  <option selected value="0"></option>
                                  <option value="1" <?php echo $row->selectRefeicaoLanche == 1 ? 'selected' : ''; ?>>Sim</option>
                                  <option value="2" <?php echo $row->selectRefeicaoLanche == 2 ? 'selected' : ''; ?>>Não</option>
                                </select>
                              </div>
                            </div>
                          </div>
                          <div class="col-12 d-flex justify-content-between">
                            <div class="col-3">
                              <div class="form-group">
                                <label class="d-flex justify-content-between"><span>01 - Grande </span> <span>Bar Utilizado &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></label>
                                <div class="d-flex">
                                  <select name="selectGrandeUm" class="form-control col-4">
                                    <option selected value="0"></option>
                                    <option value="1" <?php echo $row->selectGrandeUm == 1 ? 'selected' : ''; ?>>Cheio</option>
                                    <option value="2" <?php echo $row->selectGrandeUm == 2 ? 'selected' : ''; ?>>Vazio</option>
                                    <option value="3" <?php echo $row->selectGrandeUm == 3 ? 'selected' : ''; ?>>Em uso</option>
                                  </select>
                                  &nbsp;&nbsp;
                                  <input type="text" class="form-control col-2" id="grandeUmBarValeu" name="grandeUmBarValeu" value="<?php echo $row->grandeUmBarValeu; ?>">
                                  &nbsp;&nbsp;
                                  <input type="text" class="form-control col" id="grandeUmBarQuantidade" name="grandeUmBarQuantidade" placeholder="Quantidade" value="<?php echo $row->grandeUmBarQuantidade; ?>">
                                </div>
                              </div>
                            </div>
                            <div class="col-3">
                              <div class="form-group">
                                <label class="d-flex justify-content-between"><span>02 - Grande </span> <span>Bar Utilizado &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></label>
                                <div class="d-flex">
                                  <select name="selectGrandeDois" class="form-control col-4">
                                    <option selected value=""></option>
                                    <option value="1" <?php echo $row->selectGrandeDois == 1 ? 'selected' : ''; ?>>Cheio</option>
                                    <option value="2" <?php echo $row->selectGrandeDois == 2 ? 'selected' : ''; ?>>Vazio</option>
                                    <option value="3" <?php echo $row->selectGrandeDois == 3 ? 'selected' : ''; ?>>Em uso</option>
                                  </select>
                                  &nbsp;&nbsp;
                                  <input type="text" class="form-control col-2" id="grandeDoisBarValeu" name="grandeDoisBarValeu" value="<?php echo $row->grandeDoisBarValeu; ?>">
                                  &nbsp;&nbsp;
                                  <input type="text" class="form-control col" id="grandeDoisBarQuantidade" name="grandeDoisBarQuantidade" placeholder="Quantidade" value="<?php echo $row->grandeDoisBarQuantidade; ?>">
                                </div>
                              </div>
                            </div>
                            <div class="col-3">
                              <div class="form-group ">
                                <label class="d-flex justify-content-between"><span>03 - Pequeno </span> <span>Bar Utilizado &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></label>
                                <div class="d-flex">
                                  <select name="selectPequenoTres" class="form-control col-4">
                                    <option selected value=""></option>
                                    <option value="1" <?php echo $row->selectGrandeTres == 1 ? 'selected' : ''; ?>>Cheio</option>
                                    <option value="2" <?php echo $row->selectGrandeTres == 2 ? 'selected' : ''; ?>>Vazio</option>
                                    <option value="3" <?php echo $row->selectGrandeTres == 3 ? 'selected' : ''; ?>>Em uso</option>
                                  </select>
                                  &nbsp;&nbsp;
                                  <input type="text" class="form-control col-2" id="pequenoTresBarValeu" name="pequenoTresBarValeu" value="<?php echo $row->pequenoTresBarValeu; ?>">
                                  &nbsp;&nbsp;
                                  <input type="text" class="form-control col" id="pequenoTresBarQuantidade" name="pequenoTresBarQuantidade" placeholder="Quantidade" value="<?php echo $row->pequenoTresBarQuantidade; ?>">
                                </div>
                              </div>
                            </div>
                            <div class="col-3">
                              <div class="form-group">
                                <label class="d-flex justify-content-between"><span>04 - Pegueno </span> <span>Bar Utilizado &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></label>
                                <div class="d-flex">
                                  <select name="selectPequenoQuatro" class="form-control col-4">
                                    <option selected value=""></option>
                                    <option value="1" <?php echo $row->selectGrandeQuatro == 1 ? 'selected' : ''; ?>>Cheio</option>
                                    <option value="2" <?php echo $row->selectGrandeQuatro == 2 ? 'selected' : ''; ?>>Vazio</option>
                                    <option value="3" <?php echo $row->selectGrandeQuatro == 3 ? 'selected' : ''; ?>>Em uso</option>
                                  </select>
                                  &nbsp;&nbsp;
                                  <input type="text" class="form-control col-2" id="pequenoQuatroBarValeu" name="pequenoQuatroBarValeu" value="<?php echo $row->pequenoQuatroBarValeu; ?>">
                                  &nbsp;&nbsp;
                                  <input type="text" class="form-control col" id="pequenoQuatroBarQuantidade" name="pequenoQuatroBarQuantidade" placeholder="Quantidade" value="<?php echo $row->pequenoQuatroBarQuantidade; ?>">
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>

                        <hr class="col-12 p-0 m-0" style="background: #CCC; height: 5px;">

                        <div class="row mb-2  d-flex justify-content-center">
                          <div class="col-10">
                            <div class="col-12 d-flex justify-content-between align-items-center mb-2">
                              <div class="col-1 text-center"></div>
                              <div class="col-1 text-center">Cab</div>
                              <div class="col-1 text-center">Pesc</div>
                              <div class="col-1 text-center">T Ant</div>
                              <div class="col-1 text-center">Tpos</div>
                              <div class="col-1 text-center">Perín</div>
                              <div class="col-1 text-center">Msd</div>
                              <div class="col-1 text-center">Mse</div>
                              <div class="col-1 text-center">Mid</div>
                              <div class="col-1 text-center">Mie</div>
                            </div>
                            <div class="col-12 d-flex justify-content-between align-items-center mb-2">
                              <div class="col-1 text-center">|</div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="queimadura[0][0]" class="form-control text-center text-uppercase" value="<?php echo $row->queimadura[0][0]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="queimadura[0][1]" class="form-control text-center text-uppercase" value="<?php echo $row->queimadura[0][1]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="queimadura[0][2]" class="form-control text-center text-uppercase" value="<?php echo $row->queimadura[0][2]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="queimadura[0][3]" class="form-control text-center text-uppercase" value="<?php echo $row->queimadura[0][3]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="queimadura[0][4]" class="form-control text-center text-uppercase" value="<?php echo $row->queimadura[0][4]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="queimadura[0][5]" class="form-control text-center text-uppercase" value="<?php echo $row->queimadura[0][5]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="queimadura[0][6]" class="form-control text-center text-uppercase" value="<?php echo $row->queimadura[0][6]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="queimadura[0][7]" class="form-control text-center text-uppercase" value="<?php echo $row->queimadura[0][7]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="queimadura[0][8]" class="form-control text-center text-uppercase" value="<?php echo $row->queimadura[0][8]; ?>"></div>
                            </div>
                            <div class="col-12 d-flex justify-content-between align-items-center mb-2">
                              <div class="col-1 text-center">||</div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="queimadura[1][0]" class="form-control text-center text-uppercase" value="<?php echo $row->queimadura[1][0]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="queimadura[1][1]" class="form-control text-center text-uppercase" value="<?php echo $row->queimadura[1][1]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="queimadura[1][2]" class="form-control text-center text-uppercase" value="<?php echo $row->queimadura[1][2]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="queimadura[1][3]" class="form-control text-center text-uppercase" value="<?php echo $row->queimadura[1][3]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="queimadura[1][4]" class="form-control text-center text-uppercase" value="<?php echo $row->queimadura[1][4]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="queimadura[1][5]" class="form-control text-center text-uppercase" value="<?php echo $row->queimadura[1][5]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="queimadura[1][6]" class="form-control text-center text-uppercase" value="<?php echo $row->queimadura[1][6]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="queimadura[1][7]" class="form-control text-center text-uppercase" value="<?php echo $row->queimadura[1][7]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="queimadura[1][8]" class="form-control text-center text-uppercase" value="<?php echo $row->queimadura[1][8]; ?>"></div>
                            </div>
                            <div class="col-12 d-flex justify-content-between align-items-center mb-2">
                              <div class="col-1 text-center">|||</div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="queimadura[2][0]" class="form-control text-center text-uppercase" value="<?php echo $row->queimadura[2][0]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="queimadura[2][1]" class="form-control text-center text-uppercase" value="<?php echo $row->queimadura[2][1]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="queimadura[2][2]" class="form-control text-center text-uppercase" value="<?php echo $row->queimadura[2][2]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="queimadura[2][3]" class="form-control text-center text-uppercase" value="<?php echo $row->queimadura[2][3]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="queimadura[2][4]" class="form-control text-center text-uppercase" value="<?php echo $row->queimadura[2][4]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="queimadura[2][5]" class="form-control text-center text-uppercase" value="<?php echo $row->queimadura[2][5]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="queimadura[2][6]" class="form-control text-center text-uppercase" value="<?php echo $row->queimadura[2][6]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="queimadura[2][7]" class="form-control text-center text-uppercase" value="<?php echo $row->queimadura[2][7]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="queimadura[2][8]" class="form-control text-center text-uppercase" value="<?php echo $row->queimadura[2][8]; ?>"></div>
                            </div>
                          </div>
                        </div>

                        <hr class="col-12 p-0 m-0" style="background: #CCC; height: 5px;">

                        <div class="row mb-2  d-flex justify-content-center">
                          <div class="col-10">
                            <div class="col-12 d-flex justify-content-between align-items-center mb-2">
                              <div class="col-1 text-center">Descrição</div>
                              <div class="col-1 text-center">Crâ</div>
                              <div class="col-1 text-center">Face</div>
                              <div class="col-1 text-center">Col</div>
                              <div class="col-1 text-center">Tór</div>
                              <div class="col-1 text-center">Bac</div>
                              <div class="col-1 text-center">Msd</div>
                              <div class="col-1 text-center">Mse</div>
                              <div class="col-1 text-center">Mid</div>
                              <div class="col-1 text-center">Mie</div>
                            </div>
                            <div class="col-12 d-flex justify-content-between align-items-center mb-2">
                              <div class="col-1">Escoriação</div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="trauma[0][0]" class="form-control text-center text-uppercase" value="<?php echo $row->trauma[0][0]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="trauma[0][1]" class="form-control text-center text-uppercase" value="<?php echo $row->trauma[0][1]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="trauma[0][2]" class="form-control text-center text-uppercase" value="<?php echo $row->trauma[0][2]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="trauma[0][3]" class="form-control text-center text-uppercase" value="<?php echo $row->trauma[0][3]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="trauma[0][4]" class="form-control text-center text-uppercase" value="<?php echo $row->trauma[0][4]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="trauma[0][5]" class="form-control text-center text-uppercase" value="<?php echo $row->trauma[0][5]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="trauma[0][6]" class="form-control text-center text-uppercase" value="<?php echo $row->trauma[0][6]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="trauma[0][7]" class="form-control text-center text-uppercase" value="<?php echo $row->trauma[0][7]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="trauma[0][8]" class="form-control text-center text-uppercase" value="<?php echo $row->trauma[0][8]; ?>"></div>
                            </div>
                            <div class="col-12 d-flex justify-content-between align-items-center mb-2">
                              <div class="col-1">Cont</div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="trauma[1][0]" class="form-control text-center text-uppercase" value="<?php echo $row->trauma[1][0]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="trauma[1][1]" class="form-control text-center text-uppercase" value="<?php echo $row->trauma[1][1]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="trauma[1][2]" class="form-control text-center text-uppercase" value="<?php echo $row->trauma[1][2]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="trauma[1][3]" class="form-control text-center text-uppercase" value="<?php echo $row->trauma[1][3]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="trauma[1][4]" class="form-control text-center text-uppercase" value="<?php echo $row->trauma[1][4]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="trauma[1][5]" class="form-control text-center text-uppercase" value="<?php echo $row->trauma[1][5]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="trauma[1][6]" class="form-control text-center text-uppercase" value="<?php echo $row->trauma[1][6]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="trauma[1][7]" class="form-control text-center text-uppercase" value="<?php echo $row->trauma[1][7]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="trauma[1][8]" class="form-control text-center text-uppercase" value="<?php echo $row->trauma[1][8]; ?>"></div>
                            </div>
                            <div class="col-12 d-flex justify-content-between align-items-center mb-2">
                              <div class="col-1">Ferim</div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="trauma[2][0]" class="form-control text-center text-uppercase" value="<?php echo $row->trauma[2][0]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="trauma[2][1]" class="form-control text-center text-uppercase" value="<?php echo $row->trauma[2][1]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="trauma[2][2]" class="form-control text-center text-uppercase" value="<?php echo $row->trauma[2][2]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="trauma[2][3]" class="form-control text-center text-uppercase" value="<?php echo $row->trauma[2][3]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="trauma[2][4]" class="form-control text-center text-uppercase" value="<?php echo $row->trauma[2][4]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="trauma[2][5]" class="form-control text-center text-uppercase" value="<?php echo $row->trauma[2][5]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="trauma[2][6]" class="form-control text-center text-uppercase" value="<?php echo $row->trauma[2][6]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="trauma[2][7]" class="form-control text-center text-uppercase" value="<?php echo $row->trauma[2][7]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="trauma[2][8]" class="form-control text-center text-uppercase" value="<?php echo $row->trauma[2][8]; ?>"></div>
                            </div>
                            <div class="col-12 d-flex justify-content-between align-items-center mb-2">
                              <div class="col-1">Luxa</div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="trauma[3][0]" class="form-control text-center text-uppercase" value="<?php echo $row->trauma[3][0]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="trauma[3][1]" class="form-control text-center text-uppercase" value="<?php echo $row->trauma[3][1]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="trauma[3][2]" class="form-control text-center text-uppercase" value="<?php echo $row->trauma[3][2]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="trauma[3][3]" class="form-control text-center text-uppercase" value="<?php echo $row->trauma[3][3]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="trauma[3][4]" class="form-control text-center text-uppercase" value="<?php echo $row->trauma[3][4]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="trauma[3][5]" class="form-control text-center text-uppercase" value="<?php echo $row->trauma[3][5]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="trauma[3][6]" class="form-control text-center text-uppercase" value="<?php echo $row->trauma[3][6]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="trauma[3][7]" class="form-control text-center text-uppercase" value="<?php echo $row->trauma[3][7]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="trauma[3][8]" class="form-control text-center text-uppercase" value="<?php echo $row->trauma[3][8]; ?>"></div>
                            </div>
                            <div class="col-12 d-flex justify-content-between align-items-center mb-2">
                              <div class="col-1">Frat</div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="trauma[4][0]" class="form-control text-center text-uppercase" value="<?php echo $row->trauma[4][0]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="trauma[4][1]" class="form-control text-center text-uppercase" value="<?php echo $row->trauma[4][1]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="trauma[4][2]" class="form-control text-center text-uppercase" value="<?php echo $row->trauma[4][2]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="trauma[4][3]" class="form-control text-center text-uppercase" value="<?php echo $row->trauma[4][3]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="trauma[4][4]" class="form-control text-center text-uppercase" value="<?php echo $row->trauma[4][4]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="trauma[4][5]" class="form-control text-center text-uppercase" value="<?php echo $row->trauma[4][5]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="trauma[4][6]" class="form-control text-center text-uppercase" value="<?php echo $row->trauma[4][6]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="trauma[4][7]" class="form-control text-center text-uppercase" value="<?php echo $row->trauma[4][7]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="trauma[4][8]" class="form-control text-center text-uppercase" value="<?php echo $row->trauma[4][8]; ?>"></div>
                            </div>
                            <div class="col-12 d-flex justify-content-between align-items-center mb-2">
                              <div class="col-1">Lace</div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="trauma[5][0]" class="form-control text-center text-uppercase" value="<?php echo $row->trauma[5][0]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="trauma[5][1]" class="form-control text-center text-uppercase" value="<?php echo $row->trauma[5][1]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="trauma[5][2]" class="form-control text-center text-uppercase" value="<?php echo $row->trauma[5][2]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="trauma[5][3]" class="form-control text-center text-uppercase" value="<?php echo $row->trauma[5][3]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="trauma[5][4]" class="form-control text-center text-uppercase" value="<?php echo $row->trauma[5][4]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="trauma[5][5]" class="form-control text-center text-uppercase" value="<?php echo $row->trauma[5][5]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="trauma[5][6]" class="form-control text-center text-uppercase" value="<?php echo $row->trauma[5][6]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="trauma[5][7]" class="form-control text-center text-uppercase" value="<?php echo $row->trauma[5][7]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="trauma[5][8]" class="form-control text-center text-uppercase" value="<?php echo $row->trauma[5][8]; ?>"></div>
                            </div>
                            <div class="col-12 d-flex justify-content-between align-items-center mb-2">
                              <div class="col-1">Secç</div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="trauma[6][0]" class="form-control text-center text-uppercase" value="<?php echo $row->trauma[6][0]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="trauma[6][1]" class="form-control text-center text-uppercase" value="<?php echo $row->trauma[6][1]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="trauma[6][2]" class="form-control text-center text-uppercase" value="<?php echo $row->trauma[6][2]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="trauma[6][3]" class="form-control text-center text-uppercase" value="<?php echo $row->trauma[6][3]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="trauma[6][4]" class="form-control text-center text-uppercase" value="<?php echo $row->trauma[6][4]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="trauma[6][5]" class="form-control text-center text-uppercase" value="<?php echo $row->trauma[6][5]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="trauma[6][6]" class="form-control text-center text-uppercase" value="<?php echo $row->trauma[6][6]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="trauma[6][7]" class="form-control text-center text-uppercase" value="<?php echo $row->trauma[6][7]; ?>"></div>
                              <div class="col-1"><input type="text" oninput="validateInput(this)" name="trauma[6][8]" class="form-control text-center text-uppercase" value="<?php echo $row->trauma[6][8]; ?>"></div>
                            </div>
                          </div>
                        </div>

                        <hr class="col-12 p-0 m-0" style="background: #CCC; height: 5px;">

                        <div class="row mb-2">
                          <div class="col-12">
                            <label for="">Observações Médicas / Enfermeiros </label>
                            <textarea class="form-control mt-2" placeholder="Descreva aqui suas observações" rows="6" name="txtObsMedicaEnfermeiros"><?php echo $row->txtObsMedicaEnfermeiros; ?></textarea>
                          </div>
                        </div>

                        <hr class="col-12 p-0 m-0" style="background: #CCC; height: 5px;">

                        <div class="row mb-2">
                          <div class="col-12">
                            <label for="">Materiais Utilizados</label>
                            <textarea class="form-control mt-2" placeholder="Descreva aqui suas observações" rows="3" name="txtMateriaisUtilizados"><?php echo $row->txtMateriaisUtilizados; ?></textarea>
                          </div>
                        </div>
                        <input type="hidden" name="pkId" value="<?php echo $_GET["ref"]; ?>">
                      </form>
                    </div>
                    <div class="modal-footer justify-content-start">
                      <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                      <button type="submit" class="btn btn-primary" form="fichaAtendimentoForm">Salvar</button>
                      <a href="pdf-fichaAtendimento.php?nrOrdemServico=<?php echo $_GET["ref"]; ?>" class="btn btn-secondary" target="_blank">Imprimir</a>
                    </div>
                  </div>
                </div>
              </div>
              <!--./modal-ficha-atendimento -->

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
    let valorRemocao = 0;

    function validateInput(input) {
      const validValue = 'x';
      if (input.value.toLowerCase() !== validValue) {
        input.value = ''; // Limpa o campo se não for "x"
      }
    }


    async function addSelectTipoServicoUpdate(fkServico) {
      const selectedTipoServicoId = <?php echo $fkTipoServico; ?>;

      if (!selectedTipoServicoId) return;

      const response = await $.ajax({
        url: `https://realvidas.com/area-administrativa/api/rv_tipoServicos/?fkServico=${fkServico}`,
        type: 'GET',
        contentType: 'application/json',
      });

      if (!response || !response.result) return;

      $("#selectTipoServico").empty();
      $("#selectTipoServico").append('<option value="">Selecione um tipo de serviço</option>');

      response.result.forEach(item => {
        const option = $('<option>', {
          value: item.pkId,
          text: item.nome,
        });

        // Seleciona o valor vindo do PHP se corresponder
        if (item.pkId == selectedTipoServicoId) {
          option.prop('selected', true);
        }

        $("#selectTipoServico").append(option);
      });

      return false;
    }

    $(function() {

      <?php if (!empty($_GET["ref"])) { ?>
        addSelectTipoServicoUpdate('<?php echo $row->fkServico; ?>')
      <?php } ?>

      // faz validação de km inicial e final
      $('#txtKmFinal').change(function() {
        if (parseInt($('#txtKmFinal').val()) <= parseInt($('#txtKmInicial').val())) {
          alert('KM Final não pode ser menor do que o KM Inicial!');
          $('#txtKmFinal').val('');
          $('#txtKmFinal').focus();
        }
      });

      // faz validação de km inicial e final
      $('#txtDataHoraFim').change(function() {
        if (Date.parse($('#txtDataHoraFim').val()) < Date.parse($('#txtDataHoraInicio').val())) {
          alert('Data Final não pode ser menor do que o Data Inicial!');
          $('#txtDataHoraFim').val('');
          $('#txtDataHoraFim').focus();
        }
      });

      // faz validação de km inicial e final da origem
      $('#txtSaidaOrigem').change(function() {
        if (Date.parse($('#txtSaidaOrigem').val()) < Date.parse($('#txtChegadaOrigem').val())) {
          alert('Data Final da Origem não pode ser menor do que a Data Inicial da Origem!');
          $('#txtSaidaOrigem').val('');
          $('#txtSaidaOrigem').focus();
        }
      });

      // faz validação de km inicial e final da origem
      $('#txtChegadaOrigem').change(function() {
        if (Date.parse($('#txtChegadaOrigem').val()) < Date.parse($('#txtDataHoraInicio').val())) {
          alert('Data Inicial da Origem não pode ser menor do que a Data Inicial da OS!');
          $('#txtChegadaOrigem').val('');
          $('#txtChegadaOrigem').focus();
        }
      });

      // faz validação de km inicial e final da origem
      // $('.horaSaida').change(function() {
      // alert('teste');
      // if(Date.parse($('#txtChegadaOrigem').val()) < Date.parse($('#txtDataHoraInicio').val())) {
      //     alert('Data Inicial da Origem não pode ser menor do que a Data Inicial da OS!');
      //     $('#txtChegadaOrigem').val('');
      //     $('#txtChegadaOrigem').focus();
      // }
      // });


      bsCustomFileInput.init();
      //Initialize Select2 Elements
      $('.select2').select2();

      AddRow = function() {
        var newRow = $("<tr>");
        var cols = "";
        cols += '<td>';
        cols += '<select class="form-control select2" name="colaborador[]">';
        cols += '<?php echo $listaColaboradores ?>';
        cols += '</select>';
        cols += '</td>';
        cols += '<td><input type="text" class="form-control" name="qtdeHora[]"></td>';
        cols += '<td><input type="text" class="form-control" name="ajudaCusto[]"></td>';
        <?php
        if ($isVisualizarValores == 1) { ?>
          cols += '<td> </td>';
        <?php } ?>
        cols += '<td>';
        cols += '<button class="btn btn-danger btn-sm" onclick="RemoveRow(this)" type="button"><i class="fas fa-trash"></i></button>';
        cols += '</td>';
        newRow.append(cols);
        $("#tabelaColaboradores").append(newRow);
        return false;
      };
      AddRow2 = function() {
        var newRow = $("<tr>");
        var cols = "";
        cols += '<td>';
        cols += '<select class="form-control select2" name="despesa[]">';
        cols += '<?php echo $listaDespesas ?>';
        cols += '</select>';
        cols += '</td>';
        cols += '<td><input type="text" class="form-control" name="qtde[]"></td>';
        <?php
        if ($isVisualizarValores == 1) { ?>
          cols += '<td> </td>';
        <?php } ?>
        cols += '<td>';
        cols += '<button class="btn btn-danger btn-sm" onclick="RemoveRow(this)" type="button"><i class="fas fa-trash"></i></button>';
        cols += '</td>';
        newRow.append(cols);
        $("#tabelaDespesas").append(newRow);
        return false;
      };
      AddRowD = function() {
        var newRow = $("<tr>");
        var cols = "";
        cols += '<td>';
        cols += '<select class="form-control select2" name="destino[]">';
        cols += '<?php echo $listaDestinos ?>';
        cols += '</select>';
        cols += '</td>';
        cols += '<td><input type="datetime-local" class="form-control" name="horaChegada[]"></td>';
        cols += '<td><input type="datetime-local" class="form-control horaSaida" name="horaSaida[]"></td>';
        cols += '<td><input type="text" class="form-control" name="distancia[]" required></td>';
        cols += '<td>';
        cols += '<button class="btn btn-danger btn-sm" onclick="RemoveRow(this)" type="button"><i class="fas fa-trash"></i></button>';
        cols += '</td>';
        newRow.append(cols);
        $("#tabelaDestinos").append(newRow);
        return false;
      };
      addSelectTipoServico = async function(selectElement) {
        const selectedOption = selectElement.options[selectElement.selectedIndex];

        const response = await $.ajax({
          url: `https://realvidas.com/area-administrativa/api/rv_tipoServicos/?fkServico=${selectedOption.value}`,
          type: 'GET',
          contentType: 'application/json',
        });

        if (!response || !response.result) return;

        // Limpa opções anteriores, se necessário
        $("#selectTipoServico").empty();

        // Adiciona uma opção padrão (opcional)
        $("#selectTipoServico").append('<option value="">Selecione um tipo de serviço</option>');
        response.result.forEach(item => {
          const option = $('<option>', {
            value: item.pkId,
            text: item.nome,
            'valor-Remocao': item.valor
          });
          $("#selectTipoServico").append(option);
        });

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

    $('#modal-default').on('shown.bs.modal', function() {
      $('#fichaAtendimentoForm').trigger('reset');
    });


    function setValorRemocao(selectElement) {
      const selectedOption = selectElement.options[selectElement.selectedIndex];
      const valorRemocao = selectedOption.getAttribute('valor-Remocao');
      document.getElementsByName('txtValorRemocao')[0].value = valorRemocao;
    }


    function setLimiteTolerancia(selectElement) {
      const selectedOption = selectElement.options[selectElement.selectedIndex];
      const limiteTolerancia = selectedOption.getAttribute('limiteTolerancia');
      document.getElementsByName('txtLimiteHoraParada')[0].value = limiteTolerancia;
    }

    async function setSelectDestino(selectElement) {
      txtTotalGeral = 0;
      destinoTotal = null;

      const selectedOption = selectElement.options[selectElement.selectedIndex];
      const fkEstabelecimento = selectedOption.value;
    }
  </script>
</body>

</html>