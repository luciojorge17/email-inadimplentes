<?php
setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');
session_start();
include_once('login/protect.php');
protect();
include_once 'conexao.php';
if (!isset($_SESSION['assunto'])) {
  $_SESSION['assunto'] = "[nome_empresa]";
}
if (!isset($_SESSION['cabecalho'])) {
  $_SESSION['cabecalho'] = "Prezado(a) [cliente]";
}
if (!isset($_SESSION['mensagem'])) {
  $_SESSION['mensagem'] = "Seu boleto no valor de R$ [valor] venceu há [numero_dias] dias ([vencimento]). Se já efetuou o pagamento, desconsidere este aviso.";
}
if (!isset($_SESSION['rodape'])) {
  $_SESSION['rodape'] = "[nome_empresa], [hoje_extenso]";
}
?>
<!doctype html>
<html lang="pt-br">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="icon" href="images/favicon.gif" />
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
  <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" rel="stylesheet">
  <title>New Norte - Envio de e-mails</title>
  <style>
    body {
      font-family: 'Roboto', sans-serif;
    }

    #conteudo {
      overflow-y: scroll;
      height: 100vh;
      max-height: 100vh;
    }

    .form-check-input {
      margin-left: 0;
    }

    #filtrar label,
    #filtrar input,
    #filtrar select {
      font-size: 13px;
    }

    .filtros {
      background-color: #F2F2F2;
      max-height: 100vh;
    }

    table {
      font-size: 13px;
    }

    table thead {
      background-color: #FAFAFA;
      text-transform: uppercase;
    }

    #ajuda p {
      font-size: 0.9em;
      color: #585858;
    }

    @media (max-width:767px) {
      .filtros {
        min-height: auto;
      }
    }

    @media print {
      #conteudo {
        overflow-y: hidden;
        height: initial;
        max-height: initial;
      }

      table tbody th,
      td {
        color: black !important;
      }
    }

    .bg-sucesso {
      background: #A9F5D0 !important;
    }

    .bg-erro {
      background: #F5A9A9 !important;
    }
  </style>
</head>

