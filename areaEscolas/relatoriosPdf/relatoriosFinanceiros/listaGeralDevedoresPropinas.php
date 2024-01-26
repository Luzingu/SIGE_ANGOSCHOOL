<?php if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');

    class lista extends funcoesAuxiliares{

        function __construct(){

            parent::__construct("Rel-Lista dos Devedores");

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

            if($this->verificacaoAcesso->verificarAcesso("", ["alunosDevedores77"], [], "")){
                $this->visualizar();
            }else{
                $this->negarAcesso();
            }
            
        }

         private function visualizar(){
             $pagamentos = $this->selectArray("alunosmatriculados", ["pagamentos.referenciaPagamento"], ["pagamentos.codigoEmolumento"=>"propinas", "pagamentos.idHistoricoEscola"=>$_SESSION['idEscolaLogada'], "pagamentos.idHistoricoAno"=>$this->idPAno], ["pagamentos"], 1, [], ["pagamentos.idPHistoricoConta"=>-1]);

            $this->posicaoInicial = array_search(valorArray($pagamentos, "referenciaPagamento", "pagamentos"), $this->mesesAnoLectivo);
            
            $this->html .=$this->cabecalho()."
            <p style='".$this->text_center.$this->maiuscula.$this->bolder."'>LISTA DOS ALUNOS DEVEDORES DE<br/> ";
            if(valorArray($this->sobreUsuarioLogado, "privacidadeEscola")=="Privada"){
                $this->html .="PROPINAS";
            }else{
                $this->html .="COMPARTICIPAÇÕES";
            }
            $this->html .=" DO MÊS DE ".nomeMes($this->mes)." - ".$this->numAno."</p>
            ";
            $this->html .="
            CURSO: <strong style='".$this->maiuscula."'>".$this->nomeCurso."</strong><br><br>
            <table style='".$this->tabela." width:100%;'>
                <tr style='".$this->corDanger.$this->bolder.$this->text_center."'>
                <td style='".$this->border()." width:30px;'>N.º</td>
                <td style='".$this->border()."'>Nome Completo</td>
                <td style='".$this->border()."'>Classe</td>
                <td style='".$this->border()."'>Turma</td>
                    <td style='".$this->border()."'>N.º de<br/>Meses</td>
                </tr>";


            $this->alunos = $this->selectArray("alunosmatriculados", ["pagamentos.idHistoricoAno", "pagamentos.idHistoricoEscola", "pagamentos.codigoEmolumento", "pagamentos.referenciaPagamento", "reconfirmacoes.designacaoTurma", "numeroInterno", "idPMatricula", "nomeAluno", "reconfirmacoes.classeReconfirmacao", "escola.beneficiosDaBolsa"], ["reconfirmacoes.idReconfAno"=>$this->idPAno, "escola.idMatEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.idMatCurso"=>$this->idPCurso, "reconfirmacoes.idReconfEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.estadoReconfirmacao"=>"A", "reconfirmacoes.estadoDesistencia"=>array('$nin'=>["D", "N", "F"])], ["reconfirmacoes", "escola"], "", [], array("nomeAluno"=>1));

              $alunosDevedores = array();
              foreach ($this->alunos as $aluno) {
                
                $beneficiosDaBolsa = valorArray($aluno, "beneficiosDaBolsa","escola");
                $beneficiosDaBolsa = (is_array($beneficiosDaBolsa) || is_object($beneficiosDaBolsa))?$beneficiosDaBolsa:array();

                $eGratuito="nao";
                foreach($beneficiosDaBolsa as $ben){
                  if($ben["idPTipoEmolumento"]==1 && $ben["mes"]==$this->mes){
                      if($ben["valorPreco"]<=0){
                        $eGratuito="sim";
                      }
                      break;
                  }
                }

                if(count(listarItensObjecto($aluno, "pagamentos", ["idHistoricoAno=".$this->idPAno, "idHistoricoEscola=".$_SESSION['idEscolaLogada'], "codigoEmolumento=propinas", "referenciaPagamento=".$this->mes]))<=0 && $eGratuito=="nao"){
                  $alunosDevedores[]=$aluno;
                }
              }


            $i=0;
            foreach($alunosDevedores as $a){
               $i++;
               $this->html .="<tr>
                <td style='".$this->border().$this->text_center."'>".completarNumero($i)."</td>
                <td style='".$this->border()."'>".$a["nomeAluno"]."</td>
                <td style='".$this->border().$this->text_center."'>".classeExtensa($this, $a["reconfirmacoes"]["idMatCurso"], $a["reconfirmacoes"]["classeReconfirmacao"])."</td>
                <td style='".$this->border().$this->text_center."'>".$a["reconfirmacoes"]["designacaoTurma"]."</td><td style='".$this->border().$this->text_center."'>".$this->totMesADeves($a, $a["idPMatricula"])."</td>
                </tr>";
            }

            $this->html .="</table><p style='".$this->maiuscula."'>".$this->rodape().".</p>
            <div>".$this->porAssinatura("O(a) Responsável", "", "", 25)."</div>";

            $this->exibir("", "Lista dos Devedores");
        }
        
        private function totMesADeves($aluno, $idPMatricula){
            $totalMeses=0;
            for($i=$this->posicaoInicial; $i<=array_search($this->mes, $this->mesesAnoLectivo); $i++){
                if(count(listarItensObjecto($aluno, "pagamentos", ["codigoEmolumento=propinas", "idHistoricoEscola=".$_SESSION['idEscolaLogada'], "idHistoricoAno=".$this->idPAno, "referenciaPagamento=".$this->mesesAnoLectivo[$i]]))<=0){
                    $totalMeses++;
                }
            }
            return $totalMeses;
        }
    }

new lista();
    
    
  
?>