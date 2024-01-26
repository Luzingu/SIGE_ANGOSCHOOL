<?php 
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../../../funcoesAuxiliares.php');
    include_once ('../../../funcoesAuxiliaresDb.php');

    class ensinoMedioLiceuCEPP extends funcoesAuxiliares{

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
            <div style='margin:15px; margin-bottom:0px; height:1040px;'>".$this->fundoDocumento("../../../../").$this->cabecalho()."
                <p  style='font-size: 12pt; margin-top:15px;".$this->text_center.$this->bolder."'>DECLARAÇÃO DE FREQUÊNCIA</p>

                <div class='p13' style='margin-top:-20px; padding:10px;'>

                    <p style='line-height:30px;".$this->text_justify."'><strong style='".$this->maiuscula."'>".$this->nomeDirigente("Director")."</strong>, Director do ".valorArray($this->sobreUsuarioLogado, "nomeEscola");

                    if(valorArray($this->sobreUsuarioLogado, "decretoCriacaoInstituicao")!=""){
                       $this->html .=", criad".$this->art1Escola." sob o decreto Executivo ".valorArray($this->sobreUsuarioLogado, "decretoCriacaoInstituicao");
                    }
                    $this->html .=", declara que <strong style='".$this->vermelha.$this->maiuscula."'>".valorArray($this->sobreAluno, "nomeAluno")."</strong> filh".$this->art1." de ".tratarCamposVaziosComEComercial(valorArray($this->sobreAluno, "paiAluno"), 15)." e de ".tratarCamposVaziosComEComercial(valorArray($this->sobreAluno, "maeAluno"), 15).", nascid".$this->art1." aos ".dataExtensa(valorArray($this->sobreAluno, "dataNascAluno")).", natural ".valorArray($this->sobreAluno, "preposicaoComuna2")." ".valorArray($this->sobreAluno, "nomeComuna").", Município ".valorArray($this->sobreAluno, "preposicaoMunicipio2")." ".valorArray($this->sobreAluno, "nomeMunicipio").", Província ".valorArray($this->sobreAluno, "preposicaoProvincia2")." ".valorArray($this->sobreAluno, "nomeProvincia").", ".$this->identificacaoAluno.", matriculad".$this->art1." nesta instituição, frequenta neste ano lectivo ".$this->anoFinalizado." o curso de ".$this->nomeCurso.", da Formação Média Geral na ".classeExtensa($this, $this->idPCurso, $this->classe).", turma ".$this->turma.".</p>

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
            $this->notas = array();
            foreach ($notas as $nota) {
                if($nota["idPNomeDisciplina"]!=54){
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
            }

            $this->html .="<html style='margin:30px; margin-bottom:0px; margin-right:60px; margin-left:40px; font-family: Times New Roman !important;'>
            <head>
                <title>Declaração de Habilitações</title>
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
            <img src='CEPP/sombraIICicloCEPP.png' style='position:absolute; margin-left:-28px; margin-top:-20px; width:760px; height:1075px;'>".$this->fundoDocumento("../../../");
                           
                $qrCode = $this->qrCode("Nome d".$this->art1." Alun".$this->art1.": ".valorArray($this->sobreAluno, "nomeAluno")."; Nome da Instituição: ".valorArray($this->sobreUsuarioLogado, "nomeEscola")."; Curso/Opção: ".$this->nomeCurso."; Número Interno: ".valorArray($this->sobreAluno, "numeroInterno")."; Operador: ".valorArray($this->sobreUsuarioLogado, "nomeEntidade")."; Emitido aos:".$this->dataSistema, "../../../", 110, 110);

            $this->html .= $this->vistoDirectorMunicipal(45, $qrCode)."<p style='".$this->text_center.$this->miniParagrafo."margin-top:20px;'><img src='".$_SERVER['DOCUMENT_ROOT']."/angoschool/icones/insignia.jpg' style='width:50px; height:50px; margin-top:15px;'></p>

                      <p style='font-size: 18pt; margin-top:15px;".$this->bolder.$this->text_center."'>República de Angola</p>
                      <p style='font-size: 18pt; margin-top:-23px;".$this->text_center."'>Ministério da Educação</p>
                      <p style='font-size: 18pt; margin-top:-10px;".$this->text_center.$this->bolder."'>ENSINO GERAL</p>
                      
                      <div style='font-size: 18pt; margin-top:10px; width:410px;margin-left:130px;".$this->text_center.$this->bolder."background-color:rgb(0, 176, 240); color:white;'>DECLARAÇÃO DE HABILITAÇÕES</div>
                    
                <div class='p12' style='margin-top:-30px; padding:30px;'>
                    <p style='line-height: 20px;".$this->text_justify."margin-top:25px; text-indent:80px; font-size:12pt;'>a) <span style='".$this->bolder.$this->maiuscula."'>PEDRO GUILHERME RAIO</span>, Director do Liceu Tuku, Declara que <strong style='".$this->vermelha."'>".valorArray($this->sobreAluno, "nomeAluno")."</strong> filh".$this->art1." de ".tratarCamposVaziosComEComercial(valorArray($this->sobreAluno, "paiAluno"), 15)." e de ".tratarCamposVaziosComEComercial(valorArray($this->sobreAluno, "maeAluno"), 15).", nascid".$this->art1." aos ".dataExtensa(valorArray($this->sobreAluno, "dataNascAluno")).", natural ".valorArray($this->sobreAluno, "preposicaoComuna2")." ".valorArray($this->sobreAluno, "nomeComuna").", Município ".valorArray($this->sobreAluno, "preposicaoMunicipio2")." ".valorArray($this->sobreAluno, "nomeMunicipio").", Província ".valorArray($this->sobreAluno, "preposicaoProvincia2")." ".valorArray($this->sobreAluno, "nomeProvincia").", ".$this->identificacaoAluno.". Frequentou neste Estabelecimento de Ensino no ano lectivo de <strong>".$this->anoFinalizado."</strong>, b) a <strong>".classeExtensa($this, $this->idPCurso, $this->classe)."</strong>, na área de <strong>".$this->nomeCurso."</strong>, turma ".$this->turma.", sob o n.º ".completarNumero($this->numeroAnterior).", tendo obtido o resultado final de <strong>Apt".$this->art1."</strong> e com as seguintes classificações por disciplina: 
                    </p> 
                </div>

                <div style='width:54%; margin-left:23%;'>
                <table style='width:100%; font-size:12pt; margin-top:-10px;".$this->tabela."font-size:11pt;'>";
                
                foreach ($this->notas as $disciplina) {
                    
                    $nomeDisciplina = $disciplina["abreviacaoDisciplina1"];
                    if($disciplina["idPNomeDisciplina"]==20 || $disciplina["idPNomeDisciplina"]==21){
                        $nomeDisciplina ="Língua Estrangeira";
                    }else if($disciplina["idPNomeDisciplina"]==22 || $disciplina["idPNomeDisciplina"]==23){
                        $nomeDisciplina ="Língua Estrangeira (F.E)";
                    }
                     $this->html .="<tr><td style='padding-left:4px;".$this->border().$this->bolder."'><i>".$nomeDisciplina."</i></td>".$this->retornarNotas($disciplina["mf"], "font-weight:bolder; width:40px;")."<td style='".$this->border().$this->bolder."'>&nbsp;&nbsp;Valores</td></tr>";
                }
                   
            $this->html .="</table></div>
                <p style='padding-left:30px; padding-right:10px;".$this->text_justify."font-size:12pt;text-indent:80px;'>A presente Declaração destina-se para <strong>".$this->efeitoDeclaracao."</strong>.</p>

                <p  style='padding-left:30px; line-height:21px; font-size:12pt; padding-right:10px;".$this->text_justify."text-indent:80px;'>Por ser verdade e me ter sido solicitada e assim constar nos documentos que ficam arquivados na Secretaria, mandei passar a presente declaração que vai por mim assinada e autenticada com o carimbo a óleo em uso neste Estabelecimento Escolar</p>
                
                <p style='padding-left:10px; padding-right:10px; font-size:12pt;".$this->text_center.$this->maiuscula.$this->bolder."'>LICEU DO TUKU, EM MBANZA-KONGO,AOS ".dataExtensa($this->dataSistema).".</p><br>";
                
               
                    $this->html .="
                    <div style='margin-top: -10px;".$this->text_center."'>
                        <div style='width: 50%;'>".$this->porAssinatura("A SUB DIRECTORA PEDAGÓGICA", "ISABEL IMBO NKIDISALA", "", "nada")."
                        </div>
                        <div style='width: 50%;margin-top:-300px; margin-left:50%;'>".$this->porAssinatura("O DIRECTOR", "PEDRO GUILHERME RAIO", "", "nada")."
                        </div>
                    </div>";
                
                $this->html .="</div>";
                
                   $this->html .="<p style='".$this->text_right.$this->bolder."; margin-top:-30px; font-size:12pt; padding-right:20px;'>Dig. Dir. - Complexo Escolar Privado Progresso</p>";
                
           $this->html .="</div>
            </body></html>";

            $this->exibir("", "Declaração da ".$this->classeExtensa." - ".valorArray($this->sobreAluno, "nomeAluno"));
        } 

        public function certificado(){
            $this->nomeCurso();

            $notas =array();
            for($i=10; $i<=12; $i++){
                $notas = array_merge($notas, $this->notasDeclaracao($i, $this->idPCurso));
            }

            $this->notas = array();
            foreach ($notas as $nota) {
                if($nota["idPNomeDisciplina"]!=54){
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
            }
            $media10 = $this->calculadorMediaPorClasse(10);
            $media11 = $this->calculadorMediaPorClasse(11);
            $media12 = $this->calculadorMediaPorClasse(12);

            $PC = ($media10+$media11+$media12)/3;
            $PC = number_format($PC, 0);

            $this->html .="<html style='margin:20px; margin-right:50px; margin-left:40px; font-family: Times New Roman !important;'>
            <head>
                <title>Declaração de Habilitações</title>
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
             
            <div style='height:1000px;'>
           <img src='CEPP/sombraIICicloCEPP.png' style='position:absolute; margin-left:-28px; margin-top:-20px; width:760px; height:1085px;'>".$this->fundoDocumento("../../../");

            $qrCode = $this->qrCode("Nome d".$this->art1." Alun".$this->art1.": ".valorArray($this->sobreAluno, "nomeAluno")."; Nome da Instituição: ".valorArray($this->sobreUsuarioLogado, "nomeEscola")."; Curso/Opção: ".$this->nomeCurso."; Número Interno: ".valorArray($this->sobreAluno, "numeroInterno")."; Média Final do Curso: ".$PC."; Ano de Conclusão: ".$this->anoFinalizado."; Operador: ".valorArray($this->sobreUsuarioLogado, "nomeEntidade")."; Data de Emissão:".$this->dataSistema, "../../../", 110, 110);
            $this->html .= $this->vistoDirectorMunicipal(45, $qrCode)."
                    <p style='".$this->text_center.$this->miniParagrafo."margin-top:20px;'><img src='".$_SERVER['DOCUMENT_ROOT']."/angoschool/icones/insignia.jpg' style='width:50px; height:50px; margin-top:15px;'></p>
                    <p style='".$this->text_center.$this->miniParagrafo.$this->maiuscula."'  style='font-size:12pt;'>República de Angola</p>
                    <p style='".$this->text_center.$this->maiuscula."' style='font-size:12pt;'>Ministério da Educação </p>
                      <p style='".$this->text_center."' style='font-size: 12pt; margin-top:-40px;'>II CICLO DO ENSINO SECUNDÁRIO GERAL</p>

                      <p  style='font-size: 15pt; margin-top:-3px;".$this->text_center.$this->bolder."'>CERTIFICADO</p>
                        ";

                $this->html .="
                    <p style='line-height: 20px;".$this->text_justify."text-indent:40px; margin-top:-70px; padding:30px; padding-top:-10px;'><strong>a)</strong> <span style='".$this->bolder.$this->maiuscula."'>PEDRO GUILHERME RAIO</span>, Director do Liceu do Tuku, criado sob o Decreto Executivo n.º 19/14 de Janeiro, certifica que <span style='".$this->bolder.$this->vermelha."'>".valorArray($this->sobreAluno, "nomeAluno")."</span>, filh".$this->art1." de ".valorArray($this->sobreAluno, "paiAluno")." e de ".valorArray($this->sobreAluno, "maeAluno").", natural ".valorArray($this->sobreAluno, "preposicaoComuna2")." ".valorArray($this->sobreAluno, "nomeComuna").", Município ".valorArray($this->sobreAluno, "preposicaoMunicipio2")." ".valorArray($this->sobreAluno, "nomeMunicipio").", Província ".valorArray($this->sobreAluno, "preposicaoProvincia2")." ".valorArray($this->sobreAluno, "nomeProvincia").", nascid".$this->art1." aos ".dataExtensa(valorArray($this->sobreAluno, "dataNascAluno")).", ".$this->identificacaoAluno.", concluiu no ano lectivo <strong>".$this->anoFinalizado."</strong> o curso do <strong>II CICLO DO ENSINO SECUNDÁRIO GERAL</strong>, na área de <strong>".$this->nomeCurso."</strong>, conforme o disposto na alínea e) do artigo 109.º da <strong>LBSEE 17/16, de 7 de Outubro</strong>, com a Média Final de <strong>".$PC." valores</strong>, obtida nas seguintes classificações por disciplinas:</p>

                    <div style='width:100%; margin-top:-30px; margin-left:30px; margin-right:30px; '>
                <table class='tabela padding0' style='width:630px; font-size:13pt;".$this->tabela."'>";

                $this->html .="<tr><td style='".$this->border().$this->bolder.$this->text_center."'>Disciplina</td><td style='".$this->border().$this->bolder.$this->text_center."'>10.ª<br/>Classe</td><td style='".$this->border().$this->bolder.$this->text_center."'>11.ª<br/>Classe</td><td style='".$this->border().$this->bolder.$this->text_center."'>12.ª<br/>Classe</td><td style='".$this->border().$this->bolder.$this->text_center."' style='padding:2px;".$this->border().$this->bolder.$this->text_center."'>Média<br/>Final</td><td style='".$this->border().$this->bolder.$this->text_center."'>Média por<br/>Extenso</td></tr>";

                foreach (distinct2($this->notas, "tipoDisciplina") as $tipo) {
                    $this->html .="<tr><td style='padding-left:10px;".$this->border().$this->bolder."' colspan='6'>".tipoDisciplina($tipo)."</td></tr>";

                    foreach (distinct2(array_filter($this->notas, function($mamale) use ($tipo){
                        return $mamale["tipoDisciplina"]==$tipo;}), "idPNomeDisciplina") as $idPNomeDisciplina){
                        
                        $nomeDisciplina="";
                        $somaTotalNotas=0;
                        $contadorNotas=0;

                        $nota10="";
                        $nota11="";
                        $nota12="";
                        foreach (array_filter($this->notas, function($mamale) use ($idPNomeDisciplina){
                        return $mamale["idPNomeDisciplina"]==$idPNomeDisciplina;}) as $nota) {

                            $nomeDisciplina=$nota["abreviacaoDisciplina1"];

                            if($nota["idPNomeDisciplina"]==20 || $nota["idPNomeDisciplina"]==21 || $nota["idPNomeDisciplina"]==22 || $nota["idPNomeDisciplina"]==23){
                                $nomeDisciplina ="Língua Estrangeira";
                            }
                            
                            if($nota["classePauta"]==10){
                                $nota10 = $nota["mf"];
                                $contadorNotas++;
                                $somaTotalNotas +=$nota["mf"];
                            }else if($nota["classePauta"]==11){
                                $nota11 = $nota["mf"];
                                $contadorNotas++;
                                $somaTotalNotas +=$nota["mf"];
                            }else if($nota["classePauta"]==12){
                                $nota12 = $nota["mf"];
                                $contadorNotas++;
                                $somaTotalNotas +=$nota["mf"];
                            }
                        } 
                        if($contadorNotas==0){
                            $mediaDisciplina=0;
                        }else{
                            $mediaDisciplina = number_format(($somaTotalNotas/$contadorNotas), 0);
                        }

                        $this->html .="<tr><td style='".$this->border()." padding-left:6px;'>".$nomeDisciplina."</td>".$this->retornarNotas($nota10, "", "==").$this->retornarNotas($nota11, "", "==").$this->retornarNotas($nota12, "", "==").$this->retornarNotas($mediaDisciplina, "", "==")."<td style='".$this->bolder.$this->text_center.$this->border()."'>".primeiraLetraMaiuscula($this->retornarNotaExtensa(number_format($mediaDisciplina, 0, ",", ".")))."</td></tr>"; 
                       
                    }
                }
                
                
            $this->html .="</table></div>
                <p style='line-height: 20px;".$this->text_justify."padding-left:30px; padding-right:30px;'>Para efeitos legais lhe é passado o presente CERTIFICADO, que consta no processo n.º ".valorArray($this->sobreAluno, "numeroProcesso").", pauta n.º ".$this->numeroPauta.", assinado por mim e autenticado com carimbo a óleo em uso neste estabelecimento de ensino.</p>
                <p style='line-height: 17px;".$this->text_center.$this->bolder."padding-left:30px; padding-right:30px;'>LICEU DO TUKU, em Mbanza Kongo, aos ".dataExtensa($this->dataSistema)."</p>

                    <div style='margin-top: -13px;".$this->text_center."'>
                        <div style='width: 50%;'>".$this->porAssinatura("Conferido por", "Isabel Imbo Nkidisala<br/>(Subdirectora Pedagógica)", "", 27)."
                        </div>
                        <div style='width: 50%;margin-top:-300px; margin-left:50%;'>".$this->porAssinatura("O Director", "Pedro Guilherme Raio", "", 27)."
                        </div>
                    </div>";
                                        
           $this->html .="</div><p style='".$this->text_right.$this->bolder."; margin-top:40px; padding-right:10px;'>Dig. Dir. - Complexo Escolar Privado Progresso</p></div>
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