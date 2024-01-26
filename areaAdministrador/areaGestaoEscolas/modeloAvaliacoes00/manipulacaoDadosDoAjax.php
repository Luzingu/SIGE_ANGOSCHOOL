<?php
    session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/manipulacaoDadosDoAjax.php';

    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{

        function __construct($db = ""){
            parent::__construct();

            if($db == "teste")
              $this->conDb("teste");

            if($this->accao=="copiarAvaliacoes"){
                if($this->verificacaoAcesso->verificarAcesso("", ["modeloAvaliacoes00"])){
                  $this->copiarAvaliacoes();
                }
            }
        }

        private function copiarAvaliacoes (){
          $idDestino = isset($_GET["idDestino"])?$_GET["idDestino"]:"";
          $anoDestino = isset($_GET["anoDestino"])?$_GET["anoDestino"]:"";
          $anoOrigem = isset($_GET["anoOrigem"])?$_GET["anoOrigem"]:"";
          $modelo = explode("-", $idDestino)[0];
          $idPCurso = explode("-", $idDestino)[1];

          foreach ($this->selectArray("nomecursos", [], ["idPNomeCurso"=>$idPCurso], ["cursos"]) as $a)
          {
            foreach(listarItensObjecto($a, "classes") as $classe)
            {
              $cabecalho = valorArray($a, "cabecalhoAvaliacoes".$classe["identificador"]."-".$anoOrigem, "cursos");

              if($cabecalho != NULL && $cabecalho != "")
                $this->editarItemObjecto("nomecursos", "cursos", "cabecalhoAvaliacoes".$classe["identificador"]."-".$anoDestino, [$cabecalho], ["idPNomeCurso"=>$idPCurso], ["idPCurso"=>$a["cursos"]["idPCurso"]]);
            }
          }
          foreach ($this->selectArray("nomedisciplinas", [], ["disciplinas.idDiscCurriculo"=>$modelo, "disciplinas.idDiscCurso"=>$idPCurso], ["disciplinas"], "", [], ["disciplinas.periodoDisciplina"=>1, "disciplinas.classeDisciplina"=>1]) as $a) {

            $string = "";
            $valores = array();

            if(valorArray($a, "cabecalhoAvaliacoes-".$anoOrigem, "disciplinas")!="" && valorArray($a, "cabecalhoAvaliacoes-".$anoOrigem, "disciplinas")!=NULL)
            {
              if($string !="")
                $string .=",";
              $string .="cabecalhoAvaliacoes-".$anoDestino;
              $valores[] = valorArray($a, "cabecalhoAvaliacoes-".$anoOrigem, "disciplinas");
            }
            if(valorArray($a, "cabecalhoAvaliacoesExt-".$anoOrigem, "disciplinas")!="" && valorArray($a, "cabecalhoAvaliacoesExt-".$anoOrigem, "disciplinas")!=NULL)
            {
              if($string !="")
                $string .=",";
              $string .="cabecalhoAvaliacoesExt-".$anoDestino;
              $valores[] = valorArray($a, "cabecalhoAvaliacoesExt-".$anoOrigem, "disciplinas");
            }
            if(valorArray($a, "camposAvaliacoes-".$anoOrigem, "disciplinas")!="" && valorArray($a, "camposAvaliacoes-".$anoOrigem, "disciplinas")!=NULL)
            {
              if($string !="")
                $string .=",";
              $string .="camposAvaliacoes-".$anoDestino;
              $valores[] = valorArray($a, "camposAvaliacoes-".$anoOrigem, "disciplinas");
            }
            if(valorArray($a, "camposAvaliacoesExt-".$anoOrigem, "disciplinas")!="" && valorArray($a, "camposAvaliacoesExt-".$anoOrigem, "disciplinas")!=NULL)
            {
              if($string !="")
                $string .=",";
              $string .="camposAvaliacoesExt-".$anoDestino;
              $valores[] = valorArray($a, "camposAvaliacoesExt-".$anoOrigem, "disciplinas");
            }

            $this->editarItemObjecto("nomedisciplinas", "disciplinas", $string, $valores, ["idPNomeDisciplina"=>$a["idPNomeDisciplina"]], ["idPDisciplina"=>$a["disciplinas"]["idPDisciplina"]]);
          }
        }
    }
    new manipulacaoDadosDoAjaxInterno();
    new manipulacaoDadosDoAjaxInterno("teste");
?>
