<?php 
    session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/manipulacaoDadosDoAjax.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{

        function __construct($tipoBaseDados){
            parent::__construct();
            $this->tipoBaseDados=$tipoBaseDados;
            
            $this->ordem = isset($_POST["ordem"])?$_POST["ordem"]:"";
            $this->identificador = isset($_POST["identificador"])?$_POST["identificador"]:"";
            $this->designacao = isset($_POST["designacao"])?$_POST["designacao"]:"";
            $this->abreviacao1 = isset($_POST["abreviacao1"])?$_POST["abreviacao1"]:"";
            $this->abreviacao2 = isset($_POST["abreviacao2"])?$_POST["abreviacao2"]:"";
            $this->idPClasse = isset($_POST["idPClasse"])?$_POST["idPClasse"]:"";
            $this->idPNomeCurso = isset($_POST["idPNomeCurso"])?$_POST["idPNomeCurso"]:"";
            $this->periodos = isset($_POST["periodos"])?$_POST["periodos"]:"";
            $this->notaMaxima = isset($_POST["notaMaxima"])?$_POST["notaMaxima"]:"";
            $this->notaMedia = isset($_POST["notaMedia"])?$_POST["notaMedia"]:"";
            $this->notaMinima = isset($_POST["notaMinima"])?$_POST["notaMinima"]:"";
            $this->seComRecurso = isset($_POST["seComRecurso"])?"A":"I"; 

            $this->conDb($tipoBaseDados);
            if($this->accao=="editarClasse"){
                if($this->verificacaoAcesso->verificarAcesso("", ["classes00"])){
                  $this->editarClasse();
                }
            }else if($this->accao=="novaClasse"){
                if($this->verificacaoAcesso->verificarAcesso("", ["classes00"])){
                      $this->novaClasse();
                }
            }else if ($this->accao=="excluirClasse"){
                if($this->verificacaoAcesso->verificarAcesso("", ["classes00"])){
                  $this->excluirClasse();
                }
            }else if ($this->accao=="copiarDadosCurso"){
                if($this->verificacaoAcesso->verificarAcesso("", ["classes00"])){
                  $this->copiarDadosCurso();
                }
            }
        }

        private function novaClasse(){
            if($this->inserirObjecto("nomecursos", "classes", "idPClasse", "ordem, identificador, designacao, abreviacao1, abreviacao2, periodos, notaMaxima, notaMedia, notaMinima, seComRecurso", [$this->ordem, $this->identificador, $this->designacao, $this->abreviacao1, $this->abreviacao2, $this->periodos, $this->notaMaxima, $this->notaMedia, $this->notaMinima, $this->seComRecurso], ["idPNomeCurso"=>$this->idPNomeCurso])=="sim"){
                $this->listar();
            }else{
                echo "FNão foi possível adicionar a classe.";
            }
        }

        private function editarClasse(){
            if($this->editarItemObjecto("nomecursos", "classes", "ordem, identificador, designacao, abreviacao1, abreviacao2, periodos, notaMaxima, notaMedia, notaMinima, seComRecurso", [$this->ordem, $this->identificador, $this->designacao, $this->abreviacao1, $this->abreviacao2, $this->periodos, $this->notaMaxima, $this->notaMedia, $this->notaMinima, $this->seComRecurso], ["idPNomeCurso"=>$this->idPNomeCurso], ["idPClasse"=>$this->idPClasse])=="sim"){

                $this->listar();   
            }else{
                echo "FNão foi possível editar os dados.";
            }
        }

        private function excluirClasse(){
            if($this->excluirItemObjecto("nomecursos", "classes", ["idPNomeCurso"=>$this->idPNomeCurso], ["idPClasse"=>$this->idPClasse])=="sim"){
                $this->listar();
            }else{
                echo "FNão foi possível excluir a classe.";
            }
        }

        private function copiarDadosCurso(){
            $this->idCursoDestino = isset($_GET["idCursoOrigem"])?$_GET["idCursoOrigem"]:"";

            $this->idPNomeCurso = isset($_GET["idCursoDestino"])?$_GET["idCursoDestino"]:"";

            $this->excluirItemObjecto("nomecursos", "classes", ["idPNomeCurso"=>$this->idPNomeCurso], ["ordem"=>array('$gte'=>1)]);
            $this->excluirItemObjecto("nomecursos", "periodos", ["idPNomeCurso"=>$this->idPNomeCurso], ["ordem"=>array('$gte'=>1)]);

            $sobreCurso = $this->selectArray("nomecursos", [], ["idPNomeCurso"=>$this->idCursoDestino]);
            
            foreach(listarItensObjecto($sobreCurso, "classes") as $a){
                $stringValores="";
                $valores=array();
                foreach(retornarChaves($a) as $chave){
                    if($chave!="idPClasse" && isset($a[$chave])){
                        if($stringValores!=""){
                            $stringValores .=",";
                        }
                        $stringValores .=$chave;
                        $valores[]=$a[$chave];
                    }
                }
                $this->inserirObjecto("nomecursos", "classes", "idPClasse", $stringValores, $valores, ["idPNomeCurso"=>$this->idPNomeCurso]);           
            }
            

            
            foreach(listarItensObjecto($sobreCurso, "periodos") as $a){
                $stringValores="";
                $valores=array();
                foreach(retornarChaves($a) as $chave){
                    if($chave!="idPPeriodo" && isset($a[$chave])){
                        if($stringValores!=""){
                            $stringValores .=",";
                        }
                        $stringValores .=$chave;
                        $valores[]=$a[$chave];
                    }
                }
                $this->inserirObjecto("nomecursos", "periodos", "idPPeriodo", $stringValores, $valores, ["idPNomeCurso"=>$this->idPNomeCurso]);              
            }
            
            $this->listar();
        }
        private function listar(){
            if($this->tipoBaseDados=="escola"){
                
                $array = listarItensObjecto($this->selectArray("nomecursos", [], ["idPNomeCurso"=>$this->idPNomeCurso]), "classes");
                echo json_encode(ordenar($array, "ordem ASC"));
            }
        }
        
    }
    new manipulacaoDadosDoAjaxInterno("escola");
    new manipulacaoDadosDoAjaxInterno("teste");
?>