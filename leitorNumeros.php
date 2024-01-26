<?php 
	class leitorNumero{
		private $totalNumerosDepoisDele="";
		private $numeroDepoisDele="";

		public function numeroExtenso($numero, $numeroCasasDecimais=0, $tipoDecimal="extenso"){
			$numero = explode(",", $numero);

			$parteInteira = $numero[0];
			$parteDecimal = isset($numero[1])?$numero[1]:0;
			
			//Tirar zeros na parte decimal
			$numeroPosicaoDescontar=0;
			for($t=strlen($parteDecimal); $t>=1; $t--){
				//$valor
				if($t==1){
					$numeroAnalizar = substr($parteDecimal, 0, 1);
				}else{
					$numeroAnalizar = substr($parteDecimal, $t-1, $t-1);
				}
				if($numeroAnalizar==0){
					$numeroPosicaoDescontar++;
				}else{
					break;
				}
			}
			$parteDecimal = substr($parteDecimal, 0, strlen($parteDecimal)-$numeroPosicaoDescontar);
			
			//Fim tirar zeros(0)...
			
			$parteInteira = (int) str_replace(".", "", $parteInteira);

			$leitor="";
			if($parteInteira<=19){
				$leitor=$this->leitorNumerosSemLogica($parteInteira);
			}else if(strlen($parteInteira)<=3){
				$leitor .=$this->factorizador($parteInteira);
			}else if(strlen($parteInteira)<=6){
				$totResto = strlen($parteInteira)-3;
				$parte1 = substr($parteInteira, 0, $totResto);
				$parte2 = substr($parteInteira, $totResto, strlen($parteInteira));
				if($parte1<=1){
					$leitor .="mil";
				}else{
					$leitor .=$this->factorizador($parte1)." mil";
				}
				$leitor .=" ".$this->factorizador($parte2);
			}else if(strlen($parteInteira)<=9){
				$totResto2 = strlen($parteInteira)-6;

				//Tou em dúvida...
				$parte1 = substr($parteInteira, 0, $totResto2);
				$parte2 = substr($parteInteira, $totResto2, $totResto2);
				$parte3 = substr($parteInteira, $totResto2+3, $totResto2+3);
				//OK...

				if($parte1==1){
					$leitor .="um milhão";
				}else{
					$leitor .= $this->factorizador($parte1)." milhões";
				}
				if($parte2==1){
					$leitor .=" mil";
				}else{
					$leitor .=" ".$this->factorizador($parte2)." mil";
				}
				$leitor .=" ".$this->factorizador($parte3);
			}
			$leitor2="";
			if((int) $parteDecimal>0){
				$leitor2 .=$this->factorizador($parteDecimal);
				if($tipoDecimal=="extenso"){
					if(strlen($parteDecimal)==3){
						if($parteDecimal==1){
							$leitor2 .=" milésima";
						}else{
							$leitor2 .=" milésimas";
						}					
					}if(strlen($parteDecimal)==2){
						if($parteDecimal==1){
							$leitor2 .=" centésima";
						}else{
							$leitor2 .=" centésimas";
						}					
					}else if(strlen($parteDecimal)==1){
						if($parteDecimal==1){
							$leitor2 .=" décima";
						}else{
							$leitor2 .=" décimas";
						}					
					}
				}
			}
			if($leitor2!=""){
				return $leitor." e ".$leitor2;
			}else{
				return $leitor;
			}
		}

		private function factorizador($parteFactorizar){
			$parteFactorizar = (int) $parteFactorizar;
			$retorno="";
			for ($posicaoNumero=1;  $posicaoNumero<=strlen($parteFactorizar); $posicaoNumero++) { 
				$this->totalNumerosDepoisDele = strlen($parteFactorizar)-$posicaoNumero;

				$this->numeroDepoisDele=substr($parteFactorizar, $posicaoNumero, $posicaoNumero);
				
				if($posicaoNumero==1){
					$numeroEmReferencia = substr($parteFactorizar, 0, 1);
				}else{
					$numeroEmReferencia = substr($parteFactorizar, $posicaoNumero-1, $posicaoNumero-1);
				}
				if($parteFactorizar<=19){
					$retorno .= $this->leitorNumerosSemLogica($parteFactorizar);
					break;
				}else if(strlen($parteFactorizar)==3 && substr($parteFactorizar, 1, 2)<19 && substr($parteFactorizar, 1, 2)!=0){
					$retorno .= $this->centena(substr($parteFactorizar, 0, 1))." e ".$this->leitorNumerosSemLogica(substr($parteFactorizar, 1, 2));
					break;
				} else{

					if($this->totalNumerosDepoisDele==2 && $parteFactorizar==100){
						$retorno .="cem";
					}else if($this->totalNumerosDepoisDele==2){
						$retorno .= $this->centena($numeroEmReferencia);
					}else if($this->totalNumerosDepoisDele==1){
						$retorno .= $this->dezena($numeroEmReferencia);
					}elseif($this->totalNumerosDepoisDele==0){
						$retorno .= $this->leitorNumerosSemLogica($numeroEmReferencia);
					}

					if($retorno!="" && $this->numeroDepoisDele!="0" && $this->numeroDepoisDele!=""){
						$retorno .=" e ";
					}					
				}
			}
			return $retorno;
		}

		private function leitorNumerosSemLogica($numero){
			if($numero==1) {
	            return "um";
	        }elseif ($numero==2) {
	            return "dois";
	        }elseif ($numero==3) {
	            return "três";
	        }elseif ($numero==4) {
	            return "quatro";
	        }elseif ($numero==5) {
	            return "cinco";
	        }elseif ($numero==6) {
	            return "seis";
	        }elseif ($numero==7) {
	            return "sete";
	        }elseif ($numero==8) {
	            return "oito";
	        }elseif ($numero==9) {
	            return "nove";
	        }else if($numero==10){
	            return "dez";
	        }elseif ($numero==11) {
	            return "onze";
	        }elseif ($numero==12) {
	            return "doze";
	        }elseif ($numero==13) {
	            return "treze";
	        }elseif ($numero==14) {
	            return "catorze";
	        }elseif ($numero==15) {
	            return "quinze";
	        }elseif ($numero==16) {
	            return "dezasseis";
	        }elseif ($numero==17) {
	            return "dezassete";
	        }elseif ($numero==18) {
	            return "dezoito";
	        }elseif ($numero==19) {
	            return "dezanove";
	        }else{
	        	return "";
	        }
		}

		private function dezena($numero){
			if($numero=="2"){
				return "vinte";
			}else if($numero=="3"){
				return "trinta";
			}else if($numero=="4"){
				return "quarenta";
			}else if($numero=="5"){
				return "cinquenta";
			}else if($numero=="6"){
				return "sessenta";
			}else if($numero=="7"){
				return "setenta";
			}else if($numero=="8"){
				return "oitenta";
			}else if($numero=="9"){
				return "noventa";
			}
		}

		private function centena($numero){
			if($numero=="1"){
				return "cento";
			}else if($numero=="2"){
				return "duzentos";
			}else if($numero=="3"){
				return "trezentos";
			}else if($numero=="4"){
				return "quatrocentos";
			}else if($numero=="5"){
				return "quinhentos";
			}else if($numero=="6"){
				return "seiscentos";
			}else if($numero=="7"){
				return "setecentos";
			}else if($numero=="8"){
				return "oitocentos";
			}else if($numero=="9"){
				return "novecentos";
			}else{
				return "";
			}
		}
	}

	


?>