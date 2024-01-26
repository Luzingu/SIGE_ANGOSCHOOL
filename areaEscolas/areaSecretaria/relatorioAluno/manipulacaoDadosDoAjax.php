<?php 
  session_start();
  include_once ('../../funcoesAuxiliares.php');
  include_once ('../../manipulacaoDadosDoAjax.php');
    
    include_once ('../../manipuladorPauta.php');
    include_once 'manipuladorNotasAtraso.php';
     
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{           

      function __construct($caminhoAbsoluto){
        parent::__construct();         
          
        $this->manipuladorPautas = new manipuladorPauta(__DIR__);
        
        $this->classe="";
        if(isset($_GET["classe"])){
          $this->classe = $_GET["classe"];
        }else if(isset($_POST["classe"])){
          $this->classe = $_POST["classe"];
        }
        $this->idPCurso="";
        if(isset($_GET["idPCurso"])){
          $this->idPCurso = $_GET["idPCurso"];
        }else if(isset($_POST["idPCurso"])){
          $this->idPCurso = $_POST["idPCurso"];
        }
        
        $this->idPMatricula="";
        if(isset($_GET["idPMatricula"])){
          $this->idPMatricula = $_GET["idPMatricula"];
        }else if(isset($_POST["idPMatricula"])){
          $this->idPMatricula = $_POST["idPMatricula"];
        }

        if($this->classe==120){
          $this->classe = $this->ultimaClasse($this->idPCurso);
        }
        
        if($this->accao=="pesquisarAluno"){
          $valorPesquisado = isset($_GET['valorPesquisado'])?$_GET['valorPesquisado']:"";

          $condicoesPesquisa = [array("nomeAluno"=>new \MongoDB\BSON\Regex($valorPesquisado)), array("biAluno"=>new \MongoDB\BSON\Regex($valorPesquisado)), array("numeroInterno"=>new \MongoDB\BSON\Regex($valorPesquisado)), array("nomeAluno"=>new \MongoDB\BSON\Regex(ucwords($valorPesquisado))), array("nomeAluno"=>ucwords($valorPesquisado))];

          $alunos = $this->selectArray("alunosmatriculados", ["nomeAluno", "idPMatricula", "numeroInterno"], ["escola.idMatEscola"=>$_SESSION['idEscolaLogada'], "escola.estadoAluno"=>['$in'=>array("A", "Y", "T")], '$or'=>$condicoesPesquisa], ["escola"], 10, [], array("nomeAluno"=>1));
          echo json_encode($alunos);

        }else if($this->accao=="verificarPagamento"){
            $tipoDeDocumento= $_GET["tipoDeDocumento"];

          if($tipoDeDocumento=="declaracao"){

            if($this->verificacaoAcesso->verificarAcesso("", ["relatorioAluno"], [$this->classe, $this->idPCurso])){
                $this->verificarPagamentoDeclaracao(); 
            } 
          }else if($tipoDeDocumento=="declaracaoSemNotas"){
            if($this->verificacaoAcesso->verificarAcesso("", ["relatorioAluno"], [$this->classe, $this->idPCurso])){
              $this->verificarPagamentoDeclaracaoSemNotas();
            }
          }
        }else if($this->accao=="listarNotasAtrasoAtrasoAlunos"){
            listarNotasAtrasoAlunos($this);
        }else if($this->accao=="carregarNotasAtraso"){
           if($this->verificacaoAcesso->verificarAcesso("", ["relatorioAluno"], [$this->classe, $this->idPCurso])){
              gravarNotasAtraso($this);
           }
        }else if($this->accao=="alterarNotasAtraso"){
          if($this->verificacaoAcesso->verificarAcesso("", ["relatorioAluno"], [$this->classe, $this->idPCurso])){
            alterarNotasAtraso($this);
          }
        }else if($this->accao=="buscarCabelho"){
          echo json_encode($this->cabecalhoTermpAproveitamento($_GET['idPAno'], $this->idPCurso, $this->classe, "notasAtraso"));
        }
        
      }

      private function verificarPagamentoDeclaracaoSemNotas(){

        unset($_SESSION["idHistoricoPagamento"]);
        unset($_SESSION["idNomeDocumentoPagar"]);
        unset($_SESSION["idMatriculaPagamento"]);

          $sobreAluno = $this->selectArray("alunosmatriculados", ["dataEBIAluno", "dataCaducidadeBI", "reconfirmacoes.idReconfAno", "reconfirmacoes.idReconfEscola", "reconfirmacoes.idMatCurso", "grupo", "escola.beneficiosDaBolsa"], ["idPMatricula"=>$this->idPMatricula], ["escola"], 1);

          $mensagem="podeTratar";
          $dataEBIAluno = valorArray($sobreAluno, "dataEBIAluno");
          $dataCaducidadeBI = valorArray($sobreAluno, "dataCaducidadeBI");
          $precoEmolumento = $this->preco("declaracaoS", $this->classe, $this->idPCurso, "", $sobreAluno);

          $idPHistoricoConta = $this->pagamentoAnteriorDoAluno($this->idPMatricula, "declaracaoS");

          if($dataEBIAluno!=NULL && $dataEBIAluno!="" && $dataEBIAluno!="0000-00-00" && $dataCaducidadeBI<$this->dataSistema){
                $mensagem="O Bilhete de Identidade já caducou.";
          }else if(($idPHistoricoConta==NULL || $idPHistoricoConta=="") && $precoEmolumento>0){
              $mensagem = "Este(a) aluno(a) ainda não fez pagamento deste documento.";
          }else{
            if(count(listarItensObjecto($sobreAluno, "reconfirmacoes", ["idReconfAno=".$this->idAnoActual, "idReconfEscola=".$_SESSION["idEscolaLogada"], "idMatCurso=".$this->idPCurso]))<=0){
              $mensagem = "Este(a) aluno não fez reconfirmação neste ano lectivo.";
            } 
          }
          
          if($idPHistoricoConta!=NULL && $idPHistoricoConta!=""){ 
            $this->editarItemObjecto("alunos_".valorArray($sobreAluno, "grupo"), "pagamentos", "estadoPagamento", ["A"], ["idPMatricula"=>$this->idPMatricula], ["idPHistoricoConta"=>$idPHistoricoConta]);

            $this->editarItemObjecto("payments", "itens", "estadoItem", ["A"], ["identificadorCliente"=>$this->idPMatricula], ["idPHistoricoConta"=>$idPHistoricoConta]);
          }
          if($mensagem=="podeTratar"){
            $_SESSION["idNomeDocumentoPagar"]="declaracaoSemNotas";
            $_SESSION["idMatriculaPagamento"]=$this->idPMatricula;
          }
          echo $mensagem;
      }

      private function verificarPagamentoDeclaracao(){
        $documentoTratar = $_GET["documentoTratar"];

        unset($_SESSION["idHistoricoPagamento"]);
        unset($_SESSION["idNomeDocumentoPagar"]);
        unset($_SESSION["idMatriculaPagamento"]);

        $sobreAluno = $this->selectArray("alunosmatriculados", ["dataEBIAluno", "dataCaducidadeBI", "reconfirmacoes.idReconfAno", "reconfirmacoes.idReconfEscola", "provNascAluno", "municNascAluno", "comunaNascAluno", "paisNascAluno", "escola.classeActualAluno", "escola.idMatCurso", "grupo", "escola.beneficiosDaBolsa"], ["idPMatricula"=>$this->idPMatricula, "escola.idMatEscola"=>$_SESSION['idEscolaLogada']], ["escola"], 1);
        $sobreAluno = $this->sobreEscreverAluno($sobreAluno, $this->idPCurso);

        $precoEmolumento = $this->preco("declaracao", $documentoTratar, $this->idPCurso, "", $sobreAluno);
        $idPHistoricoConta = $this->pagamentoAnteriorDoAluno($this->idPMatricula, "declaracao", $documentoTratar);

        $mensagem="podeTratar";
        $dataEBIAluno = valorArray($sobreAluno, "dataEBIAluno");
        $dataCaducidadeBI = valorArray($sobreAluno, "dataCaducidadeBI");

        if($dataEBIAluno!=NULL && $dataEBIAluno!="" && $dataEBIAluno!="0000-00-00" && $dataCaducidadeBI<$this->dataSistema){
              $mensagem="O Bilhete de Identidade já caducou.";
        }else if(valorArray($sobreAluno, "paisNascAluno")==NULL || valorArray($sobreAluno, "municNascAluno")==NULL || valorArray($sobreAluno, "provNascAluno")==NULL || valorArray($sobreAluno, "comunaNascAluno")==NULL){
            $mensagem="Complete a naturalidade do(a) aluno(a).";
        }else if(($idPHistoricoConta==NULL || $idPHistoricoConta=="") && $precoEmolumento>0){
              $mensagem = "Este(a) aluno(a) ainda não fez pagamento deste documento.";
        }else{
            $verf = $this->verificarSePodeTratarDeclaracao($this->idPMatricula, $documentoTratar, valorArray($sobreAluno, "classeActualAluno", "escola"), valorArray($sobreAluno, "idMatCurso", "escola"));
            if($verf!="permitido"){
              $mensagem=$verf;
            }
        } 

        if($idPHistoricoConta!=NULL && $idPHistoricoConta!=""){
          $this->editarItemObjecto("alunos_".valorArray($sobreAluno, "grupo"), "pagamentos", "estadoPagamento", ["A"], ["idPMatricula"=>$this->idPMatricula], ["idPHistoricoConta"=>$idPHistoricoConta]);
          $this->editarItemObjecto("payments", "itens", "estadoItem", ["A"], ["identificadorCliente"=>$this->idPMatricula], ["idPHistoricoConta"=>$idPHistoricoConta]);
        }

        if($mensagem=="podeTratar"){
          $_SESSION["idMatriculaPagamento"]=$this->idPMatricula;
          $_SESSION["idNomeDocumentoPagar"]=$documentoTratar; 
        }
        echo $mensagem;
      }

      
    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>