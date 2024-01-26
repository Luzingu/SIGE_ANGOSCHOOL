<?php if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');

    class lista extends funcoesAuxiliares{

        function __construct(){
            parent::__construct("Lista de Notas de Crédito diárias");
            $this->dataHistorico = isset($_GET["dataHistorico"])?$_GET["dataHistorico"]:"";

            if($this->verificacaoAcesso->verificarAcesso("", ["facturas"], [$this->classe, $this->idPCurso], "")){
                $this->visualizar();
            }  
        }

         private function visualizar(){
            $this->html="<html style='margin-left:30px;margin-right:30px;'>
            <head>
                <title>Notas de Crédito</title>
            </head>
            <body>".$this->fundoDocumento("../../../").$this->cabecalho();

            $this->html .="
            <p style='".$this->text_center.$this->maiuscula.$this->bolder."'>NOTAS DE CRÉDITO -  ".dataExtensa($this->dataHistorico)."</p>";

            $this->html .="
            <table style='".$this->tabela." width:100%;'>
                <tr style='".$this->corDanger.$this->bolder.$this->text_center.$this->maiuscula."'>
                    <td style='".$this->border()."'>N.º</td>
                    <td style='".$this->border()."'>N.º Fact.</td>
                    <td style='".$this->border()."'>Funcionário</td>
                    <td style='".$this->border()."'>Cliente</td>
                    <td style='".$this->border()."'>Valor (Kz)</td>
                </tr>";
            $totalAcumulado=0;
            $i=0;
            $totalAcumulado=0;

            foreach($this->selectArray("facturas", [], ["idFacturaEscola"=>$_SESSION['idEscolaLogada'], "dataEmissao"=>$this->dataHistorico, "estadoFactura"=>"A", "tipoFactura"=>"NC"], [], "", [], array("idPFactura"=>1)) as $fact){
                $i++;

                $totalAcumulado +=floatval($fact["valorTotal"]);
                $this->html .="<tr>
                    <td style='".$this->border().$this->text_center."'>".$fact["numeroFactura"]."/".explode("-", $fact["dataEmissao"])[0]."</td>

                    <td style='".$this->border().$this->text_center."'>".$fact["numeroFacturaAnulada"]."/".explode("-", $fact["dataEmissao"])[0]."</td>
                    <td style='".$this->border()."'>".$fact["nomeFuncionario"]."</td>
                    <td style='".$this->border()."'>".$fact["nomeCliente"]."</td>
                    <td style='".$this->border().$this->text_center."'>".number_format(floatval($fact["valorTotal"]), 2, ",", ".")."</td>
                </tr>";
            }
            $this->html .="<tr>
                    <td style='".$this->border().$this->text_center."' colspan='4'>Total</td>
                    <td style='".$this->border().$this->text_center."'>".number_format($totalAcumulado, 2, ",", ".")."</td>
                </tr>";
             $this->html .="

            </table><p style='".$this->text_center."'>".$this->rodape().".</p>
            ".$this->porAssinatura("O(a) Responsável", "", "", 25);
            
            $this->exibir("", "Notas de Crédito", "", "A4", "landscape");
        }
        
    }
    new lista();
?>