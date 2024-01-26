<?php 
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../../../funcoesAuxiliares.php');
    include_once ('../../../funcoesAuxiliaresDb.php');

    class ensinoBasicoCEPP extends funcoesAuxiliares{

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

                    <p style='line-height:30px;".$this->text_justify."'><strong style='".$this->maiuscula."'>".$this->nomeDirigente("Director")."</strong>, Director do ".valorArray($this->sobreUsuarioLogado, "nomeEscola");
                    
                    if(valorArray($this->sobreUsuarioLogado, "decretoCriacaoInstituicao")!=""){
                       $this->html .=", criad".$this->art1Escola." sob o decreto Executivo ".valorArray($this->sobreUsuarioLogado, "decretoCriacaoInstituicao");
                    }
                    $this->html .=", declara que <strong style='".$this->vermelha.$this->maiuscula."'>".valorArray($this->alunos, "nomeAluno")."</strong> filh".$this->art1." de ".tratarCamposVaziosComEComercial(valorArray($this->alunos, "paiAluno"), 15)." e de ".tratarCamposVaziosComEComercial(valorArray($this->alunos, "maeAluno"), 15).", nascid".$this->art1." aos ".dataExtensa(valorArray($this->alunos, "dataNascAluno")).", natural ".valorArray($this->alunos, "preposicaoComuna2")." ".valorArray($this->alunos, "nomeComuna").", Município ".valorArray($this->alunos, "preposicaoMunicipio2")." ".valorArray($this->alunos, "nomeMunicipio").", Província ".valorArray($this->alunos, "preposicaoProvincia2")." ".valorArray($this->alunos, "nomeProvincia").", ".$this->identificacaoAluno.", matriculad".$this->art1." nesta instituição, frequenta neste ano lectivo ".$this->anoFinalizado." a ".classeExtensa($this, $this->idPCurso, $this->classe).", turma ".$this->turma.".</p>

                    <p style='line-height:25px; margin-top:20px;".$this->text_justify.$this->bolder."'>OBS: <span style='".$this->sublinhado."'>Esta Declaração destina-se para ".tratarCamposVaziosComEComercial($this->efeitoDeclaracao, 17).".</span></p>

                    <p style='line-height:30px; margin-top:20px;".$this->text_justify."'>Por ser verdade, e me ter solicitado, mandei passar a presente Declaração que vai por mim assinada e autenticada com o carimbo a óleo em uso neste Estabelecimento de Ensino.</p>

                     <p style='margin-top:25px;".$this->text_justify."'>".$this->rodape()."</p>


                    <div style='width: 50%; margin-left:25%; margin-top:30px;".$this->text_center."'>".$this->assinaturaDirigentes(7, "", "", "", "nao")."</div>
                   
            </div>
            </body> 


            </html>";

            $this->exibir("", "Declaração da ".classeExtensa($this, $this->idPCurso, $this->classe)." - ".valorArray($this->alunos, "nomeAluno"));
        }

        public function declaracao(){
           
            $this->notaMinima=10;
            $this->nomeCurso();

            $notas = $this->notasDeclaracao($this->classe, $this->idPCurso);
            
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
            
            <img src='CEPP/sombraICicloCEPP.png' style='position:absolute; margin-left:-32px; margin-top:-30px; width:780px; height:1115px;'>
            <body>";

            $qrCode = $this->qrCode("Nome d".$this->art1." Alun".$this->art1.": ".valorArray($this->sobreAluno, "nomeAluno")."; Nome da Instituição: ".valorArray($this->sobreUsuarioLogado, "nomeEscola")."; Curso/Opção: ".$this->nomeCurso."; Número Interno: ".valorArray($this->sobreAluno, "numeroInterno")."; Operador: ".valorArray($this->sobreUsuarioLogado, "nomeEntidade")."; Emitido aos:".$this->dataSistema, "../../../", 110, 110);
            $this->html .= $this->vistoDirectorMunicipal(45, $qrCode)."
              
                <p style='".$this->text_center."margin-top:15px;'><img src='".$_SERVER['DOCUMENT_ROOT']."/angoschool/icones/insignia.jpg' style='width:50px; margin-top:15px;'></p>
                <p style='".$this->text_center.$this->bolder."margin-top:-17px; font-size:23pt;'>República de Angola
                <br/>Ministério da Educação<br/>Ensino Geral</p>
        
                
                <p style='".$this->text_center.$this->sublinhado."font-size:12pt; margin-top:-10px;'>DECLARAÇÃO DE HABILITAÇÕES LITERÁRIAS</p>
                
                <p style='line-height: 23px;".$this->text_justify."margin-left:35px; margin-right:30px; text-indent:63px; margin-top:17px; font-size:12pt;'><span style='".$this->bolder."'>Manuel Faustino Gomes, Directora do Colégio Dr. António Agostinho Neto, em Mbanza-Kongo.</span>
                </p>
                
                <p style='line-height: 23px;".$this->text_justify."margin-left:35px;margin-top:-17px;margin-right:30px;'>Declaro que: <strong style='".$this->vermelha."'>".valorArray($this->alunos, "nomeAluno")."</strong>, filh".$this->art1." de ".tratarCamposVaziosComEComercial(valorArray($this->alunos, "paiAluno"), 15)." e de ".tratarCamposVaziosComEComercial(valorArray($this->alunos, "maeAluno"), 15).", natural ".valorArray($this->alunos, "preposicaoComuna2")." ".valorArray($this->alunos, "nomeComuna").", Município ".valorArray($this->alunos, "preposicaoMunicipio2")." ".valorArray($this->alunos, "nomeMunicipio").", Província ".valorArray($this->alunos, "preposicaoProvincia2")." ".valorArray($this->alunos, "nomeProvincia").", nascid".$this->art1." aos ".dataExtensa(valorArray($this->alunos, "dataNascAluno")).", ".$this->identificacaoAluno.".</p>
                
                <p style='line-height: 23px;".$this->text_justify."margin-left:35px; margin-right:30px; text-indent:63px; margin-top:-2px; font-size:12pt;'>Concluiu neste Estabelecimento, a <strong>".$this->classe.".ª</strong> Classe, Turma: <strong>".$this->turma." (Complexo Escolar Privado Progresso)</strong>, no ano lectivo de <strong>".$this->anoFinalizado."</strong>. Tendo ficado <strong>Apt".$this->art1."</strong>, conforme consta na pauta, folha n.º <strong>".completarNumero($this->numeroAnterior)."</strong>, arquivada na secretaria deste Colégio, com as seguintes classificações: 
                </p> 

                <div style='width:90%; margin-left:5%;'>
                <table style='width:100%; font-size:12pt; margin-top:-5px;".$this->tabela."'>";

                $this->html .="<tr><td style='".$this->border().$this->bolder.$this->text_center."'>ORD</td><td style='".$this->border().$this->bolder.$this->text_center."'>DISCIPLINAS</td><td style='".$this->border().$this->bolder.$this->text_center."'>VALORES</td><td style='".$this->border().$this->bolder.$this->text_center."'>OBSERVAÇÃO</td></tr>";
                $i=0;
                foreach ($this->notas as $disciplina) {
                   $i++;
                    $nomeDisciplina = $disciplina["abreviacaoDisciplina1"];
                    
                    $this->html .="<tr><td style='padding-left:4px;".$this->border().$this->text_center."'>".completarNumero($i)."</td><td style='padding-left:4px;".$this->border()."'>".$nomeDisciplina."</td><td style='".$this->border()."padding-left:5px;padding-right:5px;'>(".$this->retornarNotas($disciplina["mf"], "", "", "sim").") Valores</td><td style='".$this->border()."'></td></tr>";
                }
                      
            $this->html .="</table></div>

                <p style='line-height:23px;margin-right:30px; padding-right:10px;".$this->text_justify."margin-top:10px;margin-left:35px;'>Por ser verdade e assim constar, se passou a presente Declaração que vai por mim assinada e autenticada com o carimbo à óleo em uso nesta Direcção.</p>
        
                <p style='margin-left:35px;margin-right:30px;".$this->text_justify.$this->bolder."'>Direcção do Colégio Dr. António Agostinho Neto, em Mbanza-Kongo, aso 17 de Novembro de 2022</p>";
                
                
                    $this->html .="<div style='margin-top: -10px;".$this->text_center."'>
                        <div style='width: 50%;'>".$this->porAssinatura("O Subdirector Pedagógico", "Manuel Mvinda", "", 28)."
                        </div>
                        <div style='width: 50%;margin-top:-300px; margin-left:50%;'>".$this->porAssinatura("O Director", "Manuel Faustino Gomes", "", 28)."
                        </div>
                    </div>";
                
                
               
               $this->html .="<p style='margin-left:35px; margin-top:42px; font-size:12pt;".$this->text_justify."'>Conta n.º ...........................................</p>
               
               </div>";
                if($this->viaDocumento>1){
                   $this->html .="<p style='".$this->text_right.$this->maiuscula.$this->bolder.$this->vermelha."; margin-top:-35px; padding-right:20px;'>".$this->viaDocumento.".ª Via</p>";
                }
           $this->html .="</div>
        </body></html>";

            $this->exibir("", "Declaração da ".$this->classeExtensa." - ".valorArray($this->alunos, "nomeAluno"));
        } 

        public function certificado(){

            $this->nomeCurso();

            $notas =array();
            for($i=7; $i<=9; $i++){
                $notas = array_merge($notas, $this->notasDeclaracao($i, $this->idPCurso));
            }
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
            
            <div style='height:1055px;'>";

            $qrCode = $this->qrCode("Nome d".$this->art1." Alun".$this->art1.": ".valorArray($this->sobreAluno, "nomeAluno")."; Nome da Instituição: ".valorArray($this->sobreUsuarioLogado, "nomeEscola")."; Curso/Opção: ".$this->nomeCurso."; Número Interno: ".valorArray($this->sobreAluno, "numeroInterno")."; Média Final do Curso: ".$PC."; Ano de Conclusão: ".$this->anoFinalizado."; Operador: ".valorArray($this->sobreUsuarioLogado, "nomeEntidade")."; Data de Emissão:".$this->dataSistema, "../../../", 110, 110);
            $this->html .= $this->vistoDirectorMunicipal(45, $qrCode)."<div><br/>
                    <p style='".$this->text_center.$this->miniParagrafo."margin-top:20px;'><img src='".$_SERVER['DOCUMENT_ROOT']."/angoschool/icones/insignia.jpg' style='width:60px; height:60px; margin-top:15px;'></p>
                    <p style='".$this->text_center.$this->miniParagrafo.$this->maiuscula."font-size:11pt;'>REPÚBLICA DE ANGOLA</p>
                    <p style='".$this->text_center.$this->miniParagrafo.$this->maiuscula." font-size:11pt;'>MINISTÉRIO DA EDUCAÇÃO</p>

                      <p  style='font-size: 12pt;".$this->text_center.$this->bolder." margin-top:30px;'>I CICLO DO ENSINO SECUNDÁRIO</p>
                      <p  style='font-size: 12pt;".$this->text_center.$this->bolder." margin-top:0px;'>CERTIFICADO</p>                      
                    </div>";

                $this->html .="<div style='margin-top:-28px; padding:10px; margin-left:20px; margin-right:20px;'>
                    <p style='line-height: 25px;".$this->text_justify."'><span><span style='".$this->bolder."'>Manuel Faustino Gomes</span</span>, Director do Colégio Dr. António Agostinho Neto em Mbanza Kongo, criado sob o decreto Executivo Conjunto n.º 131/12 de Abril, certifica que: <span style='".$this->bolder.$this->vermelha."'>".valorArray($this->alunos, "nomeAluno")."</span>, filh".$this->art1." de ".valorArray($this->alunos, "paiAluno")." e de ".valorArray($this->alunos, "maeAluno").", nascid".$this->art1." aos ".dataExtensa(valorArray($this->alunos, "dataNascAluno")).", natural ".valorArray($this->alunos, "preposicaoComuna2")." ".valorArray($this->alunos, "nomeComuna").", Município ".valorArray($this->alunos, "preposicaoMunicipio2")." ".valorArray($this->alunos, "nomeMunicipio").", Província ".valorArray($this->alunos, "preposicaoProvincia2")." ".valorArray($this->alunos, "nomeProvincia").", ".$this->identificacaoAluno.",
                    oncluiu no ano lectivo <strong>".$this->anoFinalizado."</strong>, o I CICLO DO ENSINO SECUNDÁRIO GERAL, conforme o disposto na alínea c) do artigo 109º da LBSEE nº 17/16 de 7 de Outubro com Média Final de <strong>".$PC." (".primeiraLetraMaiuscula($this->retornarNotaExtensa(number_format($PC, 0, ",", "."))).")</strong> valores obtida por ciclo nas seguintes classificações por ciclos de aprendizagem:</p>

                    <div style='width:100%; margin-top:15px;'>
                <table class='tabela padding0' style='width:100%; font-size:12pt;".$this->tabela."'>";

                $this->html .="<tr><td style='".$this->border().$this->bolder.$this->text_center."'>Disciplina</td><td style='".$this->border().$this->bolder.$this->text_center."'>7.ª Classe</td><td style='".$this->border().$this->bolder.$this->text_center."'>8.ªClasse</td><td style='".$this->border().$this->bolder.$this->text_center."'>9.ª Classe</td><td style='".$this->border().$this->bolder.$this->text_center."' style='padding:2px;".$this->border().$this->bolder.$this->text_center."'>Média Final</td><td style='".$this->border().$this->bolder.$this->text_center."'>Média por Extenso</td></tr>";

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

                    $this->html .="<tr><td style='".$this->border()." padding-left:6px;'>".$nomeDisciplina."</td>".$this->retornarNotas($nota7, "", "==").$this->retornarNotas($nota8, "", "==").$this->retornarNotas($nota9, "", "==").$this->retornarNotas($mediaDisciplina, "", "==")."<td style='".$this->text_center.$this->border()."'>".primeiraLetraMaiuscula($this->retornarNotaExtensa(number_format($mediaDisciplina, 0, ",", ".")))."</td></tr>"; 
                }
                
                
            $this->html .="</table></div>
                <p style='line-height: 25px;".$this->text_justify."margin-top:10px,'>Para efeitos legais lhe é passado o presente CERTIFICADO que consta no livro de registo n.º ".$this->numeroPauta.", folha ".completarNumero($this->numeroAnterior).", assinado por mim e autenticado com carimbo a óleo em uso neste Estabelecimento de ensino.</p>
                <p style='line-height: 25px;".$this->text_justify.$this->maiuscula.$this->bolder."margin-top:15px; font-size:12pt;'>COLÉGIO DR. ANTÓNIO AGOSTINHO NETO EM MBANZA-KONGO, AOS ".dataExtensa($this->dataSistema)."</p>

                    <div style='margin-top: -10px;".$this->text_center."'>
                        <div style='width: 50%;'>".$this->porAssinatura("Conferido por", "", "", 27)."
                        </div>
                        <div style='width: 50%;margin-top:-300px; margin-left:50%;'>".$this->porAssinatura("O Director", "", "", 27)."
                        </div>
                    </div>
                    
                    </div>";
                    if($this->viaDocumento>1){
                       $this->html .="<p style='".$this->text_right.$this->maiuscula.$this->bolder.$this->vermelha."; margin-top:-1px; padding-right:10px;'>".$this->viaDocumento.".ª Via</p>";
                    }                    
           $this->html .="
            </body></html>";

            $this->exibir("", "Certificado - ".valorArray($this->alunos, "nomeAluno")); 
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