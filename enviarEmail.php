<?php
session_start();
include 'envia_email.php';
setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');
$hoje = utf8_encode(ucfirst(strftime('%d de %B de %Y', strtotime('today'))));
$hoje_data = date('d/m/Y');
$dados = explode("&sep&", $_POST['dados']);
$idEmpresa = $dados[1];
$lancamento = $dados[2];
$numParcela = $dados[3];
$cliente = $dados[4];
$email = $dados[5];
$vencimento = $dados[6];
$diasAtraso = $dados[7];
$valor = $dados[8];

$text = "";
$text .= "<h3>" . $_SESSION['cabecalho'] . "</h3>";
$text .= "<p>" . $_SESSION['mensagem'] . "</p>";
$text .= "<p>" . $_SESSION['rodape'] . "</p>";

$assunto = str_replace("[hoje_extenso]", $hoje, str_replace("[hoje_data]", $hoje_data, str_replace("[numero_parcela]", $numParcela, str_replace("[numero_dias]", $diasAtraso, str_replace("[valor]", $valor, str_replace("[vencimento]", $vencimento, str_replace("[cliente]", $cliente, $_SESSION['assunto'])))))));

$msg_temp = str_replace("[hoje_extenso]", $hoje, str_replace("[hoje_data]", $hoje_data, str_replace("[numero_parcela]", $numParcela, str_replace("[numero_dias]", $diasAtraso, str_replace("[valor]", $valor, str_replace("[vencimento]", $vencimento, str_replace("[cliente]", $cliente, $text)))))));
$mensagem =
  '
        <html>
        <style>
            body{
                font-family:Arial, Helvetica, sans-serif;
                text-align:center;
            }
            div{
                margin:20px;
                padding:25px;
            }
            h3{
                color:#424242;
                font-size:1.3em;
            }
            p{
                color:#848484;
                font-size:1em;
            }
            h4{
                color:#585858;
                font-size:1.1em;
            }    
        </style>
        <body>
        <div>
        ' . $msg_temp . '
        </div>
        </body>
        </html>
  ';

$destinatarios = explode('<br>', $email);
$count = 0;
for ($i = 0; $i < sizeof($destinatarios); $i++) {
  if (enviaEmail($idEmpresa, $destinatarios[$i], $cliente, $assunto, $mensagem)) {
    $count++;
  }
}

if ($count > 0) {
  echo json_encode(["status" => 1]);
} else {
  echo json_encode(["status" => 0]);
}
