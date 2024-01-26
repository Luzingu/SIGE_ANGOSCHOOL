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

    class mapaForcaTrabalho extends funcoesAuxiliares{

        function __construct($caminhoAbsoluto){
            $this->caminhoRetornar = retornarCaminhoRecuarArquivosPhp(__DIR__);

            parent::__construct("Rel-Professor por Tipo de Disciplina");
            $this->idPAno = $this->idAnoActual;
            $this->numAno();
            $this->tamanhoFolha = isset($_GET["tamanhoFolha"])?$_GET["tamanhoFolha"]:"A3";
            $this->tipoDisciplina = isset($_GET["tipoDisciplina"])?$_GET["tipoDisciplina"]:"FE";

            $this->html="<html>
            <head>
                <title>Professores da ".tipoDisciplina($this->tipoDisciplina)."</title>
            </head>
            <body>";

            if($this->verificacaoAcesso->verificarAcessoAlteracao(["aDirectoria", "aAdministrativa", "aPedagogica"], "", "", "")){                   
                $this->mapa();
            }else{
              $this->negarAcesso();
            }
        }

         private function mapa(){

          $this->todosProfessores=array();
          foreach($this->selectArray("escolas LEFT JOIN div_terit_comunas ON idPComuna=comuna LEFT JOIN div_terit_municipios ON idPMunicipio=municipio LEFT JOIN div_terit_provincias ON idPProvincia=provincia", "DISTINCT idPEscola", "tipoInstituicao=:tipoInstituicao AND provincia=:provincia AND idPEscola!=4 AND privacidadeEscola=:privacidadeEscola AND estadoEscola=:estadoEscola", ["escola", valorArray($this->sobreUsuarioLogado, "provincia"), "Pública", "A"], "idPEscola ASC") as $a){
            $this->todosProfessores =array_merge($this->todosProfessores, $this->selectArray("entidadesprimaria LEFT JOIN dadosadicionasentidade ON idDAEntidade=idPEntidade LEFT JOIN entidade_escola ON idFEntidade=idPEntidade LEFT JOIN escolas ON idPEscola=idEntidadeEscola", "*", "estadoActividadeEntidade=:estadoActividadeEntidade AND idEntidadeEscola=:idEntidadeEscola AND tipoPessoal=:tipoPessoal AND efectividade=:efectividade AND tipoPessoal='docente'", ["A", $a->idPEscola, "docente", "V"], "nomeEntidade ASC"));
          }

          $cabecalho[] = array('titulo' =>"Nº", "tituloDb"=>"num", "css"=>"text-align:center");
          $cabecalho[] = array('titulo' =>"Nome do Professor", "tituloDb"=>"nomeEntidade", "css"=>"");
          $cabecalho[] = array('titulo' =>"Data de Nascimento", "tituloDb"=>"dataNascEntidade", "css"=>"text-align:center;");
          $cabecalho[] = array('titulo' =>"Com Agregação Ped.", "tituloDb"=>"agregPedag", "css"=>"text-align:center;");

          $cabecalho[] = array('titulo' =>"Habilitação Académica<br>(área de formação base)", "tituloDb"=>"habilitLit", "css"=>"");

          $cabecalho[] = array('titulo' =>"Aréa cientifica que lecciona", "tituloDb"=>"areaCientQueLecciona", "css"=>"");
          $cabecalho[] = array('titulo' =>"Cursos que lecciona", "tituloDb"=>"cursoQueLecciona", "css"=>"");

          $cabecalho[] = array('titulo' =>"Situação profissional", "tituloDb"=>"naturezaVinc", "css"=>"text-align:center;");



            $this->html .="<div style='position: absolute;'><div style='margin-top: 10px; width:280px;'>".$this->assinaturaDirigentes("DP")."</div></div>".$this->cabecalho()."

            <br/><p style='".$this->maiuscula.$this->text_center.$this->bolder."'>Identificação dos professores das áreas de ".tipoDisciplina($this->tipoDisciplina)."</p>

            <table style='".$this->tabela." width:100%;'>
              <tr style='".$this->bolder."'>";
              foreach ($cabecalho as $vet) {
                $this->html .="<td style='".$this->border().$this->text_center.$this->bolder."'>".$vet["titulo"]."</td>";
              }
              $this->html .="</tr>";
              $contador=0;
              foreach ($this->todosProfessores as $ent) {
                if($this->ifLeccionaDisciplinaComponente($a->idPEscola, $ent->idPEntidade)){
                  $contador++;

                  if($contador%2==0){
                    $this->html .="<tr style='".$this->backGround("rgb(220,220,220)")."'>";
                  }else{
                      $this->html .="<tr>";
                  }
                  foreach ($cabecalho as $vet) {
                    $nomeCampo = $vet["tituloDb"];

                    $valor="";
                    if($nomeCampo=="num"){
                      $valor=completarNumero($contador);
                    }else if($nomeCampo=="nomeEntidade"){
                      $valor=$ent->nomeEntidade;
                    }else if($nomeCampo=="dataNascEntidade"){
                      $valor=converterData($ent->dataNascEntidade);
                    }else if($nomeCampo=="agregPedag"){
                      if($ent->comFormPedag=="V"){
                        $valor="X";
                      }
                    }else if($nomeCampo=="habilitLit"){
                      $valor=$ent->cursoLicenciatura;
                    }else if($nomeCampo=="areaCientQueLecciona"){
                      $valor = $this->retornarAreasCientificasQueLecciona($a->idPEscola, $ent->idPEntidade);
                    }else if($nomeCampo=="cursoQueLecciona"){
                      $valor = $this->retornarCursosQueLecciona($a->idPEscola, $ent->idPEntidade);
                    }else if($nomeCampo=="naturezaVinc"){
                      $valor = $ent->naturezaVinc;
                    }else if($nomeCampo=="anoServiço"){
                        if(calcularIdade(explode("-", $this->dataSistema)[0], $ent->dataInicioFuncoesEntidade)==1){
                            $valor = calcularIdade(explode("-", $this->dataSistema)[0], $ent->dataInicioFuncoesEntidade)." Ano";
                        }else{
                            $valor = calcularIdade(explode("-", $this->dataSistema)[0], $ent->dataInicioFuncoesEntidade)." Anos";
                        }
                      
                    }

                    $this->html .="<td style='".$this->border().$vet["css"]."'>".$valor."</td>";
                  }
                  $this->html .="</tr>";

                }
              }
            $this->html .="</table><br/>

            <div style='".$this->maiuscula.$this->text_center."'>".$this->assinaturaDirigentes("CDARH")."</div>
            "; 

            $this->exibir("", "Professores da-".tipoDisciplina($this->tipoDisciplina), "", $this->tamanhoFolha, "landscape");
        }

        function ifLeccionaDisciplinaComponente($idPEscola, $idPEntidade){
            $retorno=false;
            if(count($this->selectArray("divisaoprofessores LEFT JOIN disciplinas ON idDivDisciplina=idFNomeDisciplina", "*", "idDivEscola=idDiscEscola AND idDiscCurso=idDivCurso AND idDivAno=:idDivAno AND idDivEscola=:idDivEscola AND tipoDisciplina=:tipoDisciplina AND idDivEntidade=:idDivEntidade", [$this->idPAno, $idPEscola, $this->tipoDisciplina, $idPEntidade]))>0){
              $retorno=true;
            }
            return $retorno;
        }

        function retornarAreasCientificasQueLecciona($idPEscola, $idPEntidade){
            $areaFormacaoCurso="";
            foreach ($this->selectArray("divisaoprofessores LEFT JOIN disciplinas ON idDivDisciplina=idFNomeDisciplina LEFT JOIN nomecursos ON idPNomeCurso=idDiscCurso", "DISTINCT areaFormacaoCurso", "idDivEscola=idDiscEscola AND idDiscCurso=idDivCurso AND idDivAno=:idDivAno AND idDivEscola=:idDivEscola AND tipoDisciplina=:tipoDisciplina AND idDivEntidade=:idDivEntidade", [$this->idPAno, $idPEscola, $this->tipoDisciplina, $idPEntidade]) as $areas) {

              if($areaFormacaoCurso==""){
                $areaFormacaoCurso = $areas->areaFormacaoCurso;
              }else{
                $areaFormacaoCurso .=", ".$areas->areaFormacaoCurso;
              }
            }
          return $areaFormacaoCurso;
        }

        function retornarCursosQueLecciona($idPEscola, $idPEntidade){
          $nomeCurso="";
          foreach ($this->selectArray("divisaoprofessores LEFT JOIN disciplinas ON idDivDisciplina=idFNomeDisciplina LEFT JOIN nomecursos ON idPNomeCurso=idDiscCurso", "DISTINCT idPNomeCurso", "idDivEscola=idDiscEscola AND idDiscCurso=idDivCurso AND idDivAno=:idDivAno AND idDivEscola=:idDivEscola AND tipoDisciplina=:tipoDisciplina AND idDivEntidade=:idDivEntidade", [$this->idPAno, $idPEscola, $this->tipoDisciplina, $idPEntidade]) as $areas) {
              
            $curso = $this->selectArray("nomecursos", "*", "idPNomeCurso=:idPNomeCurso", [$areas->idPNomeCurso]);

            if($nomeCurso==""){ 
              $nomeCurso = valorArray($curso, "nomeCurso")." (".valorArray($curso, "areaFormacaoCurso").")";
            }else{
              $nomeCurso .=", ".valorArray($curso, "nomeCurso")." (".valorArray($curso, "areaFormacaoCurso").")";
            }
          }
          return $nomeCurso;
        }       
    }

new mapaForcaTrabalho(__DIR__);
    
    
  
?>