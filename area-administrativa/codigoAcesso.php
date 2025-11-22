<?php

ob_start();

if ($_SERVER["HTTP_HOST"] == 'realvidas.com') {
    if (!empty($_POST["txtCodigo"]) && !empty($_POST["txtSenha"])) {

        include('connectDb.php');

        $queryConsult = mysqli_query($connecta, "SELECT pkId, fkAdministrador FROM rv_recuperaSenha WHERE codigo = '" . mysqli_real_escape_string($connecta, $_POST["txtCodigo"]) . "'");

        if (mysqli_num_rows($queryConsult) > 0) {

            $resultConsult = mysqli_fetch_assoc($queryConsult);
            $sql = "
            UPDATE rv_administradores SET senha = SHA1('".mysqli_real_escape_string($connecta, $_POST["txtSenha"])."')
            WHERE pkId = " . $resultConsult["fkAdministrador"] . ";
            DELETE FROM rv_recuperaSenha WHERE pkId = " . $resultConsult["pkId"] . ";
            ";
            // echo $sql;exit;
            $updatePass = mysqli_multi_query($connecta, $sql);

            $type = base64_encode('success');
            $msg = base64_encode("Sua senha foi alterada com sucesso!");
        } else {

            $type = base64_encode('info');
            $msg = base64_encode("Falha ao tentar recuperar seu acesso!");
        }
    }
}

header('Location: https://' . $_SERVER['HTTP_HOST'] . '/area-administrativa/userLogin.php?msg=' . $msg . '&type=' . $type);
exit;
