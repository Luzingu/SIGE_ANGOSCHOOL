<?php if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');

    class lista extends funcoesAuxiliares{

        function __construct($caminhoAbsoluto){
            $this->idPCurso = isset($_GET["idPCurso"])?$_GET["idPCurso"]:null;
            
            parent::__construct("Rel-Lista de Resultados");
            $this->idPAno = $this->idAnoActual;
            $this->numAno();
            $this->classe=10;
            if($this->verificacaoAcesso->verificarAcesso("", ["processarResultados"], [], "")){
                $this->listaInscritos();              
            }else{
                 $this->negarAcesso();
            }
            
        }

         private function listaInscritos(){
            $condicaoCurso =" ";

            $this->conDb("inscricao");

            $gestor = $this->selectArray("gestorvagas", [], ["idGestEscola"=>$_SESSION["idEscolaLogada"], "idGestAno"=>$this->idPAno, "idGestCurso"=>$this->idPCurso, "estadoTransicaoCurso"=>['$in'=>array("V", "Y")]]);


            $cabecalho[] = array('titulo'=>"Ordem", "tituloDb"=>"posicaoApuramento");
            $cabecalho[] = array('titulo'=>"Nome do Aluno", "tituloDb"=>"nomeAluno");

            if(valorArray($gestor, "criterioTeste")=="exameAptidao"){
                $cabecalho[] = array('titulo'=>"Sexo", "tituloDb"=>"sexoAluno");
                $cabecalho[] = array('titulo'=>"Data de Nasc.", "tituloDb"=>"dataNascAluno");
                
                if(valorArray($gestor, "tipoAutenticacao")!="nome"){
                    for($i=1; $i<=valorArray($gestor, "numeroProvas"); $i++){
                        $cabecalho[] = array('titulo'=>valorArray($gestor, "nomeProva".$i), "tituloDb"=>"notaExame".$i);
                    }
                }
                $cabecalho[] = array('titulo'=>"Média", "tituloDb"=>"mediaExames");               
            }else if(valorArray($gestor, "criterioTeste")=="factor"){
                //$cabecalho[] = array('titulo'=>"M. Disc. N.", "tituloDb"=>"mediaDiscN");
                $cabecalho[] = array('titulo'=>"Data de Nasc.", "tituloDb"=>"dataNascAluno");
                $cabecalho[] = array('titulo'=>"Sexo", "tituloDb"=>"sexoAluno");
                $cabecalho[] = array('titulo'=>"%", "tituloDb"=>"percentagemAcumulada");
                                
            }else{
                //$cabecalho[] = array('titulo'=>"M. Disc. N.", "tituloDb"=>"mediaDiscN");
                $cabecalho[] = array('titulo'=>"Data de Nasc.", "tituloDb"=>"dataNascAluno");
                $cabecalho[] = array('titulo'=>"Sexo", "tituloDb"=>"sexoAluno");                                
            }
            $cabecalho[] = array('titulo'=>"OBS", "tituloDb"=>"obsFinal");


            $this->conDb();
            $this->html .="<html style='margin-left:20px; margin-right:20px;'>
            <head>
                <title>Resultados</title>
                <style>
                    *{
                        font-size:11pt !important;
                    }
                    table tr td{
                        padding:2px;
                    }
                </style>
            </head>
            <body>".$this->fundoDocumento("../../../")."
            <div style='position: absolute;'><div style='margin-top: -30px; width:250px;'>".$this->assinaturaDirigentes(7)."</div></div>".$this->cabecalho(); 

               $this->html .="<p style='".$this->text_center.$this->bolder."'>RESULTADOS DO TESTE DE ADIMISSÃO DO ANO LECTIVO ".$this->numAno."</p>";
            
            $this->conDb();

            $cur = $this->selectArray("nomecursos", [], ["idPNomeCurso"=>$this->idPCurso]);

            if(valorArray($cur, "tipoCurso")=="pedagogico"){
                $this->html .="<p style='".$this->maiuscula.$this->miniParagrafo."'>CURSO: <strong>".valorArray($cur, "areaFormacaoCurso")."</strong></p>
                <p style='".$this->maiuscula."'>OPÇÃO: <strong>".valorArray($cur, "nomeCurso")."</strong></p>";
            }else if(valorArray($cur, "tipoCurso")=="tecnico"){
                $this->html .="<p style='".$this->maiuscula.$this->miniParagrafo."'>ÁREA DE FORMAÇÃO: <strong>".valorArray($cur, "areaFormacaoCurso")."</strong></p>
                <p style='".$this->maiuscula."'>CURSO: <strong>".valorArray($cur, "nomeCurso")."</strong></p>";
            }else{
                $this->html .="<p style='".$this->maiuscula."'>CURSO: <strong>".valorArray($cur, "nomeCurso")."</strong></p>";
            }

            $this->html .="<table style='".$this->tabela." width:100%;'>
                    <tr style='".$this->corDanger."'>";
            foreach ($cabecalho as $cab) {
                $this->html .="<td style='".$this->text_center.$this->bolder.$this->border()."'>".$cab["titulo"]."</td>";
            }
            $this->html .="</tr>
                ";

            $periodos[] = array("name"=>"reg", "label"=>"REGULAR");
            if(valorArray($this->sobreUsuarioLogado, "periodosEscolas")=="regPos"){
                $periodos[] = array("name"=>"pos", "label"=>"PÓS-LABORAL");
            }
            $this->conDb("inscricao");
            $alunosRegular = $alunos = $this->selectArray("alunos", [], ["idAlunoAno"=>$this->idPAno, "idAlunoEscola"=>$_SESSION["idEscolaLogada"], "inscricao.idInscricaoCurso"=>valorArray($gestor, "idGestCurso"), "inscricao.periodoApuramento"=>"reg"], ["inscricao"], "", [], array("inscricao.posicaoApuramento"=>1));

            $alunosPosLaboral = $alunos = $this->selectArray("alunos", [], ["idAlunoAno"=>$this->idPAno, "idAlunoEscola"=>$_SESSION["idEscolaLogada"], "inscricao.idInscricaoCurso"=>valorArray($gestor, "idGestCurso"), "inscricao.periodoApuramento"=>"pos"], ["inscricao"], "", [], array("inscricao.posicaoApuramento"=>1));
            

            foreach($periodos as $periodo){
                if($periodo["name"]=="reg"){
                   $alunos = $alunosRegular;
                }else{
                    $alunos = $alunosPosLaboral;
                }
                $this->html .="<tr ><td colspan='".count($cabecalho)."' style='".$this->border().$this->text_center.$this->bolder."'>PERÍODO: ".$periodo["label"]."</td></tr>";

                $contador=0;
                foreach ($alunos as $aluno) {
                    $contador++;
                    
                    if($contador%2==0){
                       $this->html .="<tr style='".$this->backGround("rgb(220,220,220)")."'>";
                    }else{
                        $this->html .="<tr>";
                    }

                    foreach ($cabecalho as $cab) {
                        $valor = $cab["tituloDb"];

                        if($valor=="notaExame1" || $valor=="notaExame2" || $valor=="notaExame3" || $valor=="mediaExames"){
                            $this->html .=$this->tratarVermelha(nelson($aluno, $valor, "inscricao"), "", 10);
                        }else if($valor=="obsFinal"){

                            $this->html .=$this->obsFinal($aluno["inscricao"]["obsApuramento"], $aluno["sexoAluno"]);
                        }else if($valor=="nomeAluno"){
                            $this->html .="<td style='".$this->border()."'>".$aluno[$valor]."</td>";
                        }else if($valor=="posicaoApuramento"){
                            $this->html .="<td style='".$this->border().$this->text_center."'>".completarNumero($contador)."</td>";
                        }else if($valor=="dataNascAluno"){
                            $this->html .="<td style='".$this->border().$this->text_center."'>".converterData($aluno["dataNascAluno"])."</td>";
                        }else{
                            $valor = isset($aluno[$valor])?$aluno[$valor]:$aluno["inscricao"][$valor];
                            $this->html .="<td style='".$this->border().$this->text_center."'>".$valor."</td>";
                        }
                        
                    }
                    $this->html .="</tr>";
                }
            }
            $this->conDb();
            $this->html .="</table>
            <p>".$this->rodape()."</p>
            <div style='width:50%;'>".$this->assinaturaDirigentes("mengi")."</div>

            <div style='width:50%; margin-left:50%; margin-top:-100px;'>
                <p style='".$this->text_center."'>A Comissão</p>
                <p style='".$this->text_center."'>______________________________</p>
                <p style='".$this->text_center."'>______________________________</p>
                <p style='".$this->text_center."'>______________________________</p>
                <p style='".$this->text_center."'>______________________________</p>
                <p style='".$this->text_center."'>______________________________</p>
            </div>";

            $this->exibir("", "Lista dos Resultados-".valorArray($cur, "abrevCurso")."-".$this->numAnoActual);
        }

        private function  obsFinal ($obsFinal, $sexoAluno){
            $retorno ="<td style='".$this->border().$this->text_center."'></td>";
            if($obsFinal=="A"){
                if($sexoAluno=="M"){
                    $retorno ="<td style='".$this->border().$this->text_center.$this->azul."'>Admitido</td>";
                }else{
                    $retorno ="<td style='".$this->border().$this->text_center.$this->azul."'>Admitida</td>";
                }
            }else if($obsFinal=="P"){
                 $retorno ="<td style='".$this->border().$this->text_center."'>--</td>";
            }else if($obsFinal=="R"){
                if($sexoAluno=="M"){
                    $retorno ="<td style='".$this->border().$this->text_center.$this->vermelha."'>Não admitido</td>";
                }else{
                    $retorno ="<td style='".$this->border().$this->text_center.$this->vermelha."'>Não admitida</td>";
                }
            }
            return $retorno;
        }
    }

new lista(__DIR__);
?>