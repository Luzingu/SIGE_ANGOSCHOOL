<?php if(session_status()!==PHP_SESSION_ACTIVE){
    session_cache_expire(60);
      session_start();
    }
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');

    class actaDefesa extends funcoesAuxiliares{

        function __construct($caminhoAbsoluto){
            parent::__construct("Rel-Acta de Defesa");
            $this->numAno=0;
            $this->idPMatricula = isset($_GET["idPMatricula"])?$_GET["idPMatricula"]:null;

            if($this->verificacaoAcesso->verificarAcesso("", "avalFinalCurso", array(), "")){
                $this->acta2();     
                
            }else{
              $this->negarAcesso();
            }
        }

        private function acta2(){

            $this->sobreAluno($this->idPMatricula);

            $this->idPAno = valorArray($this->sobreAluno, "idMatFAno", "escola");
            $this->numAno();
            $this->idPCurso = valorArray($this->sobreAluno, "idMatCurso", "escola");
            $this->nomeCurso(); 

            $this->html="<html style='margin-left:50px; margin-right:50px; margin-top:20px; margin-bottom:0px;'>
            <head>
                <title>Acta de Defesa</title>
            </head>
            <body><div style='position: absolute;'><div style='margin-top: 20px; width:270px;'>".$this->assinaturaDirigentes("Director")."</div></div>
            ".$this->cabecalho()."
            <p style='".$this->text_center.$this->maiuscula." margin-top:-14px;'>COORDENAÇÃO DO CURSO DE <strong style='".$this->vermelha."'>".$this->nomeCurso."</strong></p>

            <p style='".$this->text_center.$this->maiuscula.$this->bolder." margin-top:0px;'>ACTA N.º ".completarNumero(valorArray($this->sobreAluno, "numeroActa", "escola"));
            for($i=1; $i<=35; $i++){
                $this->html.="&nbsp;";
            }
            $this->html .="FOLHA N.º ".completarNumero(valorArray($this->sobreAluno, "numeroFolha", "escola"))."</p>";
            
            $dataExplode = explode("-", valorArray($this->sobreAluno, "dataDefesa", "escola"));
            $diaDefesa = isset($dataExplode[2])?$dataExplode[2]:"";
            $mesDefesa = isset($dataExplode[1])?$dataExplode[1]:"";
            $anoDefesa = isset($dataExplode[0])?$dataExplode[0]:"";

            $horaExplode = explode("-", valorArray($this->sobreAluno, "horaDefesa", "escola"));
            $horaDefesa = isset($horaExplode[2])?$horaExplode[0]:"";
            $minutoDefesa = isset($horaExplode[1])?$horaExplode[1]:"";
            if($minutoDefesa==0){
                $minutoDefesa="";
            }else if($minutoDefesa==1){
                $minutoDefesa ="e".$minutoDefesa." minuto";
            }else{
                $minutoDefesa ="e".$minutoDefesa." minutos";
            }

            $presidente = isset(explode(";", valorArray($this->sobreAluno, "membrosJuriDefesa", "escola"))[0])?explode(";", valorArray($this->sobreAluno, "membrosJuriDefesa", "escola"))[0]:"";
            $vogal1 = isset(explode(";", valorArray($this->sobreAluno, "membrosJuriDefesa", "escola"))[1])?explode(";", valorArray($this->sobreAluno, "membrosJuriDefesa", "escola"))[1]:"";

            $vogal2 = isset(explode(";", valorArray($this->sobreAluno, "membrosJuriDefesa", "escola"))[2])?explode(";", valorArray($this->sobreAluno, "membrosJuriDefesa", "escola"))[2]:"";

             /*nOTAS... */
            $this->notas =array();
            for($i=10; $i<=13; $i++){
                $this->notas = array_merge($this->notas, $this->notasDeclaracao($i)); 
            }
            
            if($this->tipoCurso=="tecnico"){
                $totalDisc=0;
                $totalNotas=0;
                $PC=0; 
                foreach ($this->notas as $nota) {
                    if($nota["continuidadeDisciplina"]=="T"){
                        if(!isset($nota["cf"]) || (nelson($nota, "recurso")!=NULL && nelson($nota, "recurso")!="")){
                            $nota["cf"]=nelson($nota, "recurso");
                        }
                        $totalNotas +=$nota["cf"];
                        $totalDisc++;
                    }
                }
                if($totalDisc>0){
                    $PC = number_format($totalNotas/$totalDisc, 0);
                }
            }else{
                $media10 = $this->calculadorMediaPorClasse(10);
                $media11 = $this->calculadorMediaPorClasse(11);
                $media12 = $this->calculadorMediaPorClasse(12);
                $media13 = $this->calculadorMediaPorClasse(13);

                if((int)$this->numAno<=2020){
                    $PC = ($media10+$media11+$media12+$media13)/4;
                }else{
                    $PC = ($media10+$media11+$media12)/3;
                }
                $PC = number_format($PC, 0);
            }

            $PAP = valorArray($this->sobreAluno, "provAptidao", "escola");
            $NEC = valorArray($this->sobreAluno, "notaEstagio", "escola");

            if($PAP=="" || $PAP==NULL){
                $PAP=0;
            }
            $PAP = number_format($PAP, 0);

            if($NEC=="" || $NEC==NULL){
                $NEC=0;
            }
            $NEC = number_format($NEC, 0);

            $MFC = (4*$PC+$NEC+$PAP)/6;
            $MFC = number_format($MFC, 0);


            $this->html .="<p style='".$this->maiuscula."'>CANDIDATO(A): <strong>".valorArray($this->sobreAluno, "nomeAluno")."</strong></p>
            <p style='".$this->maiuscula."'>ORIENTADOR: _______________________________________________________________________</p>
            <p style='".$this->maiuscula.$this->bolder."margin-bottom:-12px;'>JÚRI:</p>
            <p style='".$this->maiuscula.$this->miniParagrafo."'>PRESIDENTE: <strong>".$presidente."</strong></p>
            <p style='".$this->maiuscula.$this->miniParagrafo."'>1.º VOGAL: <strong>".$vogal1."</strong></p>
            <p style='".$this->maiuscula.$this->miniParagrafo."'>2.º VOGAL: <strong>".$vogal2."</strong></p>
            <p style='".$this->maiuscula.$this->text_justify."'>TÍTULO DO TRABALHO: <strong>".valorArray($this->sobreAluno, "temaTrabalho", "escola")."</strong></p>
            <p style='".$this->maiuscula."'>LOCAL: ___________________________________________________ &nbsp;&nbsp;&nbsp;  HORA DE INICIO: ".valorArray($this->sobreAluno, "horaDefesa", "escola")."</p>
            <p style='".$this->text_justify." line-height:23px; margin-top:-12px;'>Em sessão pública, após exposição de cerca de _____ minutos, o(a) candidato(a) foi arguido(a) pelo Júri, tendo ficado aprovado com <strong>".number_format((int)valorArray($this->sobreAluno, "provAptidao", "escola"),0, ".", ",")." (".$this->retornarNotaExtensa(number_format((int)valorArray($this->sobreAluno, "provAptidao", "escola"),0, ".", ",")).")</strong> valores, auferindo assim a classificação de:</p>

            <p style='".$this->miniParagrafo."'>Plano Curricular(PC) .......................................................................................... <strong>".$PC."</strong></p>
            <p style='".$this->miniParagrafo."'>Classificação do Estágio Curricular ................................................................... <strong>".$NEC."</strong></p>
            <p style='".$this->miniParagrafo."'>Classificação da PAP ......................................................................................... <strong>".$PAP."</strong></p>
            <p style='".$this->miniParagrafo."'>Classificação final do curso (4xPC+PAP+EC)/6 ............................................... <strong>".$MFC."</strong></p>

            <p style='".$this->text_justify." line-height:25px; margin-bottom:2px;'>OBSERVAÇÕES</p>
            <div style='border:solid black 1px; height:60px;'></div>

            </div>
            <p style='".$this->text_justify." line-height:20px;'>Para que conste, lavrou-se a presente acta que é assinada pelos membros do Júri, na ordem acima determinada, e pelo(a) candidato(a):</p>
            <p style='".$this->text_justify.$this->maiuscula." line-height:20px;'>".$this->rodape()."</p>
            <p>Presidente: ____________________________________________________________________________</p>
            <p style='margin-top:-5px;'>(1º Vogal) _____________________________________________________________________________</p>
            <p style='margin-top:-5px;'>(2º Vogal) _____________________________________________________________________________</p>
            <p style='margin-top:-5px;'>Candidatos (as): ________________________________________________________________________</p>
            <p style='margin-top:-5px;'>Secretário(a): __________________________________________________________________________
            </p><div>".$this->assinaturaDirigentes(["Pedagógico"])."</div>";

            $this->exibir("", "Acta de Defesa - ".valorArray($this->sobreAluno, "nomeAluno"));
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

new actaDefesa(__DIR__);
    
    
  
?>