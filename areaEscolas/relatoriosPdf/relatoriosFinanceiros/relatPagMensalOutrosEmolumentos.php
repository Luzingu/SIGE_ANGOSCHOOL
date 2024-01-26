<?php if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');

    class lista extends funcoesAuxiliares{

        function __construct(){
            parent::__construct("Relatório Mensal de Pagamentos de Outros Emolumentos");

            $this->mesPagamento = isset($_GET["mesPagamento"])?$_GET["mesPagamento"]:"";
            $this->anoCivil = isset($_GET["anoCivil"])?$_GET["anoCivil"]:"";

             $this->html="<html style='margin-left:30px;margin-right:30px;'>
            <head>
                <title>Lista de Pagamentos</title>
            </head>
            <body>";
            $this->visualizar();
        }

         private function visualizar(){

                $this->html .=$this->fundoDocumento("../../../").$this->cabecalho();

                $this->html .="
                <p style='".$this->text_center.$this->maiuscula.$this->bolder."'>RESUMO DE PAGAMENTOS DE OUTROS EMOLUMENTOS DO<br>MÊS DE ".nomeMes($this->mesPagamento)." -  ".$this->anoCivil."</p>";

                $this->html .="
                <table style='".$this->tabela." width:100%;'>
                    <tr style='".$this->corDanger.$this->bolder.$this->text_center.$this->maiuscula."'>
                        <td style='".$this->border()."'>N.º</td>
                        <td style='".$this->border()."'>Nome Completo</td>
                        <td style='".$this->border()."'>Curso</td>
                        <td style='".$this->border()."'>Classe</td>
                        <td style='".$this->border()."'>Turma</td>
                        <td style='".$this->border()."'>Referência</td>
                        <td style='".$this->border()."'>Valor (Kz)</td>
                    </tr>";
                $totalAcumulado=0;
                $i=0;
                $totalAcumulado=0;

                $this->todosPagamentos = $this->selectArray("alunosmatriculados", ["idPMatricula", "nomeAluno", "pagamentos.precoPago", "pagamentos.referenciaPagamento", "pagamentos.idHistoricoAno", "pagamentos.designacaoEmolumento", "pagamentos.codigoEmolumento", "escola.idMatEscola", "escola.idMatCurso", "reconfirmacoes.classeReconfirmacao", "reconfirmacoes.designacaoTurma", "reconfirmacoes.idReconfAno", "reconfirmacoes.idReconfEscola"], ["pagamentos.idHistoricoEscola"=>$_SESSION['idEscolaLogada'], "pagamentos.dataPagamento"=>new \MongoDB\BSON\Regex($this->anoCivil."-".completarNumero($this->mesPagamento)."-"), "pagamentos.idTipoEmolumento"=>array('$ne'=>1)], ["pagamentos"], "", [], array("nomeAluno"=>1));

                foreach(distinct2($this->todosPagamentos, "idPMatricula") as $idPMatricula){
                    $i++;
                    $totLonda = $this->totalPago($idPMatricula);
                    $totalAcumulado +=$totLonda;
                    $this->html .="<tr>
                        <td style='".$this->border().$this->text_center."'>".completarNumero($i)."</td>
                        <td style='".$this->border()."'>".$this->retornarNome($idPMatricula)."</td>
                        <td style='".$this->border().$this->text_center."'>".$this->abrevCurso."</td>
                        <td style='".$this->border().$this->text_center."'>".$this->classeReconfirmacao."</td>
                        <td style='".$this->border().$this->text_center."'>".$this->designacaoTurma."</td>
                        <td style='".$this->border()."'>".$this->retornarMeses($idPMatricula)."</td>
                        <td style='".$this->border().$this->text_center."'>".number_format($totLonda, 2, ",", ".")."</td>
                    </tr>";
                }
                $this->html .="<tr>
                        <td style='".$this->border().$this->text_center."' colspan='6'>Total</td>
                        <td style='".$this->border().$this->text_center."'>".number_format($totalAcumulado, 2, ",", ".")."</td>
                    </tr>";
                 $this->html .="

                </table><p style='".$this->text_center."'>".$this->rodape().".</p>
                ".$this->porAssinatura("O(a) Responsável", "", "", 25);
            
            $this->exibir("", "Relatório Mensal de Pagamentos de Outros Emolumentos-".nomeMes($this->mesPagamento));
        }
        private function retornarMeses($idPMatricula){
            $retorno="";
            foreach($this->todosPagamentos as $t){
                if($t["idPMatricula"]==$idPMatricula){
                    if($retorno!=""){
                        $retorno .=", ";
                    }
                    $retorno .=$this->nomePagamento($t["pagamentos"]["designacaoEmolumento"], $t["pagamentos"]["referenciaPagamento"], $t["pagamentos"]["codigoEmolumento"]);
                }
            } 
            return $retorno;
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
        
        private function totalPago($idPMatricula){
            $retorno=0;
            foreach($this->todosPagamentos as $t){
                if($t["idPMatricula"]==$idPMatricula){
                    $retorno +=$t["pagamentos"]["precoPago"];
                }
            }
            return $retorno;
        }

        private function retornarNome($idPMatricula){
            foreach($this->todosPagamentos as $p){
                if($p["idPMatricula"]==$idPMatricula){

                   $this->abrevCurso =  $this->selectUmElemento("nomecursos", "abrevCurso", ["idPNomeCurso"=>valorArray(listarItensObjecto($p, "escola", ["idMatEscola=".$_SESSION['idEscolaLogada']]), "idMatCurso")]);

                   $reconfirmacao = listarItensObjecto($p, "reconfirmacoes", ["idReconfAno=".$p["pagamentos"]["idHistoricoAno"], "idReconfEscola=".$_SESSION['idEscolaLogada']]);
                   $this->classeReconfirmacao = valorArray($reconfirmacao, "classeReconfirmacao");
                   $this->designacaoTurma = valorArray($reconfirmacao, "designacaoTurma");

                    return $p["nomeAluno"];
                    break;
                }
            }   
        }


    }

new lista(__DIR__);
    
    
  
?>