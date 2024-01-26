<?php 
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');
    include_once ('../../../manipulacaoDadosMae.php');

    include_once ('facturaFR.php');
    include_once ('notaCredito.php');
    include_once ('reciboPagamento.php');
    include_once ('reciboPagamentoMensalidades.php');

    $m = new manipulacaoDadosMae();
    $idPDocumento="";

    if(isset($_GET["idPHistoricoConta"])){
    	$idPMatricula = isset($_GET["idPMatricula"])?$_GET["idPMatricula"]:"";
    	$array = $m->selectArray("alunosmatriculados", ["pagamentos.identFactura", "pagamentos.idTipoEmolumento"], ["idPMatricula"=>$idPMatricula, "pagamentos.idPHistoricoConta"=>$_GET["idPHistoricoConta"]], ["pagamentos"]);
 
    	if(valorArray($array, "identFactura", "pagamentos")=="" || valorArray($array, "identFactura", "pagamentos")==NULL){
    		if(valorArray($array, "idTipoEmolumento", "pagamentos")==1){
    			new reciboPagamentoMensalidade();
    		}else{
    			new reciboPagamento();
    		}
    	}else{
        $idPDocumento = $m->selectUmElemento("payments", "idPDocumento", ["identificacaoUnica"=>valorArray($array, "identFactura", "pagamentos"), "idDocEscola"=>$_SESSION['idEscolaLogada']]);
    	}

    }else if(isset($_GET["idPDocumento"])){
      $idPDocumento = $m->selectUmElemento("payments", "idPDocumento", ["idPDocumento"=>$_GET["idPDocumento"], "idDocEscola"=>$_SESSION['idEscolaLogada'], "tipoDocumento"=>array('$in'=>array("NC", "RC"))]);
    }else if(isset($_GET["idPPagamento"])){
      $array = $m->selectArray("payments", ["itens.idPPagamento", "idPDocumento"], ["itens.idPPagamento"=>$_GET["idPPagamento"]], ["itens"]);
      $idPDocumento = valorArray($array, "idPDocumento");
    }

    if($idPDocumento!=""){
      $array = $m->selectArray("payments", ["idPDocumento", "tipoDocumento", "estadoDocumento"], ["idPDocumento"=>$idPDocumento, "idDocEscola"=>$_SESSION['idEscolaLogada']]);
        
      if(valorArray($array, "tipoDocumento")=="RC"){
        $factura = new facturaFR();
        $factura->idPDocumento = valorArray($array, "idPDocumento");
        $factura->facturaFR();
      }else if(valorArray($array, "tipoDocumento")=="NC"){
        $factura = new notaCredito();
        $factura->idPDocumento = valorArray($array, "idPDocumento");
        $factura->notaCredito();
      }
    }

    
?>