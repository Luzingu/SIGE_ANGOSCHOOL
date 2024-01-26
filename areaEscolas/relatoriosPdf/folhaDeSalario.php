<?php if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }   
   
    include_once ('../funcoesAuxiliares.php');
    include_once ('../funcoesAuxiliaresDb.php');

    class lista extends funcoesAuxiliares{

        function __construct($caminhoAbsoluto){
            parent::__construct("Rel-Folha de Salário");
            $this->anoCivil = isset($_GET["anoCivil"])?$_GET["anoCivil"]:"";
            $this->mesPagamento = isset($_GET["mesPagamento"])?$_GET["mesPagamento"]:"";
            $this->relatorio();
        }

         private function relatorio(){

            $lista = $this->selectArray("entidadesprimaria", ["idPEntidade", "nomeEntidade", "salarios.idPSalario", "salarios.dataPagamento", "salarios.horaPagamento", "salarios.numeroTempos", "salarios.nomeFuncProc", "pagamentoPorTempo", "salarios.contaDebitada", "escola.funcaoEnt", "salarios.segurancaSocial", "salarios.IRT", "salarios.totalSubsidios", "salarios.outrosDescontos", "salarios.valorAuferidoNaInstituicao", "salarios.salarioLiquido"], ["salarios.idEscola"=>$_SESSION['idEscolaLogada'], "escola.idEntidadeEscola"=>$_SESSION['idEscolaLogada'], "salarios.anoCivil"=>$this->anoCivil, "salarios.mesPagamento"=>$this->mesPagamento], ["salarios", "escola"], "", [], ["nomeEntidade"=>1]); 


            $this->html .="<html style='margin-left:0px; margin-right:0px;'>
            <head>
                <title>Folha de Salário</title>
                <style>
                  table tr td{
                      padding:3px;
                  }
                  table, p{
                    font-size:11pt;
                  }
                
                </style>
            </head>
            <body style='margin-left:50px; margin-right:50px;'>".$this->fundoDocumento("../../")."
            ".$this->cabecalho()."
            <p style='".$this->text_center.$this->maiuscula.$this->bolder.$this->sublinhado."'>FOLHA DE SALÁRIO DO MÊS DE ".nomeMes($this->mesPagamento)." / ".$this->anoCivil."</p>
            <table style='".$this->tabela."width:100%;'>
                <tr style='".$this->corDanger."'><td style='".$this->border().$this->bolder.$this->text_center."' rowspan='2'></td>N.º</td><td style='".$this->border().$this->bolder.$this->text_center."' rowspan='2'>Nome Completo</td><td style='".$this->border().$this->bolder.$this->text_center."' rowspan='2'>Função</td><td style='".$this->border().$this->bolder.$this->text_center."' rowspan='2'>Salário<br>Básico</td><td style='".$this->border().$this->text_center.$this->bolder."' rowspan='2'>N.º Tempos</td><td style='".$this->border().$this->bolder.$this->text_center."' rowspan='2'>Pag/Tempo</td><td style='".$this->border().$this->text_center.$this->bolder."' rowspan='2'>Subsídio</td><td style='".$this->border().$this->text_center.$this->bolder."' colspan='3' >Descontos</td><td style='".$this->border().$this->text_center.$this->bolder."' rowspan='2'>Salário<br>Líquido</td></tr>
                <tr style='".$this->corDanger.$this->bolder."'>
                    <td style='".$this->border().$this->text_center."'>IRT</td>
                    <td style='".$this->border().$this->text_center."'>SS</td>
                    <td style='".$this->border().$this->text_center."'>O. Descontos</td>
                </tr>";

            
            $i=0;
            foreach ($lista as $a) {
                $i++;
                $this->html .="<tr>
                    <td style='".$this->border().$this->text_center."'>".completarNumero($i)."</td>
                    <td style='".$this->border()."'>".$a["nomeEntidade"]."</td>
                    <td style='".$this->border()."'>".$a["escola"]["funcaoEnt"]."</td>
                    <td style='".$this->border().$this->text_center."'>".number_format(floatval(valorArray($a, "valorAuferidoNaInstituicao", "salarios")), 2, ",", ".")."</td>
                    <td style='".$this->border().$this->text_center."'>".valorArray($a, "numeroTempos", "salarios")."</td>
                    <td style='".$this->border().$this->text_center."'>".valorArray($a, "pagamentoPorTempo", "salarios")."</td>
                    <td style='".$this->border().$this->text_center."'>".valorArray($a, "totalSubsidios", "salarios")."</td>
                    <td style='".$this->border().$this->text_center."'>".number_format(floatval(valorArray($a, "IRT", "salarios")), 2, ",", ".")."</td>
                    <td style='".$this->border().$this->text_center."'>".number_format(floatval(valorArray($a, "segurancaSocial", "salarios")), 2, ",", ".")."</td>
                    <td style='".$this->border().$this->text_center."'>".number_format(valorArray($a, "outrosDescontos", "salarios"), 2, ",", ".")."</td>
                    <td style='".$this->border().$this->text_center."'>".number_format(valorArray($a, "salarioLiquido", "salarios"), 2, ",", ".")."</td>
                </tr>";
            }
            $this->html .="</table>";
            $this->html .="<p style='padding-left:10px; padding-right:10px;".$this->text_center."'>".$this->rodape().".</p>
            <div style='".$this->maiuscula."'>".$this->assinaturaDirigentes(7)."</div>";
            
           $this->exibir("", "Folha de Salário-".$this->anoCivil, "", "A4", "landscape");
        }
    }
    new lista(__DIR__);
?>