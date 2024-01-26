<?php session_start();
    include_once ('../../funcoesAuxiliares.php');
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Publicação de Pautas", "publicacaoPautas");
    $includarHtmls = new includarHtmls();
    $janelaMensagens = new janelaMensagens();
    $conexaoFolhas = new conexaoFolhas();
    $verificacaoAcesso = new verificacaoAcesso();
    $layouts = new layouts();
    $manipulacaoDados->retornarAnosEmJavascript();
    $layouts->idPArea = $manipulacaoDados->idPArea;
    $layouts->designacaoArea = $manipulacaoDados->designacaoArea;
 ?>

 <!DOCTYPE html>
<html lang="pt">

<head>
  <?php $conexaoFolhas->folhasCss();?>
</head>

<body>
  <?php
    $janelaMensagens->processar (); 
    $layouts->cabecalho();
    $layouts->aside();
  ?>

  <section id="main-content"> 
        <section class="wrapper" id="containers">
          <div class="row">
            <div class="col-lg-12 col-md-12">
              <nav role="navigation" class="navbar navbar-inverse paternSubMenuInterno" > 

                  <a data-toggle="dropdown" class="dropdown-toggle navbar-brand chamMenuInterno"   href="#">
                                        <b class="caret"></b>
                                    </a>
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-eyes"></i> Publicação de Pautas</strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso("", ["publicacaoPautas"], array(), "msg")){

          echo "<script>var mesesAnoLectivo =".json_encode($manipulacaoDados->mesesAnoLectivo)."</script>";
          echo "<script>var listaTurma = ".json_encode($manipulacaoDados->turmasEscola())."</script>";
           ?> 
                                    
          <div class="table-responsive">

                <table id="example1" class="table table-striped table-bordered table-hover" >
                    <thead> 
                      <tr class="corPrimary">                 
                        <td class="lead"></td>
                        <td class="text-center lead">Iº Trimestre<div class="switch"><label class="lead"><input type="checkbox" style="margin-left: -15px;" referencia="0-1" class="altEstado"><span class="lever"></span></label></div><?php echo listarMeses($manipulacaoDados, "0-1") ?></td>
                        <td class="text-center lead">IIº Trimestre<div class="switch"><label class="lead"><input type="checkbox" style="margin-left: -15px;" referencia="0-2" class="altEstado"><span class="lever"></span></label></div><?php echo listarMeses($manipulacaoDados, "0-2") ?></td>
                        <td class="text-center lead">IIIº Trimestre<div class="switch"><label class="lead"><input type="checkbox" style="margin-left: -15px;" referencia="0-3" class="altEstado"><span class="lever"></span></label></div><?php echo listarMeses($manipulacaoDados, "0-3") ?></td>
                        <td class="text-center lead">Pauta Final<div class="switch"><label class="lead"><input type="checkbox" style="margin-left: -15px;" referencia="0-4" class="altEstado"><span class="lever"></span></label></div><?php echo listarMeses($manipulacaoDados, "0-4") ?></td>
                      </tr>
                    </thead>
                    <tbody id="tabela">
                    </tbody>
                    
                </table>  
          </div><br>

        <?php } echo "</div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha();
  

  function listarMeses($m, $referencia){
    $retorno="<select referencia='".$referencia."'><option value='0'>Para todos</option>";
    foreach ($m->mesesAnoLectivo as $m){
      $retorno .="<option value='".$m."'>".nomeMes($m)."</option>";
    }
    return $retorno."</select>";
  }



 ?>