<?php 
    session_start();
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../manipulacaoDadosDoAjax.php');
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{
        private $classe ="";
        private $idPCurso="";

        function __construct($caminhoAbsoluto){
            parent::__construct();
            if($this->accao=="actualizarDados"){
                
                $this->actualizarDados();
                
            }else if($this->accao=="alterarDados"){
                if($this->verificacaoAcesso->verificarAcesso("", ["gestaoLinguasEOpcoes"], [])){
                    $this->alterarDados();
                }
            }
        }

        private function actualizarDados(){

            $periodos[]="reg";
            if(valorArray($this->sobreUsuarioLogado, "periodosEscolas")=="regPos"){
              $periodos[]="pos";
            }

            $arrayClasses = array();
            foreach ($periodos as $p) {
                foreach ($this->selectArray("nomecursos", [], ["cursos.idCursoEscola"=>$_SESSION["idEscolaLogada"], "cursos.estadoCurso"=>"A"], ["cursos"], "", [], ["nomeCurso"=>1]) as $c) {
                   
                    foreach(listarItensObjecto($c, "classes") as $classe) { 
                        $arrayClasses[] = $p."-".$classe["identificador"]."-".$c["idPNomeCurso"];
                    }
                }
            }          
            
            foreach($arrayClasses as $a){
                $explode = explode("-", $a);
                $periodo = isset($explode[0])?$explode[0]:"";
                $classe = isset($explode[1])?$explode[1]:"";
                $idPCurso = isset($explode[2])?$explode[2]:"";

                $this->inserirObjecto("escolas", "gerencMatricula", "idPGerMatr", "idGerMatEscola, idCurso, classe, periodoClasse, chavePrincipal", [$_SESSION['idEscolaLogada'], $idPCurso, $classe, $periodo, $a."-".$_SESSION['idEscolaLogada']], ["idPEscola"=>$_SESSION['idEscolaLogada']]);
            }       
        }

        private function alterarDados(){
            $dados =isset($_GET['dados'])?$_GET['dados']:array();
            $dados = json_decode($dados);

            foreach($dados as $d){
                $this->editarItemObjecto("escolas", "gerencMatricula", "idsLinguasEtrang, idsDisciplOpcao", [$d->linguasEntrangeira, $d->disciplinasOpcao], ["idPEscola"=>$_SESSION['idEscolaLogada']], ["idPGerMatr"=>$d->id]);
            }

            $array = $this->selectArray("escolas", ["gerencMatricula.idGerMatEscola", "gerencMatricula.idPGerMatr", "gerencMatricula.idCurso", "gerencMatricula.classe", "gerencMatricula.periodoClasse", "gerencMatricula.idsLinguasEtrang", "gerencMatricula.idsDisciplOpcao", "gerencMatricula.chavePrincipal"], ["idPEscola"=>$_SESSION['idEscolaLogada']], ["gerencMatricula"]);
            $array = $this->anexarTabela2($array, "nomecursos", "gerencMatricula", "idPNomeCurso", "idCurso");
            
            echo json_encode($array);
        }

        
    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>