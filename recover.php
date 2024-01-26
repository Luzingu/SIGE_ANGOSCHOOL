<?php session_start();
    include 'funcoesAuxiliares.php';
    include 'manipulacaoDadosMae.php';

    $m = new manipulacaoDadosMae(__DIR__); ?>
    
    <form method="POST" enctype="multipart/form-data">
       <input type="file" name="arquivo" placeholder="Pesquisar" size="30"><br/><br/>
       <button type="submit" name="btnEnviar">Pesquiar</button>   
    </form>
    <?php if(isset($_POST['btnEnviar'])){

        if(isset($_FILES['arquivo']) ){

          $arquivo = new DomDocument();
          $arquivo->load($_FILES['arquivo']['tmp_name']);            
            
          $linhas = $arquivo->getElementsByTagName("Row");

          foreach($linhas as $linha){
            $nomeAluno = trim($linha->getElementsByTagName("Data")->item(0)->nodeValue);
            $sexoAluno = trim($linha->getElementsByTagName("Data")->item(1)->nodeValue);
            $sexoAluno = substr($sexoAluno, 0, 1);
            $idPCurso = trim($linha->getElementsByTagName("Data")->item(2)->nodeValue);
            $classe = trim($linha->getElementsByTagName("Data")->item(3)->nodeValue);
            $turma = trim($linha->getElementsByTagName("Data")->item(4)->nodeValue);
            $periodo = trim($linha->getElementsByTagName("Data")->item(5)->nodeValue);
            if($idPCurso==0){
                $idPCurso="";
            }
            
            if(count($m->selectArray("alunosmatriculados LEFT JOIN aluno_escola ON idPMatricula=idFMatricula", "*","nomeAluno=:nomeAluno AND idMatEscola=:idMatEscola", [$nomeAluno, $_SESSION["idEscolaLogada"]]))<=0){
                
                $jaExistemNumero="V";
                while ($jaExistemNumero=="V"){
                    $characters= "1234567890";
                    $numeroUnico = substr(str_shuffle($characters),0, 4)."ANGOS1".substr(str_shuffle($characters),0, 3);
    
                    if(count($m->selectArray("alunosmatriculados", "*","numeroInterno=:numeroInterno", [$numeroUnico]))<=0){
                        $jaExistemNumero="F";
                    }      
                }
        
                if($m->inserir("alunosmatriculados", "nomeAluno, sexoAluno, dataNascAluno, comunaNascAluno, municNascAluno, provNascAluno, paisNascAluno, paiAluno, maeAluno, biAluno, dataEBIAluno, encarregadoEducacao, telefoneAluno, numeroInterno, fotoAluno, estadoActividade, emailAluno, estadoAcessoAluno, deficienciaAluno, tipoDeficienciaAluno, senhaAluno, dataCaducidadeBI",  [$nomeAluno, "M", "2017-07-06", NULL, 5, 7, 7, NULL, NULL, NULL, NULL, NULL, NULL, $numeroUnico, "usuario_default.png", "A", NULL, "A",NULL, NULL, "0c7".criptografarMd5("0000")."ab", NULL])=="sim"){
    
                    $idPMatricula = $m->selectUmElemento("alunosmatriculados", "idPMatricula", "numeroInterno=:numeroInterno", [$numeroUnico]);
    
                    $expl = explode("ANGOS", $numeroUnico);
                    $numeroProcesso = $expl[0].$expl[1];
    
                    $m->inserir("aluno_escola", "idFMatricula, idMatAno, idMatEscola, idMatEntidade, estadoAluno, dataMatricula, horaMatricula, periodoAluno, numeroProcesso, inscreveuSeAntes, idTabelaInscricao, idMatAnexo, idMatCurso, classeActualAluno, estadoDeDesistenciaNaEscola", [$idPMatricula, 9266, $_SESSION["idEscolaLogada"], 35, "A", "2022-09-30", "13:00:00", $periodo, $numeroProcesso, "F", NULL, 24, $idPCurso, $classe, "A"]);
    
    
                    $m->inserir("alunosreconfirmados", "idReconfMatricula, dataReconf, horaReconf, classeReconfirmacao, tipoEntrada, chaveReconf, idReconfProfessor, idReconfAno, idReconfEscola", [$idPMatricula, "2022-12-01", "13:00:00", $classe, "novaMatricula", $idPMatricula."-9266-".$_SESSION["idEscolaLogada"], 35, 9266, $_SESSION["idEscolaLogada"]]);
    
                    if(count($m->selectCondClasseCurso("array", "listaturmas", "*", "idListaEscola=:idListaEscola AND classe=:classe AND nomeTurma=:nomeTurma", [$_SESSION['idEscolaLogada'], $classe, $turma, $idPCurso], $classe, " AND idListaCurso=:idListaCurso"))<=0){
    
                        $m->inserir("listaturmas", "nomeTurma, designacaoTurma, classe, idListaEscola, idListaAno, periodoTurma, idAnexoTurma, idListaCurso", [$turma, $turma, $classe, $_SESSION["idEscolaLogada"], 9266, $periodo, 24, $idPCurso]);
                    }
                    echo $m->inserir("turmas", "idTurmaMatricula, idTurmaAno, nomeTurma, designacaoTurma, idTurmaEscola, classeTurma", [$idPMatricula, 9266, $turma, $turma, $_SESSION["idEscolaLogada"], $classe]);
                }else{
                    echo "Não foi possível<br/>";
                }
            }else{
                echo "Já existe<br/>";
            }
          }
        }

    } ?>

    