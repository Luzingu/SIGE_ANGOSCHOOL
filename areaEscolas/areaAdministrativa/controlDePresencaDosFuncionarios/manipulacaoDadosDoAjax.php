<?php 
  session_start();
  
   include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
  include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/manipulacaoDadosDoAjax.php';
    class manipulacaoDadosDoAjaxInternoAvaliacaoAnual extends manipulacaoDadosAjax{           

      function __construct($caminhoAbsoluto){
        parent::__construct();
        if($this->accao=="manipularControlFaltas"){
          if($this->verificacaoAcesso->verificarAcesso("", ["controlDePresencaDosFuncionarios"])){
                $this->manipularControlFaltas();
          }          
        }
      }

      private function manipularControlFaltas(){
        $this->idPAno = isset($_POST["idPAno"])?$_POST["idPAno"]:"";
        $mes = isset($_POST["mes"])?$_POST["mes"]:"";
        
        $anoCivil = isset($_POST["anoCivil"])?$_POST["anoCivil"]:"";
        $dados = isset($_POST["dados"])?$_POST["dados"]:array();

        $dados = json_decode($dados);

        $entidades =array();
        foreach($dados as $dado){
          if(!seTemValorNoArray($entidades, $dado->idPEntidade)){
            $entidades[]=$dado->idPEntidade;
          }
        }

        foreach($entidades as $entidade){
          
          foreach (array_filter($dados, function ($mamale) use ($entidade){
              return $mamale->idPEntidade==$entidade;
          }) as $dado){
            $implicacao="";

            $this->excluirItemObjecto("entidadesprimaria", "controlPresenca", ["idPEntidade"=>$dado->idPEntidade], ["data"=>$dado->data, "idEscola"=>$_SESSION['idEscolaLogada']]);

            $falta = explode("-", $dado->falta);
            $berta = $falta[0];
            $ntonto = $falta[1];

            if($berta=="T" || $ntonto=="NP"){
              $implicacao="T";
            }
            $this->inserirObjecto("entidadesprimaria", "controlPresenca", "idPControl", "idEscola, idEntidade, data, faltas, implicacao, presencas", [$_SESSION['idEscolaLogada'], $entidade, $dado->data, $ntonto, $implicacao, $dado->presenca], ["idPEntidade"=>$entidade]);
          }
          $this->excluirItemObjecto("entidadesprimaria", "contadorFaltas", ["idPEntidade"=>$entidade], ["mes"=>$mes, "anoCivil"=>$anoCivil, "idEscola"=>$_SESSION['idEscolaLogada']]);

          $contFaltas=0;
          $contAusencias=0;

          $tempoTotLeccionado=0;
          $tempoTotNaoLeccionado=0;

          foreach($this->selectArray("entidadesprimaria", [], ["idPEntidade"=>$entidade, "controlPresenca.data"=>new \MongoDB\BSON\Regex($anoCivil."-".completarNumero($mes)."-"), "controlPresenca.idEscola"=>$_SESSION['idEscolaLogada']], ["controlPresenca"]) as $luzingu){

            if($luzingu["controlPresenca"]["implicacao"]=="T"){
              $contFaltas++;
            }else{
              $contAusencias+=intval($luzingu["controlPresenca"]["faltas"]);
            }
            if($luzingu["controlPresenca"]["presencas"]!="P" && $luzingu["controlPresenca"]["presencas"]!="NP"){

              $tempoTotLeccionado +=intval($luzingu["controlPresenca"]["presencas"]);
              $tempoTotNaoLeccionado +=intval($luzingu["controlPresenca"]["faltas"]);
            }
          }
          $contFaltas +=intdiv($contAusencias, 6);
          $contAusencias = $contAusencias-intdiv($contAusencias, 6)*6;
          $this->inserirObjecto("entidadesprimaria", "contadorFaltas", "idPContador", "idEscola, idEntidade, mes, anoCivil, ausencias, faltas, tempoTotLeccionado, tempoTotNaoLeccionado", [$_SESSION['idEscolaLogada'], $entidade, $mes, $anoCivil, $contAusencias, $contFaltas, $tempoTotLeccionado, $tempoTotNaoLeccionado], ["idPEntidade"=>$entidade]);
        }
        echo json_encode($this->selectArray("entidadesprimaria", ["idPEntidade", "nomeEntidade", "numeroInternoEntidade", "fotoEntidade", "controlPresenca.data", "controlPresenca.faltas", "controlPresenca.presencas", "controlPresenca.idEscola", "contadorFaltas.idEscola", "contadorFaltas.mes", "contadorFaltas.anoCivil", "contadorFaltas.ausencias", "contadorFaltas.faltas"], ["escola.idEntidadeEscola"=>$_SESSION['idEscolaLogada'], "escola.estadoActividadeEntidade"=>"A", "escola.tipoPessoal"=>"docente"], ["escola"], "", [], ["nomeEntidade"=>1]));
      }

      
    }
    new manipulacaoDadosDoAjaxInternoAvaliacaoAnual(__DIR__);
?>