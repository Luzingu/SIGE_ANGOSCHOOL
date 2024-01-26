<?php if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');

    class lista extends funcoesAuxiliares{

        function __construct(){
            parent::__construct("Rel-Lista de Pagamentos do mês");

            $this->idPAno = isset($_GET["idPAno"])?$_GET["idPAno"]:"";
            $this->mes = isset($_GET["mes"])?$_GET["mes"]:"";
            $this->idPCurso = isset($_GET["idPNomeCurso"])?$_GET["idPNomeCurso"]:"";
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
            <p style='".$this->text_center.$this->maiuscula.$this->bolder."'>LISTA DE PAGAMENTOS DE ";
            if(valorArray($this->sobreUsuarioLogado, "privacidadeEscola")=="Privada"){
                $this->html .="PROPINAS";
            }else{
                $this->html .="COMPARTICIPAÇÕES";
            }
            $this->html .="<br>REFERENTE AO MÊS DE ".nomeMes($this->mes)." - ".$this->numAno."</p>
            CURSO: <strong style='".$this->maiuscula."'>".$this->nomeCurso."</strong><br><br>
            ";
            $this->html .="
            <table style='".$this->tabela." width:100%;'>
                <tr style='".$this->corDanger.$this->bolder.$this->text_center.$this->maiuscula."'>
                <td style='".$this->border()." width:30px;'>N.º</td>
                <td style='".$this->border()."'>Nome Completo</td>
                <td style='".$this->border()." width:100px;'>Classe</td>
                <td style='".$this->border()." width:100px;'>Turma</td>
                <td style='".$this->border()." width:100px;'>Valor</td>
                </tr>";
            $i=0;

            $this->alunos = $this->selectArray("alunosmatriculados", ["nomeAluno", "numeroInterno", "reconfirmacoes.designacaoTurma", "reconfirmacoes.classeReconfirmacao", "pagamentos.precoPago"], ["reconfirmacoes.idReconfEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.idReconfAno"=>$this->idPAno, "reconfirmacoes.idMatCurso"=>$this->idPCurso, "pagamentos.idHistoricoEscola"=>$_SESSION['idEscolaLogada'], "pagamentos.idHistoricoAno"=>$this->idPAno, "pagamentos.idTipoEmolumento"=>1, "pagamentos.referenciaPagamento"=>$this->mes], ["reconfirmacoes", "pagamentos"], "", [], array("nomeAluno"=>1));
            foreach($this->alunos as $a){
               $i++;
               $this->html .="<tr>
                <td style='".$this->border().$this->text_center."'>".completarNumero($i)."</td>
                <td style='".$this->border()."'>".$a["nomeAluno"]."</td>
                <td style='".$this->border().$this->text_center."'>".classeExtensa($this, $a["reconfirmacoes"]["idMatCurso"], $a["reconfirmacoes"]["classeReconfirmacao"])."</td>
                <td style='".$this->border().$this->text_center."'>".nelson($a, "designacaoTurma", "reconfirmacoes")."</td>
                <td style='".$this->border().$this->text_center."'>".number_format(nelson($a, "precoPago", "pagamentos"), 2, ",", ".")."</td>
                </tr>";
            }

            $this->html .="</table><p style='".$this->text_center."'>".$this->rodape().".</p><div >".$this->porAssinatura("O(a) Responsável", "", "", 25)."</div>";
             
           $this->exibir("", "Lista de Pagamentos do mês de ".nomeMes($this->mes)."-".$this->numAno);
        }
    }

new lista();
    
    
  
?>