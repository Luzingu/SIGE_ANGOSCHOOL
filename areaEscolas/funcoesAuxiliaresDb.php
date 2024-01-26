<?php
    if(session_status()!==PHP_SESSION_ACTIVE){
        session_cache_expire(60);
      session_start();
    }
    include_once ($_SERVER['DOCUMENT_ROOT'].'/angoschool/funcoesAuxiliares.php');
    curtina($_SERVER['DOCUMENT_ROOT'].'/angoschool/'.directorioEmExecucao().'/funcoesAuxiliares.php');
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/funcoesAuxiliaresDb.php';

    class funcoesAuxiliares extends funcoesAuxiliaresMae{
        public $sexoDirigente="";
        public $nomeCurso="";
        public $areaFormacaoCurso="";
        public $nomeCursoAbr="";
        public $nomeCursoRel="";
        public $numAno="";
        public $idPAno="";
        public $idPCurso="";
        public $classe="";
        public $classeExtensa="";
        public $classeExt="";
        public $cursoDir="";
        public $turma="";
        public $periodoTurma="";
        public $designacaoTurma="";
        public $nomeDirigente="";
        public $valorObservacaoF;
        public $modLinguaEstrangeira="";
        public $tipoCurso="";
        public $NEC=0;
        public $PAP=0;
        public $definicoesConselhoNotas=array();

        function __construct($areaVisualizada=""){
            ini_set('memory_limit', '500M');
            parent::__construct($areaVisualizada);
        }

        private function dadoPersonalizado($dado){
            $arrayAbigael=explode("/", valorArray($this->sobreEscolaLogada, $dado));
            $posicao=0;
            if($this->classe<7){
                $posicao=0;
            }else if($this->classe>=7 && $this->classe<=9){
                $posicao=1;
            }else{
                if($this->tipoCurso=="geral"){
                    $posicao=2;
                }else if($this->tipoCurso=="tecnico"){
                    if($this->especialidadeCurso=="saude"){
                        $posicao=4;
                    }else{
                        $posicao=3;
                    }
                }else{
                    $posicao=5;
                }
            }
            return isset($arrayAbigael[$posicao])?$arrayAbigael[$posicao]:"";
        }

        function cabecalho($comInsignia="sim", $posicionamento="text-align:center;", $capitalize="text-transform:uppercase;", $tamanhoDesignacao="width:620px; height:50px;"){

            $retorno ="<p style='".$posicionamento.$this->miniParagrafo."'></p>";
            if($comInsignia=="sim"){
                if(valorArray($this->sobreEscolaLogada, "insigniaUsar")=="escola"){
                    $retorno .="<p style='".$posicionamento.$this->miniParagrafo."'><img src='".$_SERVER['DOCUMENT_ROOT'].'/angoschool/Ficheiros/Escola_'.$_SESSION['idEscolaLogada'].'/Icones/'.valorArray($this->sobreEscolaLogada, "logoEscola")."' style='".$this->insignia_medio."'></p>";
                }else{

                    $retorno .="
                    <p style='".$posicionamento.$this->miniParagrafo."'>
                        <img src='".$_SERVER['DOCUMENT_ROOT']."/angoschool/icones/insignia.jpg' style='".$this->insignia_medio."'>
                    </p>";
                }
            }
            $retorno .="<p style='".$posicionamento.$capitalize."'>".valorArray($this->sobreUsuarioLogado, "cabecalhoPrincipal");
            if ($_SESSION["idEscolaLogada"] == 27)
              $retorno .="<br><img src='".$_SERVER['DOCUMENT_ROOT']."/angoschool/icones/modSagradoIICiclo.png' style='width:400px; height:50px;'>";
            $retorno .="</p>";

            return $retorno;
        }

        function cabecalhoParaAluno(){

            $src = $_SERVER['DOCUMENT_ROOT'].'/angoschool/Ficheiros/Escola_'.$_SESSION['idEscolaLogada'].'/Icones/'.valorArray($this->sobreUsuarioLogado, "logoEscola");
              if(!file_exists($src) || valorArray($this->sobreUsuarioLogado, "logoEscola")==NULL || valorArray($this->sobreUsuarioLogado, "logoEscola")==""){
                $src = $_SERVER['DOCUMENT_ROOT'].'/angoschool/icones/logoAngoSchool1.png';
              }
            $retorno ="<p style='".$this->text_center.$this->miniParagrafo."'><img src='".$src."' style='".$this->insignia_medio."'></p>
            <p style='".$this->text_center.$this->miniParagrafo."'>".valorArray($this->sobreUsuarioLogado, "tituloEscola")."</p>";
              return $retorno;
        }

        public function rodape (){
            $dataVisualizacao = isset($_GET["dataVisualizacao"])?$_GET["dataVisualizacao"]:$this->dataSistema;
            return "<strong>".valorArray($this->sobreEscolaLogada, "rodapePrincipal").", aos ".dataExtensa($dataVisualizacao)."<strong>";
        }

        function assinaturaDirigentes($idPCargo, $tamanhoLinha="", $nomeMasculino="", $nomeFeminino="", $comRubrica="sim", $rubricaAutomaticoObrigatorio="nao", $paraDecl="nao"){

            if($idPCargo =="mengi" )
                $idPCargo = 8;
            $teresa = $this->selectArray("cargos", ["idPCargo", "designacaoCargo"], ["instituicao"=>valorArray($this->sobreEscolaLogada, "tipoInstituicao"), "idPCargo"=>$idPCargo]);
            $designacaoCargo = valorArray($teresa, "designacaoCargo");

            $nomeMasculino="O ".$designacaoCargo;
            $nomeFeminino="A";
            foreach(explode(" ", valorArray($teresa, "designacaoCargo")) as $desig){
                $divine = substr($desig,
                    (strlen($desig)-1), strlen($desig));

                if($divine=="o"){
                    $nomeFeminino .=" ".substr($desig, 0,
                    (strlen($desig)-1))."a";
                }else if($divine=="r"){
                    $nomeFeminino .=" ".$desig."a";
                }else{
                    $nomeFeminino .=" ".$desig;
                }
            }
            $dirigente=array();

            $dirigente = $this->selectArray("entidadesprimaria", ["generoEntidade", "tituloNomeEntidade"], ["escola.nivelSistemaEntidade"=>$idPCargo, "escola.idEntidadeEscola"=>$_SESSION['idEscolaLogada'], "escola.estadoActividadeEntidade"=>"A"], ["escola"]);
            $nomeEntidade = valorArray($dirigente, "tituloNomeEntidade");

            if(valorArray($dirigente, "generoEntidade")=="F"){
                $primeiraLinha = $nomeFeminino;
            }else{
                $primeiraLinha = $nomeMasculino;
            }

            if($idPCargo==7){
                if(valorArray($this->sobreEscolaLogada, "designacaoAssinate1")!="" && valorArray($this->sobreEscolaLogada, "designacaoAssinate1")!=NULL){
                    $primeiraLinha = valorArray($this->sobreEscolaLogada, "designacaoAssinate1");
                }
                if(valorArray($this->sobreEscolaLogada, "nomeAssinate1")!="" && valorArray($this->sobreEscolaLogada, "nomeAssinate1")!=NULL){
                    $nomeEntidade = valorArray($this->sobreEscolaLogada, "nomeAssinate1");
                }
            }
            if($idPCargo==8){
                if(valorArray($this->sobreEscolaLogada, "designacaoAssinate2")!="" && valorArray($this->sobreEscolaLogada, "designacaoAssinate2")!=NULL){
                    $primeiraLinha = valorArray($this->sobreEscolaLogada, "designacaoAssinate2");
                }
                if(valorArray($this->sobreEscolaLogada, "nomeAssinate2")!="" && valorArray($this->sobreEscolaLogada, "nomeAssinate2")!=NULL){
                    $nomeEntidade = valorArray($this->sobreEscolaLogada, "nomeAssinate2");
                }
            }
            $estadoPeriodo = $this->selectArray("escolas", ["estadoperiodico.estado"], ["idPEscola"=>$_SESSION['idEscolaLogada'], "estadoperiodico.objecto"=>"exibirAssinaturas"], ["estadoperiodico"], 1);
            $estadoExibirAssinaturas = valorArray($estadoPeriodo, "estado", "estadoperiodico");

            $rubrica="";
            $fotoRubrica="";
            if(($comRubrica=="sim" && $estadoExibirAssinaturas=="V" && seOficialEscolar()) || $rubricaAutomaticoObrigatorio=="sim"){

                if($idPCargo==7){
                    $fotoRubrica = valorArray($this->sobreEscolaLogada, "assinatura1");
                }else if($idPCargo==8){
                    $fotoRubrica = valorArray($this->sobreEscolaLogada, "assinatura2");
                }
                $rubrica="";
                if($fotoRubrica!="" && $fotoRubrica!=NULL && file_exists($_SERVER['DOCUMENT_ROOT']."/angoschool/Ficheiros/Escola_".$_SESSION["idEscolaLogada"]."/Icones/".$fotoRubrica)){
                    $rubrica = "<img src='".$_SERVER['DOCUMENT_ROOT']."/angoschool/Ficheiros/Escola_".$_SESSION["idEscolaLogada"]."/Icones/".$fotoRubrica."' style='width: 120px; height: 50px; margin-top:-20px; margin-bottom:-20px; position:top;'>";
                }
            }
            return $this->porAssinatura($primeiraLinha, $nomeEntidade, $rubrica, $tamanhoLinha);
        }

        function assinaturaProfessor($idPProfessor, $textoM, $textoF, $tamanhoLinha="default"){
            $professor = $this->selectArray("entidadesprimaria", [], ["idPEntidade"=>$idPProfessor]);
            $retorno="";
             $linha="";
            if($tamanhoLinha=="default"){
                $linha = "_________________________";
            }else{
                for($i=0; $i<=$tamanhoLinha; $i++){
                    $linha .="_";
                }
            }

            if(valorArray($professor, "generoEntidade")=="M"){
                $retorno .="<p style='".$this->text_center."'>".$textoM."</p>";
            }else{
                $retorno .="<p style='".$this->text_center."'>".$textoF."</p>";
            }
            $retorno .="<p style='".$this->text_center.$this->miniParagrafo."'>".$linha."</p>";
            $retorno .="<p style='".$this->text_center."'>".valorArray($professor, "nomeEntidade")."</p>";
            return $retorno;
        }


        function nomeCurso($idPCurso=""){
            if($idPCurso!=""){
                $this->idPCurso=$idPCurso;
            }

            if($this->idPCurso=="T"){
                $this->idPCurso = $this->selectUmElemento("nomecursos", "idPNomeCurso", ["cursos.idCursoEscola"=>$_SESSION["idEscolaLogada"]], ["cursos"]);
            }

            $this->sobreCurso = $curso = $this->selectArray("nomecursos", [], ["cursos.idCursoEscola"=>$_SESSION['idEscolaLogada'], "idPNomeCurso"=>$this->idPCurso], ["cursos"]);

            $curso = $this->anexarTabela2($curso, "entidadesprimaria", "cursos", "idPEntidade", "idCursoEntidade");

            $this->nomeCurso = valorArray($curso, "nomeCurso");

            $this->duracaoCurso = (int)valorArray($curso, "duracao");

            $this->areaFormacaoCurso = valorArray($curso, "areaFormacaoCurso");
            $this->especialidadeCurso = valorArray($curso, "especialidadeCurso");
            $this->coordenadorCurso = valorArray($curso, "nomeEntidade");

            $this->modLinguaEstrangeira = valorArray($curso, "modLinguaEstrangeira", "cursos");
            $this->campoAvaliar = valorArray($curso, "campoAvaliar", "cursos");
            $this->tipoCurso = valorArray($curso, "tipoCurso");
            $this->nomeCursoAbr = valorArray($curso, "abrevCurso");
            $this->sePorSemestre = valorArray($curso, "sePorSemestre");

            if($this->classe<=9){
                $this->cursoDir="";
                $this->nomeCursoRel="";
            }else{
                $this->cursoDir="/".$this->nomeCurso."/".$this->areaFormacaoCurso;
                $this->nomeCursoRel="-".$this->nomeCurso."-".$this->areaFormacaoCurso;
            }
            foreach(listarItensObjecto($curso, "classes") as $classe){
                if($classe["identificador"]==$this->classe){
                    $this->classeExt = $classe["abreviacao1"];
                    $this->classeExtensa = $classe["designacao"];
                    break;
                }
            }
        }

        function numAno ($idPAno=""){
            if($idPAno!=""){
                $this->idPAno = $idPAno;
            }

            $anolectivo = $this->selectArray("anolectivo", [], ["idPAno"=>$this->idPAno, "anos_lectivos.idAnoEscola"=>$_SESSION["idEscolaLogada"]], ["anos_lectivos"]);
            $this->idPAno = valorArray($anolectivo, "idPAno");
            $this->numAno = valorArray($anolectivo, "numAno");
        }

        function nomeTurma($turma="", $classe="", $idPCurso="", $idPAno=""){
            if($turma!=""){
                $this->turma=$turma;
            }
            if($classe!=""){
                $this->classe=$classe;
            }
            if($idPCurso!=""){
                $this->idPCurso=$idPCurso;
            }
            if($idPAno!=""){
                $this->idPAno=$idPAno;
            }
            if($idPAno!=""){
                $idPAno=$this->idPAno;
            }
            $this->sobreTurma = $this->selectArray("listaturmas", [], ["classe"=>$this->classe, "idPEscola"=>$_SESSION["idEscolaLogada"], "idListaAno"=>$this->idPAno, "nomeTurma"=>$this->turma, "idPNomeCurso"=>$this->idPCurso]);
             $this->sobreTurma = $this->anexarTabela($this->sobreTurma, "entidadesprimaria", "idPEntidade", "idCoordenadorTurma");

            $this->periodoTurma = valorArray($this->sobreTurma, "periodoTurma");
            $this->designacaoTurma = valorArray($this->sobreTurma, "designacaoTurma");
            $this->atributoTurma  = valorArray($this->sobreTurma, " atributoTurma ");
            $this->numeroSalaTurma  = valorArray($this->sobreTurma, " numeroSalaTurma ");

            return $this->designacaoTurma;
        }

         public function observacaoF($observacaoF, $seAlunoFoiAoRecurso, $sexoAluno, $colspan=1){

            $exprParaAprovado = valorArray($this->definicoesConselhoNotas, "exprParaAprovado");
            $exprParaAprovadoComDef = valorArray($this->definicoesConselhoNotas, "exprParaAprovadoComDef");
            $exprParaAprovadoComRecurso = valorArray($this->definicoesConselhoNotas, "exprParaAprovadoComRecurso");
            $exprParaNaoAprovado = valorArray($this->definicoesConselhoNotas, "exprParaNaoAprovado");
            $this->valorObservacaoF = $observacaoF;

            if($observacaoF==""){
                return "<td style='".$this->text_center.$this->bolder.$this->border()."' colspan='".$colspan."'></td>";
            }else if($observacaoF=="D"){
                return "<td style='".$this->text_center.$this->bolder.$this->vermelha.$this->border()."' colspan='".$colspan."'>DESISTENTE</td>";
            }else if($observacaoF=="N"){
                if($sexoAluno=="M"){
                    return "<td style='".$this->text_center.$this->bolder.$this->vermelha.$this->border()."' colspan='".$colspan."'>ANULADO</td>";
                }else{
                    return "<td style='".$this->text_center.$this->bolder.$this->vermelha.$this->border()."' colspan='".$colspan."'>ANULADA</td>";
                }
            }else if($observacaoF=="F"){
                return "<td style='".$this->text_center.$this->bolder.$this->vermelha.$this->border()."' colspan='".$colspan."'>REP. FALTAS</td>";

            }else if($observacaoF=="RI"){
                return "<td style='".$this->text_center.$this->bolder.$this->vermelha.$this->border()."' colspan='".$colspan."'>RI</td>";

            }else if($observacaoF=="RFN"){
                return "<td style='".$this->text_center.$this->bolder.$this->vermelha.$this->border()."' colspan='".$colspan."'>RFN</td>";

            }else if($observacaoF=="NA/TRANSF"){
                return "<td style='".$this->text_center.$this->bolder.$this->vermelha.$this->border()."' colspan='".$colspan."'>N. APTO<br>TRANSF.</td>";

            }else if($observacaoF=="A/TRANSF"){
                return "<td style='".$this->text_center.$this->bolder.$this->azul.$this->border()."' colspan='".$colspan."'>APTO/TRANSF.</td>";

            }else if($observacaoF=="A"){
                if($exprParaAprovado=="TRANSITA"){
                    return "<td style='".$this->text_center.$this->bolder.$this->azul.$this->border()."' colspan='".$colspan."'>TRANSITA</td>";
                }else if($exprParaAprovado=="APROVADO"){
                    if($sexoAluno=="M"){
                        return "<td style='".$this->text_center.$this->bolder.$this->azul.$this->border()."' colspan='".$colspan."'>APROVADO</td>";
                    }else{
                        return "<td style='".$this->text_center.$this->bolder.$this->azul.$this->border()."' colspan='".$colspan."'>APROVADA</td>";
                    }
                } else{
                    if($sexoAluno=="M"){
                    return "<td style='".$this->text_center.$this->bolder.$this->azul.$this->border()."' colspan='".$colspan."'>APTO</td>";
                    }else{
                        return "<td style='".$this->text_center.$this->bolder.$this->azul.$this->border()."' colspan='".$colspan."'>APTA</td>";
                    }
                }

            }else if($observacaoF=="TR"){
                if($exprParaAprovadoComDef=="" || $exprParaAprovadoComDef==null){
                    return "<td style='".$this->text_center.$this->bolder.$this->azul.$this->border()."' colspan='".$colspan."'>TRANSITA</td>";
                }else{
                    return "<td style='".$this->text_center.$this->bolder.$this->azul.$this->border()."' colspan='".$colspan."'>TRANS./DEF</td>";
                }
            }else{
                $resultPauta = isset($_GET["resultPauta"])?$_GET["resultPauta"]:"";

                if($seAlunoFoiAoRecurso=="A" && $resultPauta=="naoDefinitivo"){
                    return "<td style='".$this->text_center.$this->bolder.$this->border()."' colspan='".$colspan."'>RECURSO</td>";
                }else{
                    if($exprParaNaoAprovado=="N. TRANSITA"){
                        return "<td style='".$this->text_center.$this->bolder.$this->vermelha.$this->border()."' colspan='".$colspan."'>N. TRANSITA</td>";
                    }else if($exprParaNaoAprovado=="N. APROVADO"){
                        if($sexoAluno=="M"){
                            return "<td style='".$this->text_center.$this->bolder.$this->vermelha.$this->border()."' colspan='".$colspan."'>N. APROVADO</td>";
                        }else{
                            return "<td style='".$this->text_center.$this->bolder.$this->vermelha.$this->border()."' colspan='".$colspan."'>N. APROVADA</td>";
                        }
                    }else{
                       if($sexoAluno=="M"){
                            return "<td style='".$this->text_center.$this->bolder.$this->vermelha.$this->border()."' colspan='".$colspan."'>N. APTO</td>";
                        }else{
                            return "<td style='".$this->text_center.$this->bolder.$this->vermelha.$this->border()."' colspan='".$colspan."'>N. APTA</td>";
                        }
                    }
                }

            }
        }

         public function notaAptidaoEstagio($sobreAluno){

            $provAptidao=valorArray($sobreAluno, "provAptidao", "escola");
            $notaEstagio=valorArray($sobreAluno, "notaEstagio", "escola");
            $retor="";
            $retor .=$this->tratarVermelha($notaEstagio, "", 10).$this->tratarVermelha($provAptidao, "", 10);

            if($notaEstagio=="" || $notaEstagio==NULL){
                $this->NEC=0;
            }else{
                $this->NEC=$notaEstagio;
            }
            if($provAptidao=="" || $provAptidao==NULL){
                $this->PAP=0;
            }else{
                $this->PAP=$provAptidao;
            }
            return $retor;
        }

        public function retornarNotas($nota, $classCss="", $camposVazios="==", $sommenteNota="nao"){
            if(!is_numeric($nota)){
                $nota=0;
            }
            if($this->classe<=6){
                $notaMinima=5;
            }else{
                $notaMinima=10;
            }

            if($sommenteNota=="sim"){
                if($nota==""){
                    return "<span style='".$classCss."'>".$camposVazios."</span>";
                }else if($nota<$notaMinima){
                    return "<span style='".$classCss.$this->vermelha."'>".completarNumero($nota)."</span>";
                }else{
                    return "<span style='".$classCss."'>".completarNumero($nota)."</span>";
                }
            }else{
                if($nota=="" || $nota==0){
                    return "<td style='".$this->border().$this->text_center.$classCss."'>".$camposVazios."</td>";
                }else if($nota<$notaMinima){
                    return "<td style='".$this->border().$this->text_center.$classCss.$this->vermelha."'>".completarNumero($nota)."</td>";
                }else{
                    return "<td style='".$this->border().$this->text_center.$classCss."'>".completarNumero($nota)."</td>";
                }
            }

        }

        public function vistoDirectorMunicipal($marginTop=45, $dadosQR="")
        {
            $comAssinDirectProv = isset($_GET["comAssinDirectProv"])?$_GET["comAssinDirectProv"]:"";
            $comAssinDirectMunicipal = isset($_GET["comAssinDirectMunicipal"])?$_GET["comAssinDirectMunicipal"]:"";
            $nomeDirectorMunicipal = isset($_GET["nomeDirectorMunicipal"])?$_GET["nomeDirectorMunicipal"]:"";
            $nomeDirectorProvincial = isset($_GET["nomeDirectorProvincial"])?$_GET["nomeDirectorProvincial"]:"";
            $comQRCode = isset($_GET["comQRCode"])?$_GET["comQRCode"]:"";

            $div1 = "";
            $div2 = "";
            if($comQRCode == "sim" && $dadosQR !="")
                $div2 = "<div style='text-align:right;'>".$dadosQR."</div>";
            else if ($comAssinDirectProv == "sim")
                $div1 = "<div style='text-align:center;'><p class='text-center' style='font-size:10pt;'>Visto<br/>Director Provincial da Educação</p>
                <p class='text-center bolder' style='font-size:10pt;  margin-top:20px;'>".$nomeDirectorProvincial."</p></div>";
            else if ($comAssinDirectMunicipal == "sim")
                $div1 = "<div style='text-align:right;'><div style='text-align:center;'><p class='text-center' style='font-size:10pt;'>Visto<br/>Director Municipal da Educação</p>
                <p class='text-center bolder' style='font-size:10pt;  margin-top:20px;'>".$nomeDirectorMunicipal."</p></div></div>";

            if($comAssinDirectProv == "sim" && $comAssinDirectMunicipal == "sim")
            {
                $div2 = "<div style='text-align:center;'><p class='text-center' style='font-size:10pt;'>Visto<br/>Director Provincial da Educação</p>
                <p class='text-center bolder' style='font-size:10pt;  margin-top:20px;'>".$nomeDirectorProvincial."</p></div>";
                $div1 = "<div style='text-align:right;'><div style='text-align:center;'><p class='text-center' style='font-size:10pt;'>Visto<br/>Director Municipal da Educação</p>
                <p class='text-center bolder' style='font-size:10pt;  margin-top:20px;'>".$nomeDirectorMunicipal."</p></div></div>";
            }
            else if($comAssinDirectProv == "sim" && $comQRCode == "sim" && $dadosQR !="")
            {
                $div1 = "<div style='text-align:center;'><p class='text-center' style='font-size:10pt;'>Visto<br/>Director Provincial da Educação</p>
                <p class='text-center bolder' style='font-size:10pt;  margin-top:20px;'>".$nomeDirectorProvincial."</p></div>";
                $div2 = "<div style='text-align:right;'>".$dadosQR."</div>";
            }
            else if($comAssinDirectMunicipal == "sim" && $comQRCode == "sim" && $dadosQR !="")
            {
                $div1 = "<div style='text-align:center;'><p class='text-center' style='font-size:10pt;'>Visto<br/>Director Municipal da Educação</p>
                <p class='text-center bolder' style='font-size:10pt;  margin-top:20px;'>".$nomeDirectorMunicipal."</p></div>";
                $div2 = "<div style='text-align:right;'>".$dadosQR."</div>";
            }

            return "<div style='position:absolute; margin-top:45px;'><div style='height:150px; width:220px;'>".$div1."</div><div style = 'height:150px; width:210px; margin-top:-150px; margin-left:470px;'>".$div2."</div></div>";
        }
    }
?>
