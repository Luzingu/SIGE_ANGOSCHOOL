<?php
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');
    include_once 'pautaMod2.php';
    include_once 'pautaMod1.php';
    include_once 'resumoNotas.php';
    include_once 'pauta13Magisterio.php';
    include_once 'pautaModPorSemestre.php';

 
    class pautas extends funcoesAuxiliares{

        function __construct($caminhoAbsoluto){
             parent::__construct();

            $this->mesPagamentoApartir = isset($_GET["mesPagamentoApartir"])?$_GET["mesPagamentoApartir"]:"";
            $this->trimestreApartir = isset($_GET["trimestreApartir"])?$_GET["trimestreApartir"]:null;
            $this->seListarTodaPauta = isset($_GET["listarTodas"])?$_GET["listarTodas"]:null;
            $this->idPCurso = isset($_GET["idPCurso"])?$_GET["idPCurso"]:null;
            $this->classe = isset($_GET["classe"])?$_GET["classe"]:null;
            $this->turma = isset($_GET["turma"])?$_GET["turma"]:null;
            $this->tamanhoFolha =isset($_GET["tamanhoFolha"])?$_GET["tamanhoFolha"]:"A3";
            $this->modeloPauta = isset($_GET["tipoPauta"])?$_GET["tipoPauta"]:null;
            $this->resultPauta = isset($_GET["resultPauta"])?$_GET["resultPauta"]:null;
            $this->idPAno = isset($_GET["idPAno"])?$_GET["idPAno"]:$this->idAnoActual;
            
            if($this->trimestreApartir==1){
                $this->trimestreApartirExtensa="DO I TRIMESTRE";
            }else if($this->trimestreApartir==2){
                $this->trimestreApartirExtensa="DO II  TRIMESTRE";
            }else if($this->trimestreApartir==3){
                $this->trimestreApartirExtensa="DO III  TRIMESTRE";
            }else{
                $this->trimestreApartirExtensa="FINAL";
            }
            
            if($this->classe<=6){
                $this->notaMinima=5;
            }else{
                $this->notaMinima=10;
            }

            $this->nomeCurso();
            
            if($this->sePorSemestre=="sim"){
                $pauta = new pautaModPorSemestre(__DIR__);
            }else if($this->tipoCurso=="pedagogico"  && $this->classe==13){
                $pauta = new pauta13Magisterio(__DIR__);
            }else{
                if($this->modeloPauta=="aprovGeral"){
                    $pauta = new pautaMod2(__DIR__);
                }else if($this->modeloPauta=="pautaGeral"){
                    $pauta = new pautaMod1(__DIR__);
                }else{
                    $pauta = new resumoNotas(__DIR__);
                }                
            }

            $pauta->mesPagamentoApartir = $this->mesPagamentoApartir;
            $pauta->trimestreApartir = $this->trimestreApartir;
            $pauta->seListarTodaPauta = $this->seListarTodaPauta;
            $pauta->idPCurso = $this->idPCurso;
            $pauta->classe = $this->classe;
            $pauta->turma = $this->turma;
            $pauta->tamanhoFolha = $this->tamanhoFolha;
            $pauta->trimestreApartirExtensa = $this->trimestreApartirExtensa;
            $pauta->notaMinima = $this->notaMinima;
            $pauta->idPAno = $this->idPAno;
            $pauta->disciplinasDaClasse = $this->tratarDisciplinas($this->classe);
            $pauta->todasDisciplinasDoCurso = $this->tratarDisciplinas("");

           

            if($this->verificacaoAcesso->verificarAcesso("", ["pautaGeral1", "pautasArquivadas"], [$this->classe, $this->idPCurso], "") || count($this->selectArray("listaturmas", ["nomeTurma"], ["idListaAno"=>$this->idAnoActual, "idPEscola"=>$_SESSION['idEscolaLogada'], "idCoordenadorTurma"=>$_SESSION['idUsuarioLogado'], "classe"=>$this->classe, "nomeTurma"=>$this->turma, "idPNomeCurso"=>$this->idPCurso]))>0){
                $pauta->exibirPauta();
            }else{
                $this->negarAcesso();
            }
        }

        private function tratarDisciplinas($classe){
            $this->nomeCurso();
            $this->nomeTurma();

            $gerenciador_matriculas = listarItensObjecto($this->sobreEscolaLogada, "gerencMatricula", ["classe=".$this->classe, "periodoClasse=".$this->periodoTurma, "idCurso=".$this->idPCurso]);

            $atributoTurma = explode("-", $this->atributoTurma);
            $atributo1 = isset($atributoTurma[0])?$atributoTurma[0]:"";
            $atributo2 = isset($atributoTurma[1])?$atributoTurma[1]:"";
            
            $nomeLE="";
            $idAtributoLE="";
            $nomeDiscOpc="";
            
            if($atributo1!=""){
                $nomeDisciplina=$this->selectUmElemento("nomedisciplinas", "abreviacaoDisciplina2", ["idPNomeDisciplina"=>$atributo1]);
                if($atributo1==20 || $atributo1==21 || $atributo1==22 || $atributo1==23){
                    $nomeLE=$nomeDisciplina;
                    $idAtributoLE = $atributo1;
                }else{
                    $nomeDiscOpc=$nomeDisciplina;
                }
            }
            
            if($atributo2!=""){
                $nomeDisciplina=$this->selectUmElemento("nomedisciplinas", "abreviacaoDisciplina2", ["idPNomeDisciplina"=>$atributo2]);
                if($atributo2==20 || $atributo2==21 || $atributo2==22 || $atributo2==23){
                    $nomeLE=$nomeDisciplina;
                    $idAtributoLE = $atributo2;
                }else{
                    $nomeDiscOpc=$nomeDisciplina;
                }
            }
            
            if($nomeDiscOpc==""){
                
                foreach(explode(",", valorArray($gerenciador_matriculas, "idsDisciplOpcao")) as $a){
                    if($nomeDiscOpc!=""){
                        $nomeDiscOpc .="/ ";
                    }
                    $nomeDiscOpc .=$this->selectUmElemento("nomedisciplinas", "abreviacaoDisciplina2", ["idPNomeDisciplina"=>$a]);
                }
            }
            
            if($nomeLE==""){
                foreach(explode(",", valorArray($gerenciador_matriculas, "idsLinguasEtrang")) as $a){
                    if($nomeLE!=""){
                        $nomeLE .="/ ";
                    }
                    if(count(explode(",", valorArray($gerenciador_matriculas, "idsLinguasEtrang")))==1){
                        $idAtributoLE=valorArray($gerenciador_matriculas, "idsLinguasEtrang");
                    }
                    $nomeLE .=$this->selectUmElemento("nomedisciplinas", "abreviacaoDisciplina2", ["idPNomeDisciplina"=>$a]);
                }
            }
            

            $jaConteiEspecGeral="nao";
            $jaConteiLinguaE="nao";
            $jaConteiLinguaEGeral="nao";
            $jaConteiLinguaEEspecifica="nao";

            $this->disciplinas($this->idPCurso, $classe, valorArray($this->sobreTurma, "periodoTurma"), "", array(), [58, 59, 60, 231, 232, 233], [], $this->idPAno);

            $arrayRetorno = array();
            $tiposDisciplinas = distinct2($this->disciplinas, "tipoDisciplina", "disciplinas");

            foreach ($tiposDisciplinas as $tipo) {
                foreach (array_filter($this->disciplinas, function ($mamale) use ($tipo){
                    return $mamale["disciplinas"]["tipoDisciplina"]==$tipo;
                }) as $disciplina ) {
                    
                    $podeEntrar="nao";
                    $atributoDisciplina="";
                    $nomeDisciplina = $disciplina["abreviacaoDisciplina2"];

                    if($this->tipoCurso=="geral" && ($disciplina["idPNomeDisciplina"]==17 || $disciplina["idPNomeDisciplina"]==14 || $disciplina["idPNomeDisciplina"]==9 || $disciplina["idPNomeDisciplina"]==122) && $disciplina["disciplinas"]["tipoDisciplina"]=="Op"){
                        
                        if($jaConteiEspecGeral=="nao"){
                            $jaConteiEspecGeral="sim";
                            $podeEntrar="sim";
                            $atributoDisciplina="OP";
                            
                            $nomeDisciplina="--";
                            if($nomeDiscOpc!=""){
                                $nomeDisciplina=$nomeDiscOpc;
                            }
                        }
                    }else if(($this->modLinguaEstrangeira=="opcional" || $this->classe<=9) && ($disciplina["idPNomeDisciplina"]==20 || $disciplina["idPNomeDisciplina"]==21)){

                        if($jaConteiLinguaE=="nao"){
                            $jaConteiLinguaE="sim";
                            $podeEntrar="sim";
                            $atributoDisciplina="LE";

                            $nomeDisciplina="L. Estrangeira";
                            if($nomeLE!=""){
                                $nomeDisciplina=$nomeLE;
                            }
                        }
                        

                    }else if(($this->modLinguaEstrangeira=="lingEsp" || $this->modLinguaEstrangeira=="lingEspUnica") && ($disciplina["idPNomeDisciplina"]==20 || $disciplina["idPNomeDisciplina"]==21)){
                        
                        if($jaConteiLinguaEGeral=="nao"){
                            $jaConteiLinguaEGeral="sim";
                            $podeEntrar="sim";
                            $atributoDisciplina="LE Geral";
                            $nomeDisciplina="L. Estrangeira";
                            if($idAtributoLE==20){
                                $nomeDisciplina ="L. Francesa";
                            }else if($idAtributoLE==21){
                                $nomeDisciplina ="L. Inglesa";
                            } 
                        }

                    }else if(($this->modLinguaEstrangeira=="lingEsp" || $this->modLinguaEstrangeira=="lingEspUnica") && ($disciplina["idPNomeDisciplina"]==22 || $disciplina["idPNomeDisciplina"]==23)){

                        if($jaConteiLinguaEEspecifica=="nao"){
                            $jaConteiLinguaEEspecifica="sim";
                            $podeEntrar="sim";
                            $atributoDisciplina="LE Esp";
                            $nomeDisciplina="L. Estrangeira";
                            
                            if($idAtributoLE==20){
                                $nomeDisciplina ="L. Inglesa";
                            }else if($idAtributoLE==21){
                                $nomeDisciplina ="L. Francesa";
                            }
                        }
                    }else{
                        $podeEntrar="sim";
                        $nomeDisciplina= $disciplina["abreviacaoDisciplina2"];
                        $atributoDisciplina="";
                    }
                    foreach($arrayRetorno as $a){
                        if($a["idPNomeDisciplina"]==$disciplina["idPNomeDisciplina"]){
                            $podeEntrar="nao";
                            break;
                        }
                    }
                    

                    if($podeEntrar=="sim"){
                        $arrayRetorno[] =array('idPNomeDisciplina'=>$disciplina["idPNomeDisciplina"], "nomeDisciplina"=>$nomeDisciplina, "tipoDisciplina"=>$disciplina["disciplinas"]["tipoDisciplina"], "atributoDisciplina"=>$atributoDisciplina, "ordenacao"=>$disciplina["disciplinas"]["ordenacao"], "continuidadeDisciplina"=>$disciplina["disciplinas"]["continuidadeDisciplina"], "semestreDisciplina"=>$disciplina["disciplinas"]["semestreDisciplina"]); 
                    }                   
               }
            }
            return $arrayRetorno;
        }
    }

new pautas(__DIR__);
?>