<?php if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');

    class lista extends funcoesAuxiliares{

        function __construct($caminhoAbsoluto){
            parent::__construct("Rel-Lista dos Devedores por Turma");

            $this->idPAno = isset($_GET["idPAno"])?$_GET["idPAno"]:"";
            $this->mes = isset($_GET["mes"])?$_GET["mes"]:"";
            $this->idPCurso = isset($_GET["idPNomeCurso"])?$_GET["idPNomeCurso"]:"";
            $this->numAno();
            $this->nomeCurso();

             $this->html="<html style='margin-left:60px;margin-right:60px;'>
            <head>
                <title>Lista de Pagamentos</title>
            </head>
            <body>";
            if($this->verificacaoAcesso->verificarAcesso("", ["alunosDevedores77"], [], "")){
                $this->visualizar();
            }else{
                $this->negarAcesso();
            }
            
        } 

         private function visualizar(){
            $pagamentos = $this->selectArray("alunosmatriculados", ["pagamentos.referenciaPagamento"], ["pagamentos.codigoEmolumento"=>"propinas", "pagamentos.idHistoricoEscola"=>$_SESSION['idEscolaLogada'], "pagamentos.idHistoricoAno"=>$this->idPAno], ["pagamentos"], 1, [], ["pagamentos.idPHistoricoConta"=>-1]);

            $this->posicaoInicial = array_search(valorArray($pagamentos, "referenciaPagamento", "pagamentos"), $this->mesesAnoLectivo);                

            
            foreach($this->turmasEscola(array(intval($this->idPCurso)), array(), $this->idPAno) as $turma){
 
                $this->html .="
            <div style='page-break-after: always;'>".$this->fundoDocumento("../../../").$this->cabecalho()."
                <p style='".$this->text_center.$this->maiuscula.$this->bolder."'>LISTA DOS ALUNOS DOS DEVEDORES DE<br/> ";
            if(valorArray($this->sobreUsuarioLogado, "privacidadeEscola")=="Privada"){
                $this->html .="PROPINAS";
            }else{
                $this->html .="COMPARTICIPAÇÕES";
            }
            $this->html .=" DO MÊS DE ".nomeMes($this->mes)." - ".$this->numAno."</p>
                ";
            if(valorArray($turma, "tipoCurso")=="pedagogico"){
                $this->html .="<p style='".$this->miniParagrafo.$this->maiuscula."'>CURSO: <strong>".valorArray($turma, "areaFormacaoCurso")."</strong></p>
                <p style='".$this->maiuscula."'>OPÇÃO: <strong>".valorArray($turma, "nomeCurso")."</strong></p>";
            }else if(valorArray($turma, "tipoCurso")=="tecnico"){
                $this->html .="<p style='".$this->miniParagrafo.$this->maiuscula."'>ÁREA DE FORMAÇÃO: <strong>".valorArray($turma, "areaFormacaoCurso")."</strong></p>
                <p style='".$this->maiuscula."'>CURSO: <strong>".valorArray($turma, "nomeCurso")."</strong></p>";
            }else{
                $this->html .="<p style='".$this->maiuscula."'>CURSO: <strong>".valorArray($turma, "nomeCurso")."</strong></p>";
            }

                $this->html .="
                <p style='".$this->maiuscula." margin-top:-10px;'>PERÍODO: <strong>".periodoExtenso($turma["periodoT"])."</strong>
                <p style='".$this->maiuscula." margin-top:-10px;'>CLASSE: <strong>".classeExtensa($this, $turma["idPNomeCurso"], $turma["classe"])." / ".$turma["designacaoTurma"]."</strong></p>
                <table style='".$this->tabela." width:100%;'>
                    <tr style='".$this->corDanger.$this->bolder.$this->text_center."'>
                    <td style='".$this->border()."'>N.º</td>
                    <td style='".$this->border()."'>Nome Completo</td>
                    <td style='".$this->border()."'>N.º Interno</td>
                    <td style='".$this->border()."'>N.º de<br/>Meses</td>
                    </tr>";
                $totalAcumulado=0;
                $i=0;
                foreach($this->alunosPorTurma(valorArray($turma, "idPNomeCurso"), valorArray($turma, "classe"), $turma["nomeTurma"], $this->idPAno, array(), ["pagamentos.idHistoricoAno", "pagamentos.idHistoricoEscola", "pagamentos.codigoEmolumento", "pagamentos.referenciaPagamento", "reconfirmacoes.designacaoTurma", "numeroInterno", "idPMatricula", "nomeAluno", "escola.beneficiosDaBolsa"], [], ["reconfirmacoes.estadoDesistencia"=>array('$nin'=>["D", "N", "F"])]) as $a){

                    $beneficiosDaBolsa = valorArray($a, "beneficiosDaBolsa","escola");
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

                    if(count(listarItensObjecto($a, "pagamentos", ["idHistoricoAno=".$this->idPAno, "idHistoricoEscola=".$_SESSION['idEscolaLogada'], "codigoEmolumento=propinas", "referenciaPagamento=".$this->mes]))<=0 && $eGratuito=="nao"){
                       $i++;
                       $this->html .="<tr> 
                        <td style='".$this->border().$this->text_center."'>".completarNumero($i)."</td>
                        <td style='".$this->border()."'>".$a->nomeAluno."</td>
                        <td style='".$this->border().$this->text_center."'>".$a["numeroInterno"]."</td><td style='".$this->border().$this->text_center."'>".$this->totMesADeves($a, $a["idPMatricula"])."</td></tr>";
                    }
                }
                 $this->html .="
                 </table><p>".$this->rodape().".</p>
                ".$this->porAssinatura("O(a) Responsável", "", "", 25)."</div>";
            }
            $this->exibir("", "Lista de Pagamentos de Comparticipações-".$this->numAno);
        }
    
        private function totMesADeves($a, $idPMatricula){
            $totalMeses=0;
            for($i=$this->posicaoInicial; $i<=array_search($this->mes, $this->mesesAnoLectivo); $i++){

                if(count(listarItensObjecto($a, "pagamentos", ["codigoEmolumento=propinas", "idHistoricoEscola=".$_SESSION['idEscolaLogada'], "idHistoricoAno=".$this->idPAno, "referenciaPagamento=".$this->mesesAnoLectivo[$i]]))<=0){
                    $totalMeses++;
                }
            }
            return $totalMeses;
        }

    }

new lista(__DIR__);
    
    
  
?>