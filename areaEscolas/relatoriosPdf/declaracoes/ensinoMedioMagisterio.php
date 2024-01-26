<?php 
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');

    class ensinoMedioMagisterio extends funcoesAuxiliares{

        function __construct(){
            parent::__construct();
            
        }

        public function declaracaoSemNotas(){
            
            $this->classe = valorArray($this->sobreAluno, "classeActualAluno", "escola");
            $this->idPCurso = valorArray($this->sobreAluno, "idMatCurso", "escola");
            $this->nomeCurso();

            $periodoAluno = valorArray($this->sobreAluno, "periodoAluno", "escola");
            if($periodoAluno=="reg"){
                $periodoAluno="Regular";
            }else{
                $periodoAluno="Pós-Laboral";
            }


            $this->html .="<html style='margin-top: 50px; margin-left:70px; margin-right:70px;'> 
            <head>
                <title>Declaração de Frequência</title>
            </head>
            <body>".$this->fundoDocumento("../../../").$this->cabecalho()."
                <p style='".$this->bolder.$this->text_center."'>DECLARAÇÃO DE FREQUÊNCIA</p>

                <div class='p13' style='margin-top:-20px; padding:10px;'>

                    <p style='line-height:25px;".$this->text_justify."'><strong style='".$this->maiuscula."'>".$this->nomeDirigente(7)."</strong>, Director do ".valorArray($this->sobreUsuarioLogado, "nomeEscola");
                    
                    if(valorArray($this->sobreUsuarioLogado, "privacidadeEscola")=="Pública"){
                       $this->html .=", criad".$this->art1Escola." sob o Decreto Executivo ".valorArray($this->sobreUsuarioLogado, "decretoCriacaoInstituicao");
                    }
                    $this->html .=", declara que <strong style='".$this->vermelha.$this->maiuscula."'>".valorArray($this->sobreAluno, "nomeAluno")."</strong>, filh".$this->art1." de ".tratarCamposVaziosComEComercial(valorArray($this->sobreAluno, "paiAluno"), 15)." e de ".tratarCamposVaziosComEComercial(valorArray($this->sobreAluno, "maeAluno"), 15).", nascid".$this->art1." aos ".dataExtensa(valorArray($this->sobreAluno, "dataNascAluno")).", natural ".valorArray($this->sobreAluno, "preposicaoComuna2")." ".valorArray($this->sobreAluno, "nomeComuna").", Município ".valorArray($this->sobreAluno, "preposicaoMunicipio2")." ".valorArray($this->sobreAluno, "nomeMunicipio").", Província ".valorArray($this->sobreAluno, "preposicaoProvincia2")." ".valorArray($this->sobreAluno, "nomeProvincia").", ".$this->identificacaoAluno.", alun".$this->art1." matriculad".$this->art1." nesta Instituição, frequentando neste ano lectivo ".$this->anoFinalizado." a <strong style='".$this->minuscula."'>".classeExtensa($this, $this->idPCurso,$this->classe)."</strong>, turma <strong>".$this->turma."</strong>, especialidade de <strong>".$this->nomeCurso."</strong>.";
                    $this->html .="</p>

                    <p style='line-height:25px;margin-top:20px;".$this->text_justify.$this->bolder."'>OBS: <span style='".$this->sublinhado."'>Esta Declaração destina-se para ".tratarCamposVaziosComEComercial($this->efeitoDeclaracao, 17).".</span></p>

                    <p style='line-height:30px; margin-top:20px;".$this->text_justify."'>Por ser verdade, e me ter solicitado, mandei passar a presente Declaração que vai por mim assinada e autenticada com o carimbo a óleo em uso neste estabelecimento de ensino.</p>

                     <p style='margin-top:20px;".$this->maiuscula.$this->text_center.$this->bolder."'>".$this->rodape().".</p>


                    <div style='width: 50%; margin-left:25%; margin-top:10px;".$this->text_center.$this->bolder."'>".$this->assinaturaDirigentes(7, "", "", "", "nao")."</div>
                   
            </body> 


            </html>";

            $this->exibir("", "Declaração da ".classeExtensa($this, $this->idPCurso,$this->classe)." - ".valorArray($this->sobreAluno, "nomeAluno"));
        }


        public function declaracao(){
            $this->notaMinima=10;
            $this->nomeCurso();
            $periodoAluno = valorArray($this->sobreAluno, "periodoAluno", "escola");
            if($periodoAluno=="reg"){
                $periodoAluno="Regular";
            }else{
                $periodoAluno="Pós-Laboral";
            } 

            $notas = $this->notasDeclaracao($this->classe, $this->idPCurso);
            $notas = ordenar($notas, "ordenacao ASC");
            $this->notas = array();
            foreach ($notas as $nota) {
                if($this->idPCurso!=8 || ($this->idPCurso==8 && $nota["idPNomeDisciplina"]!=54)){
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
            }

            $this->html .="<html style='margin:30px; margin-bottom:0px; margin-right:25px; margin-left:25px; font-family: Times New Roman !important;'>
            <head>
                <title>Declaração</title>
                <style>
                    p{
                        font-family: Times New Roman !important;
                        font-size: 12pt;
                    }
                </style>
            </head>
            <body>";
            ///$this ->html .="<div style='height:1055px;'><img src='".$_SESSION["directorioPaterno"]."angoschool/icones/sombraGeral.png' style='position:absolute; margin-left:-40px; margin-top:-40px; width:780px; height:1100px;'>";

            $this->html .="<div style='border:solid black 3px; margin:10px; margin-top:0px; padding-top:-15px; height:1050px;'>";

            $this->html .=$this->fundoDocumento("../../../");

                $qrCode = $this->qrCode("Nome d".$this->art1." Alun".$this->art1.": ".valorArray($this->sobreAluno, "nomeAluno")."; Nome da Instituição: ".valorArray($this->sobreUsuarioLogado, "nomeEscola")."; Curso/Opção: ".$this->nomeCurso."; Número Interno: ".valorArray($this->sobreAluno, "numeroInterno")."; Ano de Conclusão: ".$this->anoFinalizado."; Operador: ".valorArray($this->sobreUsuarioLogado, "nomeEntidade")."; Data de Emissão:".$this->dataSistema, "../../../", 110, 110);
            $this->html .= $this->vistoDirectorMunicipal(45, $qrCode)."<div class='cabecalho'>
                    <p style='".$this->text_center.$this->miniParagrafo."margin-top:20px;'><img src='../../../icones/insigniaDeclaracao.jpg' class='text-center' style='width:50px; height:50px;'></p>
                    <p style='".$this->text_center.$this->miniParagrafo."font-size:12pt;'>REPÚBLICA DE ANGOLA</p>
                    <p style='".$this->text_center."font-size:12pt;'>MINISTÉRIO DA EDUCAÇÃO</p>
                      <p style='".$this->text_center."font-size: 12pt; margin-top:10px;'>ENSINO SECUNDÁRIO PEDAGÓGICO</p>

                      <p style='font-size: 14pt; margin-top:10px;".$this->bolder.$this->text_center."'>DECLARAÇÃO DE HABILITAÇÕES</p>
                </div>

                <div class='p12' style='margin-top:-25px; padding:10px;'>
                    <p style='line-height: 25px;".$this->text_justify."'>
                        <span style='".$this->bolder.$this->maiuscula."'>
                        ".$this->nomeDirigente(7)."</span>, Director do ".valorArray($this->sobreUsuarioLogado, "nomeEscola");
                    
                    if(valorArray($this->sobreUsuarioLogado, "decretoCriacaoInstituicao")!=""){
                       $this->html .=", criad".$this->art1Escola." sob o Decreto Executivo ".valorArray($this->sobreUsuarioLogado, "decretoCriacaoInstituicao");
                    } 
                    $this->html .=", declara que <strong style='".$this->vermelha.$this->maiuscula."'>".valorArray($this->sobreAluno, "nomeAluno")."</strong>, filh".$this->art1." de ".tratarCamposVaziosComEComercial(valorArray($this->sobreAluno, "paiAluno"), 15)." e de ".tratarCamposVaziosComEComercial(valorArray($this->sobreAluno, "maeAluno"), 15).", nascid".$this->art1." aos ".dataExtensa(valorArray($this->sobreAluno, "dataNascAluno")).", natural ".valorArray($this->sobreAluno, "preposicaoComuna2")." ".valorArray($this->sobreAluno, "nomeComuna").", Município ".valorArray($this->sobreAluno, "preposicaoMunicipio2")." ".valorArray($this->sobreAluno, "nomeMunicipio").", Província ".valorArray($this->sobreAluno, "preposicaoProvincia2")." ".valorArray($this->sobreAluno, "nomeProvincia").", ".$this->identificacaoAluno.". Frequentou neste estabelecimento de ensino no ano lectivo de ".$this->anoFinalizado.", o <strong>II CICLO DO ENSINO SECUNDÁRIO PEDAGÓGICO</strong>, na especialidade de <strong>".$this->nomeCurso."</strong>, a <strong>".classeExtensa($this, $this->idPCurso,$this->retornarClasse($this->anoFinalizado, $this->classe))."</strong>, na turma ".$this->turma." sob o n.º ".completarNumero($this->numeroAnterior).", tendo obtido o resultado final <strong>Apt".$this->art1."</strong> e com as seguintes classificações por disciplinas: 
                    </p> 
                </div>

                <div style='width:80%; margin-left:10%;'>
                <table style='width:100%; font-size:11pt; margin-top:-10px;".$this->tabela."'>";

                $this->html .="<tr><td style='".$this->border().$this->bolder.$this->text_center."'>Disciplinas</td><td style='".$this->border().$this->bolder.$this->text_center."'>Média Final</td><td style='".$this->border().$this->bolder.$this->text_center."'>Média por Extenso</td></tr>";
                
                $tiposDisciplinas=array();
                foreach (distinct2($this->notas, "tipoDisciplina") as $tipo) {
                    if($tipo=="FG"){
                        $tiposDisciplinas[]=array("ordenacao"=>1, "tipoDisciplina"=>"FG");
                    }else if($tipo=="FE"){
                        $tiposDisciplinas[]=array("ordenacao"=>2, "tipoDisciplina"=>"FE");
                    }else if($tipo=="FP"){
                        $tiposDisciplinas[]=array("ordenacao"=>3, "tipoDisciplina"=>"FP");
                    }
                }

                foreach (ordenar($tiposDisciplinas, "ordenacao ASC") as $tipo) {

                    $this->html .="<tr><td colspan='3' style='padding-left:4px;".$this->border().$this->bolder."'>".tipoDisciplina($tipo["tipoDisciplina"])."</td></tr>";
                    foreach (array_filter($this->notas, function($mamale) use ($tipo){
                        return $mamale["tipoDisciplina"]==$tipo["tipoDisciplina"];}) as $disciplina) {
                            
                        $nomeDisciplina=$disciplina["abreviacaoDisciplina1"];
                        if(($disciplina["idPNomeDisciplina"]==20 || $disciplina["idPNomeDisciplina"]==21) && $this->modLinguaEstrangeira=="opcional"){
                            $nomeDisciplina="Língua Estrangeira";
                        }
                         $this->html .="<tr><td style='padding-left:4px;".$this->border()."'>".$nomeDisciplina."</td>".$this->retornarNotas($disciplina["mf"])."<td style='".$this->border().$this->text_center."'>".primeiraLetraMaiuscula($this->retornarNotaExtensa(number_format($disciplina["mf"], 0, ",", ".")))."</td></tr>";                       
                    }
                }      
            $this->html .="</table></div>
                <p style='font-size:12pt; padding-left:10px; padding-right:10px; line-height:22px;".$this->text_justify."'>A presente Declaração destina-se para ".$this->efeitoDeclaracao.".</p>

                <p  style='font-size:12pt; padding-left:10px; padding-right:10px; line-height:22px;".$this->text_justify."margin-top:-10px;'>Por ser verdade e me ter sido solicitada, mandei passar a presente Declaração, que vai por mim assinada e autenticada com o carimbo a óleo em uso nesta Instituição.</p>

                <p style='font-size:12pt; padding-left:10px; padding-right:10px;".$this->text_center.$this->maiuscula."'>".$this->rodape().".</p>
                <div style='margin-top: -10px;".$this->text_center."'>";
                        $this->nomeDirigente(8);
                    $peda ="(Subdirector Pedagógico)";
                    if($this->sexoDirigente=="F"){
                        $peda ="(Subdirectora Pedagógica)";
                    }
                    $this->html .="
                    <div style='width: 50%;'>".$this->porAssinatura("Conferido por", $this->tituloNomeDirigente."<br/>".$peda, "", strlen($this->tituloNomeDirigente))."
                    </div>
                    <div style='width: 50%;margin-top:-300px; margin-left:50%;'>".$this->assinaturaDirigentes(7, "", "", "", "nao")."
                    </div>
                </div>
            </div>";
            if($this->viaDocumento>1){
               $this->html .="<p style='".$this->text_right.$this->maiuscula.$this->bolder.$this->vermelha."; margin-top:-30px; padding-right:20px;'>".$this->viaDocumento.".ª Via</p>";
            }
           $this->html .="
        </body></html>";

        $this->exibir("", "Declaração da ".$this->classeExtensa." - ".valorArray($this->sobreAluno, "nomeAluno"));
        }

        public function certificado(){
             $this->nomeCurso();

            $notas =array();
            for($i=10; $i<=13; $i++){
                $notas = array_merge($notas, $this->notasDeclaracao($i, $this->idPCurso));
            }
            $notas = ordenar($notas, "ordenacao ASC");

            $this->notas = array();
            foreach ($notas as $nota) {
                if($this->idPCurso!=8 || ($this->idPCurso==8 && $nota["idPNomeDisciplina"]!=54)){
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
            }

            if(count(distinct2($this->notas, "idPNomeDisciplina"))>=21){
                $this->certificadoMais18();
            }else{
                $this->certificadoMenos18();
            }
        }
        public function certificadoMenos18(){
           
            $media10 = $this->calculadorMediaPorClasse(10);
            $media11 = $this->calculadorMediaPorClasse(11);
            $media12 = $this->calculadorMediaPorClasse(12);
            $media13 = $this->calculadorMediaPorClasse(13);


            $PAP = (int) valorArray($this->sobreAluno, "provAptidao", "escola");
            $NEC = (int) valorArray($this->sobreAluno, "notaEstagio", "escola");

            $PAP = number_format($PAP, 0);
            $NEC = number_format($NEC, 0);
            if($media13>0){
                $PC = ($media10+$media11+$media12+$media13)/4;
            }else{
                $PC = ($media10+$media11+$media12)/3;
            }

            $PC = number_format($PC, 0);
            
            $colspan=7;

            if($NEC>0 && $PAP>0){
                $MFC = (3*$PC+$NEC+$PAP)/5;
            }else{
                $MFC = $PC;                
               // $colspan=6;
            }
            
            $MFC = number_format($MFC, 0);

            $this->html .="<html style='margin:2px; margin-right:6px; margin-left:6px; font-family: Times New Roman !important;'>
            <head>
                <title>Certificado de Habilitações</title>
                <style>
                    table tr td{
                        padding:0px;
                    }
                    p{
                        font-size:12.5pt;
                    }
                </style>
            </head>
            <body>";
            //$this->html .="<div style='height:1055px;'><img src='".$_SESSION["directorioPaterno"]."angoschool/icones/sombraGeral.png' style='position:absolute; margin-left:-40px; margin-top:-40px; width:805px; height:1140px;'>";


            $this->html .="<div style='border:double black 5px; margin:18px; padding-top:-40px; margin-bottom:-30px; height:1070px;'>".$this->fundoDocumento("../../../"); 
            
            $qrCode = $this->qrCode("Nome d".$this->art1." Alun".$this->art1.": ".valorArray($this->sobreAluno, "nomeAluno")."; Nome da Instituição: ".valorArray($this->sobreUsuarioLogado, "nomeEscola")."; Curso/Opção: ".$this->nomeCurso."; Número Interno: ".valorArray($this->sobreAluno, "numeroInterno")."; Média Final do Curso: ".$MFC."; Ano de Conclusão: ".$this->anoFinalizado."; Operador: ".valorArray($this->sobreUsuarioLogado, "nomeEntidade")."; Data de Emissão:".$this->dataSistema, "../../../", 110, 110);
            $this->html .= $this->vistoDirectorMunicipal(45, $qrCode)."<div class='cabecalho'>
                    <p style='".$this->text_center.$this->miniParagrafo."margin-top:5px;'><img src='../../../icones/insigniaDeclaracao.jpg' class='text-center' style='width:55px; height:55px;'></p>
                    <p style='".$this->text_center.$this->miniParagrafo.$this->maiuscula."'  style='font-size:13pt;'>República de Angola</p>
                    <p style='".$this->text_center.$this->maiuscula."' style='font-size:13pt;'>Ministério da Educação </p>
                      <p style='".$this->text_center."' style='font-size: 13pt; margin-top:-40px;'>ENSINO SECUNDÁRIO PEDAGÓGICO</p>

                      <p  style='font-size: 14pt; margin-top:-10px;".$this->text_center.$this->bolder."'>CERTIFICADO</p>
                </div>
 
                <div style='margin-top:-30px; padding:10px;'>
                    <p style='line-height: 19px;".$this->text_justify."'>a) <span style='".$this->bolder.$this->maiuscula."'>".$this->nomeDirigente(7)."</span>, Director do ".valorArray($this->sobreUsuarioLogado, "nomeEscola");
                    
                    if(valorArray($this->sobreUsuarioLogado, "decretoCriacaoInstituicao")!=""){
                       $this->html .=", criad".$this->art1Escola." sob o Decreto Executivo ".valorArray($this->sobreUsuarioLogado, "decretoCriacaoInstituicao");
                    }
            $this->html .=", certifica que <span style='".$this->bolder.$this->vermelha.$this->maiuscula."'>".valorArray($this->sobreAluno, "nomeAluno")."</span>, filh".$this->art1." de ".valorArray($this->sobreAluno, "paiAluno")." e de ".valorArray($this->sobreAluno, "maeAluno").", natural ".valorArray($this->sobreAluno, "preposicaoComuna2")." ".valorArray($this->sobreAluno, "nomeComuna").", Município ".valorArray($this->sobreAluno, "preposicaoMunicipio2")." ".valorArray($this->sobreAluno, "nomeMunicipio").", Província ".valorArray($this->sobreAluno, "preposicaoProvincia2")." ".valorArray($this->sobreAluno, "nomeProvincia").", nascid".$this->art1." aos ".dataExtensa(valorArray($this->sobreAluno, "dataNascAluno")).", ".$this->identificacaoAluno.", concluiu no ano lectivo ".$this->anoFinalizado." o <strong>II CICLO DO ENSINO SECUNDÁRIO PEDAGÓGICO</strong>, na Especialidade de <strong>".$this->nomeCurso."</strong>, conforme o disposto na alínea f) do artigo 109.º da LBSEE 32/20, de 12 de Agosto, com a Média Final do Curso de <strong>".$MFC."</strong> valores, obtida nas seguintes classificações por disciplinas:</p>
                
            <div style='width:100%; margin-left:0%;'>
                <table  style='width:100%; font-size:11.5pt; margin-top:-10px;".$this->tabela."'>";
                $this->html .="<tr><td style='".$this->text_center.$this->bolder.$this->border()."'>Disciplinas</td><td style='".$this->text_center.$this->bolder.$this->border()."'>".$this->retornarClasse($this->anoFinalizado, 10).".ª<br/> Classe</td><td style='".$this->text_center.$this->bolder.$this->border()."'>".$this->retornarClasse($this->anoFinalizado, 11).".ª<br/> Classe</td><td style='".$this->text_center.$this->bolder.$this->border()."'>".$this->retornarClasse($this->anoFinalizado, 12).".ª<br/> Classe</td><td style='".$this->text_center.$this->bolder.$this->border()."'>".$this->retornarClasse($this->anoFinalizado, 13).".ª<br/> Classe</td><td style='".$this->text_center.$this->bolder.$this->border()."'>Média<br/>Final</td><td style='".$this->text_center.$this->bolder.$this->border()."'>Média por<br/> Extenso</td></tr>";

                $tiposDisciplinas=array();
                foreach (distinct2($this->notas, "tipoDisciplina") as $tipo) {
                    if($tipo=="FG"){
                        $tiposDisciplinas[]=array("ordenacao"=>1, "tipoDisciplina"=>"FG");
                    }else if($tipo=="FE"){
                        $tiposDisciplinas[]=array("ordenacao"=>2, "tipoDisciplina"=>"FE");
                    }else if($tipo=="FP"){
                        $tiposDisciplinas[]=array("ordenacao"=>3, "tipoDisciplina"=>"FP");
                    }
                }    
                foreach (ordenar($tiposDisciplinas, "ordenacao ASC") as $tipo) {
                    $this->html .="<tr><td  style='padding-left:10px;".$this->border()."' colspan='".$colspan."'><strong>".tipoDisciplina($tipo["tipoDisciplina"])."</strong></td></tr>";
                    
                    
                    foreach (distinct2(array_filter($this->notas, function($mamale) use ($tipo){
                        return $mamale["tipoDisciplina"]==$tipo["tipoDisciplina"];}), "idPNomeDisciplina") as $idPNomeDisciplina) {
                    
                    
                            $nomeDisciplina="";
                            $somaTotalNotas=0;
                            $contadorNotas=0;
    
                            $nota10="";
                            $nota11="";
                            $nota12="";
                            $nota13="";
                            foreach (array_filter($this->notas, function($mamale) use ($idPNomeDisciplina){
                                return $mamale["idPNomeDisciplina"]==$idPNomeDisciplina;}) as $nota) {

                                $nomeDisciplina=$nota["abreviacaoDisciplina1"];
                                if(($nota["idPNomeDisciplina"]==20 || $nota["idPNomeDisciplina"]==21) && $this->modLinguaEstrangeira=="opcional"){
                                    $nomeDisciplina="Língua Estrangeira";
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
                                }else if($nota["classePauta"]==13){
                                    
                                    $nota13 = $nota["mf"];
                                    $contadorNotas++;
                                    $somaTotalNotas +=$nota["mf"];
                                }
                            }
    
                            if($contadorNotas==0){
                                $mediaDisciplina=0;
                            }else{
                                $mediaDisciplina = number_format(($somaTotalNotas/$contadorNotas), 0);
                            }
                            
                            $nomeDisciplina = str_replace("Met. Ens.", "Met. de Ensino de ", $nomeDisciplina);
                            $this->html .="<tr><td style='".$this->border()."padding-left:3px;'>".$nomeDisciplina."</td>".$this->retornarNotas($nota10, "", "==").$this->retornarNotas($nota11, "", "==").$this->retornarNotas($nota12, "", "==").$this->retornarNotas($nota13, "", "==").$this->retornarNotas($mediaDisciplina, "", "==")."<td style='".$this->text_center.$this->bolder.$this->border()."'>".primeiraLetraMaiuscula($this->retornarNotaExtensa(number_format($mediaDisciplina, 0, ",", ".")))."</td></tr>"; 
                    }
                } 
            
            if($NEC>0 && $PAP>0){
                $this->html .="                
                <tr><td style='".$this->border()."padding-left:5px;'>Estágio Profissional Supervisionado</td><td style='".$this->text_center.$this->border()."'>==</td><td style='".$this->text_center.$this->border()."'>==</td><td style='".$this->text_center.$this->border()."'>==</td><td style='".$this->text_center.$this->border()."'>".$NEC."</td><td style='".$this->text_center.$this->border()."'>".$NEC."</td><td style='".$this->text_center.$this->border().$this->bolder."'>".primeiraLetraMaiuscula($this->retornarNotaExtensa(number_format($NEC, 0, ",", ".")))."</td></tr>

                <tr><td style='".$this->border()." padding-left:5px;'>Avaliação Final de Aptidão para Docência</td><td style='".$this->text_center.$this->bolder.$this->border()."'>==</td><td style='".$this->text_center.$this->border()."'>==</td><td style='".$this->text_center.$this->border()."'>==</td><td style='".$this->text_center.$this->border()."'>".$PAP."</td><td style='".$this->text_center.$this->border()."'>".$PAP."</td><td style='".$this->text_center.$this->bolder.$this->border()."'>".primeiraLetraMaiuscula($this->retornarNotaExtensa(number_format($PAP, 0, ",", ".")))."</td></tr>";  
            }else if($PAP>0){
                $this->html .="<tr><td style='".$this->border()." padding-left:5px;'>Avaliação Final de Aptidão para Docência</td><td style='".$this->text_center.$this->bolder.$this->border()."'>==</td><td style='".$this->text_center.$this->border()."'>==</td><td style='".$this->text_center.$this->border()."'>==</td><td style='".$this->text_center.$this->border()."'>".$PAP."</td><td style='".$this->text_center.$this->border()."'>".$PAP."</td><td style='".$this->text_center.$this->bolder.$this->border()."'>".primeiraLetraMaiuscula($this->retornarNotaExtensa(number_format($PAP, 0, ",", ".")))."</td></tr>";
            }
            $this->html .="
                </table></div>
                    <p  style='line-height: 19px; margin-top:10px;".$this->text_justify."'>Para efeitos considerados legais, lhe é passado o presente CERTIFICADO, que consta no livro n.º ".$this->numeroPauta.", folha ".$this->numeroAnterior.", assinado por mim e autenticado com carimbo a óleo em uso neste estabelecimento de ensino.</p> 
                    <p c style='line-height: 17px; margin-top:-5px;".$this->maiuscula.$this->text_center."'>".$this->rodape().".</p>

                    <div style='margin-top: -17px;".$this->text_center."'>";
                        $this->nomeDirigente(8);
                        $peda ="(Subdirector Pedagógico)";
                        if($this->sexoDirigente=="F"){
                            $peda ="(Subdirectora Pedagógica)";
                        }
                $this->html .="<div style='width: 50%;'>".$this->porAssinatura("Conferido por", $this->tituloNomeDirigente."<br/>".$peda, "", strlen($this->tituloNomeDirigente))."
                </div>
                <div style='width: 50%;margin-top:-300px; margin-left:50%;'>".$this->assinaturaDirigentes(7, "", "", "", "nao")."
                </div>
            </div>
            </div></div>";
           
            if($this->viaDocumento>1){
               $this->html .="<p style='".$this->text_right.$this->maiuscula.$this->bolder.$this->vermelha."; margin-top:-20px; padding-right:30px;'>".$this->viaDocumento.".ª Via</p>";
            }
            
            $this->html .="</body></html>";
            $this->exibir("", "Certificado - ".valorArray($this->sobreAluno, "nomeAluno"));
        }        
        public function certificadoMais18(){

            $media10 = $this->calculadorMediaPorClasse(10);
            $media11 = $this->calculadorMediaPorClasse(11);
            $media12 = $this->calculadorMediaPorClasse(12);
            $media13 = $this->calculadorMediaPorClasse(13);


            $PAP = (int) valorArray($this->sobreAluno, "provAptidao", "escola");
            $NEC = (int) valorArray($this->sobreAluno, "notaEstagio", "escola");

            $PAP = number_format($PAP, 0);
            $NEC = number_format($NEC, 0);
            
            if($media13>0){
                $PC = ($media10+$media11+$media12+$media13)/4;
            }else{
                $PC = ($media10+$media11+$media12)/3;
            }

            $PC = number_format($PC, 0);
            
            $colspan=7;
            if($NEC>0 && $PAP>0){
                $MFC = (3*$PC+$NEC+$PAP)/5;
            }else{
                $MFC = $PC;
               // $colspan=6;
            }
            
            $MFC = number_format($MFC, 0);

            $this->html .="<html style='margin:20px; margin-top:5px; margin-right:10px; margin-left:10px; font-family: Times New Roman !important;'>
            <head>
                <title>Certificado de Habilitações</title>
                <style>
                    table tr td{
                        padding:0px;
                    }
                    p{
                        font-size:12.5pt;
                    }
                </style>
            </head>
            <body>";
            //$this->html .="<div style='height:1055px;'><img src='".$_SESSION["directorioPaterno"]."angoschool/icones/sombraGeral.png' style='position:absolute; margin-left:-40px; margin-top:-40px; width:805px; height:1140px;'>";


            $this->html .="<div style='border:double black 5px; margin:18px; padding-top:-15px; margin-bottom:-30px; height:1063px;'>".$this->fundoDocumento("../../../");

            $qrCode = $this->qrCode("Nome d".$this->art1." Alun".$this->art1.": ".valorArray($this->sobreAluno, "nomeAluno")."; Nome da Instituição: ".valorArray($this->sobreUsuarioLogado, "nomeEscola")."; Curso/Opção: ".$this->nomeCurso."; Número Interno: ".valorArray($this->sobreAluno, "numeroInterno")."; Média Final do Curso: ".$MFC."; Ano de Conclusão: ".$this->anoFinalizado."; Operador: ".valorArray($this->sobreUsuarioLogado, "nomeEntidade")."; Data de Emissão:".$this->dataSistema, "../../../", 110, 110);
            $this->html .= $this->vistoDirectorMunicipal(45, $qrCode)."<div class='cabecalho'>
                    <p style='".$this->text_center.$this->miniParagrafo."margin-top:10px;'><img src='../../../icones/insigniaDeclaracao.jpg' class='text-center' style='width:45px; height:45px;'></p>
                    <p style='".$this->text_center.$this->miniParagrafo.$this->maiuscula."'  style='font-size:13pt;'>República de Angola</p>
                    <p style='".$this->text_center.$this->maiuscula."font-size:13pt; margin-bottom:-15px;'>Ministério da Educação </p>
                    
                    <p style='".$this->text_center."' style='font-size: 13pt;'>ENSINO SECUNDÁRIO PEDAGÓGICO</p>

                      <p  style='font-size: 14pt; margin-top:-15px;".$this->text_center.$this->bolder."'>CERTIFICADO</p>
                </div>
 
                <div style='margin-top:-35px; padding:10px;'>
                    <p style='line-height: 17px;".$this->text_justify." font-size:11pt;'>a) <span style='".$this->bolder.$this->maiuscula."'>".$this->nomeDirigente(7)."</span>, Director do ".valorArray($this->sobreUsuarioLogado, "nomeEscola");
                    
                    if(valorArray($this->sobreUsuarioLogado, "decretoCriacaoInstituicao")!=""){
                       $this->html .=", criad".$this->art1Escola." sob o Decreto Executivo ".valorArray($this->sobreUsuarioLogado, "decretoCriacaoInstituicao");
                    }
                    $this->html .=", certifica que <span style='".$this->bolder.$this->vermelha.$this->maiuscula."'>".valorArray($this->sobreAluno, "nomeAluno")."</span>, filh".$this->art1." de ".valorArray($this->sobreAluno, "paiAluno")." e de ".valorArray($this->sobreAluno, "maeAluno").", natural ".valorArray($this->sobreAluno, "preposicaoComuna2")." ".valorArray($this->sobreAluno, "nomeComuna").", Município ".valorArray($this->sobreAluno, "preposicaoMunicipio2")." ".valorArray($this->sobreAluno, "nomeMunicipio").", Província ".valorArray($this->sobreAluno, "preposicaoProvincia2")." ".valorArray($this->sobreAluno, "nomeProvincia").", nascid".$this->art1." aos ".dataExtensa(valorArray($this->sobreAluno, "dataNascAluno")).", ".$this->identificacaoAluno.", concluiu no ano lectivo ".$this->anoFinalizado." o <strong>II CICLO DO ENSINO SECUNDÁRIO PEDAGÓGICO</strong>, na Especialidade de <strong>".$this->nomeCurso."</strong>, conforme o disposto na alínea f) do artigo 109.º da LBSEE 32/20, de 12 de Agosto, com a Média Final do Curso de <strong>".$MFC."</strong> valores obtidas nas seguintes classificações por disciplina:</p>
                
            <div style='width:100%; margin-left:0%;'>
                <table  style='width:100%; font-size:10pt; margin-top:-10px;".$this->tabela."'>";

                $this->html .="<tr><td style='".$this->text_center.$this->bolder.$this->border()."'>Disciplinas</td><td style='".$this->text_center.$this->bolder.$this->border()."'>".$this->retornarClasse($this->anoFinalizado, 10).".ª<br/> Classe</td><td style='".$this->text_center.$this->bolder.$this->border()."'>".$this->retornarClasse($this->anoFinalizado, 11).".ª<br/> Classe</td><td style='".$this->text_center.$this->bolder.$this->border()."'>".$this->retornarClasse($this->anoFinalizado, 12).".ª<br/> Classe</td><td style='".$this->text_center.$this->bolder.$this->border()."'>".$this->retornarClasse($this->anoFinalizado, 13).".ª<br/> Classe</td><td style='".$this->text_center.$this->bolder.$this->border()."'>Média<br/>Final</td><td style='".$this->text_center.$this->bolder.$this->border()."'>Média por<br/> Extenso</td></tr>";

            $tiposDisciplinas=array();
                foreach (distinct2($this->notas, "tipoDisciplina") as $tipo) {
                    if($tipo=="FG"){
                        $tiposDisciplinas[]=array("ordenacao"=>1, "tipoDisciplina"=>"FG");
                    }else if($tipo=="FE"){
                        $tiposDisciplinas[]=array("ordenacao"=>2, "tipoDisciplina"=>"FE");
                    }else if($tipo=="FP"){
                        $tiposDisciplinas[]=array("ordenacao"=>3, "tipoDisciplina"=>"FP");
                    }
                }    
                

            foreach (ordenar($tiposDisciplinas, "ordenacao ASC") as $tipo) {
                    $this->html .="<tr><td  style='padding-left:10px;".$this->border()."' colspan='".$colspan."'><strong>".tipoDisciplina($tipo["tipoDisciplina"])."</strong></td></tr>";
                    
                    
                    foreach (distinct2(array_filter($this->notas, function($mamale) use ($tipo){
                        return $mamale["tipoDisciplina"]==$tipo["tipoDisciplina"];}), "idPNomeDisciplina") as $idPNomeDisciplina) {
                    
                    
                            $nomeDisciplina="";
                            $somaTotalNotas=0;
                            $contadorNotas=0;
    
                            $nota10="";
                            $nota11="";
                            $nota12="";
                            $nota13="";
                            foreach (array_filter($this->notas, function($mamale) use ($idPNomeDisciplina){
                                return $mamale["idPNomeDisciplina"]==$idPNomeDisciplina;}) as $nota) {

                                $nomeDisciplina=$nota["abreviacaoDisciplina1"];
                                if(($nota["idPNomeDisciplina"]==20 || $nota["idPNomeDisciplina"]==21) && $this->modLinguaEstrangeira=="opcional"){
                                    $nomeDisciplina="Língua Estrangeira";
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
                                }else if($nota["classePauta"]==13){
                                    
                                    $nota13 = $nota["mf"];
                                    $contadorNotas++;
                                    $somaTotalNotas +=$nota["mf"];
                                }
                            }
    
                            if($contadorNotas==0){
                                $mediaDisciplina=0;
                            }else{
                                $mediaDisciplina = number_format(($somaTotalNotas/$contadorNotas), 0);
                            }
                            
                            $nomeDisciplina = str_replace("Met. Ens.", "Met. de Ensino de ", $nomeDisciplina);
                            $this->html .="<tr><td style='".$this->border()."padding-left:3px;'>".$nomeDisciplina."</td>".$this->retornarNotas($nota10, "", "==").$this->retornarNotas($nota11, "", "==").$this->retornarNotas($nota12, "", "==").$this->retornarNotas($nota13, "", "==").$this->retornarNotas($mediaDisciplina, "", "==")."<td style='".$this->text_center.$this->bolder.$this->border()."'>".primeiraLetraMaiuscula($this->retornarNotaExtensa(number_format($mediaDisciplina, 0, ",", ".")))."</td></tr>"; 
                    }
                }               
    
            if($NEC>0 && $PAP>0){
                $this->html .="
                    <tr><td style='".$this->border()."padding-left:5px;'>Estágio Profissional Supervisionado</td><td style='".$this->text_center.$this->border()."'>==</td><td style='".$this->text_center.$this->border()."'>==</td><td style='".$this->text_center.$this->bolder.$this->border()."'>==</td><td style='".$this->text_center.$this->bolder.$this->border()."'>".$NEC."</td><td style='".$this->text_center.$this->bolder.$this->border()."'>".$NEC."</td><td style='".$this->text_center.$this->bolder.$this->border()."'>".primeiraLetraMaiuscula($this->retornarNotaExtensa(number_format($NEC, 0, ",", ".")))."</td></tr>               
                    <tr><td style='".$this->border()." padding-left:5px;'>Avaliação Final de Aptidão para Docência</td><td style='".$this->text_center.$this->bolder.$this->border()."'>==</td><td style='".$this->text_center.$this->border()."'>==</td><td style='".$this->text_center.$this->border()."'>==</td><td style='".$this->text_center.$this->bolder.$this->border()."'>".$PAP."</td><td style='".$this->text_center.$this->bolder.$this->border()."'>".$PAP."</td><td style='".$this->text_center.$this->bolder.$this->border()."'>".primeiraLetraMaiuscula($this->retornarNotaExtensa(number_format($PAP, 0, ",", ".")))."</td></tr>";
            }else if($PAP>0){
                $this->html .="<tr><td style='".$this->border()." padding-left:5px;'>Avaliação Final de Aptidão para Docência</td><td style='".$this->text_center.$this->bolder.$this->border()."'>==</td><td style='".$this->text_center.$this->border()."'>==</td><td style='".$this->text_center.$this->border()."'>==</td><td style='".$this->text_center.$this->border()."'>".$PAP."</td><td style='".$this->text_center.$this->border()."'>".$PAP."</td><td style='".$this->text_center.$this->bolder.$this->border()."'>".primeiraLetraMaiuscula($this->retornarNotaExtensa(number_format($PAP, 0, ",", ".")))."</td></tr>";
            }


            $this->html .="</table></div>
                <p  style='line-height: 19px; margin-top:5px;".$this->text_justify." font-size:11pt;'>Para efeitos legais lhe é passado o presente CERTIFICADO, que consta no livro n.º ".$this->numeroPauta.", folha ".$this->numeroAnterior.", assinado por mim e autenticado com carimbo a óleo em uso neste estabelecimento de ensino.</p> 
                <p c style='line-height: 17px; margin-top:-5px;".$this->maiuscula.$this->text_center." font-size:11pt;'>".$this->rodape().".</p>

                <div style='margin-top: -19px;".$this->text_center." font-size:11pt;'>";
                    $this->nomeDirigente(8);
                    $peda ="(Subdirector Pedagógico)";
                    if($this->sexoDirigente=="F"){
                        $peda ="(Subdirectora Pedagógica)";
                    }
            
            $this->html .="
                <div style='width: 50%; font-size:11pt;'>".$this->porAssinatura("Conferido por", $this->tituloNomeDirigente."<br/>".$peda, "", strlen($this->tituloNomeDirigente))."
                </div>
                <div style='width: 50%;margin-top:-300px; margin-left:50%; font-size:11pt;'>".$this->assinaturaDirigentes(7, "", "", "", "nao")."
                </div>
            </div>
            </div></div>";
            
            if($this->viaDocumento>1){
               $this->html .="<p style='".$this->text_right.$this->maiuscula.$this->bolder.$this->vermelha."; margin-top:-20px; padding-right:30px;'>".$this->viaDocumento.".ª Via</p>";
            }
            
        $this->html .="</body></html>";

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
        
        private function retornarClasse($anoLectivo, $classe){
            //De 1988 a 1992, o II ciclo começava na 9.ª classe e terminava na 12.ª classe!!! Neste caso 10.ª actual equivale a 9.ª classe antigamente.
             if((int)$anoLectivo<=2006){
                return ($classe-1);
            }else{
                return $classe;
            }
        }
    }

?>