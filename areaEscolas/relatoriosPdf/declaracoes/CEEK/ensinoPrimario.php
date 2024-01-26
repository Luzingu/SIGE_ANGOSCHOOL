<?php 
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');

    class ensinoPrimarioKami extends funcoesAuxiliares{
       
        public $documentoTratar="";
        public $numeroAnterior="";
        public $notaMinima=5;
        public $idDeclaracao="";
        public $alunos="";
        public $disciplinas=null;
        public $classeInterno="";
         private $tN=0;
        private $tD=0;
        
        public $efeitoDeclaracao="";
        public $numeroDeclaracao="";
        public $idPMatricula="";
        public $art1="";
        public $art2="";


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
            <div style='margin:15px; margin-bottom:0px; height:1040px;'>".$this->fundoDocumento("../../../").$this->cabecalho()."
                <p  style='font-size: 12pt; margin-top:15px;".$this->text_center.$this->bolder."'>DECLARAÇÃO DE FREQUÊNCIA</p>

                <div class='p13' style='margin-top:-20px; padding:10px;'>

                    <p style='line-height:30px;".$this->text_justify."'><strong style='".$this->maiuscula."'>".$this->nomeDirigente(7)."</strong>, Director do ".valorArray($this->sobreUsuarioLogado, "nomeEscola");
                    
                    if(valorArray($this->sobreUsuarioLogado, "privacidadeEscola")=="Pública"){
                       $this->html .=", criad".$this->art1Escola." sob o decreto Executivo ".valorArray($this->sobreUsuarioLogado, "decretoCriacaoInstituicao");
                    }
                    $this->html .=", declara que <strong style='".$this->vermelha.$this->maiuscula."'>".valorArray($this->sobreAluno, "nomeAluno")."</strong> filh".$this->art1." de ".tratarCamposVaziosComEComercial(valorArray($this->sobreAluno, "paiAluno"), 15)." e de ".tratarCamposVaziosComEComercial(valorArray($this->sobreAluno, "maeAluno"), 15).", nascid".$this->art1." aos ".dataExtensa(valorArray($this->sobreAluno, "dataNascAluno")).", natural ".valorArray($this->sobreAluno, "preposicaoComuna2")." ".valorArray($this->sobreAluno, "nomeComuna").", Município ".valorArray($this->sobreAluno, "preposicaoMunicipio2")." ".valorArray($this->sobreAluno, "nomeMunicipio").", Província ".valorArray($this->sobreAluno, "preposicaoProvincia2")." ".valorArray($this->sobreAluno, "nomeProvincia").", ".$this->identificacaoAluno.", matriculad".$this->art1." nesta instituição, frequenta neste ano lectivo ".$this->anoFinalizado." a ".classeExtensa($this, $this->idPCurso, $this->classe).", turma ".$this->selectUmElemento("turmas", "designacaoTurma", "idTurmaAno=:idTurmaAno AND idTurmaMatricula=:idTurmaMatricula AND idTurmaEscola=:idTurmaEscola", [$this->idPAno, valorArray($this->sobreAluno, "idPMatricula"), $_SESSION['idEscolaLogada']]).".</p>

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
            $this->notaMinima=5;

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
            <body style='padding: 0px; margin: 0px;'> 
            <div style='height:1055px;'>
            <img src='".$_SERVER['DOCUMENT_ROOT']."/angoschool/icones/sombraGeral.png' style='position:absolute; margin-left:-40px; margin-top:-40px; width:780px; height:1120px;'>".$this->fundoDocumento("../../../");
            
            $qrCode = $this->qrCode("Nome d".$this->art1." Alun".$this->art1.": ".valorArray($this->sobreAluno, "nomeAluno")."; Nome da Instituição: ".valorArray($this->sobreUsuarioLogado, "nomeEscola")."; Curso/Opção: ".$this->nomeCurso."; Número Interno: ".valorArray($this->sobreAluno, "numeroInterno")."; Operador: ".valorArray($this->sobreUsuarioLogado, "nomeEntidade")."; Emitido aos:".$this->dataSistema, "../../../", 110, 110);
            $this->html .= $this->vistoDirectorMunicipal(45, $qrCode)."

                <div class='cabecalho'>
                    <p style='".$this->text_center.$this->miniParagrafo."margin-top:25px;'><img src='../../../icones/insigniaDeclaracao.jpg' class='text-center' style='width:65px; height:65px;'></p>
                    <p style='".$this->text_center.$this->miniParagrafo."font-size:12pt;'>REPÚBLICA DE ANGOLA</p>
                    <p style='".$this->text_center."font-size:13pt;'>MINISTÉRIO DA EDUCAÇÃO</p>
                      <p style='".$this->text_center."font-size: 13pt;'>ENSINO GERAL</p>

                      <p style='font-size: 14pt; margin-top:10px;".$this->bolder.$this->text_center."'>DECLARAÇÃO DE HABILITAÇÕES</p>
                </div>

                <div class='p12' style='margin-top:-30px; padding:10px;'>
                    <p style='line-height: 25px;".$this->text_justify."'>a) <span style='".$this->bolder."'>Belarmina Domingos Kita</span>, Directora da Escola Primária n.º 86 Nkungu-Eyenguele, criada sob o decreto Executivo n.º 141/2012 de 24 de Abril, declara que <strong style='".$this->vermelha.$this->maiuscula."'>".valorArray($this->sobreAluno, "nomeAluno")."</strong>, filh".$this->art1." de ".tratarCamposVaziosComEComercial(valorArray($this->sobreAluno, "paiAluno"), 15)." e de ".tratarCamposVaziosComEComercial(valorArray($this->sobreAluno, "maeAluno"), 15).", nascid".$this->art1." aos ".dataExtensa(valorArray($this->sobreAluno, "dataNascAluno")).", natural ".valorArray($this->sobreAluno, "preposicaoComuna2")." ".valorArray($this->sobreAluno, "nomeComuna").", Município ".valorArray($this->sobreAluno, "preposicaoMunicipio2")." ".valorArray($this->sobreAluno, "nomeMunicipio").", Província ".valorArray($this->sobreAluno, "preposicaoProvincia2")." ".valorArray($this->sobreAluno, "nomeProvincia").", ".$this->identificacaoAluno.". Frequentou neste Estabelecimento de Ensino no ano lectivo de <strong>".$this->anoFinalizado."</strong>, b) a <strong>".classeExtensa($this, $this->idPCurso, $this->classe)."</strong>, turma ".$this->turma.", sob o n.º ".completarNumero($this->numeroAnterior).", tendo obtido o resultado final <strong>Apt".$this->art1."</strong> e com as seguintes classificações por disciplina: 
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
                <p style='padding-left:10px; padding-right:10px;".$this->text_justify."'>A presente Declaração destina-se para ".$this->efeitoDeclaracao.".</p>

                <p  style='padding-left:10px; line-height:25px; padding-right:10px;".$this->text_justify."'>Por ser verdade e me ter sido solicitada e assim constar nos documentos que ficam arquivados na Secretaria, mandei passar a presente declaração que vai por mim assinada e autenticada com o carimbo a óleo em uso neste Liceu.</p>
        
                <p style='padding-left:10px; padding-right:10px;".$this->text_justify.$this->maiuscula."'>Escola do Ensino Primário n.º 86 em Soyo, aos ".dataExtensa($this->dataSistema).".</p>
                <div style='margin-top: -17px;".$this->text_center."'>

                        <div style='width: 50%;'>".$this->porAssinatura("Conferido por", "Abel Londa Simba")."
                        </div>
                        <div style='width: 50%;margin-top:-300px; margin-left:50%;'>".$this->porAssinatura("A Directora", "Lic. Belarmina Domingos Kita")."
                        </div>
                    </div>
                </div>";
                if($this->viaDocumento>1){
                   $this->html .="<p style='".$this->text_right.$this->maiuscula.$this->bolder.$this->vermelha."; margin-top:-35px; padding-right:20px;'>".$this->viaDocumento.".ª Via</p>";
                }
           $this->html .="</div>
        </body></html>";

        $this->exibir("", "Declaração da ".$this->classeExtensa." - ".valorArray($this->sobreAluno, "nomeAluno"));
        } 

        public function certificado(){
            $this->notaMinima=5;
            $this->nomeCurso();

            $notas =array();
            for($i=2; $i<=6; $i++){
                if(($i%2)==0){
                    $notas = array_merge($notas, $this->notasDeclaracao($i, $this->idPCurso));
                }
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
            
            $media2 = $this->calculadorMediaPorClasse(2);
            $media4 = $this->calculadorMediaPorClasse(4);
            $media6 = $this->calculadorMediaPorClasse(6);
            $PC = ($media2+$media4+$media6)/3;
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
            <img src='".$_SERVER['DOCUMENT_ROOT']."/angoschool/icones/sombraGeral.png' style='position:absolute; margin-left:-40px; margin-top:-30px; width:780px; height:1120px;'>".$this->fundoDocumento("../../../");

            $qrCode = $this->qrCode("Nome d".$this->art1." Alun".$this->art1.": ".valorArray($this->sobreAluno, "nomeAluno")."; Nome da Instituição: ".valorArray($this->sobreUsuarioLogado, "nomeEscola")."; Curso/Opção: ".$this->nomeCurso."; Número Interno: ".valorArray($this->sobreAluno, "numeroInterno")."; Média Final do Curso: ".$PC."; Ano de Conclusão: ".$this->anoFinalizado."; Operador: ".valorArray($this->sobreUsuarioLogado, "nomeEntidade")."; Data de Emissão:".$this->dataSistema, "../../../", 110, 110);
            $this->html .= $this->vistoDirectorMunicipal(45, $qrCode)."<div class='cabecalho'>
                    <p style='".$this->text_center.$this->miniParagrafo."margin-top:20px;'><img src='../../../icones/insigniaDeclaracao.jpg' class='text-center' style='width:65px; height:65px;'></p>
                    <p style='".$this->text_center.$this->miniParagrafo.$this->maiuscula."'  style='font-size:12pt;'>República de Angola</p>
                    <p style='".$this->text_center.$this->maiuscula."' style='font-size:12pt;'>Ministério da Educação </p>
                      <p style='".$this->text_center."' style='font-size: 12pt; margin-top:-40px;'>I CICLO DO ENSINO SECUNDÁRIO GERAL</p>

                      <p  style='font-size: 15pt; margin-top:-10px;".$this->text_center.$this->bolder."'>CERTIFICADO DE HABILITAÇÕES</p>
                </div>";

                $this->html .="<div style='margin-top:-30px; padding:10px;'>
                    <p style='line-height: 22px;".$this->text_justify."'><span style='".$this->bolder."'>Belarmina Domingos Kita</span>, Directora da Escola Primária n.º 86 Nkungu-Eyenguele, criada sob o Decreto Executivo n.º 141/2012 de 24 de Abril, certifica que <span style='".$this->bolder.$this->vermelha.$this->maiuscula."'>".valorArray($this->sobreAluno, "nomeAluno")."</span>, filh".$this->art1." de ".valorArray($this->sobreAluno, "paiAluno")." e de ".valorArray($this->sobreAluno, "maeAluno").", natural ".valorArray($this->sobreAluno, "preposicaoComuna2")." ".valorArray($this->sobreAluno, "nomeComuna").", Município ".valorArray($this->sobreAluno, "preposicaoMunicipio2")." ".valorArray($this->sobreAluno, "nomeMunicipio").", Província ".valorArray($this->sobreAluno, "preposicaoProvincia2")." ".valorArray($this->sobreAluno, "nomeProvincia").", nascid".$this->art1." aos ".dataExtensa(valorArray($this->sobreAluno, "dataNascAluno")).", ".$this->identificacaoAluno.", concluiu no ano lectivo <strong>".$this->anoFinalizado."</strong> o <strong>ENSINO PRIMÁRIO</strong>, na área de <strong>".$this->nomeCurso."</strong>, conforme o disposto na alínea f) do artigo 109.º da <strong>LBSEE 32/20, de 12 de Agosto</strong>, com a Média Final de <strong style='".$this->vermelha."'>".$PC." </strong>valores, obtido nas seguintes classificações por disciplinas:</p>

                    <div style='width:100%; margin-top:15px;'>
                <table class='tabela padding0' style='width:100%; font-size:13pt;".$this->tabela."'>";

                $this->html .="<tr><td style='".$this->border().$this->bolder.$this->text_center."'>Disciplina</td><td style='".$this->border().$this->bolder.$this->text_center."'>2.ª<br/>Classe</td><td style='".$this->border().$this->bolder.$this->text_center."'>4.ª<br/>Classe</td><td style='".$this->border().$this->bolder.$this->text_center."'>6.ª<br/>Classe</td><td style='".$this->border().$this->bolder.$this->text_center."' style='padding:2px;".$this->border().$this->bolder.$this->text_center."'>Média Final</td><td style='".$this->border().$this->bolder.$this->text_center."'>Média por<br/>Extenso</td></tr>";

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
                            
                            if($nota["classePauta"]==2){
                                $nota7 = $nota["mf"];
                                $contadorNotas++;
                                $somaTotalNotas +=$nota["mf"];
                            }else if($nota["classePauta"]==4){
                                $nota8 = $nota["mf"];
                                $contadorNotas++;
                                $somaTotalNotas +=$nota["mf"];
                            }else if($nota["classePauta"]==6){
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
                <p style='line-height: 22px;".$this->text_justify."'>Para efeitos legais lhe é passado o presente CERTIFICADO, que consta no processo n.º ".valorArray($this->sobreAluno, "numeroProcesso").", assinado por mim e autenticado com carimbo a óleo em uso neste Estabelecimento de Ensino.</p>
                <p style='line-height: 17px;".$this->text_justify.$this->text_center.$this->bolder."'>Escola do Ensino Primário n.º 86 em Soyo, aos ".dataExtensa($this->dataSistema)."</p> 

                    <div style='margin-top: -17px;".$this->text_center."'>

                        <div style='width: 50%;'>".$this->porAssinatura("Conferido por", "Abel Londa Simba")."
                        </div>
                        <div style='width: 50%;margin-top:-300px; margin-left:50%;'>".$this->porAssinatura("A Directora", "Lic. Belarmina Domingos Kita")."
                        </div>
                    </div>
                    
                    </div>";
                    if($this->viaDocumento>1){
                       $this->html .="<p style='".$this->text_right.$this->maiuscula.$this->bolder.$this->vermelha."; margin-top:-1px; padding-right:10px;'>".$this->viaDocumento.".ª Via</p>";
                    }                    
           $this->html .="</div>
            </body></html>";

            $this->exibir("", "Certificado-".valorArray($this->sobreAluno, "nomeAluno")); 
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