<?php if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');

    class lista extends funcoesAuxiliares{

        function __construct($caminhoAbsoluto){
            parent::__construct("Rel-Lista dos Alunos que já efectuaram pagamentos");

            $this->idPCurso = isset($_GET["idPNomeCurso"])?$_GET["idPNomeCurso"]:"";
            $this->idPAno = isset($_GET["idPAno"])?$_GET["idPAno"]:"";
            $this->mes = isset($_GET["mes"])?$_GET["mes"]:"";
            $this->numAno();
            $this->nomeCurso();

             $this->html="<html style='margin-left:60px;margin-right:60px;'>
            <head>
                <title>Lista de Pagamentos</title>
            </head>
            <body>".$this->fundoDocumento("../../../");
            if($this->verificacaoAcesso->verificarAcesso("", ["alunosJaEfectuaramPagamentos77"], [], "")){
                $this->visualizar();
            }else{
                $this->negarAcesso();
            }
            
        }

         private function visualizar(){
            $this->html .=$this->cabecalho()."
            <p style='".$this->text_center.$this->maiuscula.$this->bolder."'>LISTA GERAL DE PAGAMENTOS DE<br>";
            if(valorArray($this->sobreUsuarioLogado, "privacidadeEscola")=="Privada"){
                $this->html .="PROPINAS";
            }else{
                $this->html .="COMPARTICIPAÇÕES";
            }
            $this->html .=" - ".$this->numAno."</p>
            CURSO: <strong style='".$this->maiuscula."'>".$this->nomeCurso."</strong><br>
            <table style='".$this->tabela." width:100%;'>
                <tr style='".$this->corDanger.$this->bolder.$this->text_center.$this->maiuscula."'>
                <td style='".$this->border()."' rowspan='2'>N.º</td>
                <td style='".$this->border()."' rowspan='2'>Nome Completo</td>
                <td style='".$this->border()."' rowspan='2'>Classe</td>
                <td style='".$this->border()."' rowspan='2'>Turma</td>
                <td style='".$this->border()."' colspan='2'>Meses Pagos</td>
                </tr>
                <tr style='".$this->corDanger.$this->bolder.$this->text_center."'>
                    <td style='".$this->border()."'>N.º</td>
                    <td style='".$this->border()."'>KZ</td>
                </tr>
                ";
            $this->alunos = $this->selectArray("alunosmatriculados", ["pagamentos.idHistoricoEscola", "pagamentos.codigoEmolumento", "pagamentos.precoPago", "reconfirmacoes.classeReconfirmacao", "reconfirmacoes.designacaoTurma", "nomeAluno", "numeroInterno", "pagamentos.idHistoricoAno"], ["reconfirmacoes.idReconfAno"=>$this->idPAno, "reconfirmacoes.idReconfEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.estadoReconfirmacao"=>"A", "reconfirmacoes.idMatCurso"=>$this->idPCurso], ["reconfirmacoes"], "", [], array("nomeAluno"=>1));

            $totalAcumulado=0;
            $diferencaAcumulada=0;
            $i=0;
            foreach($this->alunos as $a){

                $listaMeses = listarItensObjecto($a, "pagamentos", ["idHistoricoAno=".$this->idPAno, "idHistoricoEscola=".$_SESSION['idEscolaLogada'], "codigoEmolumento=propinas"]);

                $total = count($listaMeses);
                $totalKz=0;
                foreach($listaMeses as $b){
                    $totalKz +=$b["precoPago"];
                }
                $totalAcumulado += $totalKz;

               $i++;
               $this->html .="<tr> 
                <td style='".$this->border().$this->text_center."'>".completarNumero($i)."</td>
                <td style='".$this->border()."'>".$a["nomeAluno"]."</td>
                <td style='".$this->border().$this->text_center."'>".classeExtensa($this, valorArray($a, "idMatCurso", "reconfirmacoes"), valorArray($a, "classeReconfirmacao", "reconfirmacoes"))."</td>
                <td style='".$this->border().$this->text_center."'>".valorArray($a, "designacaoTurma", "reconfirmacoes")."</td>

                <td style='".$this->border().$this->text_center."'>".$total."</td> <td style='".$this->border().$this->text_center."'>".number_format($totalKz, 2, ",", ".")."</td>
                </tr>";
            }
             $this->html .="<tr> 
                <td style='".$this->border().$this->text_center."' colspan='5'>Total</td>

                <td style='".$this->border().$this->text_center."' >".number_format(($totalAcumulado), 2, ",", ".")."</td>
                </tr>";

            $this->html .="</table><p style='".$this->text_center."'>".$this->rodape().".</p>".$this->porAssinatura("O(a) Responsável", "", "", 25);
             
           $this->exibir("", "Lista Geral de Pagamentos de Propinas-".$this->numAno);
        }


    }

new lista(__DIR__);
    
    
  
?>