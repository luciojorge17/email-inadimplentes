<?php
	session_start();
?>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
	<link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" rel="stylesheet">
	<link rel="stylesheet" href="../css/theme.css">
	<link rel="stylesheet" href="../css/login.css">
	<link rel="icon" href="../images/favicon.gif"/>
    <title>Questor Empresarial - Login</title>
  </head>
  <body class="text-center">
    <form class="form-signin" method="post" action="valida.php">
      <img src="../images/logo-preto.png" class="mx-auto" height="75px" alt="Logo Questor">
      <label for="inputUsuario" class="sr-only">Usuário</label>
      <input name="inputUsuario" type="text" id="inputUsuario" class="form-control" placeholder="Usuário" required autofocus>
      <label for="inputPassword" class="sr-only">Senha</label>
      <input name="inputPassword" type="password" id="inputPassword" class="form-control" placeholder="Senha" required>
      <button class="btn btn-primary btn-block" type="submit">Entrar <i class="fas fa-sign-in-alt"></i></button>
	  <?php
			if(isset($_SESSION['msg_login'])){
				echo
					"<div class='col-12'>
						<div class='alert alert-danger alert-dismissible fade show' role='alert'>"
							.$_SESSION['msg_login'].
							"<button type='button' class='close' data-dismiss='alert' aria-label='Close'>
								<span aria-hidden='true'>&times;</span>
							</button>
						</div>
					</div>";
				unset($_SESSION['msg_login']);	
			}
		?>
      <p class="mt-5 mb-3 text-muted"><i class="far fa-copyright"></i> New Norte Informática</p>
    </form>
	
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  </body>
</html>