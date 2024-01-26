<?php 
    class reciboPagamento extends funcoesAuxiliares{
              
        private $idPMatricula = "";
        private $tipoRecibo="";
        private $pagamento="";

        function __construct(){
            parent::__construct("Rel-Recibo de Pagamento");
            $this->idPHistoricoConta = isset($_GET["idPHistoricoConta"])?$_GET["idPHistoricoConta"]:null;
            $this->idPMatricula = isset($_GET["idPMatricula"])?$_GET["idPMatricula"]:"";

            $this->pagamento = $this->selectArray("alunosmatriculados", ["pagamentos.idHistoricoFuncionario", "pagamentos.referenciaPagamento","reconfirmacoes.classeReconfirmacao", "pagamentos.idHistoricoAno", "escola.classeActualAluno", "escola.idMatCurso", "pagamentos.dataPagamento", "nomeAluno", "numeroInterno", "sexoAluno", "pagamentos.idHistoricoAno", "pagamentos.horaPagamento"], ["idPMatricula"=>$this->idPMatricula, "pagamentos.idPHistoricoConta"=>$this->idPHistoricoConta, "pagamentos.idHistoricoEscola"=>$_SESSION["idEscolaLogada"], "pagamentos.codigoEmolumento"=>array('$ne'=>"propinas")], ["pagamentos"]);
            
            $this->pagamento = $this->anexarTabela2($this->pagamento, "entidadesprimaria", "pagamentos", "idPEntidade", "idHistoricoFuncionario");

            $this->sobreAluno(valorArray($this->pagamento, "idPMatricula"), ["pagamentos.idHistoricoFuncionario", "pagamentos.referenciaPagamento", "reconfirmacoes.idReconfEscola", "reconfirmacoes.idReconfAno", "reconfirmacoes.classeReconfirmacao", "pagamentos.idHistoricoAno", "reconfirmacoes.nomeTurma", "reconfirmacoes.designacaoTurma", "escola.classeActualAluno", "escola.idMatCurso", "pagamentos.dataPagamento", "nomeAluno", "numeroInterno", "sexoAluno", "pagamentos.precoInicial", "pagamentos.designacaoEmolumento", "pagamentos.precoMulta", "pagamentos.precoPago", "pagamentos.idHistoricoEscola", "pagamentos.horaPagamento", "pagamentos.codigoEmolumento", "biAluno"]);

            $this->reconfirmacao = listarItensObjecto($this->sobreAluno, "reconfirmacoes", ["idReconfAno=".valorArray($this->pagamento, "idHistoricoAno", "pagamentos"), "idReconfEscola=".valorArray($this->pagamento, "idHistoricoEscola", "pagamentos"), "idMatCurso=".valorArray($this->sobreAluno, "idMatCurso", "escola")]);

            $this->pagamentos = listarItensObjecto($this->sobreAluno, "pagamentos", ["idHistoricoAno=".valorArray($this->pagamento, "idHistoricoAno", "pagamentos"), "idHistoricoEscola=".valorArray($this->pagamento, "idHistoricoEscola", "pagamentos"), "codigoEmolumento!=propinas"]);


            $this->idPAno = valorArray($this->pagamento, "idHistoricoAno", "pagamentos");
            $this->classe = valorArray($this->reconfirmacao, "classeReconfirmacao");
            
            if($this->classe==NULL || $this->classe==""){
                $this->classe=valorArray($this->sobreAluno, "classeActualAluno", "escola");
            }
            if($this->classe==120){
                $this->classeEmExtenso = "Finalista";
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
           <div style='border:solid black 2px; padding:5px;'>".$this->cabecalho("sim", "text-align:left;")."
                <p style='".$this->bolder.$this->text_center.$this->miniParagrafo." margin-top:-10px;'>RECIBO DE PAGAMENTOS</p>
                <p style='".$this->bolder.$this->text_center." font-size:22pt;margin-top:10px;margin-bottom:0px;'>".$this->numAno."</p>
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
                        <tr><td style='".$this->text_center."'><strong>N.º</strong></td><td><strong>Referências</strong></td><td><strong>Valor</strong></td><td><strong>Multa</strong></td><td><strong>Total (Kz)</strong></td><td><strong>Data</strong></td></tr>";
 
                    $i=0;
                    $totalKz=0;
                    foreach($this->pagamentos as $p){
                        $i++;
                        $totalKz +=floatval($p["precoPago"]);
                        $this->html .="<tr><td style='".$this->text_center."'>".completarNumero($i)."</td><td>".$this->nomePagamento(nelson($p, "designacaoEmolumento"), nelson($p, "referenciaPagamento"), nelson($p, "codigoEmolumento"))."</td><td>".number_format(floatval($p["precoInicial"]), 2, ",", ".")."</td><td>".number_format(floatval($p["precoMulta"]), 2, ",", ".")."</td><td>".number_format(floatval($p["precoPago"]), 2, ",", ".")."</td><td>".converterData($p["dataPagamento"])." | ".$p["horaPagamento"]."</td></tr></tr>";
                    }
                    $this->html .="<tr><td style='".$this->text_right." border:none;' colspan='4'>Total</td><td><strong>".number_format($totalKz, 2, ",", ".")."</strong></td></tr></table>

                    ".$this->porAssinatura("O(a) Funcionário(a)", valorArray($this->pagamento, "nomeEntidade"))."
                    <br/>
                </div>
               
            </div>";

           $this->html .="</body></html>";
            
            $this->exibir("", "Recibo de ".dataExtensa(valorArray($this->pagamento, "dataPagamento"))."  - ".valorArray($this->pagamento, "numeroInterno"));
        }

        private function nomePagamento($designacaoEmolumento, $referenciaPagamento, $codigoEmolumento){

            if($codigoEmolumento=="boletim"){
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