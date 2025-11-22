<?php

if(!empty($_COOKIE["txtEmail"]) and !empty($_COOKIE["txtSenha"])) {
    $email = $_COOKIE["txtEmail"];
    $senha = $_COOKIE["txtSenha"];
    $checked = "checked";
} else {
    $email = "";
    $senha = "";
    $checked = "";
}

?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Real Vidas | Área Administrativa</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" type="image/png" href="imagens/favicon.png" />
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
</head>
<body class="hold-transition login-page">
    <?php
    if(!empty($_GET['msg']) and !empty($_GET['type'])) {
        if(base64_decode($_GET['type'])=='success') { $title = "Sucesso!"; $alert = "alert-success"; }
        if(base64_decode($_GET['type'])=='info') { $title = "Informação!"; $alert = "alert-info"; }
        if(base64_decode($_GET['type'])=='danger') { $title = "Erro!"; $alert = "alert-danger"; }
        if(base64_decode($_GET['type'])=='warning') { $title = "Aviso!"; $alert = "alert-warning"; }
    ?>
    <div class="alert <?php echo $alert ?> alert-dismissible">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
      <h5><i class="icon fas fa-ban"></i> <?php echo $title ?></h5>
      <?php echo base64_decode($_GET["msg"]) ?>
    </div>
    <?php } ?>
<div class="login-box">
  <div class="login-logo">
    <a href="">
       <img src="imagens/logo-real-vidas.png" width="250">
    </a>
  </div>
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">Digite seu e-mail e senha</p>

      <form action="authenticUser.php" method="post">
        <div class="input-group mb-3">
          <input required type="email" class="form-control" placeholder="Email" name="txtEmail" value="<?php echo $email; ?>">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input required type="password" class="form-control" placeholder="Senha" name="txtSenha" value="<?php echo $senha; ?>">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-8">
            <div class="icheck-primary">
              <input type="checkbox" id="remember" name="ckbLembrar" value="S" <?php echo $checked; ?>>
              <label for="remember">
                Lembre-me
              </label>
            </div>
          </div>
          <!-- /.col -->
          <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block">Entrar</button>
          </div>
          <!-- /.col -->
        </div>
      </form>

      <p class="mb-1">
        <a href="recupera-senha.php">Esqueceu a senha?</a>
      </p>
    </div>
    <!-- /.login-card-body -->
  </div>
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>

</body>
</html>
