<?php 
  
  use GuzzleHttp\Client;
  use GuzzleHttp\RequestOptions;

  if(session_status()!==PHP_SESSION_ACTIVE){
    session_cache_expire(60);
    session_start();
  }
  
  if(isset($_GET["termSessao"])){
    terminarSessao();
  }

  function seInteiro($valor){
   return preg_match('/^[1-9][0-9]*$/', $valor);
  }
  function luzl($valor){
    if($valor=="NULL"){
      return "";
    }else if(is_numeric($valor)){
      if(seInteiro($valor)){
        return (int)$valor;
      }else{
        return (double)$valor;
      }
    }else{
      return $valor;
    }
  }

  function organizarArray($array){
    $arrayFinal =array();
    foreach($array as $a){
      $arrayFinal[]=$a;
    }
    return $arrayFinal;
  }

  function retornarChaves($array){
    $chaves =array();
    $angola = isset($array[0])?json_encode($array[0]):json_encode($array);
    $i=0;
    foreach(explode("],", $angola) as $c){
     
      $matondo = str_replace('"', "", explode('":[', $c)[0]);

      $divine = explode(",", $matondo);
      foreach($divine as $div){
        $chave = str_replace("{", "", explode(":", $div)[0]);
        $chaves[]=$chave;
        $i++;
      }
    }
    return $chaves;
  }

  function retornarObjectos($array){
    $chaves =array();
    $angola = isset($array[0])?json_encode($array[0]):json_encode($array);
    $i=0;
    foreach(explode(',"', $angola) as $c){
      $mpanzu=explode('":[', $c);
      if(count($mpanzu)>1){
        $chaves[]=$mpanzu[0];
      }
    }
    return $chaves;
  }


  function abrirSessao(){
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
  }
  
    function bianca($verf, $valor){
        if($verf==""){
            return $valor;
        }else{
            return $verf;
        }
    }

  function periodoExtenso($p){
    if($p=="pos"){
      return "Pós-Laboral";
    }else{
      return "Regular";
    }
  }
  
  function calcularDiferencaEntreDatas($data1, $data2){
    $diferenca =  strtotime($data1) - strtotime($data2);
      return floor($diferenca / (60 * 60 * 24));
  }

  function nelson($array, $index, $objecto=""){
    if(isset($array->$index)){
      return $array->$index;
    }else if(isset($array[$index])){
      return $array[$index];
    }else if(isset($array[$objecto]->$index)){
      return $array[$objecto]->$index;
    }else if(isset($array[$objecto][$index])){
      return $array[$objecto][$index];
    }else{
      return "";
    }
  }

  function valor($valor){
    if(isset($valor)){
      return $valor;
    }else{
      return "";
    }
  }

  function seTemValorNoArray($array, $valor){
    $retorno=false;
    foreach($array as $a){
      if($a==$valor){
        $retorno=true;
        break;
      }
    }
    return $retorno;
  }
  function tipoDisciplina($disc){
      if($disc=="FG"){
          $tp="Formação Geral";
      }else if($disc=="FP"){
          $tp="Formação Profissional";
      }else if($disc=="FE"){
          $tp="Formação Específica";
      }else if($disc=="Op"){
          $tp="Opção";
      }else if($disc=="CSC"){
          $tp="Componente Sócio-Cultural";
      }else if($disc=="CC"){
          $tp="Componente Científica";
      }else if($disc=="CTTP"){
          $tp="Componente Técnica, Tecnológica e Prática";
      }else{
        $tp = $disc;
      }
      return $tp;
  }
  function tipoDisciplina2($disc){
      if($disc=="FG"){
          $tp="F. Geral";
      }else if($disc=="FP"){
          $tp="F. Profissional";
      }else if($disc=="FE"){
          $tp="F. Específica";
      }else if($disc=="Op"){
          $tp="Opção";
      }else if($disc=="CSC"){
          $tp="C. Sócio-Cultural";
      }else if($disc=="CC"){
          $tp="C. Científica";
      }else if($disc=="CTTP"){
          $tp="C. Técn., Tecnol. e Prát.";
      }else{
        $tp = $disc;
      }
      return $tp;
  }
  function limpadorEspacosDuplicados($dado){
    $array = explode("  ", $dado);
    $pedrito="";
    foreach($array as $a){
      if(trim($a)!=""){
        if($pedrito==""){
          $pedrito=trim($a);
        }else{
          $pedrito.=" ".trim($a);
        }
      }
    }
    return $pedrito;
  }

  function seTudoMaiuscula($dado){
    $array = explode(" ", $dado);
    
    $retorno =false;
    foreach($array as $a){
      if(trim($a)!=""){
        if(ctype_upper($a)){
          $retorno=true;
          break;
        }
      }
    }
    return $retorno;
  }

  

  function seComparador($valor="", $valorDb="", $comparador="="){
    $retorno=false;
    $valor = strval($valor);
    if($valor=="TOT"){
        $retorno=true;
    }else{
      if($comparador=="="){
         if($valor==$valorDb){
          $retorno=true;
         }else{
          $retorno=false;
         }
      }else if($comparador=="<"){
         if($valor<$valorDb){
          $retorno=true;
         }else{
          $retorno=false;
         }
      }else if($comparador=="<="){
         if($valor<=$valorDb){
          $retorno=true;
         }else{
          $retorno=false; 
         }
      }else if($comparador==">"){
         if($valor>$valorDb){
          $retorno=true;
         }else{
          $retorno=false;
         }
      }else if($comparador==">="){
         if($valor>=$valorDb){
          $retorno=true;
         }else{
          $retorno=false;
         }
      }else if($comparador=="!="){
         if($valor!=$valorDb){
          $retorno=true;
         }else{
          $retorno=false;
         }
      }
    }
    return $retorno;
  }

  function classeExtensa($manipulacaoDados, $idPCurso, $classe){

      $array = $manipulacaoDados->selectArray("nomecursos", ["classes.designacao"], ["idPNomeCurso"=>$idPCurso, "classes.identificador"=>$classe], ["classes"], 1);
      return valorArray($array, "designacao", "classes");
  }

  function compararLinhaArray($linhaArray, $comparadores){
    $valor1=array();
    $sinal=array();
    $valor2=array();
    for($i=0; $i<=(count($comparadores)-1); $i++){

      if(strpos($comparadores[$i], ">=")>0){
        $operador=">=";
      }else if(strpos($comparadores[$i], "!=")>0){
        $operador="!=";
      }else if(strpos($comparadores[$i], "<=")>0){
        $operador="<=";
      }else if(strpos($comparadores[$i], "<")>0){
        $operador="<";
      }else if(strpos($comparadores[$i], ">")>0){
        $operador=">";
      }else{
        $operador="=";
      }
      $cabeleira = explode($operador, $comparadores[$i]);
      $valor1[$i]=$cabeleira[1];
      $sinal[$i]=$operador;
      $valor2[$i] = isset($linhaArray[$cabeleira[0]])?$linhaArray[$cabeleira[0]]:"ahasjashjshjasjaassa!!";

      if($operador==">=" || $operador=="<=" || $operador==">" || $operador=="<"){
        //Fazer trocar de valoress
        $ntonto = $valor1[$i];
        $valor1[$i]=$valor2[$i];
        $valor2[$i]=$ntonto;
      }
    }
    for($i=count($comparadores); $i<=10; $i++){
      $valor1[]="TOT";
      $sinal[]="=";
      $valor2[] = "";            
    }

    if(seComparador($valor1[0], $valor2[0], $sinal[0]) && seComparador($valor1[1], $valor2[1], $sinal[1]) && seComparador($valor1[2], $valor2[2], $sinal[2]) && seComparador($valor1[3], $valor2[3], $sinal[3]) && seComparador($valor1[4], $valor2[4], $sinal[4]) && seComparador($valor1[5], $valor2[5], $sinal[5]) && seComparador($valor1[6], $valor2[6], $sinal[6]) && seComparador($valor1[7], $valor2[7], $sinal[7]) && seComparador($valor1[8], $valor2[8], $sinal[8])){
      return true;
    }else{
      return false;
    }
  }

  function condicionadorArray($array, $condicoes){
    $arrayRetorno=array();
    foreach($array as $ar){
      if(compararLinhaArray($ar, $condicoes)){
        $arrayRetorno[]=$ar;
      }
    }
    return $arrayRetorno;
  }

  function includar($caminhoRetornar="", $directorioEmExecucao=""){
   
    curtina($_SERVER['DOCUMENT_ROOT'].'/angoschool/janelasMensagens.php');
    curtina($_SERVER['DOCUMENT_ROOT'].'/angoschool/verificadorAcesso.php');
    curtina($_SERVER['DOCUMENT_ROOT'].'/angoschool/'.directorioEmExecucao($directorioEmExecucao).'/layouts.php');
     curtina($_SERVER['DOCUMENT_ROOT'].'/angoschool/'.directorioEmExecucao($directorioEmExecucao).'/conexaoFolhas.php');
    curtina($_SERVER['DOCUMENT_ROOT'].'/angoschool/'.directorioEmExecucao($directorioEmExecucao).'/htmls.php');
    curtina($_SERVER['DOCUMENT_ROOT'].'/angoschool/'.directorioEmExecucao($directorioEmExecucao).'/manipulacaoDados.php');
  }
  function curtina($arquivo){
    if(file_exists($arquivo)){
        include_once ($arquivo);
    }
  }

  function directorioEmExecucao($directorioEmExecucao=""){
    if($directorioEmExecucao!=""){
      return $directorioEmExecucao;
    }else if(isset($_SESSION["tipoUsuario"])){
      if($_SESSION['tipoUsuario']=="direccaoP"){
        $_SESSION['directorioEmExecucao']="areaDireccaoProvincial";
      }else if($_SESSION['tipoUsuario']=="administrador"){
        $_SESSION['directorioEmExecucao']="areaAdministrador";
      }else{
        $_SESSION['directorioEmExecucao']="areaEscolas";
      }
    }else{
      $_SESSION['directorioEmExecucao']="areaEscolas";
    }
    return $_SESSION['directorioEmExecucao'];
  }

    function abreviarNomeEscola($escola=""){
      return ""; 
    }

    function carregarHeaderAutomaticamente($db, $caminhoRetornar, $verificadorAcesso){

        if($_SESSION["tipoUsuario"]=="professor" || $_SESSION["tipoUsuario"]=="aluno"){
          curtina($_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/layouts.php');
        }else if($_SESSION["tipoUsuario"]=="administrador"){
          curtina($_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/layouts.php');
        }else if($_SESSION["tipoUsuario"]=="direccaoP"){
          curtina($_SERVER['DOCUMENT_ROOT'].'/angoschool/areaDireccaoProvincial/header.php');
        }
        $layouts = new layouts();
        //$layouts->cabecalho();
    }

  function retornarCaminhoRecuarArquivosPhp($caminhoAbsoluto){
    abrirSessao();
    $caminho = explode("/", $caminhoAbsoluto);
    $caminhoRetornar = "";
    for($i=1; $i<=(count($caminho)-(count(explode("/", $_SERVER['DOCUMENT_ROOT']))+1)); $i++){
      $caminhoRetornar .="../";
    } 
    return $caminhoRecuar;
  }

  function seDirectorEscola(){
      curtina($_SERVER['DOCUMENT_ROOT'].'/angoschool/manipulacaoDadosMae');
      $manipulacaoDados = new manipulacaoDadosMae(); 

      if(valorArray($manipulacaoDados->sobreUsuarioLogado, "nivelSistemaEntidade", "escola")=="Director" || valorArray($manipulacaoDados->sobreUsuarioLogado, "nivelSistemaEntidade", "escola")=="Pedagógico" || valorArray($manipulacaoDados->sobreUsuarioLogado, "nivelSistemaEntidade", "escola")=="Administrativo" || valorArray($manipulacaoDados->sobreUsuarioLogado, "nivelSistemaEntidade", "escola")=="Usuário_Master" || valorArray($manipulacaoDados->sobreUsuarioLogado, "nivelSistemaEntidade", "escola")=="Acessório_Promotor" || valorArray($manipulacaoDados->sobreUsuarioLogado, "nivelSistemaEntidade", "escola")=="Promotor" || valorArray($manipulacaoDados->sobreUsuarioLogado, "nivelSistemaEntidade", "escola")=="Promotor_Adjunto"){
        return true;
      }else{
        return false;
      }
    }
    function seOficialEscolar(){
      curtina($_SERVER['DOCUMENT_ROOT'].'/angoschool/manipulacaoDadosMae');
      $manipulacaoDados = new manipulacaoDadosMae();
      if(valorArray($manipulacaoDados->sobreUsuarioLogado, "nivelSistemaEntidade", "escola")!=4){
        return true;
      }else{
        return false;
      }
    }

    function seNaoAluno(){
      curtina($_SERVER['DOCUMENT_ROOT'].'/angoschool/manipulacaoDadosMae');
      $manipulacaoDados = new manipulacaoDadosMae();
      if(valorArray($manipulacaoDados->sobreUsuarioLogado, "nivelSistemaEntidade", "escola")!="Aluno"){
        return true;
      }else{
        return false;
      }
    }
    function seAluno(){
      curtina($_SERVER['DOCUMENT_ROOT'].'/angoschool/manipulacaoDadosMae');
      $manipulacaoDados = new manipulacaoDadosMae();
      if(valorArray($manipulacaoDados->sobreUsuarioLogado, "nivelSistemaEntidade", "escola")=="Aluno"){
        return true;
      }else{
        return false;
      }
    }
    function seProfessor(){
      curtina($_SERVER['DOCUMENT_ROOT'].'/angoschool/manipulacaoDadosMae');
      $manipulacaoDados = new manipulacaoDadosMae();
      if(valorArray($manipulacaoDados->sobreUsuarioLogado, "nivelSistemaEntidade", "escola")=="Director" || valorArray($manipulacaoDados->sobreUsuarioLogado, "nivelSistemaEntidade", "escola")=="Pedagógico" || valorArray($manipulacaoDados->sobreUsuarioLogado, "nivelSistemaEntidade", "escola")=="Administrativo" || valorArray($manipulacaoDados->sobreUsuarioLogado, "nivelSistemaEntidade", "escola")=="Usuário_Master" || valorArray($manipulacaoDados->sobreUsuarioLogado, "nivelSistemaEntidade", "escola")=="Professor"  || valorArray($manipulacaoDados->sobreUsuarioLogado, "nivelSistemaEntidade", "escola")=="Chefe_da_Secretaria" || valorArray($manipulacaoDados->sobreUsuarioLogado, "nivelSistemaEntidade", "escola")=="Secretário_Pedagógico" || valorArray($manipulacaoDados->sobreUsuarioLogado, "nivelSistemaEntidade", "escola")=="Acessório_Pedagógico" || valorArray($manipulacaoDados->sobreUsuarioLogado, "nivelSistemaEntidade", "escola")=="Acessório_Promotor" || valorArray($manipulacaoDados->sobreUsuarioLogado, "nivelSistemaEntidade", "escola")=="Promotor" || valorArray($manipulacaoDados->sobreUsuarioLogado, "nivelSistemaEntidade", "escola")=="Promotor_Adjunto" || valorArray($manipulacaoDados->sobreUsuarioLogado, "nivelSistemaEntidade", "escola")=="Secretário_Administrativo" || valorArray($manipulacaoDados->sobreUsuarioLogado, "nivelSistemaEntidade", "escola")=="Acessório_Administrativo"){
        return true;
      }else{
        return false;
      }
    }
    function seProgramador(){
      curtina($_SERVER['DOCUMENT_ROOT'].'/angoschool/manipulacaoDadosMae');
      $manipulacaoDados = new manipulacaoDadosMae();
      if(valorArray($manipulacaoDados->sobreUsuarioLogado, "nivelSistemaEntidade", "escola")=="CEO" || valorArray($manipulacaoDados->sobreUsuarioLogado, "nivelSistemaEntidade", "escola")=="PCA" || valorArray($manipulacaoDados->sobreUsuarioLogado, "nivelSistemaEntidade", "escola")=="CFO" || valorArray($manipulacaoDados->sobreUsuarioLogado, "nivelSistemaEntidade", "escola")=="CTO" || valorArray($manipulacaoDados->sobreUsuarioLogado, "nivelSistemaEntidade", "escola")=="Admin"){
        return true;
      }else{
        return false;
      }
    }

    function seFuncionarioRepart(){
      curtina($_SERVER['DOCUMENT_ROOT'].'/angoschool/manipulacaoDadosMae');
      $manipulacaoDados = new manipulacaoDadosMae();
        if(valorArray($manipulacaoDados->sobreUsuarioLogado, "nivelSistemaEntidade", "escola")=="Director Municipal"){
          return true;
        }else{
          return false;
        }
    }

    function seFuncionarioDirProv(){
      curtina($_SERVER['DOCUMENT_ROOT'].'/angoschool/manipulacaoDadosMae');
      $manipulacaoDados = new manipulacaoDadosMae();
        if(valorArray($manipulacaoDados->sobreUsuarioLogado, "nivelSistemaEntidade", "escola")=="Director Provincial" || valorArray($manipulacaoDados->sobreUsuarioLogado, "nivelSistemaEntidade", "escola")=="Director Municipal"){
          return true;
        }else{
          return false;
        }
    } 

  function umaOuDuasBarras(){
    return "/";
  }

  function enviarSMS($telefone, $mensagem){

    $curl = curl_init();
    curl_setopt_array($curl, [
    CURLOPT_URL => 'https://appsms.okulanda.ao/forward',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => ['ReciverNumbers' =>$telefone,'TextSms' =>kitchula($mensagem),'CEspecial' => '1'],
    CURLOPT_HTTPHEADER => ['AuthNIF: 5001148583','RemeteKey: 9ORDH9WYVOME2I21QWAS94B6O1B7B958']
    ]);
    $response = curl_exec($curl);
    curl_close($curl);

  }
  
  function smtpmailer($to, $from, $from_name, $subject, $body, $password="Renapol1.."){
    include_once ($_SERVER['DOCUMENT_ROOT']."/angoschool/bibliotecas/PHPMailer/PHPMailerAutoload.php");

    $mail = new PHPMailer(true);
    $mail->IsSMTP();

    $mail->Charset = 'UTF-8';
    
    //Configurações 
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = 'ssl';
    
    //nome do servidor
    $mail->Host = 'smtp.titan.email';
    //Porta de saida de e-mail 
    $mail->Port = 465; 
    
    //Dados do e-mail de saida - autenticação
    $mail->Username = $from;
    $mail->Password = $password;
    $mail->isHTML(true);
    $mail->From = $from;
    $mail->FromName = $from_name;
    $mail->Sender = $from;
    $mail->AddReplyTO($from, $from_name);
    $mail->Subject = utf8_decode($subject);
    $mail->AddAddress($to);
    $mail->Body = $body;

    //Destinatario 
    //other stuff
    if($mail->Send()){
      return true;
    }else{
      return false;
    }

  }

  function utf81($valor){
    return utf8_encode($valor);    
  }

  function utf82($valor){
    return $valor;
  }
  function utf8Enc($valor){
    //Esta é a função antiga de tratar caracteres, praticamente já não faz nada.
    return $valor; 
  }
    
  function descodificarCampos($array, $tabelasSql, $manipulacaoDados){
     $atributosTabela = array();
      $tabelas=array();
      $tabelasCond = explode(strtolower("LEFT JOIN"), strtolower($tabelasSql));
      foreach($tabelasCond as $tabCond){
          $tabelas[] = explode(" ", trim($tabCond))[0];
      }

    foreach ($array as $valor) {
      
      foreach($tabelas as $tabela){

        foreach($manipulacaoDados->selectSimple("DESC ".$tabela) as $sel){
            $campo = $sel->Field;
            if(isset($valor->$campo) && $valor->$campo!=NULL && $valor->$campo!=null){
                $valor->$campo =utf81($valor->$campo);
            }
            
        }

      }
    }
    return $array;
  }



  function terminarSessao(){
    abrirSessao();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/manipulacaoDadosMae.php';
    $manipulacaoDados = new manipulacaoDadosMae(__DIR__);

    if(isset($_SESSION['idPOnline'])){
      $manipulacaoDados->editar("entidadesonline", "estadoExpulsao", ["I"], ["idPOnline"=>$_SESSION['idPOnline']]);
    }
    session_destroy();
    session_unset();
    echo "<script>window.location='".$manipulacaoDados->enderecoSite."'</script>";
  }

  function retornarNacionalidade($pais){
    if($pais=="Angola"){
      return "Angolana";
    }else if($pais=="Vietnam"){
      return "Vietnamita";
    }else if($pais=="África do Sul"){
      return "Sul Africano";
    }else{
      return $pais;
    }
  }
  function criptografarDado($valor, $campo=""){
    return "Xy=".criptografarBase64(utf81($valor))."=Y";
  }
  function criptografarDadoSemPrefixos($valor, $campo=""){
    return criptografarBase64($valor);
  }
  function stand_up($valor, $campo=""){
    return str_replace("..h", "", desencriptografarBase64(substr($valor, 3, strlen($valor)-5)));
  }  

  function tratarCamposVaziosComEComercial($campo, $numero){
    $retorno="";
    if($campo=="" || $campo==NULL){
      for($i=0; $i<=$numero; $i++){
        $retorno .="&";
      }
      return $retorno;
    }else{
      return $campo;
    }

    
  }

  function converterUtf8GoDb($valor){
    if(is_string($valor)){
      return utf8_decode($valor);
    }else{
      return $valor;
    }
  }
  function valorArray($array, $campo, $objecto=""){
    if(is_string($campo)){
      $campo= trim($campo);
    }

    $retorno="";
    if(is_array($array)){
      if(isset($array[0])){
        $retorno= nelson($array[0], $campo, $objecto);  
      }else{
        $retorno= nelson($array, $campo, $objecto);
      }
    }else{
      $retorno= nelson($array, $campo, $objecto);
    }
    return $retorno;
  }

  function generoExtenso($genero){
    if($genero=="M"){
      return "Masculino";
    }else{
      return "Feminino";
    }
  }

  function completarNumero($n){
    if($n==0){
      return "00";
    }else if($n==null || $n==""){
      return "";
    }else if($n<=9){
      return "0".(int)$n;
    }else{
      return $n;
    }
  }

  function abreviarDoisNomes($disciplina=""){
      $disciplinas = explode(" ", $disciplina);
      $i=0;
      $abrev="";
      foreach ($disciplinas as $disc) {
          $i++;
          if(count($disciplinas)<=2){
            return $disciplina;
          }else{
            if($i==1){
              $abrev =$disc." ";
            }else if($i<count($disciplinas)){
                if(strlen($disc)>3){
                    $abrev =$abrev." ".mb_substr($disc, 0, 1).". ";
                }else{
                  $abrev .= $disc." ";
                }
            }else if($i==count($disciplinas)){
                $abrev .=$disc;
            }
          }
      }
      return $abrev;
  }
  function abreviarUmNome($disciplina){
    $disciplinas = explode(" ", $disciplina);
      $i=0;
      $abrev="";
      foreach ($disciplinas as $disc) {
          $i++;
          if(count($disciplinas)<=1){
            return $disciplina;
          }else{
            if($i<count($disciplinas)){
                if(strlen($disc)>3){
                    $abrev =$abrev." ".substr($disc, 0, 1).". ";
                }else{
                  $abrev .= $disc." ";
                }
            }else if($i==count($disciplinas)){
                $abrev .=$disc;
            }
          }
      }
      return $abrev;
  }

  function abreviarPorNumeroDeLetras($str, $numeroLetras){
    if(strlen($str)>$numeroLetras){
        return substr($str, 0, $numeroLetras).".";
    }else{
      return $str;
    }
  }

  function primeiraLetraMaiuscula($texto){
      $letra1 = substr($texto, 0, 1);
      return strtoupper($letra1).substr($texto, 1, strlen($texto));

  }

  

  function primeirasLetrasDaPalavraMaiuscula($texto){
      $vetor = explode(" ", $texto);
      $retorno ="";

      foreach ($vetor as $vet) {
        if($retorno!=""){
          $retorno .=" ";
        }
        if(strlen($vet)>=3){
            $retorno .=ucfirst($vet);
        }else{
          $retorno .= $vet;
        }
      }
      return $retorno;
  }
  
    
   
    function nomeMes($mes){
      $retorno="";
      if($mes==1){
        $retorno="Janeiro";
      }else if($mes==2){
        $retorno="Fevereiro";
      }else if($mes==3){
        $retorno="Março";
      }else if($mes==4){
        $retorno="Abril";
      }else if($mes==5){
        $retorno="Maio";
      }else if($mes==6){
        $retorno="Junho";
      }else if($mes==7){
        $retorno="Julho";
      }else if($mes==8){
        $retorno="Agosto";
      }else if($mes==9){
        $retorno="Setembro";
      }else if($mes==10){
        $retorno="Outubro";
      }else if($mes==11){
        $retorno="Novembro";
      }else if($mes==12){
        $retorno="Dezembro";
      }
      return $retorno;
  }
  function nomeMes2($mes){
      $retorno="";
      if($mes==1){
        $retorno="Jan.";
      }else if($mes==2){
        $retorno="Fev.";
      }else if($mes==3){
        $retorno="Mar.";
      }else if($mes==4){
        $retorno="Abr.";
      }else if($mes==5){
        $retorno="Maio";
      }else if($mes==6){
        $retorno="Junho";
      }else if($mes==7){
        $retorno="Julho";
      }else if($mes==8){
        $retorno="Ago.";
      }else if($mes==9){
        $retorno="Set.";
      }else if($mes==10){
        $retorno="Out.";
      }else if($mes==11){
        $retorno="Nov.";
      }else if($mes==12){
        $retorno="Dez.";
      }
      return $retorno;
  }

  function converterData($data){
    if($data==null  || $data==""){
      return "";
    }else{
      $dataEx = explode("-", $data);
      return $dataEx[2]."/".$dataEx[1]."/".$dataEx[0];
    }
  }

  function calcularIdade($ano, $data){

    if($data==null  || $data==""){
      return "";
    }else{
      $dataEx = explode("-", $data);
      return  (intval($ano) - intval($dataEx[0]));
    }
  }

  function dataExtensa($data){
    if($data==null  || $data=="" || $data=="0000-00-00"){
      return "";
    }else{
      $dataEx = explode("-", $data);
      if(count($dataEx)==3){
        return $dataEx[2]." de ".nomeMes($dataEx[1])." de ".$dataEx[0];
      }else{
        return "";
      }
    }
  }

   function diaSemana ($dia){
      if($dia==1){
        return "Segunda-Feira";
      }else if($dia==2){
        return "Terça-Feira";
      }else if($dia==3){
        return "Quarta-Feira";
      }else if($dia==4){
        return "Quinta-Feira";
      }else if($dia==5){
        return "Sexta-Feira";
      }else if($dia==6){
        return "Sábado";
      }else if($dia==0){
        return "Domingo";
      }
   }
   function diaSemana2 ($dia){
      if($dia==1){
        return "2.ª Feira";
      }else if($dia==2){
        return "3.ª Feira";
      }else if($dia==3){
        return "4.ª Feira";
      }else if($dia==4){
        return "5.ª Feira";
      }else if($dia==5){
        return "6.ª Feira";
      }else if($dia==6){
        return "Sábado";
      }else if($dia==0){
        return "Domingo";
      }
   }

   function diaSemana3($dia){
      if($dia==1){
        return "2.ª F";
      }else if($dia==2){
        return "3.ª F";
      }else if($dia==3){
        return "4.ª F";
      }else if($dia==4){
        return "5.ª F";
      }else if($dia==5){
        return "6.ª F";
      }else if($dia==6){
        return "Sáb.";
      }else if($dia==0){
        return "Domingo";
      }
   }
   function diaSemana4($dia){
      if($dia==1){
        return "2.ª";
      }else if($dia==2){
        return "3.ª";
      }else if($dia==3){
        return "4.ª";
      }else if($dia==4){
        return "5.ª";
      }else if($dia==5){
        return "6.ª";
      }else if($dia==6){
        return "S";
      }else if($dia==0){
        return "DOM";
      }
   }

  function ordenar($array, $ordenacao=""){
    $arrayFinal =array();
    foreach($array as $a){
      $arrayFinal[]=$a;
    }

    $_SESSION["ordemArray"]=$ordenacao;
    if($ordenacao!=""){
      usort($arrayFinal, 'ordenarArrayManualmente');
    }  
    return $arrayFinal;
  }
  function usortTest($a, $b) {
      return $a>$b;
  }

  function Utf8_ansi($valor='') {
    $utf8_ansi2 = array(
    "\u00c0" =>"À",
    "\u00c1" =>"Á",
    "\u00c2" =>"Â",
    "\u00c3" =>"Ã",
    "\u00c4" =>"Ä",
    "\u00c5" =>"Å",
    "\u00c6" =>"Æ",
    "\u00c7" =>"Ç",
    "\u00c8" =>"È",
    "\u00c9" =>"É",
    "\u00ca" =>"Ê",
    "\u00cb" =>"Ë",
    "\u00cc" =>"Ì",
    "\u00cd" =>"Í",
    "\u00ce" =>"Î",
    "\u00cf" =>"Ï",
    "\u00d1" =>"Ñ",
    "\u00d2" =>"Ò",
    "\u00d3" =>"Ó",
    "\u00d4" =>"Ô",
    "\u00d5" =>"Õ",
    "\u00d6" =>"Ö",
    "\u00d8" =>"Ø",
    "\u00d9" =>"Ù",
    "\u00da" =>"Ú",
    "\u00db" =>"Û",
    "\u00dc" =>"Ü",
    "\u00dd" =>"Ý",
    "\u00df" =>"ß",
    "\u00e0" =>"à",
    "\u00e1" =>"á",
    "\u00e2" =>"â",
    "\u00e3" =>"ã",
    "\u00e4" =>"ä",
    "\u00e5" =>"å",
    "\u00e6" =>"æ",
    "\u00e7" =>"ç",
    "\u00e8" =>"è",
    "\u00e9" =>"é",
    "\u00ea" =>"ê",
    "\u00eb" =>"ë",
    "\u00ec" =>"ì",
    "\u00ed" =>"í",
    "\u00ee" =>"î",
    "\u00ef" =>"ï",
    "\u00f0" =>"ð",
    "\u00f1" =>"ñ",
    "\u00f2" =>"ò",
    "\u00f3" =>"ó",
    "\u00f4" =>"ô",
    "\u00f5" =>"õ",
    "\u00f6" =>"ö",
    "\u00f8" =>"ø",
    "\u00f9" =>"ù",
    "\u00fa" =>"ú",
    "\u00fb" =>"û",
    "\u00fc" =>"ü",
    "\u00fd" =>"ý",
    "\u00ff" =>"ÿ",
    "\u00ba" =>"º"); 
    return strtr($valor, $utf8_ansi2);      
  }

  function zipador($array, $conjuntoTabelas, $anexarPai="sim"){
    //Reforçao as condições de listagem de um registo....
    $arrayRetorno=array();

    $contador=0;
    foreach($array as $a){
      if($anexarPai=="sim"){
          $mamaPolina=$a;
      }
      foreach($conjuntoTabelas as $conjunto){

        $tabelEncontrada="nao";
        if(isset($a[$conjunto[0]])){
          foreach($a[$conjunto[0]] as $tabela){
            $chaveTabela = retornarChaves($tabela);
            if(compararLinhaArray($tabela, $conjunto[1])){
              foreach($chaveTabela as $chave){
                if(isset($tabela[$chave]) && !is_object($tabela[$chave])){
                   $tabelEncontrada="sim";
                  $mamaPolina[$chave]=$tabela[$chave];
                }
              }
              break;
            }
          }
        }
        if($tabelEncontrada=="nao"){
          break;
        }
      }
      if($tabelEncontrada=="sim"){
        $arrayRetorno[$contador]=$mamaPolina;
        $contador++;
      }     
    }
    return $arrayRetorno;
  }



  function listarItensObjecto($linhaArray, $objecto, $condicoes=array(), $anexarPai="nao", $ordenacao=""){
    //Lista Muitos Itens do objecto dum registo... 
    if(is_array($linhaArray)){
      $linhaArray = isset($linhaArray[0])?$linhaArray[0]:array();
    }    

    $contador=0;
    $arrayRetorno=array();
    if(isset($linhaArray[$objecto])){
      
      foreach($linhaArray[$objecto] as $obj){

        $tabelEncontrada="nao";
        $chaveTabela = retornarChaves($obj);
        $mamaPolina =array();
        
        if(compararLinhaArray($obj, $condicoes)){
          if($anexarPai=="sim"){
            foreach(retornarChaves($linhaArray) as $tony){
              if(isset($linhaArray[$tony]) && !is_object($linhaArray[$tony])){
                  $mamaPolina[trim($tony)]=$linhaArray[trim($tony)];
              }
            }
          }
          foreach($chaveTabela as $chave){
            if(isset($obj[$chave]) && !is_object($obj[$chave])){
              $mamaPolina[$chave]=$obj[$chave];
              $tabelEncontrada="sim";
            }
          }
        }

        if($tabelEncontrada=="sim"){
          $arrayRetorno[$contador]=$mamaPolina;
          $contador++;
        }
      }
    }
    if($ordenacao!=""){
      $arrayRetorno = ordenar($arrayRetorno, $ordenacao);
    }
    return $arrayRetorno;
  }

  function distinct($array){
    $arrayRetorno=array();
    foreach($array as $a){
      if(!seTemValorNoArray($arrayRetorno, $a)){
        $arrayRetorno[]=$a;
      }
    }
    return $arrayRetorno;
  }

  function distinct2($array, $campo, $objecto=""){
    $arrayRetorno=array();
    foreach($array as $a){
      if(valorArray($a, $campo, $objecto)!=""){
        if(!seTemValorNoArray($arrayRetorno, valorArray($a, $campo, $objecto))){
          $arrayRetorno[]=valorArray($a, $campo, $objecto);
        }
      }
    }
    return $arrayRetorno;
  }

  function ordenarArrayManualmente($a, $b) {    
    $retorno=false;
    if(isset($_SESSION["ordemArray"]) && $_SESSION["ordemArray"]!=""){

      $ordemArray = explode(",", $_SESSION["ordemArray"]);
      $continuarAvaliar=true;
      $i=0;

      while ($continuarAvaliar==true) {
        $ordem = trim($ordemArray[$i]);
        $operador = trim(substr($ordem, strlen($ordem)-4, strlen($ordem)-1));
        $campo = trim(substr($ordem, 0, strlen($ordem)-4));

        if(isset($a["reconfirmacoes"][$campo])){
          $valA = $a["reconfirmacoes"][$campo];
        }else if(isset($a["pautas"][$campo])){
          $valA = $a["pautas"][$campo];
        }else if(isset($a["alteracoes_notas"][$campo])){
          $valA = $a["alteracoes_notas"][$campo];
        }else if(isset($a["instituicoes"][$campo])){
          $valA = $a["instituicoes"][$campo];
        }else if(isset($a[$campo])){
          $valA = $a[$campo];
        }else if(isset($a->$campo)){
          $valA = $a->$campo;
        }else{
          $valA=":::";
        }

        $valA = strtoupper(kitchula($valA));

        if(isset($b["reconfirmacoes"][$campo])){
          $valB = $b["reconfirmacoes"][$campo];
        }else if(isset($b["pautas"][$campo])){
          $valB = $b["pautas"][$campo];
        }else if(isset($b["alteracoes_notas"][$campo])){
          $valB = $b["alteracoes_notas"][$campo];
        }else if(isset($b["instituicoes"][$campo])){
          $valB = $b["instituicoes"][$campo];
        }else if(isset($b->$campo)){
          $valB = $b->$campo;
        }else if(isset($b[$campo])){
          $valB = $b[$campo];
        }else{
          $valB=":::";
        }
        $valB = strtoupper(kitchula($valB));

        if($valA==$valB && $valA!=":::"){
          if(isset($ordemArray[$i+1]) && $ordemArray[$i+1]){
            //Avaliar campo seguinte...
            $i++;
          }else{
            $retorno=true;
            $continuarAvaliar=false;
          }
        }else{
          if($operador=="DESC" && $valA!=":::"){
            $retorno =$valA < $valB;
          }else if($valA){
            $retorno = $valA > $valB;
          }            
          $continuarAvaliar=false;
        }
      }
    } 
    return $retorno;
  }

  function kitchula($valor){
    $valor = str_replace("Ç", "C", $valor);
    $valor = str_replace("ç", "c", $valor);
    $valor = str_replace("Á", "A", $valor);
    $valor = str_replace("á", "a", $valor);
    $valor = str_replace("Ã", "A", $valor);
    $valor = str_replace("ã", "a", $valor);
    $valor = str_replace("À", "A", $valor);
    $valor = str_replace("à", "a", $valor);
    $valor = str_replace("Â", "A", $valor);
    $valor = str_replace("â", "a", $valor);
    $valor = str_replace("é", "e", $valor);
    $valor = str_replace("É", "E", $valor);
    $valor = str_replace("È", "E", $valor);
    $valor = str_replace("è", "e", $valor);
    $valor = str_replace("Ê", "E", $valor);
    $valor = str_replace("ê", "e", $valor);
    $valor = str_replace("ó", "o", $valor);
    $valor = str_replace("Ó", "O", $valor);
    $valor = str_replace("ò", "o", $valor);
    $valor = str_replace("Ò", "O", $valor);
    $valor = str_replace("Õ", "O", $valor);
    $valor = str_replace("õ", "o", $valor);
    $valor = str_replace("í", "i", $valor);
    $valor = str_replace("Í", "I", $valor);
    $valor = str_replace("ì", "i", $valor);
    $valor = str_replace("Ì", "I", $valor);
    $valor = str_replace("ú", "U", $valor);
    $valor = str_replace("Ú", "U", $valor);
    $valor = str_replace("ù", "U", $valor);
    $valor = str_replace("Ù", "U", $valor);
    return $valor;
  }

  function limitandoCamposDeArray($array){
    $retorno = $array;
    if(isset($_SESSION["limiteArray"]) && $_SESSION["limiteArray"]!=null && $_SESSION["limiteArray"]!=""){
      $retorno= array();
      for ($i=0; $i<=count($array)-1; $i++) {
          if($i<=$_SESSION["limiteArray"]-1){
              $retorno[] = $array[$i];
          }
      }
    }    
    return $retorno;
  }

  function calculadorAVG($array, $campo){
    $somaTotal=0;
    $campoTotal=0;
    foreach ($array as $ar) {
        $campoTotal++;
        $somaTotal +=(double)$ar->$campo;
    }
    if($campoTotal==0){
      return 0;
    }else{
      return ($somaTotal/$campoTotal);
    }
  }
  function calculadorSUM($array, $campo){
    $somaTotal=0;
    foreach ($array as $ar){
        $somaTotal +=(double)$ar->$campo;
    }
    return $somaTotal;
  }
  function calculadorMAX($array, $campo){
    $valorMaximo=0;
    foreach ($array as $ar){
      if($valorMaximo<$ar->campo){
        $valorMaximo = $ar->campo;
      }
    }
    return $valorMaximo;
  }
  function calculadorMIN($array, $campo){
    $valorMaximo=0;
    foreach ($array as $ar){
      if($valorMaximo>$ar->campo){
        $valorMaximo = $ar->campo;
      }
    }
    return $valorMaximo;
  }
  function criptografarMd5($texto){
    return md5($texto);
  }

  function atenticarMd5($passworDb, $password){
    if($passworDb=="0c7".md5($password)."ab"){
      return "sim";
    }else{
      return "nao";
    }
  }
    
    function criptografarBase64($texto){
      return base64_encode($texto);
    }

    function desencriptografarBase64($textoDb){
      return base64_decode($textoDb);
    }
    function fixObject (&$object)
      {
        if (!is_object ($object) && gettype ($object) == 'object')
          return ($object = unserialize (serialize ($object)));
        return $object;
    }

    function camposAnexar(){
      return ["idPAno", "numAno", "idPComuna", "nomeComuna", "preposicaoComuna", "preposicaoComuna2", "idPMunicipio", "nomeMunicipio", "preposicaoMunicipio", "preposicaoMunicipio2", "idPPais", "nomePais", "preposicaoPais", "preposicaoPais2", "idPProvincia", "nomeProvincia", "preposicaoProvincia", "preposicaoProvincia2", "idPEntidade", "nomeEntidade", "idPEscola", "nomeEscola", "abrevNomeEscola", "abrevNomeEscola2", "tituloEscola", "pais", "provincia", "municipio", "comuna", "privacidadeEscola", "idPNomeCurso", "nomeCurso", "abrevCurso", "areaFormacaoCurso", "tipoCurso", "sePorSemestre", "duracao", "idPNomeDisciplina", "nomeDisciplina", "abreviacaoDisciplina1", "abreviacaoDisciplina2", "idPMatricula", "nomeAluno"];
    }

    function plamedi($m, $condicoes=array(), $tabela, $tabelasFilha=array(), $retornar="yes"){
        $array = $m->selectArray($tabela, [], $condicoes, [], 1);
        
        $campos =["_id"]; 
      foreach(retornarChaves($array) as $c){
        if(!is_object($array[0][$c])){
          $campos[]=$c;
          if($retornar=="yes"){
            echo "'".$c."',";
          }
        }
      }
        
        foreach($tabelasFilha as $filha){
          $luzingu = listarItensObjecto($array[0], $filha);
          if(count($luzingu)>0){
          foreach(retornarChaves($luzingu[0]) as $chave){
            $campos[]=$filha.".".$chave;
            if($retornar=="yes"){
              echo "'".$filha.".".$chave."',";
            }
          }
        }       
        }
      }
?>