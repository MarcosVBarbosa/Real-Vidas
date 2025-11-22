<?php
include('../verifyConnection.php');
$pageActive = "Estabelecimentos";
$acessoPermissoes = validAcesso(json_decode(base64_decode($_SESSION["acessoPermissoes"]), true), $pageActive);

if ($acessoPermissoes["isAcesso"] == 0) {
  $type = base64_encode("error");
  $msg  = base64_encode("Usuário sem permissão para acessar este conteúdo!");
  header('Location: ../?msg=' . $msg . '&type=' . $type);
  exit;
}

include('../connectDb.php');

$isButton = false;

if (!empty($_GET["ref"])) {
  $query = "SELECT * FROM rv_estabelecimentos WHERE ativo = 'S' AND pkId = " . mysqli_real_escape_string($connecta, base64_decode($_GET["ref"]));
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

if ($_GET["ref"]) {
  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://realvidas.com/area-administrativa/api/rv_estabelecimentos/?id=' .  base64_decode($_GET["ref"]),
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
  $row = $response['result'];
}

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://realvidas.com/area-administrativa/api/rv_estabelecimentosDestino/?fkEstabelecimento=' .  base64_decode($_GET["ref"]),
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

if ($response['result']['pkId'] > 0) {
  $totalDestino[] = $response['result'] ?? [];
} else {
  $totalDestino = $response['result'] ?? [];
}

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://realvidas.com/area-administrativa/api/rv_estabelecimentos/',
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

if ($response['result']['pkId'] > 0) {
  $selectDestino[] = $response['result'] ?? [];
} else {
  $selectDestino = $response['result'] ?? [];
}

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

$response = curl_exec($curl);

curl_close($curl);

$response = json_decode($response, true);

if ($response['result']['pkId'] > 0) {
  $selectServicos[] = $response['result'] ?? [];
} else {
  $selectServicos = $response['result'] ?? [];
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
  <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <link rel="stylesheet" href="../dist/css/adminlte.min.css">
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
  <style>
    .custom-file-label::after {
      content: "Selecionar"
    }
  </style>
</head>

<body class="hold-transition sidebar-mini">
  <div class="wrapper">
    <?php include('../header.php') ?>
    <?php include('../sideBar.php') ?>

    <div class="content-wrapper">
      <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1>Estabelecimentos</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="../">Home</a></li>
                <li class="breadcrumb-item active">Estabelecimentos</li>
              </ol>
            </div>
          </div>
        </div>
      </section>

      <section class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
              <div class="card card-default color-palette-box">
                <div class="card-header">
                  <h3 class="card-title">Informações</h3>
                </div>
                <form id="form">
                  <input type="hidden" name="pkId" id="pkId" value="<?php echo $row['pkId']; ?>">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-12 col-md-5">
                        <div class="form-group">
                          <label for="nome">Origem</label>
                          <input type="text" class="form-control" required id="nome" name="nome" value="<?php echo $row['nome']; ?>" placeholder="Nome">
                        </div>
                      </div>
                      <div class="col-12 col-sm-5">
                        <div class="form-group">
                          <label for="cidade">Cidade</label>
                          <input type="text" class="form-control" required id="cidade" name="cidade" value="<?php echo $row['cidade']; ?>" placeholder="Cidade">
                        </div>
                      </div>
                      <div class="col-12 col-md-2">
                        <div class="form-group">
                          <label for="estado">Estado</label>
                          <select required id="estado" name="estado" class="form-control">
                            <option value="">-- Selecione --</option>
                            <?php
                            $estados = ["AC", "AL", "AP", "AM", "BA", "CE", "DF", "ES", "GO", "MA", "MT", "MS", "MG", "PA", "PB", "PR", "PE", "PI", "RJ", "RN", "RS", "RO", "RR", "SC", "SP", "SE", "TO"];
                            foreach ($estados as $uf) {
                              $selected = $row['estado'] == $uf ? "selected" : "";
                              echo "<option value='$uf' $selected>$uf</option>";
                            }
                            ?>
                          </select>
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-12">
                        <table class="table table-bordered" id="tabelaServicos">
                          <thead>
                            <tr>
                              <th width="45%">DESTINO</th>
                              <th width="25%">SERVIÇO</th>
                              <th style="width: 10%;">IDA</th>
                              <th style="width: 10%;">IDA - VOLTA</th>
                              <th style="width: 10%;"> <button class="btn btn-success btn-sm w-100" type="button" onclick="AddRow()">Adicionar</button> </th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php
                            if ($totalDestino) {
                              foreach ($totalDestino as $row1) {
                            ?>
                                <tr>
                                  <td>
                                    <select class="form-control" name="destinos[]">
                                      <option value=""> -- Selecione -- </option>
                                      <?php
                                      foreach ($selectDestino as $row2) {
                                        $selected = $row2['pkId'] == $row1['fkDestino'] ? "selected" : "";
                                        echo "<option value='$row2[pkId]' $selected>$row2[nome]</option>";
                                      }
                                      ?>
                                    </select>
                                  </td>
                                  <td>
                                    <select class="form-control" name="servicos[]">
                                      <option value=""> -- Selecione -- </option>
                                      <?php
                                      foreach ($selectServicos as $row2) {
                                        $selected = $row2['pkId'] == $row1['fkServico'] ? "selected" : "";
                                        echo "<option value='$row2[pkId]' $selected>$row2[nome]</option>";
                                      }
                                      ?>
                                    </select>
                                  </td>
                                  <td><input type="text" class="form-control" placeholder="R$" name="ida[]" value="<?php echo $row1['ida']; ?>"></td>
                                  <td><input type="text" class="form-control" placeholder="R$" name="idaVolta[]" value="<?php echo $row1['idaVolta']; ?>"></td>
                                  <td><button class="btn btn-danger btn-sm" onclick="RemoveRow(this)" type="button"><i class="fas fa-trash"></i></button></td>
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
                  <div class="card-footer">
                    <input type="hidden" name="pkId" value="<?php echo $_GET["ref"] ?? ''; ?>">
                    <a href="./" class="btn btn-default">Cancelar</a>
                    <?php if ($isButton) {
                      echo '<button type="button" onclick="rv_estabelecimentosDestino()" class="btn btn-primary">Salvar</button>';
                    } ?>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>

    <?php include('../footer.php') ?>
  </div>

  <script src="../plugins/jquery/jquery.min.js"></script>
  <script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../dist/js/jquery.mask.min.js"></script>
  <script src="../plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
  <script src="../dist/js/adminlte.min.js"></script>
  <script src="../dist/js/demo.js"></script>
  <script>
    function AddRow() {
      const htmlSelectDestinos = `
        <option value=""> -- Selecione -- </option>
        <?php foreach ($selectDestino as $row2) {
          echo "<option value='{$row2['pkId']}'>{$row2['nome']}</option>";
        } ?>
      `;
      const htmlSelectServicos = `
        <option value=""> -- Selecione -- </option>
        <?php foreach ($selectServicos as $row2) {
          echo "<option value='{$row2['pkId']}'>{$row2['nome']}</option>";
        } ?>
      `;
      const row = `
        <tr>
          <td><select class='form-control' name='destinos[]'>` + htmlSelectDestinos + `</select></td>
          <td><select class='form-control' name='servicos[]'>` + htmlSelectServicos + `</select></td>
          <td><input type='text' class='form-control' name='ida[]' placeholder='R$'></td>
          <td><input type='text' class='form-control' name='idaVolta[]' placeholder='R$'></td>
          <td><button class='btn btn-danger btn-sm' onclick='RemoveRow(this)' type='button'><i class='fas fa-trash'></i></button></td>
        </tr>`;
      document.querySelector("#tabelaServicos tbody").insertAdjacentHTML('beforeend', row);
    }

    function RemoveRow(btn) {
      btn.closest('tr').remove();
    }

    async function rv_estabelecimentosDestino() {
      const nome = document.getElementById('nome').value;
      const cidade = document.getElementById('cidade').value;
      const estado = document.getElementById('estado').value;
      const pkIdInput = document.getElementById('pkId').value;

      const bodyEstab = {
        nome,
        cidade,
        estado
      };
      if (pkIdInput) bodyEstab.pkId = pkIdInput;

      const method = pkIdInput ? 'PUT' : 'POST';

      try {
        const responseEstab = await $.ajax({
          url: `https://realvidas.com/area-administrativa/api/rv_estabelecimentos/`,
          type: method,
          contentType: 'application/json',
          data: JSON.stringify(bodyEstab)
        });

        const pkId = pkIdInput || responseEstab.pkId;

        const responseDeleteDestinos = await $.ajax({
          url: `https://realvidas.com/area-administrativa/api/rv_estabelecimentosDestino/`,
          type: 'DELETE',
          contentType: 'application/json',
          data: JSON.stringify({
            'fkEstabelecimento': pkId
          })
        });

        const destinos = [];
        const destinosEls = document.getElementsByName('destinos[]');
        const servicosEls = document.getElementsByName('servicos[]');
        const idaEls = document.getElementsByName('ida[]');
        const idaVolta = document.getElementsByName('idaVolta[]');

        for (let i = 0; i < destinosEls.length; i++) {
          const fkDestinoAtual = destinosEls[i].value;
          const fkServicosAtual = servicosEls[i].value;
          if (fkDestinoAtual) {
            // Verifica se já existe no array
            // const jaExiste = destinos.some(destino => destino.fkDestino === fkDestinoAtual);
            // if (!jaExiste) {
            destinos.push({
              fkDestino: fkDestinoAtual,
              fkServico: fkServicosAtual,
              ida: idaEls[i].value || '0',
              idaVolta: idaVolta[i].value || '0',
              fkEstabelecimento: pkId
            });
            // }
          }
        }

        if (destinos.length > 0) {
          for (let i = 0; i < destinos.length; i++) {
            const body = destinos[i];
            response = await $.ajax({
              url: `https://realvidas.com/area-administrativa/api/rv_estabelecimentosDestino/`,
              type: 'POST',
              contentType: 'application/json',
              data: JSON.stringify(body)
            });
          }
        }


        window.location.href = './?msg=<?php echo base64_encode("Registro inserido com sucesso!"); ?>&type=<?php echo base64_encode("success"); ?>';

      } catch (err) {
        console.error('Erro no fluxo completo:', err);
        // window.location.href = './?msg=<?php echo base64_encode("Algo deu errado!"); ?>&type=<?php echo base64_encode("danger"); ?>';
      }
    }
  </script>
</body>

</html>