<?php if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');

    class lista extends funcoesAuxiliares{

        function __construct(){
            parent::__construct("Relatório Diário de Saídas de Valores");
            $this->dataHistorico = isset($_GET["dataHistorico"])?$_GET["dataHistorico"]:"";

            if($this->verificacaoAcesso->verificarAcesso("", ["facturas"], [$this->classe, $this->idPCurso], "")){
                $this->visualizar();
            }   
        }

         private function visualizar(){
            $this->html="<html style='margin-left:30px;margin-right:30px;'>
            <head>
                <title>Lista de Saídas de Valores</title>
            </head>
            <body>".$this->fundoDocumento("../../../").$this->cabecalho();

            $this->html .="
            <p style='".$this->text_center.$this->maiuscula.$this->bolder."'>SAÍDAS DE VALORES -  ".dataExtensa($this->dataHistorico)."</p>";

            $this->html .="
            <table style='".$this->tabela." width:100%;'>
                <tr style='".$this->corDanger.$this->bolder.$this->text_center.$this->maiuscula."'>
                    <td style='".$this->border()."'>ORDEM</td>
                    <td style='".$this->border()."'>N.º</td>
                    <td style='".$this->border()."'>Funcionário</td>
                    <td style='".$this->border()."'>Beneficiário</td>
                    <td style='".$this->border()."'>NIF</td>
                    <td style='".$this->border()."'>Itens</td>
                    <td style='".$this->border()."'>Valor (Kz)</td>
                </tr>";
            $totalAcumulado=0;
            $i=0;
            $totalAcumulado=0;

            foreach($this->selectArray("facturas", [], ["idFacturaEscola"=>$_SESSION['idEscolaLogada'], "dataEmissao"=>$this->dataHistorico, "estadoFactura"=>"A", "tipoFactura"=>"RP"], [], "", [], array("idPFactura"=>1)) as $fact){
                $i++;
                $itens="";
                foreach($fact["itens"] as $item){
                    if($itens!=""){
                        $itens .=", ";
                    }
                    $itens .=valorArray($item, "descricaoItem");                    
                }

                $totalAcumulado +=floatval($fact["valorTotal"]);
                $this->html .="<tr>
                    <td style='".$this->border().$this->text_center."'>".completarNumero($i)."</td>
                    <td style='".$this->border().$this->text_center." font-size:10pt;'>".$fact["numeroFactura"]."/".explode("-", $fact["dataEmissao"])[0]."</td>
                    <td style='".$this->border()."'>".$fact["nomeFuncionario"]."</td>
                    <td style='".$this->border()."'>".valorArray($fact, "nomeEmpresa")."</td>
                    <td style='".$this->border()."'>".valorArray($fact, "nifEmpresa")."</td>
                    <td style='".$this->border()."'>".$itens."</td>
                    <td style='".$this->border().$this->text_center."'>".number_format(floatval($fact["valorTotal"]), 2, ",", ".")."</td>
                </tr>";
            }
            $this->html .="<tr>
                    <td style='".$this->border().$this->text_center."' colspan='6'>Total</td>
                    <td style='".$this->border().$this->text_center."'>".number_format($totalAcumulado, 2, ",", ".")."</td>
                </tr>";
             $this->html .="

            </table><p style='".$this->text_center."'>".$this->rodape().".</p>
            ".$this->porAssinatura("O(a) Responsável", "", "", 25);
            
            $this->exibir("", "Saída de Valores -".$this->dataSistema, "", "A4", "landscape");
        }
        
    }
    new lista();
?>