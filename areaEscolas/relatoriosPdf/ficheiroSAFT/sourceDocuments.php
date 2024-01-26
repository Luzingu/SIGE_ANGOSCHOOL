<?php 
	
	function payments($m, $dataInicial, $dataFinal){

		$payments = $m->selectArray("payments", [], ["idDocEscola"=>$_SESSION['idEscolaLogada'], "dataEmissao"=>array('$gte'=>$dataInicial), "dataEmissao"=>array('$gte'=>$dataFinal), "tipoDocumento"=>"RC"]);

		$total=0;
		$totalCredito=0;
		$totalizadorSemImposto=0;

		foreach($payments as $p){
			if($p["estadoDocumento"]=="N"){
				$total++;
				$totalCredito +=$p["valorTotComImposto"];
			}
		}

		$retorno ="
		<Payments>
			<NumberOfEntries>".$total."</NumberOfEntries>
			<TotalDebit>0.00</TotalDebit>
			<TotalCredit>".$totalCredito."</TotalCredit>";
		foreach($payments as $p){
			$retorno .="
			<Payment>
				<PaymentRefNo>".$p["identificacaoUnica"]."</PaymentRefNo>
				<TransactionDate>".$p["dataEmissao"]."</TransactionDate>
				<PaymentType>".$p["tipoDocumento"]."</PaymentType>

				<DocumentStatus>
					<PaymentStatus>".$p["estadoDocumento"]."</PaymentStatus>
					<PaymentStatusDate>".$p["dataEmissao"]."T".$p["horaEmissao"]."</PaymentStatusDate>
					<SourceID>".$p["idFuncionario"]."</SourceID>
					<SourcePayment>P</SourcePayment>
				</DocumentStatus>
				<Hash>".$p["hash"]."</Hash>
    			<HashControl>1</HashControl>
				<SourceID>".$p["idFuncionario"]."</SourceID>
				<SystemEntryDate>".$p["dataEmissao"]."T".$p["horaEmissao"]."</SystemEntryDate>
				<CustomerID>".$p["identificadorCliente"]."</CustomerID>";

			if(isset($p["itens"])){
				$i=0;
				foreach(listarItensObjecto($p, "itens") as $item){
					$i++;
					$retorno .="
					<Line>
						<LineNumber>".$i."</LineNumber>
						<SourceDocumentID>
							<OriginatingON>".$p["identificacaoUnica"]."</OriginatingON>
							<InvoiceDate>".$p["dataEmissao"]."</InvoiceDate>
						</SourceDocumentID>

						<SettlementAmount>0.00</SettlementAmount>
						<CreditAmount>".number_format($item["valorTotSemImposto"], 2, ".", "")."
						</CreditAmount>
						<Tax>
							<TaxType>IVA</TaxType>
							<TaxCountryRegion>AO</TaxCountryRegion>
							<TaxCode>ISE</TaxCode>
							<TaxPercentage>0.00</TaxPercentage>
							<TaxAmount>0.00</TaxAmount>
						</Tax>
						<TaxExemptionReason>Transmissão de bens e serviço não sujeita</TaxExemptionReason>
						<TaxExemptionCode>M02</TaxExemptionCode>
					</Line>";
				}
				$retorno .="";
			}
			$retorno .="
				<DocumentTotals>
					<TaxPayable>0.00</TaxPayable>
					<NetTotal>".number_format($p["valorTotSemImposto"], 2, ".", "")."</NetTotal>
					<GrossTotal>0.00</GrossTotal>
				</DocumentTotals>
			</Payment>";
		}
		$retorno .="
		</Payments>";
		return $retorno;
	}






 ?>