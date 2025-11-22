<?php
$pageActive = 'Fornecedores';
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

$pagina = isset($_GET["pag"]) ? (int)$_GET["pag"] : 1;
$max = 15;
$qtd = 3;
$inicio = ($pagina - 1) * $max;

$pesquisa = isset($_GET['id']) && $_GET['id'] !== '' ? "?id=" . $_GET['id'] : '';

$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://realvidas.com/area-administrativa/api/rv_fornecedores/' . $pesquisa,
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

if (isset($response['result']['pkId']) && $response['result']['pkId'] > 0) {
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

        <!-- Content Wrapper -->
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
            <!-- Content Header -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Fornecedores</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../">Home</a></li>
                                <li class="breadcrumb-item active">Fornecedores</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Lista de Fornecedores <a href="insert.php" class="btn btn-primary btn-xs"><i class="fas fa-plus"></i> Novo</a></h3>
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

                                <div class="card-body table-responsive p-0" style="height: 65vh;">
                                    <table class="table table-head-fixed table-striped table-bordered text-nowrap table-sm">
                                        <thead>
                                            <tr style="text-align: center; text-transform: uppercase;">
                                                <th>Nome</th>
                                                <th>Telefone Fixo</th>
                                                <th>whatsapp</th>
                                                <th>Email</th>
                                                <th>Categoria</th>
                                                <th style="width:90px">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if (count($totalRegistros) > 0) {
                                                foreach ($totalRegistros as $row) { ?>
                                                    <tr style="text-align: center; text-transform: uppercase;">
                                                        <td><?php echo htmlspecialchars($row['nomeFantasia']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['telefoneFixo']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['whatsapp']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['categoriaFornecedor']); ?></td>
                                                        <td style="padding-right:.75rem">
                                                            <a href="insert.php?ref=<?php echo base64_encode($row['pkId']) ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                                                            <?php
                                                            if (validButtonSubmit($acessoPermissoes['isPermissao'], 'Remover')) {
                                                                echo "<a href='javascript:void(0)' onclick=\"if(confirm('Deseja realmente excluir esse fornecedor?')) { onDeleteFornecedor(" . $row["pkId"] . "); }\" class='btn btn-sm btn-outline-danger'>
                                                                        <i class='fas fa-trash'></i>
                                                                      </a>";
                                                            }
                                                            ?>
                                                        </td>
                                                    </tr>
                                                <?php
                                                }
                                            } else {
                                                ?>
                                                <tr>
                                                    <td colspan="6" class="text-center">Nenhum registro encontrado!</td>
                                                </tr>
                                            <?php
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>

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
    <script src="../dist/js/demo.js"></script>

    <script>
        function onDeleteFornecedor(id) {
            try {
                if (!id || isNaN(id) || parseInt(id) <= 0) {
                    alert('ID inválido. Não foi possível excluir o fornecedor.');
                    return;
                }

                $.ajax({
                    url: 'https://realvidas.com/area-administrativa/api/rv_fornecedores/',
                    type: 'DELETE',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        'id': id
                    }),
                    success: function(response) {
                        window.location.href = './?msg=<?php echo base64_encode("Fornecedor excluído com sucesso!"); ?>&type=<?php echo base64_encode("success"); ?>';
                    },
                    error: function(xhr, status, error) {
                        window.location.href = './?msg=<?php echo base64_encode("Erro ao excluir fornecedor!"); ?>&type=<?php echo base64_encode("danger"); ?>';
                    }
                });
            } catch (err) {
                console.error('Erro inesperado:', err);
            }
        }
    </script>
</body>

</html>