<?php session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Sobre Funcionários", "relatorioFuncionario");
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
  <style type="text/css">
    #citacaoProfessor{
      font-style: italic;
      color: white;
      font-weight: 800;
      color: orange;
    }
    .valor{
      font-weight: 800;
    }
    .informPerfil{
      font-weight: 1000;
    }
    .cargoProfessor{
      color: white;
      font-weight: 700;
      font-size: 18px;  
    }
    .nomeUsuarioCorente{
      font-weight: 800;
      color:white;
    }
    #imageProfessor{
      width: 130px;
      height: 130px;
      max-height: 130px;
      max-width: 130px;
      min-width: 130px;
      min-height: 130px;
    }
    .border div{
      border: solid white 1px;
      height: 120px;
      padding:0px;
      padding: 0px;
      margin: 0px;
      padding-top: 50px;
      color: white;
      font-weight: bolder;
      font-size: 19px;
    }
    .outrasInformacoes{
      color: white;
    }
  </style>
</head>

<body>
  <?php
    $janelaMensagens->processar (); 
    $layouts->cabecalho();
    $layouts->aside();

    $idPProfessor = isset($_GET["idPFuncionario"])?$_GET["idPFuncionario"]:"";
    $valorPesquisado = isset($_GET["valorPesquisado"])?$_GET["valorPesquisado"]:""; 

    if($valorPesquisado!=""){
      $condicoesPesquisa = [array("nomeEntidade"=>new \MongoDB\BSON\Regex($valorPesquisado)), array("biEntidade"=>new \MongoDB\BSON\Regex($valorPesquisado)), array("numeroInternoEntidade"=>new \MongoDB\BSON\Regex($valorPesquisado)), array("nomeEntidade"=>new \MongoDB\BSON\Regex(ucwords($valorPesquisado))), array("nomeEntidade"=>ucwords($valorPesquisado))];

      $pedro = $manipulacaoDados->selectArray("entidadesprimaria", ["idPEntidade"], ["escola.idEntidadeEscola"=>$_SESSION['idEscolaLogada'], "escola.estadoActividadeEntidade"=>"A", '$or'=>$condicoesPesquisa], ["escola"]);
      $idPProfessor = valorArray($pedro, "idPEntidade");
    }

    $array = $manipulacaoDados->selectArray("entidadesprimaria", [], ["idPEntidade"=>$idPProfessor, "escola.idEntidadeEscola"=>$_SESSION['idEscolaLogada']], ["escola"]);
    echo "<script>var idPProfessor='".valorArray($array, "idPEntidade")."'</script>";
    echo "<script>var listaValores=".json_encode($array)."</script>";
  ?>

  <section id="main-content"> 
        <section class="wrapper" id="containers">
          <div class="main-body">
    <?php if($verificacaoAcesso->verificarAcesso("", ["relatorioFuncionario"], array(), "msg")){ ?>
      
        <div class="row">
          <!-- profile-widget -->
          <div class="col-lg-12">
            <div class="profile-widget profile-widget-info">
              <div class="panel-body">
                <div class="col-lg-3 col-sm-3 text-center">
                  <h4 class="nomeUsuarioCorente text-center"><?php echo valorArray($array, "nomeEntidade"); ?></h4>
                  <div class="follow-ava text-center">
                    <img src="<?php echo  '../../../fotoUsuarios/'.valorArray($array, 'fotoEntidade'); ?>" class="medio imagemUsuarioCorrente" id="imageProfessor">
                  </div>

                </div>
                <div class="col-lg-3 col-sm-3 follow-info">
                  <p class="text-justify citacaoUsuarioCorente" id="citacaoProfessor"><?php echo valorArray($array, "citacaoFavoritaEntidade"); ?></p>
                  <h6 class="outrasInformacoes">

                                    <span class="lead"><i class="fa fa-phone"></i> <strong class="numeroTelefone" id="telProfessor"><?php echo valorArray($array, "numeroTelefoneEntidade");?></strong></span><br/><br/>

                                    <span class="lead"><strong class="numeroTelefone" id="telProfessor"><?php echo valorArray($array, "emailEntidade");?></strong></span><br/>
                                </h6>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-12 follow-info weather-category border" style="padding-top: 0px; border: none !important;">
                  <div class="text-center">
                    <strong class="text-center " id="nivelAcademino"><?php 
                        echo valorArray($array, "nivelAcademicoEntidade")
                     ?></strong>
                  </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-12 follow-info weather-category border" style="padding-top: 0px; border: none !important;">
                  <div class="text-center" >
                    <strong class="text-center " id="areaFormacao">
                      <?php 
                        if(valorArray($array, "nivelAcademicoEntidade")=="Médio"){
                            echo valorArray($array, "cursoEnsinoMedio");
                        }else if(valorArray($array, "nivelAcademicoEntidade")=="Licenciado" || valorArray($array, "nivelAcademicoEntidade")=="Bacharel"){
                            echo valorArray($array, "cursoLicenciatura");
                        }else if(valorArray($array, "nivelAcademicoEntidade")=="Mestre"){
                            echo valorArray($array, "cursoMestrado");
                        }else if(valorArray($array, "nivelAcademicoEntidade")=="Doutor"){
                            echo valorArray($array, "cursoDoutoramento");
                        }
                        ?>

                    </strong>
                  </div>
                </div>

              </div>
            </div>
          </div>
        </div>

        <form class="row" id="pesquisarAluno">
          <div class="col-lg-10 col-md-10" id="pesqUsario">
                <input type="search" class="form-control lead"  placeholder="Pesquisar Funcionário..." required list="listaOpcoes" id="valorPesquisado" value="<?php echo valorArray($array, 'numeroInternoEntidade'); ?>" autocomplete="off"  tipoEntidade="professores" >   
              </div>
          <div class="col-lg-2 col-md-2">
            <button type="submit" class="form-control lead btn-primary"><i class="fa fa-search"></i> Pesquisar</button>
          </div>
          <input type="hidden" name="action" value="pesquisarAluno">
        </form>

        <div class="row">
          <div class="col-lg-12 col-md-12">
            <?php 
              if(isset($_GET["valorPesquisado"])){
                   //Outros nomes como sugestão...
                $valorPesquisado = $_GET["valorPesquisado"];

                $pedro = $manipulacaoDados->selectArray("entidadesprimaria", [], ["escola.idEntidadeEscola"=>$_SESSION['idEscolaLogada'], "idPEntidade"=>array('$ne'=>$idPProfessor), "escola.estadoActividadeEntidade"=>"A", '$or'=>$condicoesPesquisa], ["escola"], 10);

                $contador=0;
                foreach($pedro as $a){
                    $contador++;
                    echo "<i class='fa fa-user-circle'></i> <a href='?idPFuncionario=".$a["idPEntidade"]."' class='lead'>".$a["nomeEntidade"]." (".$a["numeroInternoEntidade"].")</a>&nbsp;&nbsp;";        
                }
              }

             ?>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-12">
            <div class="panel">
              <header class="panel-heading tab-bg-info">
                <ul class="nav nav-tabs">
                  <li class="active">
                    <a data-toggle="tab" href="#profile" class="lead">
                                          <i class="fa fa-user"></i>
                                         Perfil
                                      </a>
                  </li>
                  <?php if(count($array)){ ?>
                  <li>
                    <a data-toggle="tab" href="#edit-profile" class="lead">
                        <i class="fa fa-user-edit"></i>
                        Editar Perfil
                    </a>
                  </li>
                  <li class="">
                    <a data-toggle="tab" href="#documentos" class="lead">
                        <i class="fa fa-print"></i>
                        Relatórios
                    </a>
                  </li>
                <?php } ?>
                </ul>
              </header>
              <div class="panel-body">
                <div class="tab-content"> 
                  <!-- profile -->
                   <div id="profile" class="tab-pane active">
                    <div class="panel" style="min-height: 200px;">
                      <div class="bio-graph-heading lead col-sm-12 col-xs-12 col-lg-12 col-md-12" id="acercaUsuarioCorente" style="margin-bottom: 30px;">
                        <?php echo valorArray($array, "funcaoEnt", "escola"); ?>
                      </div>

                         <div class="col-lg-6 col-md-6">
                            <div class="row">
                              <div class="col-lg-4  col-md-4 lead text-right lab etiqueta">Número de Agente:</div>
                              <div class="col-lg-8  col-md-8 lead valor" id="numeroAgente"><?php echo valorArray($array, "numeroAgenteEntidade"); ?></div>
                            </div>

                            <div class="row">
                              <div class="col-lg-4  col-md-4 lead text-right lab etiqueta">Categoria:</div>
                              <div class="col-lg-8  col-md-8 lead valor" id="numeroAgente"><?php echo valorArray($array, "categoriaEntidade"); ?></div>
                            </div>

                            <div class="row">
                              <div class="col-lg-4  col-md-4 lead text-right lab etiqueta">Nome do Pai:</div>
                              <div class="col-lg-8  col-md-8 lead valor" id="paiAluno"><?php echo valorArray($array, "paiEntidade"); ?></div>
                            </div>
                            <div class="row">
                              <div class="col-lg-4 col-md-4 lead text-right lab etiqueta">Nome da Mãe:</div>
                              <div class="col-lg-8  col-md-8 lead valor" id="maeAluno"><?php echo valorArray($array, "maeEntidade"); ?></div>
                            </div>

                            <div class="row">
                              <div class="col-lg-4  col-md-4 lead text-right lab etiqueta">Sexo:</div>
                              <div class="col-lg-8  col-md-8 lead valor" id="sexoAluno"><?php echo generoExtenso(valorArray($array, "generoEntidade")); ?></div>
                            </div>

                            <div class="row">
                              <div class="col-lg-4  col-md-4 lead text-right lab etiqueta">Nascido Aos:</div>
                              <div class="col-lg-8  col-md-8 lead valor" id="dataNascAluno"><?php echo dataExtensa(valorArray($array, "dataNascEntidade")); ?></div>
                            </div>
                          </div>

                          <div class="col-lg-6  col-md-6">
                            <div class="row">
                            <div class="col-lg-4  col-md-4 lead text-right lab etiqueta">Municipio:</div>
                            <div class="col-lg-8  col-md-8 lead valor" id="municAluno"><?php echo $manipulacaoDados->selectUmElemento("div_terit_municipios", "nomeMunicipio", ["idPMunicipio"=>valorArray($array, "municNascEntidade")]); ?></div>
                          </div>

                          <div class="row">
                            <div class="col-lg-4  col-md-4 lead text-right lab etiqueta">Província:</div>
                            <div class="col-lg-8  col-md-8 lead valor" id="provAluno"><?php echo $manipulacaoDados->selectUmElemento("div_terit_provincias", "nomeProvincia", ["idPProvincia"=>valorArray($array, "provNascEntidade")]); ?></div>
                          </div>

                          <div class="row">
                            <div class="col-lg-4  col-md-4 lead text-right lab etiqueta">BI:</div>
                            <div class="col-lg-8  col-md-8 lead valor" id="biAluuno"><?php echo valorArray($array, "biEntidade"); ?></div>
                          </div>

                          <div class="row">
                            <div class="col-lg-4  col-md-4 lead text-right lab etiqueta">Emitido aos:</div>
                            <div class="col-lg-8 col-md-8 lead valor" id="dataEmitidoBI"><?php echo dataExtensa(valorArray($array, "dataEBIEntidade")); ?></div>
                          </div>
                            
                          </div>
                    </div>
                   </div>

            <div id="edit-profile" class="tab-pane">
              <div class="panel">
                <?php 
                $readOnly="";
                if(valorArray($manipulacaoDados->sobreUsuarioLogado, "privacidadeEscola")!="Pública" ){
                  $readOnly="readonly";
                } ?>
                  <form class="form-horizontal" role="form" method="POST" enctype="multipart-data" id="formularioPerfil">

                      <div class="form-group">
                        
                        <div class="col-lg-6 col-md-6 lead">
                          Nome Completo:
                          <input type="text" class="form-control lead" id="nomeEntidade" name="nomeEntidade" maxlength="60" required>
                        </div>
                        <div class="col-lg-4 col-md-4">
                          <label class="lead" for="nomeEntidade">Foto:</label>
                          <input type="file" name="fotoEntidade" value="" accept='.jpg, .png, .jpeg' class="form-control fa-border vazio" id="fotoEntidade" maxlength="60">
                        </div>
                        <div class="col-lg-2 col-md-2">
                            <label class="lead" for="sexoEntidade">Sexo:</label>
                            <select class="form-control lead" id="sexoEntidade" name="sexoEntidade">
                                <option value="M">Masculino</option>
                                <option value="F">Feminino</option>
                            </select>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-lg-2 col-md-2">
                          <label class="lead" for="dataNascEntidade">Nascido aos:</label>
                          <input type="date" name="dataNascEntidade" class="form-control vazio" id="dataNascEntidade" max="<?php echo $manipulacaoDados->dataSistema; ?>" min="<?php echo $manipulacaoDados->adicionarDiasData((-1*365*70)); ?>">
                          <div class="dataNascEntidade discasPrenchimento lead"></div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                          <label class="lead" for="pais">País:</label>
                            <select id="pais" name="pais" class="form-control lead" required>
                              <?php 
                                foreach($manipulacaoDados->selectArray("div_terit_paises", [], [], [], "", [], ["nomePais"=>1]) as $a){
                                  echo "<option value='".$a["idPPais"]."'>".$a["nomePais"]."</option>";
                                }
                              ?>
                            </select>
                        </div>
                        <div class="col-lg-3 col-md-3">
                          <label class="lead" for="provincia">Província:</label>
                            <select id="provincia" required name="provincia" class="form-control lead"></select>
                        </div>
                        <div class="col-lg-3 col-md-3">
                          <label class="lead" for="municipio">Municipio:</label>
                            <select id="municipio" required name="municipio" class="form-control municipio lead">                            
                            </select>                         
                        </div>          
                    </div>   

                    <div class="row">
                        <div class="col-lg-3 col-md-3">
                          <label class="lead" for="comuna">Comuna:</label>
                            <select id="comuna" required name="comuna" class="form-control municipio lead">                            
                            </select>                        
                        </div> 
                        <div class="col-lg-3 col-md-3">
                          <label class="lead labelBI" for="biEntidade">N.º BI:</label>
                             <input type="text" name="biEntidade" class="form-control somenteLetrasNumeros vazio" id="biEntidade" autocomplete="off" maxlength="15">
                             <div class="biEntidade discasPrenchimento lead"></div>
                        </div>
                        <div class="col-lg-2 col-md-2">
                          <label class="lead" for="dataEBIEntidade">Emitido aos:</label>
                          <input type="date" name="dataEBIEntidade" class="form-control lead data" id="dataEBIEntidade" title="Data de emissão">
                          <div class="dataEBIEntidade discasPrenchimento lead"></div>
                        </div>
                        <div class="col-lg-2 col-md-2">
                          <label class="lead" for="dataEBIEntidade">Caduca em:</label>
                          <input type="date" name="dataCaducBI" class="form-control data" id="dataCaducBI" title="Data de Caducidade" >
                          <div class="dataCaducBI discasPrenchimento lead"></div>
                        </div>
                        <div class="col-lg-2 col-md-2">
                          <label class="lead" for="numeroTelefoneEntidade">N.º de Telefone:</label>
                           <input type="text" name="numeroTelefoneEntidade" class="form-control numeroDeTelefone vazio" id="numeroTelefoneEntidade" title="Número de telefone" autocomplete="off" >
                           <div class="numeroTelefoneEntidade discasPrenchimento lead"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-4 col-md-4">
                          <label class="lead" for="emial">E-mail:</label>
                             <input type="emailEntidade" name="emailEntidade" class="form-control  vazio" id="emailEntidade" title="E-mail do(a) professor(a)" autocomplete="off">
                             <div class="emailEntidade discasPrenchimento lead"></div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                          <label class="lead" for="paiEntidade">Nome do Pai:</label>
                             <input type="text" name="paiEntidade" class="form-control vazio somenteLetras" id="paiEntidade" title="Nome do Pai">
                             <div class="paiEntidade discasPrenchimento lead" autocomplete="off"></div>
                        </div>                   
                        <div class="col-lg-4 col-md-4">
                          <label class="lead" for="maeEntidade">Nome da Mãe:</label>
                             <input type="text" name="maeEntidade" class="form-control lead vazio somenteLetras" id="maeEntidade" title="Nome da mãe">
                             <div class="maeEntidade discasPrenchimento lead" autocomplete="off" style="margin-top: -15px;"></div>
                        </div>     
                    </div>
                    <div class="row"> 
                      <div class="col-lg-2 col-md-2">
                        <label class="lead" for="numeroAgenteEntidade">N.º de Agente:</label>
                        <input type="number" name="numeroAgenteEntidade" class="form-control fa-border vazio" id="numeroAgenteEntidade" autocomplete="off" >

                        <div class="numeroAgenteEntidade discasPrenchimento lead"></div>
                      </div>   
                      <div class="col-lg-2 col-md-2">
                        <label class="lead" for="tipoPessoal">Pessoal:</label>
                         <select name="tipoPessoal" class="form-control lead fa-border vazio" id="tipoPessoal">
                           <option value="docente">Docente</option>
                            <option value="naoDocente">Não Docente</option>
                         </select>
                      </div>  
                      <div class="col-lg-3 col-md-3 lead"><label class="lead" for="naturezaVinc">Natureza do Vínculo:</label>
                        <select type="text"  class="form-control lead vazio" id="naturezaVinc" name="naturezaVinc">
                          <option>Provimento Provisório</option>
                          <option>Pessoal Do Quadro</option>
                          <option>Comissão De  Serviço</option>
                          <option>Eventual</option>
                          <option>Colaborador</option>
                        </select>
                      </div>  
                      <div class="col-lg-5 col-md-5 docente efetivo">
                          <label class="lead" for="categoriaEntidade">Categoria:</label>
                           <select type="text" name="categoriaEntidade" class="form-control" id="categoriaEntidade">
                              <option>Professor do Ensino Primário e Secundário do 1º Grau</option>
                              <option>Professor do Ensino Primário e Secundário do 2º Grau</option>
                              <option>Professor do Ensino Primário e Secundário do 3º Grau</option>
                              <option>Professor do Ensino Primário e Secundário do 4º Grau</option>
                              <option>Professor do Ensino Primário e Secundário do 5º Grau</option>
                              <option>Professor do Ensino Primário e Secundário do 6º Grau</option>
                              <option>Professor do Ensino Primário e Secundário do 7º Grau</option>
                              <option>Professor do Ensino Primário e Secundário do 8º Grau</option>
                              <option>Professor do Ensino Primário e Secundário do 9º Grau</option>
                              <option>Professor do Ensino Primário e Secundário do 10º Grau</option>
                              <option>Professor do Ensino Primário e Secundário do 11º Grau</option>
                              <option>Professor do Ensino Primário e Secundário do 12º Grau</option>
                              <option>Professor do Ensino Primário e Secundário do 13º Grau</option>
                              <option>Auxiliar de Limpeza da 2ª Classe</option>
                              <option>Operário nã Qualificado de 2ª Classe</option>
                           </select>
                        </div>                     
                    </div>
                    <div class="row">
                        
                      
                       
                    </div>
                    <div class="row">
                      <div class="col-lg-2 col-md-2 lead">
                      <label class="lead" for="funcaoProf">Nível de Acesso:</label>
                      <select class="form-control" required id="nivelSistemaEntidade" name="nivelSistemaEntidade">
                        
                        <?php 
                          foreach($manipulacaoDados->selectArray("cargos", ["idPCargo", "designacaoCargo"], ["instituicao"=>valorArray($manipulacaoDados->sobreEscolaLogada, "tipoInstituicao")], [], "", [], array("designacaoCargo"=>1)) as $a){
                            echo '<option class="lead" value="'.$a["idPCargo"].'">'.$a["designacaoCargo"].'</option>';
                          }
                         ?>
                        <?php if ($_SESSION['idUsuarioLogado']==35){ ?>
                          <option class="lead" value="0">Usuário_Master</option>
                        <?php } ?>
                        <option class="lead" value="">Sem Acesso</option>
                      </select>
                     </div>
                      <div class="col-lg-4 col-md-4 lead"><label class="lead" for="funcaoEnt">Função:</label>
                        <input type="text" class="form-control vazio" id="funcaoEnt" name="funcaoEnt" list="listaFuncoes" autocomplete="off">
                        <datalist id="listaFuncoes">
                          <option>Director</option>
                          <option>Subdirector Pedagógico</option>
                          <option>Subdirectora Administrativa</option>
                          <option>Coordenador de Turno</option>
                          <option>Coordenador do Giva</option>
                          <option>Coordenador de Área</option>
                          <option>Coordenador de Curso </option>
                          <option>Chefe de Secretaria</option>
                          <option>Chefe de Secretaria Pedagógica</option>
                          <option>Chefe de Secretaria Administrativa</option>
                          <option>Coordenador de Educação Física e Desporto Escolar</option>
                          <option>Coordenador de Actividade Extracular Círculo de Interesse e Extra-Escolar</option>
                          <option>Coordenador de Disciplina</option>
                          <option>Coordenador de Área Disciplinar</option>
                          <option>Professor</option>
                          <option>Auxiliar Administrativo de Limpeza</option>
                          <option>Auxiliar de Limpeza</option>
                        </datalist>
                      </div>
                      <div class="col-lg-2 col-md-2 lead docente efetivo">
                        <label class="lead" for="cargoPedagogicoEnt">Tempo Pedag.</label><input type="number"  class="form-control lead vazio text-center" id="cargoPedagogicoEnt" name="cargoPedagogicoEnt" min="0">
                      </div>

                      <div class="col-lg-2 col-md-2 lead docente efetivo">
                        <label for="valorAuferidoNaEducacao">Salário(Estado):</label><input type="number" step="0.001" class="form-control lead vazio text-center" id="valorAuferidoNaEducacao" name="valorAuferidoNaEducacao" >
                      </div>

                      <div class="col-lg-2 col-md-2 lead docente efetivo">
                        <label for="valorAuferidoNaInstituicao">Salário(Instituição):</label><input type="number"  step="0.001" class="form-control lead vazio text-center" id="valorAuferidoNaInstituicao" name="valorAuferidoNaInstituicao" >
                      </div>  
                    </div>

                    <div class="row">
                      <div class="col-lg-2 col-md-2 lead docente efetivo">
                        <label for="pagamentoPorTempo">
                          Pagamento/Tempo
                        </label>
                        <input type="number"  step="0.001" class="form-control lead vazio text-center" id="pagamentoPorTempo" name="pagamentoPorTempo">
                      </div>

                      <div class="col-lg-3 col-md-3 lead docente efetivo"><label class="lead" for="nomeBanco">Nome do Banco:</label><input type="text"  class="form-control lead vazio" id="nomeBanco" name="nomeBanco"></div>
                      <div class="col-lg-3 col-md-3 lead docente efetivo">
                        <label class="lead" for="numeroContaBancaria">N.º da Conta:</label><input type="text" step="0.001" class="form-control lead vazio" id="numeroContaBancaria" name="numeroContaBancaria">
                      </div>
                      <div class="col-lg-4 col-md-4 lead docente efetivo"><label class="lead" for="ibanContaBancaria">IBAN:</label><input type="text" class="form-control vazio" id="ibanContaBancaria" name="ibanContaBancaria"></div>
                    </div>
                    <div class="row">
                      <div class="col-lg-3 col-md-3 lead"><label class="lead" for="dataInicEduc">Inicio das Funções<br>(Estado)</label><input type="date"  class="form-control lead vazio" id="dataInicEduc" name="dataInicEduc" ></div>
                      <div class="col-lg-3 col-md-3 lead"><label class="lead" for="dataInicioFuncoesEntidade">Inicio das Funções<br>(Instituição)</label><input type="date" class="form-control lead vazio" id="dataInicioFuncoesEntidade" name="dataInicioFuncoesEntidade"></div>
                      <div class="col-lg-3 col-md-3 lead">
                        <label class="lead" for="tempoServOutraEsc">Tempo de Serviço<br>Outra Esc.:</label><input type="number" min="0" class="form-control lead vazio text-center" id="tempoServOutraEsc" name="tempoServOutraEsc">
                      </div>
                      <div class="col-lg-3 col-md-3 lead">
                        <label class="lead" for="numSegSocial">NSS:<br><br></label>
                        <input type="text"  class="form-control lead vazio" id="numSegSocial" name="numSegSocial">
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-lg-2 col-md-2">
                        <label class="lead" for="nivelAcademicoEntidade">Nível Académico:</label><br><br>
                        <select class="form-control" id="nivelAcademicoEntidade" name="nivelAcademicoEntidade">
                          <option value="Primário">Ensino Primário</option>
                          <option value="Básico">Técnico Básico</option>
                          <option value="Médio">Técnico Médio</option>
                          <option value="Bacharel">Bacharel</option>
                          <option value="Licenciado">Licenciado</option>
                          <option value="Mestre">Mestre</option>
                          <option value="Doutor">Doutor</option>
                        </select>
                      </div>
                      <div class="col-lg-3 col-md-3 lead"><label class="lead" for="dataDespacho">Data de Despacho<br>Nomeação:</label><input type="date"  class="form-control lead vazio" id="dataDespacho" name="dataDespacho"></div>

                      <div class="col-lg-2 col-md-2 lead"><label class="lead" for="numDespacho">N.º de Despacho:<br><br></label><input type="text"  class="form-control lead vazio" id="numDespacho" name="numDespacho"></div>

                     <div class="col-lg-5 col-md-5 lead"><br><br>
                      <input type="checkbox" id="comFormPedag" name="comFormPedag"> <label for="comFormPedag" class="lead">Com Formação Pedagógica</label>
                     </div>
                    </div>
                      <div class="row">
                        <div class="col-lg-6 col-md-6">
                          <h3 class="text-danger"><strong>Ensino Médio</strong></h3>
                          <div class="col-lg-6 col-md-6">
                            <label class="lead" for="cursoEnsinoMedio">Curso:</label>
                            <input type="text" name="cursoEnsinoMedio" class="form-control fa-border vazio" id="cursoEnsinoMedio" maxlength="60">
                            <div class="cursoEnsinoMedio discasPrenchimento lead"></div>
                          </div>
                          <div class="col-lg-6 col-md-6">
                            <label class="lead" for="escolaEnsinoMedio">Escola:</label>
                            <input type="text" name="escolaEnsinoMedio" class="form-control fa-border vazio" id="escolaEnsinoMedio" maxlength="60">

                            <div class="escolaEnsinoMedio discasPrenchimento lead"></div>
                          </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                          <h3 class="text-danger"><strong>Licenciatura</strong></h3>
                            <div class="col-lg-6 col-md-6">
                              <label class="lead" for="cursoLicenciatura">Curso:</label>
                            <input type="text" name="cursoLicenciatura" class="form-control fa-border vazio" id="cursoLicenciatura" maxlength="60">

                            <div class="cursoLicenciatura discasPrenchimento lead"></div>
                          </div>
                          <div class="col-lg-6 col-md-6">
                            <label class="lead" for="escolaLicenciatura">Escola:</label>
                            <input type="text" name="escolaLicenciatura" class="form-control fa-border vazio" id="escolaLicenciatura" maxlength="60">

                            <div class="escolaLicenciatura discasPrenchimento lead"></div>
                          </div>
                        </div>
                      </div>


                      <div class="row">
                        <div class="col-lg-6 col-md-6">
                          <h3 class="text-danger"><strong>Mestrado</strong></h3>
                          <div class="col-lg-6 col-md-6">
                            <label class="lead" for="cursoMestrado">Curso:</label>
                          <input type="text" name="cursoMestrado" class="form-control fa-border vazio" id="cursoMestrado" maxlength="60">

                          <div class="cursoMestrado discasPrenchimento lead"></div>
                          </div>
                          <div class="col-lg-6 col-md-6">
                            <label class="lead" for="escolaMestrado">Escola:</label>
                            <input type="text" name="escolaMestrado" class="form-control fa-border vazio" id="escolaMestrado" maxlength="60">

                            <div class="escolaMestrado discasPrenchimento lead"></div>
                          </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                          <h3 class="text-danger"><strong>Doutoramento</strong></h3>
                          <div class="col-lg-6 col-md-6">
                            <label class="lead" for="cursoDoutoramento">Curso:</label>
                          <input type="text" name="cursoDoutoramento" class="form-control fa-border vazio" id="cursoDoutoramento" maxlength="60">

                          <div class="cursoDoutoramento discasPrenchimento lead"></div>
                          </div>
                            <div class="col-lg-6 col-md-6">
                              <label class="lead" for="escolaDoutoramento">Escola:</label>
                              <input type="text" name="escolaDoutoramento" class="form-control fa-border vazio" id="escolaDoutoramento" autocomplete="off" maxlength="60">
                              <div class="escolaDoutoramento discasPrenchimento lead"></div>
                            </div>
                        </div>
                      </div>
                      <input type="hidden" name="idPEntidade" id="idPEntidade" value="<?php echo $idPProfessor; ?>">
                      <input type="hidden" name="editadoNoPerfilEntidade" value="sim">
                      <input type="hidden" name="action" value="editarPerfilEntidade">

                      <div class="form-group">
                        <div class="col-lg-12">
                          <div class="col-lg-3 col-md-3"> 
                              <button  type="submit" class="btn-primary btn lead"><i class="fa fa-check"></i> Editar</button>
                                
                            </div>
                        </div>
                      </div>
                    </form>
                </div>
              </div>

                   <div id="documentos" class="tab-pane">
                    <div class="panel">
                        <div class="panel-body bio-graph-info" id="listaDocumentos" style="min-height: 200px;">
                          <div class="row">
                              <fieldset class="col-lg-12 col-md-12" style="border:solid rgba(0, 0, 0, 0.3) 1px; border-radius: 10px;" id="boletimDocumento">
                                  <legend style="width: 400px;"><strong>Ficha de Avaliação de Desempenho</strong></legend>
                                  <div class="row"> 
                                    <div class="col-lg-2 col-md-2 col-sm-4 col-sm-6">
                                        <select class="form-control" id="anosLectivos">
                                      <?php foreach ($manipulacaoDados->anosLectivos as $ano){                      
                                        echo "<option value='".$ano["idPAno"]."'>".$ano["numAno"]."</option>";
                                      } ?>  
                                        </select>
                                    </div>
                                      <div class="col-lg-2 col-md-2 col-sm-4 col-sm-6">
                                        <a href="#" class="fichaAvaliacaoDesempenho btn-primary btn" id="I"><i class="fa fa-print"></i> I Trimestre</a>
                                      </div>
                                      <div class="col-lg-2 col-md-2 col-sm-4 col-sm-6">
                                        <a href="#" class="fichaAvaliacaoDesempenho btn-primary btn" id="II"><i class="fa fa-print"></i> II Trimestre</a>
                                      </div>
                                      <div class="col-lg-2 col-md-2 col-sm-4 col-sm-6">
                                        <a href="#" class="fichaAvaliacaoDesempenho btn-primary btn" id="III"><i class="fa fa-print"></i> III Trimestre</a>
                                      </div>
                                    <div class="col-lg-2 col-md-2 col-sm-4 col-sm-6">
                                      <a href="#" class="fichaAvaliacaoDesempenho btn-primary btn" id="IV"><i class="fa fa-print"></i> Final</a>
                                    </div>
                                  </div>
                                </fieldset>
                            </div>
                            <div class="row">
                              <div class="col-lg-12 col-md-12">
                                <a href="#" class="btn-primary btn" id="guiaMarcha"><i class="fa fa-print" ></i> Guia de Marcha</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="btn btn-primary" id="declarao"><i class="fa fa-print"></i> Declaração</a>
                                &nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="btn-primary btn" id="declaraoVencimento"><i class="fa fa-print"></i> Declaração com Vencimento</a>
                                &nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo '../../relatoriosPdf/relatoriosProfessores/horarioProfessor.php?idPProfessor='.$idPProfessor ?>" class="btn-primary btn" id="declaraoVencimento" ><i class="fa fa-print"></i> Horário do Professor</a>
                              </div>
                            </div>
                        </div>
                      </div>
                    </div>
              </div>
            </div>
          </div>
        </div>
      <?php } echo "</div>"; $includarHtmls->rodape(); ?>
        </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); ?>
