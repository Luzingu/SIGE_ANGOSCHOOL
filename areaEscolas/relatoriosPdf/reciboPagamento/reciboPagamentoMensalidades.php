<?php 
    class reciboPagamentoMensalidade extends funcoesAuxiliares{

        function __construct(){
            parent::__construct("Rel-Recibo de Pagamento de Propinas");
            $this->idPHistoricoConta = isset($_GET["idPHistoricoConta"])?$_GET["idPHistoricoConta"]:null;
            $this->idPMatricula = isset($_GET["idPMatricula"])?$_GET["idPMatricula"]:null;

            $this->pagamento = $this->selectArray("alunosmatriculados", ["pagamentos.idHistoricoFuncionario", "pagamentos.referenciaPagamento","reconfirmacoes.classeReconfirmacao", "pagamentos.idHistoricoAno", "escola.classeActualAluno", "escola.idMatCurso", "pagamentos.dataPagamento", "pagamentos.horaPagamento", "nomeAluno", "numeroInterno", "sexoAluno", "pagamentos.idHistoricoAno"], ["idPMatricula"=>$this->idPMatricula, "pagamentos.idPHistoricoConta"=>$this->idPHistoricoConta, "pagamentos.idHistoricoEscola"=>$_SESSION["idEscolaLogada"], "pagamentos.codigoEmolumento"=>"propinas"], ["pagamentos"]);
            
            $this->pagamento = $this->anexarTabela2($this->pagamento, "entidadesprimaria", "pagamentos", "idPEntidade", "idHistoricoFuncionario");

            $this->sobreAluno(valorArray($this->pagamento, "idPMatricula"), ["pagamentos.idHistoricoFuncionario", "pagamentos.referenciaPagamento", "reconfirmacoes.idReconfEscola", "reconfirmacoes.idReconfAno", "reconfirmacoes.classeReconfirmacao", "pagamentos.idHistoricoAno", "reconfirmacoes.nomeTurma", "reconfirmacoes.designacaoTurma", "escola.classeActualAluno", "escola.idMatCurso", "pagamentos.dataPagamento", "nomeAluno", "numeroInterno", "sexoAluno", "pagamentos.precoInicial", "pagamentos.precoMulta", "pagamentos.precoPago", "pagamentos.idHistoricoEscola", "pagamentos.horaPagamento", "pagamentos.codigoEmolumento", "biAluno"]);

            $this->reconfirmacao = listarItensObjecto($this->sobreAluno, "reconfirmacoes", ["idReconfAno=".valorArray($this->pagamento, "idHistoricoAno", "pagamentos"), "idReconfEscola=".valorArray($this->pagamento, "idHistoricoEscola", "pagamentos"), "idMatCurso=".valorArray($this->sobreAluno, "idMatCurso", "escola")]);

            $this->pagamentos = listarItensObjecto($this->sobreAluno, "pagamentos", ["idHistoricoAno=".valorArray($this->pagamento, "idHistoricoAno", "pagamentos"), "idHistoricoEscola=".valorArray($this->pagamento, "idHistoricoEscola", "pagamentos"), "codigoEmolumento=propinas"]);


            $this->idPAno = valorArray($this->pagamento, "idHistoricoAno", "pagamentos");
            $this->classe = valorArray($this->reconfirmacao, "classeReconfirmacao");
            
            if($this->classe==NULL || $this->classe==""){
                $this->classe=valorArray($this->sobreAluno, "classeActualAluno", "escola");
            }
            
            if($this->classe==120){
                $this->classeEmExtenso = "Técnico Médio";
            }else{
                $this->classeEmExtenso = classeExtensa($this, valorArray($this->sobreAluno, "idMatCurso", "escola"), $this->classe);
            }

            $this->idPCurso = valorArray($this->sobreAluno, "idMatCurso", "escola");
            $this->numAno();
            $this->nomeCurso();
            $this->numAno();
            $this->recibo();  
        }

         private function recibo(){

            $gerenciador = $this->selectCondClasseCurso("array", "listaturmas", [], ["classe"=>$this->classe, "nomeTurma"=>valorArray($this->reconfirmacao, "nomeTurma"), "idListaAno"=>$this->idPAno, "idPEscola"=>$_SESSION["idEscolaLogada"]], $this->classe,  ["idPNomeCurso"=>$this->idPCurso]);
           $this->html .="
           <html style='margin:10px;'>
            <head>
                <title>Comprovatico de Pagamentos</title>
                <style>
                    .tabela tr td{
                        border-left: solid black 1px;
                        border-bottom: solid black 1px;
                        padding:4px;

                    }
                    .tabela{
                        border-spacing:5px;
                    }
                </style>
            </head>
           <body>".$this->fundoDocumento("../../../")."
           <div style='border:solid black 2px; padding:5px; height:170px;'>
            <div style='padding-top:20px;'>";
            
            if(valorArray($this->sobreUsuarioLogado, "privacidadeEscola")=="Pública"){
                $this->html .="<img src='../../../../angoschool/icones/insignia.jpg' style='height:120px; width:120px;'></div><div style='margin-left:125px; margin-top:-200px;'>
                <p style='".$this->miniParagrafo."'>REPÚBLICA DE ANGOLA</p>";
            }else{
                $src = '../../../Ficheiros/Escola_'.$_SESSION['idEscolaLogada'].'/Icones/'.valorArray($this->sobreUsuarioLogado, "logoEscola");
                if(!file_exists($src) || valorArray($this->sobreUsuarioLogado, "logoEscola")==NULL || valorArray($this->sobreUsuarioLogado, "logoEscola")==""){
                  $src = '../../../icones/logoAngoSchool1.png';
                }
                $this->html .="<img src='".$src."' style='height:120px; width:120px;'></div><div style='margin-left:125px; margin-top:-200px;'>";
            }
            
            
                
            $this->html .="<p style='".$this->miniParagrafo."'>GOVERNO PROVINCIAL DO ZAIRE</p>
                <p style='".$this->miniParagrafo."'>GABINETE PROVINCIAL DA EDUCAÇÃO</p>
                <p style='".$this->maiuscula.$this->miniParagrafo."'>";

              if($_SESSION["idEscolaLogada"]==17 && $this->tipoCurso=="geral"){
                
                    $this->html .="Liceu do Tuku<br/>Mbanza Kongo";
                
              }else{
                    $this->html .=valorArray($this->sobreUsuarioLogado, "tituloEscola");
              }
              $this->html .="</p>
              <p style='".$this->maiuscula.$this->miniParagrafo."'>COMISSÃO DOS PAIS E ENCARREGADOS DE EDUCAÇÃO</p>
                <p style='".$this->bolder.$this->text_center."margin-bottom:-20px;'>RECIBO DE PAGAMENTOS DE PROPINAS</p>
                
                <p style='".$this->bolder.$this->miniParagrafo.$this->text_center." font-size:20pt;'>".$this->numAno."</p>
            </div>
           </div>
           <div style='border:solid black 2px; margin-top:10px; background-color: rgba(0, 0, 0, 0.5); color:white;".$this->text_center." padding-top:5px;'>
            <strong>Dados do Aluno</strong>

            <div style='border:solid black 2px; padding:5px; background-color:white;color:black; margin-top:5px; margin-bottom:5px; border-left:none; border-right:none;'>
                <table class='tabela' style='width:100%; '>
                    <tr>
                        <td style='".$this->text_right."'>N.º Interno:</td><td colspan='2'><strong>".valorArray($this->pagamento, "numeroInterno")."</strong></td>

                        <td style='".$this->text_right."'>Data Pagamento:</td><td><strong>".valorArray($this->pagamento, "dataPagamento", "pagamentos")." ".valorArray($this->pagamento, "horaPagamento", "pagamentos")."</strong></td>
                    </tr>
                    <tr>
                        <td style='".$this->text_right."'>Nome Completo:</td>
                        <td colspan='5'><strong>".valorArray($this->pagamento, "nomeAluno")."</strong></td>
                    </tr>
                    <tr>
                        <td style='".$this->text_right."'>Sexo:</td>
                        <td><strong>".generoExtenso(valorArray($this->pagamento, "sexoAluno"))."</strong></td>
                        <td style='".$this->text_right."'>N.º BI:</td>
                        <td colspan='2'><strong>".valorArray($this->pagamento, "biAluno")."</strong></td>
                    </tr>

                </table>
            </div>
            <strong>Dados Académicos</strong>

            <div style='border:solid black 2px; padding:5px; background-color:white;color:black;margin-top:5px; margin-bottom:5px; border-left:none; border-right:none;'>
                    <table class='tabela' style='width:100%; '>
                        <tr>
                            <td style='".$this->text_right."'>Classe:</td><td><strong>".$this->classeEmExtenso."</strong></td><td style='".$this->text_right."'>Opção:</td><td colspan='3'><strong>".$this->nomeCurso."</strong></td>
                        </tr>
                        <tr>
                            <td style='".$this->text_right."'>Turma:</td><td><strong>".valorArray($gerenciador, "designacaoTurma")."</strong></td><td style='".$this->text_right."'>Período:</td><td><strong>".valorArray($gerenciador, "periodoT")."</strong></td><td style='".$this->text_right."'>Sala n.º:</td><td><strong>".completarNumero(valorArray($gerenciador, "numeroSalaTurma"))."</strong></td>
                        </tr>
                    </table>
                </div>
                <strong>Pagamentos</strong>
                <div style='border:solid black 2px; padding:5px; background-color:white;color:black; margin-top:5px; border-left:none; border-right:none; border-bottom:none; '>

                    <table class='tabela' style='width:100%;'>
                        <tr><td style='".$this->text_center."'><strong>N.º</strong></td><td><strong>Meses Pagos</strong></td><td><strong>Valor</strong></td><td><strong>Multa</strong></td><td><strong>Total (Kz)</strong></td><td><strong>Data</strong></td></tr>";

                    $i=0;
                    $totalKz=0;
                    foreach($this->pagamentos as $p){
                        $i++;
                        $totalKz +=$p["precoPago"];
                        $this->html .="<tr><td style='".$this->text_center."'>".completarNumero($i)."</td><td>".nomeMes($p["referenciaPagamento"])."</td><td>".number_format((double)$p["precoInicial"], 2, ",", ".")."</td><td>".number_format((double)$p["precoMulta"], 2, ",", ".")."</td><td>".number_format((double)$p["precoPago"], 2, ",", ".")."</td><td>".converterData($p["dataPagamento"])." | ".$p["horaPagamento"]."</td></tr>";
                    } 
                    $this->html .="<tr><td style='".$this->text_right."border:none;' colspan='4'>Total</td><td><strong>".number_format($totalKz, 2, ",", ".")."</strong></td></tr></table>

                    ".$this->porAssinatura("O(a) Funcionário(a)", valorArray($this->pagamento, "nomeEntidade"))."<br/>

                </div>
               
            </div></body></html>";
            
           $this->exibir("", "Recibo de ".dataExtensa(valorArray($this->pagamento, "dataPagamento"))."  - ".valorArray($this->pagamento, "numeroInterno"));
        }

        private function nomePagamento($codigoEmolumento, $referenciaPagamento){
            return nomeMes($referenciaPagamento);
        }
    }
?>