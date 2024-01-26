<?php session_start();
     
   include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/login/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/login/manipulacaoDadosDoAjax.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjaxMae{

        function __construct(){
            parent::__construct();
            $this->numeroInterno =filter_input(INPUT_POST, "numeroInterno", FILTER_SANITIZE_STRING );
            $this->email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_STRING);

            if($this->accao=="recuperarSenha"){
                $this->recuperarSenha();
            }
        }

        private function recuperarSenha(){

            $_SESSION["tipoUsuario"] = "entidade";

            $array = $this->selectArray("entidadesprimaria", ["escola.idEntidadeEscola", "escola.nivelSistemaEntidade", "senhaEntidade", "estadoAcessoEntidade", "nomeEntidade", "fotoEntidade", "idPEntidade", "emailEntidade"], ["escola.estadoActividadeEntidade"=>"A", "escola.idEntidadeEscola"=>$_SESSION['idInstituicaoEntrar'], '$or'=>[array("numeroTelefoneEntidade"=>(int)$this->numeroInterno), array("biEntidade"=>$this->numeroInterno), array("numeroInternoEntidade"=>$this->numeroInterno)]], ["escola"]);

            $array = $this->anexarTabela2($array, "escolas", "escola", "idPEscola", "idEntidadeEscola");
                
            if(count($array)<=0){
                $_SESSION["tipoUsuario"] = "aluno";
                $array = $this->selectArray("alunosmatriculados", ["senhaAluno", "estadoAluno", "idPMatricula", "senhaAluno", "escola.idMatEscola", "fotoAluno", "nomeAluno", "numeroInterno", "escola.classeActualAluno", "idPMatricula", "emailAluno"], ['$or'=>[array("numeroInterno"=>$this->numeroInterno), array("biAluno"=>$this->numeroInterno)], "escola.estadoAluno"=>"A", "escola.idMatEscola"=>$_SESSION['idInstituicaoEntrar']], ["escola"]);
                $array = $this->anexarTabela2($array, "escolas", "escola", "idPEscola", "idMatEscola");
            }
            $codigo = substr(str_shuffle("0123456789"),0, 8);
            $corpoEmail ='
            <p style="text-align:justify; line-height:23px; font-size:17px;">Recebemos um pedido de recuperação de Senha. Abaixo está o código que precisas para criar uma Nova Senha.<br>Se não fizeste o pedido em questão, por favor, ignora esta mensagem.</p><p style="text-align:center; line-height:23px; font-size:17px;"><strong style="color:red;">'.$codigo.'</strong></p>';

            if(count($array)<=0){
                echo "0Não foi encontrado nenhuma entidade com este endereço.";
            }else{
                if($_SESSION["tipoUsuario"]=="aluno"){
                    if(valorArray($array, "emailAluno")!=$this->email){
                        echo "1E-mail incorrecto.";
                    }else{         
                        if(smtpmailer(valorArray($array, "emailAluno"), "admin@angoschool.org", "LUZINGU LUAME", "RECUPERAÇÃO DE SENHA", $corpoEmail)){

                            $_SESSION["idUsuarioLogado"] =valorArray($array, "idPMatricula");
                            $_SESSION["idEscolaLogada"] =valorArray($array, "idPEscola");
                            $_SESSION["tipoUsuario"]="aluno";

                            if(count($this->selectArray("backupsenha", [], ["idBackMatricula"=>$_SESSION["idUsuarioLogado"]]))<=0){
                                $this->inserir("backupsenha", "idPBSenha", "idBackMatricula, codigo, dataEnvio", [$_SESSION["idUsuarioLogado"], strtoupper($codigo), $this->dataSistema]);
                            }else{
                                $this->editar("backupsenha", "codigo, dataEnvio", [strtoupper($codigo), $this->dataSistema], ["idBackMatricula"=>$_SESSION["idUsuarioLogado"]]);
                            }
                            echo "enviado";
                            $_SESSION["recuperacaoSenha"]="yes";
                        }else{
                            echo "1Não foi possível enviar o código no seu e-mail.";
                        } 
                    }
                }else{
                    if(valorArray($array, "emailEntidade")!=$this->email){
                        echo "1E-mail Incorrecto.";
                    }else{
                        if(smtpmailer(valorArray($array, "emailEntidade"), "admin@angoschool.org", "LUZINGU LUAME", "RECUPERAÇÃO DE SENHA", $corpoEmail)){ 
                            
                            $_SESSION["idUsuarioLogado"]=valorArray($array, "idPEntidade");
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

                            if(count($this->selectArray("backupsenha", [], ["idBackEntidade"=>$_SESSION["idUsuarioLogado"]]))<=0){
                                $this->inserir("backupsenha", "idPBSenha", "idBackEntidade, codigo, dataEnvio", [$_SESSION["idUsuarioLogado"], $codigo, $this->dataSistema]);
                            }else{
                                $this->editar("backupsenha", "codigo, dataEnvio", [$codigo, $this->dataSistema], ["idBackEntidade"=>$_SESSION["idUsuarioLogado"]]);
                            }
                            $_SESSION["recuperacaoSenha"]="yes";
                            echo "enviado";
                        }else{
                            echo "1Não foi possível enviar o código no seu e-mail.";
                        } 
                    }
                }                
            }
        }
    }
    new manipulacaoDadosDoAjaxInterno();
?>