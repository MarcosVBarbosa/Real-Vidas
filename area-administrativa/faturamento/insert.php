<?php
include('../verifyConnection.php');
$pageActive = "Faturamento";
$acessoPermissoes = validAcesso(json_decode(base64_decode($_SESSION["acessoPermissoes"]), true), $pageActive);

if ($acessoPermissoes["isAcesso"] == 0) {
  $type = base64_encode("error");
  $msg  = base64_encode("Usuário sem permissão para acessar este conteúdo!");
header('Location: ../?msg=' . $msg . '&type=' . $type);
  exit;
}
include('../connectDb.php');

if (!empty($_GET["ref"])) {
  $query = "SELECT *,(SELECT SUM(valorRemocao + valorPercurso + totalHoraParada) FROM rv_ordemServico WHERE pkId IN (SELECT fkOrdemServico FROM rv_faturamentoOS WHERE fkFaturamento = '" . mysqli_real_escape_string($connecta, base64_decode($_GET["ref"])) . "')) valorTotal FROM rv_faturamento WHERE ativo = 'S' AND pkId = " . mysqli_real_escape_string($connecta, base64_decode($_GET["ref"]));
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

$listaOS = '<option value=""> -- Selecione -- </option>';
$sql0 = "
SELECT os.pkId,CONCAT(os.nrOrdemServico,' | ',c.razaoSocial) ordemServico 
FROM rv_ordemServico os
LEFT JOIN rv_clientes c ON (c.pkId = os.fkCliente)
WHERE os.ativo = 'S'
AND os.pkId NOT IN (
  SELECT fos.fkOrdemServico
  FROM rv_faturamento f
  JOIN rv_faturamentoOS fos ON (f.pkId = fos.fkFaturamento)
  WHERE f.ativo = 'S'
)
ORDER BY os.pkId DESC
";
$rs0 = mysqli_query($connecta, $sql0);
while ($row1 = mysqli_fetch_object($rs0)) {
  $listaOS .= '<option value="' . $row1->pkId . '">' . $row1->ordemServico . '</option>';
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
              <h1>Faturamento</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="../">Home</a></li>
                <li class="breadcrumb-item active">Faturamento</li>
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
                          <label for="txtNumFaturamento">Nº Faturamento</label>
                          <div class="input-group">
                            <input type="text" class="form-control" id="txtNumFaturamento" name="txtNumFaturamento" value="<?php echo $row->pkId; ?>" required readonly>
                          </div>
                        </div>
                      </div>
                      <div class="col-12 col-sm-6">
                        <div class="form-group">
                          <label for="txtStatus">Status</label>
                          <select class="form-control select2" id="txtStatus" name="txtStatus" required>
                            <option value=""> -- Selecione -- </option>
                            <option <?php echo $row->status == "Cancelado" ? "selected" : ""; ?> value="Cancelado">Cancelado</option>
                            <option <?php echo $row->status == "Pendente" ? "selected" : ""; ?> value="Pendente">Pendente</option>
                            <option <?php echo $row->status == "A faturar" ? "selected" : ""; ?> value="A faturar">A faturar</option>
                            <option <?php echo $row->status == "Pago" ? "selected" : ""; ?> value="Pago">Pago</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-12 col-sm-4">
                        <div class="form-group">
                          <label for="txtNF">Nota Fiscal</label>
                          <input type="text" class="form-control" id="txtNF" name="txtNF" value="<?php echo $row->notaFiscal; ?>">
                        </div>
                      </div>
                      <div class="col-12 col-sm-4">
                        <div class="form-group">
                          <label for="txtDataHora">Data</label>
                          <input type="date" class="form-control" id="txtDataHora" name="txtDataHora" value="<?php echo $row->dataHora; ?>" required>
                        </div>
                      </div>
                      <div class="col-12 col-sm-4">
                        <div class="form-group">
                          <label for="txtDataVencimento">Data Vencimento</label>
                          <input type="date" class="form-control" id="txtDataVencimento" name="txtDataVencimento" value="<?php echo $row->dataVencimento; ?>" required>
                        </div>
                      </div>
                      <div class="col-12 col-sm-4">
                        <div class="form-group">
                          <label for="txtFormaPgto">Forma Pagamento</label>
                          <select class="form-control select2" id="txtFormaPgto" name="txtFormaPgto" required>
                            <option value=""> -- Selecione -- </option>
                            <option <?php echo $row->formaPgto == "Boleto" ? "selected" : ""; ?> value="Boleto">Boleto</option>
                            <option <?php echo $row->formaPgto == "Depósito CC PJ" ? "selected" : ""; ?> value="Depósito CC PJ">Depósito CC PJ</option>
                            <option <?php echo $row->formaPgto == "Depósito CC PF" ? "selected" : ""; ?> value="Depósito CC PF">Depósito CC PF</option>
                            <option <?php echo $row->formaPgto == "Cartão Crédito" ? "selected" : ""; ?> value="Cartão Crédito">Cartão Crédito</option>
                            <option <?php echo $row->formaPgto == "Cartão Débito" ? "selected" : ""; ?> value="Cartão Débito">Cartão Débito</option>
                          </select>
                        </div>
                      </div>
                      <!--
                      <div class="col-12 col-sm-6">
                          <div class="form-group">
                            <label for="txtPrazoPgto">Prazo Pagamento</label>
                            <select class="form-control select2" id="txtPrazoPgto" name="txtPrazoPgto" required>
                                <option value=""> -- Selecione -- </option>
                                <option <php echo $row->prazoPgto == "A VISTA" ? "selected" : ""; ?> value="A VISTA">A VISTA</option>
                                <option <php echo $row->prazoPgto == "10 Dias" ? "selected" : ""; ?> value="10 Dias">10 Dias</option>
                                <option <php echo $row->prazoPgto == "15 Dias" ? "selected" : ""; ?> value="15 Dias">15 Dias</option>
                                <option <php echo $row->prazoPgto == "20 Dias" ? "selected" : ""; ?> value="20 Dias">20 Dias</option>
                                <option <php echo $row->prazoPgto == "30 Dias" ? "selected" : ""; ?> value="30 Dias">30 Dias</option>
                                <option <php echo $row->prazoPgto == "1ª Quinzena Subsequente" ? "selected" : ""; ?> value="1ª Quinzena Subsequente">1ª Quinzena Subsequente</option>
                                <option <php echo $row->prazoPgto == "2ª Quinzena Subsequente" ? "selected" : ""; ?> value="2ª Quinzena Subsequente">2ª Quinzena Subsequente</option>
                                <option <php echo $row->prazoPgto == "Todo dia 15" ? "selected" : ""; ?> value="Todo dia 15">Todo dia 15</option>
                                <option <php echo $row->prazoPgto == "Todo dia 30" ? "selected" : ""; ?> value="Todo dia 30">Todo dia 30</option>
                            </select>
                          </div>
                      </div> -->
                      <!-- <div class="col-12 col-sm-6">
                          <div class="form-group">
                            <label for="txtTaxaNF">% NF</label>
                            <div class="input-group">
                              <input type="text" class="form-control" id="txtTaxaNF" name="txtTaxaNF" value="?php echo $row->taxaNF; ?>" required>
                            </div>
                          </div>
                      </div> -->
                      <div class="col-12 col-sm-4">
                        <div class="form-group">
                          <label for="txtTaxaNF">R$ NF</label>
                          <div class="input-group">
                            <input type="text" class="form-control" id="txtTaxaNF" name="txtTaxaNF" value="<?php echo number_format((($row->valorTotal * $row->taxaNF) / 100), 2, ',', '.'); ?>" readonly>
                          </div>
                        </div>
                      </div>
                      <div class="col-12 col-sm-4">
                        <div class="form-group">
                          <label for="txtValorTotal">R$ Total</label>
                          <div class="input-group">
                            <input type="text" class="form-control" id="txtValorTotal" name="txtValorTotal" value="<?php echo number_format(($row->valorTotal), 2, ',', '.'); ?>" required readonly>
                          </div>
                        </div>
                      </div>
                      <div class="col-12 col-sm-6">
                        <div class="form-group">
                          <label for="txtCaminhoNF">Anexar NF</label>
                          <div class="input-group">
                            <div class="custom-file">
                              <input type="file" class="custom-file-input" id="txtCaminhoNF" name="txtCaminhoNF" accept="application/pdf">
                              <label class="custom-file-label" for="txtCaminhoNF">Selecione a NF</label>
                            </div>
                          </div>
                          <?php
                          if (!empty($row->caminhoNF)) {
                            echo '<a href="arquivos/' . $row->caminhoNF . '" target="_blank">Visualizar PDF <i class="fas fa-file-pdf"> </i></a>';
                            echo ' | ';
                            echo '<a class="text-danger" href="delete.php?ref=' . base64_encode($row->caminhoNF) . '&refFaturamento=' . $_GET["ref"] . '&table=' . base64_encode('caminhoNF') . '">Remover PDF <i class="fas fa-eraser"> </i></a>';
                          } ?>
                        </div>
                      </div>
                      <div class="col-12 col-sm-6">
                        <div class="form-group">
                          <label for="txtCaminhoBoleto">Anexar Boleto</label>
                          <div class="input-group">
                            <div class="custom-file">
                              <input type="file" class="custom-file-input" id="txtCaminhoBoleto" name="txtCaminhoBoleto" accept="application/pdf">
                              <label class="custom-file-label" for="txtCaminhoBoleto">Seleciona o boleto</label>
                            </div>
                          </div>
                          <?php
                          if (!empty($row->caminhoBoleto)) {
                            echo '<a href="arquivos/' . $row->caminhoBoleto . '" target="_blank">Visualizar PDF <i class="fas fa-file-pdf"> </i></a>';
                            echo ' | ';
                            echo '<a class="text-danger" href="delete.php?ref=' . base64_encode($row->caminhoBoleto) . '&refFaturamento=' . $_GET["ref"] . '&table=' . base64_encode('caminhoBoleto') . '">Remover PDF <i class="fas fa-eraser"> </i></a>';
                          } ?>
                        </div>
                      </div>
                    </div>
                    <!-- INICIO DA TABELA PARA INSERIR PRODUTOS -->
                    <div class="row">
                      <div class="col-12">
                        <div class="table-responsive">
                          <table class="table table-bordered" id="tabelaOS">
                            <thead>
                              <tr>
                                <th width="90%">Ordem Serviço</th>
                                <th> <button class="btn btn-info btn-sm" type="button" onclick="AddRow()"><i class="fas fa-plus"> </i> </button> </th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php
                              if (!empty($_GET["ref"])) {
                                $sql1 = "SELECT fos.pkId,fos.fkOrdemServico,CONCAT(os.nrOrdemServico,' | ',c.razaoSocial) ordemServico
                                        FROM rv_faturamentoOS fos 
                                        INNER JOIN rv_ordemServico os ON (fos.fkOrdemServico = os.pkId)
                                        INNER JOIN rv_clientes c ON (os.fkCliente = c.pkId)
                                        WHERE fos.fkFaturamento = " . base64_decode($_GET["ref"]);
                                $rs1 = mysqli_query($connecta, $sql1);
                                while ($row1 = mysqli_fetch_object($rs1)) {
                              ?>
                                  <tr>
                                    <td>
                                      <select class="form-control" name="ordemServico[]" <?php echo $required; ?>>
                                        <option value=""> -- Selecione -- </option>
                                        <?php
                                        $sql0 = "
                                                        SELECT os.pkId,CONCAT(os.nrOrdemServico,' | ',c.razaoSocial) ordemServico,
                                                        (
                                                          SELECT GROUP_CONCAT(f.pkId)
                                                          FROM rv_faturamento f
                                                          JOIN rv_faturamentoOS fos ON (f.pkId = fos.fkFaturamento)
                                                          WHERE fos.fkOrdemServico = os.pkId AND f.ativo = 'S'
                                                        ) id_faturamento
                                                        FROM rv_ordemServico os
                                                        LEFT JOIN rv_clientes c ON (c.pkId = os.fkCliente)
                                                        WHERE os.ativo = 'S' 
                                                        ORDER BY os.pkId DESC
                                                        ";
                                        $rs0 = mysqli_query($connecta, $sql0);
                                        while ($row2 = mysqli_fetch_object($rs0)) {
                                          if ($row2->pkId == $row1->fkOrdemServico) {
                                            $selected = "selected";
                                          } else {
                                            $selected = "";
                                          }

                                          // VERIF
                                          if (!empty($row2->id_faturamento) && $row2->id_faturamento != base64_decode($_GET["ref"])) {
                                            $row2->ordemServico = "(FATURADO - $row2->id_faturamento) - $row2->ordemServico";
                                          }

                                          echo '<option ' . $selected . ' value="' . $row2->pkId . '">' . $row2->ordemServico . '</option>';
                                        } ?>
                                      </select>
                                    </td>
                                    <td><button class="btn btn-danger btn-sm" onclick="RemoveRow(this)" type="button"><i class="fas fa-trash"></i></button></td>
                                  </tr>
                              <?php
                                }
                              } ?>
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

      bsCustomFileInput.init();

      //Initialize Select2 Elements
      $('.select2').select2();

      AddRow = function() {
        var newRow = $("<tr>");
        var cols = "";
        cols += '<td>';
        cols += '<select class="form-control" name="ordemServico[]">';
        cols += '<?php echo $listaOS ?>';
        cols += '</select>';
        cols += '</td>';
        cols += '<td>';
        cols += '<button class="btn btn-danger btn-sm" onclick="RemoveRow(this)" type="button"><i class="fas fa-trash"></i></button>';
        cols += '</td>';
        newRow.append(cols);
        $("#tabelaOS").append(newRow);
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