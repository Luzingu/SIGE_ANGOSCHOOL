<?php 
	session_start();

	$luzl = isset($_GET["luzl"])?$_GET["luzl"]:"";

if($luzl=="luzl"){
	$idEscola=1;
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/manipulacaoDadosMae.php';
    $msgErro="";
    try {
        $dbDestino = new manipulacaoDadosMae();
        $dbDestino->conexaoDb = new MongoDB\Client;
        $dbDestino->db = $dbDestino->conexaoDb->luzl;
        $dbDestino->backup="";
        $dbDestino->serverAlteracao="online";

        if ($dbDestino->selectArray("escolas", ["idPEscola"], [], [], 1)<=0) {
            $msgErro="FNão foi possível estabeler a conexão com a bese de dados.";
        }
    }catch (MongoDB\Driver\Exception\ConnectionException $e) {
        $msgErro="FErro ao conectar com o MongoDB: " . $e->getMessage();
    }catch (Exception $e) {
        $msgErro="FOcorreu um erro inesperado: " . $e->getMessage();
    }

    if($msgErro==""){
        try {
            $dbOrigem = new manipulacaoDadosMae();
            $dbOrigem->conexaoDb = new MongoDB\Client;

            $dbOrigem->conexaoDb = new MongoDB\Client("mongodb://abigael:Renapol1..abigael@194.233.69.35:27019");
            $dbOrigem->db = $dbOrigem->conexaoDb->escola;
            $dbOrigem->backup="";
            $dbOrigem->serverAlteracao="online";

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

        $condicao=["cloner_".$idEscola=>null];
        foreach($dbOrigem->selectArray("agrup_alunos", ["grupo"]) as $grupo){
        	$condicao["escola.idMatEscola"]=$idEscola;

            $coleccoes[]=array(
            "nome"=>"alunos_".$grupo["grupo"],
            "id"=>"idPMatricula", "condicao"=>$condicao);
        }

        foreach ($dbOrigem->db->listCollections() as $coleccao) {
        	
            $nameColeccao1 = explode("_", $coleccao->getName())[0];
            $nameColeccao2 = isset(explode("_", $coleccao->getName())[1])?explode("_", $coleccao->getName())[1]:"";

            if(!($nameColeccao1=="alunos" && $nameColeccao2!="") && $nameColeccao1!="entidadesonline" && $nameColeccao1!="mensagens"){

            	if($nameColeccao1=="entidadesprimaria"){
            		$condicao["escola.idEntidadeEscola"]=$idEscola;
            	}else if($nameColeccao1=="escolas"){
            		$condicao["idPEscola"] = $idEscola;
            	}else if($nameColeccao1=="comissAvalDesempPessoalNaoDocente"){
            		$condicao["idEscola"] = $idEscola;
            	}else if($nameColeccao1=="comissAvalDesempProfessor"){
            		$condicao["idEscola"] = $idEscola;
            	}else if($nameColeccao1=="comunicados"){
            		$condicao["idPEscola"] = $idEscola;
            	}else if($nameColeccao1=="definicoesConselhoNotas"){
            		$condicao["idPEscola"] = $idEscola;
            	}else if($nameColeccao1=="divisaoprofessores"){
            		$condicao["idPEscola"] = $idEscola;
            	}else if($nameColeccao1=="facturas"){
            		$condicao["idFacturaEscola"] = $idEscola;
            	}else if($nameColeccao1=="horario"){
            		$condicao["idPEscola"] = $idEscola;
            	}else if($nameColeccao1=="listaturmas"){
            		$condicao["idPEscola"] = $idEscola;
            	}else if($nameColeccao1=="nomecursos"){
            		$condicao["cursos.idCursoEscola"] = $idEscola;
            	}else if($nameColeccao1=="nomedisciplinas"){
            		$condicao["disciplinas.idDiscEscola"] = $idEscola;
            	}else if($nameColeccao1=="pagamentos_matricula_inscricao"){
            		$condicao["idPagEscola"] = $idEscola;
            	}
                $coleccoes[]=array(
                "nome"=>$nameColeccao1,
                "id"=>"idPMatricula", "condicao"=>$condicao);

            }
        }

        foreach($coleccoes as $col){

            foreach($dbOrigem->selectArray($col["nome"], [], $col["condicao"]) as $tabela){
                $contChave=0;
                $idPrincipal="";
                $strinValores="";
                $valores=array();

                foreach(retornarChaves($tabela) as $chave){

                    if($chave!="_id" && isset($tabela[$chave]) && !is_object($tabela[$chave])){
                        $contChave++;

                        if($col["nome"]=="entidadesprimaria"){
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
                            if(count($dbDestino->selectArray($col["nome"], [$idPrincipal], [$idPrincipal=>$tabela[$idPrincipal]]))<=0){
                                $dbDestino->inserir($col["nome"], $idPrincipal, $idPrincipal, [$tabela[$idPrincipal]], "sim", "nao", [], $tabela[$idPrincipal]);
                            }
                        }

                    }
                }

                foreach(retornarChaves($tabela) as $chave){

                    if($chave!="_id" && isset($tabela[$chave]) && is_object($tabela[$chave])){
                        
                        $arrayCondicao=array();
                        if($chave=="escola" && $tabela!="entidadesprimaria"){
                        	$arrayCondicao = ["idMatEscola=".$idEscola];
                        }else if($chave=="escola" && $tabela=="entidadesprimaria"){
                        	$arrayCondicao = ["idEntidadeEscola=".$idEscola];
                        }else if($chave=="arquivo_pautas"){
                        	$arrayCondicao = ["idPautaEscola=".$idEscola];
                        }else if($chave=="reconfirmacoes"){
                        	$arrayCondicao = ["idReconfEscola=".$idEscola];
                        }else if($chave=="pagamentos"){
                        	$arrayCondicao = ["idHistoricoEscola=".$idEscola];
                        }else if($chave=="dadosatraso"){
                        	$arrayCondicao = ["idDEscola=".$idEscola];
                        }else if($chave=="transferencia"){
                        	$arrayCondicao = ["idTransfEscolaOrigem=".$idEscola];
                        }else if($chave=="alteracoes_notas"){
                        	$arrayCondicao = ["idHistEscola=".$idEscola];
                        }else if($chave=="aval_desemp"){
                        	$arrayCondicao = ["idAvalProfEscola=".$idEscola];
                        }else if($chave=="cursos"){
                        	$arrayCondicao = ["idCursoEscola=".$idEscola];
                        }else if($chave=="disciplinas"){
                        	$arrayCondicao = ["idDiscEscola=".$idEscola];
                        }

                        foreach(listarItensObjecto($tabela, $chave, $arrayCondicao) as $itemActualizado){

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
                            $dbDestino->excluirItemObjecto($col["nome"], $chave, [$idPrincipal=>$tabela[$idPrincipal]], [$idPSubChave=>$itemActualizado[$idPSubChave]]);

                            $dbDestino->inserirObjecto($col["nome"], $chave, $idPSubChave, $strings, $dados, [$idPrincipal=>$tabela[$idPrincipal]], "sim", "nao", $itemActualizado[$idPSubChave]);
                        }
                    }
                }
                $dbDestino->editar($col["nome"], $strinValores, $valores, [$idPrincipal=>$tabela[$idPrincipal]]);

                $dbOrigem->editar($col["nome"], "cloner_".$idEscola, ["ja"], [$idPrincipal=>$tabela[$idPrincipal]]);
            }
        }
    }
 }
