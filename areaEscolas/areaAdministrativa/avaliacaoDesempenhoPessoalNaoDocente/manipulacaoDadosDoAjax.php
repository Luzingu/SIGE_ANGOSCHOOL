<?php
  session_start();

   include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/manipulacaoDadosDoAjax.php';
    class manipulacaoDadosDoAjaxInternoAvaliacaoAnual extends manipulacaoDadosAjax{

      function __construct($caminhoAbsoluto){
        parent::__construct();

        if($this->accao=="gravarAvaliacaoProfessor"){

          if($this->verificacaoAcesso->verificarAcesso("", ["avaliacaoDesempenhoPessoalNaoDocente"])){

                $this->gravarAvaliacaoProfessor();
          }
        }else if($this->accao=="manipularAvaliacaoDesempenho"){

          if($this->verificacaoAcesso->verificarAcesso("", ["avaliacaoDesempenhoPessoalNaoDocente"])){
                $this->manipularAvaliacaoDesempenho();
          }
        }
      }

      private function manipularAvaliacaoDesempenho(){
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

        $this->inserir("comissAvalDesempPessoalNaoDocente", "idComAvalPessoal", "idEscola, idAno, chavePrincipal", [$_SESSION['idEscolaLogada'], $this->idPAno, $_SESSION['idEscolaLogada']."-".$this->idPAno]);

        $this->editar("comissAvalDesempPessoalNaoDocente", "coordenador, coordenadorAdjunto, vogal1, vogal2, vogal3, secretario, dataInicial, dataFinal", [$coordenador, $coordenadorAdjunto, $vogal1, $vogal2, $vogal3, $secretario, $dataInicial, $dataFinal], ["idEscola"=>$_SESSION['idEscolaLogada'], "idAno"=>$this->idPAno]);

        foreach(json_decode($dados) as $a){
          $this->editarItemObjecto("entidadesprimaria", "aval_desemp", "CAP, interesse, CLT, organizacao, SP, criatividade, RIP, atencao, PA, disciplina, dataAvaliacao, comentario", [$a->CAP, $a->interesse, $a->CLT, $a->organizacao, $a->SP, $a->criatividade, $a->RIP, $a->atencao, $a->PA, $a->disciplina, $a->dataAvaliacao, $a->comentario], ["idPEntidade"=>$a->idPEntidade], ["idPAvalDesEnt"=>$a->idPAvalDesEnt]);
        }
        $this->listar();
      }

      private function gravarAvaliacaoProfessor (){
        $this->idPAno = isset($_GET["idPAno"])?$_GET["idPAno"]:"";

        foreach ($this->entidades(["idPEntidade", "escola.nivelSistemaEntidade"], "naoDocente", "V") as $prof) {

            if(!($prof["escola"]["nivelSistemaEntidade"]=="Director" || $prof["escola"]["nivelSistemaEntidade"]=="Pedagógico" || $prof["escola"]["nivelSistemaEntidade"]=="Administrativo")){

              $chave=$this->idPAno."-".$_SESSION["idEscolaLogada"]."-".$prof["idPEntidade"];

              $this->inserirObjecto("entidadesprimaria", "aval_desemp", "idPAvalDesEnt", "idAvalEntEnt, idAvalEntAno, idAvalEntEscola, chaveAvaliacao, comentario", [$prof["idPEntidade"], $this->idPAno, $_SESSION["idEscolaLogada"], $chave, "O Coordenador , revela boa capacidade de análise e algum interesse sistemático no trabalho, Bom conhecimento do trabalho, organizado e com muito sentido de responsabilidade, sigiloso, muito criativo, pouco interativo, e incentiva pouco ambiente"], ["idPEntidade"=>$prof["idPEntidade"]]);
            }
        }
        $this->listar();
      }

      function listar(){
        echo $this->selectJson("entidadesprimaria", [], ["escola.idEntidadeEscola"=>$_SESSION['idEscolaLogada'], "escola.estadoActividadeEntidade"=>"A", "escola.tipoPessoal"=>"naoDocente", "aval_desemp.idAvalEntAno"=>$this->idPAno, "aval_desemp.idAvalEntEscola"=>$_SESSION['idEscolaLogada']], ["escola", "aval_desemp"], "", [], ["nomeEntidade"=>1]);
      }


    }
    new manipulacaoDadosDoAjaxInternoAvaliacaoAnual(__DIR__);
?>
