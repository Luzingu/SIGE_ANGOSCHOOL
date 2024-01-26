<?php if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');

    class lista extends funcoesAuxiliares{

        function __construct($caminhoAbsoluto){
            parent::__construct("Rel-Lista de Pagamentos por Turma");

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
            if($this->verificacaoAcesso->verificarAcesso("", ["alunosJaEfectuaramPagamentos77"], [], "")){
                $this->visualizar();
            }else{
                $this->negarAcesso();
            }
            
        }

         private function visualizar(){
 
            foreach($this->turmasEscola(array(intval($this->idPCurso)), array(), $this->idPAno) as $turma){

                $this->html .="
            <div style='page-break-after: always;'>".$this->fundoDocumento("../../../").$this->cabecalho()."
                <p style='".$this->text_center.$this->maiuscula.$this->bolder."'>LISTA DE PAGAMENTOS DE ";
                if(valorArray($this->sobreUsuarioLogado, "privacidadeEscola")=="Privada"){
                    $this->html .="PROPINAS";
                }else{
                    $this->html .="COMPARTICIPAÇÕES";
                }
                $this->html .=" - ".$this->numAno."</p>
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
                <p style='".$this->maiuscula." margin-top:-10px;'>CLASSE: <strong>".classeExtensa($this, valorArray($turma, "idPNomeCurso"), valorArray($turma, "classe"))." / ".$turma["designacaoTurma"]."</strong></p>
                <table style='".$this->tabela." width:100%;'>
                    <tr style='".$this->corDanger.$this->bolder.$this->text_center.$this->maiuscula."'>
                    <td style='".$this->border()."' rowspan='2'>N.º</td>
                    <td style='".$this->border()."' rowspan='2'>Nome Completo</td>
                    <td style='".$this->border()."' rowspan='2'>N.º Interno</td>
                    <td style='".$this->border()."' colspan='2'>Meses Pagos</td>
                    </tr>
                    <tr style='".$this->corDanger.$this->bolder.$this->text_center."'>
                        <td style='".$this->border()."'>N.º</td>
                        <td style='".$this->border()."'>KZ</td>
                    </tr>";
                $totalAcumulado=0;
                $i=0;
                foreach($this->alunosPorTurma(nelson($turma, "idPNomeCurso"), $turma["classe"], $turma["nomeTurma"], $this->idPAno, array(), ["pagamentos.idHistoricoEscola", "pagamentos.codigoEmolumento", "pagamentos.precoPago", "nomeAluno", "numeroInterno", "pagamentos.idHistoricoAno"]) as $a){

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
                    <td style='".$this->border().$this->text_center."'>".$a["numeroInterno"]."</td>

                    <td style='".$this->border().$this->text_center."'>".$total."</td> <td style='".$this->border().$this->text_center."'>".number_format($totalKz, 2, ",", ".")."</td>
                    </tr>";
                }
                 $this->html .="<tr> 
                <td style='".$this->border().$this->text_center."' colspan='3'>Total</td>

                <td style='".$this->border().$this->text_center."' colspan='2'>".number_format(($totalAcumulado), 2, ",", ".")."</td>
                </tr></table><p style='".$this->text_center."'>".$this->rodape().".</p>
                ".$this->porAssinatura("O(a) Responsável", "", "", 25)."</div>";
            }
            $this->exibir("", "Lista de Pagamentos de Propinas por Turma-".$this->numAno);
        }


    }

new lista(__DIR__);
    
    
  
?>