<?php 
    session_start();
    include_once 'manipulacaoDadosMae2.php';
    $m = new manipulacaoDadosMae2();

    
    $campos = ["idPMatricula", "pautas.idPPauta", "pautas.idPautaDisciplina", "pautas.idPautaEscola", "grupo", "pautas.chavePauta"];
    $avaliacoes = ["macI", "avaliacoesContinuasI", "nppI", "npp2I", "nptI", "tcpI", "aaI", "mtI"];
    foreach($avaliacoes as $av)
    {
        $campos[] = "pautas.".$av;
    }

    $array = $m->selectArray("alunos_3", $campos, ["pautas.update"=>['$in'=>array('2024-01-04', '2024-01-05')]], ["pautas"]);
    $m->conDb("grupo_alunos", "destino");

    foreach($array as $p)
    {
        $strings="";
        $valores=array();
        foreach($avaliacoes as $campo)
        {

            if(isset($p["pautas"][$campo]))
            {
                if($strings !="")
                    $strings .=",";

                $strings .=$campo;
                $valores[] = $p["pautas"][$campo];
            }
        }
        if($strings!="")
        {
            $nota = $m->selectArray("alunos_3", ["pautas.mtI"], ["pautas.chavePauta"=>$p["pautas"]["chavePauta"]], ["pautas"], 1);
            if(valorArray($nota, "mtI", "pautas") != $p["pautas"]["mtI"])
            {
                $m->editarItemObjecto("alunos_3", "pautas", $strings, $valores, ["idPMatricula"=>$p["idPMatricula"]], ["chavePauta"=>$p["pautas"]["chavePauta"]]);
            }
        }
    }
?>