<?php 
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');

    class mapaForcaTrabalho extends funcoesAuxiliares{

        function __construct($caminhoAbsoluto){
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

            if($this->verificacaoAcesso->verificarAcesso("", ["listaAgentes"], [], "")){                   
              $this->mapa();
            }else{
              $this->negarAcesso();
            }
        }

         private function mapa(){
            $this->todosProfessores = $this->entidades("docente", "V");
          $totalProfComponentes=0;
          foreach ($this->todosProfessores as $ent) {
            if($this->ifLeccionaDisciplinaComponente($ent["idPEntidade"])){
              $totalProfComponentes++;
            }
          }

          $totalProfComponentesComAgregPedagog=0;
          foreach ($this->todosProfessores as $ent) {
            if($this->ifLeccionaDisciplinaComponente($ent["idPEntidade"]) && $ent["comFormPedag"]!="V"){
              $totalProfComponentesComAgregPedagog++;
            }
          }

          $cabecalho[] = array('titulo' =>"Nº", "tituloDb"=>"num", "css"=>"text-align:center");
          $cabecalho[] = array('titulo' =>"Nome do Professor", "tituloDb"=>"nomeEntidade", "css"=>"");
          $cabecalho[] = array('titulo' =>"Data de Nascimento", "tituloDb"=>"dataNascEntidade", "css"=>"text-align:center;");
          $cabecalho[] = array('titulo' =>"Com Agregação Ped.", "tituloDb"=>"agregPedag", "css"=>"text-align:center;");

          $cabecalho[] = array('titulo' =>"Habilitação Académica<br>(área de formação base)", "tituloDb"=>"habilitLit", "css"=>"");

          $cabecalho[] = array('titulo' =>"Aréa cientifica que lecciona", "tituloDb"=>"areaCientQueLecciona", "css"=>"");
          $cabecalho[] = array('titulo' =>"Cursos que lecciona", "tituloDb"=>"cursoQueLecciona", "css"=>"");

          $cabecalho[] = array('titulo' =>"Tempo de serviço", "tituloDb"=>"anoServiço", "css"=>"text-align:center;");
          $cabecalho[] = array('titulo' =>"Situação profissional", "tituloDb"=>"naturezaVinc", "css"=>"text-align:center;");



            $this->nomeDirigente("Director");
            $this->html .="<p style='".$this->corPrimary.$this->miniParagrafo."'>1. Identificação das partes</p>
            <p style='".$this->miniParagrafo."'>Província: ".valorArray($this->sobreEscolaLogada, "nomeProvincia")."</p>
            <p style='".$this->miniParagrafo."'>Municipio: ".valorArray($this->sobreEscolaLogada, "nomeMunicipio")."</p><br/>
            <p style='".$this->corPrimary.$this->miniParagrafo."'>2. Dados da direcção</p>"; 
            if($this->sexoDirigente=="M"){
              $this->html .="<p style='".$this->miniParagrafo."'>Nome do Director: <strong>".$this->nomeDirigente."</strong></p>";
            }else{
              $this->html .="<p style='".$this->miniParagrafo."'>Nome da Directora: <strong>".$this->nomeDirigente."</strong></p>";
            }
            $this->html .="
            <p style='".$this->corPrimary.$this->miniParagrafo."'>3. Dados gerais da instituição</p>
            <p style='".$this->miniParagrafo."'>Nº Total de Professores: ".completarNumero(count($this->todosProfessores))."</p>

            <p style='".$this->miniParagrafo."'>Nº de professores que leccionam disciplinas da área de ".tipoDisciplina($this->tipoDisciplina).": ".completarNumero($totalProfComponentes)."</p>

            <p style='".$this->miniParagrafo."'>Nº de professores que leccionam disciplinas da área de ".tipoDisciplina($this->tipoDisciplina)." e sem agregação pedagógica: ".completarNumero($totalProfComponentesComAgregPedagog)."</p><br/>

            <p style='".$this->corPrimary."'>4. Identificação dos professores das áreas de ".tipoDisciplina($this->tipoDisciplina)."</p>

            <table style='".$this->tabela." width:100%;'>
              <tr style='".$this->bolder."'>";
              foreach ($cabecalho as $vet) {
                $this->html .="<td style='".$this->border().$this->text_center.$this->bolder."'>".$vet["titulo"]."</td>";
              }
              $this->html .="</tr>";
              $contador=0;
              foreach ($this->todosProfessores as $ent) {
                if($this->ifLeccionaDisciplinaComponente($ent["idPEntidade"])){
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
                      if($ent["comFormPedag"]=="V"){
                        $valor="X";
                      }
                    }else if($nomeCampo=="habilitLit"){
                      $valor=$ent->cursoLicenciatura;
                    }else if($nomeCampo=="areaCientQueLecciona"){
                      $valor = $this->retornarAreasCientificasQueLecciona($ent["idPEntidade"]);
                    }else if($nomeCampo=="cursoQueLecciona"){
                      $valor = $this->retornarCursosQueLecciona($ent["idPEntidade"]);
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

            <div style='".$this->maiuscula.$this->text_center."'>".$this->assinaturaDirigentes(7)."</div>
            "; 

            //$this->exibir("", "Professores da-".tipoDisciplina($this->tipoDisciplina), "", $this->tamanhoFolha, "landscape");
        }

        function ifLeccionaDisciplinaComponente($idPEntidade){
            /*$retorno=false;
            if(count($this->selectArray("divisaoprofessores", ["idDivAno"=>$this->idPAno, "idDivEscola"=>$_SESSION["idEscolaLogada"], "idDivEntidade"=>$idPEntidade, "idDivs"], ["limit"=>1]))>0){
              $retorno=true;
            }
            return $retorno;*/
        }

        function retornarAreasCientificasQueLecciona($idPEntidade){
          /*  $areaFormacaoCurso="";
            foreach ($this->selectArray("divisaoprofessores LEFT JOIN disciplinas ON idDivDisciplina=idFNomeDisciplina LEFT JOIN nomecursos ON idPNomeCurso=idDiscCurso", "DISTINCT areaFormacaoCurso", "idDivEscola=idDiscEscola AND idDiscCurso=idDivCurso AND idDivAno=:idDivAno AND idDivEscola=:idDivEscola AND tipoDisciplina=:tipoDisciplina AND idDivEntidade=:idDivEntidade", [$this->idPAno, $_SESSION["idEscolaLogada"], $this->tipoDisciplina, $idPEntidade]) as $areas) {

              if($areaFormacaoCurso==""){
                $areaFormacaoCurso = $areas->areaFormacaoCurso;
              }else{
                $areaFormacaoCurso .=", ".$areas->areaFormacaoCurso;
              }
            }
          return $areaFormacaoCurso;*/
        }

        function retornarCursosQueLecciona($idPEntidade){
          /*$nomeCurso="";
          foreach ($this->selectDistinct("divisaoprofessores", ["idDivAno"=>$this->idPAno, "idDivEscola"=>$_SESSION["idEscolaLogada"], "tipoDisciplina"=>$this->tipoDisciplina, "idDivEntidade"=>$idPEntidade]) as $areas) {
              
            $curso = $this->selectArray("nomecursos", "*", "idPNomeCurso=:idPNomeCurso", [$areas->idPNomeCurso]);

            if($nomeCurso==""){ 
              $nomeCurso = valorArray($curso, "nomeCurso")." (".valorArray($curso, "areaFormacaoCurso").")";
            }else{
              $nomeCurso .=", ".valorArray($curso, "nomeCurso")." (".valorArray($curso, "areaFormacaoCurso").")";
            }
          }
          return $nomeCurso;*/
        }       
    }

new mapaForcaTrabalho(__DIR__);
    
    
  
?>