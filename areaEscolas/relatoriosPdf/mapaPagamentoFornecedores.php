<?php if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }   
   
    include_once ('../funcoesAuxiliares.php');
    include_once ('../funcoesAuxiliaresDb.php');

    class lista extends funcoesAuxiliares{

        function __construct(){
            parent::__construct("Rel-Movimentos Contabilísticos");
            $this->anoCivil = isset($_GET["anoCivil"])?$_GET["anoCivil"]:"";
            $this->mesPagamento = isset($_GET["mesPagamento"])?$_GET["mesPagamento"]:"";
            $this->relatorio();
        }

         private function relatorio(){

            $lista = $this->selectArray("purchase_invoices", [],["idDocEscola"=>$_SESSION['idEscolaLogada'], "dataEmissao"=>new \MongoDB\BSON\Regex($this->anoCivil."-".completarNumero($this->mesPagamento)."-")],[], "", [], ["idPDocumento"=>1]);


            $this->html .="<html style='margin-left:0px; margin-right:0px;'>
            <head>
                <title>Folha de Salário</title>
                <style>
                  table tr td{
                      padding:3px;
                  }
                  table, p{
                    font-size:11pt;
                  }
                
                </style>
            </head>
            <body style='margin-left:50px; margin-right:50px;'>".$this->fundoDocumento("../../")."
            ".$this->cabecalho()."
            <p style='".$this->text_center.$this->maiuscula.$this->bolder.$this->sublinhado."'>Pagamento dos Fornecedores - ".nomeMes($this->mesPagamento)." / ".$this->anoCivil."</p>

            <table style='".$this->tabela."width:100%;'>
                <tr style='".$this->corDanger."'><td style='".$this->border().$this->bolder.$this->text_center."'>N.º</td><td style='".$this->border().$this->bolder."'>Referência</td><td style='".$this->border().$this->text_center.$this->bolder."'>Data<br>Emissão</td><td style='".$this->border().$this->text_center.$this->bolder."'>Tipo</td><td style='".$this->border().$this->text_center.$this->bolder."'>Empresa</td><td style='".$this->border().$this->text_center.$this->bolder."'>Valor</td><td style='".$this->border().$this->text_center.$this->bolder."'>IVA</td><td style='".$this->border().$this->text_center.$this->bolder."'>Retenção</td><td style='".$this->border().$this->text_center.$this->bolder."'>Total</td><td style='".$this->border().$this->text_center.$this->bolder."'>Conta</td></tr>";

            
            $i=0;
            $valorLiquidado=0;
            $IVA=0;
            $montanteImpostoRetido=0;
            $totalLiquidado=0;

            foreach ($lista as $a) {
                $i++;

                $valorLiquidado +=$a["valorLiquidado"];
                $IVA +=$a["IVA"];
                $montanteImpostoRetido +=floatval($a["montanteImpostoRetido"]);
                $totalLiquidado +=floatval($a["totalLiquidado"]);

                $this->html .="<tr>
                    <td style='".$this->border().$this->text_center."'>".completarNumero($i)."</td>
                    <td style='".$this->border().$this->text_center."'>".$a["referenciaDocumento"]."</td>
                    <td style='".$this->border().$this->text_center."'>".$a["dataDocCompra"]."</td>
                    <td style='".$this->border().$this->text_center."'>".$a["tipoDocumento"]."</td>
                    <td style='".$this->border()."'>".$a["nomeEmpresa"]."</td>
                    <td style='".$this->border().$this->text_center."'>".number_format($a["valorLiquidado"], 2, ",", ".")."</td>
                    <td style='".$this->border().$this->text_center."'>".number_format($a["IVA"], 2, ",", ".")."</td>
                    <td style='".$this->border().$this->text_center."'>".number_format(floatval($a["montanteImpostoRetido"]), 2, ",", ".")."</td>
                    <td style='".$this->border().$this->text_center."'>".number_format(floatval($a["totalLiquidado"]), 2, ",", ".")."</td>
                    <td style='".$this->border().$this->text_center."'>".valorArray($a, "descricaoConta")."</td>
                </tr>";
            }
            $this->html .="<tr>
                <td style='".$this->border().$this->text_center."' colspan='5'>Total</td>
                <td style='".$this->border().$this->text_center."'>".number_format($valorLiquidado, 2, ",", ".")."</td>
                <td style='".$this->border().$this->text_center."'>".number_format($IVA, 2, ",", ".")."</td>
                <td style='".$this->border().$this->text_center."'>".number_format($montanteImpostoRetido, 2, ",", ".")."</td>
                <td style='".$this->border().$this->text_center."'>".number_format($totalLiquidado, 2, ",", ".")."</td>
                <td style='".$this->border().$this->text_center."'></td>
            </tr>";
            $this->html .="</table>";
            $this->html .="<p style='padding-left:10px; padding-right:10px;".$this->text_center."'>".$this->rodape().".</p>
            <div style='".$this->maiuscula."'>".$this->assinaturaDirigentes(7)."</div>";
            
           $this->exibir("", "Pagamento de Fornecedores -".$this->anoCivil, "", "A4", "landscape");
        }
    }
    new lista();
?>