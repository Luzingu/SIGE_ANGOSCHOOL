<?php 

    class facturaFR extends funcoesAuxiliares{

        public function facturaFR(){

            $factura = $this->selectArray("payments", [], ["idPDocumento"=>$this->idPDocumento]);

            $array = $this->selectArray("alunosmatriculados", ["escola.idMatEscola", "escola.classeActualAluno", "escola.idMatCurso", "reconfirmacoes.idReconfEscola", "reconfirmacoes.idReconfAno", "reconfirmacoes.designacaoTurma"], ["idPMatricula"=>valorArray($factura, "idPMatricula"), "escola.idMatEscola"=>$_SESSION['idEscolaLogada']], ["escola"]);
            $arrayTurma = listarItensObjecto($array, "reconfirmacoes", ["idReconfEscola=".$_SESSION['idEscolaLogada'], "idReconfAno=".$this->idAnoActual]);

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
                        margin-top:0px;
                        line-height:15px;
                        font-size:8pt;";
                    }else if(valorArray($this->sobreEscolaLogada, "comprovativo")=="A5"){
                        $this->html .="margin:10px;
                        font-size:10pt;";
                    }else{
                        $this->html .="margin:50px;";
                    }
                $this->html .="
                    }

                    body{
                        ";
                    if(valorArray($this->sobreEscolaLogada, "comprovativo")=="A6"){
                        $this->html .="width:250px;";
                    }else if(valorArray($this->sobreEscolaLogada, "comprovativo")=="A5"){
                        
                    }else{
                        
                    }
                $this->html .="
                    }
                </style>
            </head>
           <body>";

           if(valorArray($factura, "estadoDocumento")=="A"){
                if(valorArray($this->sobreEscolaLogada, "comprovativo")=="A6"){
                    $this->html .="<img src='".$_SERVER['DOCUMENT_ROOT']."/angoschool/icones/anulado.png' style='position:absolute; margin-top:210px;  opacity:0.3; margin-left:0px; width:350px;transform: rotate(45deg);'>";

                }else if(valorArray($this->sobreEscolaLogada, "comprovativo")=="A5"){
                    $this->html .="<img src='".$_SERVER['DOCUMENT_ROOT']."/angoschool/icones/anulado.png' style='position:absolute; margin-top:270px;  opacity:0.3; margin-left:70px; width:350px;transform: rotate(45deg);'>";
                }else{
                    $this->html .="<img src='".$_SERVER['DOCUMENT_ROOT']."/angoschool/icones/anulado.png' style='position:absolute; margin-top:400px;  opacity:0.3; margin-left:130px; width:450px;transform: rotate(45deg);'>";
                }
           }
            
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
                $this->html .="<div style='margin-top:-75pt; position:absolute; margin-left:400px;'>";
            }

            $this->html .="
                Exmo.(s) Sr.(s)<br>
                <strong>Cliente: ".valorArray($factura, "nomeCliente")."</strong><br>";
                if(valorArray($factura, "nifCliente")==""){
                    $this->html .="<strong>Consumidor Final</strong>";    
                }else{
                    $this->html .="<strong>".valorArray($factura, "nifCliente")."</strong>";
                }
                $this->html .="</p>
            </div>";
            
            if(valorArray($this->sobreEscolaLogada, "comprovativo")=="A6"){
                $this->html .="<table style='width:100%; margin-top:10px; border-spacing:0px;'>";
            }else{
                $this->html .="<table style='width:100%; margin-top:30px; border-spacing:0px;'>";
            }
            $this->html .="            
                <tr>
                    <td colspan='7' style='border-bottom:solid black 2px'><strong>Recibo n.º ".valorArray($factura, "identificacaoUnica")."</strong></td>
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
                    <td style='border-bottom:solid black 2px'>".valorArray($factura, "dataEmissao")."</td>
                    <td style='border-bottom:solid black 2px'></td>
                    <td style='border-bottom:solid black 2px'>".valorArray($factura, "dataEmissao")."</td>
                    <td style='border-bottom:solid black 2px'></td>
                    <td style='border-bottom:solid black 2px'>".valorArray($factura, "nifCliente")."</td>
                    <td style='border-bottom:solid black 2px'></td>
                    <td style='border-bottom:solid black 2px'>".valorArray($factura, "identificacaoUnica")."</td>
                </tr>
            </table>

            <table style='width:100%; margin-top:15px; border-spacing:0'>
                <tr>
                    <td style='".$this->bolder.$this->text_center." border-bottom:solid black 1px;'>Código</td>
                    <td style='".$this->bolder." border-bottom:solid black 1px;'>Descrição</td>
                    <td style='".$this->bolder." border-bottom:solid black 1px;'>P. Uni.</td>
                    <td style='".$this->bolder.$this->text_center." border-bottom:solid black 1px;'>Qtd.</td>
                    <td style='".$this->bolder." border-bottom:solid black 1px;'>IVA</td>
                    <td style='".$this->bolder." border-bottom:solid black 1px;'>Total</td>
                </tr>";
            foreach(listarItensObjecto($factura, "itens") as $item){
                $this->html .="<tr>
                    <td style='border-bottom:solid black 1px;".$this->text_center."'>".$item["idProduto"]."</td>
                    <td style='border-bottom:solid black 1px;'>".$this->nomePagamento(valorArray($item,"descricaoProduto"), valorArray($item,"referenciaPagamento"), valorArray($item, "codigoProduto"))."</td>
                    <td style='border-bottom:solid black 1px;'>".number_format(valorArray($item,"precoUnitario"), 2, ",", ".")."</td>
                    <td style='".$this->text_center."border-bottom:solid black 1px;'>".valorArray($item,"quantidade")."</td>
                    <td style='border-bottom:solid black 1px;'>0,00</td>
                    <td style='border-bottom:solid black 1px;'>".number_format(valorArray($item,"precoUnitario")*valorArray($item,"quantidade"), 2, ",", ".")."</td>
                </tr>";
            }

           $this->html .="</table>

               <table style='width:45%; margin-top:5px; border-spacing:0px;'>
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
                    <td style='border-bottom: solid black 2px;'>".number_format(valorArray($factura,"valorTotComImposto"), 2, ",", ".")."</td>
                    <td style='border-bottom: solid black 2px;'>0,00</td>
                </tr>
               </table>";
            $marcia ="width:45%; position:absolute; margin-top:-50px; margin-left:55%; border-spacing:0px; border-top:solid black 1px;";

            if(valorArray($this->sobreEscolaLogada, "comprovativo")=="A6"){
                $marcia ="border-spacing:0px; border-top:solid black 1px; margin-top:10px; margin-left:110px; width:140px;";
            }
            $this->html .="<table style='".$marcia."'>
                <tr>
                    <td style='".$this->bolder."'>Total Líquido:</td>
                    <td style='".$this->text_right."'>".number_format(valorArray($factura,"valorTotComImposto"), 2, ",", ".")."</td>
                </tr>
                <tr>
                    <td style='".$this->bolder."'>Total Desconto:</td>
                    <td style='".$this->text_right."'>0,00</td>
                </tr>
                <tr>
                    <td style='".$this->bolder." border-bottom:solid black 2px;'>Total Imposto:</td>
                    <td style='".$this->text_right." border-bottom:solid black 2px;'>0,00</td>
                </tr>
                <tr>
                    <td style='".$this->bolder."'>Total:</td>
                    <td style='".$this->text_right."'>".number_format(valorArray($factura,"valorTotComImposto"), 2, ",", ".")."</td>
                </tr>

            </table>
           ";
            $this->html .="</div></body></html>";
            
           $this->exibir("", "Recibo de Pagamento - ".valorArray($factura, "identificacaoUnica"), "", valorArray($this->sobreEscolaLogada, "comprovativo"));
        }

        private function nomePagamento($designacaoEmolumento, $referenciaPagamento, $codigoEmolumento){
            if($codigoEmolumento=="propinas"){
                if($referenciaPagamento=="Divida"){
                    return "Divida";
                }else{
                    return nomeMes($referenciaPagamento);
                }
            }else if($designacaoEmolumento=="matricula"){
                return "Matricula";
            }else if($designacaoEmolumento=="inscricao"){
                return "Inscrição";
            }else if($codigoEmolumento=="boletim"){
                if($referenciaPagamento=="IV"){
                  $designacaoEmolumento ="Boletim Final";
                }else{
                  $designacaoEmolumento ="Boletim do ".$referenciaPagamento." Trimestre";
                }
            }else if($codigoEmolumento=="declaracao"){
                $designacaoEmolumento =retornarNomeDocumento ($referenciaPagamento);
            }
            return $designacaoEmolumento;
            
        }
    }
?>
