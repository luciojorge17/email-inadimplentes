<?php
include_once 'conexao.php';
$idEmpresa = $_POST['idEmpresa'];
$filiais = [];
$sql = "SELECT cd_filial, ds_filial FROM tbl_empresas_filiais WHERE x_ativa = 1 AND cd_empresa = $idEmpresa ORDER BY cd_filial";
$consulta = odbc_exec($conexao, $sql);
while ($linha = odbc_fetch_array($consulta)) {
  array_push($filiais, [
    "id" => $linha['cd_filial'],
    "nome" => utf8_encode($linha['ds_filial'])
  ]);
}
echo json_encode($filiais);
