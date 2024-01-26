<?php 
  session_start();
  
   include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/manipulacaoDadosDoAjax.php';
    class manipulacaoDadosDoAjaxInternoAvaliacaoAnual extends manipulacaoDadosAjax{           

      function __construct($caminhoAbsoluto){
        parent::__construct();


        $this->trimestre=isset($_GET["trimestre"])?$_GET["trimestre"]:"";
        if($this->accao=="gravarAvaliacaoProfessor"){

          if($this->verificacaoAcesso->verificarAcesso("", ["avaliacaoDesempenhoProfessor"])){
              
                $this->gravarAvaliacaoProfessor();
          }          
        }else if($this->accao=="manipularAvaliacaoDesempenho"){
          if($this->verificacaoAcesso->verificarAcesso("", ["avaliacaoDesempenhoProfessor"])){
                $this->manipularAvaliacaoDesempenho();
          }          
        }
      }

      private function manipularAvaliacaoDesempenho(){
        $this->trimestre=isset($_POST["trimestre"])?$_POST["trimestre"]:"";
        $this->idPAno = isset($_POST["idPAno"])?$_POST["idPAno"]:"";
        $dados = isset($_POST["dadosEnviar"])?$_POST["dadosEnviar"]:"";

        $coordenador=isset($_POST["coordenador"])?$_POST["coordenador"]:"";
        $coordenadorAdjunto=isset($_POST["coordenadorAdjunto"])?$_POST["coordenadorAdjunto"]:"";
        $vogal1=isset($_POST["vogal1"])?$_POST["vogal1"]:"";
        $vogal2=isset($_POST["vogal2"])?$_POST["vogal2"]:"";
        $vogal3=isset($_POST["vogal3"])?$_POST["vogal3"]:"";
        $secretario=isset($_POST["secretario"])?$_POST["secretario"]:"";
        $dataInicial=isset($_POST["dataInicial"])?$_POST["dataInicial"]:"";
        $dataFinal=isset($_POST["dataFinal"])?$_POST["dataFinal"]:"";

        $this->inserir("comissAvalDesempProfessor", "idComAvalProf", "idEscola, idAno, trimestre, chavePrincipal", [$_SESSION['idEscolaLogada'], $this->idPAno, $this->trimestre, $_SESSION['idEscolaLogada']."-".$this->idPAno."-".$this->trimestre]);

        $this->editar("comissAvalDesempProfessor", "coordenador, coordenadorAdjunto, vogal1, vogal2, vogal3, secretario, dataInicial, dataFinal", [$coordenador, $coordenadorAdjunto, $vogal1, $vogal2, $vogal3, $secretario, $dataInicial, $dataFinal], ["idEscola"=>$_SESSION['idEscolaLogada'], "idAno"=>$this->idPAno, "trimestre"=>$this->trimestre]);

        foreach(json_decode($dados) as $a){

          $this->editarItemObjecto("entidadesprimaria", "aval_desemp", "qualProcEnsAprend".$this->trimestre.", aperfProfissional".$this->trimestre.", PA".$this->trimestre.", resposabilidade".$this->trimestre.", relHum".$this->trimestre.", comentario".$this->trimestre.", dataAvaliacao".$this->trimestre, [$a->QPEA, $a->aperfProf, $a->PA, $a->resposabilidade, $a->relHum, trim($a->comentario), $a->dataAvaliacao], ["idPEntidade"=>$a->idPEntidade], ["idPAvalDesEnt"=>$a->idPAvalDesEnt]);

          if($this->trimestre!="IV"){
            $qualProcEnsAprendIV=0;
            $aperfProfissionalIV=0;
            $PAIV=0;
            $resposabilidadeIV=0;
            $relHumIV=0;
            foreach($this->selectArray("entidadesprimaria", ["aval_desemp.qualProcEnsAprendI", "aval_desemp.qualProcEnsAprendII", "aval_desemp.qualProcEnsAprendIII", "aval_desemp.aperfProfissionalI", "aval_desemp.aperfProfissionalII", "aval_desemp.aperfProfissionalIII", "aval_desemp.PAI", "aval_desemp.PAII", "aval_desemp.PAIII", "aval_desemp.resposabilidadeI", "aval_desemp.resposabilidadeII", "aval_desemp.resposabilidadeIII", "aval_desemp.relHumI", "aval_desemp.relHumII", "aval_desemp.relHumIII"], ["idPEntidade"=>$a->idPEntidade, "aval_desemp.idAvalEntAno"=>$this->idPAno, "aval_desemp.idAvalEntEscola"=>$_SESSION['idEscolaLogada']], ["aval_desemp"]) as $prof){

              $qualProcEnsAprendIV +=intval(isset($prof["aval_desemp"]["qualProcEnsAprendI"])?$prof["aval_desemp"]["qualProcEnsAprendI"]:0);
              $qualProcEnsAprendIV +=intval(isset($prof["aval_desemp"]["qualProcEnsAprendII"])?$prof["aval_desemp"]["qualProcEnsAprendII"]:0);
              $qualProcEnsAprendIV +=intval(isset($prof["aval_desemp"]["qualProcEnsAprendIII"])?$prof["aval_desemp"]["qualProcEnsAprendIII"]:0);

              $aperfProfissionalIV +=intval(isset($prof["aval_desemp"]["aperfProfissionalI"])?$prof["aval_desemp"]["aperfProfissionalI"]:0);
              $aperfProfissionalIV +=intval(isset($prof["aval_desemp"]["aperfProfissionalII"])?$prof["aval_desemp"]["aperfProfissionalII"]:0);
              $aperfProfissionalIV +=intval(isset($prof["aval_desemp"]["aperfProfissionalIII"])?$prof["aval_desemp"]["aperfProfissionalIII"]:0);

              $PAIV +=intval(isset($prof["aval_desemp"]["PAI"])?$prof["aval_desemp"]["PAI"]:0);
              $PAIV +=intval(isset($prof["aval_desemp"]["PAII"])?$prof["aval_desemp"]["PAII"]:0);
              $PAIV +=intval(isset($prof["aval_desemp"]["PAIII"])?$prof["aval_desemp"]["PAIII"]:0);

              $resposabilidadeIV +=intval(isset($prof["aval_desemp"]["resposabilidadeI"])?$prof["aval_desemp"]["resposabilidadeI"]:0);
              $resposabilidadeIV +=intval(isset($prof["aval_desemp"]["resposabilidadeII"])?$prof["aval_desemp"]["resposabilidadeII"]:0);
              $resposabilidadeIV +=intval(isset($prof["aval_desemp"]["resposabilidadeIII"])?$prof["aval_desemp"]["resposabilidadeIII"]:0);

              $relHumIV +=intval(isset($prof["aval_desemp"]["relHumI"])?$prof["aval_desemp"]["relHumI"]:0);
              $relHumIV +=intval(isset($prof["aval_desemp"]["relHumII"])?$prof["aval_desemp"]["relHumII"]:0);
              $relHumIV +=intval(isset($prof["aval_desemp"]["relHumIII"])?$prof["aval_desemp"]["relHumIII"]:0);
            }
            $this->editarItemObjecto("entidadesprimaria", "aval_desemp", "qualProcEnsAprendIV, aperfProfissionalIV, PAIV, resposabilidadeIV, relHumIV", [number_format($qualProcEnsAprendIV/3, 2), number_format($aperfProfissionalIV/3, 2), number_format($PAIV/3, 2), number_format($resposabilidadeIV/3, 2), number_format($relHumIV/3, 2)], ["idPEntidade"=>$a->idPEntidade], ["idPAvalDesEnt"=>$a->idPAvalDesEnt]);
          }
        }

        $this->listar();
      }

      private function gravarAvaliacaoProfessor (){
        $this->idPAno = isset($_GET["idPAno"])?$_GET["idPAno"]:"";
        
        foreach ($this->entidades(["idPEntidade", "escola.nivelSistemaEntidade"], "docente", "V") as $prof) {
            
            if(!($prof["escola"]["nivelSistemaEntidade"]=="Director" || $prof["escola"]["nivelSistemaEntidade"]=="Pedagógico" || $prof["escola"]["nivelSistemaEntidade"]=="Administrativo")){

              $chave=$this->idPAno."-".$_SESSION["idEscolaLogada"]."-".$prof["idPEntidade"];

              $this->inserirObjecto("entidadesprimaria", "aval_desemp", "idPAvalDesEnt", "idAvalEntEnt, idAvalEntAno, idAvalEntEscola, chaveAvaliacao, comentarioI, comentarioII, comentarioIII, comentarioIV", [$prof["idPEntidade"], $this->idPAno, $_SESSION["idEscolaLogada"], $chave,
                "O Professor revela boa capacidade de análise e algum interesse sistemático no trabalho, Bom conhecimento do trabalho, organizado e com muito sentido de responsabilidade, sigiloso, muito criativo, pouco interativo, e incentiva pouco ambiente",
                "O Professor revela boa capacidade de análise e algum interesse sistemático no trabalho, Bom conhecimento do trabalho, organizado e com muito sentido de responsabilidade, sigiloso, muito criativo, pouco interativo, e incentiva pouco ambiente",
                "O Professor revela boa capacidade de análise e algum interesse sistemático no trabalho, Bom conhecimento do trabalho, organizado e com muito sentido de responsabilidade, sigiloso, muito criativo, pouco interativo, e incentiva pouco ambiente",
                "O Professor revela boa capacidade de análise e algum interesse sistemático no trabalho, Bom conhecimento do trabalho, organizado e com muito sentido de responsabilidade, sigiloso, muito criativo, pouco interativo, e incentiva pouco ambiente"], ["idPEntidade"=>$prof["idPEntidade"]]);
            }
        }
        $this->listar();
      }

      function listar(){
        echo $this->selectJson("entidadesprimaria", [], ["escola.idEntidadeEscola"=>$_SESSION['idEscolaLogada'], "escola.estadoActividadeEntidade"=>"A", "escola.tipoPessoal"=>"docente", "aval_desemp.idAvalEntAno"=>$this->idPAno, "aval_desemp.idAvalEntEscola"=>$_SESSION['idEscolaLogada']], ["escola", "aval_desemp"], "", [], ["nomeEntidade"=>1]);
      }

      
    }
    new manipulacaoDadosDoAjaxInternoAvaliacaoAnual(__DIR__);
?>