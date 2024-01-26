<?php if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');
 
    class mapaForcaTrabalho extends funcoesAuxiliares{

        function __construct($caminhoAbsoluto){
            parent::__construct("Rel-Mapa de Avaliação de Desempenho");
            $this->idPAno = isset($_GET["idPAno"])?$_GET["idPAno"]:null;
            $this->tipoPessoal = isset($_GET["tipoPessoal"])?$_GET["tipoPessoal"]:"docente";

            $this->numAno();
            
            if($this->verificacaoAcesso->verificarAcessoAlteracao(["aDirectoria", "aAdministrativa"], "", "", "")){
                if($this->tipoPessoal=="docente"){
                    $this->mapaPessoalDocente();
                }else{
                    $this->mapaPessoalNaoDocente();
                }                 
                
            }else{
              $this->negarAcesso();
            }
        }

         private function mapaPessoalDocente(){

            $this->html="<html style='margin-left:10px; margin-right:10px;'>
            <head>
                <title>Mapa Geral de Avaliação de Desempenho</title>
                <style>
                    table tr td{
                        font-size:7pt;
                        padding:2px;
                    }
                </style>
            </head>
            <body>
            <p style='".$this->text_center.$this->miniParagrafo."'>".$this->cabecalho()."
            <p style='".$this->text_center.$this->bolder."'>MAPA GERAL DE AVALIAÇÃO DE DESEMPENHO DE PESSOAL DOCENTE - ".$this->numAno."</p>";

            $this->html .="<table style='".$this->tabela." width:100%;'>
                <tr style='".$this->corDanger."'>
                    <td rowspan='3' style='width:15px;".$this->bolder.$this->text_center.$this->border()."'>Nº</td>
                    <td rowspan='3' style='width:40px;".$this->bolder.$this->text_center.$this->border()."'>Agente</td>
                    <td rowspan='3' style='width:140px;".$this->bolder.$this->text_center.$this->border()."'>Nome Completo</td>
                    <td rowspan='3'style='".$this->bolder.$this->text_center.$this->border()."width:100px;'>Categoria</td>
                    <td rowspan='3' style='".$this->bolder.$this->text_center.$this->border()."'>Função</td>
                    <td colspan='6' style='".$this->bolder.$this->text_center.$this->border()."'>Pontuação</td>

                    <td rowspan='3' style='".$this->bolder.$this->text_center.$this->border()."'>Pontuação<br/>Total<br/>Obtida</td>
                    <td rowspan='3' style='".$this->bolder.$this->text_center.$this->border()."'>Aval.<br/>Desemp.</td>
                    <td rowspan='3' style='".$this->bolder.$this->text_center.$this->border()."'>Classificação</td>
                </tr>
                <tr style='".$this->corDanger."'>
                    <td colspan='5' style='".$this->bolder.$this->text_center.$this->border()."'>Parcelar</td>
                    <td style='".$this->bolder.$this->text_center.$this->border()."' rowspan='2'>Total</td>
                </tr>
                <tr style='".$this->corDanger."'>
                    <td style='".$this->bolder.$this->text_center.$this->border()."'>QPEA</td>
                    <td style='".$this->bolder.$this->text_center.$this->border()."'>APERF.<br/>PROF.</td>
                    <td style='".$this->bolder.$this->text_center.$this->border()."'>I.<br/>PEDAG.</td>
                    <td style='".$this->bolder.$this->text_center.$this->border()."'>RESPONS.</td>
                    <td style='".$this->bolder.$this->text_center.$this->border()."'>RELAÇÃO HUM.<br/>TRAB.</td>
                </tr>";

                $i=0;
                foreach ($this->selectArray("entidadesprimaria", [], ["escola.idEntidadeEscola"=>$_SESSION['idEscolaLogada'], "escola.estadoActividadeEntidade"=>"A", "escola.tipoPessoal"=>"docente", "aval_desemp.idAvalProfAno"=>$this->idPAno, "aval_desemp.idAvalProfEscola"=>$_SESSION['idEscolaLogada']], ["escola", "aval_desemp"], "", [], ["nomeEntidade"=>1]) as $prof) { 
                    $i++;
                    
                    $total = $prof["aval_desemp"]["qualProcEnsAprend"]+$prof["aval_desemp"]["aperfProfissional"]+$prof["aval_desemp"]["inovPedag"]+$prof["aval_desemp"]["resposabilidade"]+$prof["aval_desemp"]["relHumTrabalho"];

                    $this->html .="<tr><td style='".$this->border().$this->text_center."'>".completarNumero($i)."</td><td style='".$this->border()."'>".$prof["numeroAgenteEntidade"]."</td><td style='".$this->border()."'>".$prof["nomeEntidade"]."</td><td style='".$this->border()."'>".$prof["categoriaEntidade"]."</td><td style='".$this->border()."'>".$prof["escola"]["funcaoEnt"]."</td><td style='".$this->border().$this->text_center."'>".$prof["aval_desemp"]["qualProcEnsAprend"]."</td><td style='".$this->border().$this->text_center."'>".$prof["aval_desemp"]["aperfProfissional"]."</td><td style='".$this->border().$this->text_center."'>".$prof["aval_desemp"]["inovPedag"]."</td><td style='".$this->border().$this->text_center."'>".$prof["aval_desemp"]["resposabilidade"]."</td><td style='".$this->border().$this->text_center."'>".$prof["aval_desemp"]["relHumTrabalho"]."</td><td style='".$this->border().$this->bolder.$this->text_center."'>".$total."</td><td style='".$this->border().$this->text_center."'>".($total)."</td><td style='".$this->border().$this->text_center."'>".number_format(($total/10), 0)."</td><td style='".$this->border().$this->text_center."'>".$this->classificacao(($total/10))."</td></tr>";
                }
                $this->html .="</table>
                <p style='".$this->bolder.$this->text_center."'>".$this->rodape()."</p><br/><div>".$this->assinaturaDirigentes("Director")."</div>";
                $this->html .="</body></html>";
            
            $this->exibir("", "", "", "Mapa Geral de Avaliação de Desempenho ".$this->numAno, "A4", "landscape");
        }

        private function mapaPessoalNaoDocente(){

            $this->html="<html style='margin-left:10px; margin-right:10px;'>
            <head>
                <title>Mapa Geral de Avaliação de Desempenho</title>
                <style>
                    table tr td{
                        font-size:7pt;
                        padding:2px;
                    }
                </style>
            </head>
            <body>
            <p style='".$this->text_center.$this->miniParagrafo."'>".$this->cabecalho()."
            <p style='".$this->text_center.$this->bolder."'>MAPA GERAL DE AVALIAÇÃO DE DESEMPENHO DE PESSOAL NÃO DOCENTE - ".$this->numAno."</p>";

            $this->html .="<table style='".$this->tabela." width:100%;'>
                <tr style='".$this->corDanger."'>
                    <td rowspan='3' style='width:15px;".$this->bolder.$this->text_center.$this->border()."'>Nº</td>
                    <td rowspan='3' style='width:40px;".$this->bolder.$this->text_center.$this->border()."'>Agente</td>
                    <td rowspan='3' style='width:140px;".$this->bolder.$this->text_center.$this->border()."'>Nome Completo</td>
                    <td rowspan='3'style='".$this->bolder.$this->text_center.$this->border()."width:100px;'>Categoria</td>
                    <td rowspan='3' style='".$this->bolder.$this->text_center.$this->border()."'>Função</td>
                    <td colspan='6' style='".$this->bolder.$this->text_center.$this->border()."'>Pontuação</td>

                    <td rowspan='3' style='".$this->bolder.$this->text_center.$this->border()."'>Pontuação<br/>Total<br/>Obtida</td>
                    
                    <td rowspan='3' style='".$this->bolder.$this->text_center.$this->border()."'>Avaliação<br/>Desempenho</td>
                    <td rowspan='3' style='".$this->bolder.$this->text_center.$this->border()."'>Classificação</td>
                </tr>
                <tr style='".$this->corDanger."'>
                    <td colspan='5' style='".$this->bolder.$this->text_center.$this->border()."'>Parcelar</td>
                    <td style='".$this->bolder.$this->text_center.$this->border()."' rowspan='2'>Total</td>
                </tr>
                <tr style='".$this->corDanger."'>
                    <td style='".$this->bolder.$this->text_center.$this->border()."'>Qual. Trab.</td>
                    <td style='".$this->bolder.$this->text_center.$this->border()."'>Aperf.<br/>Prof.</td>
                    <td style='".$this->bolder.$this->text_center.$this->border()."'>Esp.<br/>Inic.</td>
                    <td style='".$this->bolder.$this->text_center.$this->border()."'>Respons.</td>
                    <td style='".$this->bolder.$this->text_center.$this->border()."'>Rel. Hum.<br/>Trab.</td>
                </tr>";

                $i=0;
                foreach ($this->selectArray("entidadesprimaria", [], ["escola.idEntidadeEscola"=>$_SESSION['idEscolaLogada'], "escola.estadoActividadeEntidade"=>"A", "escola.tipoPessoal"=>"naoDocente", "aval_desemp.idAvalProfAno"=>$this->idPAno, "aval_desemp.idAvalProfEscola"=>$_SESSION['idEscolaLogada']], ["escola", "aval_desemp"], "", [], ["nomeEntidade"=>1]) as $prof) {
                    $i++;
                     
                    $total = intval($prof["aval_desemp"]["qualTrabalho"])+intval($prof["aval_desemp"]["aperfProf"])+intval($prof["aval_desemp"]["responsabilidade"])+intval($prof["aval_desemp"]["relHumTrabalho"])+intval($prof["aval_desemp"]["epiritoIniciativa"]);

                    $this->html .="<tr><td style='".$this->border().$this->text_center."'>".completarNumero($i)."</td><td style='".$this->border()."'>".$prof["numeroAgenteEntidade"]."</td><td style='".$this->border()."'>".$prof["nomeEntidade"]."</td><td style='".$this->border()."'>".$prof->categoriaEntidade."</td><td style='".$this->border()."'>".$prof["escola"]["funcaoEnt"]."</td><td style='".$this->border().$this->text_center."'>".$prof["aval_desemp"]["qualTrabalho"]."</td><td style='".$this->border().$this->text_center."'>".$prof["aval_desemp"]["aperfProf"]."</td>
                    <td style='".$this->border().$this->text_center."'>".$prof["aval_desemp"]["epiritoIniciativa"]."</td>
                        <td style='".$this->border().$this->text_center."'>".$prof["aval_desemp"]["responsabilidade"]."</td><td style='".$this->border().$this->text_center."'>".$prof["aval_desemp"]["relHumTrabalho"]."</td><td style='".$this->border().$this->bolder.$this->text_center."'>".$total."</td><td style='".$this->border().$this->text_center."'>".($total)."</td><td style='".$this->border().$this->text_center."'>".($total/10)."</td><td style='".$this->border().$this->text_center."'>".$this->classificacao(($total/10))."</td></tr>";
                }
                $this->html .="</table>
                <p style='".$this->bolder.$this->text_center."'>".valorArray($this->sobreUsuarioLogado, "nomeEscola").", em ".valorArray($this->sobreUsuarioLogado, "municipio")." aos ".dataExtensa($this->dataSistema)."</p><div style='margin-top:-10px;'>".$this->assinaturaDirigentes("Director")."</div>";
                $this->html .="</body></html>";
            

            $this->exibir("", "Mapa Geral de Avaliação de Desempenho-".$this->numAno, "A4", "landscape");
        }

       
        private function classificacao($classificacao){
            if($classificacao<=9){
                return "Medíocre";
            }else if($classificacao<=13){
                return "Suficiente";
            }else if($classificacao<=17){
                return "Bom";
            }else if($classificacao<=20){
                return "Muito Bom";
            }
        }
    }

new mapaForcaTrabalho(__DIR__);
    
    
  
?>