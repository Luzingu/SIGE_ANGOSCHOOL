<?php
  if(session_status()!==PHP_SESSION_ACTIVE){
    session_cache_expire(60);
    session_start();
  }
  require_once ($_SERVER['DOCUMENT_ROOT']."/angoschool/bibliotecas/mongo/vendor/autoload.php");

  class manipulacaoDadosMae{
    public $db="";
    private $serverDb=""; private $userDb=""; private $nameDb=""; private $passwordDb="";
    public $tempoSistema="";  public $data=""; public $dataSistema ="";
    public $dia =""; public $mes =""; public $ano ="";
    public $hora = "";
    private $caminhoRetornar ="";
    public $podesExectar="nao";
    public $percentAutorAngoSchool=0.55;
    public $sobreUsuarioLogado = array();
    public $art1Escola=""; public $art2Escola=""; public $art3Escola="";
    public $mesesAnoLectivo = [9, 10, 11, 12, 1, 2, 3, 4, 5, 6, 7, 8];
    public $backup="nao";
    public $serverAlteracao="";
    public $actualizacaoDados="on";
    public $arquivarExclusoes="sim";
    private $tipoBaseDados;
    private $cumprimentoObrigatorio;

    public $camposUnicos = ["reconfirmacoes.chaveReconf", "codigo", "niveis_acesso.chave", "pautas.chavePauta", "arquivo_pautas.chavePauta", "dadosatraso.chaveEA", "chaveArquio", "chavePrincipal", "gerencMatricula.chavePrincipal", "escola.chaveEnt", "aval_desemp.chaveAvaliacao", "numeroInternoEscola", "chaveUnicaEscola", "contrato.idEscolaContrato", "estadoperiodico.chaveEstado", "gerencPerido.chaveGerPerido", "chaveH", "chaveParaTurma", "nomeCurso", "cursos.chaveCurso", "nomeDisciplina", "disciplinas.chaveDisciplina", "chaveTransf", "emolumentos.chaveEmolumento", "inscricao.chaveInscricao", "acessoPorArea.chaveArea", "chaveGestao", "designacaoArea", "identificaorMenu"];

    function __construct($areaVisualizada="", $identMenu=""){
      date_default_timezone_set('Africa/Luanda');
      $this->data = date("Y/m/d");
      $this->dataSistema = date("Y-m-d");
      $this->tempoSistema = date("H:i:s");
      $this->dia = date("d");
      $this->mes = date("m");
      $this->ano = date("Y");
      $this->hora = date("H");
      $this->minutos = date("i");
      $this->segundos = date("s");

      $this->serverAlteracao=($_SERVER['SERVER_NAME']=="angoschool.com" || $_SERVER['SERVER_NAME']=="angoschool.org")?"online":"local";

      $this->actualizacaoDados=($_SERVER['SERVER_NAME']=="angoschool.com" || $_SERVER['SERVER_NAME']=="angoschool.org")?"on":"on";

        include_once ($_SERVER["DOCUMENT_ROOT"].'/angoschool/funcoesAuxiliares.php');
        $this->conDb();

        if(file_exists("error_log")){
         unlink("error_log");
        }

        if($this->verificarSeParaExpulasar()){
            $this->podesExectar="sim";
        }else{
          echo "FA tua sessão já expirou, por favor reinicie a sessão.";
          session_unset();
          session_destroy();
        }
        if(isset($_SESSION["idUsuarioLogado"]) && isset($_SESSION['idEscolaLogada'])){
          $array = $this->selectArray("menus", ["designacaoMenu", "idPMenu", "instituicoes.idArea", "instituicoes.idArea", "icone"], ['$or'=>[array("identificadorMenu"=>$identMenu), array("subMenus.identificadorSubMenu"=>$identMenu)], "instituicoes.idEscola"=>$_SESSION['idEscolaLogada']], ["instituicoes"]);

          $this->designacaoMenu = valorArray($array, "designacaoMenu");
          $this->iconeMenu = valorArray($array, "icone");
          $this->idPArea = valorArray($array, "idArea", "instituicoes");
          $array = $this->selectArray("areas", [], ["idPArea"=>$this->idPArea, "instituicoes.idEscola"=>$_SESSION['idEscolaLogada']], ["instituicoes"]);
          $this->acessos = valorArray($array, "acessos", "instituicoes");
          $this->designacaoArea = valorArray($array, "designacaoArea");


          if($_SESSION['tipoUsuario']=="aluno"){

            $this->sobreUsuarioLogado = $this->selectArray("alunosmatriculados", [], ["idPMatricula"=>$_SESSION['idUsuarioLogado'], "escola.idMatEscola"=>$_SESSION['idEscolaLogada'], "escola.estadoAluno"=>"A"], ["escola"]);
            $this->sobreUsuarioLogado = $this->anexarTabela2($this->sobreUsuarioLogado, "escolas", "escola", "idPEscola", "idMatEscola");
            $this->sobreUsuarioLogado = $this->anexarTabela2($this->sobreUsuarioLogado, "nomecursos", "escola", "idPNomeCurso", "idMatCurso");
          }else{
            $this->sobreUsuarioLogado = $this->selectArray("entidadesprimaria", [], ["idPEntidade"=>$_SESSION['idUsuarioLogado'], "escola.idEntidadeEscola"=>$_SESSION['idEscolaLogada']], ["escola"]);
            $this->sobreUsuarioLogado = $this->anexarTabela2($this->sobreUsuarioLogado, "escolas", "escola", "idPEscola", "idEntidadeEscola");
          }
          $this->sobreEscolaLogada = $this->selectArray("escolas", [], ["idPEscola"=>$_SESSION['idEscolaLogada']]);
            $this->sobreEscolaLogada = $this->anexarTabela($this->sobreEscolaLogada, "div_terit_provincias", "idPProvincia", "provincia");
            $this->sobreEscolaLogada = $this->anexarTabela($this->sobreEscolaLogada, "div_terit_municipios", "idPMunicipio", "municipio");
        }

        $nome1Escola = explode(" ", valorArray($this->sobreUsuarioLogado, "nomeEscola"))[0];
        if(trim(substr($nome1Escola, (strlen($nome1Escola)-1)))=="a"){
            $this->art1Escola = "a";
            $this->art3Escola = "à";
            $this->art2Escola = "a";
        }else{
            $this->art1Escola = "o";
            $this->art3Escola = "ao";
            $this->art2Escola = "";
        }
        if($areaVisualizada!="" && $areaVisualizada!="sim" && $areaVisualizada!="nao" && isset($_SESSION['idPOnline'])){
          if((isset($_SESSION['areaActualVisualizada']) && $_SESSION['areaActualVisualizada']!=$areaVisualizada) || !isset($_SESSION['areaActualVisualizada'])){

            $areasAcessadas = $this->selectUmElemento("entidadesonline", "areasAcessadas", ["idPOnline"=>$_SESSION["idPOnline"]]);
            if($areasAcessadas!=""){
              $areasAcessadas .=",  ";
            }
            $areasAcessadas.="<strong>".$this->tempoSistema."</strong> ".$areaVisualizada;
            $this->editar("entidadesonline", "areasAcessadas", [$areasAcessadas], ["idPOnline"=>$_SESSION["idPOnline"]]);

            $_SESSION['areaActualVisualizada'] =$areaVisualizada;
          }
        }

      $this->protocolo = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS']=="on") ? "https" : "http");
      $this->enderecoSite = $this->protocolo."://".$_SERVER['SERVER_NAME'];
      $this->enderecoArquivos = $this->enderecoSite."/angoschool/";
      $this->directorioArquivos = $_SERVER["DOCUMENT_ROOT"];
    }

    public function conDb($tipoBaseDados="escola", $cumprimentoObrigatorio=false){
      if($cumprimentoObrigatorio==false && ((isset($_SESSION['idEscolaLogada']) && $_SESSION['idEscolaLogada']==4) || (isset($_SESSION['idInstituicaoEntrar']) && $_SESSION['idInstituicaoEntrar']==4))){
        if($tipoBaseDados=="inscricao"){
          $tipoBaseDados="teste_inscricao";
        }else{
          $tipoBaseDados="teste";
        }
      }
      if($tipoBaseDados !="grupo_alunos")
      {
        $this->tipoBaseDados = $tipoBaseDados;
        $this->cumprimentoObrigatorio = $cumprimentoObrigatorio;
      }

      if($_SERVER['SERVER_NAME']=="localhost"){
        $this->conexaoDb = new MongoDB\Client;
      }else{
        $this->conexaoDb = new MongoDB\Client("mongodb://abigael:Renapol1..abigael@154.38.185.120:27019");
      }
      //$this->conexaoDb = new MongoDB\Client(stand_up("Xy=bW9uZ29kYitzcnY6Ly9heW5lemFudGEuLmhudGFtdToyeWpvMUhISU1xLi5oNHJ4N0d4QGNsdXN0ZXIwLjN0OS4uaHFweXgubW9uZ29kYi5uZXQv=Y"));

        $this->db = $this->conexaoDb->$tipoBaseDados;
    }

    public function selectArray($tabela, $project=array(), $match=array(), $unwind=array(), $limit="", $group=array(), $sort=array(), $matchMae=array()){

      $arrayRetorno=array();
      if($tabela=="alunosmatriculados"){

        $agrupador = $this->selectArray("agrup_alunos", ["idPGrupo"]);

        for($i=(count($agrupador)-1); $i>=0; $i--){
          $array = $this->select("alunos_".$i, $project, $match, $unwind, $limit, $group, array(), $matchMae);
          $arrayRetorno = array_merge($arrayRetorno, $array);
          if($limit!="" && count($arrayRetorno)>=$limit){
            break;
          }
        }

        $ordecaoArray="";
        foreach(array_keys($sort) as $ordem){
          if($ordecaoArray!=""){
            $ordecaoArray .=",";
          }
          $ordecaoArray .=$ordem;
          if($sort[$ordem]==-1){
            $ordecaoArray .=" DESC";
          }else{
            $ordecaoArray .=" ASC";
          }
        }
        if($ordecaoArray!=""){
          $arrayRetorno = ordenar($arrayRetorno,$ordecaoArray);
        }

      }else{
        $arrayRetorno = $this->select($tabela, $project, $match, $unwind, $limit, $group, $sort, $matchMae);
      }
      return $arrayRetorno;
    }

  public function select($tabela, $project=array(), $match=array(), $unwind=array(), $limit="", $group=array(), $sort=array(), $matchMae=array()){

    $this->dbUsar($tabela);
    $pipeline=array();
    if(count($project)>0){
      $campos=array();
      $pedroPepe=array();
      foreach($project as $p){
        if(!seTemValorNoArray($pedroPepe, $p)){
          $pedroPepe[]=$p;
          $campos[$p]=1;
        }
      }
      foreach($sort as $p){
        if(!seTemValorNoArray($pedroPepe, $p)){
          $pedroPepe[]=$p;
          $campos[$p]=1;
        }
      }
      foreach(array_keys($match) as $key){
        if($key=='$text'){
          //Não entra nada
        }else if($key=='$or'){

          for($i=0; $i<=(count($match[$key])-1); $i++){
            foreach(array_keys($match[$key][$i]) as $subKey){
              if(!seTemValorNoArray($pedroPepe, $subKey)){
                $pedroPepe[]=$subKey;
                $campos[$subKey]=1;
              }
            }
          }
        }else{
          if(!seTemValorNoArray($pedroPepe, $key)){
            $pedroPepe[]=$key;
            $campos[$key]=1;
          }
        }
      }
    }

    if(count(array_keys($match))>0){
      $contador=0;
      foreach(array_keys($match) as $key){
        if(!is_object($match[$key])){
          $match[$key] = luzl($match[$key]);
          $contador++;
        }
      }
    }

    if(count(array_keys($matchMae))>0){
      $contador=0;
      foreach(array_keys($matchMae) as $key){
        if(!is_object($matchMae[$key])){
          $matchMae[$key] = luzl($matchMae[$key]);
          $contador++;
        }
      }
    }
    if(count(array_keys($matchMae))>0){
      $pipeline[]=['$match'=>$matchMae];
    }

    if(count($unwind)<=0 && count(array_keys($group))<=0 && count(array_keys($sort))<=1){
      $atributo2=array();
      if(count($project)>0){
        $atributo2["projection"]=$campos;
      }
      if(count(array_keys($sort))>0){
        $atributo2["sort"]=$sort;
      }
      if($limit!=""){
        $atributo2["limit"]=intval($limit);
      }
      $arrayRetorno = $this->db->$tabela->find($match, $atributo2);
    }else{
      if(count($unwind)>0){
        $tabelas=array();
        foreach($unwind as $un){
          $pipeline[]=['$unwind'=>array("path"=>'$'.$un)];
        }
      }

      if(count(array_keys($match))>0){
        $pipeline[]=['$match'=>$match];
      }
      if(count($project)>0){
        $pipeline[]=['$project'=>$campos];
      }
      if(count(array_keys($group))>0){
        if(!seTemValorNoArray(array_keys($group), "_id")){
          $group["_id"]=0;
        }
        $pipeline[]=['$group'=>$group];
      }
      if(count(array_keys($sort))>0){
        $pipeline[]=['$sort'=>$sort];
      }
      if($limit!=""){
        $pipeline[]=['$limit'=>intval($limit)];
      }
      $arrayRetorno = $this->db->$tabela->aggregate($pipeline);
    }
    $arrayRetorno = iterator_to_array($arrayRetorno);
    return $arrayRetorno;
  }

  public function selectJson($tabela, $project=array(), $match=array(), $unwind=array(), $limit="", $group=array(), $sort=array()){
    return json_encode($this->selectArray($tabela, $project, $match, $unwind, $limit, $group, $sort));
  }

  public function selectDistinct($tabela, $campo, $match=array(), $unwind=array(), $limit=""){
    $grup = ["_id"=>'$'.$campo];
    return $this->selectArray($tabela, array(), $match, $unwind, $limit, $grup, ["_id"=>-1]);
  }

  public function selectCondClasseCurso($tipoSeleccao, $tabela, $project=array(), $match=array(), $classe, $matchCurso, $unwind=array(), $limit="", $group=array(), $sort=array()){


    foreach(array_keys($matchCurso) as $chave){
      $match[$chave] = $matchCurso[$chave];
    }
    if($tipoSeleccao=="array"){
      return $this->selectArray($tabela, $project, $match, $unwind, $limit, $group, $sort);
    }else if($tipoSeleccao=="um"){
      return $this->selectUmElemento($tabela, $project, $match, $unwind, $group, $sort);
    }else if($tipoSeleccao=="distinct"){
      return $this->selectDistinct($tabela, $project, $match, $unwind, $limit);
    }else if($tipoSeleccao=="json"){
      return $this->selectJson($tabela, $project, $match, $unwind, $limit, $group, $sort);
    }
  }

  public function selectUmElemento($tabela, $campo="", $match=array(), $unwind=array(), $group=array(), $sort=array(), $objecto=""){

    $retorno="";
    $arrayRetorno = $this->selectArray($tabela, [$campo], $match, $unwind, 1, $group, $sort);
    $retorno = valorArray($arrayRetorno, $campo, $objecto);
    if($retorno=="" && (count($unwind)>0 || $objecto!="")){
      if(count($unwind)==1){
        $objecto = $unwind[0];
      }
      $arrayRetorno = $this->selectArray($tabela, [$objecto.".".$campo], $match, $unwind, 1, $group, $sort);
      $retorno = valorArray($arrayRetorno, $campo, $objecto);
    }
    return $retorno;
  }

  public function inserirObjecto ($tabela, $nomeObjecto, $idPrincipal, $string, $valores, $condicoes, $matondo="sim", $divine="nao", $idPorDefeito=""){

    $this->dbUsar($tabela);

    $chavesCondicoes = array_keys($condicoes);
    foreach($chavesCondicoes as $chave){
      $condicoes[$chave] = luzl($condicoes[$chave]);
    }
    if($idPorDefeito==""){
      $idPorDefeito = substr(str_shuffle("1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ"),0, 6);
    }
    $valoresAInserir[$idPrincipal]=$idPorDefeito;

    $seCampoUnicoJaExistem="nao";
    $i=0;
    foreach(explode(",", $string) as $a){
      if(seTemValorNoArray($this->camposUnicos, $nomeObjecto.".".trim($a)) && luzl($valores[$i])!="" && luzl($valores[$i])!=null){
        if($this->seCampoUnicoJaExistem($tabela, $nomeObjecto.".".trim($a), luzl($valores[$i]), $condicoes)){
          $seCampoUnicoJaExistem="sim";
          break;
        }
      }
      $valoresAInserir[trim($a)]=luzl($valores[$i]);
      $i++;
    }

    $idUsuario="";
    if(isset($_SESSION['idUsuarioLogado'])){
      $idUsuario=$_SESSION['idEscolaLogada'];
    }
    $idEscola = isset($_SESSION['idEscolaLogada'])?$_SESSION['idEscolaLogada']:"";
    $valoresAInserir['update']=$this->dataSistema;
    $valoresAInserir['backup_'.$this->serverAlteracao.'_'.$idEscola]=$this->backup;
    $valoresAInserir['backup_'.$this->serverAlteracao]=$this->backup;
    $valoresAInserir['timeUpdate']=$this->tempoSistema;
    $valoresAInserir['userUpdate']=$idUsuario;

    if($this->actualizacaoDados=="off" || $seCampoUnicoJaExistem=="sim"){
      if($divine=="nao"){
        return $divine;
      }else{
        echo $divine;
      }
      exit();
    }

    try{
      $this->db->$tabela->updateMany($condicoes, ['$push'=>[$nomeObjecto=>$valoresAInserir]]);

      if($matondo=="sim"){
        $idUsuario="";
        if(isset($_SESSION['idUsuarioLogado'])){
          $idUsuario=$_SESSION['idEscolaLogada'];
        }
        $idEscola = isset($_SESSION['idEscolaLogada'])?$_SESSION['idEscolaLogada']:"";
        $this->editar($tabela, "update, timeUpdate, userUpdate, backup_".
        $this->serverAlteracao."_".$idEscola.", backup_".
        $this->serverAlteracao, [$this->dataSistema, $this->tempoSistema, $idUsuario, $this->backup, $this->backup], $condicoes);
        return $matondo;
      }else{
        echo $matondo;
      }
    }catch(MongoDB\Driver\Exception\BulkWriteException $e) {
      if($divine=="nao"){
        return $divine;
      }else{
        echo $divine;
      }
    }
  }
  public function inserir ($tabela, $idPrincipal, $string, $valores, $matondo="sim", $divine="nao", $tabelasAnexar=array(), $idPorDefeito=""){

    $this->dbUsar($tabela);
    if($idPorDefeito==""){
      $id = $this->selectArray($tabela, [$idPrincipal], [], [], 1, [], [$idPrincipal=>-1]);
      $id = valorArray($id, $idPrincipal);
    }else{
      $id=$idPorDefeito;
    }
    $valoresAInserir[$idPrincipal]=intval($id)+1;

    $seCampoUnicoJaExistem="nao";
    $i=0;
    foreach(explode(",", $string) as $a){

      if(seTemValorNoArray($this->camposUnicos, trim($a)) && luzl($valores[$i])!="" && luzl($valores[$i])!=null){
        if($this->seCampoUnicoJaExistem($tabela, trim($a), luzl($valores[$i]))){
          $seCampoUnicoJaExistem="sim";
          break;
        }
      }
      $valoresAInserir[trim($a)]=luzl($valores[$i]);
      $i++;
    }
    if($this->actualizacaoDados=="off" || $seCampoUnicoJaExistem=="sim"){
      if($divine=="nao"){
        return $divine;
      }else{
        echo $divine;
      }
      exit();
    }

    $idEscola = isset($_SESSION['idEscolaLogada'])?$_SESSION['idEscolaLogada']:"";
    $valoresAInserir["update"]=$this->dataSistema;
    $valoresAInserir["backupGeral"]=$this->backup;
    $valoresAInserir["backup_".$this->serverAlteracao."_".$idEscola]=$this->backup;
    $valoresAInserir["backup_".$this->serverAlteracao]=$this->backup;
    $valoresAInserir["timeUpdate"]=$this->tempoSistema;
    if(isset($_SESSION["idUsuarioLogado"])){
      $valoresAInserir["userUpdate"]=$_SESSION['idUsuarioLogado'];
    }

    try{

      if(count($tabelasAnexar)>0){
        foreach($tabelasAnexar as $tbl){
          $array = $this->selectArray($tbl[0], [], [$tbl[2]=>luzl($tbl[1])]);
          foreach(retornarChaves($array) as $chave){
            if($chave!="_id"){

              if(isset($array[0][$chave]) && !is_object($array[0][$chave]) && seTemValorNoArray(camposAnexar(), $chave)){

                $string .=",".$chave;
                $valoresAInserir[trim($chave)]=luzl($array[0][$chave]);
              }
            }
          }
        }
      }
      $this->db->$tabela->insertOne($valoresAInserir);
      if($matondo=="sim"){
        return $matondo;
      }else{
        echo $matondo;
      }
    }catch(MongoDB\Driver\Exception\BulkWriteException $e) {
      if($divine=="nao"){
        return $divine;
      }else{
        echo $divine;
      }
    }
  }

  public function editarItemObjecto ($tabela, $nomeObjecto, $string, $valores, $condicoesMae, $condicoesFilha, $matondo="sim", $divine="nao", $qtdArray=1000000){

    $this->dbUsar($tabela);

    if($this->actualizacaoDados=="off"){
      if($divine=="nao"){
        return $divine;
      }else{
        echo $divine;
      }
      exit();
    }

    $condicaoSeleccao =array();
    $chavesCondicoes = array_keys($condicoesMae);

    $condicaoActualizacao=array();
    foreach($chavesCondicoes as $chave){
      $condicaoSeleccao[$chave] = luzl($condicoesMae[$chave]);
      $condicaoActualizacao[$chave] = luzl($condicoesMae[$chave]);
    }

    $chavesCondicoes = array_keys($condicoesFilha);
    foreach($chavesCondicoes as $chave){
      $condicaoSeleccao[$nomeObjecto.".".$chave] = luzl($condicoesFilha[$chave]);
      $condicao2[$chave] = luzl($condicoesFilha[$chave]);
    }

    $novoValoresArray=array();
    $contador=0;
    foreach(explode(",", $string) as $campo){
      $novoValoresArray[$nomeObjecto.'.$.'.trim($campo)] = luzl($valores[$contador]);
      $contador++;
    }

    $idEscola = isset($_SESSION['idEscolaLogada'])?$_SESSION['idEscolaLogada']:"";

    $novoValoresArray[$nomeObjecto.'.$.update']=$this->dataSistema;
    $novoValoresArray[$nomeObjecto.'.$.backup_'.$this->serverAlteracao.'_'.$idEscola]=$this->backup;
    $novoValoresArray[$nomeObjecto.'.$.backup_'.$this->serverAlteracao]=$this->backup;
    $novoValoresArray[$nomeObjecto.'.$.timeUpdate']=$this->tempoSistema;
    $idUsuario="";
    if(isset($_SESSION["idUsuarioLogado"])){
      $idUsuario=$_SESSION['idUsuarioLogado'];
      $novoValoresArray[$nomeObjecto.'.$.userUpdate']=$_SESSION['idUsuarioLogado'];
    }
    try{

        $campoPrincipal =$this->retornarCampoPrincipalDoObjecto($tabela, $nomeObjecto);
        if($campoPrincipal==""){
          $array = $this->selectArray($tabela, [], $condicaoSeleccao, [$nomeObjecto], 1);
          $campoPrincipal = "";
          foreach($array as $a){
            $campoPrincipal = retornarChaves($a[$nomeObjecto])[0];
            break;
          }
        }

        if($campoPrincipal!=""){
          foreach($this->selectArray($tabela, [$nomeObjecto.".".$campoPrincipal], $condicaoSeleccao, [$nomeObjecto], $qtdArray) as $b){
            //
            $condicao2[$campoPrincipal]=$b[$nomeObjecto][$campoPrincipal];
            $condicaoActualizacao[$nomeObjecto]=['$elemMatch'=>$condicao2];
            $this->db->$tabela->updateMany($condicaoActualizacao, ['$set'=>$novoValoresArray]);
          }
          $this->editar($tabela, "update, timeUpdate, userUpdate, backup_".$this->serverAlteracao."_".$idEscola.", backup_".$this->serverAlteracao, [$this->dataSistema, $this->tempoSistema, $idUsuario, $this->backup, $this->backup], $condicoesMae);
        }

        if($matondo=="sim"){
          return $matondo;
        }else{
          echo $matondo;
        }
    }catch(MongoDB\Driver\Exception\BulkWriteException $e) {
      if($divine=="nao"){
        return $divine;
      }else{
        echo $divine;
      }
    }
  }
  private function retornarCampoPrincipalDoObjecto($tabela, $objecto){
    $mbengani["alunosmatriculados"]["escola"]="idPAlEscola";
    $mbengani["alunosmatriculados"]["reconfirmacoes"]="idPReconf";
    $mbengani["alunosmatriculados"]["avaliacao_anual"]="idPAval";
    $mbengani["alunosmatriculados"]["turmas"]="idPTurma";
    $mbengani["alunosmatriculados"]["notas_finais"]="idPNotaF";
    $mbengani["alunosmatriculados"]["pautas"]="idPPauta";
    $mbengani["alunosmatriculados"]["arquivo_pautas"]="idPPauta";
    $mbengani["alunosmatriculados"]["pagamentos"]="idPHistoricoConta";
    $mbengani["alunosmatriculados"]["dadosatraso"]="idDAtraso";
    $mbengani["alunosmatriculados"]["transferencia"]="idPTransferencia";
    $mbengani["alunosmatriculados"]["alteracoes_notas"]="idPHistorial";
    $mbengani["alunosmatriculados"]["transferencia"]="idPTransferencia";

    $mbengani["entidadesprimaria"]["escola"]="idP_Escola";
    $mbengani["entidadesprimaria"]["niveis_acesso"]="idPNiveisAcesso";
    $mbengani["entidadesprimaria"]["classes_aceso"]="idPClasseAcesso";
    $mbengani["entidadesprimaria"]["controlPresenca"]="idPControl";

    $mbengani["escolas"]["contrato"]="idPContrato";
    $mbengani["escolas"]["anexos"]="idPAnexo";
    $mbengani["escolas"]["estadoperiodico"]="idPEstado";
    $mbengani["escolas"]["gerencMatricula"]="idPGerMatr";
    $mbengani["escolas"]["fotos"]="idPGaleria";
    $mbengani["escolas"]["gerencPerido"]="idPGerPeriodo";
    $mbengani["escolas"]["pagamentos"]="idPPagamento";
    $mbengani["escolas"]["emolumentos"]="idPEmolumento";
    $mbengani["escolas"]["trans_classes"]="idPTransClasse";

    $mbengani["nomecursos"]["cursos"]="idPCurso";
    $mbengani["nomedisciplinas"]["disciplinas"]="idPDisciplina";

    if(count(explode("_", $tabela))==2 && explode("_", $tabela)[0]=="alunos"){
      $tabela="alunosmatriculados";
    }
    return isset($mbengani[$tabela][$objecto])?$mbengani[$tabela][$objecto]:"";
  }

  public function editar ($tabela, $string, $valores, $condicoes=array(), $matondo="sim", $divine="nao", $tabelasAnexar=array()){

    $this->dbUsar($tabela);

    if($this->actualizacaoDados=="off"){
      if($divine=="nao"){
        return $divine;
      }else{
        echo $divine;
      }
      exit();
    }

    $valoresAEditar=array();
    $i=0;
    foreach(explode(",", $string) as $a){
      $valoresAEditar[trim($a)]=luzl($valores[$i]);
      $i++;
    }

    $idEscola = isset($_SESSION['idEscolaLogada'])?$_SESSION['idEscolaLogada']:"";

    $valoresAEditar["backup_".$this->serverAlteracao."_".$idEscola]=$this->backup;
    $valoresAEditar["backup_".$this->serverAlteracao]=$this->backup;
    $valoresAEditar["backupGeral"]=$this->backup;

    $valoresAEditar["update"]=$this->dataSistema;
    $valoresAEditar["timeUpdate"]=$this->tempoSistema;
    if(isset($_SESSION["idUsuarioLogado"])){
      $valoresAEditar["userUpdate"]=$_SESSION['idUsuarioLogado'];
    }

    $chavesCondicoes = array_keys($condicoes);
    foreach($chavesCondicoes as $chave){
      $condicoes[$chave] = luzl($condicoes[$chave]);
    }

    try{
        if(count($tabelasAnexar)>0){
          foreach($tabelasAnexar as $tbl){

            $array = $this->selectArray($tbl[0], [], [$tbl[2]=>$tbl[1]]);
            foreach(retornarChaves($array) as $chave){
              if($chave!="_id"){

                if(isset($array[0][$chave]) && !is_object($array[0][$chave]) && seTemValorNoArray(camposAnexar(), $chave)){

                  $string .=",".$chave;
                  $valoresAEditar[trim($chave)]=luzl($array[0][$chave]);
                }
              }
            }
          }
        }
        $this->db->$tabela->updateMany($condicoes, ['$set'=>$valoresAEditar]);
        if($matondo=="sim"){
          return $matondo;
        }else{
          echo $matondo;
        }
    }catch(MongoDB\Driver\Exception\BulkWriteException $e) {
      if($divine=="nao"){
        return $divine;
      }else{
        echo $divine;
      }
    }
  }

  public function excluir ($tabela, $condicoes=array(), $matondo="sim", $divine="nao", $quatidaAExcluir=10000000){

    $this->dbUsar($tabela);

    if($this->actualizacaoDados=="off"){
      if($divine=="nao"){
        return $divine;
      }else{
        echo $divine;
      }
      exit();
    }

    $chavesCondicoes = array_keys($condicoes);
    foreach($chavesCondicoes as $chave){
      $condicoes[$chave] = luzl($condicoes[$chave]);
    }
    try{
      if($quatidaAExcluir<=1){
        $this->db->$tabela->deleteOne($condicoes);
      }else{
        $this->db->$tabela->deleteMany($condicoes);
      }

      if($_SERVER['SERVER_NAME']!="localhost" && $tabela!="dados_excluidos" && $tabela!="dados_excluidos2" && $this->arquivarExclusoes=="sim"){
        $this->inserir("dados_excluidos", "idDExcl", "tabela, condicoes", [$tabela, $condicoes]);
      }
      if($matondo=="sim"){
        return $matondo;
      }else{
        echo $matondo;
      }

    }catch(MongoDB\Driver\Exception\BulkWriteException $e) {
      if($divine=="nao"){
        return $divine;
      }else{
        echo $divine;
      }
    }
  }

  public function excluirItemObjecto ($tabela, $nomeObjecto, $condicoesMae, $condicoesFilha, $matondo="sim", $divine="nao", $qtdArray=1){

    $this->dbUsar($tabela);

    if($this->actualizacaoDados=="off"){
      if($divine=="nao"){
        return $divine;
      }else{
        echo $divine;
      }
      exit();
    }

    $chavesCondicoes = array_keys($condicoesMae);
    foreach($chavesCondicoes as $chave){
      $condicoesMae[$chave] = luzl($condicoesMae[$chave]);
    }

    $chavesCondicoes = array_keys($condicoesFilha);
    foreach($chavesCondicoes as $chave){
      $condicoesFilha[$chave] = luzl($condicoesFilha[$chave]);
    }
    try{
        $idUsuario="";
        if(isset($_SESSION['idUsuarioLogado'])){
          $idUsuario=$_SESSION['idEscolaLogada'];
        }
        $idEscola = isset($_SESSION['idEscolaLogada'])?$_SESSION['idEscolaLogada']:"";

        $this->db->$tabela->updateMany($condicoesMae, ['$pull'=>[$nomeObjecto=>$condicoesFilha]]);

        $this->editar($tabela, "update, timeUpdate, userUpdate, backup_".$this->serverAlteracao."_".$idEscola.", backup_".$this->serverAlteracao, [$this->dataSistema, $this->tempoSistema, $idUsuario, $this->backup, $this->backup], $condicoesMae);

        if($_SERVER['SERVER_NAME']!="localhost" && $tabela!="dados_excluidos" && $tabela!="dados_excluidos2" && $this->arquivarExclusoes=="sim"){
          $this->inserir("dados_excluidos2", "idDExcl", "tabela, nomeObjecto, condicoesMae, condicoesFilha", [$tabela, $nomeObjecto, $condicoesMae, $condicoesFilha]);
        }

        if($matondo=="sim"){
          return $matondo;
        }else{
          echo $matondo;
        }

    }catch(MongoDB\Driver\Exception\BulkWriteException $e) {
      if($divine=="nao"){
        return $divine;
      }else{
        echo $divine;
      }
    }
  }

  public function editarCondClasseCurso($tabela, $string, $valores, $condicoes=array(), $classe, $condicoesCurso, $matondo="sim", $divine="nao", $tabelasAnexar=array()){


      foreach(array_keys($condicoesCurso) as $chave){
        $condicoes[$chave] = $condicoesCurso[$chave];
      }
    return $this->editar($tabela, $string, $valores, $condicoes, $matondo, $divine, $tabelasAnexar);
  }

  private function seCampoUnicoJaExistem($tabela, $campo, $valor, $condicaoMae=array()){

    $condicao = [$campo=>$valor];
    foreach(array_keys($condicaoMae) as $key){
      $condicao[$key]=$condicaoMae[$key];
    }
    return count($this->selectArray($tabela, [$campo], $condicao, [], 1))>0;
  }

  public function anexarTabela($array, $tabela2, $campo1, $campo2){
    $newArray=array();
    $i=0;
    foreach($array as $a){
      $newArray[$i]=$a;
      if(isset($a[$campo2])){
        $sobreTb = $this->selectArray($tabela2, [], [$campo1=>$a[$campo2]]);

        foreach(retornarChaves($sobreTb) as $chave){
          if(isset($sobreTb[0][$chave]) && !is_object($sobreTb[0][$chave])){

            $newArray[$i][$chave]=$sobreTb[0][$chave];
          }
        }
      }
      $i++;
    }
    return $newArray;
  }

  public function anexarTabela2($array, $tabela2, $objecto, $campo1, $campo2){
    $newArray=array();

    $i=0;
    foreach($array as $a){

      $newArray[$i]=$a;
      if(isset($a[$objecto][$campo2])){
        $sobreTb = $this->selectArray($tabela2, [], [$campo1=>$a[$objecto][$campo2]]);
        foreach(retornarChaves($sobreTb) as $chave){
          if(isset($sobreTb[0][$chave]) && !is_object($sobreTb[0][$chave])){
            $newArray[$i][$chave]=$sobreTb[0][$chave];
          }
        }
      }
      $i++;
    }
    return $newArray;
  }

  public function objectoAluno($idPMatricula){
    $this->objectoAluno=array();
    foreach($this->selectArray("alunosmatriculados", ["idPMatricula"=>$idPMatricula]) as $a){
      $this->objectoAluno = $a;
      break;
    }
  }

  public function objectoAluno2($idPMatricula){
    foreach($this->alunos as $aluno){
      if($idPMatricula==$aluno["idPMatricula"]){
        $this->objectoAluno=$aluno;
        break;
      }
    }
  }

  public function verificarSeParaExpulasar(){
    $this->podesExectar="sim";

    if(isset($_SESSION['idPOnline'])){

      $dataSaida = $this->dataSistema.$this->tempoSistema;
      $estadoExpulsao="I";
      $idPOnline="";

      if($_SESSION['tipoUsuario']=="aluno"){
        $condicao = ["idUsuarioLogado"=>$_SESSION['idUsuarioLogado'], "tipoUsuario"=>"aluno", "estadoExpulsao"=>"A"];
      }else{
        $condicao = ["idUsuarioLogado"=>$_SESSION['idUsuarioLogado'], "tipoUsuario"=>"entidade", "estadoExpulsao"=>"A"];
      }
      foreach ($this->selectArray("entidadesonline", [], $condicao) as $data){
        $dataSaida = strtotime($data["dataSaida"].$data["horaSaida"]." + 3000 seconds");
        $estadoExpulsao=$data["estadoExpulsao"];
      }
      $this->editar("entidadesonline", "dataSaida, horaSaida", [$this->dataSistema, $this->tempoSistema], $condicao);
        $this->podesExectar="sim";
        return true;

      //if(date("Y-m-d H:i:s")>date("Y-m-d H:i:s", intval($dataSaida)) || $estadoExpulsao=="I"){

      /*if($estadoExpulsao!="A"){
        $this->editar("entidadesonline", "estadoExpulsao", ["I"], ["idPOnline"=>$_SESSION["idPOnline"]]);
        $this->podesExectar="nao";
        return false;
      }else{

      }*/

    }else{
      return true;
    }
  }

     public function valorDisponivelInstituicao($tipoConta="geral"){
        $this->valorDisponivel = $this->selectUmElemento("historicocontaescola", "precoFinal", ["estadoHistorico"=>"V", "idHistoricoEscola"=>$_SESSION["idEscolaLogada"], "tipoConta"=>$tipoConta], [], [], ["idPHistoricoConta"=>-1]);
        return floatval($this->valorDisponivel);
    }

    public function actualizarContaInstituicao($contaUsar, $valorEfectuado, $identificador=""){

      if($contaUsar!=""){
        $valorInicial = floatval($this->selectUmElemento("escolas", $contaUsar, ["idPEscola"=>$_SESSION['idEscolaLogada']]));

        $this->editar("escolas", $contaUsar, [$valorInicial+$valorEfectuado], ["idPEscola"=>$_SESSION['idEscolaLogada']]);

        if($identificador!=""){
          $this->editar("facturas", "valorFinal", [($valorInicial+$valorEfectuado)], ["identificador"=>$identificador]);
        }
      }
    }
    public function identificacaoUnica($coleccao, $tipoDocumento, $serieFactura=""){

      if($serieFactura==""){
        $serieFactura=valorArray($this->sobreEscolaLogada, "serieFactura");
      }
      $nf = $this->selectUmElemento($coleccao, "numeroSequencial", ["idDocEscola"=>$_SESSION['idEscolaLogada'], "serieDocumento"=>$serieFactura, "tipoDocumento"=>$tipoDocumento, "dataEmissao"=>new \MongoDB\BSON\Regex(explode("-", $this->dataSistema)[0]."-")], [], [], ["idPDocumento"=>-1]);
      $this->numeroSequencial = completarNumero((intval($nf)+1));

      $numeracaDocumento = $tipoDocumento." ".$serieFactura.explode("-", $this->dataSistema)[0]."/".$this->numeroSequencial;

      $this->assinaturaDigital= $this->assinaturaDigital($this->dataSistema.",".$this->dataSistema."T".$this->tempoSistema.";".$numeracaDocumento);

      return $numeracaDocumento;
    }

    public function assinaturaDigital($texto){
      $privateKey = openssl_pkey_get_private("file://".$_SERVER['DOCUMENT_ROOT']."/angoschool/bibliotecas/chavePrivada.pem", "luzl2023..");
      if ($privateKey === false) {
        echo "Erro ao carregar a chave privada.";
      }else{
        openssl_sign($texto, $assinatura, $privateKey, OPENSSL_ALGO_SHA256);
        return base64_encode($assinatura);
      }
    }

    public function totalizadorItensDocumentos($coleccao, $identificacaoUnica){

      $totalizadorSemImposto=0;
      $totalizadorComImposto=0;
      foreach($this->selectArray($coleccao, ["itens.valorTotSemImposto", "itens.valorTotComImposto"], ["identificacaoUnica"=>$identificacaoUnica, "idDocEscola"=>$_SESSION['idEscolaLogada']], ["itens"]) as $tot){

        $totalizadorSemImposto += floatval(nelson($tot, "valorTotSemImposto", "itens"));
        $totalizadorComImposto += floatval(nelson($tot, "valorTotComImposto", "itens"));

      }
      $this->editar($coleccao, "valorTotSemImposto, valorTotComImposto", [$totalizadorSemImposto, $totalizadorComImposto], ["identificacaoUnica"=>$identificacaoUnica, "idDocEscola"=>$_SESSION['idEscolaLogada']]);
    }


    public function prorogarContratoEscolasPosPago($idPEscola){

      $sobreContrato = $this->selectArray("escolas", [], ["idPEscola"=>$idPEscola], ["contrato"]);

      $prossoProrrogar="not";
      $novoSaldoParaPagamentoPosPago=0;
      //Verificar se já começou a usufruir dos serviços do sistema.
      if(valorArray($sobreContrato, "fimPrazoPosPago", "contrato")==NULL && valorArray($sobreContrato, "fimPrazoPosPago", "contrato")==""){
        $novoSaldoParaPagamentoPosPago=0;
        $prossoProrrogar="yes";
      }else{
        if((double)valorArray($sobreContrato, "saldoParaPagamentoPosPago", "contrato")>=(int)valorArray($sobreContrato, "mesesConsecutivosParaBloquear", "contrato")*2*(double)valorArray($sobreContrato, "valorPagoPor15Dias", "contrato")){

          $novoSaldoParaPagamentoPosPago =(double)valorArray($sobreContrato, "saldoParaPagamentoPosPago", "contrato") -(int)valorArray($sobreContrato, "mesesConsecutivosParaBloquear", "contrato")*2*(double)valorArray($sobreContrato, "valorPagoPor15Dias", "contrato");
          $prossoProrrogar="yes";
        }
      }

      if($prossoProrrogar=="yes" && calcularDiferencaEntreDatas(valorArray($sobreContrato, "fimPrazoPosPago", "contrato"), $this->dataSistema)<=20){

        $novoInicioPrazo = $this->adicionarDiasData(((int)valorArray($sobreContrato, "mesesConsecutivosParaBloquear", "contrato")*30), valorArray($sobreContrato, "inicioPrazoPosPago", "contrato"));

        $novoFimPrazo = $this->adicionarDiasData(((int)valorArray($sobreContrato, "mesesConsecutivosParaBloquear", "contrato")*30), $novoInicioPrazo);

        for($i=1; $i<=valorArray($sobreContrato, "mesesConsecutivosParaBloquear", "contrato"); $i++){
          if($i==1){
            $dataPagamento1=$novoInicioPrazo;
            $dataPagamento2 = $this->adicionarDiasData(30, $novoInicioPrazo);
          }else{
            $dataPagamento1 = $this->adicionarDiasData(30, $dataPagamento1);
            $dataPagamento2 = $this->adicionarDiasData(30, $dataPagamento2);
          }

          $this->inserirObjecto("escolas", "mesPagosSistema", "idPag", "data, hora, dataPagamento1, dataPagamento2, valorPago, ordenacao", [$this->dataSistema, $this->tempoSistema, $dataPagamento1, $dataPagamento2, (2*(double)valorArray($sobreContrato, "valorPagoPor15Dias", "contrato")), $this->dataSistema."-".$i], ["idPEscola"=>$_SESSION['idEscolaLogada']]);
        }


        return $this->editarItemObjecto("escolas", "contrato", "inicioPrazoPosPago, fimPrazoPosPago, saldoParaPagamentoPosPago", [$novoInicioPrazo, $novoFimPrazo, $novoSaldoParaPagamentoPosPago], ["idPEscola"=>$idPEscola], ["idEscolaContrato"=>$idPEscola]);
      }else{
        return "nao";
      }
    }
    public function adicionarDiasData($numDias=0, $dataEmReferencia=""){
      if($dataEmReferencia==""){
        $dataEmReferencia = $this->dataSistema;
      }
      return date("Y-m-d", mktime(0, 0, 0, explode("-", $dataEmReferencia)[1], (explode("-", $dataEmReferencia)[2]+$numDias), explode("-", $dataEmReferencia)[0]));
    }

    public function entidades($campos=["idPEntidade", "nomeEntidade"], $tipoPessoal="", $efectividade=""){
      
      //$divine = ["escola.idEntidadeEscola"=>$_SESSION["idEscolaLogada"]];
      $divine = ["escola.idEntidadeEscola"=>$_SESSION["idEscolaLogada"], "escola.estadoActividadeEntidade"=>"A"];
      if($tipoPessoal!=""){
        $divine["escola.tipoPessoal"]=$tipoPessoal;
      }
      if($efectividade!=""){
        $divine["escola.efectividade"]=$efectividade;
      }

      $this->docentes = $this->selectArray("entidadesprimaria", $campos, $divine, ["escola"], "", [], ["nomeEntidade"=>1]);
      return $this->docentes;
    }

    function valorContratatualDasEscolas($idPEscola){

        $sobreContrato = $this->selectArray("escolas", [], ["idPEscola"=>$idPEscola], ["contrato"]);

        $valorPorAluno = valorArray($sobreContrato, "valorPorAluno", "contrato");
        $valorPagoPor15Dias = valorArray($sobreContrato, "valorPagoPor15Dias", "contrato");

        /*if(valorArray($sobreContrato, "modoPagamento")=="porAluno"){

          $anoActual =

          $idAnoActual = $this->selectUmElemento("ano_escola", "idFAno", ["estadoAnoL"=>"V", "idAnoEscola"=>$idPEscola]);

          $totalAlunos = count($this->selectArray("alunosreconfirmados", ["idReconfEscola"=>$idPEscola, "idReconfAno"=>$idAnoActual]));
          $valorPagoPor15Dias = ($valorPorAluno*$totalAlunos)/2;
        }else{
          $valorPorAluno = NULL;
        }*/
        $this->editarItemObjecto("escolas", "contrato", "valorPorAluno, valorPagoPor15Dias", [$valorPorAluno, $valorPagoPor15Dias], ["idPEscola"=>$idPEscola], []);
    }

    public function manipularConta($idConta, $accao, $valor){

      $periodo = explode("-", $this->dataSistema)[0]."-".explode("-", $this->dataSistema)[1];

      $contas = $this->selectArray("contas_bancarias", [], ["idPContaFinanceira"=>$idConta, "idContaEscola"=>$_SESSION['idEscolaLogada']]);

      $valorFinalDia = floatval(valorArray(listarItensObjecto($contas, $periodo, ["data=".$this->dataSistema]), $accao));

      if($accao=="C"){
        $accaoOposta="D";
      }else if($accao=="D"){
        $accaoOposta="C";
      }

      $valorFinalDiaAccaoOposta = floatval(valorArray(listarItensObjecto($contas, $periodo, ["data=".$this->dataSistema]), $accaoOposta));

      $this->editar("contas_bancarias", $accao, [floatval(valorArray($contas, $accao))+floatval($valor)], ["idPContaFinanceira"=>$idConta, "idContaEscola"=>$_SESSION['idEscolaLogada']]);

      $this->excluirItemObjecto("contas_bancarias", $periodo, ["idPContaFinanceira"=>$idConta, "idContaEscola"=>$_SESSION['idEscolaLogada']], ["data"=>$this->dataSistema]);

      $this->inserirObjecto("contas_bancarias", $periodo, "idPPerido", "data, ".$accao.", ".$accaoOposta, [$this->dataSistema, ($valorFinalDia+$valor), $valorFinalDiaAccaoOposta], ["idPContaFinanceira"=>$idConta, "idContaEscola"=>$_SESSION['idEscolaLogada']]);
    }

    private function dbUsar($coleccao)
    {
      if($coleccao == "agrup_alunos" || (count(explode("_", $coleccao))>1 && explode("_", $coleccao)[0] == "alunos"))
        $this->conDb("grupo_alunos");
      else
        $this->conDb($this->tipoBaseDados, $this->cumprimentoObrigatorio);
    }

    public function upload($arquivo, $nomeArquivo, $caminhoDir, $caminhoRetornar, $nomeAnterior="", $larguraImagem=200, $alturaImagem=300){

      $retorno=$nomeAnterior;
      if(isset($_FILES[$arquivo]) && $_FILES[$arquivo]['size'] > 0){
        // Verifica se o upload foi enviado via POST
        if(is_uploaded_file($_FILES[$arquivo]['tmp_name'])){

          $cDirs = explode("/", $caminhoDir);


          for($i=0; $i<=(count($cDirs)-1); $i++){
            $caminhoVerif=$caminhoRetornar;

            for($t=0; $t<=$i; $t++){
                $caminhoVerif .=$cDirs[$t]."/";
            }
            if(!file_exists($caminhoVerif)){
                mkdir($caminhoVerif);
            }
          }

          $extensao = pathinfo($_FILES[$arquivo]["name"], PATHINFO_EXTENSION);
          $nomeArquivo .= ".".strtolower($extensao);

          if((strtolower($extensao)=="png" || strtolower($extensao)=="jpg" || strtolower($extensao)=="jpeg") && $larguraImagem!="" && $alturaImagem!="" && $_FILES[$arquivo]['size']>50000){

            $this->redimensionarImagem($arquivo, $nomeArquivo, $caminhoRetornar.$caminhoDir, $larguraImagem, $alturaImagem);
            $retorno=$nomeArquivo;
          }else{
            // Essa função move_uploaded_file() copia e verifica se o arquivo enviado foi copiado com sucesso para o destino
            if (move_uploaded_file($_FILES[$arquivo]['tmp_name'], $caminhoRetornar.$caminhoDir."/".$nomeArquivo)){
              $retorno=$nomeArquivo;
            }else{
              $retorno="";
            }
          }

        }
      }
      return $retorno;
    }

    private function redimensionarImagem($arquivo, $nomeArquivo, $caminho, $largura, $altura){

      $extensao = pathinfo($_FILES[$arquivo]["name"], PATHINFO_EXTENSION);

      if($extensao=="png" || $extensao=="PNG"){
        $imagem_temporaria = imagecreatefrompng($_FILES[$arquivo]['tmp_name']);
      }else{
        $imagem_temporaria = imagecreatefromjpeg($_FILES[$arquivo]['tmp_name']);
      }

      $largura_original = imagesx($imagem_temporaria);
      $altura_original = imagesy($imagem_temporaria);

      $nova_largura = $largura? $largura : floor (($largura_original / $altura_original) * $altura);

      $nova_altura = $altura ? $altura : floor (($altura_original / $largura_original) * $largura);

      $imagem_redimensionada = imagecreatetruecolor($nova_largura, $nova_altura);
      imagecopyresampled($imagem_redimensionada, $imagem_temporaria, 0, 0, 0, 0, $nova_largura, $nova_altura, $largura_original, $altura_original);

      if($extensao=="png" || $extensao=="JPG"){
        imagepng($imagem_redimensionada, $caminho.'/'.$nomeArquivo);
      }else{
        imagejpeg($imagem_redimensionada, $caminho.'/'.$nomeArquivo);
      }
    }
  }
?>
