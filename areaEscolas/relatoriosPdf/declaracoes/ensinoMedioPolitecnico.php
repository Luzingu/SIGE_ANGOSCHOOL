<?php 
if(session_status()!==PHP_SESSION_ACTIVE){
  session_start();
}
include_once ('../../funcoesAuxiliares.php');
include_once ('../../funcoesAuxiliaresDb.php');

class ensinoMedioPolitecnico extends funcoesAuxiliares{
    function __construct(){
        parent::__construct(); 
    }

    public function declaracaoSemNotas(){
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
        <body>".$this->fundoDocumento("../../../")."".$this->cabecalho()."
        <p style='".$this->bolder.$this->text_center.$this->sublinhado."'>DECLARAÇÃO DE FREQUÊNCIA</p>

            <div class='p13' style='margin-top:-20px; padding:10px;'>

                <p style=' line-height:25px;".$this->text_justify."'><strong style='".$this->maiuscula."'>";
                if($_SESSION['idEscolaLogada']==10){
                    $this->html .=$this->nomeDirigente(8)."</strong>, Subdirector".$this->art2Dirigente." Pedagógico d".$this->art1Escola." ".valorArray($this->sobreUsuarioLogado, "nomeEscola");
                }else{
                    $this->html .=$this->nomeDirigente(7)."</strong>, Director".$this->art2Dirigente." d".$this->art1Escola." ".valorArray($this->sobreUsuarioLogado, "nomeEscola");    
                }
                    
                    if(valorArray($this->sobreUsuarioLogado, "privacidadeEscola")=="Pública"){
                       $this->html .=", criad".$this->art1Escola." sob o decreto Executivo ".valorArray($this->sobreUsuarioLogado, "decretoCriacaoInstituicao");
                    }
                    $this->html .=", declara que <strong style='".$this->vermelha.$this->maiuscula."'>".valorArray($this->sobreAluno, "nomeAluno")."</strong>, filh".$this->art1." de ".tratarCamposVaziosComEComercial(valorArray($this->sobreAluno, "paiAluno"), 15)." e de ".tratarCamposVaziosComEComercial(valorArray($this->sobreAluno, "maeAluno"), 15).", nascid".$this->art1." aos ".dataExtensa(valorArray($this->sobreAluno, "dataNascAluno")).", natural ".valorArray($this->sobreAluno, "preposicaoComuna2")." ".valorArray($this->sobreAluno, "nomeComuna").", Município ".valorArray($this->sobreAluno, "preposicaoMunicipio2")." ".valorArray($this->sobreAluno, "nomeMunicipio").", Província ".valorArray($this->sobreAluno, "preposicaoProvincia2")." ".valorArray($this->sobreAluno, "nomeProvincia").", ".$this->identificacaoAluno.", matriculad".$this->art1." nesta instituição, frequenta neste ano lectivo ".$this->anoFinalizado." o curso de <strong>".$this->nomeCurso."</strong>, na Área de <strong>".$this->areaFormacaoCurso."</strong>, da Formação Média Técnica ";
                    if(valorArray($this->sobreUsuarioLogado, "periodosEscolas")=="regPos"){
                        $this->html .=" em (regime ".$periodoAluno.") ";
                    }

                 $this->html .="na ".classeExtensa($this, $this->idPCurso,$this->classe).", turma ".$this->turma.".</p>
                 <p style='line-height:25px;  margin-top:20px;".$this->text_justify.$this->bolder."'>OBS: <span style='".$this->sublinhado."'>Esta Declaração destina-se para ".tratarCamposVaziosComEComercial($this->efeitoDeclaracao, 17).".</span></p>

                <p style='line-height:30px;  margin-top:20px;".$this->text_justify."'>Por ser verdade, e me ter solicitado, mandei passar a presente Declaração que vai por mim assinada e autenticada com o carimbo a óleo em uso neste estabelecimento de ensino.</p>

                 <p style='margin-top:30px;".$this->text_justify."'>".$this->rodape()."</p>

                <div style='width: 50%; margin-left:25%; margin-top:20px;".$this->text_center."'>";

                if($_SESSION['idEscolaLogada']==10){
                    $this->html .=$this->assinaturaDirigentes(8, "", "", "", "nao");    
                }else{
                    $this->html .=$this->assinaturaDirigentes(7, "", "", "", "nao");
                }
            $this->html .="</div>
        </body></html>";

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
            if($nota["continuidadeDisciplina"]=="T"){
                $nota["mf"]=nelson($nota, "cf");
            }
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

        $this->html .="<html style='margin:30px; margin-right:60px; margin-left:40px; font-family: Times New Roman !important;'>
        <head>
            <title>Declaração</title>
            <style>
                p{
                    font-family: Times New Roman !important;
                    font-size: 12pt;
                }
            </style>
        </head>
        <body style='padding: 0px; margin: 0px;'>
        <div style='height:1055px;'>
            <img src='".$_SERVER['DOCUMENT_ROOT']."/angoschool/icones/sombraGeral.png' style='position:absolute; margin-left:-40px; margin-top:-40px; width:780px; height:1130px;'>".$this->fundoDocumento("../../../");

        $qrCode = $this->qrCode("Nome d".$this->art1." Alun".$this->art1.": ".valorArray($this->sobreAluno, "nomeAluno")."; Nome da Instituição: ".valorArray($this->sobreUsuarioLogado, "nomeEscola")."; Curso/Opção: ".$this->nomeCurso."; Número Interno: ".valorArray($this->sobreAluno, "numeroInterno")."; Ano de Conclusão: ".$this->anoFinalizado."; Operador: ".valorArray($this->sobreUsuarioLogado, "nomeEntidade")."; Data de Emissão:".$this->dataSistema, "../../../", 110, 110);
            $this->html .= $this->vistoDirectorMunicipal(45, $qrCode)."<div class='cabecalho'>
            <p style='".$this->text_center.$this->miniParagrafo."margin-top:10px;'><img src='../../../icones/insigniaDeclaracao.jpg' class='text-center' style='width:50px; height:50px;'></p>
            <p style='".$this->text_center.$this->miniParagrafo."font-size:12pt;'>REPÚBLICA DE ANGOLA</p>
            <p style='".$this->text_center."font-size:12pt;'>MINISTÉRIO DA EDUCAÇÃO</p>";
            
            if($_SESSION["idEscolaLogada"]==28){
                $this->html .="<p style='".$this->text_center.$this->maiuscula."font-size: 12pt;'>".valorArray($this->sobreUsuarioLogado, "nomeEscola")."</p>
                <p style='font-size: 14pt; margin-top:0px;".$this->bolder.$this->text_center."'>DECLARAÇÃO DE HABILITAÇÕES LITERÁRIAS</p>";
            }else{
                $this->html .="<p style='".$this->text_center."font-size: 12pt;'>ENSINO SECUNDÁRIO TÉCNICO-PROFISSIONAL</p>
                <p style='font-size: 14pt; margin-top:3px;".$this->bolder.$this->text_center."'>DECLARAÇÃO DE HABILITAÇÕES</p>";
            }
            
            
            $this->html .="</div>
            <div class='p12' style='margin-top:-30px; padding:10px;'>
                <p style='line-height: 17px;".$this->text_justify."'>
                    <span style='".$this->bolder.$this->maiuscula."'>
                    ";
            if($_SESSION['idEscolaLogada']==10){
                $this->html .=$this->nomeDirigente(8)."</span>, Subdirector".$this->art2Dirigente." Pedagógico d".$this->art1Escola." ".valorArray($this->sobreUsuarioLogado, "nomeEscola");
            }else{
                $this->html .=$this->nomeDirigente(7)."</span>, Director".$this->art2Dirigente." d".$this->art1Escola." ".valorArray($this->sobreUsuarioLogado, "nomeEscola");    
            }
            
                    
                    if(valorArray($this->sobreUsuarioLogado, "decretoCriacaoInstituicao")!=""){
                       $this->html .=", criad".$this->art1Escola." sob o decreto Executivo ".valorArray($this->sobreUsuarioLogado, "decretoCriacaoInstituicao");
                    }
                    $this->html .=", declara que <strong style='".$this->vermelha.$this->maiuscula."'>".valorArray($this->sobreAluno, "nomeAluno")."</strong> filh".$this->art1." de ".tratarCamposVaziosComEComercial(valorArray($this->sobreAluno, "paiAluno"), 15)." e de ".tratarCamposVaziosComEComercial(valorArray($this->sobreAluno, "maeAluno"), 15).", nascid".$this->art1." aos ".dataExtensa(valorArray($this->sobreAluno, "dataNascAluno")).", natural ".valorArray($this->sobreAluno, "preposicaoComuna2")." ".valorArray($this->sobreAluno, "nomeComuna").", Município ".valorArray($this->sobreAluno, "preposicaoMunicipio2")." ".valorArray($this->sobreAluno, "nomeMunicipio").", Província ".valorArray($this->sobreAluno, "preposicaoProvincia2")." ".valorArray($this->sobreAluno, "nomeProvincia").", ".$this->identificacaoAluno.".<br/>Frequentou neste estabelecimento de ensino no ano lectivo de ".$this->anoFinalizado.", o <strong>II CICLO DO ENSINO SECUNDÁRIO TÉCNICO</strong>, do curso de <strong>".$this->nomeCurso."</strong>, na Área de <strong>".$this->areaFormacaoCurso."</strong>";

                    if(valorArray($this->sobreUsuarioLogado, "nomeEscola")=="regPos"){
                        $this->html .=" em (regime ".$periodoAluno.")";
                    }

                    $this->html .=" na <strong>".classeExtensa($this, $this->idPCurso,$this->classe)."</strong>, na turma ".$this->turma." sob o n.º ".completarNumero($this->numeroAnterior).", tendo obtido o resultado final <strong>Apt".$this->art1."</strong> e com as seguintes classificações por disciplina: 
                </p> 
            </div>

            <div style='width:80%; margin-left:10%; margin-top:-5px;'>
            <table style='width:100%; font-size:11pt; margin-top:-10px;".$this->tabela."'>
            <tr><td style='".$this->border().$this->bolder.$this->text_center."'>Componentes de Formação</td><td style='".$this->border().$this->bolder.$this->text_center."'>Média Final</td><td style='".$this->border().$this->bolder.$this->text_center."'>Média por Extenso</td></tr>";

            foreach (distinct2($this->notas, "tipoDisciplina") as $tipo) {
                $this->html .="<tr><td colspan='3' style='padding-left:4px;".$this->border().$this->bolder."'>".tipoDisciplina($tipo)."</td></tr>";
                foreach (array_filter($this->notas, function($mamale) use ($tipo){
                        return $mamale["tipoDisciplina"]==$tipo;}) as $disciplina) {

                    $this->html .="<tr><td style='padding-left:4px;".$this->border()."'>".$disciplina["abreviacaoDisciplina1"]."</td>".$this->retornarNotas($disciplina["mf"])."<td style='".$this->border().$this->text_center."'>".primeiraLetraMaiuscula($this->retornarNotaExtensa(number_format($disciplina["mf"], 0, ",", ".")))."</td></tr>";                   
                }
            }      
        $this->html .="</table></div>
            <p style='font-size:12pt; margin-top:2px; padding-left:10px; padding-right:10px;".$this->text_justify."'>A presente Declaração destina-se para ".$this->efeitoDeclaracao.".</p>

            <p  style='font-size:12pt; margin-top:-10px; padding-left:10px; padding-right:10px;".$this->text_justify."'>Por ser verdade e me ter sido solicitada, mandei passar a presente Declaração, que vai por mim assinada e autenticada com o carimbo a óleo em uso nesta Instituição.</p>

            <p style='font-size:12pt; padding-left:10px; padding-right:10px; margin-top:-7px;".$this->text_justify."'>".$this->rodape().".</p>";

            if($_SESSION['idEscolaLogada']==10){
                $this->html .="
                <div style='margin-top: -20px;".$this->text_center."'>";
                $this->nomeDirigente(10);
                $peda ="";
                if($this->tituloNomeDirigente!=""){
                    $peda ="(Chefe da Secretaria)";
                }
                $this->html .="
                    <div style='width: 50%;'>".$this->porAssinatura("Conferido por", $this->tituloNomeDirigente."<br/>".$peda, "", strlen($this->tituloNomeDirigente))."
                    </div>
                    <div style='width: 50%;margin-top:-300px; margin-left:50%;'>".$this->assinaturaDirigentes(8, "", "", "", "nao")."</div>
                    </div>
                </div>";
            }else{
                $this->html .="
                <div style='margin-top: -20px;".$this->text_center."'>";
                $this->nomeDirigente(8);
                $peda ="";
                if($this->tituloNomeDirigente!=""){
                    $peda ="(Subdirector".$this->art2Dirigente." Pedagógico)";
                }
                $this->html .="
                    <div style='width: 50%;'>".$this->porAssinatura("Conferido por", $this->tituloNomeDirigente."<br/>".$peda, "", strlen($this->tituloNomeDirigente))."
                    </div>
                    <div style='width: 50%;margin-top:-300px; margin-left:50%;'>".$this->assinaturaDirigentes(7, "", "", "", "nao")."</div>
                    </div>
                </div>";
            }

            if($this->viaDocumento>1){
               $this->html .="<p style='".$this->text_right.$this->maiuscula.$this->bolder.$this->vermelha."; margin-top:-35px; padding-right:30px;'>".$this->viaDocumento.".ª Via</p>";
            }
        $this->html .="</div></body></html>";

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

            if(nelson($nota, "cf")>0){
                $nota["mf"]=nelson($nota, "cf");
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


        if(count($this->notas)>18){
            $this->certificadoMais18Disciplinas();
        }else{
            $this->certificadoSimples();
        }
    }

    private function certificadoSimples(){
        
        $totalDisc=0;
        $totalNotas=0;
        $PC=0;
        foreach ($this->notas as $nota) {
            $totalNotas +=$nota["mf"];
            $totalDisc++;
        }

        if($totalDisc==0){
            $PC=0;
        }else{
            $PC = number_format($totalNotas/$totalDisc, 0);
        }

        $PAP = (double) valorArray($this->sobreAluno, "provAptidao", "escola");
        $NEC = (double) valorArray($this->sobreAluno, "notaEstagio", "escola");

        $PAP = number_format($PAP, 0);
        $NEC = number_format($NEC, 0);
        
        if($this->anoFinalizado>2017){
            $MFC = (4*$PC+$NEC+$PAP)/6;
        }else{
            $MFC = (2*$PC+$PAP)/3;
        }
        $MFC = number_format($MFC, 0);

         
        
        $periodoAluno = valorArray($this->sobreAluno, "periodoAluno", "escola");
        if($periodoAluno=="reg"){
            $periodoAluno="Regular";
        }else{
            $periodoAluno="Pós-Laboral";
        }

        $this->html .="<html style='margin:30px; margin-right:50px; margin-left:30px; font-family: Times New Roman !important;'>
        <head>
            <title>Certificado de Habilitações</title>
            <style>
                table tr td{
                    padding:0px;
                }
                p{
                    font-size:12pt;
                }
            </style>
        </head>
        <body>
        <div style='height:1055px;'>
            <img src='".$_SERVER['DOCUMENT_ROOT']."/angoschool/icones/sombraGeral.png' style='position:absolute; margin-left:-40px; margin-top:-40px; width:800px; height:1135px;'>".$this->fundoDocumento("../../../");
        
        $qrCode = $this->qrCode("Nome d".$this->art1." Alun".$this->art1.": ".valorArray($this->sobreAluno, "nomeAluno")."; Nome da Instituição: ".valorArray($this->sobreUsuarioLogado, "nomeEscola")."; Curso/Opção: ".$this->nomeCurso."; Número Interno: ".valorArray($this->sobreAluno, "numeroInterno")."; Média Final do Curso: ".$MFC."; Ano de Conclusão: ".$this->anoFinalizado."; Operador: ".valorArray($this->sobreUsuarioLogado, "nomeEntidade")."; Data de Emissão:".$this->dataSistema, "../../../", 110, 110);

        $this->html .= $this->vistoDirectorMunicipal(45, $qrCode)."<div class='cabecalho'>
                <p style='".$this->text_center.$this->miniParagrafo."margin-top:20px;'><img src='../../../icones/insigniaDeclaracao.jpg' class='text-center' style='width:55px; height:55px;'></p>
                <p style='".$this->text_center.$this->miniParagrafo.$this->maiuscula."'  style='font-size:12pt;'>República de Angola</p>
                <p style='".$this->text_center.$this->maiuscula."' style='font-size:12pt;'>Ministério da Educação </p>
                  <p style='".$this->text_center."' style='font-size: 12pt; margin-top:-40px;'>ENSINO SECUNDÁRIO TÉCNICO-PROFISSIONAL</p>

                  <p  style='font-size: 14pt; margin-top:-10px;".$this->text_center.$this->bolder."'>CERTIFICADO</p>
            </div>

            <div style='margin-top:-32px; padding:10px;'>
                <p style='line-height: 17px;".$this->text_justify."'><span style='".$this->bolder.$this->maiuscula."'>".$this->nomeDirigente(7)."</span>, Director d".$this->art1Escola." ".valorArray($this->sobreUsuarioLogado, "nomeEscola");
                    
        if(valorArray($this->sobreUsuarioLogado, "decretoCriacaoInstituicao")!=""){
           $this->html .=", criad".$this->art1Escola." sob o decreto Executivo ".valorArray($this->sobreUsuarioLogado, "decretoCriacaoInstituicao");
        }
        $this->html .=", certifica que <span style='".$this->bolder.$this->vermelha.$this->maiuscula."'>".valorArray($this->sobreAluno, "nomeAluno")."</span>, filh".$this->art1." de ".valorArray($this->sobreAluno, "paiAluno")." e de ".valorArray($this->sobreAluno, "maeAluno").", natural ".valorArray($this->sobreAluno, "preposicaoComuna2")." ".valorArray($this->sobreAluno, "nomeComuna").", Município ".valorArray($this->sobreAluno, "preposicaoMunicipio2")." ".valorArray($this->sobreAluno, "nomeMunicipio").", Província ".valorArray($this->sobreAluno, "preposicaoProvincia2")." ".valorArray($this->sobreAluno, "nomeProvincia").", nascid".$this->art1." aos ".dataExtensa(valorArray($this->sobreAluno, "dataNascAluno")).", ".$this->identificacaoAluno.", concluiu no ano lectivo ".$this->anoFinalizado." o <strong>II CICLO DO ENSINO SECUNDÁRIO TÉCNICO</strong>, do curso de <strong>".$this->nomeCurso."</strong>, na Área de <strong>".$this->areaFormacaoCurso."</strong>, conforme o disposto na alínea f) do artigo 109.º da LBSEE 17/16, de 7 de Outubro, com a Média Final de <strong>".$MFC."</strong> valores obtidas nas seguintes classificações por disciplina:</p>
            
        <div style='width:80%; margin-left:10%;'>
            <table  style='width:100%; font-size:11pt; margin-top:-10px;".$this->tabela."'>";

            $this->html .="<tr><td style='".$this->bolder.$this->border().$this->bolder.$this->text_center."'>Componente de Formação</td><td style='".$this->bolder.$this->border().$this->bolder.$this->text_center."'>Média Final</td><td style='".$this->bolder.$this->border().$this->bolder.$this->text_center."'>Média por Extenso</td></tr>";
            foreach (distinct2($this->notas, "tipoDisciplina") as $tipo) {
                $this->html .="<tr><td colspan='3' style='padding-left:4px;".$this->border().$this->bolder."'>".tipoDisciplina($tipo)."</td></tr>";

                foreach (array_filter($this->notas, function($mamale) use ($tipo){
                        return $mamale["tipoDisciplina"]==$tipo;}) as $disciplina) {

                    $this->html .="<tr><td style='padding-left:4px;".$this->border()."'>".$disciplina["abreviacaoDisciplina1"]."</td>".$this->retornarNotas(number_format($disciplina["mf"], 0, ",", "."))."<td style='".$this->text_center.$this->border()."'>".primeiraLetraMaiuscula($this->retornarNotaExtensa(number_format($disciplina["mf"], 0, ",", ".")))."</td></tr>";
                   
                }
            }
            $this->html .="<tr><td style='padding-left:4px;".$this->border()."'>Média do Plano Curricular (PC)</td><td style='".$this->border().$this->text_center."'>".$PC."</td><td style='".$this->border().$this->text_center."'>".primeiraLetraMaiuscula($this->retornarNotaExtensa(number_format($PC, 0, ",", ".")))."</td></tr>";
        
            $this->html .="<tr><td style='padding-left:4px;".$this->border()."'>Prova de Aptidão Profissional (PAP)</td><td style='".$this->border().$this->text_center."'>".$PAP."</td><td style='".$this->border().$this->text_center."'>".primeiraLetraMaiuscula($this->retornarNotaExtensa(number_format($PAP, 0, ",", ".")))."</td></tr>";
            
          if($this->anoFinalizado>2017){
            $this->html .="<tr><td style='padding-left:4px;".$this->border()."'>Estágio Curricular Supervisionado (ECS)</td><td style='".$this->border().$this->text_center."'>".$NEC."</td><td style='".$this->border().$this->text_center."'>".primeiraLetraMaiuscula($this->retornarNotaExtensa(number_format($NEC, 0, ",", ".")))."</td></tr>";
            
              $this->html .="<tr><td style='padding-left:4px;".$this->border().$this->bolder."'>Classificação Final do Curso (4*PC+PAP+ECS)/6</td><td style='".$this->border().$this->text_center.$this->bolder."'>".$MFC."</td><td style='".$this->border().$this->bolder.$this->text_center."'>".primeiraLetraMaiuscula($this->retornarNotaExtensa(number_format($MFC, 0, ",", ".")))."</td></tr>";
          }else{
              $this->html .="<tr><td style='padding-left:4px;".$this->border().$this->bolder."'>Classificação Final do Curso (2*PC+PAP)/3</td><td style='".$this->border().$this->text_center.$this->bolder."'>".$MFC."</td><td style='".$this->border().$this->bolder.$this->text_center."'>".primeiraLetraMaiuscula($this->retornarNotaExtensa(number_format($MFC, 0, ",", ".")))."</td></tr>";
          }

        $this->html .="</table></div>
            <p  style='line-height: 17px;".$this->text_justify." margin-top:7px;'>Para efeitos legais lhe é passado o presente CERTIFICADO, que consta no livro de registo n.º ".$this->numeroPauta.", folha n.º ".$this->numeroAnterior.",  assinado por mim e autenticado com carimbo a óleo em uso neste Estabelecimento de Ensino.</p> 

            <p style='line-height: 17px;".$this->maiuscula.$this->text_justify."margin-top:3px;'>".$this->rodape().".</p>

                <div style='margin-top: -17px;".$this->text_center."'>";
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
                </div>";
                    
                $this->html .="</div>
        </div>";
        if($this->viaDocumento>1){
           $this->html .="<p style='".$this->text_right.$this->maiuscula.$this->bolder.$this->vermelha."; margin-top:-30px; padding-right:30px;'>".$this->viaDocumento.".ª Via</p>";
        }
        $this->html .="</body></html>";

        $this->exibir("", "Certificado - ".valorArray($this->sobreAluno, "nomeAluno"));
    }

    private function certificadoMais18Disciplinas(){
        
        $totalDisc=0;
        $totalNotas=0;
        $PC=0;
        foreach ($this->notas as $nota) {
            $totalNotas +=$nota["mf"];
            $totalDisc++;
        }

        if($totalDisc==0){
            $PC=0;
        }else{
            $PC = number_format($totalNotas/$totalDisc, 0);
        }

        $PAP = (double) valorArray($this->sobreAluno, "provAptidao", "escola");
        $NEC = (double) valorArray($this->sobreAluno, "notaEstagio", "escola");

        $PAP = number_format($PAP, 0);
        $NEC = number_format($NEC, 0);
        
        if($this->anoFinalizado>2017){
            $MFC = (4*$PC+$NEC+$PAP)/6;
        }else{
            $MFC = (2*$PC+$PAP)/3;
        }
        $MFC = number_format($MFC, 0);

        
        
        $periodoAluno = valorArray($this->sobreAluno, "periodoAluno", "escola");
        if($periodoAluno=="reg"){
            $periodoAluno="Regular";
        }else{
            $periodoAluno="Pós-Laboral";
        }


        $this->html .="<html style='margin:30px; margin-right:50px; margin-left:30px; font-family: Times New Roman !important;'>
        <head>
            <title>Certificado de Habilitações</title>
            <style>
                table tr td{
                    padding:0px;
                }
                p{
                    font-size:12pt;
                }
            </style>
        </head>
        <body>
        <div style='height:1055px;'>
            <img src='".$_SERVER['DOCUMENT_ROOT']."/angoschool/icones/sombraGeral.png' style='position:absolute; margin-left:-40px; margin-top:-40px; width:800px; height:1135px;'>";

        $qrCode = $this->qrCode("Nome d".$this->art1." Alun".$this->art1.": ".valorArray($this->sobreAluno, "nomeAluno")."; Nome da Instituição: ".valorArray($this->sobreUsuarioLogado, "nomeEscola")."; Curso/Opção: ".$this->nomeCurso."; Número Interno: ".valorArray($this->sobreAluno, "numeroInterno")."; Média Final do Curso: ".$MFC."; Ano de Conclusão: ".$this->anoFinalizado."; Operador: ".valorArray($this->sobreUsuarioLogado, "nomeEntidade")."; Data de Emissão:".$this->dataSistema, "../../../", 110, 110);
        
        $this->html .= $this->vistoDirectorMunicipal(45, $qrCode)."<div class='cabecalho'>
                <p style='".$this->text_center.$this->miniParagrafo."margin-top:3px;'><img src='../../../icones/insigniaDeclaracao.jpg' class='text-center' style='width:55px; height:55px;'></p>
                <p style='".$this->text_center.$this->miniParagrafo.$this->maiuscula."'  style='font-size:12pt;'>República de Angola</p>
                <p style='".$this->text_center.$this->maiuscula.$this->miniParagrafo."' style='font-size:12pt;'>Ministério da Educação </p>
                  <p style='".$this->text_center."' style='font-size: 12pt; margin-top:-40px;'>ENSINO SECUNDÁRIO TÉCNICO-PROFISSIONAL</p>

                  <p  style='font-size: 14pt; margin-top:-10px;".$this->text_center.$this->bolder."'>CERTIFICADO</p>
            </div>

            <div style='margin-top:-35px; padding:10px;'>
                <p style='line-height: 17px; font-size:11pt;".$this->text_justify."'><span style='".$this->bolder.$this->maiuscula."'>".$this->nomeDirigente(7)."</span>, Director d".$this->art1Escola." ".valorArray($this->sobreUsuarioLogado, "nomeEscola");
                    
                    if(valorArray($this->sobreUsuarioLogado, "decretoCriacaoInstituicao")!=""){
                       $this->html .=", criad".$this->art1Escola." sob o decreto Executivo ".valorArray($this->sobreUsuarioLogado, "decretoCriacaoInstituicao");
                    }
                    $this->html .=", certifica que <span style='".$this->bolder.$this->vermelha.$this->maiuscula."'>".valorArray($this->sobreAluno, "nomeAluno")."</span>, filh".$this->art1." de ".valorArray($this->sobreAluno, "paiAluno")." e de ".valorArray($this->sobreAluno, "maeAluno").", natural ".valorArray($this->sobreAluno, "preposicaoComuna2")." ".valorArray($this->sobreAluno, "nomeComuna").", Município ".valorArray($this->sobreAluno, "preposicaoMunicipio2")." ".valorArray($this->sobreAluno, "nomeMunicipio").", Província ".valorArray($this->sobreAluno, "preposicaoProvincia2")." ".valorArray($this->sobreAluno, "nomeProvincia").", nascid".$this->art1." aos ".dataExtensa(valorArray($this->sobreAluno, "dataNascAluno")).", ".$this->identificacaoAluno.", concluiu no ano lectivo ".$this->anoFinalizado." o <strong>II CICLO DO ENSINO SECUNDÁRIO TÉCNICO</strong>, do curso de <strong>".$this->nomeCurso."</strong>, na Área de <strong>".$this->areaFormacaoCurso."</strong>, conforme o disposto na alínea f) do artigo 109.º da LBSEE 17/16, de 7 de Outubro, com a Média Final de <strong>".$MFC."</strong> valores obtidas nas seguintes classificações por disciplina:</p>
            
        <div style='width:80%; margin-left:10%;'>
            <table  style='width:100%; font-size:9.5pt; margin-top:-10px;".$this->tabela."'>";

            $this->html .="<tr><td style='".$this->bolder.$this->border().$this->bolder.$this->text_center."'>Componente de Formação</td><td style='".$this->bolder.$this->border().$this->bolder.$this->text_center."'>Média Final</td><td style='".$this->bolder.$this->border().$this->bolder.$this->text_center."'>Média por Extenso</td></tr>";
             foreach (distinct2($this->notas, "tipoDisciplina") as $tipo) {
                $this->html .="<tr><td colspan='3' style='padding-left:4px;".$this->border().$this->bolder."'>".tipoDisciplina($tipo)."</td></tr>";

                foreach (array_filter($this->notas, function($mamale) use ($tipo){
                        return $mamale["tipoDisciplina"]==$tipo;}) as $disciplina) {

                    $this->html .="<tr><td style='padding-left:4px;".$this->border()."'>".$disciplina["abreviacaoDisciplina1"]."</td>".$this->retornarNotas(number_format($disciplina["mf"], 0, ",", "."))."<td style='".$this->text_center.$this->border()."'>".primeiraLetraMaiuscula($this->retornarNotaExtensa(number_format($disciplina["mf"], 0, ",", ".")))."</td></tr>";
                   
                }
            }
            $this->html .="<tr><td style='padding-left:4px;".$this->border()."'>Média do Plano Curricular (PC)</td><td style='".$this->border().$this->text_center."'>".$PC."</td><td style='".$this->border().$this->text_center."'>".primeiraLetraMaiuscula($this->retornarNotaExtensa(number_format($PC, 0, ",", ".")))."</td></tr>";
        
            $this->html .="<tr><td style='padding-left:4px;".$this->border()."'>Prova de Aptidão Profissional (PAP)</td><td style='".$this->border().$this->text_center."'>".$PAP."</td><td style='".$this->border().$this->text_center."'>".primeiraLetraMaiuscula($this->retornarNotaExtensa(number_format($PAP, 0, ",", ".")))."</td></tr>";
            
          if($this->anoFinalizado>2017){
            $this->html .="<tr><td style='padding-left:4px;".$this->border()."'>Estágio Curricular Supervisionado (ECS)</td><td style='".$this->border().$this->text_center."'>".$NEC."</td><td style='".$this->border().$this->text_center."'>".primeiraLetraMaiuscula($this->retornarNotaExtensa(number_format($NEC, 0, ",", ".")))."</td></tr>";
            
              $this->html .="<tr><td style='padding-left:4px;".$this->border().$this->bolder."'>Classificação Final do Curso (4*PC+PAP+ECS)/6</td><td style='".$this->border().$this->text_center.$this->bolder."'>".$MFC."</td><td style='".$this->border().$this->bolder.$this->text_center."'>".primeiraLetraMaiuscula($this->retornarNotaExtensa(number_format($MFC, 0, ",", ".")))."</td></tr>";
          }else{
              $this->html .="<tr><td style='padding-left:4px;".$this->border().$this->bolder."'>Classificação Final do Curso (2*PC+PAP)/3</td><td style='".$this->border().$this->text_center.$this->bolder."'>".$MFC."</td><td style='".$this->border().$this->bolder.$this->text_center."'>".primeiraLetraMaiuscula($this->retornarNotaExtensa(number_format($MFC, 0, ",", ".")))."</td></tr>";
          }
          
        $this->html .="</table></div>
            <p  style='line-height: 15px;".$this->text_justify." margin-top:7px; font-size:11pt;'>Para efeitos legais lhe é passado o presente CERTIFICADO, que consta no livro de registo n.º ".$this->numeroPauta.", folha n.º ".$this->numeroAnterior.",  assinado por mim e autenticado com carimbo a óleo em uso neste Estabelecimento de Ensino.</p> 

            <p style='line-height: 17px;".$this->text_justify."margin-top:3px; font-size:11pt;'>".$this->rodape().".</p>

                <div style='margin-top: -20px;".$this->text_center." font-size:11pt;'>";
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
                </div>";
                    
                $this->html .="</div>
        </div>";
        if($this->viaDocumento>1){
           $this->html .="<p style='".$this->text_right.$this->maiuscula.$this->bolder.$this->vermelha."; margin-top:-30px; padding-right:30px;'>".$this->viaDocumento.".ª Via</p>";
        }
        $this->html .="</body></html>";

        $this->exibir("", "Certificado - ".valorArray($this->sobreAluno, "nomeAluno"));
    }
}
?>