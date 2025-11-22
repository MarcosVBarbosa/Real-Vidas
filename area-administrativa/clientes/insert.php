<?php
$pageActive = "Clientes";

include('../verifyConnection.php');
$acessoPermissoes = validAcesso(json_decode(base64_decode($_SESSION["acessoPermissoes"]), true), $pageActive);

if ($acessoPermissoes["isAcesso"] == 0) {
  $type = base64_encode("error");
  $msg  = base64_encode("Usuário sem permissão para acessar este conteúdo!");
  header('Location: https://' . $_SERVER['HTTP_HOST'] . '/area-administrativa-developer/index.php?msg=' . $msg . '&type=' . $type);
  exit;
}

include('../connectDb.php');
$isButton = false;

if (!empty($_GET["ref"])) {
  $query = "
  SELECT * 
  FROM rv_clientes 
  WHERE ativo = 'S' 
  AND pkId = " . mysqli_real_escape_string($connecta, base64_decode($_GET["ref"]));
  // echo $query;  exit;
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
              <h1>Clientes</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="../">Home</a></li>
                <li class="breadcrumb-item active">Clientes</li>
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
                <form method="post" action="save.php">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-10">
                        <div class="form-group">
                          <label for="txtRazaoSocial">Razão Social</label>
                          <input type="text" class="form-control" required id="txtRazaoSocial" name="txtRazaoSocial" value="<?php echo $row->razaoSocial; ?>" placeholder="Razão Social">
                        </div>
                      </div>
                      <div class="col-2">
                        <div class="form-group">
                          <label for="txtLimiteHoraParada">Limite Tolerância</label>
                          <input type="number" min="0" class="form-control" id="txtLimiteHoraParada" name="txtLimiteHoraParada" value="<?php echo $row->limiteHoraParada; ?>">
                        </div>
                      </div>
                      <div class="col-12 col-sm-6">
                        <div class="form-group">
                          <label for="txtNomeFantasia">Nome Fantasia</label>
                          <input type="text" class="form-control" required id="txtNomeFantasia" name="txtNomeFantasia" value="<?php echo $row->nomeFantasia; ?>" placeholder="Nome Fantasia">
                        </div>
                      </div>
                      <div class="col-12 col-sm-6">
                        <div class="form-group">
                          <label for="txtCNPJ">CNPJ/CPF</label>
                          <input type="text" class="form-control" required name="txtCNPJ" id="txtCNPJ" value="<?php echo $row->cpfCnpj; ?>" placeholder="CPF/CNPJ">
                        </div>
                      </div>
                      <div class="col-12 col-sm-6">
                        <div class="form-group">
                          <label for="txtResponsavel">Responsável</label>
                          <input type="text" class="form-control" required id="txtResponsavel" name="txtResponsavel" value="<?php echo $row->responsavel; ?>" placeholder="Responsável pelo contato">
                        </div>
                      </div>
                      <div class="col-12 col-sm-6">
                        <div class="form-group">
                          <label for="txtEmail">E-mail</label>
                          <input type="text" class="form-control" required id="txtEmail" name="txtEmail" value="<?php echo $row->email; ?>" placeholder="E-mail">
                        </div>
                      </div>
                      <div class="col-12 col-sm-6">
                        <div class="form-group">
                          <label for="txtTelefone">Telefone</label>
                          <input type="text" class="form-control" required id="txtTelefone" name="txtTelefone" value="<?php echo $row->telefone; ?>" placeholder="Telefone" data-mask="(99) 9999-9999">
                        </div>
                      </div>
                      <div class="col-12 col-sm-6">
                        <div class="form-group">
                          <label for="txtCelular">Celular</label>
                          <input type="text" class="form-control" required id="txtCelular" name="txtCelular" value="<?php echo $row->celular; ?>" placeholder="Celular" data-mask="(99) 9 9999-9999">
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
                    </div>
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
  <!-- InputMask -->
  <script src="../dist/js/jquery.mask.min.js"></script>
  <!-- AdminLTE App -->
  <script src="../dist/js/adminlte.min.js"></script>
  <!-- AdminLTE for demo purposes -->
  <script src="../dist/js/demo.js"></script>
  <script>
    $(function() {
      var options = {
        onKeyPress: function(cpf, ev, el, op) {
          var masks = ['000.000.000-000', '00.000.000/0000-00'];
          $('#txtCNPJ').mask((cpf.length > 14) ? masks[1] : masks[0], op);
        }
      }

      $('#txtCNPJ').val().length > 11 ? $('#txtCNPJ').mask('00.000.000/0000-00', options) : $('#txtCNPJ').mask('000.000.000-00#', options);

    });
  </script>
</body>

</html>