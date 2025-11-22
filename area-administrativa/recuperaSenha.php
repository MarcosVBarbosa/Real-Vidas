<?php

ob_start();

require("PHPMailer-master/src/PHPMailer.php");
require("PHPMailer-master/src/SMTP.php");

if($_SERVER["HTTP_HOST"]=='realvidas.com') {
    if(!empty($_POST["txtEmail"])) {

        include('connectDb.php');

        $queryConsult = mysqli_query($connecta,"SELECT pkId,nome,email FROM rv_administradores WHERE email = '" . mysqli_real_escape_string($connecta,$_POST["txtEmail"]) . "' AND ativo = 'S'");

        if(mysqli_num_rows($queryConsult) > 0) {

            $resultConsult = mysqli_fetch_assoc($queryConsult);
            $codigo = rand(1000,9999);
            $sql = "
            INSERT INTO rv_recuperaSenha (fkAdministrador, codigo) VALUES
            (" . $resultConsult["pkId"] . " , $codigo)
            ";
            // echo $sql;exit;
            $updatePass = mysqli_query($connecta,$sql);

            $body = "
            <html>
            <body>
            <p>Prezado colaborador!</p>
            <p>Segue abaixo instruções para recuperação de acesso a nossa plataforma!</p>
            <p><strong>Código:</strong> $codigo</p>
            <p><strong>Link:</strong> <a href='https://realvidas.com/area-cliente/codigo-acesso.php'>https://realvidas.com/area-cliente/codigo-acesso.php</a></p>
            <p><br>Atenciosamente,</p>
            <p><b>Real Vidas Remoções</b></p>
            <p>Tel: (12) 3522-1128<br>
            Cel: (12) 9 9123-3435 - DISK AMBULÂNCIA
            <br>
            <a href='https://realvidas.com.br/' target='_blank'>www.realvidas.com.br</a></p>
            <p><img src='https://realvidas.com.br/wp-content/uploads/2020/07/logo-realvidas-head.png' width='150'></p>
            </body>
            </html>
            ";

            $mail = new PHPMailer\PHPMailer\PHPMailer();
            $mail->IsSMTP(); // enable SMTP
            $mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only
            $mail->SMTPAuth = true; // authentication enabled
            $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
            $mail->Host = "smtp.hostinger.com";
            $mail->Port = 465; // or 587
            $mail->IsHTML(true);
            $mail->Username = "carlos.financeiro@realvidas.com.br";
            $mail->Password = "Rv@2023@";
            $mail->SetFrom("carlos.financeiro@realvidas.com.br");
            $mail->Subject = "Recuperação de Acesso - Real Vidas";
            $mail->Body = $body;
            $mail->AddAddress($resultConsult["email"]);
            $mail->CharSet = "UTF-8";
            $mail->AddReplyTo('carlos.financeiro@realvidas.com.br', 'Carlos Marcondes');

            if (!$mail->Send()) {
                $type = base64_encode('danger');
                $msg = base64_encode('Falha ao enviar e-mail com as instruções! Por favor tente mais tarde.' . $mail->ErrorInfo);
            } else {
                $type = base64_encode('success');
                $msg = base64_encode('Enviamos em seu e-mail instruções para recuperar seu acesso! Por favor verifique também a sua caixa de SPAM.');
            }

            $type = base64_encode('success');
            $msg = base64_encode("Enviamos em seu e-mail instruções para recuperar seu acesso! Por favor verifique também a sua caixa de SPAM.");
        } else {
            $type = base64_encode('info');
            $msg = base64_encode("Este e-mail não consta em nosso banco de dados!");
        }

    }
}

header('Location: https://' . $_SERVER['HTTP_HOST'] . '/area-administrativa/userLogin.php?msg='.$msg.'&type='.$type);
exit;

?>