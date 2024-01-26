<?php session_start();       
  
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Emolumentos", "emolumentos");
    $includarHtmls = new includarHtmls();
    $janelaMensagens = new janelaMensagens();
    $conexaoFolhas = new conexaoFolhas();
    $verificacaoAcesso = new verificacaoAcesso();
    $layouts = new layouts();
    $layouts->idPArea = $manipulacaoDados->idPArea;
    $layouts->designacaoArea = $manipulacaoDados->designacaoArea;
    $manipulacaoDados->listaClassesPorCurso();
 ?>

 <!DOCTYPE html>
<html lang="pt">

<head>
  <?php $conexaoFolhas->folhasCss();?>
  <style type="text/css">
    table tr td{
      font-size: 18px !important;
      padding: 0px !important;
    }
    .main-body h1{
      font-size: 26px;
      font-weight: 510;
    }
  </style>
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
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-dollar-sign"></i> Emolumentos</strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso("", "emolumentos", array(), "msg")){ 


            $idPTipoEmolumento = isset($_GET["idPTipoEmolumento"])?$_GET['idPTipoEmolumento']:$manipulacaoDados->selectUmElemento("tipos_emolumentos", "idPTipoEmolumento", [], [], [], ["designacaoEmolumento"=>1]);
            echo "<script>var idPTipoEmolumento='".$idPTipoEmolumento."'</script>";
            $array = $manipulacaoDados->selectArray("tipos_emolumentos", [], ["idPTipoEmolumento"=>$idPTipoEmolumento]);


            echo "<script>var codigoEmolumento='".valorArray($array, "codigo")."'</script>";

            echo "<script>var tabelaprecos =".$manipulacaoDados->selectJson("escolas",["emolumentos.idTipoEmolumento", "emolumentos.codigoEmolumento", "emolumentos.classe", "emolumentos.idCurso", "emolumentos.mes", "emolumentos.valor"], ["idPEscola"=>$_SESSION['idEscolaLogada'], "emolumentos.idTipoEmolumento"=>$idPTipoEmolumento], ["emolumentos"])."</script>";

            $listaCursos = $manipulacaoDados->selectArray("nomecursos", [], ["cursos.idCursoEscola"=>$_SESSION['idEscolaLogada'], "cursos.estadoCurso"=>"A"], ["cursos"], "", [], ["nomeCurso"=>1]);
          ?> 

          <div class="row">
            <div class="col-lg-4 col-md-4 lead">
              <label>Emolumento</label>
              <select class="form-control" id="idPTipoEmolumento">
                <?php 
                foreach($manipulacaoDados->selectArray("tipos_emolumentos", [], [], [], "", [], ["designacaoEmolumento"=>1]) as $a){
                  echo "<option value='".$a["idPTipoEmolumento"]."'>".$a["designacaoEmolumento"]."</option>";
                }

                 ?>
              </select>
            </div>
            <div class="col-lg-1 col-md-1 col-sm-2 col-xs-2 "><br>
              <a href="#" title="Actualizar" id="actualizar"><i class="fa fa-spinner fa-2x"></i></a>
            </div>
          </div>

          <?php if(valorArray($array, "tipoPagamento")=="pagMensal"){ ?>

        <div class="row">
          <div class="col-lg-6 col-md-6 col-lg-offset-3 col-md-offset-3">
            <div class="row">
              <div class="col-lg-6 col-md-6">
                <label>Curso:</label>
                <select class="form-control lead" id="idPCurso">
                  <?php 
                    foreach($listaCursos as $curso){ 
                      echo "<option value='".$curso["idPNomeCurso"]."' sePorSemestre='".$curso["sePorSemestre"]."'>".$curso["nomeCurso"]." (".$curso["areaFormacaoCurso"].")</option>";
                    }
                    
                  ?>
                </select>
              </div>
              <div class="col-lg-4 col-md-4">
                <label>Classe:</label>
                <select class="form-control lead" id="classe">
                  <optgroup id='listaClasses'></optgrup>
                </select>
                 
              </div>
            </div>
            <table class="table table-striped table-bordered table-hover" style="border:solid black 2px;" id="tabPrecoMensalidades">
              <tr >
                <td style="background-color: #428bca !important;">Mês</td>
                <td style="background-color: #428bca !important;">Valor</td>
              </tr>           
              <?php
                foreach($manipulacaoDados->mesesAnoLectivo as $a){
                  echo '<tr><td class="lead text-center">'.nomeMes($a).'</td><td class="lead text-center valor" id="mes'.$a.'"></td>
                    </tr>';
                }
               ?>
            </table>
          </div>
        </div>
        <?php }else{?>
        <div class="row">
          <div class="col-lg-6 col-md-6 col-lg-offset-3 col-md-offset-3">
              <table class="table table-striped table-bordered table-hover" id="tabPagamentos" style="border: solid black 2px;">
                
                <tr >
                  <td style="background-color: #428bca !important;">Referência</td>
                  <td style="background-color: #428bca !important;">Valor</td>
                </tr>
                <?php 
                foreach ($listaCursos as $curso) {
                  echo '
                  <tr class="text-center"><td colspan="2"><strong>'.$curso["nomeCurso"].'</strong></td></tr>';
                  foreach(listarItensObjecto($curso, "classes") as $classe){

                    echo '
                    <tr><td>'.$classe["designacao"].'</td><td class="text-center" id="preco_'.$classe["identificador"].'-'.$curso["idPNomeCurso"].'"></td></tr>';
                  }
                  if(valorArray($array, "codigo")=="declaracao"){
                    echo '
                      <tr><td>Certificado</td><td class="text-center" id="preco_120-'.$curso["idPNomeCurso"].'"></td></tr>';
                      echo '
                      <tr><td>Diploma</td><td class="text-center" id="preco_1200-'.$curso["idPNomeCurso"].'"></td></tr>';
                  }
                }
                  
                
                 ?>
              </table>
          </div>
        </div>
      <?php } ?>

        <?php  } echo "</div></div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha(); ?>


