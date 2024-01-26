<?php session_start();
    include_once ('../../funcoesAuxiliares.php');
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Zona de Pesquisa", "zonaPesquisa");
    $includarHtmls = new includarHtmls();
    $janelaMensagens = new janelaMensagens();
    $conexaoFolhas = new conexaoFolhas();
    $verificacaoAcesso = new verificacaoAcesso();
    $layouts = new layouts();
    $manipulacaoDados->retornarAnosEmJavascript();
    $layouts->idPArea = $manipulacaoDados->idPArea;
    $layouts->designacaoArea = $manipulacaoDados->designacaoArea;
    $manipulacaoDados->listaClassesPorCurso();
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
           <nav role="navigation" class="navbar navbar-inverse paternSubMenuInterno" > 

          <a data-toggle="dropdown" class="dropdown-toggle navbar-brand chamMenuInterno"   href="#">
                                <b class="caret"></b>
                            </a>
          <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-search"></i> Zona de Pesquisa</strong></h1>     
     
          </nav>

        <div class="main-body">

        <?php  if($verificacaoAcesso->verificarAcesso("", ["zonaPesquisa"],array(), "msg")){ ?>

          

   <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-lg-4 col-md-4 lead">
            Curso
              <select class="form-control lead" id="curso">
                <?php 
                  echo "<option value='T'>Todos</option>";
                  foreach ($manipulacaoDados->selectArray("nomecursos", ["idPNomeCurso", "nomeCurso", "areaFormacaoCurso"], ["cursos.idCursoEscola"=>$_SESSION['idEscolaLogada'], "cursos.estadoCurso"=>"A"], ["cursos"], "", [], ["nomeCurso"=>1]) as $curso) {

                    echo "<option value='".$curso["idPNomeCurso"]."'>".$curso["nomeCurso"]." (".$curso["areaFormacaoCurso"].")</option>";
                  }
                ?> 
              </select>
          </div>
          
          <div class="col-lg-2 col-md-2 lead">
            Classe:
            <select class="form-control lead" id="classe">                    
              <option class="lead" value="T">Todos</option>
              <optgroup id='listaClasses'></optgrup>                   
            </select>
          </div>

          <div class="col-lg-2 col-md-2 lead">
            Turma:
                <select class="form-control lead" id="turma">
                  <option value="T">Todas</option>
                  <?php foreach ($manipulacaoDados->selectDistinct("listaturmas", "nomeTurma", ["idListaEscola"=>$_SESSION["idEscolaLogada"]], ['sort'=>["nomeTurma"=>1]]) as $turma) {                    
                      echo "<option value='".$turma."'>".$turma."</option>";
                  } ?>
                </select>
          </div>

          <div class="col-lg-2 col-md-2 lead">
            Ano:
          <select class="form-control lead" id="anoLectino">
            <?php 
              foreach($manipulacaoDados->anosLectivos as $ano){                       
                echo "<option value='".$ano["idPAno"]."'>".$ano["numAno"]."</option>";
              }
            ?>                    
            <option value="0">Toda Base de Dados</option>
                     
          </select>
        </div>
      </div>

      <form method="" id="formularioPesquisa">
        <div class="row">
          <div class="col-lg-12 col-md-12 col-xs-12 col-sm-12">
            <div class="col-lg-2 col-md-2">
              <label>Campo</label>
              <select class="form-control lead" id="campoPesquisado">
                <optgroup label="Nomes">
                  <option class="lead" value="nomeAluno" title="paraNomes">Nome do Aluno</option>
                  <option class="lead" value="paiAluno" title="paraNomes">Nome do Pai</option>
                  <option class="lead" value="maeAluno" title="paraNomes">Nome da Mãe</option>            
                </optgroup>

                <optgroup label="Género">
                  <option class="lead" value="sexoAluno" title="paraGenero">Género do(a) Aluno</option>
                </optgroup>

                <optgroup label="Asssento de Nascimento">
                  <option class="lead" value="idade" title="paraIdade">Idade</option>

                  <option class="lead" value="dataNascAluno" title="paraDataDeNascimento">Data de Nascimento:</option>

                  <option class="lead" value="nomeComuna" title="paraNomes">Comuna de Nascimento</option>
                  <option class="lead" value="nomeMunicipio" title="paraNomes">Municipio de Nascimento</option>
                  <option class="lead" value="nomeProvincia" title="paraNomes">Província de Nascimento</option>
                </optgroup>

                <optgroup label="Número de Identificação">
                  <option class="lead" value="biAluno" title="paraNumero">Número de BI</option>
                  <option class="lead" value="numeroInterno" title="paraNumero">Número interno</option>
                </optgroup>           
              </select>
            </div>

            <div class="col-lg-3 col-md-3">
              <label>Condição</label>
              <select class="form-control lead" id="operador">
                <option class="lead paraNomes" value="comecar" >Começa com o nome:</option> 
                <option class="lead paraNomes" value="tem" >Tem o Nome:</option>

                <option class="lead paraNomes paraIdade paraNumero paraDataDeNascimento paraGenero" value="=">Igual a:</option>

                <option class="lead paraData paraIdade paraDataDeNascimento" value=">=">Maior ou Igual a:</option>
                <option class="lead naoParaNomes paraIdade paraDataDeNascimento" value=">">Maior a:</option>

                <option class="lead naoParaNomes paraData paraIdade paraDataDeNascimento" value="<=" >Menor ou Igual a:</option>
                <option class="lead naoParaNomes paraData paraIdade paraDataDeNascimento" value="<">Menor a:</option>

                <option class="lead paraDataDeNascimento" value="dia">Nascido no Dia:</option>
                <option class="lead paraDataDeNascimento" value="mes">Nascido no Mês:</option>
                <option class="lead paraDataDeNascimento" value="ano">Nascido no Ano:</option>
              </select>
            </div>

            <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12">
              <label>Valor</label>
              <div class="form-group input-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                   <input type="search" name="" class="form-control lead" id="valorPesquisado">
                  <span class="input-group-addon"><i class="fa fa-search"></i></span>
              </div>    
            </div>
            <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12"><br>
              <button type="submit" class="btn btn-primary btn-lg"><i class="fa fa-search"></i> Pesquisar</button>
            </div>
          </div>
        </div>
      </form>
      <div class="row">
          <div class="col-md-12 col-lg-12">
            <label class="lead">Total: <span id="numTotAlunos" class="quantidadeTotal">0</span></label>
            <label class="lead">Femininos: <span id="numTotAlunosM" class="quantidadeTotal">0</span></label>
            &nbsp;&nbsp;<a href="#" class="btn btn-primary abrirRelatorio" caminho="resumoMatriculas"><i class="fa fa-print"></i> Resumo das Matriculas</a>
             &nbsp;&nbsp;
             <a href="#" class="btn btn-primary abrirRelatorio" caminho="mapaFrequencias.php"><i class="fa fa-print"></i> Mapa de Frequências</a>
             &nbsp;&nbsp;

             <a href="#" class="btn btn-primary abrirRelatorio" caminho="mapaFrequenciasPorTurma"><i class="fa fa-print"></i> Mapa de Frequências Por Turma</a>
             &nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="btn btn-primary abrirRelatorio" caminho="estatisticaAlunosRepetentes"><i class="fa fa-print"></i> Estatística de Repetentes</a>
             <!--&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="lead btn btn-primary abrirRelatorio" caminho="estatistica.php"><i class="fa fa-print"></i> Matrícula</a>!-->
            
          </div>
      </div>
            
      <table id="example1" class="table table-striped table-bordered table-hover" >
      <thead class="corPrimary">
        <tr>
          <th class="lead font-weight-bolder text-center" ><strong><i class='fa fa-sort-numeric-down'></i></strong></th>
          <th class="lead font-weight-bolder"><strong><i class='fa fa-sort-alpha-down'></i> Nome do Aluno</strong></th>
          <th class="lead font-weight-bolder bolder" id="celulaPesquisada">Pesquisa</th>
          <th class="lead font-weight-bolder bolder">Sexo</th>
          <th class="lead font-weight-bolder text-center" >Classe</i></th>
        </tr>
      </thead>
      <tbody id="listaAlunosBody">

      </tbody>
      </table>


     </div> 
    </div>

        <?php } echo "</div><br/>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha(); ?>
