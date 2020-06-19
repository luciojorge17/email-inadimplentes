<?php
    session_start();
    include_once("../conexao.php");

    if((isset($_POST['inputUsuario'])) && (isset($_POST['inputPassword']))){
        $usuario = $_POST['inputUsuario'];
        $password = $_POST['inputPassword'];
        
        $sql = "SELECT cd_codusuario,ds_usuario,ds_login,ds_senha,ds_email,ds_email_senha FROM tbl_usuarios WHERE ds_login = '$usuario' AND ds_senha='$password'";
        $result = odbc_exec($conexao, $sql) or die("Erro no sql");
		$resultado =odbc_fetch_array($result,1);
		
        if(empty($resultado)){
            $_SESSION['msg_login'] = "Usuário ou senha inválido";
            header("Location: login.php");
        } elseif(isset($resultado)){
            $_SESSION['id_usuario'] = odbc_result($result,"cd_codusuario");
            $_SESSION['nome_usuario'] = odbc_result($result,"ds_usuario");
            $_SESSION['email_envio'] = odbc_result($result,"ds_email");
            $_SESSION['senha_envio'] = odbc_result($result,"ds_email_senha");
            header("Location: ../index.php");
        } else{
            $_SESSION['msg_login'] = "Usuário ou senha inválido";
            header("Location: login.php");
        }  
    } else{
        $_SESSION['msg_login'] = "Usuário ou senha inválido";
        header("Location: login.php");
	}
?>