<?php if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../funcoesAuxiliares.php');
    include_once ('../funcoesAuxiliaresDb.php');
 
    class mapaForcaTrabalho extends funcoesAuxiliares{

        function __construct($caminhoAbsoluto){
            parent::__construct("Rel-Mapa de Avaliação de Desempenho");
            $this->idPAno = isset($_GET["idPAno"])?$_GET["idPAno"]:null;
            $this->tipoPessoal = isset($_GET["tipoPessoal"])?$_GET["tipoPessoal"]:"docente";
            $this->trimestre = isset($_GET["trimestre"])?$_GET["trimestre"]:"I";


            if(!($this->tipoPessoal=="docente" || $this->tipoPessoal=="naoDocente")){
                $this->tipoPessoal="docente";
            }
            if(!($this->trimestre=="I" || $this->trimestre=="II" || $this->trimestre=="III" || $this->trimestre=="IV")){
                $this->trimestre=="I";
            }
            if($this->trimestre=="IV"){
                $this->label ="FINAL";
            }else{
                $this->label ="DO ".$this->trimestre." TRIMESTRE";
            }


            $this->numAno();
            

            if($this->verificacaoAcesso->verificarAcesso("", "avaliacaoDesempenhoProfessor", array($this->classe, $this->idPCurso), "")){
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

            $factores[]=array("titulo"=>"Qualidade do processo de ensino aprendizagem", "tituloDb"=>"qualProcEnsAprend".$this->trimestre, "cotacao"=>0.2);
            $factores[]=array("titulo"=>"Progresso do aluno ou desenvolvimento do aluno", "tituloDb"=>"PA".$this->trimestre, "cotacao"=>0.3);
            $factores[]=array("titulo"=>"Responsabi<br>lidade", "tituloDb"=>"resposabilidade".$this->trimestre, "cotacao"=>0.1);
            $factores[]=array("titulo"=>"Aperfeiçoamento<br>profissional e<br>Inovação pedagógica", "tituloDb"=>"aperfProfissional".$this->trimestre, "cotacao"=>0.2);
            $factores[]=array("titulo"=>"Relações<br>Humanas", "tituloDb"=>"relHum".$this->trimestre, "cotacao"=>0.2);

            $this->html="<html style='margin-left:10px; margin-right:10px; margin-top:10px;'>
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
            <p style='".$this->text_center.$this->bolder." width:70%; margin-left:15%;'>MAPA DE AVALIAÇÃO DE DESEMPENHO DE PESSOAL DOCENTE ".$this->label."- ".$this->numAno."</p>";

            $this->html .="<table style='".$this->tabela." width:100%; font-size:9pt;'>
                <tr style='".$this->corDanger."'>
                    <td rowspan='3' style='width:15px;".$this->bolder.$this->text_center.$this->border()."'>Nº</td>
                    <td rowspan='3' style='width:100px;".$this->bolder.$this->text_center.$this->border()."'>Nome Completo</td>
                    <td rowspan='3' style='width:40px;".$this->bolder.$this->text_center.$this->border()."'>Agente</td>
                    <td rowspan='3'style='".$this->bolder.$this->text_center.$this->border()."width:120px;'>Categoria</td>
                    <td rowspan='3' style='".$this->bolder.$this->text_center.$this->border()."width:100px;'>Função</td>
                    <td colspan='".count($factores)."' style='".$this->bolder.$this->text_center.$this->border()."'>PONTUAÇÃO</td>

                    <td rowspan='2' colspan='2' style='".$this->bolder.$this->text_center.$this->border()."'>CLASSIFICAÇÃO<br>FINAL</td>
                </tr>
                <tr style='".$this->corDanger."'>
                    <td colspan='".count($factores)."' style='".$this->bolder.$this->text_center.$this->border()."'>PARCELAR</td>
                </tr>
                <tr style='".$this->corDanger."'>";
                    foreach($factores as $fact){
                        $this->html .="<td style='".$this->bolder.$this->text_center.$this->border()."'>".$fact["titulo"]."</td>";    
                    }
                $this->html .="
                <td style='".$this->bolder.$this->text_center.$this->border()."'>QUANTI<br>TATIVA</td>
                <td style='".$this->bolder.$this->text_center.$this->border()."'>QUALI<br>TATIVA</td></tr>";

                $i=0;
                foreach ($this->selectArray("entidadesprimaria", [], ["escola.idEntidadeEscola"=>$_SESSION['idEscolaLogada'], "escola.estadoActividadeEntidade"=>"A", "escola.tipoPessoal"=>"docente", "aval_desemp.idAvalEntAno"=>$this->idPAno, "aval_desemp.idAvalEntEscola"=>$_SESSION['idEscolaLogada']], ["escola", "aval_desemp"], "", [], ["nomeEntidade"=>1]) as $prof) { 
                    $i++;

                    $total=0;
                    foreach($factores as $fact){
                        $total +=number_format(floatval((isset($prof["aval_desemp"][$fact["tituloDb"]])?$prof["aval_desemp"][$fact["tituloDb"]]:0))*$fact["cotacao"], 1);
                    }
                    $total = number_format($total, 0);

                    $this->html .="<tr><td style='".$this->border().$this->text_center."'>".completarNumero($i)."</td><td style='".$this->border()."'>".$prof["nomeEntidade"]."</td><td style='".$this->border()."'>".$prof["numeroAgenteEntidade"]."</td><td style='".$this->border()."'>".$prof["categoriaEntidade"]."</td><td style='".$this->border()."'>".$prof["escola"]["funcaoEnt"]."</td>";

                    foreach($factores as $fact){
                        $this->html .="<td style='".$this->border().$this->text_center."'>".number_format(floatval((isset($prof["aval_desemp"][$fact["tituloDb"]])?$prof["aval_desemp"][$fact["tituloDb"]]:0))*$fact["cotacao"], 1)."</td>";
                    }
                    $this->html .="<td style='".$this->border().$this->bolder.$this->text_center."'>".$total."</td><td style='".$this->border().$this->text_center."'>".$this->classificacao($total)."</td></tr>";
                }
                $comissAval =$this->selectArray("comissAvalDesempProfessor", [], ["idEscola"=>$_SESSION['idEscolaLogada'], "idAno"=>$this->idPAno, "trimestre"=>$this->trimestre]);
                
                $coordenador =$this->selectArray("entidadesprimaria", ["nomeEntidade", "escola.funcaoEnt"], ["idPEntidade"=>valorArray($comissAval, "coordenador")], ["escola"]);

                $coordenadorAdjunto =$this->selectArray("entidadesprimaria", ["nomeEntidade", "escola.funcaoEnt"], ["idPEntidade"=>valorArray($comissAval, "coordenadorAdjunto")], ["escola"]);
                $vogal1 =$this->selectArray("entidadesprimaria", ["nomeEntidade", "escola.funcaoEnt"], ["idPEntidade"=>valorArray($comissAval, "vogal1")], ["escola"]);
                $vogal2 =$this->selectArray("entidadesprimaria", ["nomeEntidade", "escola.funcaoEnt"], ["idPEntidade"=>valorArray($comissAval, "vogal2")], ["escola"]);
                $vogal3 =$this->selectArray("entidadesprimaria", ["nomeEntidade", "escola.funcaoEnt"], ["idPEntidade"=>valorArray($comissAval, "vogal3")], ["escola"]);
                $secretario =$this->selectArray("entidadesprimaria", ["nomeEntidade", "escola.funcaoEnt"], ["idPEntidade"=>valorArray($comissAval, "secretario")], ["escola"]);

                $this->html .="</table>
                <p style='".$this->bolder.$this->text_center."'>".$this->rodape()."</p><br/>
                 <p style='".$this->bolder.$this->text_center."'>A COMISSÃO DE AVALIAÇÃO</p><br/>

                 <table style='width:80%; margin-left:10%;".$this->tabela." margin-top:-20px;'>
                    <tr><td style='".$this->border()."'>1.</td><td style='".$this->border()."'>Coordenador</td><td style='".$this->border()."'>".valorArray($coordenador, "nomeEntidade").": ".valorArray($coordenador, "funcaoEnt", "escola")."</td><td style='".$this->border()."width:300px;'></td></tr>

                    <tr><td style='".$this->border()."'>2.</td><td style='".$this->border()."'>Coordenador<br>Adjunto</td><td style='".$this->border()."'>".valorArray($coordenadorAdjunto, "nomeEntidade").": ".valorArray($coordenadorAdjunto, "funcaoEnt", "escola")."</td><td style='".$this->border()."w'></td></tr>

                    <tr><td style='".$this->border()."'>3.</td><td style='".$this->border()."'>1.º Vogal</td><td style='".$this->border()."'>".valorArray($vogal1, "nomeEntidade").": ".valorArray($vogal1, "funcaoEnt", "escola")."</td><td style='".$this->border()."w'></td></tr>
                    <tr><td style='".$this->border()."'>4.</td><td style='".$this->border()."'>2.º Vogal</td><td style='".$this->border()."'>".valorArray($vogal2, "nomeEntidade").": ".valorArray($vogal2, "funcaoEnt", "escola")."</td><td style='".$this->border()."w'></td></tr>
                    <tr><td style='".$this->border()."'>5.</td><td style='".$this->border()."'>3.º Vogal</td><td style='".$this->border()."'>".valorArray($vogal3, "nomeEntidade").": ".valorArray($vogal3, "funcaoEnt", "escola")."</td><td style='".$this->border()."w'></td></tr>
                    <tr><td style='".$this->border()."'>5.</td><td style='".$this->border()."'>Secretário</td><td style='".$this->border()."'>".valorArray($secretario, "nomeEntidade").": ".valorArray($secretario, "funcaoEnt", "escola")."</td><td style='".$this->border()."w'></td></tr>
                 </table></body></html>";
            
            $this->exibir("", "", "", "Mapa Geral de Avaliação de Desempenho ".$this->numAno, "A4", "landscape");
        }

        private function mapaPessoalNaoDocente(){

            $factores[]=array("titulo"=>"CAP", "tituloDb"=>"CAP", "cotacao"=>0.1);
            $factores[]=array("titulo"=>"Inte<br>resse", "tituloDb"=>"interesse", "cotacao"=>0.1);
            $factores[]=array("titulo"=>"Organi<br>zação", "tituloDb"=>"organizacao", "cotacao"=>0.1);
            $factores[]=array("titulo"=>"Criati<br>vidade", "tituloDb"=>"criatividade", "cotacao"=>0.1);
            $factores[]=array("titulo"=>"RIP", "tituloDb"=>"RIP", "cotacao"=>0.1);
            $factores[]=array("titulo"=>"Atenção", "tituloDb"=>"atencao", "cotacao"=>0.1);
            $factores[]=array("titulo"=>"PA", "tituloDb"=>"PA", "cotacao"=>0.1);
            $factores[]=array("titulo"=>"Disci<br>plina", "tituloDb"=>"disciplina", "cotacao"=>0.1);

            $this->html="<html style='margin-left:10px; margin-right:10px; margin-top:10px;'>
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
            <p style='".$this->text_center.$this->bolder." width:70%; margin-left:15%;'>MAPA DE AVALIAÇÃO DE DESEMPENHO DE PESSOAL NÃO DOCENTE - ".$this->numAno."</p>";

            $this->html .="<table style='".$this->tabela." width:100%; font-size:9pt;'>
                <tr style='".$this->corDanger."'>
                    <td rowspan='3' style='width:15px;".$this->bolder.$this->text_center.$this->border()."'>Nº</td>
                    <td rowspan='3".$this->bolder.$this->text_center.$this->border()."'>Nome Completo</td>
                    <td rowspan='3' style='".$this->bolder.$this->text_center.$this->border()."'>Agente</td>
                    <td rowspan='3'style='".$this->bolder.$this->text_center.$this->border()."'>Categoria</td>
                    <td rowspan='3' style='".$this->bolder.$this->text_center.$this->border()."'>Função</td>
                    <td colspan='".count($factores)."' style='".$this->bolder.$this->text_center.$this->border()."'>PONTUAÇÃO</td>

                    <td rowspan='2' colspan='2' style='".$this->bolder.$this->text_center.$this->border()."'>CLASSIFICAÇÃO<br>FINAL</td>
                </tr>
                <tr style='".$this->corDanger."'>
                    <td colspan='".count($factores)."' style='".$this->bolder.$this->text_center.$this->border()."'>PARCELAR</td>
                </tr>
                <tr style='".$this->corDanger."'>";
                foreach($factores as $fact){
                    $this->html .="<td style='".$this->bolder.$this->text_center.$this->border()."width:30px;'>".$fact["titulo"]."</td>";
                }
                $this->html .="<td style='".$this->bolder.$this->text_center.$this->border()."'>QUANTI<br>TATIVA</td>
                    <td style='".$this->bolder.$this->text_center.$this->border()."'>QUALI<br>TATIVA</td>
                </tr>";

                $i=0;
                foreach ($this->selectArray("entidadesprimaria", [], ["escola.idEntidadeEscola"=>$_SESSION['idEscolaLogada'], "escola.estadoActividadeEntidade"=>"A", "escola.tipoPessoal"=>"naoDocente", "aval_desemp.idAvalEntAno"=>$this->idPAno, "aval_desemp.idAvalEntEscola"=>$_SESSION['idEscolaLogada']], ["escola", "aval_desemp"], "", [], ["nomeEntidade"=>1]) as $prof) { 
                    $i++;

                    $total=0;
                    foreach($factores as $fact){
                        $total +=floatval(isset($prof["aval_desemp"][$fact["tituloDb"]])?$prof["aval_desemp"][$fact["tituloDb"]]:0)*0.1;
                    }

                    $this->html .="<tr><td style='".$this->border().$this->text_center."'>".completarNumero($i)."</td><td style='".$this->border()."'>".$prof["nomeEntidade"]."</td><td style='".$this->border()."'>".$prof["numeroAgenteEntidade"]."</td><td style='".$this->border()."'>".$prof["categoriaEntidade"]."</td><td style='".$this->border()."'>".$prof["escola"]["funcaoEnt"]."</td>";
                    foreach($factores as $fact){
                        $this->html .="<td style='".$this->border().$this->bolder.$this->text_center."'>".(floatval(isset($prof["aval_desemp"][$fact["tituloDb"]])?$prof["aval_desemp"][$fact["tituloDb"]]:0)*$fact["cotacao"])."</td>";   
                    }
                    $this->html .="<td style='".$this->border().$this->bolder.$this->text_center."'>".$total."</td><td style='".$this->border().$this->text_center."'>".$this->classificacao($total)."</td></tr>";
                }
                $comissAval =$this->selectArray("comissAvalDesempPessoalNaoDocente", [], ["idEscola"=>$_SESSION['idEscolaLogada'], "idAno"=>$this->idPAno]);
                $coordenador =$this->selectArray("entidadesprimaria", ["nomeEntidade", "escola.funcaoEnt"], ["idPEntidade"=>valorArray($comissAval, "coordenador")], ["escola"]);

                $coordenadorAdjunto =$this->selectArray("entidadesprimaria", ["nomeEntidade", "escola.funcaoEnt"], ["idPEntidade"=>valorArray($comissAval, "coordenadorAdjunto")], ["escola"]);
                $vogal1 =$this->selectArray("entidadesprimaria", ["nomeEntidade", "escola.funcaoEnt"], ["idPEntidade"=>valorArray($comissAval, "vogal1")], ["escola"]);
                $vogal2 =$this->selectArray("entidadesprimaria", ["nomeEntidade", "escola.funcaoEnt"], ["idPEntidade"=>valorArray($comissAval, "vogal2")], ["escola"]);
                $vogal3 =$this->selectArray("entidadesprimaria", ["nomeEntidade", "escola.funcaoEnt"], ["idPEntidade"=>valorArray($comissAval, "vogal3")], ["escola"]);
                $secretario =$this->selectArray("entidadesprimaria", ["nomeEntidade", "escola.funcaoEnt"], ["idPEntidade"=>valorArray($comissAval, "secretario")], ["escola"]);

                $this->html .="</table>
                <p style='".$this->bolder.$this->text_center."'>".$this->rodape()."</p><br/>
                 <p style='".$this->bolder.$this->text_center."margin-top:-17px;'>A COMISSÃO DE AVALIAÇÃO</p><br/>

                 <table style='width:80%; margin-left:10%;".$this->tabela." margin-top:-20px;'>
                    <tr><td style='".$this->border()."'>1.</td><td style='".$this->border()."'>Coordenador</td><td style='".$this->border()."'>".valorArray($coordenador, "nomeEntidade").": ".valorArray($coordenador, "funcaoEnt", "escola")."</td><td style='".$this->border()."width:300px;'></td></tr>

                    <tr><td style='".$this->border()."'>2.</td><td style='".$this->border()."'>Coordenador<br>Adjunto</td><td style='".$this->border()."'>".valorArray($coordenadorAdjunto, "nomeEntidade").": ".valorArray($coordenadorAdjunto, "funcaoEnt", "escola")."</td><td style='".$this->border()."w'></td></tr>

                    <tr><td style='".$this->border()."'>3.</td><td style='".$this->border()."'>1.º Vogal</td><td style='".$this->border()."'>".valorArray($vogal1, "nomeEntidade").": ".valorArray($vogal1, "funcaoEnt", "escola")."</td><td style='".$this->border()."w'></td></tr>
                    <tr><td style='".$this->border()."'>4.</td><td style='".$this->border()."'>2.º Vogal</td><td style='".$this->border()."'>".valorArray($vogal2, "nomeEntidade").": ".valorArray($vogal2, "funcaoEnt", "escola")."</td><td style='".$this->border()."w'></td></tr>
                    <tr><td style='".$this->border()."'>5.</td><td style='".$this->border()."'>3.º Vogal</td><td style='".$this->border()."'>".valorArray($vogal3, "nomeEntidade").": ".valorArray($vogal3, "funcaoEnt", "escola")."</td><td style='".$this->border()."w'></td></tr>
                    <tr><td style='".$this->border()."'>5.</td><td style='".$this->border()."'>Secretário</td><td style='".$this->border()."'>".valorArray($secretario, "nomeEntidade").": ".valorArray($secretario, "funcaoEnt", "escola")."</td><td style='".$this->border()."w'></td></tr>
                 </table></body></html>";
            
            $this->exibir("", "", "", "Mapa Geral de Avaliação de Desempenho ".$this->numAno, "A4", "landscape");
        }

       
        private function classificacao($classificacao){
            if($classificacao<=9){
                return "Mau";
            }else if($classificacao<14){
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