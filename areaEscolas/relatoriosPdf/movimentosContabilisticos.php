<?php if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }   
   
    include_once ('../funcoesAuxiliares.php');
    include_once ('../funcoesAuxiliaresDb.php');

    class lista extends funcoesAuxiliares{

        function __construct($caminhoAbsoluto){
            parent::__construct("Rel-Movimentos Contabilísticos");
            $this->anoCivil = isset($_GET["anoCivil"])?$_GET["anoCivil"]:"";
            $this->mesPagamento = isset($_GET["mesPagamento"])?$_GET["mesPagamento"]:"";
            $this->relatorio();
        }

         private function relatorio(){

            $lista = $this->selectArray("general_ledger_entries", [],["idDocEscola"=>$_SESSION['idEscolaLogada'], "dataEmissao"=>new \MongoDB\BSON\Regex($this->anoCivil."-".completarNumero($this->mesPagamento)."-"), "sePagSalario"=>"I"],[], "", [], ["idPDocumento"=>1]);


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
            <p style='".$this->text_center.$this->maiuscula.$this->bolder.$this->sublinhado."'>Movimentos Contabilísticos - ".nomeMes($this->mesPagamento)." / ".$this->anoCivil."</p>

            <table style='".$this->tabela."width:100%;'>
                <tr style='".$this->corDanger."'><td style='".$this->border().$this->bolder.$this->text_center."'>N.º</td><td style='".$this->border().$this->bolder."'>Descrição do Movimento</td><td style='".$this->border().$this->text_center.$this->bolder."'>N.º<br>Arquivo</td><td style='".$this->border().$this->text_center.$this->bolder."'>Tipo<br>Mvt</td><td style='".$this->border().$this->text_center.$this->bolder."'>Natureza</td><td style='".$this->border().$this->text_center.$this->bolder."'>Conta</td><td style='".$this->border().$this->text_center.$this->bolder."'>Valor</td></tr>";

            
            $i=0;
            foreach ($lista as $a) {
                $i++;
                $this->html .="<tr>
                    <td style='".$this->border().$this->text_center."'>".completarNumero($i)."</td>
                    <td style='".$this->border()."'>".$a["descricaoMovimento"]."</td>
                    <td style='".$this->border().$this->text_center."'>".$a["numArquivoDocumento"]."</td>
                    <td style='".$this->border().$this->text_center."'>".$a["tipoMovimento"]."</td>
                    <td style='".$this->border().$this->text_center."'>".$a["movimento"]."</td>
                    <td style='".$this->border().$this->text_center."'>".$a["descricaoContaLiha"]."</td>
                    <td style='".$this->border().$this->text_center."'>".number_format(floatval($a["valorLinha"]), 2, ",", ".")."</td>
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