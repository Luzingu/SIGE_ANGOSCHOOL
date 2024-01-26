<?php 
     if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }

    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');
    
    class factura extends funcoesAuxiliares{

        function __construct(){
            parent::__construct();
            $this->relatorio();
        }

        public function relatorio(){
            $idPDocumento = isset($_GET["idPDocumento"])?$_GET["idPDocumento"]:"";

           $fornecedor = $this->selectArray("purchase_invoices", [], ["idPDocumento"=>$idPDocumento, "idDocEscola"=>$_SESSION['idEscolaLogada']]);

           $this->html .="
           <html >
            <head>
                <title>Recibo</title>
                <style>
                    .tabela tr td{
                        border-left: solid black 1px;
                        border-bottom: solid black 1px;
                        padding:4px;

                    }
                    .tabela{
                        border-spacing:5px;
                    }
                    html{
                        ";
                    if(valorArray($this->sobreEscolaLogada, "comprovativo")=="A6"){
                        $this->html .="margin:10px;
                        font-size:9pt;";
                    }else if(valorArray($this->sobreEscolaLogada, "comprovativo")=="A5"){
                        $this->html .="margin:10px;
                        font-size:10pt;";
                    }else{
                        $this->html .="margin:50px;";
                    }
                $this->html .="
                    }
                </style>
            </head>
           <body>";
            
            $nomeComercial = valorArray($this->sobreEscolaLogada, "nomeComercial");
            if($nomeComercial=="" || $nomeComercial==null){
                $nomeComercial = valorArray($this->sobreEscolaLogada, "nomeEscola");
            }
            if(valorArray($this->sobreEscolaLogada, "comprovativo")=="A6"){
                $this->html .="<p style='".$this->miniParagrafo.$this->maiuscula.$this->text_center.$this->bolder."'>".$nomeComercial."</p>";
            }else{
                $src = $_SERVER['DOCUMENT_ROOT'].'/angoschool/Ficheiros/Escola_'.$_SESSION['idEscolaLogada'].'/Icones/'.valorArray($this->sobreEscolaLogada, "logoEscola");
                
                if(!file_exists($src) || valorArray($this->sobreUsuarioLogado, "logoEscola")==NULL || valorArray($this->sobreUsuarioLogado, "logoEscola")==""){
                  $src = $_SERVER['DOCUMENT_ROOT'].'/angoschool/icones/insignia.jpg';
                }

                $this->html .="<p style='".$this->miniParagrafo."'><img src='".$src."' style='with:45px; height:45px;'></p>
                <p style='".$this->miniParagrafo."'>".$nomeComercial."</p>";
            }


            $this->html .="
            <p style='".$this->miniParagrafo."'>Contribuinte N.º ".valorArray($this->sobreEscolaLogada, "nifEscola")."</p>
            <p style='".$this->miniParagrafo."'>Telefone: ".valorArray($this->sobreEscolaLogada, "numeroTelefone")."</p>
            <p style='".$this->miniParagrafo."'>E-mail: ".valorArray($this->sobreEscolaLogada, "email")."</p>
            <p style='".$this->miniParagrafo."'>Endereço: ".valorArray($this->sobreEscolaLogada, "enderecoEscola")."</p>";

            if(valorArray($this->sobreEscolaLogada, "comprovativo")=="A6"){
                $this->html .="<div style='margin-top:10px;".$this->text_right."'>";
            }else if(valorArray($this->sobreEscolaLogada, "comprovativo")=="A5"){
                $this->html .="<div style='margin-top:-50pt; position:absolute; margin-left:300px; width:230px;'>";
            }else{
                $this->html .="<div style='margin-top:-60pt; position:absolute; margin-left:400px;'>";
            }

            $this->html .="
                Exmo.(s) Sr.(s)<br>
                <strong>".valorArray($fornecedor, "nomeEmpresa")."</strong>
                <br><strong>".valorArray(listarItensObjecto($this->sobreEscolaLogada, "fornecedores", ["idPFornecedor=".valorArray($fornecedor, "idFornecedor")]), "nifEmpresa")."</strong>
            </div>
            <p style='".$this->text_center.$this->bolder.$this->maiuscula."'>Nota de Liquidição</p>";
            
            if(valorArray($this->sobreEscolaLogada, "comprovativo")=="A6"){
                $this->html .="<table style='width:100%; margin-top:10px; border-spacing:0px;'>";
            }else{
                $this->html .="<table style='width:100%; margin-top:30px; border-spacing:0px;'>";
            }
            $this->html .="            
                <tr>
                    <!--<td colspan='7' style='border-bottom:solid black 2px'><strong>Pagamento n.º ".valorArray($fornecedor, "identificacaoUnica")."</strong></td>!-->
                </tr>
                <tr>
                    <td style='border-bottom:solid black 1px'>Data</td>
                    <td></td>
                    <td style='border-bottom:solid black 1px'>Vencimento</td>
                    <td></td>
                    <td style='border-bottom:solid black 1px'>Contribuinte</td>
                    <td></td>
                    <td style='border-bottom:solid black 1px'>V/ Ref.</td>
                </tr>
                <tr>
                    <td style='border-bottom:solid black 2px'>".valorArray($fornecedor, "dataEmissao")."</td>
                    <td style='border-bottom:solid black 2px'></td>
                    <td style='border-bottom:solid black 2px'>".valorArray($fornecedor, "dataEmissao")."</td>
                    <td style='border-bottom:solid black 2px'></td>
                    <td style='border-bottom:solid black 2px'>".valorArray(listarItensObjecto($this->sobreEscolaLogada, "fornecedores", ["idPFornecedor=".valorArray($fornecedor, "idFornecedor")]), "nifEmpresa")."</td>
                    <td style='border-bottom:solid black 2px'></td>
                    <td style='border-bottom:solid black 2px'>--</td>
                </tr>
            </table>

            <table style='width:100%; margin-top:15px; border-spacing:0; font-size:10pt;'>
                <tr style='".$this->text_center."'>
                    <td style='".$this->bolder." border-bottom:solid black 1px;'>Valor</td>
                    <td style='".$this->bolder." border-bottom:solid black 1px;'>IVA</td>
                    <td style='".$this->bolder.$this->text_center." border-bottom:solid black 1px;'>Retenção</td>
                    <td style='".$this->bolder.$this->text_center." border-bottom:solid black 1px;'>Total</td>
                </tr>";
            
            $this->html .="<tr style='".$this->text_center."'>
                <td style='border-bottom:solid black 1px;".$this->text_center."'>".number_format(floatval(valorArray($fornecedor, "valorLiquidado")), 2, ".",",")."</td>
                <td style='border-bottom:solid black 1px;".$this->text_center."'>".number_format(floatval(valorArray($fornecedor, "IVA")), 2, ".",",")."</td>
                <td style='border-bottom:solid black 1px;".$this->text_center."'>".number_format(floatval(valorArray($fornecedor, "montanteImpostoRetido")), 2, ".",",")."</td>
                <td style='border-bottom:solid black 1px;".$this->text_center."'>".number_format(floatval(valorArray($fornecedor, "totalLiquidado")), 2, ".",",")."</td>
            </tr>";
            

           $this->html .="</table>

               <table style='width:45%; margin-top:20px; border-spacing:0px;'>
                <tr>
                    <td style='border-bottom: solid black 2px;' colspan='3'>Quadro Resumo de Impostos</td>
                </tr>
                <tr>
                    <td style='border-bottom: solid black 2px;'>Descrição</td>
                    <td style='border-bottom: solid black 2px;'>Incidência</td>
                    <td style='border-bottom: solid black 2px;'>Imposto</td>
                </tr>
                <tr>
                    <td style='border-bottom: solid black 2px;'>Isento</td>
                    <td style='border-bottom: solid black 2px;'>0,00</td>
                    <td style='border-bottom: solid black 2px;'>0,00</td>
                </tr>
               </table>

            <table style='width:45%; position:absolute; margin-top:-50px; margin-left:55%; border-spacing:0px; border-top:solid black 1px;'>
                <tr>
                    <td style='".$this->bolder."'>Total Líquido:</td>
                    <td style='".$this->text_right."'>".number_format(valorArray($fornecedor, "valorLiquidado") , 2, ",", ".")."</td>
                </tr>
                <tr>
                    <td style='".$this->bolder."'>IVA:</td>
                    <td style='".$this->text_right."'>".number_format(floatval(valorArray($fornecedor, "IVA")), 2, ",", ".")."</td>
                </tr>
                <tr>
                    <td style='".$this->bolder." border-bottom:solid black 2px;'>Retenção na Fonte:</td>
                    <td style='".$this->text_right." border-bottom:solid black 2px;'>".number_format(floatval(valorArray($fornecedor, "montanteImpostoRetido")), 2, ",", ".")."</td>
                </tr>
                <tr>
                    <td style='".$this->bolder."'>Total:</td>
                    <td style='".$this->text_right."'>".number_format(floatval(valorArray($fornecedor, "totalLiquidado")), 2, ",", ".")."</td>
                </tr>
            </table>
        </div></body></html>";
            
            
           $this->exibir("", "Nota de Liquidição", "", valorArray($this->sobreEscolaLogada, "comprovativo"));
        }
    }
    new factura();
?>
