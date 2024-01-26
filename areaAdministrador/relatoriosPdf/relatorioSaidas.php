<?php 
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    } 
    include_once ('../funcoesAuxiliares.php');
    include_once ('../funcoesAuxiliaresDb.php');

    class mapaPagamentos extends funcoesAuxiliares{
        private $ultimaData="";

        function __construct($caminhoAbsoluto){

            $this->mesPagamento = isset($_GET["mesPagamento"])?$_GET["mesPagamento"]:null;
            $this->anoCivil = isset($_GET["anoCivil"])?$_GET["anoCivil"]:null;

            parent::__construct("Relatário de Saídas");  

            $this->html="<html style='margin:20px; margin-top:20px;'>
            <head>
                <title>Relatório de Saídas</title>
                <style>
                    table tr td{
                        border:solid rgba(0,0,0,0.01) 0.01px;
                        padding: 3px;
                        font-size: 10pt;
                    }

                    #detalhes p{
                        margin-bottom:-7px !important;
                    }
                </style>
            </head>
            <body>";
            $this->mapa();            
        }

         private function mapa(){

            $this->html .="
            <p ><strong>LUZINGU LUA KIESSE LDA</strong><br>
            NIF: 5001148583<br>
            Luanda, Cacuaco
            </p>
            <p style='".$this->bolder.$this->maiuscula.$this->text_center."'>Relatório de Saídas - ".nomeMes($this->mesPagamento)."/".$this->anoCivil."</p>
            <table style='".$this->tabela."width:100%;'>
            <tr style='".$this->corDanger.$this->bolder.$this->maiuscula."'><td style='".$this->border().$this->text_center."'>N.º</td><td style='".$this->border().$this->text_center."'>Data</td><td style='".$this->border().$this->text_center."'>Descrição</td><td style='".$this->border().$this->text_center."'>Valor</td></tr>";
            $i=0;
            $total=0;
            foreach ($this->selectArray("saidas_luzl", [], ["dataSaida"=>new \MongoDB\BSON\Regex($this->anoCivil."-".completarNumero($this->mesPagamento)."-")]) as $a) {
                $i++;
                $this->html .="<tr><td style='".$this->border().$this->text_center."'>".completarNumero($i)."</td><td style='".$this->border().$this->text_center."'>".$a["dataSaida"]."</td><td style='".$this->border()."'>".$a["descricaoSaida"]."</td><td style='".$this->border().$this->text_center."'>".number_format($a["valor"], 2, ",", ".")."</td></tr>";
                $total +=$a["valor"];
            }

            $this->html .="<tr style='".$this->bolder."'><td style='".$this->border().$this->text_center."' colspan='3'>Total</td><td style='".$this->border().$this->text_center."'>".number_format($total, 2, ",", ".")."</td></tr>
            </table>

            <p>Luzingu Lua Kiesse, em Luanda, aos ".dataExtensa($this->dataSistema)."</p>
            <p>".$this->porAssinatura("O(a) Responsável", "", "", 20)."</p>";

            $this->exibir("", "Relatório de Saídas - ".nomeMes($this->mesPagamento));
        }



    }

new mapaPagamentos(__DIR__);
    
    
  
?>