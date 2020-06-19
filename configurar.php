<?php
    session_start();
    $_SESSION['assunto']=$_POST['assunto'];
    $_SESSION['cabecalho']=$_POST['cabecalho'];
    $_SESSION['mensagem']=$_POST['msg'];
    $_SESSION['rodape']=$_POST['rodape'];
    echo "<p class='text-success'>Alterado!</p>";
?>