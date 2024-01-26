<?php 
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');

    class ensinoBasicoKami extends funcoesAuxiliares{

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
            
            <img src='".$_SERVER['DOCUMENT_ROOT']."/angoschool/icones/bordasKami.png' style='position:absolute; margin-left:-32px; margin-top:-20px; width:780px; height:1095px;'>
            <body>";

            $qrCode = $this->qrCode("Nome d".$this->art1." Alun".$this->art1.": ".valorArray($this->sobreAluno, "nomeAluno")."; Nome da Instituição: ".valorArray($this->sobreUsuarioLogado, "nomeEscola")."; Curso/Opção: ".$this->nomeCurso."; Número Interno: ".valorArray($this->sobreAluno, "numeroInterno")."; Operador: ".valorArray($this->sobreUsuarioLogado, "nomeEntidade")."; Emitido aos:".$this->dataSistema, "../../../", 110, 110);
            $this->html .= $this->vistoDirectorMunicipal(45, $qrCode)."
              
                    <p style='".$this->text_center."'><img src='".$_SERVER['DOCUMENT_ROOT']."/angoschool/icones/insignia.jpg' style='width:70px; margin-top:15px;'></p>
                    <p style='".$this->text_center.$this->bolder.$this->maiuscula."margin-top:-10px; font-size:11pt;'>REPÚBLICA DE ANGOLA
                    <br/>COMPLEXO ESCOLAR N.º 293 NGULU A NEKANDA</p>
                    
                    <p style='".$this->text_center."margin-top:10px; font-size:14pt;'>ENSINO GERAL</p>
        
                
                <p style='".$this->text_center.$this->sublinhado."font-size:16pt; margin-top:0px; color:rgb(0, 176, 240)'>DECLARAÇÃO DE HABILITAÇÕES</p>
                
                <p style='line-height: 24px;".$this->text_justify."margin-left:60px; margin-top:20px; font-size:12pt;'>a) <span style='".$this->bolder.$this->sublinhado.$this->maiuscula."'>Miguel Jorge Maria</span>, Director do complexo Escolar nº 293, Ngulu a Nekanda, criado sob o decreto Executivo Conjunto n.º 81/018 de 06 de Junho.
                </p>
                 
                <p style='line-height: 24px;".$this->text_justify."margin-left:25px; text-indent:35px; margin-top:-10px;'>Declara que: <strong style='".$this->vermelha."'>".valorArray($this->sobreAluno, "nomeAluno")."</strong> = = = = = = = = = = = = = = = = = = = = = ==<br/>Filh".$this->art1." de ".tratarCamposVaziosComEComercial(valorArray($this->sobreAluno, "paiAluno"), 15)." e de ".tratarCamposVaziosComEComercial(valorArray($this->sobreAluno, "maeAluno"), 15).", nascid".$this->art1." aos ".dataExtensa(valorArray($this->sobreAluno, "dataNascAluno")).", natural ".valorArray($this->sobreAluno, "preposicaoComuna2")." ".valorArray($this->sobreAluno, "nomeComuna").", Município ".valorArray($this->sobreAluno, "preposicaoMunicipio2")." ".valorArray($this->sobreAluno, "nomeMunicipio").", Província ".valorArray($this->sobreAluno, "preposicaoProvincia2")." ".valorArray($this->sobreAluno, "nomeProvincia").", ".$this->identificacaoAluno.".
                <br/>Frenquentou neste Complexo Escolar no ano lectivo ".$this->anoFinalizado." b) <strong style='".$this->vermelha."'>".$this->classe. ".ª (";
                
                if($this->classe==7){
                    $this->html .="Sétima Classe";
                }else if($this->classe==8){
                    $this->html .="Oitava Classe";
                }else if($this->classe==9){
                    $this->html .="Nona Classe";
                }
                
                $this->html .=")</strong>, turma ".$this->turma.", sob o n.º ".completarNumero($this->numeroAnterior).", na Pauta n.º <strong>".$this->numeroPauta."</strong>, obtendo o Resultado Final de <strong style='".$this->vermelha."'>Transita</strong> com as seguintes classificações: 
                </p> 

                <div style='width:95%; margin-left:4%;'><br>
                <table style='width:50%; font-size:14pt; margin-top:-27px;".$this->tabela."'>";

                $this->html .="<tr><td style='".$this->border().$this->bolder.$this->text_center."'>DISCIPLINAS</td><td style='".$this->border().$this->bolder.$this->text_center."'>VALORES</td></tr>";
                
                $i=0;
                foreach ($this->notas as $disciplina) {
                   $i++;
                   if($i<=number_format((count($this->notas)/2), 0)){
                    $nomeDisciplina = $disciplina["abreviacaoDisciplina1"];
                    if($disciplina["idPNomeDisciplina"]==20 || $disciplina["idPNomeDisciplina"]==21){
                        $nomeDisciplina ="Língua Estrangeira";
                    }
                    $this->html .="<tr><td style='padding-left:4px;".$this->border()."'>".$nomeDisciplina."</td>".$this->retornarNotas($disciplina["mf"])."</tr>";
                   }
                }
                $this->html .="</table>
                
                <table style='width:50%; font-size:14pt; margin-top:-212px;".$this->tabela." margin-left:50%; height:220px; border:none;'>
                <tr><td style='".$this->border().$this->bolder.$this->text_center."'>DISCIPLINAS</td><td style='".$this->border().$this->bolder.$this->text_center."'>VALORES</td></tr>";
                $i=0;
                foreach ($this->notas as $disciplina) {
                   $i++;
                   if($i>number_format((count($this->notas)/2), 0)){
                    $nomeDisciplina = $disciplina["abreviacaoDisciplina1"];
                    if($disciplina["idPNomeDisciplina"]==20 || $disciplina["idPNomeDisciplina"]==21){
                        $nomeDisciplina ="Língua Estrangeira";
                    }
                    $this->html .="<tr><td style='padding-left:4px;".$this->border()."'>".$nomeDisciplina."</td>".$this->retornarNotas($disciplina["mf"])."</tr>";
                   }
                }
                      
            $this->html .="</table></div>
                <p style='padding-right:10px; margin-top:19px;".$this->text_justify."margin-left:25px; text-indent:35px;'>A presente Declaração destina-se para <strong>".$this->efeitoDeclaracao."</strong>.</span></p>

                <p style='line-height:23px; padding-right:10px;".$this->text_justify."margin-top:-10px;margin-left:25px; text-indent:35px;'>Por ser verdade e me ter sido solicitada e assim constar nos documentos que ficam arquivados na Secretaria, mandei passar a presente Declaração que vai por mim assinanda e autentivada com o carimbo a óleo em uso nesta Escola.</p>
        
                <p style='margin-left:25px; text-indent:35px;".$this->text_justify."'>Complexo Escolar nº 293, Ngulu a Nekanda, no Soyo, aos ".dataExtensa($this->dataSistema).".</p>";
                
                $this->html .=" <div style='margin-top: 0px;".$this->text_center." font-size:11pt;'>";
                $this->nomeDirigente(8);

                    $peda ="(Subdirector Pedagógico)";
                    if($this->sexoDirigente=="F"){
                        $peda ="(Subdirectora Pedagógica)";
                    }
                    $this->html .="
                    <div style='width: 50%;'>".$this->porAssinatura("Conferido por", "", "", strlen($this->nomeDirigente))."
                    </div>
                    <div style='width: 50%;margin-top:-300px; margin-left:50%;'>".$this->porAssinatura("O Director", "Miguel Jorge Maria", "", 30)."
                    </div>
                </div>
                <br/><br/><p style='margin-left:25px; margin-top:16px; font-size:11px;".$this->text_justify.$this->bolder."'>a) Director da Escola<br/>b) Classe por Extenso</p>
               
               </div>";
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
                $nota["mf"] = number_format(floatval($nota["mf"]), 0);
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
            
            <img src='".$_SERVER['DOCUMENT_ROOT']."/angoschool/icones/bordasKami2.png' style='position:absolute; margin-left:-48px; margin-top:-45px; width:810px; height:1145px;'>
            <body>
            
            <div style='height:1055px;'>";

            $qrCode = $this->qrCode("Nome d".$this->art1." Alun".$this->art1.": ".valorArray($this->sobreAluno, "nomeAluno")."; Nome da Instituição: ".valorArray($this->sobreUsuarioLogado, "nomeEscola")."; Curso/Opção: ".$this->nomeCurso."; Número Interno: ".valorArray($this->sobreAluno, "numeroInterno")."; Média Final do Curso: ".$PC."; Ano de Conclusão: ".$this->anoFinalizado."; Operador: ".valorArray($this->sobreUsuarioLogado, "nomeEntidade")."; Data de Emissão:".$this->dataSistema, "../../../", 110, 110);
            $this->html .= $this->vistoDirectorMunicipal(45, $qrCode)."<div><br/>
                    <p style='".$this->text_center.$this->miniParagrafo."margin-top:20px;'><img src='../../../icones/insigniaDeclaracao.jpg' class='text-center' style='width:45px; height:45px;'></p>
                    <p style='".$this->text_center.$this->miniParagrafo.$this->maiuscula."font-size:11pt;'>REPÚBLICA DE ANGOLA</p>
                    <p style='".$this->text_center.$this->miniParagrafo.$this->maiuscula." font-size:11pt;'>GOVERNO PROVINCIAL DO ZAIRE </p>
                    <p style='".$this->text_center.$this->maiuscula.$this->miniParagrafo."font-size:11pt;'>ADMINISTRAÇÃO MUNICIPAL DO SOYO</p>
                      <p  style='".$this->text_center."font-size: 11pt;'>DIRECÇÃO MUNICIPAL DA EDUCAÇÃO</p>

                      <p  style='font-size: 20pt;".$this->text_center.$this->bolder." margin-top:-5px;'>I CICLO DO ENSINO SECUNDÁRIO</p>
                      <p  style='font-size: 20pt; color: rgb(0, 176, 240);".$this->text_center.$this->bolder.$this->sublinhado." margin-top:-15px;'>CERTIFICADO DE HABILITAÇÕES LITERÁRIAS</p></div>";

                $this->html .="<div style='margin-top:-35px; padding:10px; margin-left:20px; margin-right:20px;'>
                    <p style='line-height: 22px;".$this->text_justify."'><span><span style='".$this->bolder.$this->maiuscula."'>MIGUEL JORGE MARIA</span</span>, Director do Complexo Escolar n.º 293, Ngulu a Nekanda, criado sob o decreto Executivo Conjunto n.º 81/018 de 06 de Junho, certifica que: <span style='".$this->bolder.$this->vermelha."'>".valorArray($this->sobreAluno, "nomeAluno")."</span>, filh".$this->art1." de ".valorArray($this->sobreAluno, "paiAluno")." e de ".valorArray($this->sobreAluno, "maeAluno").", nascid".$this->art1." aos ".dataExtensa(valorArray($this->sobreAluno, "dataNascAluno")).", natural ".valorArray($this->sobreAluno, "preposicaoComuna2")." ".valorArray($this->sobreAluno, "nomeComuna").", Município ".valorArray($this->sobreAluno, "preposicaoMunicipio2")." ".valorArray($this->sobreAluno, "nomeMunicipio").", Província ".valorArray($this->sobreAluno, "preposicaoProvincia2")." ".valorArray($this->sobreAluno, "nomeProvincia").", ".$this->identificacaoAluno.".
                    <br/>Concluiu o<strong> I Ciclo do Ensino Secundário Geral</strong>, conforme consta na Pauta Final n.º ".$this->numeroPauta." do Ano Lectivo ".$this->anoFinalizado.", folha n.º ".$this->numeroAnterior.", com a média final de <strong style='".$this->vermelha."'>".$PC."</strong> Valores, obtida nas seguintes classificações por ciclos de aprendizagem:</p>

                    <div style='width:100%; margin-top:15px;'>
                <table class='tabela padding0' style='width:100%; font-size:13pt;".$this->tabela."'>";

                $this->html .="<tr><td style='".$this->border().$this->bolder.$this->text_center."'>Disciplina</td><td style='".$this->border().$this->bolder.$this->text_center."'>7.ª<br/>Classe</td><td style='".$this->border().$this->bolder.$this->text_center."'>8.ª<br/>Classe</td><td style='".$this->border().$this->bolder.$this->text_center."'>9.ª<br/>Classe</td><td style='".$this->border().$this->bolder.$this->text_center."' style='padding:2px;".$this->border().$this->bolder.$this->text_center."'>Média<br/>Final</td><td style='".$this->border().$this->bolder.$this->text_center."'>Média por<br/>Extenso</td></tr>";

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

                    $this->html .="<tr><td style='".$this->border()." padding-left:6px;'>".$nomeDisciplina."</td>".$this->retornarNotas($nota7, "", "==").$this->retornarNotas($nota8, "", "==").$this->retornarNotas($nota9, "", "==").$this->retornarNotas($mediaDisciplina, "", "==")."<td style='".$this->text_center.$this->border()."'>".primeiraLetraMaiuscula($this->retornarNotaExtensa(number_format($mediaDisciplina, 0, ",", ".")))." Valores</td></tr>"; 
                }
                
                
            $this->html .="</table></div>
                <p style='line-height: 20px;".$this->text_justify."margin-top:18px,'>Para efeitos legais lhe é passado o presente Certificado, conforme o disposto na alínea c) do artigo 109º, da LBSEE 32/20 de 12 de Agosto, que vai por mim assinado e autenticado com carimbo a óleo em uso neste estabelecimento de Ensino.</p>
                <p style='line-height: 17px;".$this->text_justify.$this->bolder."margin-top:-10px;'>Complexo Escolar nº 293, Ngulu a Nekanda, no Soyo, aos ".dataExtensa($this->dataSistema)."</p>

                    <div style='margin-top: -10px;".$this->text_center."'>
                        <div style='width: 50%;'>".$this->porAssinatura("Conferido por", "", "", 27)."
                        </div>
                        <div style='width: 50%;margin-top:-300px; margin-left:50%;'>".$this->porAssinatura("O Director", "Miguel Jorge Maria", "", 30)."
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