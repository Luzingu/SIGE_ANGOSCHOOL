<?php if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');

    class lista extends funcoesAuxiliares{

        function __construct(){
            parent::__construct("Lista de Facturas mensais");

            $this->mesPagamento = isset($_GET["mesPagamento"])?$_GET["mesPagamento"]:"";
            $this->anoCivil = isset($_GET["anoCivil"])?$_GET["anoCivil"]:"";
            $this->estadoDocumento = isset($_GET["estadoDocumento"])?$_GET["estadoDocumento"]:"N";

            if($this->estadoDocumento!="N" && $this->estadoDocumento!="A"){
                $this->estadoDocumento ="N";
            }

            if($this->verificacaoAcesso->verificarAcesso("", ["facturas"], array(), "")){
                $this->visualizar();
            }   
        }

         private function visualizar(){
            $this->html="<html style='margin-left:30px;margin-right:30px;'>
            <head>
                <title>Lista de Pagamentos</title>
            </head>
            <body>".$this->fundoDocumento("../../../").$this->cabecalho();

            $this->html .="
            <p style='".$this->text_center.$this->maiuscula.$this->bolder."'>Recibos ".($this->estadoDocumento=="N"?"Normais":"Anulados")." do mês de ".nomeMes($this->mesPagamento)." -  ".$this->anoCivil."</p>";

            $this->html .="
            <table style='".$this->tabela." width:100%;'>
                <tr style='".$this->corDanger.$this->bolder.$this->text_center.$this->maiuscula."'>
                    <td style='".$this->border()."'>N.º</td>
                    <td style='".$this->border()."'>Data</td>
                    <td style='".$this->border()."'>Funcionário</td>
                    <td style='".$this->border()."'>Cliente</td>
                    <td style='".$this->border()."'>Itens</td>
                    <td style='".$this->border()."'>Valor (Kz)</td>
                </tr>";
            $totalAcumulado=0;
            $i=0;
            $totalAcumulado=0;

            foreach($this->selectArray("payments", [], ["idDocEscola"=>$_SESSION['idEscolaLogada'], "dataEmissao"=>new \MongoDB\BSON\Regex($this->anoCivil."-".completarNumero($this->mesPagamento)."-"), "estadoDocumento"=>$this->estadoDocumento, "tipoDocumento"=>"RC"], [], "", [], array("idPDocumento"=>1)) as $fact){
                $i++;
                $itens="";
                foreach($fact["itens"] as $item){
                    if($item["codigoProduto"]=="propinas"){
                        if($itens!=""){
                            $itens .=", ";
                        }
                        $itens .=nomeMes($item["referenciaPagamento"]);
                    }else{
                        if($itens!=""){
                            $itens .=", ";
                        }
                        $itens .=$item["descricaoProduto"];
                    }
                }
                $totalAcumulado +=floatval($fact["valorTotComImposto"]);
                $this->html .="<tr>
                    <td style='".$this->border().$this->text_center." font-size:10pt;'>".$fact["identificacaoUnica"]."/".explode("-", $fact["dataEmissao"])[0]."</td>
                    <td style='".$this->border().$this->text_center."'>".converterData($fact["dataEmissao"])."</td>
                    <td style='".$this->border()."'>".$fact["nomeFuncionario"]."</td>
                    <td style='".$this->border()."'>".$fact["nomeCliente"]."</td>
                    <td style='".$this->border()." font-size:9pt;'>".$itens."</td>
                    <td style='".$this->border().$this->text_center."'>".number_format(floatval($fact["valorTotComImposto"]), 2, ",", ".")."</td>
                </tr>";
            }
            $this->html .="<tr>
                    <td style='".$this->border().$this->text_center."' colspan='5'>Total</td>
                    <td style='".$this->border().$this->text_center."'>".number_format($totalAcumulado, 2, ",", ".")."</td>
                </tr>";
             $this->html .="

            </table><p style='".$this->text_center."'>".$this->rodape().".</p>
            ".$this->porAssinatura("O(a) Responsável", "", "", 25);
            
            $this->exibir("", "Facturas do mês de ".nomeMes($this->mesPagamento), "", "A4", "landscape"); 
        }
        
    }
    new lista();
?>