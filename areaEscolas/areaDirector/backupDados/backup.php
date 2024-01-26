<?php 
	session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/manipulacaoDadosMae.php';
    $msgErro="";
    
    try { 
        $dbDestino = new manipulacaoDadosMae();
        $dbDestino->conexaoDb = new MongoDB\Client;
        if($_SESSION['idEscolaLogada']==4){
            $dbDestino->db = $dbDestino->conexaoDb->teste;
        }else{
            $dbDestino->db = $dbDestino->conexaoDb->escola;
        }
        $dbDestino->actualizacaoDados = "on";
        $dbDestino->backup="";
        $dbDestino->serverAlteracao="online";
        $mengi = valorArray($dbDestino->selectArray("maria_mengi"), "kuansambu");

        if ($dbDestino->selectArray("escolas", ["idPEscola"], [], [], 1)<=0) {
            $msgErro="FNão foi possível estabeler a conexão com a bese de dados.";
        }
    }catch (MongoDB\Driver\Exception\ConnectionException $e) {
        $msgErro="FErro ao conectar com o MongoDB:".$e->getMessage();
    }catch (Exception $e) {
        $msgErro="FOcorreu um erro inesperado:".$e->getMessage();
    }

    if($msgErro==""){
        try {
            $dbOrigem = new manipulacaoDadosMae();
            $dbOrigem->conexaoDb = new MongoDB\Client("mongodb://".stand_up("Xy=bW9uZ29kYitzcnY6Ly9heW5lemFudGEuLmhudGFtdToyeWpvMUhISU1xLi5oNHJ4N0d4QGNsdXN0ZXIwLjN0OS4uaHFweXgubW9uZ29kYi5uZXQv=Y"));

            $backup_name="backup_".$_SESSION['idEscolaLogada'];

            $dbOrigem->db = $dbOrigem->conexaoDb->$backup_name;
            $dbOrigem->actualizacaoDados = "on";
            $dbOrigem->backup="";
            $dbOrigem->serverAlteracao="online";
        }catch (MongoDB\Driver\Exception\ConnectionException $e) {
            $msgErro="FErro ao conectar com o MongoDB: " . $e->getMessage();
        }catch (Exception $e) {
            $msgErro="FOcorreu um erro inesperado: " . $e->getMessage();
        }
    } 
    
    if($msgErro!=""){
        echo $msgErro;
    }else{
        if(!isset($_SESSION['posColeccao'])){
            $_SESSION['posColeccao']=0;
        }

        if(!isset($_SESSION['coleccoes']) || isset($_SESSION['coleccoes'])){

            $_SESSION['coleccoes']=array();

            foreach ($dbOrigem->db->listCollections() as $coleccao) {
                
                if($coleccao->getName()!="dados_excluidos" && $coleccao->getName()!="dados_excluidos2"){

                    $_SESSION['coleccoes'][]=array(
                    "nome"=>$coleccao->getName());
                }
            }
        }
        if(count($_SESSION['coleccoes'])<=0){
            echo "<script>window.location='index.php'</script>";
        }

        $contador=0;
        foreach($dbOrigem->selectArray($_SESSION['coleccoes'][$_SESSION['posColeccao']]["nome"], [], [], [], 1) as $tabela){
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
                    
                    foreach(listarItensObjecto($tabela, $chave, []) as $itemActualizado){
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
                    }
                }
            }
            if(valorArray($tabela, "backupGeral")=="nao"){
                $dbDestino->editar($_SESSION['coleccoes'][$_SESSION['posColeccao']]["nome"], $strinValores, $valores, [$idPrincipal=>$tabela[$idPrincipal]]);
            }
            $dbOrigem->excluir($_SESSION['coleccoes'][$_SESSION['posColeccao']]["nome"], [$idPrincipal=>$tabela[$idPrincipal]]);
        }
        if($contador>0){
            echo "<script>window.location='?criador=Afonso Luzingu'</script>";        
        }else{
            

            if($_SESSION['posColeccao']<(count($_SESSION['coleccoes'])-1)){
                $_SESSION['posColeccao']++;

                echo "<script>window.location='?criador=Afonso Luzingu'</script>";
            }else{

                foreach($dbOrigem->selectArray("dados_excluidos2") as $a){

                    $condicoesMae = array();
                    foreach(retornarChaves($a["condicoesMae"]) as $chave){
                        if(isset($a["condicoesMae"][$chave])){
                            $condicoesMae[$chave] = $a["condicoesMae"][$chave];
                        }
                    }

                    $condicoesFilha = array();
                    foreach(retornarChaves($a["condicoesFilha"]) as $chave){
                        if(isset($a["condicoesFilha"][$chave])){
                            $condicoesFilha[$chave] = $a["condicoesFilha"][$chave];
                        }
                    }
                    $dbDestino->excluirItemObjecto($a["tabela"], $a["nomeObjecto"], $condicoesMae, $condicoesFilha);  
                }
                foreach($dbOrigem->selectArray("dados_excluidos") as $a){

                    $condicoes = array();
                    foreach(retornarChaves($a["condicoes"]) as $chave){
                       $condicoes[$chave] = $a["condicoes"][$chave];
                    }

                    $dbDestino->excluir($a["tabela"], $a["condicoes"]);  
                }
                $dbOrigem->excluir("dados_excluidos", []);
                $dbOrigem->excluir("dados_excluidos2", []);
                $dbDestino->editar("escolas", "dataBackup1, horaBackup, idUsuarioBackup, nomeUsuarioBackup", [$dbDestino->dataSistema, $dbDestino->tempoSistema, $_SESSION['idUsuarioLogado'], valorArray($dbOrigem->sobreUsuarioLogado, "nomeEntidade")], ["idPEscola"=>$_SESSION['idEscolaLogada']]);
                echo "<script>window.location='index.php'</script>";
            }
        }        
    }
    