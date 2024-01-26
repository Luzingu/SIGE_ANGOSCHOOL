<?php 
    session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEntretenimento/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEntretenimento/manipulacaoDadosDoAjax.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{

        function __construct($caminhoAbsoluto){
            parent::__construct();
            $this->conDb("entretenimento", true);
            if($this->accao=="novaQuestao"){
                $this->novaQuestao();
            }else if($this->accao=="respostaCerta" || $this->accao=="respostaErrada" || $this->accao=="tempoAcabou"){

                $idPQuestao =  $_GET['idPQuestao'];
                $sobreQuestao = $this->selectArray("questoes", [], ["idPQuestao"=>$idPQuestao]);
                $pontuacao=0;
                $tipoUsuario="aluno";
                if($_SESSION['tipoUsuario']!="aluno"){
                    $tipoUsuario="entidade";
                }
                $sobePontuacaoJogador =$this->selectArray("pontuacao_jogador", [], ["idJogador"=>$_SESSION['idUsuarioLogado'], "tipoJogador"=>$tipoUsuario]);
                $pontosAnterior=valorArray($sobePontuacaoJogador, "pontuacao");

                if(count($sobePontuacaoJogador)<=0){
                    $nomeJogador=valorArray($this->sobreUsuarioLogado, "nomeEntidade");
                    $fotoJogador=valorArray($this->sobreUsuarioLogado, "fotoEntidade");
                    $escolaJogador=valorArray($this->sobreUsuarioLogado, "nomeEscola");

                    if($tipoUsuario=="aluno"){
                        $nomeJogador=valorArray($this->sobreUsuarioLogado, "nomeAluno");
                        $fotoJogador=valorArray($this->sobreUsuarioLogado, "fotoAluno");
                    }
                    $this->inserir("pontuacao_jogador", "idPPontuacao", "idJogador, tipoJogador, nomeJogador, fotoJogador, escolaJogador, pontuacao", [$_SESSION['idUsuarioLogado'], $tipoUsuario, $nomeJogador, $fotoJogador, $escolaJogador, 0]); 
                    $pontosAnterior=0;  
                }

                if($this->accao=="respostaCerta"){
                    $resultado="Acertou";
                    $pontuacao = valorArray($sobreQuestao, "pontuacao");
                    $labelPontuacao="+".valorArray($sobreQuestao, "pontuacao");
                }else{
                    if($pontosAnterior<=0 || ($pontosAnterior-valorArray($sobreQuestao, "pontuacao")/2)<0){
                        $pontuacao=0;
                        $labelPontuacao=0;
                    }else{
                        $pontuacao = -1*valorArray($sobreQuestao, "pontuacao")/2;
                        $labelPontuacao="-".valorArray($sobreQuestao, "pontuacao")/2;

                    }
                    if($this->accao=="respostaErrada"){
                        $resultado="Errou";
                    }else{
                        $resultado="Tempo";
                    }
                }
                $this->inserir("historial_jogador", "idPHistorial", "idJogador, tipoJogador, idPQuestao, questao, dataQuestao, horaQuestao, resultado, pontuacao", [$_SESSION['idUsuarioLogado'], $tipoUsuario, $idPQuestao, valorArray($sobreQuestao, "questao"), $this->dataSistema, $this->tempoSistema, $resultado, $labelPontuacao]);

                $this->editar("pontuacao_jogador", "pontuacao", [($pontosAnterior+$pontuacao)],["idJogador"=>$_SESSION['idUsuarioLogado'], "tipoJogador"=>$tipoUsuario]);
                $this->novaQuestao();
            }else if($this->accao=="saltar"){
                $idPQuestao =  $_GET['idPQuestao'];
                $sobreQuestao = $this->selectArray("questoes", [], ["idPQuestao"=>$idPQuestao]);
                $tipoUsuario="aluno";
                if($_SESSION['tipoUsuario']!="aluno"){
                    $tipoUsuario="entidade";
                }
                $this->inserir("historial_jogador", "idPHistorial", "idJogador, tipoJogador, idPQuestao, questao, dataQuestao, horaQuestao, resultado, pontuacao", [$_SESSION['idUsuarioLogado'], $tipoUsuario, $idPQuestao, valorArray($sobreQuestao, "questao"), $this->dataSistema, $this->tempoSistema, "Saltou", ""]);
                $this->novaQuestao();
            }else if($this->accao=="pedirAjuda"){
                $idPQuestao =  $_GET['idPQuestao'];
                $sobreQuestao = $this->selectArray("questoes", [], ["idPQuestao"=>$idPQuestao]);
                $tipoUsuario="aluno";
                if($_SESSION['tipoUsuario']!="aluno"){
                    $tipoUsuario="entidade";
                }
                $this->inserir("historial_jogador", "idPHistorial", "idJogador, tipoJogador, idPQuestao, questao, dataQuestao, horaQuestao, resultado", [$_SESSION['idUsuarioLogado'], $tipoUsuario, $idPQuestao, valorArray($sobreQuestao, "questao"), $this->dataSistema, $this->tempoSistema, "Ajuda"]);
                $estadoAjuda="V";
                if(count($this->selectArray("historial_jogador", ["idPHistorial"], ["tipoJogador"=>$tipoUsuario, "idJogador"=>$_SESSION['idUsuarioLogado'], "dataQuestao"=>$this->dataSistema, "resultado"=>"Ajuda"]))>=3){
                    $estadoAjuda="F";
                }
                echo $estadoAjuda;
            }
        }

        private function novaQuestao(){
            $tipoUsuario="aluno";
            if($_SESSION['tipoUsuario']!="aluno"){
                $tipoUsuario="entidade";
            }

            $questoesTodas=array();
            $questoesHoje=array();
            $perguntas=array();

            if(!isset($_SESSION['zerarTudo'])){
                foreach($this->selectArray("historial_jogador", ["idPQuestao", "dataQuestao"], ["idJogador"=>$_SESSION['idUsuarioLogado'], "tipoJogador"=>$tipoUsuario, "resultado"=>array('$nin'=>["Acertou", "Saltou"])], [], 200, [], ["idPQuestao"=>-1]) as $questao){
                    $questoesTodas[]=intval($questao["idPQuestao"]);
                    if($questao["dataQuestao"]==$this->dataSistema){
                        $questoesHoje[]=intval($questao["idPQuestao"]);
                    }
                }
                $perguntas = $this->selectArray("questoes", [], ["idPQuestao"=>array('$nin'=>$questoesHoje)], [], "", [], ["idPQuestao"=>1]);
                if(count($perguntas)<=0){
                    $perguntas = $this->selectArray("questoes", [], ["idPQuestao"=>array('$nin'=>$questoesTodas)], [], "", [], ["idPQuestao"=>1]);
                }
            }

            if(count($perguntas)<=0){
                $_SESSION['zerarTudo']="sim";
                $perguntas = $this->selectArray("questoes", [], [], [], "", [], ["idPQuestao"=>1]);
            }
            $numero = rand(0, (count($perguntas)-1));
            $estadoAjuda="V";
            if(count($this->selectArray("historial_jogador", ["idPHistorial"], ["tipoJogador"=>$tipoUsuario, "idJogador"=>$_SESSION['idUsuarioLogado'], "dataQuestao"=>$this->dataSistema, "resultado"=>"Ajuda"]))>=3){
                $estadoAjuda="F";
            }

            $estadoSaltos="V";
            if(count($this->selectArray("historial_jogador", ["idPHistorial"], ["tipoJogador"=>$tipoUsuario, "idJogador"=>$_SESSION['idUsuarioLogado'], "dataQuestao"=>$this->dataSistema, "resultado"=>"Saltou"]))>=3){
                $estadoSaltos="F";
            }

            $sobePontuacaoJogador =$this->selectArray("pontuacao_jogador", ["pontuacao"], ["idJogador"=>$_SESSION['idUsuarioLogado'], "tipoJogador"=>$tipoUsuario]);

            $dadosRetornar = ["pontuacao"=>valorArray($sobePontuacaoJogador, "pontuacao"), "estadoSaltos"=>$estadoSaltos, "estadoAjuda"=>$estadoAjuda,"numeroSaltos"=>"", "idPQuestao"=>$perguntas[$numero]->idPQuestao, "questao"=>$perguntas[$numero]->questao, "tipoResposta"=>$perguntas[$numero]->tipoResposta, "resposta1"=>$perguntas[$numero]->resposta1, "resposta2"=>$perguntas[$numero]->resposta2, "resposta3"=>nelson($perguntas[$numero], "resposta3"), "resposta4"=>nelson($perguntas[$numero], "resposta4")];
            echo json_encode($dadosRetornar);
        }

        
    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>