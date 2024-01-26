<?php
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once $_SESSION["directorioPaterno"].'angoschool/funcoesAuxiliares.php';
    curtina($_SESSION["directorioPaterno"].'angoschool/'.directorioEmExecucao().'/funcoesAuxiliares.php');
    include_once $_SESSION["directorioPaterno"].'angoschool/funcoesAuxiliaresDb.php';

    class funcoesAuxiliares extends funcoesAuxiliaresMae{

        function __construct(){
            ini_set('memory_limit', '500M');
            parent::__construct(__DIR__);            
        }

        function numAno ($idPAno=""){
            if($idPAno!=""){
                $this->idPAno = $idPAno;
            }else{
                $this->idPAno = $this->selectUmElemento("anolectivo", "idPAno", "estado=:estado", ["V"]);
            }
            $this->numAno = $this->selectUmElemento("anolectivo", "numAno", "idPAno=:idPAno", [$this->idPAno]);
        }

        function cabecalho($comInsignia="sim", $posicionamento="text-align:center;", $capitalize="text-transform:uppercase;"){
            
            $sobreProvincia = $this->selectArray("div_terit_provincias", "*", "idPProvincia=:idPProvincia", [valorArray($this->sobreUsuarioLogado, "provincia")]);
            $retorno ="<p style='".$posicionamento.$this->miniParagrafo."'></p>
            <p style='".$posicionamento.$this->miniParagrafo."'><img src='".$_SESSION["directorioPaterno"]."angoschool/icones/insignia.jpg' style='".$this->insignia_medio."'></p>
            <p style='".$posicionamento.$capitalize.$this->miniParagrafo."'>República de Angola</p>
            <p style='".$posicionamento.$capitalize.$this->miniParagrafo."'>Governo Provincial ".valorArray($sobreProvincia, "preposicaoProvincia2")." ".valorArray($sobreProvincia, "nomeProvincia")."</p>
                <p style='".$posicionamento.$capitalize.$this->miniParagrafo."'>Gabinete Provincial da Educação</p>";
            return $retorno;
        }
        public function rodape (){
            $sobreEscola = $this->selectArray("escolas LEFT JOIN div_terit_provincias ON idPProvincia=provincia LEFT JOIN div_terit_municipios ON idPMunicipio=municipio", "*", "idPEscola=:idPEscola", [$_SESSION['idEscolaLogada']]);
            $retorno=valorArray($this->sobreUsuarioLogado, "nomeEscola").", ".trim(valorArray($sobreEscola, "preposicaoMunicipio"))." ".valorArray($sobreEscola, "nomeMunicipio").", aos ".dataExtensa($this->dataSistema);           
            return $retorno;
        }
        function nomeCurso($idPCurso=""){
            if($idPCurso!=""){
                $this->idPCurso=$idPCurso;
            }

            $curso = $this->selectArray("cursos LEFT JOIN nomecursos ON idPNomeCurso=idFNomeCurso", "*", "idPNomeCurso=:idPNomeCurso", [$this->idPCurso, $_SESSION["idEscolaLogada"]], "idPNomeCurso ASC LIMIT 1");

            $this->nomeCurso = valorArray($curso, "nomeCurso");

            $this->duracaoCurso = (int)valorArray($curso, "duracao");

            $this->areaFormacaoCurso = valorArray($curso, "areaFormacaoCurso");
            $this->especialidadeCurso = valorArray($curso, "especialidadeCurso");

            $this->modLinguaEstrangeira = valorArray($curso, "modLinguaEstrangeira");
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
            $this->classeExt = classeExtensa($this->classe, valorArray($curso, "sePorSemestre"), "sim");
            $this->classeExtensa = classeExtensa($this->classe, valorArray($curso, "sePorSemestre"));
        }
        function assinaturaDirigentes($cargo, $tamanhoLinha="", $nomeMasculino="", $nomeFeminino="", $comRubrica="sim", $rubricaAutomaticoObrigatorio="nao"){

            if(is_array($cargo)){
                $i=0;
                foreach ($cargo as $miradi) {
                    if($i==0){
                        $cargo=$miradi;
                    }
                    if($miradi==valorArray($this->sobreUsuarioLogado, "nivelSistemaEntidade")){
                        $cargo=$miradi;
                        break;
                    }
                    $i++;
                }
            }

            $dirigente = $this->selectArray("entidadesprimaria LEFT JOIN entidade_escola ON idPEntidade=idFEntidade", "*", "nivelSistemaEntidade=:nivelSistemaEntidade AND idEntidadeEscola=:idEntidadeEscola AND estadoActividadeEntidade=:estadoActividadeEntidade", [$cargo, $_SESSION["idEscolaLogada"], "A"]);

            $retorno="";
            $artigo="o";
            $artigo2="";

            
               
            if(valorArray($dirigente, "generoEntidade")=="F"){
                $artigo="a";
                $artigo2="a";
            }
            $primeiraLinha="";

            $nomeEntidade = valorArray($dirigente, "tituloNomeEntidade");


            if($nomeMasculino!="" && $nomeFeminino!=""){
                if(valorArray($dirigente, "generoEntidade")=="M"){
                    $primeiraLinha = strtoupper($artigo)." ".$nomeMasculino;
                }else{
                    $primeiraLinha = strtoupper($artigo)." ".$nomeFeminino;
                }                    
            }else if($cargo=="DP"){
                $primeiraLinha = strtoupper($artigo)." Director".$artigo2;
            }else if($cargo=="CDEPE"){
                $primeiraLinha = strtoupper($artigo)." Chefe do DPPE";
            }else if($cargo=="CDARH"){
                $primeiraLinha = strtoupper($artigo)." Chefe do DARH";
            }

            

            $rubrica=""; 
            $estadoExibirAssinaturas= $this->selectUmElemento("estadoperiodico", "estado", "objecto=:objecto AND idEstadoEscola=:idEstadoEscola", ["exibirAssinaturas", $_SESSION["idEscolaLogada"]]);
            $fotoRubrica="";
            if($comRubrica=="sim" && $estadoExibirAssinaturas=="V" && $rubricaAutomaticoObrigatorio=="sim"){ 

                if($cargo=="DP"){
                    $fotoRubrica = $this->selectUmElemento("escolas", "assinatura1", "idPEscola=:idPEscola", [$_SESSION["idEscolaLogada"]]);
                }

                $rubrica="";
                if(file_exists($this->caminhoRetornar."Ficheiros/Escola_".$_SESSION["idEscolaLogada"]."/Icones/".$fotoRubrica)){

                    $rubrica = "<img src='".$this->caminhoRetornar."Ficheiros/Escola_".$_SESSION["idEscolaLogada"]."/Icones/".$fotoRubrica."' style='width: 120px; height: 50px; margin-top:-20px; margin-bottom:-20px; position:top;'>";
                }
            }
            return $this->porAssinatura($primeiraLinha, $nomeEntidade, $rubrica, $tamanhoLinha);
        }       

    }

?>