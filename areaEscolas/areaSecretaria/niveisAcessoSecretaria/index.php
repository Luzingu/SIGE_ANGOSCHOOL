<?php session_start();
     include_once ('../../funcoesAuxiliares.php');
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Níveis de Acesso");
    $includarHtmls = new includarHtmls();
    $janelaMensagens = new janelaMensagens();
    $conexaoFolhas = new conexaoFolhas();
    $verificacaoAcesso = new verificacaoAcesso();
    $layouts = new layouts();

    $idPArea = isset($_GET["idPArea"])?$_GET["idPArea"]:"";
    $areas = $manipulacaoDados->selectArray("areas", ["designacaoArea"], ["idPArea"=>$idPArea, "instituicoes.idEscola"=>$_SESSION['idEscolaLogada']], ["instituicoes"]);
    if(count($areas)<=0){
      $areas = $manipulacaoDados->selectArray("areas", ["designacaoArea", "idPArea"], ["instituicoes.idEscola"=>$_SESSION['idEscolaLogada']], ["instituicoes"], 1);
      $idPArea = valorArray($areas, "idPArea");
    }
    echo "<script>var idPArea='".$idPArea."'</script>";
    echo "<script>var idPEscola='".$_SESSION['idEscolaLogada']."'</script>";

    $layouts->idPArea=valorArray($areas, "idPArea");
    $manipulacaoDados->idPArea=valorArray($areas, "idPArea");
    $layouts->designacaoArea=valorArray($areas, "designacaoArea");
    $manipulacaoDados->retornarAnosEmJavascript();
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
    $layouts->aside($idPArea);
  ?>

  <section id="main-content"> 
        <section class="wrapper" id="containers">
          <div class="row">
            <div class="col-lg-12 col-md-12">
              <nav role="navigation" class="navbar navbar-inverse paternSubMenuInterno" > 

                  <a data-toggle="dropdown" class="dropdown-toggle navbar-brand chamMenuInterno"   href="#">
                                        <b class="caret"></b>
                                    </a>
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-blind"></i> Níveis de Acesso</strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso($idPArea)){

         ?> 
      
    <div class="card">
        <div class="card-body"> 

          <?php 

          $acessos = array();
          foreach($manipulacaoDados->selectArray("menus", [], ["instituicoes.idEscola"=>$_SESSION['idEscolaLogada'], "instituicoes.idArea"=>$idPArea, "identificadorMenu"=>array('$nin'=>["nivelAcesso", ""])]) as $menu){

            $subMenus = isset($menu["subMenus"])?$menu["subMenus"]:array();
            if(count($subMenus)>0){
              foreach( $subMenus as $sub){ 
                $acessos[]=array("idPMenu"=>$sub["idPSubMenu"], "designacaoMenu"=>$menu["designacaoMenu"]." (".$sub["designacaoSubMenu"].")");
              } 
            }else{
              $acessos[]=array("idPMenu"=>$menu["idPMenu"], "designacaoMenu"=>$menu["designacaoMenu"]);
            } 
          }
  
          echo "<script>var listaProfessores = ".json_encode($manipulacaoDados->entidades(["idPEntidade", "nomeEntidade", "numeroInternoEntidade", "fotoEntidade", "acessos.idEscola", "acessos.idPMenu", "acessos.designacaoMenu", "classes_aceso.classes", "classes_aceso.idPArea", "classes_aceso.idEscola"]))."</script>";
          echo "<script>var niveisAcessos=".json_encode($acessos)."</script>";              
           ?>
    
            
            <table id="example1" class="table table-striped table-bordered table-hover" >
              <thead class="corPrimary">
                <tr>
                  <th class="lead text-center"><strong>Nº</strong></th>
                  <th class="lead"><strong>Nome Completo</strong></th>
                  <th class="lead"><strong>Acessos</strong></th>
                  <th class="lead text-center"></th>
                </tr>
              </thead>
              <tbody id="tabela">
              </tbody>
            </table>

          </div>
          </div>
        <?php } echo "</div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha(); ?>