<div class="modal fade" id="formPrecos" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
      <form class="modal-dialog" id="formPrecosForm">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"><i class="fa fa-dollar-sign"></i> Emolumentos</h4>
              </div>

              <div class="modal-body">

                  <div class="row">
                      <div class="col-md-4 col-md-offset-4 text-center lead">
                        <strong>Preço Único</strong>
                        <input type="number" name="precoUnico" id="precoUnico" class="form-control lead bolder text-center">
                      </div>
                      <hr>
                  </div>
                  <div class="row" id="outrosPagamentos">

              <?php

                  $classes="";
                  $classesId="";
                  foreach ($listaCursos as $curso) { ?>
                  <div class="row">
                    <div class="col-lg-12 col-md-12 lead">
                    <div class="col-lg-12 col-md-12 lead"><strong><?php echo $curso["nomeCurso"]; ?></strong></div>
                    <div class="col-lg-12" style="padding-left: 20px;">
                   <?php foreach(listarItensObjecto($curso, "classes") as $classe){ ?>

                      <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 outrosPagamentos">
                        <label><?php echo $classe["designacao"]; ?></label>:
                        <input type="number" class="form-control lead text-center outrosPagamentos" id="<?php echo 'preco_'.$classe["identificador"].'_'.$curso["idPNomeCurso"]; ?>" required step="5" min="0" valor="<?php echo $classe["identificador"].'_'.$curso["idPNomeCurso"]; ?>">
                      </div>

                  <?php /* if($i==((int)$curso["duracao"]+9)){ ?>
                      <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 declaracao">
                        <label>Certificado:</label>:
                        <input type="number" class="form-control lead text-center declaracao" id="preco_120_<?php echo $curso["idPNomeCurso"]; ?>" step="5" required min="0" valor="120_<?php echo $curso["idPNomeCurso"]; ?>">
                      </div>
                      <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 declaracao">
                        <label>Diploma</label>:
                        <input type="number" class="form-control lead text-center declaracao outrosPagamentos" id="preco_1200_<?php echo $curso["idPNomeCurso"]; ?>" step="5" required min="0" valor="1200_<?php echo $curso["idPNomeCurso"]; ?>">
                      </div>
                   <?php }*/ } ?>
                  </div>
                </div>
                </div>
                <?php }?>
                </div>

                <div class="row" id="paraMensalidades">
                    <div class="col-lg-12 lead " ><strong class="titulo"></strong></div><br/>

                    <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                      <label><?php echo nomeMes(1).":"; ?></label>
                      <input type="number" class="form-control lead text-center" id="preco1" step="5" required min="0" valor="1">
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                      <label><?php echo nomeMes(2).":"; ?></label>
                      <input type="number" class="form-control lead text-center" id="preco2" step="5" required min="0" valor="2">
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                      <label><?php echo nomeMes(3).":"; ?></label>
                      <input type="number" class="form-control lead text-center" id="preco3" step="5" required min="0" valor="3">
                    </div>

                    <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                      <label><?php echo nomeMes(4).":"; ?></label>
                      <input type="number" class="form-control lead text-center" id="preco4" step="5" required min="0" valor="4">
                    </div>

                    <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                      <label><?php echo nomeMes(5).":"; ?></label>
                      <input type="number" class="form-control lead text-center" id="preco5" step="5" required min="0" valor="5">
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                      <label><?php echo nomeMes(6).":"; ?></label>
                      <input type="number" class="form-control lead text-center" id="preco6" step="5" required min="0" valor="6">
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                      <label><?php echo nomeMes(7).":"; ?></label>
                      <input type="number" class="form-control lead text-center" id="preco7" step="5" required min="0" valor="7">
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                      <label><?php echo nomeMes(8).":"; ?></label>
                      <input type="number" class="form-control lead text-center" id="preco8" step="5" required min="0" valor="8">
                    </div>

                    <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                      <label><?php echo nomeMes(9).":"; ?></label>
                      <input type="number" class="form-control lead text-center" id="preco9" step="5" required min="0" valor="9">
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                      <label><?php echo nomeMes(10).":"; ?></label>
                      <input type="number" class="form-control lead text-center" id="preco10" step="5" required min="0" valor="10">
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                      <label><?php echo nomeMes(11).":"; ?></label>
                      <input type="number" class="form-control lead text-center" id="preco11" step="5" required min="0" valor="11">
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                      <label><?php echo nomeMes(12).":"; ?></label>
                      <input type="number" class="form-control lead text-center" id="preco12" step="5" required min="0" valor="12">
                    </div>
                </div>
              </div>
              <div class="modal-footer">
                  <div class="row">
                    <div class="col-lg-5 col-md-5 text-left">
                      <button type="submit" class="btn btn-success btn lead btn-lg "><i class="fa fa-check"></i> Alterar</button>
                    </div>                    
                  </div>                
              </div>
            </div>
          </form>
      </div>



