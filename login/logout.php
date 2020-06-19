<?php
    session_start();
    unset(
        $_SESSION['id_usuario'],
        $_SESSION['nome_usuario'],
        $_SESSION['email_envio'],
        $_SESSION['senha_envio']
    );
	$_SESSION['msg_login'] = "Você saiu";
    header ("Location: login.php");
?>