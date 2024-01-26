<?php if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }    
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');

    class notaEntrega extends funcoesAuxiliares{

        function __construct($caminhoAbsoluto){
            $this->idPHistoricoConta = isset($_GET["idPHistoricoConta"])?$_GET["idPHistoricoConta"]:null;
            parent::__construct("Rel-Nota de Entrega");  

            $this->html="<html style='margin-top:10px;'>
            <head>
                <title>Nota de Entrega</title>
            </head>
            <body>".$this->fundoDocumento("../../../")."".$this->cabecalho();
 
            if($this->verificacaoAcesso->verificarAcessoAlteracao(["aDirectoria", "aAdministrativa", "aComissaoPais"], "", "")){                                
                $this->nota();
            }else{
              $this->negarAcesso();
            }
            
        }

         private function nota(){ 

            $notaEntrega =  $this->selectArray("historicocontaescola", [], ["idHistoricoEscola"=>$_SESSION["idEscolaLogada"], "idPHistoricoConta"=>$this->idPHistoricoConta]); 
            $notaEntrega = $this->anexarTabela($notaEntrega, "entidadesprimaria", "idPEntidade", "idHistoricoFuncionario");

            $this->html .="<p>________/".valorArray($this->sobreUsuarioLogado, "abrevNomeEscola")."/".explode("-", valorArray($notaEntrega, "dataHistorico"))[0]."</p>
            <p style='".$this->text_justify."line-height:35px;'>Foi entregue ao(a) Senhor(a) ".valorArray($notaEntrega, "referenciaOperacao").", o valor de ".number_format(floatval(valorArray($notaEntrega, "valorEfectuado")), 2, ",", ".")." KZS (".primeiraLetraMaiuscula($this->retornarNotaExtensa(number_format(floatval(valorArray($notaEntrega, "valorEfectuado")), 2, ",", ".")))." Kwanzas).<br/>Referente a ".valorArray($notaEntrega, "descritivoConta").".</p>
            <p>OBS: __________________________________________________________________________________<br/><br/>
            ________________________________________________________________________________________<br/><br/>________________________________________________________________________________________</p>
            ";
           

            $this->html .="<p style='".$this->text_justify." line-height:25px;'>A Subdirecção Administrativa d".$this->art1Escola." ".$this->rodape().".</p><br/>"; 

             $this->html .="
            <div class='rodape'>
                <div class='financeiro' style='width: 50%;'>".$this->porAssinatura("Entregou", valorArray($notaEntrega, "nomeEntidade")."<br/>(".converterData(valorArray($notaEntrega, "dataHistorico")), "", strlen(valorArray($notaEntrega, "nomeEntidade")))."
                     
                </div>

                <div class='director' style='width: 50%;margin-left: 50%;margin-top: -150px;'>".$this->porAssinatura("Recebeu", valorArray($notaEntrega, "referenciaOperacao")."<br/>(______/_______/20______)", "", strlen(valorArray($notaEntrega, "referenciaOperacao")))."</div>
            </div>";

          $this->exibir("", "Nota de Entrega-".dataExtensa(valorArray($notaEntrega, "dataHistorico")));
        }


    }
new notaEntrega(__DIR__);
    
    
  
?>
