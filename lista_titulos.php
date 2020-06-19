<?php
include_once('conexao.php');
session_start();
$linhas = [];
$empresa = !empty($_POST['slcEmpresa']) ? "AND CRP.CD_EMPRESA = " . $_POST['slcEmpresa'] : "";
$filial = !empty($_POST['slcFilial']) ? "AND CRP.CD_FILIAL = " . $_POST['slcFilial'] : "";
$carteira = !empty($_POST['slcCarteira']) ? "AND CRP.CD_CARTEIRA = " . $_POST['slcCarteira'] : "";

$hoje = date('Y/m/d');

$arrInicial = explode('-', $_POST['dataInicial']);
$ddi = $arrInicial[2];
$mdi = $arrInicial[1];
$adi = $arrInicial[0];
$dataInicial = $ddi . '/' . $mdi . '/' . $adi;
$_SESSION['data_inicial'] = $_POST['dataInicial'];

$arrFinal = explode('-', $_POST['dataFinal']);
$ddf = $arrFinal[2];
$mdf = $arrFinal[1];
$adf = $arrFinal[0];
$dataFinal = $ddf . '/' . $mdf . '/' . $adf;

$consulta = "
    SELECT CRP.CD_EMPRESA, CRP.CD_FILIAL, CRP.CD_LANCAMENTO, CRP.CD_CLIENTE, CRP.DS_CLIENTE, CLI.DS_EMAIL, CLI.DS_EMAIL_BOLETO, CRP.NR_PARCELA, GETDATE() DT_HOJE, CRP.DT_VENCIMENTO, DATEDIFF(DAY, CRP.DT_VENCIMENTO, GETDATE()) AS DIAS, CRP.VL_PARCELA,
        (SELECT SUM(CRB2.VL_PAGAMENTO) 
            FROM SEL_FINANCEIRO_TITULOS_ARECEBER_BAIXAS AS CRB2
            LEFT JOIN SEL_FINANCEIRO_TITULOS_ARECEBER_PARCELAS AS CRP2 ON CRP2.CD_LANCAMENTO = CRB2.CD_LANCAMENTO AND CRP2.NR_PARCELA = CRB2.NR_PARCELA  
                WHERE CRP.CD_LANCAMENTO = CRP2.CD_LANCAMENTO AND CRP.NR_PARCELA = CRB2.NR_PARCELA $empresa $filial $carteira
                    GROUP BY CRP2.CD_LANCAMENTO, CRP2.NR_PARCELA) VL_PAGO
    FROM SEL_FINANCEIRO_TITULOS_ARECEBER_PARCELAS AS CRP
        INNER JOIN SEL_CLIENTES AS CLI ON CRP.CD_CLIENTE = CLI.CD_ENTIDADE
        LEFT JOIN SEL_FINANCEIRO_TITULOS_ARECEBER_BAIXAS AS CRB ON CRP.CD_LANCAMENTO = CRB.CD_LANCAMENTO AND CRP.NR_PARCELA = CRB.NR_PARCELA
            WHERE CRP.X_BOLETO_AGRUPADO = 0 AND (CRB.VL_SALDO IS NULL OR CRB.VL_SALDO > 0 ) AND CRP.DT_VENCIMENTO BETWEEN CONVERT(DATETIME,'" . $_POST['dataInicial'] . "',102) AND CONVERT(DATETIME,'" . $_POST['dataFinal'] . "',102) $empresa $filial $carteira
                GROUP BY CRP.CD_EMPRESA, CRP.CD_FILIAL, CRP.CD_CARTEIRA, CRP.CD_LANCAMENTO, CRP.CD_CLIENTE, CRP.DS_CLIENTE, CLI.DS_EMAIL, CLI.DS_EMAIL_BOLETO, CRP.NR_PARCELA, CRP.DT_VENCIMENTO, CRP.VL_PARCELA
                    ORDER BY CRP.DT_VENCIMENTO";
$resultado = odbc_exec($conexao, $consulta);
if (odbc_num_rows($resultado) == 0) {
    echo json_encode(["status" => 0]);
    exit;
}
while ($linha = odbc_fetch_array($resultado)) {
    $emails = str_replace(';', '<br>', utf8_encode(strtolower($linha['DS_EMAIL'])));
    $emails = str_replace(',', '<br>', $emails);
    $emails = str_replace(' ', '', $emails);
    array_push($linhas, [
        "empresa" => $linha['CD_EMPRESA'],
        "filial" => $linha['CD_FILIAL'],
        "lancamento" => $linha['CD_LANCAMENTO'],
        "numeroParcela" => $linha['NR_PARCELA'],
        "cliente" => utf8_encode(ucwords(strtolower($linha['DS_CLIENTE']))),
        "email" => $emails,
        "dataVencimento" => date('d/m/Y', strtotime($linha['DT_VENCIMENTO'])),
        "diasAtraso" => $linha['DIAS'],
        "valorParcela" => number_format($linha['VL_PARCELA'], 2, ',', '.')
    ]);
}
echo json_encode(["status" => 1, "linhas" => $linhas]);
