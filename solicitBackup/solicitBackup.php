<?php session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/manipulacaoDadosMae.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/funcoesAuxiliares.php';
    require_once ($_SERVER['DOCUMENT_ROOT']."/angoschool/bibliotecas/mongo/vendor/autoload.php");

    
    $afonsoluzingu = isset($_GET["afonsoluzingu"])?$_GET["afonsoluzingu"]:"";
    $_SESSION['idEscolaLogada']=$afonsoluzingu;
    if($afonsoluzingu==""){
        exit();
    }
    $msgErro="";

    try {
        $dbDestino = new manipulacaoDadosMae();
        $backup_name="backup_".$afonsoluzingu;
        $dbDestino->conexaoDb = new MongoDB\Client("mongodb://".stand_up("Xy=YWJpZy4uaGFlbDpSZW5hcG9sMS4uYWJpLi5oZ2FlbEA4OS4xMTcuNzIuNTE6MjcwMTk==Y"));
        $dbDestino->db = $dbDestino->conexaoDb->$backup_name;
        $dbDestino->actualizacaoDados = "on";
        $dbDestino->backup="nao";
        $dbDestino->serverAlteracao="online";
        $dbDestino->arquivarExclusoes="nao";

    }catch (MongoDB\Driver\Exception\ConnectionException $e) {
        $msgErro="FErro ao conectar com o MongoDB:".$e->getMessage();
    }catch (Exception $e) {
        $msgErro="FOcorreu um erro inesperado:".$e->getMessage();
    } 

    if($msgErro==""){
        try {
            $dbOrigem = new manipulacaoDadosMae();
            $dbOrigem->conexaoDb = new MongoDB\Client(stand_up("Xy=bW9uZ29kYitzcnY6Ly9heW5lemFudGEuLmhudGFtdToyeWpvMUhISU1xLi5oNHJ4N0d4QGNsdXN0ZXIwLjN0OS4uaHFweXgubW9uZ29kYi5uZXQv=Y"));
            if($afonsoluzingu==4){
                $dbOrigem->db = $dbOrigem->conexaoDb->teste;    
            }else{
                $dbOrigem->db = $dbOrigem->conexaoDb->escola;
            }            
            $dbOrigem->actualizacaoDados = "on";
            $dbOrigem->backup="";
            $dbOrigem->serverAlteracao="online";
            $dbOrigem->arquivarExclusoes="nao";
            if ($dbOrigem->selectArray("escolas", ["idPEscola"], [], [], 1)<=0) {
                $msgErro="FNão foi possível estabeler a conexão com a bese de dados.";
            }
        }catch (MongoDB\Driver\Exception\ConnectionException $e) {
            $msgErro="FErro ao conectar com o MongoDB: " . $e->getMessage();
        }catch (Exception $e) {
            $msgErro="FOcorreu um erro inesperado: " . $e->getMessage();
        }
    }
    
    if($msgErro!=""){
        echo $msgErro;
    }else{

        $condicionador="backup_".$dbOrigem->serverAlteracao."_".$afonsoluzingu;

        if(!isset($_SESSION['posColeccao'])){
            $_SESSION['posColeccao']=0;
        }

        if(!isset($_SESSION['coleccoes'])){

            $_SESSION['coleccoes']=array();

            foreach($dbOrigem->selectArray("agrup_alunos", ["grupo"]) as $grupo){
                $_SESSION['coleccoes'][]=array(
                "nome"=>"alunos_".$grupo["grupo"],
                "id"=>"idPMatricula", "condicionador"=>$condicionador);
            }

            foreach ($dbOrigem->db->listCollections() as $coleccao) {
                $nameColeccao1 = explode("_", $coleccao->getName())[0];
                $nameColeccao2 = isset(explode("_", $coleccao->getName())[1])?explode("_", $coleccao->getName())[1]:"";

                if(!($nameColeccao1=="alunos" && $nameColeccao2!="") && $nameColeccao1!="entidadesonline" && $nameColeccao1!="mensagens"){

                    $condicionador="backup_".$dbOrigem->serverAlteracao."_".$afonsoluzingu;

                    if($coleccao->getName()=="agrup_alunos" || $coleccao->getName()=="anolectivo" || $coleccao->getName()=="areas" || $coleccao->getName()=="cargos" || $coleccao->getName()=="div_terit_comunas" || $coleccao->getName()=="div_terit_municipios" || $coleccao->getName()=="div_terit_paises" || $coleccao->getName()=="div_terit_provincias" || $coleccao->getName()=="escolas" || $coleccao->getName()=="tipos_emolumentos"){
                        $condicionador="backup";
                    }
                    $_SESSION['coleccoes'][]=array(
                    "nome"=>$nameColeccao1,
                    "id"=>"idPMatricula", "condicionador"=>$condicionador);

                }
            }
        }

        $contador=0;

        foreach($dbOrigem->selectArray($_SESSION['coleccoes'][$_SESSION['posColeccao']]["nome"], [], [$_SESSION['coleccoes'][$_SESSION['posColeccao']]["condicionador"]=>"nao"], [], 1) as $tabela){

            $contChave=0;
            $idPrincipal="";
            $strinValores="";
            $valores=array();

            $contador++;

            foreach(retornarChaves($tabela) as $chave){

                if($chave!="_id" && isset($tabela[$chave]) && !is_object($tabela[$chave])){
                    $contChave++;

                    if($_SESSION['coleccoes'][$_SESSION['posColeccao']]["nome"]=="entidadesprimaria"){
                        $idPrincipal="idPEntidade";
                    }else if($contChave==1){
                        $idPrincipal=$chave;
                    }else{
                        if($strinValores!=""){
                            $strinValores .=",";
                        }
                        $strinValores .=$chave;
                        $valores[]=$tabela[$chave];
                    }

                    if($contChave==1){
                        if(count($dbDestino->selectArray($_SESSION['coleccoes'][$_SESSION['posColeccao']]["nome"], [$idPrincipal], [$idPrincipal=>$tabela[$idPrincipal]]))<=0){

                            $dbDestino->inserir($_SESSION['coleccoes'][$_SESSION['posColeccao']]["nome"], $idPrincipal, $idPrincipal, [$tabela[$idPrincipal]], "sim", "nao", [], $tabela[$idPrincipal]);
                        }
                    }

                }
            }

            foreach(retornarChaves($tabela) as $chave){

                if($chave!="_id" && isset($tabela[$chave]) && is_object($tabela[$chave])){
                    
                    foreach(listarItensObjecto($tabela, $chave, [$_SESSION['coleccoes'][$_SESSION['posColeccao']]["condicionador"]."=nao"]) as $itemActualizado){

                        $idPSubChave="";
                        $contSubChave=0;
                        $strings="";
                        $dados=array();
                        foreach(retornarChaves($itemActualizado) as $subChave){
                            $contSubChave++;
                            if($contSubChave==1){
                                $idPSubChave=$subChave;
                            }else if(isset($itemActualizado[$subChave])){
                                if($strings!=""){
                                    $strings .=",";
                                }
                                $strings .=$subChave;
                                $dados[]=$itemActualizado[$subChave];
                            }
                        }
                        $dbDestino->excluirItemObjecto($_SESSION['coleccoes'][$_SESSION['posColeccao']]["nome"], $chave, [$idPrincipal=>$tabela[$idPrincipal]], [$idPSubChave=>$itemActualizado[$idPSubChave]]);

                        $dbDestino->inserirObjecto($_SESSION['coleccoes'][$_SESSION['posColeccao']]["nome"], $chave, $idPSubChave, $strings, $dados, [$idPrincipal=>$tabela[$idPrincipal]], "sim", "nao", $itemActualizado[$idPSubChave]);

                        $dbOrigem->editarItemObjecto($_SESSION['coleccoes'][$_SESSION['posColeccao']]["nome"], $chave, $_SESSION['coleccoes'][$_SESSION['posColeccao']]["condicionador"], [""], [$idPrincipal=>$tabela[$idPrincipal]], [$idPSubChave=>$itemActualizado[$idPSubChave]]);
                    }
                }
            }
            if(valorArray($tabela, "backupGeral")=="nao"){
                $dbDestino->editar($_SESSION['coleccoes'][$_SESSION['posColeccao']]["nome"], $strinValores, $valores, [$idPrincipal=>$tabela[$idPrincipal]]);
            }
            $dbOrigem->editar($_SESSION['coleccoes'][$_SESSION['posColeccao']]["nome"], $_SESSION['coleccoes'][$_SESSION['posColeccao']]["condicionador"].", backupGeral", ["", ""], [$idPrincipal=>$tabela[$idPrincipal]]);
        }
        if($contador>0){
            echo "<script>window.location='?afonsoluzingu=".$afonsoluzingu."'</script>";        
        }else{
            if($_SESSION['posColeccao']<(count($_SESSION['coleccoes'])-1)){
                $_SESSION['posColeccao']++;
                echo "<script>window.location='?afonsoluzingu=".$afonsoluzingu."'</script>";
            }else{
                foreach($dbOrigem->selectArray("dados_excluidos") as $a){
                    $dbDestino->inserir("dados_excluidos", "idDExcl", "tabela, condicoes", [$a["tabela"], $a["condicoes"]]);
                }
                foreach($dbOrigem->selectArray("dados_excluidos2") as $a){
                    $dbDestino->inserir("dados_excluidos2", "idDExcl", "tabela, nomeObjecto, condicoesMae, condicoesFilha", [$a["tabela"], $a["nomeObjecto"], $a["condicoesMae"], $a["condicoesFilha"]]);   
                }
                $dbOrigem->excluir("dados_excluidos", []);
                $dbOrigem->excluir("dados_excluidos2", []);

                unset($_SESSION['posColeccao']);
                unset($_SESSION['coleccoes']);
                echo "<script>window.open('http://localhost/angoschool/areaEscolas/areaDirector/backupDados/maria_mengi.php', '_self')</script>";
            }
        }        
    }