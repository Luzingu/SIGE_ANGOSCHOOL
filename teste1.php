<?php 
    session_start();
    include_once 'manipulacaoDadosMae2.php';
    $m = new manipulacaoDadosMae2();
/*
    $array = $m->selectArray("alunos_3", [], ["pagamentos.dataPagamento"=>['$in'=>array('2024-01-04', '2024-01-05')]], []);
    $m->conDb("grupo_alunos", "destino");

    foreach($array as $a)
    {
        echo $a["idPMatricula"]."<br>";
        foreach(listarItensObjecto($a, "pagamentos") as $p)
        {
            if($p["dataPagamento"] == "2024-01-04" || $p["dataPagamento"] == "2024-01-05")
            {
                $strings="";
                $valores = array();
                foreach(retornarChaves($p) as $chave)
                {
                    if($chave != "idPHistoricoConta")
                    {
                        if($strings !="")
                        {
                            $strings .=",";
                        }
                        $strings .= $chave;
                        $valores[] = $p[$chave];
                    }
                }
                $m->inserirObjecto ("alunos_3", "pagamentos", "idPHistoricoConta", $strings, $valores, ["idPMatricula"=>$a["idPMatricula"]], "sim", "nao", $p["idPHistoricoConta"]);
            }
        }
    }*/
?>