<?php
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    if(!isset($_SESSION["directorioPaterno"])){
      $protocolo = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS']=="on") ? "https" : "http");
      echo "<script>window.location='".$protocolo."://".$_SERVER["HTTP_HOST"]."'</script>";
    }
    include_once $_SESSION["directorioPaterno"].'angoschool/areaDireccaoProvincial/funcoesAuxiliares.php';
    include_once $_SESSION["directorioPaterno"].'angoschool/areaDireccaoProvincial/funcoesAuxiliaresDb.php';

    include_once 'primeiroCiclo.php';
    include_once 'segundoCiclo.php';
    include_once 'ensinoPrimario.php';
 
    class relatorioPorCurso extends funcoesAuxiliares{
        public $alunosTrans = array();
        public $alunosInscritos = array();

        function __construct($caminhoAbsoluto){
            $this->caminhoRetornar = retornarCaminhoRecuarArquivosPhp(__DIR__);
            parent::__construct("Rel-Resumo de Matricula");
            $this->idPAno = isset($_GET["idPAno"])?$_GET["idPAno"]:$this->idAnoActual;        
            $this->numAno();

            

            if($this->verificacaoAcesso->verificarAcessoAlteracao(["aPedagogica", "aDirectoria", "aAdministrativa"], "", "")){
              $this->visualizar();
            }else{
              $this->negarAcesso();
            }
            
        }

        private function visualizar(){
            $classe = isset($_GET["classe"])?$_GET["classe"]:"";

            $this->privacidade = isset($_GET["privacidade"])?$_GET["privacidade"]:"";
            $this->labelPrivacidade="Público";
            if($this->privacidade!="Privada" && $this->privacidade!="Pública"){
                $this->privacidade="Pública";
            }
            if($this->privacidade=="Privada"){
                $this->labelPrivacidade="Privado";
            }

            $this->html ="
            <html>
                <head>
                    <title>Resumo de Matricula</title>
                    <style>
                        table tr td{
                            padding:3px;
                        }
                    </style>
                </head>
                <body>".$this->fundoDocumento("horizontal");

                $mamale="TOT";
                if($classe<=6){
                    $this->ensinoPrimario = new ensinoPrimario($this);
                    $mamale= $this->seCriarNovaFolha($mamale, $this->ensinoPrimario->visualizar());
                }
                if($classe>=7 && $classe<=9){
                    $this->primeiroCiclo = new primeiroCiclo($this);
                    $mamale= $this->seCriarNovaFolha($mamale, $this->primeiroCiclo->visualizar());
                }
                if($classe>=10){
                    $this->segundoCiclo = new segundoCiclo($this);
                    $mamale= $this->seCriarNovaFolha($mamale, $this->segundoCiclo->visualizar());
                }
                

            $this->html.=$mamale."
                </body>
            </html>";

           $this->exibir("Ficheiros/Escola_".$_SESSION['idEscolaLogada']."/".$this->idPAno."/Estatisticas", "Resumo de Matricula-".$this->numAno, "Resumo_Matricula-".$this->idPAno, "A4", "landscape");

        }



        public function contador($idPEscola, $idade, $genero, $turma, $classe, $curso="", $classeTd="TOT"){
            $contar=0; 
            foreach ($this->alunosInscritos as $aluno) {
                if($this->idade($idade, $aluno->dataNascAluno) && seComparador($genero, $aluno->sexoAluno) && seComparador($turma, $aluno->nomeTurma) && seComparador($idPEscola, $aluno->idReconfEscola) && seComparador($classe, $aluno->classeReconfirmacao) && seComparador($curso, $aluno->idMatCurso)){
                    $contar++;
                }
                
            }

            foreach ($this->alunosTrans as $aluno) {
                if($this->idade($idade, $aluno->dataNascAluno) && seComparador($idPEscola, $aluno->idReconfEscola) && seComparador($genero, $aluno->sexoAluno) && ($turma=="A" || $turma=="TOT") && seComparador($classe, $aluno->classeReconfirmacao) && seComparador($curso, $aluno->idMatCurso)){
                    $contar++;
                }
                
            }
            return "<td style='".$this->text_center.$this->border()."'>".completarNumero($contar)."</td>";
        }

        function idade($idade, $dataNascAluno){
            if($idade=="TOT"){
                return true;
            }else if($idade=="<=14"){
                if(calcularIdade(explode("-", $this->dataSistema)[0], $dataNascAluno)<=14){
                    return true;
                }else{
                    return false;
                }
            }else if($idade=="<=11"){
                if(calcularIdade(explode("-", $this->dataSistema)[0], $dataNascAluno)<=11){
                    return true;
                }else{
                    return false;
                }
            }else if($idade==">=21"){
                if(calcularIdade(explode("-", $this->dataSistema)[0], $dataNascAluno)>=21){
                    return true;
                }else{
                    return false;
                }
            }else if($idade==">=18"){
                if(calcularIdade(explode("-", $this->dataSistema)[0], $dataNascAluno)>=18){
                    return true;
                }else{
                    return false;
                }
            }else if($idade=="TOT"){
                return true;
            }else{
                if(calcularIdade(explode("-", $this->dataSistema)[0], $dataNascAluno)==$idade){
                    return true;
                }else{
                    return false;
                }
            }
        }

        private function seCriarNovaFolha($mamale, $chadrack){
            if($mamale=="TOT"){
                return $chadrack;
            }else{
                return $mamale."<div style='page-break-before: always;'>".$this->fundoDocumento("horizontal").$chadrack."</div>";
            }
        }
    }
    new relatorioPorCurso(__DIR__);  
?>