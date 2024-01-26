<?php
    include_once '../../manipuladorPauta.php';
    $m = new manipuladorPauta();

    $classe = isset($_GET["classe"])?$_GET["classe"]:"";
    $idPCurso = isset($_GET["idPCurso"])?$_GET["idPCurso"]:"";
    $turma = isset($_GET["turma"])?$_GET["turma"]:"";

    $m = new manipuladorPauta();
    foreach($m->alunosPorTurma($idPCurso, $classe, $turma, $m->idAnoActual, array(), ["idPMatricula"]) as $a)
    {
       $m->calcularObservacaoFinalDoAluno($a["idPMatricula"]);
    }
?>
<script>
    window.history.back();
</script>
