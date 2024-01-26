<?php if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');
 
    class cartoesAlunos extends funcoesAuxiliares{
        private $precoCartao=0;
        function __construct($caminhoAbsoluto){
            
            parent::__construct("Rel-Cartão de Aluno");

            $this->idPAno = isset($_GET["idPAno"])?$_GET["idPAno"]:$this->idAnoActual;
            $this->idPCurso = isset($_GET["idPCurso"])?$_GET["idPCurso"]:null;
            $this->classe = isset($_GET["classe"])?$_GET["classe"]:null;
            $this->turma = isset($_GET["turma"])?$_GET["turma"]:null;
            $this->idAlunosVisualizar = isset($_GET["idAlunosVisualizar"])?$_GET["idAlunosVisualizar"]:"";

            $this->html="<html style='margin:0px;'>
            <head>
                <title>Cartões de Estudantes</title>
                <style>
                    p{
                        font-size:10pt !important;
                    }
                </style>
            </head>
            <body style='margin:0px; margin-top:15px; margin-left:".((793-valorArray($this->sobreUsuarioLogado, "tamanhoCartEstudante"))/2)."px;'>";

            $this->nomeCurso();
            $this->numAno();
            if($this->verificacaoAcesso->verificarAcesso("", ["impressaoPersonalizada"], [$this->classe, $this->idPCurso], "")){                   
                $this->cartoesAlunos();
            }else{
              $this->negarAcesso();
            }
        }

        private function cartoesAlunos(){

            $idsVisualizar = array();
            foreach(explode(",", $this->idAlunosVisualizar) as $id){
                $idsVisualizar[]=intval($id);
            }
            $alunos = $this->alunosPorTurma($this->idPCurso, $this->classe, $this->turma, $this->idPAno, $idsVisualizar, ["nomeAluno", "idPMatricula", "numeroInterno", "fotoAluno", "reconfirmacoes.designacaoTurma", "escola.beneficiosDaBolsa", "pagamentos.idHistoricoAno", "pagamentos.referenciaPagamento", "pagamentos.idHistoricoEscola", "pagamentos.codigoEmolumento", "grupo"]);

            $this->nomeTurma("", "", "", $this->idPAno);

            if(valorArray($this->sobreUsuarioLogado, "insigniaUsar")=="escola"){
                $src = '../../../Ficheiros/Escola_'.$_SESSION['idEscolaLogada'].'/Icones/'.valorArray($this->sobreUsuarioLogado, "logoEscola");                
            }else{
                $src = '../../../icones/insignia.jpg';
            }
            $logotipo ="<img src='".$src."' style='width:35px; height:35px;'>";

            
            $fotoRubrica = valorArray($this->sobreUsuarioLogado, "assinatura1");
            
            $rubrica="";
            if($fotoRubrica!=""){
                $rubrica = "<img src='../../../Ficheiros/Escola_".$_SESSION["idEscolaLogada"]."/Icones/".$fotoRubrica."' style='width: 100px; height: 30px; margin-top:-10px; margin-bottom:-20px; position:top;'>";
            }

            
            
            //assGarciaFernandes.png
            if($_SESSION["idEscolaLogada"]==11){
                $assinatura = "<div style='margin-left:10px; width:110px; margin-top:15px;'><img src='".$_SERVER['DOCUMENT_ROOT']."/angoschool/icones/assGarciaFernandes.png' style='width: 90px; height: 58px; margin-top:-6px; margin-bottom:-20px; position:top;'>
            </div>";
            }else{
                $color="";
                if($_SESSION["idEscolaLogada"]==15){
                    $color="color:white;";
                }
                $assinatura = "<div style='margin-left:10px; width:110px; margin-top:20px;".$color."'>
                <p style='font-size:10px !important;'>O Director</p>
                <p style='text-align:center;'>".$rubrica."</p>
                <p style='font-size:10px !important;'>".$this->nomeDirigente(7)."</p>
                </div>";
            }
           

            $nomeEscola=valorArray($this->sobreUsuarioLogado, "nomeEscola");
            $borda = "solid black 1px";
            $margem2="";
            
            if($_SESSION["idEscolaLogada"]==27){
                $borda = "solid rgb(122, 6, 41) 8px";
                $nomeEscola="COMPLEXO ESCOLAR<br>SAGRADO CORAÇÃO DE JESUS";
                $margem2="margin-top:-7px;";
                $assinatura = "<p style='".$this->text_center.$this->bolder."font-size:10px !important;'>A DIRECÇÃO</p><div style='".$this->text_center.$this->bolder."margin-top:-10px;'><img src='../../../icones/iconAssSagrada.png' style='width:60px; height:50px;'></div>";
            }else if($_SESSION["idEscolaLogada"]==10){
                $nomeEscola="INSTITUTO POLITÉCNICO DE ADMINISTRAÇÃO E GESTÃO";
            }else if($_SESSION["idEscolaLogada"]==25){
                if($this->classe<=9 || $this->tipoCurso=="geral"){
                    $nomeEscola="COMPLEXO ESCOLAR<BR/>LADSSI"; 
                }else if($this->tipoCurso=="tecnico"){
                    if($this->especialidadeCurso=="saude"){
                        $nomeEscola="INSTIT. TÉCNICO PRIVADO DE SAÚDE<br/>LADSSI";
                    }else{
                        $nomeEscola="INSTITUTO MÉDIO POLITÉCNICO<br/>LADSSI";
                    }
                     
                }
                   
            }
            


           $this->html .="<table style='margin-bottom: 10px; border: none; width:".valorArray($this->sobreUsuarioLogado, "tamanhoCartEstudante")."px;'>";
            
            $corCart1 = valorArray($this->sobreUsuarioLogado, "corCart1");
            if($corCart1=="#ffffff"){
                $corCart1 ="transparent";
            }
            
            $nomeAlunoUnicoExibir="";
             $n=0;
            foreach ($alunos as $aluno) {

                $this->precoCartao = $this->preco("cartaoEstudante", $this->classe, $this->idPCurso, "", $aluno);

                $nomeAlunoUnicoExibir=$aluno["nomeAluno"];

                $n++;
                if($n%2==1){
                    $this->html .="<tr>";
                }

                $padding = valorArray($this->sobreUsuarioLogado, "alturaCartEstudante")-185;

                $this->html .="<td style='padding: 5px; padding-bottom:0px; width: 50%; border: none;'>
                <div style='border: ".$borda."; border-radius:15px; padding: 5px; padding-top:".($padding*0.1)."px; padding-bottom:-10px; text-align:center; height:".valorArray($this->sobreUsuarioLogado, "alturaCartEstudante")."px; background-color:".$corCart1."'>";

                if(!$this->seJaPagou($aluno)){
                    $this->html .="<div style='margin-top:30px; font-size:16pt;".$this->text_center."'>".$aluno["nomeAluno"]."</div><div style='margin-top:30px; font-size:16pt;".$this->text_center.$this->bolder.$this->vermelha."'>
                        AINDA NÃO FEZ PAGAMENTO DE CARTÃO DE ESTUDANTE
                    </div>";
                }else{
                    if($_SESSION["idEscolaLogada"]==33){
                        $this->html .="

                        <img style='position:absolute; opacity:0.1; width:".((valorArray($this->sobreUsuarioLogado, "tamanhoCartEstudante")/2)-40)."px; height:".valorArray($this->sobreUsuarioLogado, "alturaCartEstudante")."px;' src='".'../../../Ficheiros/Escola_'.$_SESSION['idEscolaLogada'].'/Icones/'.valorArray($this->sobreUsuarioLogado, "logoEscola")."'>

                        <div style='".$this->text_center." background-color:transparent; border-radius:15px; padding:3px; height:60%; '>
                        
                        <div style='margin-left:30%; width:70%; margin-bottom:-10px; background-color:transparent;'>".$logotipo."</div>
                            
                            <div style='".$this->text_left."width:30%; height:86px; padding-left:2px; background-color:transparent;'>
                                
                                <img src='../../../fotoUsuarios/".$aluno["fotoAluno"]."' style='width: 85px; height: 85px; margin-top:-20px;'>
                            </div>
                            <div style='".$this->text_center."width:70%; margin-top:-96px; height:86px; margin-left:30%; color:".valorArray($this->sobreEscolaLogada, "corLetrasCart")."; background-color:transparent;'>
    
                                <p style='".$this->text_center.$this->maiuscula.$this->bolder."font-size: 10px !important;'>".$nomeEscola."</p>
                                <p style='".$this->text_center.$this->maiuscula.$this->bolder."font-size: 10px !important; margin-top:-10px;'>".valorArray($this->sobreEscolaLogada, "nomeMunicipio")." / ".valorArray($this->sobreEscolaLogada, "nomeProvincia")."</p>
                                
                                <p style='".$this->text_center.$this->maiuscula.$this->bolder.";margin-top:-10px; color:black; font-size: 10px !important; margin-top:-5px;'>CARTÃO DE ESTUDANTE - ".$this->numAno."</p>
                            </div>
                            <div style='width:70%; border:solid ".valorArray($this->sobreEscolaLogada, "corBordasCart")." 2px; border-radius:15px;".$this->text_left." padding-left:5px; margin-left:2px; margin-top:-36px; background-color:transparent;".$margem2."height:80px;'>
                                
                                <p style='".$this->maiuscula."margin-top:3px;font-size:10px !important;'>NOME: <strong'>".substr(abreviarDoisNomes($aluno["nomeAluno"]), 0, 32);
                                if(strlen(abreviarDoisNomes($aluno["nomeAluno"]))>32){
                                    $this->html .=".";
                                }
                                $this->html .="</strong></p>
                                <p style='"."margin-top:-10px;font-size:10px !important;'>N.º INTERNO: <strong>".$aluno->numeroInterno."</strong></p>
                                <p style='".$this->maiuscula."margin-top:-10px;font-size:11px !important;'>";
                                
                                
                                $this->html .="CURSO: <strong>".$this->nomeCursoAbr."</strong> &nbsp;&nbsp;CLASSE: <strong>".$this->classeExt."</strong> &nbsp; TURMA: <strong>".$aluno["reconfirmacoes"]["designacaoTurma"]."</strong></p> 
    
                                <p style='".$this->maiuscula."margin-top:-10px;font-size:10px !important;'>TURNO: <strong>".valorArray($this->sobreTurma, "periodoT")."</strong> &nbsp; SALA N.º: <strong>".completarNumero(valorArray($this->sobreTurma, "numeroSalaTurma"))."</strong></p>
                                <p style='".$this->text_center."margin-top:-10px;font-size:10px !important;'>Válido até 30 de Agosto de ".explode("/",$this->numAno)[1]."</p>
                            </div>
                            <div style='width:30%; margin-left:65%; margin-top:-100px; color:".valorArray($this->sobreEscolaLogada, "corLetrasCart").";'>".$assinatura."
                            </div>
                        </div>";
                    }else if($_SESSION["idEscolaLogada"]==19){
                        $this->html .="<div style='".$this->text_center." background-color:".valorArray($this->sobreUsuarioLogado, "corCart2")."; border-radius:15px; padding:3px; height:60%; '>
    
                        <div style='margin-left:30%; width:70%; margin-bottom:-10px;'>".$logotipo."</div>
                            
                            <div style='".$this->text_left."width:30%; height:86px; padding-left:2px;'>
                                
                                <img src='../../../fotoUsuarios/".$aluno["fotoAluno"]."' style='width: 85px; height: 85px; margin-top:-20px;'>
                            </div>
                            <div style='".$this->text_center."width:70%; margin-top:-96px; height:86px; margin-left:30%; color:".valorArray($this->sobreEscolaLogada, "corLetrasCart").";'>
    
                                <p style='".$this->text_center.$this->maiuscula.$this->bolder."font-size: 10px !important;'>".$nomeEscola."</p>
                                <p style='".$this->text_center.$this->maiuscula.$this->bolder."font-size: 10px !important; margin-top:-10px;'>".valorArray($this->sobreEscolaLogada, "nomeMunicipio")." / ".valorArray($this->sobreEscolaLogada, "nomeProvincia")."</p>
                                
                                <p style='".$this->text_center.$this->maiuscula.$this->bolder.";margin-top:-10px; color:black; font-size: 10px !important; margin-top:-5px;'>CARTÃO DE ESTUDANTE - ".$this->numAno."</p>
                            </div>
                            <div style='width:70%; border:solid ".valorArray($this->sobreEscolaLogada, "corBordasCart")." 2px; border-radius:15px;".$this->text_left." padding-left:5px; margin-left:2px; margin-top:-36px;".$margem2." background-color:white; height:80px;'>
                                
                                <p style='".$this->maiuscula."margin-top:3px;font-size:10px !important;'>NOME: <strong'>".substr(abreviarDoisNomes($aluno["nomeAluno"]), 0, 32);
                                if(strlen(abreviarDoisNomes($aluno["nomeAluno"]))>32){
                                    $this->html .=".";
                                }
                                $this->html .="</strong></p>
                                <p style='"."margin-top:-10px;font-size:10px !important;'>N.º INTERNO: <strong>".$aluno->numeroInterno."</strong></p>
                                <p style='".$this->maiuscula."margin-top:-10px;font-size:11px !important;'>";
                                
                                
                                $this->html .="CURSO: <strong>".$this->nomeCursoAbr."</strong> &nbsp;&nbsp;CLASSE: <strong>".$this->classeExt."</strong> &nbsp; TURMA: <strong>".$aluno["reconfirmacoes"]["designacaoTurma"]."</strong></p> 
    
                                <p style='".$this->maiuscula."margin-top:-10px;font-size:10px !important;'>TURNO: <strong>".valorArray($this->sobreTurma, "periodoT")."</strong> &nbsp; SALA N.º: <strong>".completarNumero(valorArray($this->sobreTurma, "numeroSalaTurma"))."</strong></p>
                                <p style='".$this->text_center."margin-top:-10px;font-size:10px !important;'>Válido até 30 de Agosto de ".explode("/",$this->numAno)[1]."</p>
                            </div>
                            <div style='width:30%; margin-left:65%; margin-top:-100px; color:".valorArray($this->sobreEscolaLogada, "corLetrasCart").";'>".$assinatura."
                            </div>
                        </div>";
                    }else{

                        $this->html .="<div style='".$this->text_center." background-color:".valorArray($this->sobreUsuarioLogado, "corCart2")."; border-radius:15px; padding:3px; height:65%; '>
    
                        <div style='margin-left:30%; width:70%; margin-bottom:-10px;'>".$logotipo."</div>
                            
                            <div style='".$this->text_left."width:30%; height:86px; padding-left:2px;'>
                                
                                <img src='../../../fotoUsuarios/".$aluno["fotoAluno"]."' style='width: 100px; height: 100px; margin-top:-20px;'>
                            </div>
                            <div style='".$this->text_center."width:70%; margin-top:-96px; height:86px; margin-left:30%; color:".valorArray($this->sobreEscolaLogada, "corLetrasCart").";'> 
    
                                <p style='".$this->text_center.$this->maiuscula.$this->bolder.$this->miniParagrafo."'>".$nomeEscola."</p>
                                <p style='".$this->text_center.$this->maiuscula.$this->bolder."'>".valorArray($this->sobreEscolaLogada, "nomeMunicipio")." / ".valorArray($this->sobreEscolaLogada, "nomeProvincia")."</p>";
                               // if($_SESSION["idEscolaLogada"]==27 || $_SESSION["idEscolaLogada"]==1){
                                     $this->html .="<p style='".$this->text_center.$this->maiuscula.$this->bolder." font-size:20pt;margin-top:-10px; color:black'>CARTÃO DE ESTUDANTE - ".$this->numAno."</p>";
                                //}else{
                                    //$this->html .="<p style='".$this->text_center.$this->maiuscula.$this->bolder." font-size:20pt;margin-top:-10px; color:".valorArray($this->sobreEscolaLogada, "corCart1")."'>CARTÃO DE ESTUDANTE - ".$this->numAno."</p>";
                                //}
                            $this->html .="
                            </div>
                            <div style='width:70%; border:solid ".valorArray($this->sobreEscolaLogada, "corBordasCart")." 2px; border-radius:15px;".$this->text_left." padding-left:5px; margin-left:2px; margin-top:-26px;".$margem2." background-color:white; height:80px;'>
                                
                                <p style='".$this->maiuscula."margin-top:3px;font-size:11px !important;'>NOME: <strong'>".substr(abreviarDoisNomes($aluno["nomeAluno"]), 0, 32);
                                if(strlen(abreviarDoisNomes($aluno["nomeAluno"]))>32){
                                    $this->html .=".";
                                }
                                $this->html .="</strong></p>
                                <p style='"."margin-top:-10px;font-size:11px !important;'>N.º INTERNO: <strong>".$aluno->numeroInterno."</strong></p>
                                <p style='".$this->maiuscula."margin-top:-10px;font-size:11px !important;'>CURSO: <strong>".$this->nomeCursoAbr."</strong> &nbsp;&nbsp;CLASSE: <strong>".$this->classeExt."</strong> &nbsp; TURMA: <strong>".$aluno["reconfirmacoes"]["designacaoTurma"]."</strong></p> 
    
                                <p style='".$this->maiuscula."margin-top:-10px;font-size:11px !important;'>TURNO: <strong>".valorArray($this->sobreTurma, "periodoT")."</strong> &nbsp; SALA N.º: <strong>".completarNumero(valorArray($this->sobreTurma, "numeroSalaTurma"))."</strong></p>
                                <p style='".$this->text_center."margin-top:-10px;'>Válido até 30 de Agosto de ".explode("/",$this->numAno)[1]."</p>
                            </div>
                            <div style='width:30%; margin-left:70%; margin-top:-100px; color:".valorArray($this->sobreEscolaLogada, "corLetrasCart").";'>".$assinatura."
                            </div>
                        </div>";
                    }
                }

                $this->html .="</div></td>";

                //Adicionando um td para evitar exceder o tamanho...
                if(count($this->alunos)<=1){
                    $this->html .="<td></td>";
                }
                if($n%2==0 || ($n%2==1 && $n==count($this->alunos))){
                    $this->html .="</tr>";
                }         
            }
            $this->exibir("", "Cartão de Estudante-".$nomeAlunoUnicoExibir."-".$this->numAno);            
        }


        private function seJaPagou($aluno){
            if((int)$this->precoCartao<=0){
                return true;
            }else{
                $array = listarItensObjecto($aluno, "pagamentos", ["idHistoricoAno=".$this->idPAno, "idHistoricoEscola=".$_SESSION['idEscolaLogada'], "codigoEmolumento=cartaoEstudante"]);

                if(count($array)>0){

                    $this->editarItemObjecto("alunos_".$aluno["grupo"], "pagamentos", "estadoPagamento", ["A"], ["idPMatricula"=>valorArray($aluno, "idPMatricula")], ["idPHistoricoConta"=>valorArray($array, "idPHistoricoConta")]);

                    $this->editarItemObjecto("payments", "itens", "estadoItem", ["A"], ["identificadorCliente"=>valorArray($todos, "idPMatricula")], ["idPHistoricoConta"=>valorArray($array, "idPHistoricoConta")]);

                    return true;
                }else{
                    return false;
                }

            }
        }
    }

new cartoesAlunos(__DIR__);?>  