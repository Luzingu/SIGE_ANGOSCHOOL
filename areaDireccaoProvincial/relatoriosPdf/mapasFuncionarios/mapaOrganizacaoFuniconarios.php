<?php 
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    if(!isset($_SESSION["directorioPaterno"])){
      $protocolo = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS']=="on") ? "https" : "http");
      echo "<script>window.location='".$protocolo."://".$_SERVER["HTTP_HOST"]."'</script>";
    }
    include_once $_SESSION["directorioPaterno"].'angoschool/areaDireccaoProvincial/funcoesAuxiliares.php';
    include_once $_SESSION["directorioPaterno"].'angoschool/areaDireccaoProvincial/funcoesAuxiliaresDb.php';

    class mapaOrganizacaoFuncionarios extends funcoesAuxiliares{

        function __construct($caminhoAbsoluto){
            $this->caminhoRetornar = retornarCaminhoRecuarArquivosPhp(__DIR__);

            parent::__construct("Rel-Mapa de Orgização de Funcionários");
            $this->idPAno = $this->idAnoActual;
            $this->numAno();
            $this->tamanhoFolha = isset($_GET["tamanhoFolha"])?$_GET["tamanhoFolha"]:"A3";
             if($this->tamanhoFolha!="A0" || $this->tamanhoFolha!="A1" || $this->tamanhoFolha!="A2"){
                $this->tamanhoFolha="A2";
            }
            $this->html="<html style='margin:15px;'>
            <head>
                <style>
                  table tr td{
                    padding:3px;
                  }
                </style>
            </head>
            <body>";

            if($this->verificacaoAcesso->verificarAcessoAlteracao(["aDirectoria", "aAdministrativa", "aPedagogica"], "", "", "")){ 

                $this->mapa();

                
            }else{
              $this->negarAcesso();
            }
        }

         private function mapa(){
            $this->lista = array();
            foreach($this->selectArray("escolas LEFT JOIN div_terit_comunas ON idPComuna=comuna LEFT JOIN div_terit_municipios ON idPMunicipio=municipio LEFT JOIN div_terit_provincias ON idPProvincia=provincia", "DISTINCT idPEscola", "tipoInstituicao=:tipoInstituicao AND provincia=:provincia AND idPEscola!=4 AND privacidadeEscola=:privacidadeEscola AND estadoEscola=:estadoEscola", ["escola", valorArray($this->sobreUsuarioLogado, "provincia"), "Pública", "A"], "idPEscola ASC") as $a){
              $this->lista = array_merge($this->lista, $this->selectArray("entidadesprimaria LEFT JOIN dadosadicionasentidade ON idDAEntidade=idPEntidade LEFT JOIN entidade_escola ON idFEntidade=idPEntidade LEFT JOIN escolas ON idPEscola=idEntidadeEscola", "*", "estadoActividadeEntidade=:estadoActividadeEntidade AND idEntidadeEscola=:idEntidadeEscola AND efectividade=:efectividade", ["A", $a->idPEscola, "V"], "nomeEntidade ASC"));
            }

            $this->html .="            
              <div style='page-break-after: always;'><div><div style='margin-top:20px; margin-left:50px; width:400px; position:absolute;' style='".$this->maiuscula."'>".$this->assinaturaDirigentes("DP")."</div></div>".$this->cabecalho();

            $this->html .="<br/><p style='".$this->text_center.$this->bolder.$this->maiuscula."margin-bottom:-30px;'>MAPA DE ORGANIZAÇÃO DOS FUNCIONÁRIOS</p>";

              $cabecalho[] = array('titulo'=>"Nº", "tituloDb"=>"nº");
              $cabecalho[] = array('titulo'=>"Nº de<br>Agente", "tituloDb"=>"numeroAgenteEntidade");
              $cabecalho[] = array('titulo'=>"Foto Tipo Passe", "tituloDb"=>"foto");
              $cabecalho[] = array('titulo'=>"Nome Completo do Professor", "tituloDb"=>"nomeEntidade");
              $cabecalho[] = array('titulo'=>"Categoria", "tituloDb"=>"categoriaEntidade");
              $cabecalho[] = array('titulo'=>"Função", "tituloDb"=>"funcaoEnt");
              $cabecalho[] = array('titulo'=>"Data Inicio de<br/>Funções", "tituloDb"=>"dataInicioFuncoesEntidade");
              $cabecalho[] = array('titulo'=>"Nº do BI", "tituloDb"=>"biEntidade");
              $cabecalho[] = array('titulo'=>"Curso que Fez no Ensino Médio", "tituloDb"=>"cursoEnsinoMedio");
              $cabecalho[] = array('titulo'=>"Escola Onde Fez Ensino Médio", "tituloDb"=>"escolaEnsinoMedio");
              $cabecalho[] = array('titulo'=>"Curso que Fez na Licenciatura", "tituloDb"=>"cursoLicenciatura");
              $cabecalho[] = array('titulo'=>"Escola Onde Fez a Licenciatura", "tituloDb"=>"escolaLicenciatura");
             $cabecalho[] = array('titulo'=>"Curso que Fez no Pós-Graduação", "tituloDb"=>"cursoMestrado");
              $cabecalho[] = array('titulo'=>"Escola Onde a Pós-Graduação", "tituloDb"=>"escolaMestrado");
              $cabecalho[] = array('titulo'=>"Disciplina(s) que Lecciona", "tituloDb"=>"discLeciona");
              $cabecalho[] = array('titulo'=>"Classe(s) que Lecciona", "tituloDb"=>"classeLeciona");
              $cabecalho[] = array('titulo'=>"Turma(s) que Lecciona", "tituloDb"=>"turmaLeciona");
              $cabecalho[] = array('titulo'=>"Telefone", "tituloDb"=>"numeroTelefoneEntidade");
              $cabecalho[] = array('titulo'=>"E-mail", "tituloDb"=>"emailEntidade");



            $this->html .="<table style='".$this->tabela."width:100%;margin-top:60px;font-size:11pt;'>
            <tr style='".$this->corDanger."'>";
            foreach ($cabecalho as $cab) {
                $this->html .="<td style='".$this->border().$this->bolder.$this->text_center."'>".$cab["titulo"]."</td>";
            }
            $this->html .="</tr>";


            $i=0;
            foreach ($this->lista as $profs) {
                $i++;
               if($i%2==0){
                   $this->html .="<tr style='".$this->backGround("rgb(220,220,220)")."'>";
                }else{
                    $this->html .="<tr>";
                }

               foreach ($cabecalho as $cab) {
                  $valDado=""; 
                  if($cab["tituloDb"]=="nº"){
                    $valDado=completarNumero($i);
                  }else if($cab["tituloDb"]=="foto"){
                    $valDado="<img src='../../../fotoUsuarios/".$profs->fotoEntidade."' style='border:solid #428bca 1px; border-radius: 10px; width: 100px; height: 110px;'><br/>";

                  }else if($cab["tituloDb"]=="dataInicioFuncoesEntidade"){
                    $valDado=converterData($profs->dataInicioFuncoesEntidade);
                  }else if($cab["tituloDb"]=="discLeciona") {
                      $disc="";
                      foreach ($this->selectArray("divisaoprofessores LEFT JOIN nomedisciplinas ON idPNomeDisciplina=idDivDisciplina", "DISTINCT abreviacaoDisciplina1", "idDivEntidade=:idDivEntidade AND idDivEscola=:idDivEscola AND idDivAno=:idDivAno", [$profs->idPEntidade, $profs->idPEscola, $this->idPAno], "nomeDisciplina ASC") as $disciplina) {
                          if($disc==""){
                              $disc .=$disciplina->abreviacaoDisciplina1;
                          }else{
                              $disc .=", ".$disciplina->abreviacaoDisciplina1;
                          }                            
                      }
                    $valDado = $disc;
                  }else if($cab["tituloDb"]=="classeLeciona") {
                      $classe="";
                      foreach($this->selectArray("divisaoprofessores LEFT JOIN nomedisciplinas ON idPNomeDisciplina=idDivDisciplina", "DISTINCT classe", "idDivEntidade=:idDivEntidade AND idDivEscola=:idDivEscola AND idDivAno=:idDivAno", [$profs->idPEntidade, $profs->idPEscola, $this->idPAno], "classe ASC") as $disciplina) {
                          if($classe==""){
                              $classe .=$disciplina->classe."ª";
                          }else{
                              $classe .=", ".$disciplina->classe."ª";
                          }                            
                      }
                    $valDado = $classe;
                  }else if($cab["tituloDb"]=="turmaLeciona"){
                      $cursosProfessor=array(); 
                      foreach ($this->selectArray("divisaoprofessores", "DISTINCT idDivCurso", "idDivEntidade=:idDivEntidade AND idDivEscola=:idDivEscola AND idDivAno=:idDivAno", [$profs->idPEntidade, $profs->idPEscola, $this->idPAno], "idDivCurso ASC") as $curso) {
                          $cursosProfessor[] = $curso->idDivCurso;                            
                      }
                      $classsesProfessor=array();
                      foreach ($this->selectArray("divisaoprofessores", "DISTINCT classe", "idDivEntidade=:idDivEntidade AND idDivEscola=:idDivEscola AND idDivAno=:idDivAno", [$profs->idPEntidade, $profs->idPEscola, $this->idPAno], "classe ASC") as $classes) {
                          $classsesProfessor[] = $classes->classe;
                      }
                      $tur="";
                      foreach ($cursosProfessor as $curso) {
                          foreach ($classsesProfessor as $classe) {
                             foreach ($this->selectArray("divisaoprofessores", "DISTINCT designacaoTurmaDiv", "idDivEntidade=:idDivEntidade AND idDivEscola=:idDivEscola AND idDivAno=:idDivAno AND classe=:classe AND idDivCurso=:idDivCurso", [$profs->idPEntidade, $profs->idPEscola, $this->idPAno, $classe, $curso], "designacaoTurmaDiv ASC") as $turma) {
                                  
                                  $jp = $this->selectUmElemento("nomecursos", "abrevCurso", "idPNomeCurso=:idPNomeCurso", [$curso]).$classe.$turma->designacaoTurmaDiv;
                                  
                                  if($tur==""){
                                      $tur .=$jp;
                                  }else{
                                      $tur .=", ".$jp;
                                  }
                              }
                          }
                      }
                    $valDado = $tur;
                  }else{
                    $tDb = $cab["tituloDb"];
                    $valDado = $profs->$tDb;
                  }
                  $this->html .="<td style='".$this->border().$this->text_center."'>".$valDado."</td>";
                }

               $this->html .="</tr>";
            }
            $this->html .="</table>";

            $this->html .="<div style='margin-top:30px;'><p  style='font-size:16pt;".$this->maiuscula.$this->text_center."'>".$this->rodape()."</p><br/><br/><div style='".$this->maiuscula.$this->text_center."'>".$this->assinaturaDirigentes("CDARH")."</div><div>

          </div></div></div>";

            $this->exibir("", "Mapa de Orgização dos Funcionários-".$this->numAno, "", $this->tamanhoFolha, "landscape");

            
        }
    }

new mapaOrganizacaoFuncionarios(__DIR__);
?>