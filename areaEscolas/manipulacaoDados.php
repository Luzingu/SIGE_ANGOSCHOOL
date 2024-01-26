<?php
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_cache_expire(60);
      session_start();
    }
    include_once ($_SERVER['DOCUMENT_ROOT'].'/angoschool/manipulacaoDadosMae.php');
    include_once ($_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php');

  class manipulacaoDados extends manipulacaoDadosMae{
    public $idAnoActual=0;
    public $numAnoActual=0;
    public $modeloPauta="_mod_2020";
    private $caminhoRetornar="";
    public $codigoTurma="";

    public $sobreTurmaActualAluno=array();

    function __construct($areaVisualizada="", $identMenu=""){
        parent::__construct($areaVisualizada, $identMenu);

        if(isset($_SESSION["idEscolaLogada"]) && isset($_SESSION["idUsuarioLogado"])){
          $this->anosLectivos = $this->selectArray("anolectivo", [], ["anos_lectivos.idAnoEscola"=>$_SESSION["idEscolaLogada"]], ["anos_lectivos"], "", [], ["numAno"=>-1]);

          $array = $this->selectArray("anolectivo", [], ["anos_lectivos.idAnoEscola"=>$_SESSION["idEscolaLogada"], "anos_lectivos.estadoAnoL"=>"V"], ["anos_lectivos"]);

          $this->idAnoActual = valorArray($array, "idPAno");
          $this->numAnoActual = valorArray($array, "numAno");
          $this->codigoTurma = valorArray($array, "codigoTurma", "anos_lectivos");

          if($_SESSION['tipoUsuario']=="aluno"){
            $turma = listarItensObjecto($this->sobreUsuarioLogado, "reconfirmacoes", ["idReconfAno=".$this->idAnoActual, "idMatCurso=".valorArray($this->sobreUsuarioLogado, "idMatCurso", "escola"), "idReconfEscola=".$_SESSION['idEscolaLogada']]);

            $this->sobreTurmaActualAluno = $this->selectArray("listaturmas", [], ["classe"=>valorArray($turma, "classeReconfirmacao"), "nomeTurma"=>valorArray($turma, "nomeTurma"), "idListaAno"=>$this->idAnoActual, "idPEscola"=>$_SESSION["idEscolaLogada"], "idPNomeCurso"=>valorArray($this->sobreUsuarioLogado, "idMatCurso", "escola")]);
          }
        }
    }
    public function listaClassesPorCurso(){
      echo "<script> var listaClassesPorCurso=".$this->selectJson("nomecursos", ["idPNomeCurso", "nomeCurso", "classes.identificador", "classes.designacao", "classes.abreviacao1", "classes.abreviacao2"], ["cursos.idCursoEscola"=>$_SESSION['idEscolaLogada']],["classes"])."</script>";
    }

    function retornarAnosEmJavascript(){
      echo "<script> var idAnoActual=".$this->idAnoActual."; var anoActual=".$this->numAnoActual.";</script>";
      echo "<script> var idAno=".$this->idAnoActual."; var ano =".explode("-", $this->dataSistema)[0].";</script>";
    }

    public function pagamentoAnteriorDoAluno($idPMatricula, $codigo, $referenciaOperacao="", $idPAno=""){

      if($idPAno==""){
        $idPAno = $this->idAnoActual;
      }
      $sobreAluno = $this->selectArray("alunosmatriculados", ["pagamentos.codigoEmolumento", "pagamentos.referenciaPagamento", "pagamentos.idHistoricoAno", "pagamentos.idPHistoricoConta"], ["idPMatricula"=>$idPMatricula]);

      $condicao = ["codigoEmolumento=".$codigo, "idHistoricoAno=".$idPAno];
      if($referenciaOperacao!=""){
        $condicao[]="referenciaPagamento=".$referenciaOperacao;
      }
      $array = listarItensObjecto($sobreAluno, "pagamentos", $condicao);
      return valorArray($array, "idPHistoricoConta");
    }

    public function preco($codigo, $classe, $idCurso, $mes="", $sobreAluno=array()){
      $condicao =["codigoEmolumento=".$codigo, "classe=".$classe, "idCurso=".$idCurso];
      if($mes!=""){
        $condicao[]="mes=".$mes;
      }
      $precoEmolumento = valorArray(listarItensObjecto($this->sobreEscolaLogada, "emolumentos", $condicao), "valor");

      if(count($sobreAluno)>0){
        $beneficiosDaBolsa = valorArray($sobreAluno, "beneficiosDaBolsa","escola");
            $beneficiosDaBolsa = (is_array($beneficiosDaBolsa) || is_object($beneficiosDaBolsa))?$beneficiosDaBolsa:array();
        foreach($beneficiosDaBolsa as $ben){
          if(($ben["codigoEmolumento"]==$codigo && $codigo!="propina") || ($ben["codigoEmolumento"]==$codigo && $ben["mes"]!=$mes)){
            $precoEmolumento = $ben["valorPreco"];
            break;
          }
        }
      }
      return floatval($precoEmolumento);
    }
    public function sobreAluno($idPMatricula, $campos=array(), $grupo=""){
      $this->sobreAluno = $this->selectArray("alunosmatriculados", $campos, ["idPMatricula"=>$idPMatricula, "escola.idMatEscola"=>$_SESSION["idEscolaLogada"]], ["escola"], 1);
      $this->grupoAluno= valorArray($this->sobreAluno, "grupo");
      $this->sobreCursoAluno = $this->selectArray("nomecursos", [], ["idPNomeCurso"=>valorArray($this->sobreAluno, "idMatCurso", "escola"), "cursos.idCursoEscola"=>$_SESSION['idEscolaLogada']], ["cursos"]);
      return $this->sobreAluno;
    }

    public function turmasEscola($idCursoCond=array(), $classeCond=array(), $idPAno="", $tipoCurso="", $campos=array()){
      if($idPAno==""){
        $idPAno = $this->idAnoActual;
      }
      $condicao =["idPEscola"=>$_SESSION["idEscolaLogada"], "idListaAno"=>$idPAno];

      if($tipoCurso!=""){
        $idCursoCond=array();
        foreach ($this->selectArray("nomecursos", [], ["cursos.idCursoEscola"=>$_SESSION['idEscolaLogada'], "cursos.estadoCurso"=>"A"], ["cursos"]) as $c) {
          $idCursoCond[]=intval($c["idPNomeCurso"]);
        }
      }

      if(count($idCursoCond)>0){
        $condicao["idPNomeCurso"]=['$in'=>$idCursoCond];
      }

      if(count($classeCond)>0){
          $condicao["classe"]=['$in'=>$classeCond];
      }

      $this->turmasEscola = $this->selectArray("listaturmas", $campos, $condicao, [], "", [], ["nomeCurso"=>1, "classe"=>1, "nomeTurma"=>1]);

      $this->turmasEscola = $this->anexarTabela($this->turmasEscola, "entidadesprimaria", "idPEntidade", "idCoordenadorTurma");
      return $this->turmasEscola;
    }

    private function todasDisciplinas ($idPCurso="", $classe="", $periodo, $continuidadeDisciplina="", $idPNomeDisciplina=array(), $condEnsPrimario=array(), $campos=array(), $idPAno="")
    {
      if($idPAno==""){
        $idPAno=$this->idAnoActual;
      }
      $classeSeguinte = $this->classeSeguinte($idPCurso, $classe);

      if(count($campos)>0){
        $campos[]="disciplinas.anosLectivos";
        $campos[]="disciplinas.idPNomeDisciplina";
      }

      $sobreCurso = $this->selectArray("nomecursos", ["classes.identificador", "tipoCurso", "classes.ordem", "cursos.curriculoEscola", "cursos.tipoCurriculo"], ["cursos.idCursoEscola"=>$_SESSION['idEscolaLogada'], "idPNomeCurso"=>$idPCurso], ["cursos"], 1);

      $divine = ["disciplinas.idDiscCurriculo"=>valorArray($sobreCurso, "tipoCurriculo", "cursos")];
      if($classe != "")
        $divine["disciplinas.classeDisciplina"]=$classe;

      if(valorArray($sobreCurso, "tipoCurso")=="pedagogico" && $condEnsPrimario!=""){
        $divine["idPNomeDisciplina"]=['$nin'=>$condEnsPrimario];
      }

      if(is_array($idPNomeDisciplina) && count($idPNomeDisciplina)>0){
        for($i=0; $i<=(count($idPNomeDisciplina)-1); $i++){
          $idPNomeDisciplina[$i]=intval($idPNomeDisciplina[$i]);
        }
        $divine["idPNomeDisciplina"]=['$in'=>$idPNomeDisciplina];
      }

      if($idPCurso != "")
        $divine["disciplinas.idDiscCurso"]=$idPCurso;

      $todasDisciplinas=array();
      $array = $this->selectArray("nomedisciplinas", $campos, $divine, ["disciplinas"], "", [], ["disciplinas.ordenacao"=>1]);

      foreach($array as $a){
        $a["disciplinas"]["continuidadeDisciplina"]="T";

        $pepeMbenza = explode(",", valorArray($a, "anosLectivos", "disciplinas"));
        $seTem="nao";
        foreach($pepeMbenza as $pepe){
          if($pepe==$idPAno){
            $seTem="sim";
            break;
          }
        }
        if($seTem=="sim" || $idPAno=="todas"){
          $todasDisciplinas[]=$a;
        }
      }
      return $todasDisciplinas;
    }

    private function excepcoes($idPCurso="", $classe="", $periodo="", $idPAno="")
    {
      if($idPAno==""){
        $idPAno=$this->idAnoActual;
      }
      $condicaoDisciplina["idDiscEscola"] = $_SESSION['idEscolaLogada'];
      if ($idPCurso != "")
        $condicaoDisciplina["idDiscCurso"] = $idPCurso;
      else if ($classe != "")
        $condicaoDisciplina["classeDisciplina"] = $classe;
      else if ($periodo != "")
        $condicaoDisciplina["periodoDisciplina"] = $periodo;

      $listaExcepcoes = array();
      $array = $this->selectArray("excepcoes_curriculares", [], $condicaoDisciplina);
      foreach ($array as $a)
      {
        $pepeMbenza = explode(",", valorArray($a, "anosLectivos"));
        $seTem="nao";
        foreach($pepeMbenza as $pepe)
        {
          if($pepe==$idPAno)
          {
            $seTem="sim";
            break;
          }
        }
        if($seTem=="sim" || valorArray($a, "anosLectivos") == "" || valorArray($a, "anosLectivos") == NULL)
          $listaExcepcoes[]=$a;
      }
      return ($listaExcepcoes);
    }

    public function disciplinas ($idPCurso="", $classe="", $periodo, $continuidadeDisciplina="", $idPNomeDisciplina=array(), $condEnsPrimario=array(), $campos=array(), $idPAno="")
    {
      $this->disciplinas = array();
      $disciplinas = $this->todasDisciplinas ($idPCurso, $classe, $periodo, $continuidadeDisciplina, $idPNomeDisciplina, $condEnsPrimario, $campos, $idPAno);
      $listaExcepcoes = $this->excepcoes($idPCurso, $classe, $periodo, $idPAno);
      $classeSeguinte = $this->classeSeguinte($idPCurso, $classe);

      foreach($disciplinas as $d)
      {
        if(count(array_filter($listaExcepcoes, function ($mamale) use ($d){
          return ($mamale["idPNomeDisciplina"]==$d["idPNomeDisciplina"] && $mamale["classeDisciplina"]==$d["disciplinas"]["classeDisciplina"] && $mamale["idDiscCurso"]==$d["disciplinas"]["idDiscCurso"]) ;
        })) <= 0)
        {
          $discClasseSeguinte = $this->todasDisciplinas ($idPCurso, $classeSeguinte, $periodo, "", [$d["idPNomeDisciplina"]], array(), ["idPNomeDisciplina", "disciplinas.anosLectivos"], $idPAno);
          if(count($discClasseSeguinte) > 0)
            $d["disciplinas"]["continuidadeDisciplina"] = "C";

          if($continuidadeDisciplina == "" || $d["disciplinas"]["continuidadeDisciplina"]==$continuidadeDisciplina){
            $this->disciplinas[] = $d;
          }

        }
      }

      return ($this->disciplinas);
    }

    public function ultimaClasse($idPNomeCurso){
      return $this->selectUmElemento("nomecursos", "ultimaClasse", ["idPNomeCurso"=>$idPNomeCurso]);
    }
    public function primeiraClasse($idPNomeCurso){
      return $this->selectUmElemento("nomecursos", "primeiraClasse", ["idPNomeCurso"=>$idPNomeCurso]);
    }

    public function classeSeguinte($idPNomeCurso, $classeActual){

      $sobreCurso = $this->selectArray("nomecursos", ["classes.identificador", "tipoCurso", "classes.ordem", "cursos.curriculoEscola"], ["cursos.idCursoEscola"=>$_SESSION['idEscolaLogada'], "idPNomeCurso"=>$idPNomeCurso], ["cursos"], 1);

      $retorno="";
      $classesOrdenadas =ordenar(listarItensObjecto($sobreCurso, "classes"), "ordem ASC");
      $posicao=0;
      foreach($classesOrdenadas as $classe){
        $posicao++;
        if($classe["identificador"]==$classeActual){
          $retorno = isset($classesOrdenadas[$posicao])?$classesOrdenadas[$posicao]:"";
          break;
        }
      }
      return (valorArray($retorno, "identificador"));
    }

    public function sobreEscreverAluno($array, $idCursoMaster){
      $i=0;
      foreach($array as $ar){
        $idCursos = (is_array(valorArray($ar, "idCursos", "escola")) || is_object(valorArray($ar, "idCursos", "escola")))?valorArray($ar, "idCursos", "escola"):array();

        foreach($idCursos as $luzl){
          if($luzl["idMatCurso"]==$idCursoMaster){
            foreach(retornarChaves($luzl) as $chave){

              if(isset($luzl[$chave])){
                $array[$i]["escola"][$chave]=$luzl[$chave];
              }
            }
          }
        }
        $i++;
      }
      return $array;
    }

    public function alunosPorTurma($idCurso, $classe, $turma, $idPAno="", $idAlunoEspec=array(), $campos=array(), $unwindAdicional=array(), $condicaoAdicional=array()){

      $this->idPAno = ($idPAno=="")?$this->idAnoActual:$idPAno;

      $condicao = ["reconfirmacoes.idReconfEscola"=>$_SESSION['idEscolaLogada'], "escola.idMatEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.idReconfAno"=>$this->idPAno];

      $matchMae["escolasReconfirmacao"] = new \MongoDB\BSON\Regex("21_15219");

      if($idCurso!=""){
        $condicao["reconfirmacoes.idMatCurso"]=$idCurso;
      }
      if($classe!=""){
        $condicao["reconfirmacoes.classeReconfirmacao"]=$classe;
      }
      if($turma!=""){
        $condicao["reconfirmacoes.nomeTurma"]=$turma;
      }
      $condicao["reconfirmacoes.estadoReconfirmacao"]="A";

      if(is_array($idAlunoEspec) && count($idAlunoEspec)>0){
        for($t=0; $t<count($idAlunoEspec); $t++){
          $idAlunoEspec[$t]=luzl($idAlunoEspec[$t]);
        }
        $condicao["idPMatricula"]=['$in'=>$idAlunoEspec];
      }
      foreach(array_keys($condicaoAdicional) as $chave){
        $condicao[$chave] = $condicaoAdicional[$chave];
      }
      return $this->alunos = $this->selectArray("alunosmatriculados", $campos, $condicao, array_merge(["escola", "reconfirmacoes"], $unwindAdicional), "", [], ["nomeAluno"=>1], $this->matchMaeAlunos($this->idPAno, $idCurso, $classe, $turma));
    }

    public function matchMaeAlunos($idPAno="", $idCurso="", $classe="", $turma=""){
      $matchMae["escolasReconfirmacao"] = new \MongoDB\BSON\Regex($_SESSION['idEscolaLogada']."_".$idPAno);
      if($idCurso!=""){
        $matchMae["idCursosReconfirmacao"] = new \MongoDB\BSON\Regex($_SESSION['idEscolaLogada']."_".$idPAno."=".$idCurso);
      }
      if($classe!=""){
        $matchMae["classesReconfirmacao"] = new \MongoDB\BSON\Regex($_SESSION['idEscolaLogada']."_".$idPAno."=".$classe);
      }
      if($turma!=""){
        $matchMae["turmasReconfirmacao"] = new \MongoDB\BSON\Regex($_SESSION['idEscolaLogada']."_".$idPAno."=".$turma);
      }
      return $matchMae;
    }

    public function miniPautas($idCurso, $classe, $turma, $idAlunoEspec=array(), $idDisciplina, $tipo="pautas", $idPAno="", $campos=array(),$semestre=""){

      if($semestre==""){
        $semestre = retornarSemestreActivo($this, $idCurso, $classe);
      }
      $idPAno = ($idPAno=="")?$this->idAnoActual:$idPAno;

      $condicaoAdicional=array();
      $condicaoAdicional[$tipo.".classePauta"]=$classe;
      $condicaoAdicional[$tipo.".idPautaDisciplina"]=$idDisciplina;
      $condicaoAdicional[$tipo.".semestrePauta"]=$semestre;
      $condicaoAdicional[$tipo.".idPautaCurso"]=$idCurso;

      if($tipo=="arquivo_pautas"){
        $condicaoAdicional[$tipo.".idPautaEscola"]=$_SESSION['idEscolaLogada'];
        $condicaoAdicional[$tipo.".idPautaAno"]=$idPAno;
      }
      return $this->alunosPorTurma($idCurso, $classe, $turma, $idPAno, $idAlunoEspec, $campos, [$tipo], $condicaoAdicional);
    }

    public function papaJipe($idCurso="", $classe="", $turma="", $idPMatricula=""){
    }

    public function notasDeclaracao($classe, $idPCurso){
      $planoCurricular = $this->disciplinas($idPCurso, $classe, valorArray($this->sobreAluno, "periodoAluno", "escola"), "", array(), [58, 59, 60, 231, 232, 233], ["idPNomeDisciplina", "disciplinas.classeDisciplina", "nomeDisciplina", "abreviacaoDisciplina1", "disciplinas.semestreDisciplina", "disciplinas.semestreDisciplina", "disciplinas.tipoDisciplina", "disciplinas.ordenacao"], "todas");


        $pautaAluno=array();
        $i=0;
        foreach(listarItensObjecto($this->sobreAluno, "pautas", ["classePauta=".$classe, "mf>0", "idPautaCurso=".$idPCurso]) as $nota){
          foreach($planoCurricular as $curriculo){
            if($curriculo["disciplinas"]["classeDisciplina"]==$nota["classePauta"] && $curriculo["disciplinas"]["semestreDisciplina"]==$nota["semestrePauta"] && $curriculo["idPNomeDisciplina"]==$nota["idPautaDisciplina"] ){
                $pautaAluno[$i]=$nota;
                $pautaAluno[$i]["nomeDisciplina"]=$curriculo["nomeDisciplina"];
                $pautaAluno[$i]["abreviacaoDisciplina1"]=$curriculo["abreviacaoDisciplina1"];
                $pautaAluno[$i]["idPNomeDisciplina"]=$curriculo["idPNomeDisciplina"];
                $pautaAluno[$i]["semestreDisciplina"]=$curriculo["disciplinas"]["semestreDisciplina"];
                $pautaAluno[$i]["continuidadeDisciplina"]=$curriculo["disciplinas"]["continuidadeDisciplina"];
                $pautaAluno[$i]["tipoDisciplina"]=$curriculo["disciplinas"]["tipoDisciplina"];
                $pautaAluno[$i]["ordenacao"]=$curriculo["disciplinas"]["ordenacao"];
                $pautaAluno[$i]["classeDisciplina"]=$curriculo["disciplinas"]["classeDisciplina"];
                $i++;
            }
          }
        }
        return $pautaAluno;
    }

    public function cabecalhoTermpAproveitamento ($idPAno, $idPCurso, $classe, $tipo=""){

      $array = $this->selectArray("nomecursos", ["cursos.curriculoEscola", "cursos.tipoCurriculo", "curriculo1", "curriculo2", "curriculo3"], ["idPNomeCurso"=>$idPCurso, "cursos.idCursoEscola"=>$_SESSION['idEscolaLogada']], ["cursos"]);

      $idEscola = "";
      if (valorArray($array, "tipoCurriculo", "cursos") == "curriculo1")
      $idEscola = valorArray ($array, "curriculo1");
      else if (valorArray($array, "tipoCurriculo", "cursos") == "curriculo2")
        $idEscola = valorArray ($array, "curriculo2");
      else if (valorArray($array, "tipoCurriculo", "cursos") == "curriculo3")
        $idEscola = valorArray ($array, "curriculo3");

      if ($idEscola == 0)
        $idEscola = $_SESSION["idEscolaLogada"];

      $sobreCurso = $this->selectArray("nomecursos", ["cursos.cabecalhoAvaliacoes".$classe."-".$idPAno, "tipoCurso"], ["idPNomeCurso"=>$idPCurso, "cursos.idCursoEscola"=>$idEscola], ["cursos"]);

      $tipoCurso = valorArray($sobreCurso, "tipoCurso");

      $cabecalhoAvaliacao=array();
      if(valorArray($sobreCurso, "cabecalhoAvaliacoes".$classe."-".$idPAno, "cursos")==NULL || valorArray($sobreCurso, "cabecalhoAvaliacoes".$classe."-".$idPAno, "cursos")==""){
          $notaMaxima=20;
          $notaMinima=0;
          $notaMedia=10;
          if($classe<=6){
            $notaMaxima=10;
            $notaMinima=0;
            $notaMedia=5;
          }
          $cabecalhoAvaliacao[]=array("idCampoAvaliacao"=>1, "identUnicaDb"=>"mtI", "designacao1"=>"CT1", "designacao2"=>"C<br>T<br>1", "periodo"=>"I", "seApenasLeitura"=>"F", "tipoCampo"=>"mediaTrim", "ordenacao"=>1, "notaMaxima"=>$notaMaxima, "notaMedia"=>$notaMedia, "notaMinima"=>$notaMinima);

          $cabecalhoAvaliacao[]=array("idCampoAvaliacao"=>2, "identUnicaDb"=>"mtII", "designacao1"=>"CT2", "designacao2"=>"C<br>T<br>2", "periodo"=>"II", "seApenasLeitura"=>"F", "tipoCampo"=>"mediaTrim", "ordenacao"=>2, "notaMaxima"=>$notaMaxima, "notaMedia"=>$notaMedia, "notaMinima"=>$notaMinima);

          $cabecalhoAvaliacao[]=array("idCampoAvaliacao"=>3, "identUnicaDb"=>"mtIII", "designacao1"=>"CT3", "designacao2"=>"C<br>T<br>3", "periodo"=>"III", "seApenasLeitura"=>"F", "tipoCampo"=>"mediaTrim", "ordenacao"=>3, "notaMaxima"=>$notaMaxima, "notaMedia"=>$notaMedia, "notaMinima"=>$notaMinima);

          $cabecalhoAvaliacao[]=array("idCampoAvaliacao"=>4, "identUnicaDb"=>"mfd", "designacao1"=>"CAP", "designacao2"=>"C<br>A<br>P", "periodo"=>"IV", "seApenasLeitura"=>"V", "tipoCampo"=>"mfd", "ordenacao"=>4, "notaMaxima"=>$notaMaxima, "notaMedia"=>$notaMedia, "notaMinima"=>$notaMinima);

          if($tipoCurso=="tecnico"){
            $cabecalhoAvaliacao[]=array("idCampoAvaliacao"=>5, "identUnicaDb"=>"exame", "designacao1"=>"PG", "designacao2"=>"P<br>G", "periodo"=>"IV", "seApenasLeitura"=>"F", "tipoCampo"=>"exame", "ordenacao"=>5, "notaMaxima"=>$notaMaxima, "notaMedia"=>$notaMedia, "notaMinima"=>$notaMinima);

            $cabecalhoAvaliacao[]=array("idCampoAvaliacao"=>6, "identUnicaDb"=>"mf", "designacao1"=>"CF", "designacao2"=>"C<br>F", "cd"=>0, "periodo"=>"IV", "seApenasLeitura"=>"V", "tipoCampo"=>"mediaFinal", "ordenacao"=>6, "notaMaxima"=>$notaMaxima, "notaMedia"=>$notaMedia, "notaMinima"=>$notaMinima);

            $cabecalhoAvaliacao[]=array("idCampoAvaliacao"=>7, "identUnicaDb"=>"cf", "designacao1"=>"CFD", "designacao2"=>"C<br>F<br>D", "cd"=>0, "periodo"=>"IV", "seApenasLeitura"=>"V", "tipoCampo"=>"classificaDisciplina", "ordenacao"=>7, "notaMaxima"=>$notaMaxima, "notaMedia"=>$notaMedia, "notaMinima"=>$notaMinima);
          }else{
            $cabecalhoAvaliacao[]=array("idCampoAvaliacao"=>5, "identUnicaDb"=>"exame", "designacao1"=>"CPE", "designacao2"=>"C<br>P<br>E", "periodo"=>"IV", "seApenasLeitura"=>"F", "tipoCampo"=>"exame", "ordenacao"=>5, "notaMaxima"=>$notaMaxima, "notaMedia"=>$notaMedia, "notaMinima"=>$notaMinima);

            $cabecalhoAvaliacao[]=array("idCampoAvaliacao"=>6, "identUnicaDb"=>"mf", "designacao1"=>"CF", "designacao2"=>"C<br>F", "cd"=>0, "periodo"=>"IV", "seApenasLeitura"=>"V", "tipoCampo"=>"mediaFinal", "ordenacao"=>6, "notaMaxima"=>$notaMaxima, "notaMedia"=>$notaMedia, "notaMinima"=>$notaMinima);
          }


      }else{

        $sobreClasse = $this->selectArray("nomecursos", ["classes.notaMaxima", "classes.notaMedia", "classes.notaMinima", "classes.periodos"], ["idPNomeCurso"=>$idPCurso, "classes.identificador"=>$classe], ["classes"]);


        foreach(explode(",", valorArray($sobreCurso, "cabecalhoAvaliacoes".$classe."-".$idPAno, "cursos")) as $a){

          $triemstreDb = explode("-", $a)[0];
          $idAvaliacao = isset(explode("-", $a)[1])?explode("-", $a)[1]:"";

          $arC = $this->selectArray("campos_avaliacao", [], ["idCampoAvaliacao"=>$idAvaliacao]);
          $jaExistem="nao";
          foreach($cabecalhoAvaliacao as $cab){
            if($cab["idCampoAvaliacao"]==$idAvaliacao){
              $jaExistem="sim";
              break;
            }
          }
          if(valorArray($arC, "tipoCampo")!="avaliacao" && count($arC)>0 && trim(valorArray($arC, "identUnicaDb"))!="" && $jaExistem=="nao" && trim(valorArray($arC, "identUnicaDb"))!=NULL ){
            $notaMaxima=(valorArray($arC, "notaMaxima")=="" || valorArray($arC, "notaMaxima")==null)?valorArray($sobreClasse, "notaMaxima", "classes"):valorArray($arC, "notaMaxima");
            $notaMedia=(valorArray($arC, "notaMedia")=="" || valorArray($arC, "notaMedia")==null)?valorArray($sobreClasse, "notaMedia", "classes"):valorArray($arC, "notaMedia");
            $notaMinima=(valorArray($arC, "notaMinima")=="" || valorArray($arC, "notaMinima")==null)?valorArray($sobreClasse, "notaMinima", "classes"):valorArray($arC, "notaMinima");

            $seApenasLeitura = (valorArray($arC, "tipoCampo")=="mediaTrim")?"F":valorArray($arC, "seApenasLeitura");
            $cabecalhoAvaliacao[]=array("idCampoAvaliacao"=>$idAvaliacao, "identUnicaDb"=>valorArray($arC, "identUnicaDb"), "designacao1"=>valorArray($arC, "designacao1"), "cd"=>valorArray($arC, "numeroCasasDecimais"), "designacao2"=>valorArray($arC, "designacao2"), "periodo"=>trim($triemstreDb), "seApenasLeitura"=>$seApenasLeitura, "tipoCampo"=>valorArray($arC, "tipoCampo"), "ordenacao"=>valorArray($arC, "ordenacao"), "notaMaxima"=>$notaMaxima, "notaMedia"=>$notaMedia, "notaMinima"=>$notaMinima);
          }
        }
        if($tipo=="notasAtraso"){

          $seTemMfd="nao";$seTemMf="nao";$seTemCFD="nao";
          foreach($cabecalhoAvaliacao as $cab){
            if($cab["identUnicaDb"]=="mfd"){
              $seTemMfd="sim";
            }
            if($cab["identUnicaDb"]=="mf"){
              $seTemMf="sim";
            }
            if($cab["identUnicaDb"]=="cfd"){
              $seTemCFD="sim";
            }
          }
          if($seTemMfd=="nao"){
            $arC = $this->selectArray("campos_avaliacao", [], ["identUnicaDb"=>"mfd"], [],1);

            $cabecalhoAvaliacao[]=array("idCampoAvaliacao"=>valorArray($arC, "idCampoAvaliacao"), "identUnicaDb"=>valorArray($arC, "identUnicaDb"), "cd"=>valorArray($arC, "numeroCasasDecimais"), "designacao1"=>valorArray($arC, "designacao1"), "designacao2"=>valorArray($arC, "designacao2"), "periodo"=>"IV", "seApenasLeitura"=>"V", "tipoCampo"=>valorArray($arC, "tipoCampo"), "ordenacao"=>valorArray($arC, "ordenacao"), "notaMaxima"=>valorArray($sobreClasse, "notaMaxima", "classes"), "notaMedia"=>valorArray($sobreClasse, "notaMedia", "classes"), "notaMinima"=>valorArray($sobreClasse, "notaMinima", "classes"));
          }
          if($seTemMf=="nao"){
            $arC = $this->selectArray("campos_avaliacao", [], ["identUnicaDb"=>"mf"], [],1);

            $cabecalhoAvaliacao[]=array("idCampoAvaliacao"=>valorArray($arC, "idCampoAvaliacao"), "identUnicaDb"=>valorArray($arC, "identUnicaDb"), "cd"=>valorArray($arC, "numeroCasasDecimais"), "designacao1"=>valorArray($arC, "designacao1"), "designacao2"=>valorArray($arC, "designacao2"), "periodo"=>"IV", "seApenasLeitura"=>"V", "tipoCampo"=>valorArray($arC, "tipoCampo"), "ordenacao"=>valorArray($arC, "ordenacao"), "notaMaxima"=>valorArray($sobreClasse, "notaMaxima", "classes"), "notaMedia"=>valorArray($sobreClasse, "notaMedia", "classes"), "notaMinima"=>valorArray($sobreClasse, "notaMinima", "classes"));
          }
        }
      }
      return $this->cabecalhoAvaliacao = ordenar($cabecalhoAvaliacao, "periodo ASC, ordenacao ASC");
    }

    public function camposAvaliacaoAlunos($idAno, $idPCurso, $classe, $periodo, $idPDisciplina, $trimestre="", $item="campos"){


      $array = $this->selectArray("nomecursos", ["cursos.curriculoEscola", "cursos.tipoCurriculo", "curriculo1", "curriculo2", "curriculo3"], ["idPNomeCurso"=>$idPCurso, "cursos.idCursoEscola"=>$_SESSION['idEscolaLogada']], ["cursos"]);

      $sobreDisciplina = $this->selectArray("nomedisciplinas", ["disciplinas.camposAvaliacoes-".$idAno, "disciplinas.cabecalhoAvaliacoes-".$idAno], ["idPNomeDisciplina"=>$idPDisciplina, "disciplinas.idDiscCurso"=>$idPCurso, "disciplinas.classeDisciplina"=>$classe, "disciplinas.idDiscCurriculo"=>valorArray($array, "tipoCurriculo", "cursos")], ["disciplinas"]);

      $sobreClasse = $this->selectArray("nomecursos", ["classes.notaMaxima", "classes.notaMedia", "classes.notaMinima", "classes.periodos"], ["idPNomeCurso"=>$idPCurso, "classes.identificador"=>$classe], ["classes"]);
      $prosperoMako = explode(",", valorArray($sobreClasse, "periodos", "classes"));
      $this->trimestres =array();
      foreach($prosperoMako as $makongo){

        $array = $this->selectArray("nomecursos", [], ["idPNomeCurso"=>$idPCurso, "periodos.identificador"=>trim($makongo)], ["periodos"]);
        if(count($array)>0){
          $this->trimestres[]=array("identificador"=>valorArray($array, "identificador", "periodos"), "designacao"=>valorArray($array, "designacao", "periodos"), "abreviacao1"=>valorArray($array, "abreviacao1", "periodos"), "abreviacao2"=>valorArray($array, "abreviacao2", "periodos"));
        }
      }
      $camposAvaliacao=array();
      foreach(explode(",", valorArray($sobreDisciplina, $item."Avaliacoes-".$idAno, "disciplinas")) as $a){
        $triemstreDb = explode("-", $a)[0];
        $idAvaliacao = isset(explode("-", $a)[1])?explode("-", $a)[1]:"";

        $arC = $this->selectArray("campos_avaliacao", [], ["idCampoAvaliacao"=>$idAvaliacao]);
        if(($trimestre=="" || trim($trimestre)==trim($triemstreDb)) && count($arC)>0 && valorArray($arC, "identUnicaDb")!="" && valorArray($arC, "identUnicaDb")!=NULL){

          $notaMaxima=(valorArray($arC, "notaMaxima")=="" || valorArray($arC, "notaMaxima")==null)?valorArray($sobreClasse, "notaMaxima", "classes"):valorArray($arC, "notaMaxima");
          $notaMedia=(valorArray($arC, "notaMedia")=="" || valorArray($arC, "notaMedia")==null)?valorArray($sobreClasse, "notaMedia", "classes"):valorArray($arC, "notaMedia");
          $notaMinima=(valorArray($arC, "notaMinima")=="" || valorArray($arC, "notaMinima")==null)?valorArray($sobreClasse, "notaMinima", "classes"):valorArray($arC, "notaMinima");

          $camposAvaliacao[]=array("idCampoAvaliacao"=>$idAvaliacao, "identUnicaDb"=>valorArray($arC, "identUnicaDb"), "designacao1"=>valorArray($arC, "designacao1"), "cd"=>valorArray($arC, "numeroCasasDecimais"), "designacao2"=>valorArray($arC, "designacao2"), "periodo"=>trim($triemstreDb), "seApenasLeitura"=>valorArray($arC, "seApenasLeitura"), "tipoCampo"=>valorArray($arC, "tipoCampo"), "ordenacao"=>valorArray($arC, "ordenacao"), "notaMaxima"=>$notaMaxima, "notaMedia"=>$notaMedia, "notaMinima"=>$notaMinima);
        }
      }


      $camposAvaliacao = ordenar($camposAvaliacao, "periodo ASC, ordenacao ASC");
      $this->camposPautas=array();
      $this->camposArquivoPautas=array();
      foreach($camposAvaliacao as $campo){
        $this->camposPautas[]="pautas.".$campo["identUnicaDb"];
        $this->camposArquivoPautas[]="arquivo_pautas.".$campo["identUnicaDb"];
      }
      $this->camposAvaliacao=$camposAvaliacao;
      return $camposAvaliacao;
    }

    public function cabecalhoBoletim ($idPAno, $idPCurso, $classe, $trimestre=""){

      $array = $this->selectArray("nomecursos", ["cursos.tipoCurriculo", "curriculo1", "curriculo2", "curriculo3"], ["idPNomeCurso"=>$idPCurso, "cursos.idCursoEscola"=>$_SESSION['idEscolaLogada']], ["cursos"]);

      $idEscola = "";
      if (valorArray($array, "tipoCurriculo", "cursos") == "curriculo1")
        $idEscola = valorArray ($array, "curriculo1");
      else if (valorArray($array, "tipoCurriculo", "cursos") == "curriculo2")
        $idEscola = valorArray ($array, "curriculo2");
      else if (valorArray($array, "tipoCurriculo", "cursos") == "curriculo3")
        $idEscola = valorArray ($array, "curriculo3");

      if($idEscola == 0)
        $idEscola = $_SESSION["idEscolaLogada"];

      $sobreCurso = $this->selectArray("nomecursos", ["cursos.cabecalhoAvaliacoes".$classe."-".$idPAno], ["idPNomeCurso"=>$idPCurso, "cursos.idCursoEscola"=>$idEscola], ["cursos"]);

      $sobreClasse = $this->selectArray("nomecursos", ["classes.notaMaxima", "classes.notaMedia", "classes.notaMinima", "classes.periodos"], ["idPNomeCurso"=>$idPCurso, "classes.identificador"=>$classe], ["classes"]);

      $cabecalhoAvaliacao=array();
      foreach(explode(",", valorArray($sobreCurso, "cabecalhoAvaliacoes".$classe."-".$idPAno, "cursos")) as $a){

        $triemstreDb = explode("-", $a)[0];
        $idAvaliacao = isset(explode("-", $a)[1])?explode("-", $a)[1]:"";

        $arC = $this->selectArray("campos_avaliacao", [], ["idCampoAvaliacao"=>$idAvaliacao]);
        if($trimestre=="" || trim($trimestre)==trim($triemstreDb) && count($arC)>0 && trim(valorArray($arC, "identUnicaDb"))!="" && trim(valorArray($arC, "identUnicaDb"))!=NULL){

          $notaMaxima=(valorArray($arC, "notaMaxima")=="" || valorArray($arC, "notaMaxima")==null)?valorArray($sobreClasse, "notaMaxima", "classes"):valorArray($arC, "notaMaxima");
          $notaMedia=(valorArray($arC, "notaMedia")=="" || valorArray($arC, "notaMedia")==null)?valorArray($sobreClasse, "notaMedia", "classes"):valorArray($arC, "notaMedia");
          $notaMinima=(valorArray($arC, "notaMinima")=="" || valorArray($arC, "notaMinima")==null)?valorArray($sobreClasse, "notaMinima", "classes"):valorArray($arC, "notaMinima");

          $cabecalhoAvaliacao[]=array("idCampoAvaliacao"=>$idAvaliacao, "identUnicaDb"=>valorArray($arC, "identUnicaDb"), "designacao1"=>valorArray($arC, "designacao1"), "cd"=>valorArray($arC, "numeroCasasDecimais"), "designacao2"=>valorArray($arC, "designacao2"), "periodo"=>trim($triemstreDb), "seApenasLeitura"=>valorArray($arC, "seApenasLeitura"), "tipoCampo"=>valorArray($arC, "tipoCampo"), "ordenacao"=>valorArray($arC, "ordenacao"), "notaMaxima"=>$notaMaxima, "notaMedia"=>$notaMedia, "notaMinima"=>$notaMinima);
        }
      }
      return $this->cabecalhoAvaliacao = ordenar($cabecalhoAvaliacao, "periodo ASC, ordenacao ASC");
    }
}

?>
