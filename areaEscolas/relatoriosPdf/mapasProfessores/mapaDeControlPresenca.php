<?php 
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');

    class mapaForcaTrabalho extends funcoesAuxiliares{

        function __construct($caminhoAbsoluto){
            parent::__construct("Rel-Professor por Tipo de Disciplina");
            $this->anoCivil = isset($_GET["anoCivil"])?$_GET["anoCivil"]:2022;
            $this->tamanhoFolha = isset($_GET["tamanhoFolha"])?$_GET["tamanhoFolha"]:"A4";
            $this->diaInicial = isset($_GET["diaInicial"])?$_GET["diaInicial"]:"";
            $this->mesInicial = isset($_GET["mesInicial"])?$_GET["mesInicial"]:9;
            $this->diaFinal = isset($_GET["diaFinal"])?$_GET["diaFinal"]:9;
            $this->mesFinal = isset($_GET["mesFinal"])?$_GET["mesFinal"]:9;

            $this->anoInicial=intval($this->anoCivil);
            if($this->mesInicial!=$this->mesFinal && $this->mesInicial==12){
                $this->anoInicial--;
            }
            $this->dataInicial = $this->anoInicial."-".completarNumero($this->mesInicial)."-".completarNumero($this->diaInicial);
            $this->dataFinal = $this->anoCivil."-".completarNumero($this->mesFinal)."-".completarNumero($this->diaFinal);
 
            $this->html="<html style='margin-left:20px; margin-right:20px;'>
            <head>
                <title>Mapa de Presença<title>
            </head>
            <body>";
            if($this->verificacaoAcesso->verificarAcesso("", ["listaAgentes"], [], "")){               
                $this->mapa();
            }else{
              $this->negarAcesso();
            }
        }

         private function mapa(){

            $this->controlFaltas = $this->selectArray("entidadesprimaria", ["controlPresenca.presencas", "idPEntidade"], ["escola.idEntidadeEscola"=>$_SESSION['idEscolaLogada'], "escola.estadoActividadeEntidade"=>"A", "escola.tipoPessoal"=>"docente", "controlPresenca.idEscola"=>$_SESSION['idEscolaLogada'], "controlPresenca.data"=>array('$gte'=>$this->diaInicial), "controlPresenca.data"=>array('$lte'=>$this->dataFinal)], ["escola", "controlPresenca"]);

            $datas =array();
            for($i=$this->diaInicial; $i<=date("t", strtotime($this->dataInicial)); $i++){

                $luzinguLuame = strtotime($this->anoInicial."-".$this->mesInicial."-".completarNumero($i));

                $data = date("Y-m-d", $luzinguLuame);
                $semana = date("w", $luzinguLuame);

                if($semana!=0){
                    $datas[]=array("dia"=>completarNumero($i), "semana"=>$semana, "data"=>$data);
                }
            }
            if($this->mesInicial!=$this->mesFinal){
                for($i=1; $i<=$this->diaFinal; $i++){

                    $luzinguLuame = strtotime($this->anoCivil."-".$this->mesFinal."-".completarNumero($i));

                    $data = date("Y-m-d", $luzinguLuame);
                    $semana = date("w", $luzinguLuame);

                    if($semana!=0){
                        $datas[]=array("dia"=>completarNumero($i), "semana"=>$semana, "data"=>$data);
                    }
                }
            }

            $this->html .=$this->cabecalho()."
            <p style='".$this->text_center.$this->bolder.$this->maiuscula.$this->miniParagrafo."'>Mapa de Presença REFERENTE AO mês de ".nomeMes($this->mesFinal)." de ".$this->anoCivil."<p/>
            <p style='".$this->text_center.$this->bolder."margin-top:-13px;'>".dataExtensa($this->dataInicial)." a ".dataExtensa($this->dataFinal)."</p>

            <table style='".$this->tabela."font-size:9pt; width:100%;'>
              <tr style='".$this->corDanger.$this->bolder.$this->text_center."'>
                <td style='".$this->border()."' rowspan='2'>N.º</td>
                <td style='".$this->border()."' rowspan='2'>AGENTE</td>
                <td style='".$this->border()."' rowspan='2'>NOME COMPLETO</td>";
                foreach($datas as $data){
                    $this->html .="<td style='".$this->border().$this->text_center."'>".diaSemana4($data["semana"])."</td>";
                }

                $this->html .="<td style='".$this->border()."' rowspan='2'>Total</td></tr>
                <tr style='".$this->corDanger.$this->bolder.$this->text_center."'>";
                foreach($datas as $data){
                    $this->html .="<td style='".$this->border().$this->text_center."'>".$data["dia"]."</td>";
                }
                $i=0;
                foreach($this->entidades(["idPEntidade", "numeroAgenteEntidade", "nomeEntidade"], "docente") as $entidade){
                        $i++;
                        $this->html .="
                            <tr><td style='".$this->border().$this->text_center."'>".completarNumero($i)."</td><td style='".$this->border().$this->text_center."'>".$entidade["numeroAgenteEntidade"]."</td><td style='".$this->border()."'>".$entidade["nomeEntidade"]."</td>";

                        $contPresenca=0;

                        foreach($datas as $data){
                            $presencas="";

                            if($data["data"]<=$this->dataSistema){
                                foreach($this->controlFaltas as $control){
                                    if($control["idPEntidade"]==$entidade["idPEntidade"] && $control["controlPresenca"]["data"]==$data["data"]){

                                        $presencas = valorArray($control, "presencas", "controlPresenca");
                                        $contPresenca +=intval(valorArray($control, "presencas", "controlPresenca"));
                                        
                                        break;
                                    }
                                }
                            }
                            $this->html .="<td style='".$this->border().$this->text_center."'>".$presencas."</td>";
                            
                        }
                        $this->html .="<td style='".$this->border().$this->text_center."'>".$contPresenca."</td></tr>";
                }

                $this->html .="</table>
                <p style='".$this->text_center."'>".$this->rodape()."</p>".$this->assinaturaDirigentes(7); 

            $this->exibir("", "Control de Presença ".nomeMes($this->mes)." de ".$this->anoCivil, "A4", "landscape");
        }
      
    }

new mapaForcaTrabalho(__DIR__);
    
    
  
?>