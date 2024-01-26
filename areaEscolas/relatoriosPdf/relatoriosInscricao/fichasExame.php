<?php if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');

    class lista extends funcoesAuxiliares{

        function __construct($caminhoAbsoluto){
            $this->idPCurso = isset($_GET["idPCurso"])?$_GET["idPCurso"]:null;
            $this->grupo = isset($_GET["grupo"])?$_GET["grupo"]:0;
            parent::__construct("Rel-Ficha de Exame");
            $this->idPAno = $this->idAnoActual;
            $this->numAno();

            if($this->verificacaoAcesso->verificarAcesso("", ["divisaoGrupos"], [], "")){
                $this->listaInscritos();              
            }else{
                 $this->negarAcesso();
            }
            
        }

         private function listaInscritos(){
            $condicaoCurso =" ";

            $this->conDb("inscricao");
           $alunos = $this->selectArray("alunos", ["nomeAluno", "dataNascAluno", "sexoAluno", "codigoAluno"], ["idAlunoAno"=>$this->idPAno, "idAlunoEscola"=>$_SESSION['idEscolaLogada'], "grupo.idGrupoCurso"=>$this->idPCurso, "grupo.grupoNumero"=>$this->grupo], ["grupo"],"", [], array("nomeAluno"=>1));

           $tipoAutenticacao = $this->selectUmElemento("gestorvagas", "tipoAutenticacao", ["idGestEscola"=>$_SESSION["idEscolaLogada"], "idGestAno"=>$this->idPAno, "idGestCurso"=>$this->idPCurso]);

            $this->conDb();

            $cur = $this->selectArray("nomecursos", [], ["idPNomeCurso"=>$this->idPCurso]);

            foreach ($alunos as $aluno) {

                $this->html .="<html style='padding:0px;margin:20px;'>
                <head>
                    <title>Fichas de Exame de Admissão</title>
                </head>
                <body>
                <div style='padding:0px;margin:0px; page-break-after: always; border:double black 6px; height:1060px;'><br/>".$this->cabecalho()."
                <div style='width:200px; border:solid black 1px; position:absolute;margin-top:-175px; margin-left:530px;padding:0px;".$this->text_center."'>
                    <p style='font-size:24pt;margin-top:10px;'>___/20</p>
                    <p style='margin-top:-10px;'>O(a) Avaliador(a)</p>
                    <p style='margin-top:-10px;'>______________________</p>
                  </div>

                  <br/>

                  <p style='".$this->text_center.$this->bolder." font-size:19pt;margin-top:120px;'>FICHA DE EXAME DE ADMISSÃO</p>
                  <p style='".$this->text_center.$this->maiuscula." font-size:16pt;margin-top:10px;'><strong>".valorArray($cur, "nomeCurso")."</strong></p>

                  <p style='".$this->text_center." font-size:26pt;margin-top:258px; color:red; letter-spacing:5px;'><strong>";
                  if($tipoAutenticacao=="nome"){
                    $this->html .=$aluno["nomeAluno"];
                  }else{
                    $this->html .=$aluno["codigoAluno"];
                  }
                  $this->html .="</p>

                  <p style='".$this->text_center." font-size:20pt;margin-top:250px;'>".$this->selectUmElemento("div_terit_municipios", "nomeMunicipio", ["idPMunicipio"=>valorArray($this->sobreUsuarioLogado, "municipio")]).", ".explode("-", $this->dataSistema)[0]."</p>

                </div>";
            }
            $this->exibir("", "Fichas de Exame de Admissão-".valorArray($cur, "abrevCurso")."-".$this->numAnoActual);
        }
    }
    new lista(__DIR__);
?>