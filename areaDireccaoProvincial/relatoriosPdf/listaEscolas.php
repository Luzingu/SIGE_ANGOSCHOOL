<?php if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }

    include_once $_SESSION["directorioPaterno"].'angoschool/areaDireccaoProvincial/funcoesAuxiliares.php';
    include_once $_SESSION["directorioPaterno"].'angoschool/areaDireccaoProvincial/funcoesAuxiliaresDb.php';

    class ralatorio extends funcoesAuxiliares{

        function __construct($caminhoAbsoluto){
            $this->caminhoRetornar = retornarCaminhoRecuarArquivosPhp(__DIR__);
            parent::__construct("Rel-Lista das Escolas");

            $this->privacidade = isset($_GET["privacidade"])?$_GET["privacidade"]:null;
            if($this->privacidade!="Pública" && $this->privacidade!="Privada"){
                $this->privacidade="Pública";
            }

            $this->html="<html>
            <head>
                <style>
                    table tr td{
                        padding:3px;
                    }
                </style>
            </head>
            <body>".$this->fundoDocumento();
 
            if($this->verificacaoAcesso->verificarAcessoAlteracao(["aRelEstatistica"])){                                
                $this->exibirRelatorio();
            }else{
              $this->negarAcesso();
            }
        }

         private function exibirRelatorio(){

            $this->html .="<div style='position: absolute;'><div style='margin-top: -10px; width:280px;'>".$this->assinaturaDirigentes("DP")."</div></div>".$this->cabecalho()."<br/>
            <p  style='margin-top:10px;".$this->maiuscula.$this->sublinhado.$this->bolder.$this->text_center."'>Lista das Escolas ".$this->privacidade."s</p>
            <table style='".$this->tabela."width:100%;'>
                <tr style='".$this->corDanger.$this->text_center.$this->bolder."'>
                    <td style='".$this->border()."'>N.º</td>
                    <td style='".$this->border()."'>Nome</td>
                    <td style='".$this->border()."'>Categoria</td>
                    <td style='".$this->border()."'>Localização</td>
                </tr>";
            $contador=0;
            foreach($this->selectArray("escolas LEFT JOIN div_terit_comunas ON idPComuna=comuna LEFT JOIN div_terit_municipios ON idPMunicipio=municipio LEFT JOIN div_terit_provincias ON idPProvincia=provincia", "*", "tipoInstituicao=:tipoInstituicao AND provincia=:provincia AND idPEscola!=4 AND privacidadeEscola=:privacidadeEscola AND estadoEscola=:estadoEscola", ["escola", valorArray($this->sobreUsuarioLogado, "provincia"), $this->privacidade, "A"], "nomeEscola ASC") as $a){
                $contador++;

                $nivelEscola = $a->nivelEscola;
                if($nivelEscola=="primaria"){
                    $nivelEscola="Primário";
                }else if($nivelEscola=="basica"){
                    $nivelEscola="I Ciclo";
                }else if($nivelEscola=="media"){
                    $nivelEscola="II Ciclo";
                }else if($nivelEscola=="primBasico"){
                    $nivelEscola="Complexo (Primária e I Ciclo)";
                }else if($nivelEscola=="basicoMedio"){
                    $nivelEscola="Complexo (I e II Ciclo)";
                }else if($nivelEscola=="complexo"){
                    $nivelEscola="Complexo (Primária, I e II Ciclo)";
                }

                $this->html .="<tr>
                    <td style='".$this->border().$this->text_center."'>".completarNumero($contador)."</td>
                    <td style='".$this->border()."'>".$a->nomeEscola."</td>
                    <td style='".$this->border()."'>".$nivelEscola."</td>
                    <td style='".$this->border()."'>".$a->nomeMunicipio."</td></tr>";
            }

            $this->html .="</table><p>".$this->rodape()."</p>".$this->assinaturaDirigentes("CDEPE");
            
           $this->exibir("", "Lista das Escolas-".$this->privacidade);
        }
    }

new ralatorio(__DIR__);
    
    
  
?>