<body>
  <div class="container-fluid">
    <div class="row">
      <div id="sidebar" class="col-sm-12 col-md-12 col-lg-2 pt-3 pb-3 filtros d-print-none">
        <img id="logo" src="images/logo-preto.png" alt="Logo New Norte" class="img img-fluid mx-auto mb-3">
        <form id="filtrar" action="#">
          <div class="form-row">
            <div class="form-group col-12">
              <label for="slcEmpresa">Empresa</label>
              <select name="slcEmpresa" id="slcEmpresa" class="form-control">
                <option value="">Todas</option>
                <?php
                $sql = 'SELECT cd_empresa, ds_empresa FROM tbl_empresas WHERE x_ativa = 1 ORDER BY cd_empresa';
                $consulta = odbc_exec($conexao, $sql);
                while ($linha = odbc_fetch_array($consulta)) {
                  echo '<option value="' . $linha['cd_empresa'] . '">' . $linha['cd_empresa'] . ' - ' . utf8_encode($linha['ds_empresa']) . '</option>';
                }
                ?>
              </select>
            </div>
            <div class="form-group col-12">
              <label for="slcFilial">Filial</label>
              <select name="slcFilial" id="slcFilial" class="form-control">
              </select>
            </div>
            <div class="form-group col-12">
              <label for="slcCarteira">Carteira da parcela</label>
              <select name="slcCarteira" id="slcCarteira" class="form-control">
                <option value="">Todas</option>
                <?php
                $sql = 'SELECT cd_carteira, ds_carteira FROM tbl_financeiro_carteira WHERE x_ativo = 1 ORDER BY cd_carteira';
                $consulta = odbc_exec($conexao, $sql);
                while ($linha = odbc_fetch_array($consulta)) {
                  echo '<option value="' . $linha['cd_carteira'] . '">' . $linha['cd_carteira'] . ' - ' . utf8_encode($linha['ds_carteira']) . '</option>';
                }
                ?>
              </select>
            </div>
            <div class="form-group col-12">
              <label for="dataInicial">Data Inicial</label>
              <input type="date" class="form-control" id="dataInicial" name="dataInicial" value="<?php echo date('Y-m-d'); ?>" required>
            </div>
            <div class="form-group col-12">
              <label for="dataFinal">Data Final</label>
              <input type="date" class="form-control" id="dataFinal" name="dataFinal" value="<?php echo date('Y-m-d'); ?>" required>
            </div>
          </div>
          <button id="listar-titulos" type="submit" class="btn btn-block btn-success"><i class="fas fa-list-ol"></i> Listar títulos</button>
        </form>
        <button id="btn-envio" class="btn btn-block btn-danger d-none mt-2"><i class="far fa-paper-plane"></i> Enviar E-mail</button>
        <button type="button" id="btn-configuracao" class="btn btn-secondary btn-block mt-5" data-toggle="modal" data-target="#configura">
          <i class="fa fa-cog"></i> Configuração
        </button>
        <button type="button" class="btn btn-outline-info btn-block mt-2" data-toggle="modal" data-target="#ajuda">
          <i class="far fa-question-circle"></i> Ajuda
        </button>
      </div>
      <div id="conteudo" class="col-sm-12 col-md-12 col-lg-10 pt-3 pb-1">
        <div class="row">
          <div class="col-12 col-md-12 mt-2 text-right">
            <p><small>Usuário: <?php echo $_SESSION['nome_usuario']; ?></small> <a id="btn-logout" class="btn btn-sm btn-danger d-print-none" href="login/logout.php">Sair</a> <button id="printButton" type="button" class="btn btn-sm btn-primary d-print-none"><i class="fas fa-print"></i> Imprimir</button></p>
          </div>
        </div>
        <hr>
        <div id="resultado"></div>
      </div>
      <!-- Modal ajuda -->
      <div class="modal fade" id="ajuda" tabindex="-1" role="dialog" aria-labelledby="ajuda-title" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="ajuda-title"><i class="fas fa-question-circle"></i> Ajuda</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <p><strong>Rotina:</strong> Selecione os filtros de Data inicial e Data final, depois clique em [<i class="fas fa-list-ol"></i> Listar títulos]. Após esse processo, clique
                em [<i class="far fa-paper-plane"></i> Enviar E-mail] para enviar o e-mail para os títulos pesquisados.</p>
              <p>Você pode alterar a mensagem padrão a ser enviada no e-mail clicando em [<i class="fa fa-cog"></i> Configuração]. Altere o que desejar e clique em [Alterar].</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal"><i class="fas fa-times-circle"></i> Fechar</button>
            </div>
          </div>
        </div>
      </div>
      <!-- Modal configura -->
      <div class="modal fade" id="configura" tabindex="-1" role="dialog" aria-labelledby="configura-title" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="configura-title"><i class="fa fa-cog"></i> Configurar</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form id="configurar" action="configurar.php" method="POST">
                <div class="form-group">
                  <label for="assunto">Assunto do e-mail</label>
                  <input type="text" class="form-control form-control-sm" name="assunto" id="assunto" value="<?php echo $_SESSION['assunto']; ?>" required>
                </div>
                <div class="form-group">
                  <label for="cabecalho">Cabeçalho</label>
                  <input type="text" class="form-control form-control-sm" name="cabecalho" id="cabecalho" value="<?php echo $_SESSION['cabecalho']; ?>" required>
                </div>
                <div class="form-group">
                  <label for="msg">Corpo e-mail</label>
                  <textarea class="form-control form-control-sm" name="msg" id="msg" rows="5" required><?php echo $_SESSION['mensagem']; ?></textarea>
                </div>
                <div class="form-group">
                  <label for="rodape">Rodapé</label>
                  <input type="text" class="form-control form-control-sm" name="rodape" id="rodape" value="<?php echo $_SESSION['rodape']; ?>" required>
                </div>
                <button type="submit" class="btn btn-primary">Alterar</button>
                <button type="button" class="btn btn-outline-secondary" data-container="body" data-toggle="popover" data-placement="right" data-content="[cliente]<br>[hoje_data]<br>[hoje_extenso]<br>[numero_dias]<br>[numero_parcela]<br>[valor]<br>[vencimento]">
                  Listar variáveis
                </button>
              </form>
              <div id="alterado"></div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal"><i class="fas fa-times-circle"></i> Fechar</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="https://code.jquery.com/jquery-3.4.0.min.js" integrity="sha256-BJeo0qm959uMBGb65z40ejJYGSgR7REI4+CW1fNKwOg=" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>

  <script>
    $(function() {
      $('[data-toggle="popover"]').popover({
        html: true
      });
    })
  </script>

  <script type="text/javascript">
    $('label[for="slcFilial"]').hide();
    $('#slcFilial').hide();
    var carregando = `
      <div class="col-12 mt-5 text-center">
        <lottie-player src="https://assets3.lottiefiles.com/packages/lf20_Z4BhGL.json"  background="transparent"  speed="1"  style="height: 150px;"  loop  autoplay></lottie-player>
      </div>
    `;
    var mailLoader = '<i class="fas fa-spinner fa-pulse"></i>';

    $('#slcEmpresa').on('change', function() {
      var id = $(this).val();
      $('#slcFilial').empty();
      var option = `<option value="">Todas</option>`;
      $('#slcFilial').append(option);
      if (id == '') {
        $('label[for="slcFilial"]').hide();
        $('#slcFilial').hide();
      } else {
        $('label[for="slcFilial"]').show();
        $('#slcFilial').show();
        listarFiliais(id);
      }
    })

    function listarFiliais(idEmpresa) {
      $.ajax({
        url: 'lista_filiais.php',
        type: 'post',
        data: {
          idEmpresa
        }
      }).done(function(data) {
        var response = JSON.parse(data);
        $.each(response, function(i, filial) {
          var option = `<option value="${filial.id}">${filial.id} - ${filial.nome}</option>`;
          $('#slcFilial').append(option);
        });
      });
    }

    function marcarTodos() {
      $('input[type="checkbox"]').prop('checked', true);
    }

    function desmarcarTodos() {
      $('input[type="checkbox"]').prop('checked', false);
    }

    function enviarEmail() {
      var emails = $('input:checked[name="selecionados[]"]');
      if (emails.length > 0) {
        $.each(emails, function(index, email) {
          var arr = email.value.split('&sep&');
          $.ajax({
            url: 'enviarEmail.php',
            type: 'post',
            data: {
              dados: email.value
            },
            beforeSend: function() {
              $(`#${arr[0]} .mailLoader`).html(mailLoader);
            }
          }).done(function(data) {
            $(`#${arr[0]} .mailLoader`).empty();
            var response = JSON.parse(data);
            if (response.status == 1) {
              $(`#${arr[0]}`).addClass('bg-sucesso');
            } else {
              $(`#${arr[0]}`).addClass('bg-erro');
            }
          });
        });
      } else {
        alert('Selecione ao menos uma linha');
      }
    }

    $(function() {
      $("#filtrar").submit(function(event) {
        event.preventDefault();
        var form = new FormData(this);
        $.ajax({
          url: 'lista_titulos.php',
          type: 'post',
          data: form,
          cache: false,
          processData: false,
          contentType: false,
          beforeSend: function() {
            $('#resultado').html(carregando);
          }
        }).done(function(data) {
          var response = JSON.parse(data);
          $('#resultado').empty();
          if (response.status == 1) {
            var contador = 1;
            var buttons = `
            <div class="row">
              <div class="col-6 mb-2">
                <button class="btn btn-sm btn-outline-danger" onclick="desmarcarTodos()">Desmarcar todos</button>
                <button class="btn btn-sm btn-outline-success" onclick="marcarTodos()">Marcar todos</button>
              </div>
              <div class="col-6 mb-2 text-right">
                <button class="btn btn-sm btn-success" onclick="enviarEmail()">Enviar e-mails <i class="fas fa-paper-plane"></i></button>
              </div>
            </div>`;
            $('#resultado').append(buttons);
            var table = `
              <div class="col-12">
                <table class="table table-sm table-hover table-responsive w-100 d-block d-md-table">
                  <thead>
                    <tr>
                      <th scope="col"></th>
                      <th scope="col">#</th>
                      <th scope="col">Emp.</th>
                      <th scope="col">Fl.</th>
                      <th scope="col">Lanç.</th>
                      <th scope="col">Pr.</th>
                      <th scope="col">Cliente</th>
                      <th scope="col">E-mail</th>
                      <th scope="col" class="text-center">Vencimento</th>
                      <th scope="col" class="text-center">Atr</th>
                      <th scope="col" class="text-right">Valor</th>
                    </tr>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
              </div>`;
            $('#resultado').append(table);
            $.each(response.linhas, function(index, linha) {
              var tr = `
              <tr id="linha${contador}">
                <td class="mailLoader"></td>
                <td>
                  <input checked type="checkbox" class="form-check-input" name="selecionados[]" value="linha${contador}&sep&${linha.empresa}&sep&${linha.lancamento}&sep&${linha.numeroParcela}&sep&${linha.cliente}&sep&${linha.email}&sep&${linha.dataVencimento}&sep&${linha.diasAtraso}&sep&${linha.valorParcela}">
                </td>
                <td>${linha.empresa}</td>
                <td>${linha.filial}</td>
                <td>${linha.lancamento}</td>
                <td>${linha.numeroParcela}</td>
                <td>${linha.cliente}</td>
                <td>${linha.email}</td>
                <td class="text-center">${linha.dataVencimento}</td>
                <td class="text-center">${linha.diasAtraso}</td>
                <td class="text-right">${linha.valorParcela}</td>
              </tr>`;
              $('#resultado table tbody').append(tr);
              contador++;
            });
          } else {
            var div =
              `<div class="col-12 text-center">Nenhum lançamento encontrado</div>`;
            $('#resultado').html(div);
          }
        });
      });
    });
  </script>
  <script type="text/javascript">
    $(function() {
      var request;
      $("#configurar").submit(function(event) {
        event.preventDefault();
        if (request) {
          request.abort();
        }
        var $form = $(this);
        var $inputs = $form.find("input, select, button, textarea");
        var serializedData = $form.serialize();
        $inputs.prop("disabled", true);
        request = $.ajax({
          url: "configurar.php",
          type: "post",
          data: serializedData
        });
        request.done(function(response, textStatus, jqXHR) {
          $("#alterado").html(response)
        });
        request.fail(function(jqXHR, textStatus, errorThrown) {
          console.error(
            "The following error occurred: " +
            textStatus, errorThrown
          );
        });
        request.always(function() {
          $inputs.prop("disabled", false);
        });
      });
    });
    $('#formListaDeTitulos').on('submit', function(e) {
      e.preventDefault();
      var form = new FormData($(this)[0]);
      $.ajax({
        url: 'mail.php',
        type: 'post',
        processData: false,
        cache: false,
        data: form,
      }).done(function(data) {
        let response = JSON.parse(data);
        console.log = (response);
      });
    })
  </script>
  <script>
    $('#btn-envio').click(function() {
      $(this).html('<i class="fas fa-paper-plane"></i> Aguarde...').prop('disabled', true);
      $(this).addClass("disabled");
      $('#listar-titulos').addClass("disabled");
      $('#btn-configuracao').addClass("disabled");
      $('#btn-logout').addClass("disabled");
      $('#formListaDeTitulos').submit();
    });
  </script>
  <script>
    $('#printButton').click(function() {
      window.print();
    });
  </script>
</body>

</html>