<div class="modal fade" id="formularioNiveisAcesso" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
  <form class="modal-dialog" id="formularioNiveisAcessoForm">
    <div class="modal-content">
      <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title lead font-weight-bolder diaSemana" id="myModalLabel"><i class="fa fa-check-double"></i> Acessos</h4>
      </div>
      <div class="modal-body">
        <div class="row listaAcessos">
          
            <?php 
              foreach($acessos as $menu){ ?>
                <div class="col-lg-6 col-md-6" style="line-height:30px;">
                  <label class="lead" style="font-size:12pt; text-transform:uppercase;"><input type="checkbox" idPMenu="<?php echo $menu["idPMenu"]; ?>" designacaoMenu="<?php echo $menu["designacaoMenu"]; ?>"> <?php echo $menu["designacaoMenu"]; ?></label>
                </div>
                <?php } 
            ?>
        </div><br>
      </div>
        <div class="modal-footer" style="margin-top: -30px;">
            <div class="row">

              <div class="col-lg-3 col-md-3 text-left">
                <button ype="submit" class="btn btn-success btn lead btn-lg"><i class="fa fa-check"></i> Alterar</button>
              </div>
              <div class="col-lg-9 col-md-9 text-left">
                <label for="markAcesso" class="lead"><input type="checkbox" id="markAcesso" style="margin-left: -25px;" ><span class="lever"></span> Marcar ou desmarcar todos</label>
              </div>                    
            </div>                
        </div>
    </div>
  </form>
</div>

      <div class="modal fade" id="formularioAltClasse" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
      <form class="modal-dialog" id="formularioClasseForm">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h4 class="modal-title lead font-weight-bolder diaSemana" id="myModalLabel"><i class="fa fa-blind"></i> Classes de Acesso</h4>
              </div>

              <div class="modal-body classesAcesso">
                    
                    <div class="row">
                    <?php  foreach ($manipulacaoDados->selectArray("nomecursos", ["nomeCurso", "idPNomeCurso", "areaFormacaoCurso", "classes.identificador", "classes.designacao"], ["cursos.idCursoEscola"=>$_SESSION['idEscolaLogada'], "cursos.estadoCurso"=>"A"], ["cursos"]) as $curso) { ?>

                        <div class="row" style="padding-left: 40px; padding-right: 40px; margin-bottom: 10px;">
                          <div class="col-lg-12 col-md-12 lead"><strong><?php echo $curso["nomeCurso"]." ( ".$curso["areaFormacaoCurso"].")"; ?></strong></div>
                          <?php  
                            foreach(listarItensObjecto($curso, "classes") as $classe){?>

                              <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                        <div class="switch"><label for="classe<?php echo $classe["identificador"].'_'.$curso["idPNomeCurso"]; ?>" class="lead"><input type="checkbox" style="margin-left: -25px;" name="classe<?php echo $classe["identificador"].'_'.$curso["idPNomeCurso"]; ?>" valor="<?php echo $classe["identificador"].'_'.$curso["idPNomeCurso"]; ?>" id="classe<?php echo $classe["identificador"].'_'.$curso["idPNomeCurso"]; ?>" curso="<?php echo $curso["nomeCurso"]; ?>"><span class="lever"></span><?php echo $classe["designacao"]; ?></label></div>
                      </div>

                           <?php } ?>
                        </div>
                     <?php } ?>
                     </div>

              </div>

              <div class="modal-footer">
                  <div class="row">

                    <div class="col-lg-3 col-md-3 text-left">
                      <button type="submit" class="btn btn-primary col-lg-12 lead btn-lg" id="Cadastar"><i class="fa fa-pen"></i> Salvar</button>
                    </div> 
                    <div class="col-lg-9 col-md-9 text-left">&nbsp;&nbsp;&nbsp;
                        <label for="markClass" class="lead"><input type="checkbox" id="markClass" style="margin-left: -25px;" ><span class="lever"></span> Marcar ou desmarcar todas</label>
                    </div>                   
                  </div>                
              </div>

            </div>
          </form>
      </div>

        <style type="text/css">
            #formularioNiveisAcesso .modal-dialog{
                width: 60%;
                margin-left: -30%;

            }
           @media (max-width: 768px) {
            #formularioNiveisAcesso .modal-dialog{
                width: 94%;
                margin-left: 3%;

            }
           }

           #formularioNiveisAcesso table tr td{
            padding: 4px;
            border: none; 
           }
           #formularioNiveisAcesso table thead tr td{
            font-weight: 700    ;  
           }
        </style>
