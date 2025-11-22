<?php

$pageActive = 'Categorias Fornecedores';
include('../verifyConnection.php');
$acessoPermissoes = validAcesso(json_decode(base64_decode($_SESSION["acessoPermissoes"]), true), $pageActive);

if ($acessoPermissoes["isAcesso"] == 0) {
    $type = base64_encode("error");
    $msg  = base64_encode("Usuário sem permissão para acessar este conteúdo!");
    header('Location: ../?msg=' . $msg . '&type=' . $type);
    exit;
}
include('../connectDb.php');

if (!empty($_GET["ref"])) {
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://realvidas.com/area-administrativa/api/rv_categoriasFornecedores/?id=' . base64_decode($_GET["ref"]),
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

    $response = json_decode($response, true);

    $row = $response['result'] ?? null;

    if (empty($row)) {
        $type = base64_encode('danger');
        $msg = base64_encode('Registro não encontrado!');
        header('Location: ./?msg=' . $msg . '&type=' . $type);
        exit;
    }

    $method = 'PUT';
    $proximoId =  str_pad(base64_decode($_GET["ref"]), 5, '0', STR_PAD_LEFT);
    $isButton = validButtonSubmit($acessoPermissoes['isPermissao'], 'Editar');
} else {
    $proximoId = str_pad(base64_decode($_GET["proximoId"]), 5, '0', STR_PAD_LEFT);
    $method = 'POST';
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
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../dist/css/adminlte.min.css">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <?php include('../header.php') ?>
        <?php include('../sideBar.php') ?>

        <div class="content-wrapper">
            <section class="content-header">
                <div class="container-fluid">
                    <h1>Categoria de Fornecedores</h1>
                </div>
            </section>

            <section class="content">
                <div class="container-fluid">
                    <div class="card card-default color-palette-box">
                        <div class="card-header">
                            <h3 class="card-title">Categoria</h3>
                        </div>
                        <form id="form">
                            <input type="hidden" name="pkId" id="pkId" value="<?php echo $row['pkId'] ?? ''; ?>">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="nome">Nome</label>
                                    <input type="text" class="form-control" required id="nome" name="nome" value="<?php echo $row['nome'] ?? ''; ?>" placeholder="Nome da categoria">
                                </div>
                            </div>
                        </form>
                        <div class="card-footer">
                            <a href="./" class="btn btn-default">Cancelar</a>
                            <?php if ($isButton) {
                                echo '<button type="button" onclick="rv_categoriasFornecedores()" class="btn btn-primary">Salvar</button>';
                            } ?>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <?php include('../footer.php') ?>
    </div>

    <!-- Scripts -->
    <script src="../plugins/jquery/jquery.min.js"></script>
    <script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../dist/js/adminlte.min.js"></script>
    <script>
        function rv_categoriasFornecedores() {
            const body = {
                pkId: document.getElementById('pkId').value || '',
                nome: document.getElementById('nome').value || ''
            };

            const method = body.pkId ? 'PUT' : 'POST';

            $.ajax({
                url: `https://realvidas.com/area-administrativa/api/rv_categoriasFornecedores/`,
                type: method,
                contentType: 'application/json',
                data: JSON.stringify(body),
                success: function(response) {
                    window.location.href = './?msg=<?php echo base64_encode("Registro inserido/atualizado com sucesso!"); ?>&type=<?php echo base64_encode("success"); ?>';
                },
                error: function(xhr, status, error) {
                    window.location.href = './?msg=<?php echo base64_encode("Algo deu errado!"); ?>&type=<?php echo base64_encode("danger"); ?>';
                }
            });
        }
    </script>
</body>

</html>