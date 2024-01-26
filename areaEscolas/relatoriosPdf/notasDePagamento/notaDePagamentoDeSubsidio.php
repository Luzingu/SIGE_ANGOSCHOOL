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
            $idPSalario = isset($_GET["idPSalario"])?$_GET["idPSalario"]:"";
            $idPEntidade = isset($_GET["idPEntidade"])?$_GET["idPEntidade"]:"";

           $entidade = $this->selectArray("entidadesprimaria", [], ["idPEntidade"=>$idPEntidade, "salarios.idPSalario"=>$idPSalario], ["salarios"]);

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
                <strong>".valorArray($entidade, "nomeEntidade")."</strong>
                <br><strong>".valorArray($entidade, "biEntidade")."</strong>";

                $this->html .="</p>
            </div>
            <p style='".$this->text_center.$this->bolder.$this->maiuscula."'>Recibo de Pagamento</p>";
            
            if(valorArray($this->sobreEscolaLogada, "comprovativo")=="A6"){
                $this->html .="<table style='width:100%; margin-top:10px; border-spacing:0px;'>";
            }else{
                $this->html .="<table style='width:100%; margin-top:30px; border-spacing:0px;'>";
            }
            $this->html .="            
                <tr>
                    <!--<td colspan='7' style='border-bottom:solid black 2px'><strong>Pagamento n.º ".valorArray($entidade, "identificacaoUnica")."</strong></td>!-->
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
                    <td style='border-bottom:solid black 2px'>".valorArray($entidade, "dataPagamento", "salarios")."</td>
                    <td style='border-bottom:solid black 2px'></td>
                    <td style='border-bottom:solid black 2px'>".valorArray($entidade, "dataPagamento", "salarios")."</td>
                    <td style='border-bottom:solid black 2px'></td>
                    <td style='border-bottom:solid black 2px'>".valorArray($entidade, "biEntidade")."</td>
                    <td style='border-bottom:solid black 2px'></td>
                    <td style='border-bottom:solid black 2px'>--</td>
                </tr>
            </table>

            <table style='width:100%; margin-top:15px; border-spacing:0; font-size:10pt;'>
                <tr>
                    <td style='".$this->bolder." border-bottom:solid black 1px;'>Mês</td>
                    <td style='".$this->bolder." border-bottom:solid black 1px;'>Salário<br>Base</td>
                    <td style='".$this->bolder.$this->text_center." border-bottom:solid black 1px;'>Pag/<br>Tempo</td>
                    <td style='".$this->bolder.$this->text_center." border-bottom:solid black 1px;'>N.º<br>Tempos</td>
                    <td style='".$this->bolder.$this->text_center." border-bottom:solid black 1px;'>Subsídio</td>
                    <td style='".$this->bolder.$this->text_center." border-bottom:solid black 1px;'>Descontos</td>
                    <td style='".$this->bolder." border-bottom:solid black 1px;'>Total</td>
                </tr>";
            
            $this->html .="<tr style='".$this->text_center."'>
                <td style='border-bottom:solid black 1px;".$this->text_center."'>".nomeMes(valorArray($entidade, "mesPagamento", "salarios"))."</td>
                <td style='border-bottom:solid black 1px;'>".number_format(floatval(valorArray($entidade, "valorAuferidoNaInstituicao", "salarios")), 0, ",", ".")."</td>
                <td style='border-bottom:solid black 1px;'>".number_format(floatval(valorArray($entidade, "tempoTotLeccionado", "salarios")), 0, ",", ".")."</td>
                <td style='border-bottom:solid black 1px;'>".number_format(floatval(valorArray($entidade, "pagamentoPorTempo", "salarios")), 0, ",", ".")."</td>

                <td style='border-bottom:solid black 1px;'>".number_format(floatval(valorArray($entidade, "totalSubsidios", "salarios")), 0, ",", ".")."</td>
                <td style='border-bottom:solid black 1px;'>".number_format(floatval(valorArray($entidade, "outrosDescontos", "salarios"))+floatval(valorArray($entidade, "segurancaSocial", "salarios"))+floatval(valorArray($entidade, "IRT", "salarios")), 0, ",", ".")."</td>


                <td style='border-bottom:solid black 1px;'>".number_format(floatval(valorArray($entidade, "salarioLiquido", "salarios")), 0, ",", ".")."</td>
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
                    <td style='border-bottom: solid black 2px;'>".number_format(floatval(valorArray($entidade, "salarioLiquido", "salarios")), 2, ",", ".")."</td>
                    <td style='border-bottom: solid black 2px;'>0,00</td>
                </tr>
               </table>

            <table style='width:45%; position:absolute; margin-top:-50px; margin-left:55%; border-spacing:0px; border-top:solid black 1px;'>
                <tr>
                    <td style='".$this->bolder."'>Total Líquido:</td>
                    <td style='".$this->text_right."'>".number_format(floatval(valorArray($entidade, "valorAuferidoNaInstituicao", "salarios"))+floatval(valorArray($entidade, "tempoTotLeccionado", "salarios"))*floatval(valorArray($entidade, "pagamentoPorTempo", "salarios")) , 2, ",", ".")."</td>
                </tr>
                <tr>
                    <td style='".$this->bolder."'>Subsídios:</td>
                    <td style='".$this->text_right."'>".number_format(floatval(valorArray($entidade, "totalSubsidios", "salarios")), 2, ",", ".")."</td>
                </tr>
                <tr>
                    <td style='".$this->bolder."'>IRT:</td>
                    <td style='".$this->text_right."'>".number_format(floatval(valorArray($entidade, "IRT", "salarios")), 2, ",", ".")."</td>
                </tr>
                <tr>
                    <td style='".$this->bolder."'>Segurança Social:</td>
                    <td style='".$this->text_right."'>".number_format(floatval(valorArray($entidade, "segurancaSocial", "salarios")), 2, ",", ".")."</td>
                </tr>
                <tr>
                    <td style='".$this->bolder." border-bottom:solid black 2px;'>Outros Descontos</td>
                    <td style='".$this->text_right." border-bottom:solid black 2px;'>".number_format(floatval(valorArray($entidade, "outrosDescontos", "salarios")), 2, ",", ".")."</td>
                </tr>
                <tr>
                    <td style='".$this->bolder."'>Total:</td>
                    <td style='".$this->text_right."'>".number_format(floatval(valorArray($entidade, "salarioLiquido", "salarios")), 2, ",", ".")."</td>
                </tr>
            </table>
        </div></body></html>";
            
            
           $this->exibir("", "Nota de Pagamento de Salário", "", valorArray($this->sobreEscolaLogada, "comprovativo"));
        }
    }
    new factura();
?>
