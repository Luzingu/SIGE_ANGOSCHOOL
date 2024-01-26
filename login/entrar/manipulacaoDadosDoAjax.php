<?php
    ini_set( "session.gc_maxlifetime", 600000000);
    session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/login/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/login/manipulacaoDadosDoAjax.php';

    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{
        function __construct(){
            parent::__construct();
            $this->numeroInterno = filter_input(INPUT_POST, "numeroInterno", FILTER_SANITIZE_STRING );
            $this->password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_STRING);
            if($this->accao=="fazerLogin"){
                $this->logar();
            }
        }

       private function logar(){
            $this->numeroInterno = strtoupper($this->numeroInterno);
            $tipoDb="entidade";

            $array = $this->selectArray("entidadesprimaria", ["ninjaF5", "nomeEntidade"], ['$or'=>[array("numeroTelefoneEntidade"=>(int)$this->numeroInterno), array("biEntidade"=>$this->numeroInterno), array("numeroInternoEntidade"=>$this->numeroInterno)]]);
            if(valorArray($array, "ninjaF5") == "A"){
                $condicaoEntidade=["escola.idEntidadeEscola"=>$_SESSION['idInstituicaoEntrar'], '$or'=>[array("numeroTelefoneEntidade"=>(int)$this->numeroInterno), array("biEntidade"=>$this->numeroInterno), array("numeroInternoEntidade"=>$this->numeroInterno)]];
            }else{
                $condicaoEntidade=["escola.estadoActividadeEntidade"=>"A", "escola.idEntidadeEscola"=>$_SESSION['idInstituicaoEntrar'], '$or'=>[array("numeroTelefoneEntidade"=>(int)$this->numeroInterno), array("biEntidade"=>$this->numeroInterno), array("numeroInternoEntidade"=>$this->numeroInterno)]];
            }
            $array = $this->selectArray("entidadesprimaria", ["escola.idEntidadeEscola", "escola.nivelSistemaEntidade", "senhaEntidade", "estadoAcessoEntidade", "nomeEntidade", "fotoEntidade", "idPEntidade"], $condicaoEntidade, ["escola"]);
            $array = $this->anexarTabela2($array, "escolas", "escola", "idPEscola", "idEntidadeEscola");


            if(count($array)<=0){
                $tipoDb="aluno";
                $array = $this->selectArray("alunosmatriculados", ["senhaAluno", "estadoAluno", "idPMatricula", "senhaAluno", "escola.idMatEscola", "fotoAluno", "nomeAluno", "numeroInterno", "escola.classeActualAluno", "idPMatricula", "escola.idMatEscola", "escola.idMatCurso"], ['$or'=>[array("numeroInterno"=>$this->numeroInterno), array("biAluno"=>$this->numeroInterno)], "escola.estadoAluno"=>"A", "escola.idMatEscola"=>$_SESSION['idInstituicaoEntrar']], ["escola"]);
                $array = $this->anexarTabela2($array, "escolas", "escola", "idPEscola", "idMatEscola");
                $array = $this->anexarTabela2($array, "nomecursos", "escola", "idPNomeCurso", "idMatCurso");
            }

            $erro="";
            if(count($array)<=0){
                echo "FNão foi encontrado nenhum usuário com este Número Interno.";
            }else{

                if($tipoDb=="aluno"){
                    if($_SESSION['idInstituicaoEntrar']==19 || $_SESSION['idInstituicaoEntrar']==27){
                        echo "FAs contas dos alunos foram bloquadas pela direcção da escola.";
                    }else if(atenticarMd5(valorArray($array, "senhaAluno"), $this->password)=="sim"){
                        if(valorArray($array, "estadoEscola")!="A"){
                            echo "FA conta da escola encontra-se inactiva.";
                        }else{
                            $_SESSION["idUsuarioLogado"] =valorArray($array, "idPMatricula");
                            $_SESSION["idEscolaLogada"] =valorArray($array, "idPEscola");
                            $_SESSION["tipoUsuario"]="aluno";
                            if(valorArray($array, "senhaAluno")=="0c74a7d1ed414474e4033ac29ccb8653d9bab"){
                                $_SESSION["recuperacaoSenha"]="sim";
                                echo "../novaSenha/index.php";
                            }else{
                                echo "../../".$this->podeEntrar($array);
                            }
                        }
                    }else{
                        echo "FA tua Senha está incorrecta.";
                    }

                }else{
                    if(atenticarMd5(valorArray($array, "senhaEntidade"), $this->password)=="sim"){
                        if(valorArray($array, "estadoEscola")!="A"){
                            echo "FA conta da escola encontra-se inactiva.";
                        }else if(valorArray($array, "estadoAcessoEntidade")!="A"){
                            echo "FA tua conta encontra-se inactiva, contacte os administradores do aplicativo.";
                        }else{
                            $_SESSION["idUsuarioLogado"] =valorArray($array, "idPEntidade");
                            $_SESSION["idEscolaLogada"] =valorArray($array, "idPEscola");
                            if(valorArray($array, "tipoInstituicao")=="escola"){
                                $_SESSION["tipoUsuario"]="professor";
                            }else if(valorArray($array, "tipoInstituicao")=="DP"){
                                $_SESSION["tipoUsuario"]="direccaoP";
                            }else if(valorArray($array, "tipoInstituicao")=="DM"){
                                $_SESSION["tipoUsuario"]="reparticaoM";
                            }else{
                                $_SESSION["tipoUsuario"]="administrador";
                            }
                            if(valorArray($array, "senhaEntidade")=="0c74a7d1ed414474e4033ac29ccb8653d9bab"){
                                $_SESSION["recuperacaoSenha"]="sim";
                                echo "../novaSenha/index.php";
                            }else{
                                echo "../../".$this->podeEntrar($array);
                            }
                        }
                    }else{
                        echo "FA tua Senha está incorrecta.";
                    }
                }
            }
        }
    }
    new manipulacaoDadosDoAjaxInterno();
?>
