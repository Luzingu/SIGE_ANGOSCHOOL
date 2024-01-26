<?php
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../../../funcoesAuxiliares.php');
    include_once ('../../../funcoesAuxiliaresDb.php');

    include_once 'ensinoGeral.php';
 
    class relatorioPorCurso extends funcoesAuxiliares{
        public $alunosTrans = array();
        public $alunosInscritos = array();

        function __construct($caminhoAbsoluto){
            parent::__construct("Rel-Resumo de Matricula");
            $this->idPAno = isset($_GET["idPAno"])?$_GET["idPAno"]:$this->idAnoActual;        
            $this->numAno();
            if($this->verificacaoAcesso->verificarAcesso("", ["zonaPesquisa"], [], "")){
              $this->visualizar();
            }else{
              $this->negarAcesso();
            }
            
        }

        private function visualizar(){

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
                
                $this->segundoCiclo = new segundoCiclo($this);

            $this->html.=$this->segundoCiclo->visualizar()."
                </body>
            </html>";

           $this->exibir("Ficheiros/Escola_".$_SESSION['idEscolaLogada']."/".$this->idPAno."/Estatisticas", "Resumo de Matricula-".$this->numAno, "Resumo_Matricula-".$this->idPAno, "A4", "landscape");

        }



        public function contador($idade, $genero, $turma, $classe, $curso){
            $contar=0; 
            foreach ($this->alunosInscritos as $aluno) {
                if($this->idade($idade, $aluno["dataNascAluno"]) && seComparador($genero, $aluno["sexoAluno"]) && seComparador($turma, $aluno["reconfirmacoes"]["nomeTurma"]) && seComparador($classe, nelson($aluno, "classeReconfirmacao", "reconfirmacoes")) && seComparador($curso, nelson($aluno, "idMatCurso", "reconfirmacoes"))){ 
                    $contar++; 
                }
                
            }

            foreach ($this->alunosTrans as $aluno) {
                if($this->idade($idade, $aluno["dataNascAluno"]) && seComparador($turma, $aluno["reconfirmacoes"]["nomeTurma"]) && seComparador($genero, $aluno["sexoAluno"]) && ($turma=="A" || $turma=="TOT") && seComparador($classe, nelson($aluno, "classeReconfirmacao", "reconfirmacoes")) && seComparador($curso, nelson($aluno, "idMatCurso", "reconfirmacoes"))){
                    $contar++;
                }
                
            }
            return "<td style='".$this->text_center.$this->border()."'>".completarNumero($contar)."</td>";
        }

        function idade($idade, $dataNascAluno){
            if($idade=="TOT"){
                return true;
            }else if($idade=="<=5"){
                if(calcularIdade(explode("-", $this->dataSistema)[0], $dataNascAluno)<=5){
                    return true;
                }else{
                    return false;
                }
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
            }else if($idade==">=12"){
                if(calcularIdade(explode("-", $this->dataSistema)[0], $dataNascAluno)>=12){
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