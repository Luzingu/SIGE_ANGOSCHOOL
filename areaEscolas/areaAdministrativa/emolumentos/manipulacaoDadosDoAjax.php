<?php 
	session_start();
	
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/manipulacaoDadosDoAjax.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{
    	private $idPCurso="";
        private $classe="";
        private $valoresAlteradosPrecos="";
        private $nomeValoresAlteradosPrecos="";
        private $tipoPreco="";

    	function __construct($caminhoAbsoluto){
    		parent::__construct();
            if($this->accao=="alterarPrecosMensalidades" || $this->accao=="alterarOutrosPrecos"){

               if($this->verificacaoAcesso->verificarAcesso("", ["emolumentos"])){

                    $this->idPCurso = $_GET["idPCurso"];
                    $this->classe = $_GET["classe"];
                    $this->idPTipoEmolumento = $_GET["idPTipoEmolumento"];

                    $this->valoresAlteradosPrecos = $_GET["valoresAlteradosPrecos"];
                    $this->nomeValoresAlteradosPrecos = $_GET["nomeValoresAlteradosPrecos"];

                    if($this->accao=="alterarOutrosPrecos"){
                        $this->alterarOutrosPrecos();
                    }else{
                        $this->alterarOutrosPrecosMensalidade();
                    }
                }

            }else if($this->accao=="gravarPrecos"){
                if($this->verificacaoAcesso->verificarAcesso("", ["emolumentos"])){

                    $this->gravarPrecos();
                }                
            }
    	}

        private function gravarPrecos(){
            $idPTipoEmolumento = isset($_GET["idPTipoEmolumento"])?$_GET["idPTipoEmolumento"]:"";
            $array = $this->selectArray("tipos_emolumentos", [], ["idPTipoEmolumento"=>$idPTipoEmolumento]);


                foreach ($this->selectArray("nomecursos", [], ["cursos.idCursoEscola"=>$_SESSION['idEscolaLogada'], "cursos.estadoCurso"=>"A"], ["cursos"], "", [], ["nomeCurso"=>1]) as $curso){


                    foreach(listarItensObjecto($curso, "classes") as $classe ){

                        if(valorArray($array, "tipoPagamento")=="pagMensal"){

                            for($m=1; $m<=12; $m++){
                                $this->inserirObjecto("escolas", "emolumentos", "idPEmolumento", "idEscola, idTipoEmolumento, codigoEmolumento, classe, idCurso, mes, valor, chaveEmolumento", [$_SESSION["idEscolaLogada"], $idPTipoEmolumento, valorArray($array, "codigo"), $classe["identificador"], $curso["idPNomeCurso"], $m, 0, $_SESSION["idEscolaLogada"]."-".$idPTipoEmolumento."-".$curso["idPNomeCurso"]."-".$classe["identificador"]."-".$m], ["idPEscola"=>$_SESSION['idEscolaLogada']]);
                            }
                        }else{
                            $this->inserirObjecto("escolas", "emolumentos", "idPEmolumento", "idEscola, idTipoEmolumento, codigoEmolumento, classe, idCurso, valor, chaveEmolumento", [$_SESSION["idEscolaLogada"], $idPTipoEmolumento, valorArray($array, "codigo"), $classe["identificador"], $curso["idPNomeCurso"], 0, $_SESSION["idEscolaLogada"]."-".$idPTipoEmolumento."-".$curso["idPNomeCurso"]."-".$classe["identificador"]], ["idPEscola"=>$_SESSION['idEscolaLogada']]);
                        }
                    }
                    if(valorArray($array, "codigo")=="declaracao"){
                        //$this->inserirObjecto("escolas", "emolumentos", "idPEmolumento", "idEscola, idTipoEmolumento, codigoEmolumento, classe, idCurso, valor, chaveEmolumento", [$_SESSION["idEscolaLogada"], $idPTipoEmolumento, valorArray($array, "codigo"), 120, $curso["idPNomeCurso"], 0, $_SESSION["idEscolaLogada"]."-".$idPTipoEmolumento."-".$curso["idPNomeCurso"]."-120"], ["idPEscola"=>$_SESSION['idEscolaLogada']]);

                        //$this->inserirObjecto("escolas", "emolumentos", "idPEmolumento", "idEscola, idTipoEmolumento, codigoEmolumento, classe, idCurso, valor, chaveEmolumento", [$_SESSION["idEscolaLogada"], $idPTipoEmolumento, valorArray($array, "codigo"), 1200, $curso["idPNomeCurso"], 0, $_SESSION["idEscolaLogada"]."-".$idPTipoEmolumento."-".$curso["idPNomeCurso"]."-1200"], ["idPEscola"=>$_SESSION['idEscolaLogada']]);
                    }
                    
                }
            echo $this->selectJson("escolas",["emolumentos.idTipoEmolumento", "emolumentos.codigoEmolumento", "emolumentos.classe", "emolumentos.idCurso", "emolumentos.mes", "emolumentos.valor"], ["idPEscola"=>$_SESSION['idEscolaLogada'], "emolumentos.idTipoEmolumento"=>$idPTipoEmolumento], ["emolumentos"]);
        }

        private function alterarOutrosPrecos(){
            $this->nomeValoresAlteradosPrecos = explode(",", $this->nomeValoresAlteradosPrecos);
            $this->valoresAlteradosPrecos = explode(",", $this->valoresAlteradosPrecos);

            for($i=0; $i<count($this->nomeValoresAlteradosPrecos); $i++){
                $arr = explode("_", $this->nomeValoresAlteradosPrecos[$i]);
                $classe = $arr[0];
                $idCurso = isset($arr[1])?$arr[1]:NULL;
                
                $this->editarItemObjecto("escolas", "emolumentos", "valor", [$this->valoresAlteradosPrecos[$i]], ["idPEscola"=>$_SESSION['idEscolaLogada']], ["classe"=>$classe, "idCurso"=>$idCurso, "idTipoEmolumento"=>$this->idPTipoEmolumento]);                
            }

            echo $this->selectJson("escolas",["emolumentos.idTipoEmolumento", "emolumentos.codigoEmolumento", "emolumentos.classe", "emolumentos.idCurso", "emolumentos.mes", "emolumentos.valor"], ["idPEscola"=>$_SESSION['idEscolaLogada'], "emolumentos.idTipoEmolumento"=>$this->idPTipoEmolumento], ["emolumentos"]);
        }

        function alterarOutrosPrecosMensalidade(){

            $this->nomeValoresAlteradosPrecos = explode(",", $this->nomeValoresAlteradosPrecos);
            $this->valoresAlteradosPrecos = explode(",", $this->valoresAlteradosPrecos);

            for($i=0; $i<count($this->nomeValoresAlteradosPrecos); $i++){
                $this->editarItemObjecto("escolas", "emolumentos", "valor", [$this->valoresAlteradosPrecos[$i]], ["idPEscola"=>$_SESSION['idEscolaLogada']], ["classe"=>$this->classe, "idTipoEmolumento"=>$this->idPTipoEmolumento, "mes"=>$this->nomeValoresAlteradosPrecos[$i], "idCurso"=>$this->idPCurso]);                 
            }
            echo $this->selectJson("escolas",["emolumentos.idTipoEmolumento", "emolumentos.codigoEmolumento", "emolumentos.classe", "emolumentos.idCurso", "emolumentos.mes", "emolumentos.valor"], ["idPEscola"=>$_SESSION['idEscolaLogada'], "emolumentos.idTipoEmolumento"=>$this->idPTipoEmolumento], ["emolumentos"]);
        }
    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>