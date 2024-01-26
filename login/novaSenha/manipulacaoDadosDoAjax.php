<?php session_start();
     
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/login/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/login/manipulacaoDadosDoAjax.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{
 
        function __construct(){
            parent::__construct();
            if($this->accao=="trocarSenhaConfirm"){
                $this->trocarSenhaConfirm();
            }
        }
        private function trocarSenhaConfirm (){
            $novaPassword = $_POST["novaPassword"];
            $confirmarPasword = $_POST["confirmarPasword"];

            if($novaPassword!=$confirmarPasword){
                echo "0As Senhas passes devem ser iguais.";
            }else if(strlen($novaPassword)<8){
                echo "0A Senha deve ter no mínimo 8 caracteres.";
            }else{
                if($_SESSION["tipoUsuario"]=="aluno"){
                    $grupo = $this->selectUmElemento("alunosmatriculados", "grupo", ["idPMatricula"=>$_SESSION['idUsuarioLogado']]);

                    $this->editar("alunos_".$grupo, "senhaAluno", ["0c7".criptografarMd5($novaPassword)."ab"], ["idPMatricula"=>$_SESSION["idUsuarioLogado"]]);

                    $array = $this->selectArray("alunos_".$grupo, ["senhaAluno", "estadoAluno", "idPMatricula", "senhaAluno", "escola.idMatEscola", "fotoAluno", "nomeAluno", "numeroInterno", "escola.classeActualAluno", "idPMatricula"], ["idPMatricula"=>$_SESSION["idUsuarioLogado"], "escola.estadoAluno"=>"A", "escola.idMatEscola"=>$_SESSION['idInstituicaoEntrar']], ["escola"]);
                    $array = $this->anexarTabela2($array, "escolas", "escola", "idPEscola", "idMatEscola");
                    $array = $this->anexarTabela2($array, "nomecursos", "escola", "idPNomeCurso", "idMatCurso");

                    unset($_SESSION["recuperacaoSenha"]);
                    echo "../../".$this->podeEntrar($array);
                    
                 }else{
                    $this->editar("entidadesprimaria", "senhaEntidade", ["0c7".criptografarMd5($novaPassword)."ab"], ["idPEntidade"=>$_SESSION["idUsuarioLogado"]]); 

                    $array = $this->selectArray("entidadesprimaria", ["escola.idEntidadeEscola", "escola.nivelSistemaEntidade", "senhaEntidade", "estadoAcessoEntidade", "nomeEntidade", "fotoEntidade", "idPEntidade"], ["escola.estadoActividadeEntidade"=>"A", "escola.idEntidadeEscola"=>$_SESSION['idInstituicaoEntrar'], "idPEntidade"=>$_SESSION["idUsuarioLogado"]], ["escola"]);
                    $array = $this->anexarTabela2($array, "escolas", "escola", "idPEscola", "idEntidadeEscola"); 

                    unset($_SESSION["recuperacaoSenha"]);
                    echo "../../".$this->podeEntrar($array);
                    
                 }
            }
        }
    }
    new manipulacaoDadosDoAjaxInterno();
?>