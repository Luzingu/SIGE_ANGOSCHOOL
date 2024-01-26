<?php 
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/login/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/manipulacaoDadosDoAjax.php';
    
    class manipulacaoDadosAjax extends manipulacaoDadosAjaxMae{

        function __construct(){
            parent::__construct();
            $this->actualizacaoDados="on";
        }

        public function podeEntrar($array){

            if($_SESSION["tipoUsuario"]=="aluno"){

                $this->editar("entidadesonline", "estadoExpulsao", ["I"], ["idUsuarioLogado"=>$_SESSION["idUsuarioLogado"], "estadoExpulsao"=>"A"]);

                $nomeUsuario = valorArray($array, "nomeAluno");
                $fotoUsuario = valorArray($array, "fotoAluno");
                $numeroInterno = valorArray($array, "numeroInterno");
                $funcao = valorArray($array, "abrevCurso")."-".valorArray($array, "classeActualAluno", "escola");
                $tipoUsuario="aluno";

                $_SESSION["areaActual"]="areaEscolas/areaAluno";

            }else{
                $this->editar("entidadesonline", "estadoExpulsao", ["I"], ["idUsuarioLogado"=>$_SESSION["idUsuarioLogado"], "estadoExpulsao"=>"A"]);

                $nomeUsuario = valorArray($array, "nomeEntidade");
                $fotoUsuario = valorArray($array, "fotoEntidade");
                $numeroInterno = valorArray($array, "numeroInternoEntidade");
                $funcao = $this->selectUmElemento("cargos", "designacaoCargo", ["idPCargo"=>valorArray($array, "nivelSistemaEntidade", "escola")]);
                $tipoUsuario="entidade";

                if($_SESSION["tipoUsuario"]=="professor"){
                    if(valorArray($array, "tipoPacoteEscola")=="Explorador"){
                        $_SESSION["areaActual"]="areaEscolas/areaExplorador";
                    }else{
                        $_SESSION["areaActual"]="areaEscolas";
                    }
                }else if($_SESSION["tipoUsuario"]=="direccaoP"){
                    $_SESSION["areaActual"]="areaDireccaoProvincial/areaGestaoGPE";
                }else if($_SESSION["tipoUsuario"]=="reparticao"){
                    $_SESSION["areaActual"]="areaDireccaoMunicipal/areaGestaoGPE";
                }else if($_SESSION["tipoUsuario"]=="administrador"){
                    $_SESSION["areaActual"]="areaAdministrador";
                }                
            }
            $_SESSION['tbUsuario']=$tipoUsuario;
            $this->inserir("entidadesonline", "idPOnline", "idOnlineEntEscola, dataEntrada, horaEntrada, dataSaida, horaSaida, estadoExpulsao, idUsuarioLogado, tipoUsuario, nomeUsuario, fotoUsuario, numeroInterno, funcao", [$_SESSION["idEscolaLogada"], $this->dataSistema, $this->tempoSistema, $this->dataSistema, $this->tempoSistema, "A", $_SESSION['idUsuarioLogado'], $tipoUsuario, $nomeUsuario, $fotoUsuario, $numeroInterno, $funcao], "sim", "nao", [["escolas", $_SESSION["idEscolaLogada"], "idPEscola"]]);

            $_SESSION['idPOnline'] = $this->selectUmElemento("entidadesonline", "idPOnline", ["idUsuarioLogado"=>$_SESSION["idUsuarioLogado"], "estadoExpulsao"=>"A"]);

            return $_SESSION["areaActual"]."/index.php";
        }
      
    }

?>