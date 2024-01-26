<?php if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ($_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php');
    include_once ($_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliaresDb.php');
    include_once 'ensinoMedioLiceu.php';
    include_once 'ensinoMedioPolitecnico.php';
    include_once 'ensinoMedioIPEKami.php';
    include_once 'ensinoMedioFilialEFTSZ.php';
    include_once 'ensinoMedioMagisterio.php';
    include_once 'ensinoBasico.php';
    include_once 'CEEK/ensinoBasico.php';
    include_once 'CEEK/ensinoPrimario.php';
    include_once 'ensinoPrimario.php';
    include_once 'ensinoMedioPorSemestre.php';
    include_once 'ensinoMedioFilialLS.php';
    include_once 'CEPP/ensinoBasico.php';
    include_once 'CEPP/ensinoMedioLiceu.php';
    
    class declaracoes extends funcoesAuxiliares{
       private $nivelDeclaracao = "";

        function __construct(){

            $documentoTratar = isset($_GET["documentoTratar"])?$_GET["documentoTratar"]:null;
            $this->idPMatricula = isset($_GET["idPMatricula"])?$_GET["idPMatricula"]:null;
            $this->idPCurso = isset($_GET["idPCurso"])?$_GET["idPCurso"]:null;
            parent::__construct("Rel-Declarações ou Certificado");

             $this->sobreAluno($this->idPMatricula);

            $this->sobreAluno($this->idPMatricula);
            $this->sobreAluno = $this->sobreEscreverAluno($this->sobreAluno, $this->idPCurso);
            $this->sobreAluno = $this->anexarTabela( $this->sobreAluno, "div_terit_provincias", "idPProvincia", "provNascAluno");
             $this->sobreAluno = $this->anexarTabela( $this->sobreAluno, "div_terit_municipios", "idPMunicipio", "municNascAluno");
             $this->sobreAluno = $this->anexarTabela( $this->sobreAluno, "div_terit_comunas", "idPComuna", "comunaNascAluno");
             $this->sobreAluno = $this->anexarTabela( $this->sobreAluno, "nomecursos", "idPNomeCurso", "idMatCurso");
            $this->sobreCursoAluno = $this->selectArray("nomecursos", [], ["idPNomeCurso"=>$this->idPCurso, "cursos.idCursoEscola"=>$_SESSION['idEscolaLogada']], ["cursos"]);

            $this->nomeCurso();
            $this->numAno();
                if($_SESSION["idMatriculaPagamento"]==$this->idPMatricula && $_SESSION["idNomeDocumentoPagar"]==$documentoTratar){


                    if($this->tipoCurso=="primaria"){
                        if($_SESSION["idEscolaLogada"]==29){
                            $declaracao = new ensinoPrimarioKami();
                        }else{
                            $declaracao = new ensinoPrimario();
                        }
                    }else if($this->tipoCurso=="basica"){
                        if($_SESSION["idEscolaLogada"]==29){
                            $declaracao = new ensinoBasicoKami();
                        }else if($_SESSION["idEscolaLogada"]==20){
                            $declaracao = new ensinoBasicoCEPP();
                        }else{
                            $declaracao = new ensinoBasico();
                        }
                    }else if($this->tipoCurso=="tecnico"){ 
                        if($this->sePorSemestre=="sim"){
                            $declaracao = new ensinoMedioPorSemestre();
                        }else{
                            if($_SESSION["idEscolaLogada"]==26 || $_SESSION["idEscolaLogada"]==19 || $_SESSION["idEscolaLogada"]==28){
                                 $declaracao = new ensinoMedioFilialEFTSZ();
                            }else if($_SESSION["idEscolaLogada"]==30){
                                 $declaracao = new ensinoMedioIPEKami();
                            }else{
                                $declaracao = new ensinoMedioPolitecnico();
                            }
                        }
                    }else if($this->tipoCurso=="pedagogico"){
                            $declaracao = new ensinoMedioMagisterio();
                    }else{
                        if($_SESSION["idEscolaLogada"]==20){
                            $declaracao = new ensinoMedioLiceuCEPP();
                        }else if($_SESSION['idEscolaLogada']==29 || $_SESSION['idEscolaLogada']==21){
                            $declaracao = new ensinoMedioFilialLS(); 
                        }else{
                            $declaracao = new ensinoMedioLiceu();    
                        }
                    }

                    $classe="";
                    if($documentoTratar=="declaracaoSemNotas"){
                        $tipoDocumentoATratar="declaracaoSemNotas";
                        $classe = valorArray($this->sobreAluno, "classeActualAluno", "escola");      
                    }else if($documentoTratar==120){
                        $classe=$this->ultimaClasse($this->idPCurso);
                        $tipoDocumentoATratar="certificado";  
                    }else {
                        $tipoDocumentoATratar="declaracao";
                        $classe = $documentoTratar;
                    }

                     //Enviando os valores...

                    if(isset($_GET["efeitoDeclaracao"])){
                        if(trim($_GET["efeitoDeclaracao"])==""){
                            $declaracao->efeitoDeclaracao="";
                        }else{
                            $declaracao->efeitoDeclaracao = $_GET["efeitoDeclaracao"];
                        }
                    }else{
                        $declaracao->efeitoDeclaracao="";
                    }

                    if(trim($declaracao->efeitoDeclaracao)=="" || trim($declaracao->efeitoDeclaracao)==NULL){
                        $declaracao->efeitoDeclaracao ="efeitos legais";
                    }

                    $declaracao->viaDocumento = isset($_GET["viaDocumento"])?(int)$_GET["viaDocumento"]:1;

                    if(isset($_GET["numeroDeclaracao"])){
                        if(trim($_GET["numeroDeclaracao"])==""){
                            $declaracao->numeroDeclaracao="______";
                        }else{
                            $declaracao->numeroDeclaracao = "<span class='sublinhado'>".completarNumero($_GET["numeroDeclaracao"])."</span>";
                        }
                    }else{
                        $declaracao->numeroDeclaracao="______";
                    }
                    $declaracao->idPCurso=$this->idPCurso;
                    $declaracao->idPMatricula=$this->idPMatricula;
                    $declaracao->classe = $classe;
                    $declaracao->sobreCursoAluno = $this->sobreCursoAluno;
                    $declaracao->sobreAluno = $this->sobreAluno;

                    $declaracao->art1="o";
                    $declaracao->art2 ="";
                    if(valorArray($declaracao->sobreAluno, "sexoAluno")=="F"){
                        $declaracao->art1="a";
                        $declaracao->art2 ="a";
                    }

                    if($this->verificacaoAcesso->verificarAcesso("", ["relatorioAluno"], [$classe, $this->idPCurso], "")){                   
                        
                        $this->valoresEssenciais($declaracao);
                        $this->identificao($declaracao);
                        //Chamando as Classess...
                        if($tipoDocumentoATratar=="certificado"){
                            $declaracao->certificado();
                        }else if($tipoDocumentoATratar=="declaracaoSemNotas"){
                            $declaracao->declaracaoSemNotas();
                        }else{
                             $declaracao->declaracao();
                        }
                    }else{
                        $this->negarAcesso();
                    }
            }else{
                $this->negarAcesso();
            }
        }

        private function valoresEssenciais($declaracao){

            $sobreReconf = listarItensObjecto($declaracao->sobreAluno, "reconfirmacoes", ["idReconfEscola=".$_SESSION['idEscolaLogada'], "classeReconfirmacao=".$declaracao->classe, "estadoReconfirmacao=A", "idMatCurso=".$this->idPCurso], "nao", "dataReconf DESC");
            $sobreReconf = $this->anexarTabela($sobreReconf, "anolectivo", "idPAno", "idReconfAno");
            $idAnoReconf = valorArray($sobreReconf, "idReconfAno");

            if($idAnoReconf=="" || $idAnoReconf==NULL){
                $pepe = listarItensObjecto($declaracao->sobreAluno, "dadosatraso", ["idDEscola=".$_SESSION['idEscolaLogada'], "idCurso=".$declaracao->idPCurso, "classeAnterior=".$declaracao->classe]);
                
                $declaracao->anoFinalizado = $declaracao->selectUmElemento("anolectivo", "numAno", ["idPAno"=>valorArray($pepe, "anoAnterior")]);
                if($declaracao->anoFinalizado=="" || $declaracao->anoFinalizado==NULL){
                    $declaracao->anoFinalizado = valorArray($declaracao->sobreAluno, "numAno");
                }
               
                $declaracao->turma = valorArray($pepe, "turmaAnterior");
                $declaracao->numeroAnterior = completarNumero(valorArray($pepe, "numeroAnterior"));
                $declaracao->numeroPauta = valorArray($pepe, "numeroPauta");
                if($_SESSION['idEscolaLogada']==22){
                    $declaracao->numeroPauta = valorArray($declaracao->sobreAluno, "numeroProcesso", "escola");
                }
            }else{
                $turma = valorArray($sobreReconf, "nomeTurma");

                $sobreTurma = $this->selectArray("listaturmas", [], ["idPEscola"=>$_SESSION["idEscolaLogada"], "idListaAno"=>$idAnoReconf, "classe"=>$declaracao->classe, "nomeTurma"=>$turma, "idPNomeCurso"=>$declaracao->idPCurso]);

                $declaracao->anoFinalizado = valorArray($sobreReconf, "numAno");
                if($declaracao->anoFinalizado=="" || $declaracao->anoFinalizado==NULL){
                    $declaracao->anoFinalizado = valorArray($declaracao->sobreAluno, "numAno");
                }
                $declaracao->turma = valorArray($sobreTurma, "designacaoTurma");

                $declaracao->numeroPauta = valorArray($sobreTurma, "numeroPauta");
                if($_SESSION['idEscolaLogada']==22){
                    $declaracao->numeroPauta = valorArray($declaracao->sobreAluno, "numeroProcesso", "escola");
                }

                $i=0;
                $declaracao->numeroAnterior = "";
                foreach ($this->selectArray("alunosmatriculados", ["idPMatricula", "nomeAluno"], ["reconfirmacoes.idReconfEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.idReconfAno"=>$idAnoReconf, "reconfirmacoes.classeReconfirmacao"=>$declaracao->classe, "reconfirmacoes.nomeTurma"=>$turma, "reconfirmacoes.estadoReconfirmacao"=>"A", "reconfirmacoes.idMatCurso"=>$declaracao->idPCurso], ["reconfirmacoes"], "", [], ["nomeAluno"=>1]) as $p) {
                    $i++;
                    if($p["idPMatricula"]==$declaracao->idPMatricula){
                        $declaracao->numeroAnterior = $i;
                        break;
                    }
                }
                $declaracao->numeroAnterior = completarNumero((int)$declaracao->numeroAnterior);
            } 
            
            if(valorArray($declaracao->sobreAluno, "numeroLivroRegistro")!=NULL && valorArray($declaracao->sobreAluno, "numeroLivroRegistro")!="" && $declaracao->classe==(9+$this->duracaoCurso)){
                $declaracao->numeroPauta = valorArray($declaracao->sobreAluno, "numeroLivroRegistro");
                if($_SESSION['idEscolaLogada']==22){
                    $declaracao->numeroPauta = valorArray($declaracao->sobreAluno, "numeroProcesso", "escola");
                }
                if(valorArray($declaracao->sobreAluno, "numeroFolhaRegistro")!=NULL){
                    $declaracao->numeroAnterior = valorArray($declaracao->sobreAluno, "numeroFolhaRegistro");
                }
            }
            if($declaracao->anoFinalizado=="" || $declaracao->anoFinalizado==NULL){
                $declaracao->anoFinalizado = $this->selectUmElemento("anolectivo", "numAno", ["idPAno"=>valorArray($declaracao->sobreAluno, "idMatFAno", "escola")]);   
            }
            $declaracao->numeroPauta = $declaracao->numeroPauta;
            $declaracao->numeroAnterior = $declaracao->numeroAnterior;
        }
        private function identificao($declaracao){
            if(valorArray($declaracao->sobreAluno, "tipoDocumento")=="Passaporte"){
                $declaracao->identificacaoAluno ="portador".$declaracao->art2." do Passaporte n.º <strong>".valorArray($declaracao->sobreAluno, "biAluno")."</strong>, emitido ".valorArray($declaracao->sobreAluno, "localEmissao").", aos ".dataExtensa(valorArray($declaracao->sobreAluno, "dataEBIAluno"));
            }else if(valorArray($declaracao->sobreAluno, "tipoDocumento")=="Cédula"){
                $declaracao->identificacaoAluno ="portador".$declaracao->art2." da Cédula Pessoal n.º <strong>".valorArray($declaracao->sobreAluno, "biAluno")."</strong>, passado pelo arquivo de Identificação ".valorArray($declaracao->sobreAluno, "preposicaoMunicipio2")." ".valorArray($declaracao->sobreAluno, "nomeMunicipio").", aos ".dataExtensa(valorArray($declaracao->sobreAluno, "dataEBIAluno"));
            }else{
                $declaracao->identificacaoAluno ="portador".$declaracao->art2." do Bilhete de Identidade n.º <strong>".valorArray($declaracao->sobreAluno, "biAluno")."</strong>, passado pelo Arquivo ";
                /*if($_SESSION['idEscolaLogada']==22){
                    $declaracao->identificacaoAluno .="pelo arquivo ";
                }else{
                    $declaracao->identificacaoAluno .="pela Direcção Nacional ";
                }*/
                $declaracao->identificacaoAluno .="de Identificação de Luanda, aos ".dataExtensa(valorArray($declaracao->sobreAluno, "dataEBIAluno"));
            }
        }
    }


    new declaracoes();
?>