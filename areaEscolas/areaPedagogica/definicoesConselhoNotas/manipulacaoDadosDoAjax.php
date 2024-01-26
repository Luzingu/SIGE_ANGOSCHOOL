<?php 
	session_start();
	include_once ('../../funcoesAuxiliares.php');
    include_once ('../../manipulacaoDadosDoAjax.php');
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{

    	function __construct($caminhoAbsoluto){
    		parent::__construct();

            if ($this->accao=="manipularDefinicoes"){
                if($this->verificacaoAcesso->verificarAcesso("", ["definicoesConselhoNotas"], [])){
                    $this->manipularDefinicoes();
                }
	        }
    	}

        private function manipularDefinicoes(){
            $negativasPorDeliberar = isset($_POST['negativasPorDeliberar'])?$_POST['negativasPorDeliberar']:"";
            $notaMinimaPorDeliberar = isset($_POST['notaMinimaPorDeliberar'])?$_POST['notaMinimaPorDeliberar']:"";

            $exprParaAprovado = isset($_POST['exprParaAprovado'])?$_POST['exprParaAprovado']:"";
            $exprParaAprovadoComDef = isset($_POST['exprParaAprovadoComDef'])?$_POST['exprParaAprovadoComDef']:"";
            $exprParaAprovadoComRecurso = isset($_POST['exprParaAprovadoComRecurso'])?$_POST['exprParaAprovadoComRecurso']:"";
            $exprParaNaoAprovado = isset($_POST['exprParaNaoAprovado'])?$_POST['exprParaNaoAprovado']:"";

            $mac = isset($_POST['mac'])?"V":"F";
            $trimestre1 = isset($_POST['trimestre1'])?"V":"F";
            $trimestre2 = isset($_POST['trimestre2'])?"V":"F";
            $trimestre3 = isset($_POST['trimestre3'])?"V":"F";
            $exame = isset($_POST['exame'])?"V":"F";

            $this->excluir("definicoesConselhoNotas", ["idPEscola"=>$_SESSION['idEscolaLogada'], "idPAno"=>$this->idAnoActual]);

            $this->inserir("definicoesConselhoNotas", "id", "idPEscola, idPAno, negativasPorDeliberar, notaMinimaPorDeliberar, mac, trimestre1, trimestre2, trimestre3, exame, exprParaAprovado, exprParaAprovadoComDef, exprParaAprovadoComRecurso, exprParaNaoAprovado", [$_SESSION['idEscolaLogada'], $this->idAnoActual, $negativasPorDeliberar, $notaMinimaPorDeliberar, $mac, $trimestre1, $trimestre2, $trimestre3, $exame, $exprParaAprovado, $exprParaAprovadoComDef, $exprParaAprovadoComRecurso, $exprParaNaoAprovado]);
        }

        

    	
    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>