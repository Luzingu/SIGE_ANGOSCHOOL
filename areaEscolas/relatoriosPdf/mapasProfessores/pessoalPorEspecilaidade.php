<?php 
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');

    class mapaForcaTrabalho extends funcoesAuxiliares{

        function __construct($caminhoAbsoluto){
            parent::__construct("Rel-Lista de Pessoal por Especialidade");
            $this->idPAno = $this->idAnoActual;
            $this->numAno();
            $this->tamanhoFolha = isset($_GET["tamanhoFolha"])?$_GET["tamanhoFolha"]:"A3";
            
            $styleHtmlBody ="font-size: 12pt; margin-top: 10px; margin: 10px;";
            $this->html="<html style='".$styleHtmlBody."'>
            <head>
                <title>Lista de Pessoal Docente Por Especialidade</title>
            </head>
            <body style='".$styleHtmlBody."'>";

            if($this->verificacaoAcesso->verificarAcesso("", ["listaAgentes"], [], "")){
              $this->mapa();
            }else{
              $this->negarAcesso();
            }
        }

         private function mapa(){

           $this->lista = $this->entidades(array(), "docente", "V");

           $cabecalho[] = array('titulo' =>"Nome Completo", 'tituloDb'=>"nomeEntidade", "classCSS"=>"");
           $cabecalho[] = array('titulo' =>"Função", 'tituloDb'=>"funcaoEnt", "classCSS"=>"");
           $cabecalho[] = array('titulo' =>"Habilitações<br/>Literárias", 'tituloDb'=>"nivelAcademicoEntidade", "classCSS"=>"");
            $cabecalho[] = array('titulo' =>"Área de Formação ou Especialidade", 'tituloDb'=>"cursoP", "classCSS"=>"");
           $cabecalho[] = array('titulo' =>"Local de Formação", 'tituloDb'=>"localP", "classCSS"=>"");


            $this->html .="
            <div style='page-break-after: always;'>
            <div><div style='margin-top:20px; width:270px; position:absolute;"."'>".$this->assinaturaDirigentes(7)."</div></div>".$this->cabecalho();

            $this->html .="<p style='".$this->text_center.$this->bolder."'>LISTAGEM DO PESSOAL DOCENTE,  QUANTIDADE, ÁREAS DE FORMAÇÃO OU ESPECIALIDADE DE FORMAÇÃO E LOCAL DE FORMAÇÃO</p>
            <table style='".$this->tabela." width:100%;'><tr style='".$this->corDanger."'><td style='".$this->text_center.$this->border().$this->bolder."'>Nº</td>";
            foreach ($cabecalho as $cab) {
                    $this->html .="<td style='".$this->text_center.$this->border().$this->bolder."'>".$cab["titulo"]."</td>";
            }
            $this->html .="</tr>";


            $i=0;
            foreach ($this->lista as $profs) {
                $i++;
                if($i%2==0){
                   $this->html .="<tr style='".$this->backGround("rgb(220,220,220)")."'>";
                }else{
                    $this->html .="<tr>";
                }

                $this->html .="<td style='".$this->text_center.$this->border()."'>".completarNumero($i)."</t>";
               foreach ($cabecalho as $cab) {
                   $campo = $cab["tituloDb"];
                   if($campo=="funcaoEnt"){
                    $valorTabel = isset($profs["escola"][$campo])?$profs["escola"][$campo]:"";
                   }else{
                    $valorTabel = isset($profs[$campo])?$profs[$campo]:"";
                   }
                   $cssAdicional="";
                      if($cab["tituloDb"]=="cursoP" || $cab["tituloDb"]=="localP"){

                        if($profs["nivelAcademicoEntidade"]=="Médio"){
                           $areaFormacao = $profs["cursoEnsinoMedio"];
                           $escola = $profs["escolaEnsinoMedio"];
                        }else if($profs["nivelAcademicoEntidade"]=="Licenciado" || $profs["nivelAcademicoEntidade"]=="Bacharel"){
                            $areaFormacao = $profs["cursoLicenciatura"];
                            $escola = $profs["escolaLicenciatura"];
                        }else if($profs["nivelAcademicoEntidade"]=="Mestre"){
                            $areaFormacao = $profs["cursoMestrado"];
                            $escola = $profs["escolaMestrado"];
                        }else if($profs["nivelAcademicoEntidade"]=="Doutor"){
                           $areaFormacao = $profs["cursoDoutoramento"];
                           $escola = $profs["escolaDoutoramento"];
                        }else{
                            $areaFormacao="";
                            $escola ="";
                        }
                        if($cab["tituloDb"]=="cursoP"){
                            $valorTabel = $areaFormacao;
                        }else{
                            $valorTabel = $escola;
                        }
                    }
                   $this->html .="<td style='".$cab["classCSS"].$this->border()."'>".$valorTabel."</td>";
               }
               $this->html .="</tr>";
            }

            $this->html .="</table>";

            $this->html .="<div style='margin-top:-10px;'><p style='font-size:12pt;".$this->bolder."'>".$this->rodape()."</p><div style=''>".$this->assinaturaDirigentes("mengi")."</div><div>

            </div></div></div>";
            
            $this->exibir("", "", "", "Mapa de Levantamento da Força de Trabalho-".$this->numAno, "", $this->tamanhoFolha, "landscape");
        }
    }

new mapaForcaTrabalho(__DIR__);
    
    
  
?>