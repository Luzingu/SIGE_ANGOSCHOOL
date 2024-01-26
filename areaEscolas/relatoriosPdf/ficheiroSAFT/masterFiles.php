<?php 
	
	function customer($m, $dataInicial, $dataFinal){
		$retorno="";
		foreach($m->selectDistinct("payments", "identificadorCliente", ["idDocEscola"=>$_SESSION['idEscolaLogada'], "dataEmissao"=>array('$gte'=>$dataInicial), "dataEmissao"=>array('$gte'=>$dataFinal)]) as $cliente){

			if($cliente["_id"]!=""){

				$a = $m->selectArray("payments", ["identificadorCliente", "nomeCliente", "codigoCliente", "nifCliente"], ["identificadorCliente"=>$cliente["_id"]], [], 1);

				$retorno .="
			<Customer>
		      <CustomerID>".$cliente["_id"]."</CustomerID>
		      <AccountID>Desconhecido</AccountID>
		      <CustomerTaxID>999999999</CustomerTaxID>
		      <CompanyName>Consumidor Final</CompanyName>
		      <Contact>Desconhecido</Contact>
		      <BillingAddress>
		        <AddressDetail>Desconhecido</AddressDetail>
		        <City>".valorArray($m->sobreEscolaLogada, "nomeProvincia")."</City>
		        <PostalCode>Desconhecido</PostalCode>
		        <Province>".valorArray($m->sobreEscolaLogada, "nomeProvincia")."</Province>
		        <Country>AO</Country>
		      </BillingAddress>
		      <Telephone>Desconhecido</Telephone>
		      <Email>Desconhecido</Email>
		      <Website>Desconhecido</Website>
		      <SelfBillingIndicator>0</SelfBillingIndicator>
		    </Customer>";
			}
		}
	    return $retorno;
	}

	function taxTable($m, $dataInicial, $dataFinal){
		$retorno="
		<TaxTable>
			<TaxTableEntry>
		        <TaxType>IVA</TaxType>
		        <TaxCountryRegion>AO</TaxCountryRegion>
		        <TaxCode>ISE</TaxCode>
		        <Description>Isento</Description>
		        <TaxPercentage>0.00</TaxPercentage>
	      	</TaxTableEntry>
		</TaxTable>";
	    return $retorno;
	}
	


 ?>