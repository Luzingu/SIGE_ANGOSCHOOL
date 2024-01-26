<?php 
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    if(!isset($_SESSION["directorioPaterno"])){
      $protocolo = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS']=="on") ? "https" : "http");
      echo "<script>window.location='".$protocolo."://".$_SERVER["HTTP_HOST"]."'</script>";
    }
    include_once $_SESSION["directorioPaterno"].'angoschool/areaEscolas/funcoesAuxiliares.php';
    include_once $_SESSION["directorioPaterno"].'angoschool/areaEscolas/funcoesAuxiliaresDb.php';

    class mapaForcaTrabalho extends funcoesAuxiliares{

        function __construct($caminhoAbsoluto){
            $this->caminhoRetornar = retornarCaminhoRecuarArquivosPhp(__DIR__);

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

            if($this->verificacaoAcesso->verificarAcessoAlteracao(["aDirectoria", "aAdministrativa", "aPedagogica"], "", "", "")){ 
              $this->mapa();
            }else{
              $this->negarAcesso();
            }
        }

         private function mapa(){
           $lista = $this->selectArray("entidadesprimaria LEFT JOIN dadosadicionasentidade ON idDAEntidade=idPEntidade LEFT JOIN entidade_escola ON idFEntidade=idPEntidade", "*", "estadoActividadeEntidade=:estadoActividadeEntidade AND idEntidadeEscola=:idEntidadeEscola AND tipoPessoal=:tipoPessoal AND efectividade=:efectividade", ["A", $_SESSION["idEscolaLogada"], "docente", "V"], "nomeEntidade ASC");
           $this->lista = $lista;

           $cabecalho[] = array('titulo' =>"Nome Completo", 'tituloDb'=>"nomeEntidade", "classCSS"=>"");
           $cabecalho[] = array('titulo' =>"Função", 'tituloDb'=>"funcaoEnt", "classCSS"=>"");
           $cabecalho[] = array('titulo' =>"Habilitações<br/>Literárias", 'tituloDb'=>"nivelAcademicoEntidade", "classCSS"=>"");
            $cabecalho[] = array('titulo' =>"Área de Formação ou Especialidade", 'tituloDb'=>"cursoP", "classCSS"=>"");
           $cabecalho[] = array('titulo' =>"Local de Formação", 'tituloDb'=>"localP", "classCSS"=>"");


            $this->html .="
            <div style='page-break-after: always;'>
            <div><div style='margin-top:20px; width:400px; position:absolute;"."'>".$this->assinaturaDirigentes("Director")."</div></div>".$this->cabecalho();

            $this->html .="<p style='".$this->text_center.$this->bolder."'>LISTAGEM DO PESSOAL DOCENTE,  QUANTIDADE, ÁREAS DE FORMAÇÃO OU ESPECIALIDADE DE FORMAÇÃO E LOCAL DE FORMAÇÃO</p>
            <table style='".$this->tabela." width:100%;'><tr style='".$this->corDanger."'><td style='".$this->text_center.$this->border().$this->bolder."'>Nº</td>";
            foreach ($cabecalho as $cab) {
                    $this->html .="<td style='".$this->text_center.$this->border().$this->bolder."'>".$cab["titulo"]."</td>";
            }
            $this->html .="</tr>";


            $i=0;
            foreach ($lista as $profs) {
                $i++;
                if($i%2==0){
                   $this->html .="<tr style='".$this->backGround("rgb(220,220,220)")."'>";
                }else{
                    $this->html .="<tr>";
                }

                $this->html .="<td style='".$this->text_center.$this->border()."'>".completarNumero($i)."</t>";
               foreach ($cabecalho as $cab) {
                   $campo = $cab["tituloDb"];
                   $valorTabel = isset($profs->$campo)?$profs->$campo:"";
                   $cssAdicional="";
                      if($cab["tituloDb"]=="cursoP" || $cab["tituloDb"]=="localP"){

                        if($profs->nivelAcademicoEntidade=="Médio"){
                           $areaFormacao = $profs->cursoEnsinoMedio;
                           $escola = $profs->escolaEnsinoMedio;
                        }else if($profs->nivelAcademicoEntidade=="Licenciado" || $profs->nivelAcademicoEntidade=="Bacharel"){
                            $areaFormacao = $profs->cursoLicenciatura;
                            $escola = $profs->escolaLicenciatura;
                        }else if($profs->nivelAcademicoEntidade=="Mestre"){
                            $areaFormacao = $profs->cursoMestrado;
                            $escola = $profs->escolaMestrado;
                        }else if($profs->nivelAcademicoEntidade=="Doutor"){
                           $areaFormacao = $profs->cursoDoutoramento;
                           $escola = $profs->escolaDoutoramento;
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

            $this->html .="<div style='margin-top:20px;'><p style='font-size:16pt;".$this->bolder."'>".$this->rodape()."</p><br/><br/><div style='"."'>".$this->assinaturaDirigentes(["Pedagógico", "Administrativo", "Chefe da Secretaria"])."</div><div>

            </div></div></div>";
            
            $this->exibir("", "", "", "Mapa de Levantamento da Força de Trabalho-".$this->numAno, "", $this->tamanhoFolha, "landscape");
        }
    }

new mapaForcaTrabalho(__DIR__);
    
    
  
?>