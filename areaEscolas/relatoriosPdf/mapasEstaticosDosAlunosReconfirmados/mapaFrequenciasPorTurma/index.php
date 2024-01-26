<?php
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../../../funcoesAuxiliares.php');
    include_once ('../../../funcoesAuxiliaresDb.php');

    include_once 'relatorioGeral.php';
 
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
                <body>";

                $mamale="";
                $this->segundoCiclo = new segundoCiclo($this);
                    $mamale= $this->seCriarNovaFolha($mamale, $this->segundoCiclo->visualizar());               

            $this->html.=$mamale."
                </body>
            </html>";

           $this->exibir("Ficheiros/Escola_".$_SESSION['idEscolaLogada']."/".$this->idPAno."/Estatisticas", "Resumo de Matricula-".$this->numAno, "Resumo_Matricula-".$this->idPAno);

        }

        public function contadorAlunos($idCurso="TOT", $classe="TOT", $turma="TOT", $genero="TOT"){
          $contador=0;
          foreach ($this->alunosInscritos as $alunos){
            if(seComparador($genero, $alunos["sexoAluno"]) && seComparador($classe, $alunos["reconfirmacoes"]["classeReconfirmacao"]) && seComparador($idCurso, nelson($alunos, "idMatCurso", "reconfirmacoes")) && seComparador($turma, $alunos["reconfirmacoes"]["designacaoTurma"])){
                $contador++; 
            } 
          }
          return completarNumero($contador);
        }

        public function contadorTurma($idCurso="TOT", $classe="TOT"){
          $this->listaTurmas = array();

          $contador=0;
          foreach ($this->turmas as $turma) {
              if(seComparador($classe, $turma["classe"]) && seComparador($idCurso, nelson($turma, "idPNomeCurso"))){
                  $contador++;
                  $this->listaTurmas[] = $turma;
              }
          }
          return completarNumero($contador);
        }

        public function periodoTurma($idCurso="TOT", $classe="TOT", $turma="TOT"){

          $neusa="TOT";
          foreach ($this->turmas as $gerenciador) {
              if(seComparador($classe, $gerenciador["classe"]) && seComparador($idCurso, nelson($gerenciador, "idPNomeCurso")) && seComparador($turma, $gerenciador["designacaoTurma"])){
                 
                 $neusa = $gerenciador["periodoT"];
                 break;

              }
          }
          if($neusa=="Noturno"){
            $neusa="Pós-Laboral";
          }
          return $neusa;
        }

        public function totalCargo($idCurso="TOT", $classe="TOT", $turma="TOT"){
          $contador=0;
          foreach ($this->horario as $hor) {
            if(seComparador($classe, $hor["classe"]) && seComparador($turma, $hor["turma"])){
                $contador++;
            }
          }
          return completarNumero($contador);
        }
        
        public function percentagem($valor1, $valor2){
            if($valor1==0){
                $perc = "0";
            }else{
                $perc=0;
                $perc = ($valor2/$valor1)*100;
            }
            if($perc==100){
                return "100%";
            }else{
                return number_format($perc, 2)."%";
            }
        }

        public function seCriarNovaFolha($mamale, $chadrack){
            if($mamale=="TOT"){
                return $chadrack;
            }else{
                return $mamale."<div style='page-break-after: always;'>".$this->fundoDocumento("../../../").$chadrack."</div></div>";
            }
        }
    }
    new relatorioPorCurso(__DIR__);  
?>