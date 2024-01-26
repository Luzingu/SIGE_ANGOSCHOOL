<?php 
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');

    class ensinoBasico extends funcoesAuxiliares{

        function __construct(){
            parent::__construct();  
        } 

        public function declaracaoSemNotas(){

            $this->html .="
            <html style='margin-top: 50px; margin-left:70px; margin-right:70px;'> 
            <head>
                <title>Declaração de Frequência</title>
                <style>
                    p{
                        font-size:13pt;
                    }
                </style>
            </head>
            <body>
            <div style='margin:15px; margin-bottom:0px; height:1050px;'>".$this->cabecalho()."
                <p  style='font-size: 12pt; margin-top:15px;".$this->text_center.$this->bolder."'>DECLARAÇÃO DE FREQUÊNCIA</p>

                <div class='p13' style='margin-top:-20px; padding:10px;'>

                    <p style='line-height:30px;".$this->text_justify."'><strong style='".$this->maiuscula."'>".$this->nomeDirigente(7)."</strong>, Director do ".valorArray($this->sobreUsuarioLogado, "nomeEscola");
                    
                    if(valorArray($this->sobreUsuarioLogado, "privacidadeEscola")=="Pública"){
                       $this->html .=", criad".$this->art1Escola." sob o decreto Executivo ".valorArray($this->sobreUsuarioLogado, "decretoCriacaoInstituicao");
                    }
                    $this->html .=", declara que <strong style='".$this->vermelha.$this->maiuscula."'>".valorArray($this->sobreAluno, "nomeAluno")."</strong> filh".$this->art1." de ".tratarCamposVaziosComEComercial(valorArray($this->sobreAluno, "paiAluno"), 15)." e de ".tratarCamposVaziosComEComercial(valorArray($this->sobreAluno, "maeAluno"), 15).", nascid".$this->art1." aos ".dataExtensa(valorArray($this->sobreAluno, "dataNascAluno")).", natural ".valorArray($this->sobreAluno, "preposicaoComuna2")." ".valorArray($this->sobreAluno, "nomeComuna").", Município ".valorArray($this->sobreAluno, "preposicaoMunicipio2")." ".valorArray($this->sobreAluno, "nomeMunicipio").", Província ".valorArray($this->sobreAluno, "preposicaoProvincia2")." ".valorArray($this->sobreAluno, "nomeProvincia").", ".$this->identificacaoAluno.", matriculad".$this->art1." nesta instituição, frequenta neste ano lectivo ".$this->anoFinalizado." a ".classeExtensa($this, $this->idPCurso, $this->classe).", turma ".$this->turma.".</p>

                    <p style='line-height:25px; margin-top:20px;".$this->text_justify.$this->bolder."'>OBS: <span style='".$this->sublinhado."'>Esta Declaração destina-se para ".tratarCamposVaziosComEComercial($this->efeitoDeclaracao, 17).".</span></p>

                    <p style='line-height:30px; margin-top:20px;".$this->text_justify."'>Por ser verdade, e me ter solicitado, mandei passar a presente Declaração que vai por mim assinada e autenticada com o carimbo a óleo em uso neste Estabelecimento de Ensino.</p>

                     <p style='margin-top:25px;".$this->text_justify."'>".$this->rodape()."</p>


                    <div style='width: 50%; margin-left:25%; margin-top:30px;".$this->text_center."'>".$this->assinaturaDirigentes(7, "", "", "", "nao")."</div>
                   
            </div>
            </body> 


            </html>";

            $this->exibir("", "Declaração da ".classeExtensa($this, $this->idPCurso, $this->classe)." - ".valorArray($this->sobreAluno, "nomeAluno"));
        }

        public function declaracao(){
           
            $this->notaMinima=10;
            $this->nomeCurso();


            $notas = $this->notasDeclaracao($this->classe, $this->idPCurso);
            $notas = ordenar($notas, "ordenacao ASC");

            $this->notas = array();
            foreach ($notas as $nota) {
                if(!isset($nota["mf"]) || (nelson($nota, "recurso")!=NULL && nelson($nota, "recurso")!="")){
                    $nota["mf"]=nelson($nota, "recurso");
                }
                if(!isset($nota["mf"]) || (nelson($nota, "exameEspecial")!=NULL && nelson($nota, "exameEspecial")!="")){
                    $nota["mf"]=nelson($nota, "exameEspecial");
                }
                $nota["mf"] = number_format($nota["mf"], 0);
                if($nota["mf"]>0){
                    $this->notas[]=$nota;
                }
            }           

            $this->html .="<html style='margin:30px; margin-right:60px; margin-left:40px; font-family: Times New Roman !important;'>
            <head>
                <title>Declaração</title>
                <style>
                    p{
                        font-family: Times New Roman !important;
                        font-size: 13pt;
                    }
                </style>
            </head> 
            <body>
            <div style='height:1055px;'>
            <img src='".$_SERVER['DOCUMENT_ROOT']."/angoschool/icones/sombraGeral.png' style='position:absolute; margin-left:-40px; margin-top:-30px; width:780px; height:1114px;'>";
            $qrCode = $this->qrCode("Nome d".$this->art1." Alun".$this->art1.": ".valorArray($this->sobreAluno, "nomeAluno")."; Nome da Instituição: ".valorArray($this->sobreUsuarioLogado, "nomeEscola")."; Curso/Opção: ".$this->nomeCurso."; Número Interno: ".valorArray($this->sobreAluno, "numeroInterno")."; Operador: ".valorArray($this->sobreUsuarioLogado, "nomeEntidade")."; Data de Emissão:".$this->dataSistema, "../../../", 110, 110);
            $this->html .= $this->vistoDirectorMunicipal(45, $qrCode).$this->cabecalho()."     
                <p style='".$this->text_center.$this->bolder.$this->sublinhado."font-size:15pt; margin-top:0px;'>DECLARAÇÃO DE HABILITAÇÃO</p>
                <div class='p12' style='margin-top:-30px; padding:10px;'>
                    <p style='line-height: 21px;".$this->text_justify."'><span style='".$this->bolder.$this->sublinhado.$this->maiuscula."'>".$this->nomeDirigente(7)."</span>, Director do ".valorArray($this->sobreUsuarioLogado, "nomeEscola");
                if(valorArray($this->sobreUsuarioLogado, "decretoCriacaoInstituicao")!=""){
                   $this->html .=", criad".$this->art1Escola." sob o decreto Executivo ".valorArray($this->sobreUsuarioLogado, "decretoCriacaoInstituicao");
                }
                $this->html.= ", declara que <strong style='".$this->vermelha.$this->maiuscula."'>".valorArray($this->sobreAluno, "nomeAluno")."</strong>, filh".$this->art1." de ".tratarCamposVaziosComEComercial(valorArray($this->sobreAluno, "paiAluno"), 15)." e de ".tratarCamposVaziosComEComercial(valorArray($this->sobreAluno, "maeAluno"), 15).", nascid".$this->art1." aos ".dataExtensa(valorArray($this->sobreAluno, "dataNascAluno")).", natural ".valorArray($this->sobreAluno, "preposicaoComuna2")." ".valorArray($this->sobreAluno, "nomeComuna").", Município ".valorArray($this->sobreAluno, "preposicaoMunicipio2")." ".valorArray($this->sobreAluno, "nomeMunicipio").", Província ".valorArray($this->sobreAluno, "preposicaoProvincia2")." ".valorArray($this->sobreAluno, "nomeProvincia").", ".$this->identificacaoAluno.". Concluiu a <strong>".classeExtensa($this, $this->idPCurso, $this->classe). "</strong> no ano lectivo de <strong>".$this->anoFinalizado."</strong> conforme consta na pauta n.º <strong>".$this->numeroPauta."</strong> arquivada nesta Secretaria e obteve os seguintes resultados finais: 
                    </p> 
                </div>

                <div style='width:80%; margin-left:10%;'>
                <table style='width:100%; font-size:12pt; margin-top:-10px;".$this->tabela."'>";

                $this->html .="<tr><td style='".$this->border().$this->bolder.$this->text_center."'>Disciplinas</td><td style='".$this->border().$this->bolder.$this->text_center."'>Média Final</td><td style='".$this->border().$this->bolder.$this->text_center."'>Média por Extenso</td></tr>";
                
                foreach ($this->notas as $disciplina) {
                   
                    $nomeDisciplina = $disciplina["abreviacaoDisciplina1"];
                    if($disciplina["idPNomeDisciplina"]==20 || $disciplina["idPNomeDisciplina"]==21){
                        $nomeDisciplina ="Língua Estrangeira";
                    }else if($disciplina["idPNomeDisciplina"]==22 || $disciplina["idPNomeDisciplina"]==23){
                        $nomeDisciplina ="Língua Estrangeira (F.E)";
                    }
                    $this->html .="<tr><td style='padding-left:4px;".$this->border()."'>".$nomeDisciplina."</td>".$this->retornarNotas($disciplina["mf"])."<td style='".$this->border().$this->text_center."'>".primeiraLetraMaiuscula($this->retornarNotaExtensa(number_format($disciplina["mf"], 0, ",", ".")))."</td></tr>";
                }
                      
            $this->html .="</table></div>
                <p style='padding-left:10px; padding-right:10px;".$this->text_justify." margin-top:10px;'>Pelo que foi considerad".$this->art1." <span style='".$this->sublinhado."'>APT".strtoupper($this->art1)." &&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&</span></p>

                <p style='padding-left:10px; line-height:21px; padding-right:10px;".$this->text_justify."margin-top:-10px;'>Por ser verdade e me ter sido solicitada, mandei passar a presente declaração que vai por mim assinada e autenticada com o carrimbo a óleo em uso neste Colégio.</p>
        
                <p style='padding-left:10px; padding-right:10px;".$this->text_justify.$this->maiuscula.$this->bolder."'>".$this->rodape("sim").".</p>";
                
                if($_SESSION["idEscolaLogada"]==27){
                    $this->html .="
                        <div style='width: 100%;".$this->text_center."margin-top:-10px;'>".$this->assinaturaDirigentes(7, "", "", "", "nao")."
                        </div>";
                }else{
                    $this->html .="<div style='margin-top: -17px;".$this->text_center."'>";
                        $this->nomeDirigente(8);
                        $peda ="(Subdirector Pedagógico)";
                        if($this->sexoDirigente=="F"){
                            $peda ="(Subdirectora Pedagógica)";
                        }
                        $this->html .="
                        <div style='width: 50%;'>".$this->porAssinatura("Conferido por", $this->nomeDirigente."<br/>".$peda, "", strlen($this->nomeDirigente))."
                        </div>
                        <div style='width: 50%;margin-top:-300px; margin-left:50%;'>".$this->assinaturaDirigentes(7, "", "", "", "nao", "nao")."
                        </div>
                    </div>";
                }
                
               $this->html .="</div>";
                if($this->viaDocumento>1){
                   $this->html .="<p style='".$this->text_right.$this->maiuscula.$this->bolder.$this->vermelha."; margin-top:-35px; padding-right:20px;'>".$this->viaDocumento.".ª Via</p>";
                }
           $this->html .="</div>
        </body></html>";

            $this->exibir("", "Declaração da ".$this->classeExtensa." - ".valorArray($this->sobreAluno, "nomeAluno"));
        } 

        public function certificado(){

            $this->nomeCurso();

            $notas =array();
            for($i=7; $i<=9; $i++){
                $notas = array_merge($notas, $this->notasDeclaracao($i, $this->idPCurso));
            }
            $notas = ordenar($notas, "ordenacao ASC");
            
            $this->notas = array();
            foreach ($notas as $nota) {
                if(!isset($nota["mf"]) || (nelson($nota, "recurso")!=NULL && nelson($nota, "recurso")!="")){
                    $nota["mf"]=nelson($nota, "recurso");
                }
                if(!isset($nota["mf"]) || (nelson($nota, "exameEspecial")!=NULL && nelson($nota, "exameEspecial")!="")){
                    $nota["mf"]=nelson($nota, "exameEspecial");
                }
                $nota["mf"] = number_format($nota["mf"], 0);
                if($nota["mf"]>0){
                    $this->notas[]=$nota;
                }
            }
            
            $media7 = $this->calculadorMediaPorClasse(7);
            $media8 = $this->calculadorMediaPorClasse(8);
            $media9 = $this->calculadorMediaPorClasse(9);
            $PC = ($media7+$media8+$media9)/3;
            $PC = number_format($PC, 0);

            $this->html .="<html style='margin:30px; margin-right:60px; margin-left:40px; font-family: Times New Roman !important;'>
            <head>
                <title>Certificado de Habilitações</title>
                <style>
                    table tr td{
                        padding:0px;
                    }
                    p{
                        font-size:13pt;
                    }
                </style>
            </head>
            
            <body>
            
            <div style='height:1055px;'>
            <img src='".$_SERVER['DOCUMENT_ROOT']."/angoschool/icones/sombraGeral.png' style='position:absolute; margin-left:-40px; margin-top:-30px; width:780px; height:1100px;'>";
            $qrCode = $this->qrCode("Nome d".$this->art1." Alun".$this->art1.": ".valorArray($this->sobreAluno, "nomeAluno")."; Nome da Instituição: ".valorArray($this->sobreUsuarioLogado, "nomeEscola")."; Curso/Opção: ".$this->nomeCurso."; Número Interno: ".valorArray($this->sobreAluno, "numeroInterno")."; Média Final do Curso: ".$PC."; Ano de Conclusão: ".$this->anoFinalizado."; Operador: ".valorArray($this->sobreUsuarioLogado, "nomeEntidade")."; Data de Emissão:".$this->dataSistema, "../../../", 110, 110);
            $this->html .= $this->vistoDirectorMunicipal(45, $qrCode)."<div class='cabecalho'>
                    <p style='".$this->text_center.$this->miniParagrafo."margin-top:20px;'><img src='../../../icones/insigniaDeclaracao.jpg' class='text-center' style='width:65px; height:65px;'></p>
                    <p style='".$this->text_center.$this->miniParagrafo.$this->maiuscula."'  style='font-size:12pt;'>República de Angola</p>
                    <p style='".$this->text_center.$this->maiuscula."' style='font-size:12pt;'>Ministério da Educação </p>
                      <p  style='".$this->text_center."font-size: 12pt; margin-top:-10px;'>I CICLO DO ENSINO SECUNDÁRIO GERAL</p>

                      <p  style='font-size: 15pt; margin-top:10px;".$this->text_center.$this->bolder."'>CERTIFICADO</p>
                </div>";

                $this->html .="<div style='margin-top:-35px; padding:10px;'>
                    <p style='line-height: 22px;".$this->text_justify."'>a) <span style='".$this->sublinhado."'><span style='".$this->bolder.$this->maiuscula."'>".$this->nomeDirigente(7)."</span</span>, Director do ".valorArray($this->sobreUsuarioLogado, "nomeEscola");
                    
                    if(valorArray($this->sobreUsuarioLogado, "decretoCriacaoInstituicao")!=""){
                       $this->html .=", criad".$this->art1Escola." sob o decreto Executivo ".valorArray($this->sobreUsuarioLogado, "decretoCriacaoInstituicao");
                    }
                    $this->html .=", certifica que <span style='".$this->bolder.$this->vermelha.$this->maiuscula."'>".valorArray($this->sobreAluno, "nomeAluno")."</span>, filh".$this->art1." de ".valorArray($this->sobreAluno, "paiAluno")." e de ".valorArray($this->sobreAluno, "maeAluno").", natural ".valorArray($this->sobreAluno, "preposicaoComuna2")." ".valorArray($this->sobreAluno, "nomeComuna").", Município ".valorArray($this->sobreAluno, "preposicaoMunicipio2")." ".valorArray($this->sobreAluno, "nomeMunicipio").", Província ".valorArray($this->sobreAluno, "preposicaoProvincia2")." ".valorArray($this->sobreAluno, "nomeProvincia").", nascid".$this->art1." aos ".dataExtensa(valorArray($this->sobreAluno, "dataNascAluno")).", ".$this->identificacaoAluno.", concluiu no ano lectivo <strong>".$this->anoFinalizado."</strong>, o<strong> I CICLO DO ENSINO SECUNDÁRIO GERAL</strong>, conforme o disposto na alínea c) do artigo 109.º da <strong>LBSEE 17/16, de 7 de Outubro</strong>, com a Média Final de <strong>".$PC." (".ucwords($this->retornarNotaExtensa($PC)).") </strong>valores, obtida nas seguintes classificações por ciclos de aprendizagem:</p>

                    <div style='width:100%; margin-top:15px;'>
                <table class='tabela padding0' style='width:100%; font-size:13pt;".$this->tabela."'>";

                $this->html .="<tr><td style='".$this->border().$this->bolder.$this->text_center."'>Disciplina</td><td style='".$this->border().$this->bolder.$this->text_center."'>7.ª<br/>Classe</td><td style='".$this->border().$this->bolder.$this->text_center."'>8.ª<br/>Classe</td><td style='".$this->border().$this->bolder.$this->text_center."'>9.ª<br/>Classe</td><td style='".$this->border().$this->bolder.$this->text_center."' style='padding:2px;".$this->border().$this->bolder.$this->text_center."'>Média Final</td><td style='".$this->border().$this->bolder.$this->text_center."'>Média por<br/>Extenso</td></tr>";

                    foreach (distinct2($this->notas, "idPNomeDisciplina") as $idPNomeDisciplina) {
                        
                        $nomeDisciplina="";
                        $somaTotalNotas=0;
                        $contadorNotas=0;

                        $nota7="";
                        $nota8="";
                        $nota9="";

                        foreach (array_filter($this->notas, function($mamale) use ($idPNomeDisciplina){
                        return $mamale["idPNomeDisciplina"]==$idPNomeDisciplina;}) as $nota) {

                            $nomeDisciplina=$nota["abreviacaoDisciplina1"];

                            if($nota["idPNomeDisciplina"]==20 || $nota["idPNomeDisciplina"]==21 || $nota["idPNomeDisciplina"]==22 || $nota["idPNomeDisciplina"]==23){
                                $nomeDisciplina ="Língua Estrangeira";
                            }
                            
                            if($nota["classePauta"]==7){
                                $nota7 = $nota["mf"];
                                $contadorNotas++;
                                $somaTotalNotas +=$nota["mf"];
                            }else if($nota["classePauta"]==8){
                                $nota8 = $nota["mf"];
                                $contadorNotas++;
                                $somaTotalNotas +=$nota["mf"];
                            }else if($nota["classePauta"]==9){
                                $nota9 = $nota["mf"];
                                $contadorNotas++;
                                $somaTotalNotas +=$nota["mf"];
                            }
                        }
                        if($contadorNotas==0){
                            $mediaDisciplina=0;
                        }else{
                            $mediaDisciplina = number_format(($somaTotalNotas/$contadorNotas), 0);
                        }

                    $this->html .="<tr><td style='".$this->border()." padding-left:6px;'>".$nomeDisciplina."</td>".$this->retornarNotas($nota7, "", "==").$this->retornarNotas($nota8, "", "==").$this->retornarNotas($nota9, "", "==").$this->retornarNotas($mediaDisciplina, "", "==")."<td style='".$this->bolder.$this->text_center.$this->border()."'>".primeiraLetraMaiuscula($this->retornarNotaExtensa(number_format($mediaDisciplina, 0, ",", ".")))."</td></tr>"; 
                }
                
                
            $this->html .="</table></div>
                <p style='line-height: 20px;".$this->text_justify."margin-top:5px,'>Para efeitos legais lhe é passado o presente CERTIFICADO, que consta no livro n.º <strong>".$this->numeroPauta."</strong>, folha n.º <strong>".$this->numeroAnterior."</strong> assinado por mim e autenticado com carimbo a óleo em uso neste estabelecimento de ensino.</p>
                <p style='line-height: 17px;".$this->text_center.$this->bolder."'>".$this->rodape()."</p>

                    <div style='margin-top: -10px;".$this->text_center."'>";
                        $this->nomeDirigente(8);
                        $peda ="(Subdirector Pedagógico)";
                        if($this->sexoDirigente=="F"){
                            $peda ="(Subdirectora Pedagógica)";
                        }
                        $this->html .="
                        <div style='width: 50%;'>".$this->porAssinatura("Conferido por", $this->nomeDirigente."<br/>".$peda, "", strlen($this->nomeDirigente))."
                        </div>
                        <div style='width: 50%;margin-top:-300px; margin-left:50%;'>".$this->assinaturaDirigentes(7, "", "", "", "nao")."
                        </div>
                    </div>
                    
                    </div>";
                    if($this->viaDocumento>1){
                       $this->html .="<p style='".$this->text_right.$this->maiuscula.$this->bolder.$this->vermelha."; margin-top:-1px; padding-right:10px;'>".$this->viaDocumento.".ª Via</p>";
                    }                    
           $this->html .="
            </body></html>";

            $this->exibir("", "Certificado - ".valorArray($this->sobreAluno, "nomeAluno")); 
        }

        private function calculadorMediaPorClasse($classe){
            $totalDisc=0;
            $totalNotas=0;
            foreach ($this->notas as $nota) {
                if($nota["classePauta"]==$classe){
                    $nota["mf"] = number_format($nota["mf"], 0);
                    $totalNotas +=$nota["mf"];
                    $totalDisc++;
                }
            }
            if($totalNotas<=0){
                return 0;
            }else{
                return number_format(($totalNotas/$totalDisc), 0);
            }
        }
    }
?>