<?php if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');

    class lista extends funcoesAuxiliares{

        function __construct($caminhoAbsoluto){
            parent::__construct("Rel-Lista dos Alunos que já efectuaram pagamentos");

            $this->dataHistorico = isset($_GET["dataHistorico"])?$_GET["dataHistorico"]:"";

             $this->html="<html style='margin-left:30px;margin-right:30px;'>
            <head>
                <title>Rel. de Pag. de Propinas</title>
            </head>
            <body>";
            if($this->verificacaoAcesso->verificarAcesso("", ["relatorioMensal77"], [], "")){
                $this->visualizar();
            }else{
                $this->negarAcesso();
            }
            
        }

         private function visualizar(){

                $this->html .=$this->fundoDocumento("../../../").$this->cabecalho();
                if(valorArray($this->sobreUsuarioLogado, "privacidadeEscola")=="Pública"){
                    $this->html .="<p style='".$this->text_center.$this->maiuscula.$this->bolder." margin-top:-10px;'>COMISSÃO DE PAIS E ENCARREGADOS DE EDUCAÇÃO</P>";
                }
                
                $this->html .="
                <p style='".$this->text_center.$this->maiuscula.$this->bolder."'>RESUMO DE PAGAMENTOS DE ";
                if(valorArray($this->sobreUsuarioLogado, "privacidadeEscola")=="Privada"){
                    $this->html .="PROPINAS";
                }else{
                    $this->html .="COMPARTICIPAÇÕES";
                }
                $this->html .=" -  ".dataExtensa($this->dataHistorico)."</p>
                ";

                $this->html .="
                <table style='".$this->tabela." width:100%; font-size:11pt;'>
                <tr style='".$this->corDanger.$this->bolder.$this->text_center.$this->maiuscula."'>
                    <td style='".$this->border()."'>N.º</td>
                    <td style='".$this->border()."width:160px;'>Nome Completo</td>
                    <td style='".$this->border()."'>Curso</td>
                    <td style='".$this->border()."'>Classe</td>
                    <td style='".$this->border()."'>Turma</td>
                    <td style='".$this->border()."'>Meses</td>
                    <td style='".$this->border()."'>Valor(Kz)</td>
                </tr>";
                $totalAcumulado=0;
                $i=0;
                $totalAcumulado=0;

                $this->todosPagamentos = $this->selectArray("alunosmatriculados", ["idPMatricula", "nomeAluno", "pagamentos.precoPago", "pagamentos.referenciaPagamento", "pagamentos.idHistoricoAno", "pagamentos.designacaoEmolumento", "pagamentos.codigoEmolumento", "escola.idMatEscola", "escola.idMatCurso", "reconfirmacoes.classeReconfirmacao", "reconfirmacoes.designacaoTurma", "reconfirmacoes.idReconfAno", "reconfirmacoes.idReconfEscola"], ["pagamentos.idHistoricoEscola"=>$_SESSION['idEscolaLogada'], "pagamentos.dataPagamento"=>$this->dataHistorico, "pagamentos.codigoEmolumento"=>"propinas"], ["pagamentos"], "", [], array("nomeAluno"=>1));
                
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

                </table><p>".$this->rodape().".</p>
                ".$this->porAssinatura("O(a) Responsável", "", "", 25);
            
            $this->exibir("", "Relatório de Pagamentos de Propinas -".$this->numAno);
        }
        private function retornarMeses($idPMatricula){
            $retorno="";
            foreach($this->todosPagamentos as $t){
                if($t["idPMatricula"]==$idPMatricula){
                    if($retorno!=""){
                        $retorno .=", ";
                    }
                    $retorno .=nomeMes($t["pagamentos"]["referenciaPagamento"]);
                }
            }
            return $retorno;
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