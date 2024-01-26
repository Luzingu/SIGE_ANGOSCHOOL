<?php
include_once ('../../../manipulacaoDadosMae.php');
include_once ('masterFiles.php');
include_once ('sourceDocuments.php');
$dataInicial = isset($_GET["dataInicial"])?$_GET["dataInicial"]:"";
$dataFinal = isset($_GET["dataFinal"])?$_GET["dataFinal"]:"";
$tipoFicheiro = isset($_GET["tipoFicheiro"])?$_GET["tipoFicheiro"]:"R";
$designacaoFicheiro="";
if($tipoFicheiro=="R"){
}else{
  $tipoFicheiro="R";
}
$m = new manipulacaoDadosMae();

$xmlString = "
<?xml version='1.0' encoding='UTF-8' ?>
	<AuditFile xmlns='urn:OECD:StandardAuditFile-Tax:AO_1.01_01'>
		<Header>
	    <AuditFileVersion>1.01_01</AuditFileVersion>
	    <CompanyID>".valorArray($m->sobreEscolaLogada, "nomeComercial")."</CompanyID>
	    <TaxRegistrationNumber>".valorArray($m->sobreEscolaLogada, "nifEscola")."</TaxRegistrationNumber>
	    <TaxAccountingBasis>".$tipoFicheiro."</TaxAccountingBasis>
	    <CompanyName>".valorArray($m->sobreEscolaLogada, "nomeComercial")."</CompanyName>
	    <BusinessName>".valorArray($m->sobreEscolaLogada, "nomeComercial")."</BusinessName>
	    <CompanyAddress>
	      <AddressDetail>".valorArray($m->sobreEscolaLogada, "enderecoEscola")."</AddressDetail>
	      <City>".valorArray($m->sobreEscolaLogada, "nomeProvincia")."</City>
	      <Province>".valorArray($m->sobreEscolaLogada, "nomeProvincia")."</Province>
	      <Country>AO</Country>
	    </CompanyAddress>
	    <FiscalYear>".explode("-", $dataFinal)[0]."</FiscalYear>
	    <StartDate>".$dataInicial."</StartDate>
	    <EndDate>".$dataFinal."</EndDate>
	    <CurrencyCode>AOA</CurrencyCode>
	    <DateCreated>".$m->dataSistema."</DateCreated>
	    <TaxEntity>Global</TaxEntity>
	    <ProductCompanyTaxID>5001148583</ProductCompanyTaxID>
	    <SoftwareValidationNumber>.....</SoftwareValidationNumber>
	    <ProductID>LUZINGU LUA KIESSE LDA</ProductID>
	    <ProductVersion>1.0.2</ProductVersion>
	 	</Header>

  	<MasterFiles>
  		".customer($m, $dataInicial, $dataFinal)."
  		".taxTable($m, $dataInicial, $dataFinal)."
  	</MasterFiles>
  	<SourceDocuments>
  		".payments($m, $dataInicial, $dataFinal)."
  	</SourceDocuments>
	</AuditFile>";
	header('Content-Type: application/xml');
	header('Content-Disposition: attachment; filename="SAFT ('.$tipoFicheiro.')_AO_AngoSchool_'.$dataInicial.' a '.$dataFinal.'.xml"');
	echo trim($xmlString);
	  	
