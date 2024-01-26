<?php 

    class notaCredito extends funcoesAuxiliares{

        public function notaCredito(){

            $recibo = $this->selectArray("payments", [], ["idPDocumento"=>$this->idPDocumento]);

            $factura = $this->selectArray("payments", [], ["identificacaoUnica"=>valorArray($recibo, "referenciaFactura")]);


            $array = $this->selectArray("alunosmatriculados", ["escola.idMatEscola", "escola.classeActualAluno", "escola.idMatCurso", "reconfirmacoes.idReconfEscola", "reconfirmacoes.idReconfAno", "reconfirmacoes.designacaoTurma"], ["idPMatricula"=>valorArray($recibo, "idPMatricula"), "escola.idMatEscola"=>$_SESSION['idEscolaLogada']], ["escola"]);

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
        
            if(valorArray($this->sobreEscolaLogada, "comprovativo")=="A6"){
                $this->html .="<p style='".$this->miniParagrafo.$this->maiuscula.$this->text_center.$this->bolder."'>".valorArray($this->sobreEscolaLogada, "nomeComercial")."</p>";
            }else{
                $src = $_SERVER['DOCUMENT_ROOT'].'/angoschool/Ficheiros/Escola_'.$_SESSION['idEscolaLogada'].'/Icones/'.valorArray($this->sobreEscolaLogada, "logoEscola");
                
                if(!file_exists($src) || valorArray($this->sobreUsuarioLogado, "logoEscola")==NULL || valorArray($this->sobreUsuarioLogado, "logoEscola")==""){
                  $src = $_SERVER['DOCUMENT_ROOT'].'/angoschool/icones/insignia.jpg';
                }

                $this->html .="<p style='".$this->miniParagrafo."'><img src='".$src."' style='with:45px; height:45px;'></p>
                <p style='".$this->miniParagrafo."'>".valorArray($this->sobreEscolaLogada, "nomeComercial")."</p>";
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
                <strong>Cliente: ".valorArray($recibo, "nomeCliente")."</strong><br>";
                if(valorArray($recibo, "nifCliente")==""){
                    $this->html .="<strong>Consumidor Final</strong>";    
                }else{
                    $this->html .="<strong>".valorArray($recibo, "nifCliente")."</strong>";
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
                    <td colspan='7' style='border-bottom:solid black 2px'><strong>Nota de Crédito n.º ".valorArray($recibo, "identificacaoUnica")."</strong></td>
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
                    <td style='border-bottom:solid black 2px'>".valorArray($recibo, "dataEmissao")."</td>
                    <td style='border-bottom:solid black 2px'></td>
                    <td style='border-bottom:solid black 2px'>".valorArray($recibo, "dataEmissao")."</td>
                    <td style='border-bottom:solid black 2px'></td>
                    <td style='border-bottom:solid black 2px'>".valorArray($recibo, "nifCliente")."</td>
                    <td style='border-bottom:solid black 2px'></td>
                    <td style='border-bottom:solid black 2px'>".valorArray($recibo, "referenciaFactura")."</td>
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
                    <td style='border-bottom: solid black 2px;'>".number_format(valorArray($recibo,"valorTotComImposto"), 2, ",", ".")."</td>
                    <td style='border-bottom: solid black 2px;'>0,00</td>
                </tr>
               </table>

            <table style='width:45%; position:absolute; margin-top:-50px; margin-left:55%; border-spacing:0px; border-top:solid black 1px;'>
                <tr>
                    <td style='".$this->bolder."'>Total Líquido:</td>
                    <td style='".$this->text_right."'>".number_format(valorArray($recibo,"valorTotComImposto"), 2, ",", ".")."</td>
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
                    <td style='".$this->text_right."'>".number_format(valorArray($recibo,"valorTotComImposto"), 2, ",", ".")."</td>
                </tr>

            </table>
           ";
            $this->html .="</div></body></html>";
            
           $this->exibir("", "Nota de Crédito - ".valorArray($recibo, "identificacaoUnica"), "", valorArray($this->sobreEscolaLogada, "comprovativo"));
        }

        private function nomePagamento($designacaoEmolumento, $referenciaPagamento, $codigoEmolumento){
            if($codigoEmolumento=="propinas"){
                return nomeMes($referenciaPagamento);
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