<script type="text/javascript" src="script.js"></script>
<?php $includarHtmls->formTrocarSenha(); $includarHtmls->dataList(); $janelaMensagens->funcoesDaJanelaJs(); ?>


<div class="modal fade" id="modalGuiaMarcha" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
      <form class="modal-dialog" id="formGuiaMarcha">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"><i class="fa fa-print"></i> Guia de Marcha</h4>
              </div>

              <div class="modal-body">
                  <div class="row">
                      <div class="col-lg-4 col-md-4 lead">
                        Guina N.º:
                          <input type="number" name="" id="numeroGuiaMarcha" required="" min="0" class="form-control lead text-center">
                      </div>
                      <div class="col-lg-8 col-md-8 lead">
                        Destino:
                          <select id="pais" name="pais" class="form-control lead nomePaisBI" required>
                            <?php 
                            foreach($manipulacaoDados->selectArray("div_terit_paises",[], [], [], "", [], ["nomePais"=>1]) as $a){
                              echo "<option value='".$a["idPPais"]."'>".$a["nomePais"]."</option>";
                            }
                           ?>
                          </select>
                      </div>
                  </div>

                  <div class="row">
                      <div class="col-lg-4 col-md-4 lead">
                        Província
                        <select id="provincia" name="provincia" class="form-control lead" required></select>
                      </div>
                      <div class="col-lg-4 col-md-4 lead">
                        Municipio
                        <select id="municipio" name="municipio" class="form-control municipio lead" required>                            
                        </select>                        
                      </div>
                      <div class="col-lg-4 col-md-4 lead">
                        Comuna
                        <select id="comuna" name="comuna" class="form-control municipio lead" required>                            
                        </select>                        
                      </div>
                  </div>

                  <div class="row">
                    <div class="col-lg-12 col-md-12 lead">
                      Motivo:
                          <textarea class="form-control lead" id="motivo" required="" style="min-width: 100%; max-width: 100%; min-height: 100px;"></textarea>                       
                      </div>
                  </div>

                  <div class="row">
                      <div class="col-lg-5 col-md-5 lead">
                      Assinado pelo:
                        <select class="form-control lead" id="funcionarioAssinar">
                            <option value="7">Director</option>
                            <option value="9">Sub-Director Administrativo</option>
                            <option value="8">Sub-Director Pedagógico</option>
                        </select>                     
                      </div> 
                  </div>
              </div>

              <div class="modal-footer">
                  <div class="row">
                    <div class="col-lg-4 col-md-4 text-left">
                      <button type="submit" class="btn btn-primary lead btn-lg submitter" id="Cadastar"><i class="fa fa-file"></i> Visualizar</button>
                    </div>                    
                  </div>                
              </div>
            </div>
          </form>
      </div>



      <div class="modal fade" id="modalDeclaraoTrabalho" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
      <form class="modal-dialog" id="declaraoTrabalhoForm">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"><i class="fa fa-print"></i> Declaração</h4>
              </div>

              <div class="modal-body">
                  <div class="row">
                      <div class="col-lg-3 col-md-3 lead">
                        Declaração N.º:
                          <input type="number" name="" id="numeroDeclaracao" required="" min="0" class="form-control lead text-center">
                      </div>
                      <div class="col-lg-9 col-md-9 lead">
                        Motivo da Decl.:
                          <input type="text" id="motivoDeclaracao" autocomplete="on" required=""  class="form-control lead">
                      </div>
                  </div>

                  <div class="row">
                    <div class="col-lg-5 col-md-5 lead">
                      Assinado pelo:
                        <select class="form-control lead" id="dirigenteAssinar">
                            <option value="7">Director</option>
                            <option value="9">Sub-Director Administrativo</option>
                            <option value="8">Sub-Director Pedagógico</option>
                        </select>                     
                      </div>
                  </div>
              </div>

              <div class="modal-footer">
                  <div class="row">
                    <div class="col-lg-4 col-md-4 text-left">
                      <button type="submit" class="btn btn-primary lead btn-lg submitter" id="Cadastar"><i class="fa fa-file"></i> Visualizar</button>
                    </div>                    
                  </div>                
              </div>
            </div>
          </form>
      </div>
