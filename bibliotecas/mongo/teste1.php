<?php 
	
	require "vendor/autoload.php";


	include '../angoschool/manipulacaoDadosMae.php';
	$manipulacao = new manipulacaoDadosMae(__DIR__);
    $manipulacao->db = "backup";



   	$tabelasFilhadas["alunos"][]=array("nome"=>"grupos", "id"=>"idGrupoAluno");
    $tabelasFilhadas["alunos"][]=array("nome"=>"alunos_inscritos", "id"=>"idFAluno") ;

    $tabelasNovoNome["alunos_inscritos"]="inscricao";
    $tabelasNovoNome["grupos"]="grupo";

    $tabelasPrincipal[]=array("nome"=>"alunos", "idPrincipal"=>"idPAluno");
    $tabelasPrincipal[]=array("nome"=>"gestorvagas", "idPrincipal"=>"");
    $tabelasPrincipal[]=array("nome"=>"lista_grupos", "idPrincipal"=>"");


    $manipulacao->conDb();
    $i=0;
    foreach($tabelasPrincipal as $t){
        $idPrincipal = $t["idPrincipal"];
        $tabela = trim($t["nome"]);
        $i++;
        
        foreach($manipulacao->selectArray($tabela, []) as $a){
            
            $array = array();
            if(isset($tabelasFilhadas[$tabela])){
                foreach($tabelasFilhadas[$tabela] as $tf){
                    
                    $nomeAtributo = $tf["nome"];
                    if(isset($tabelasNovoNome[$tf["nome"]])){
                        $nomeAtributo = $tabelasNovoNome[$tf["nome"]];
                    }

                    $arrayFilial=array();
                    $campos = retornarChaves($manipulacao->selectArray($tf["nome"], [], ["limit"=>1]));

                    $maeElisa=0;
                    foreach($manipulacao->selectArray($tf["nome"], [$tf["id"]=>$a->$idPrincipal]) as $b){
                        $contador=0;
                        foreach($campos as $c){
                            $contador++;
                            if($contador>1){
                                if(isset($b->$c)){
                                    $arrayFilial[$maeElisa][$c] = luzl($b->$c);
                                }
                            }                            
                        }
                        $maeElisa++;
                    }
                    $array[$nomeAtributo]=$arrayFilial;
                }
            }

            $camposTabelaPrincipa = retornarChaves($manipulacao->selectArray($tabela, [], ["limit"=>1]));
            $contador=0;
            foreach($camposTabelaPrincipa as $c){
                if(isset($a->$c)){
                    $contador++;
                    if($contador>1){
                        if(isset($a->$c)){
                            $array[$c] = luzl($a->$c);
                        }
                    }
                }                
            }
            $client = new MongoDB\Client;
            $db = $client->inscricao;
            $db->$tabela->insertOne($array);
        }
    }


    function iso8859_1_to_utf8(string $s): string {
        $s .= $s;
        $len = \strlen($s);

        for ($i = $len >> 1, $j = 0; $i < $len; ++$i, ++$j) {
            switch (true) {
                case $s[$i] < "\x80": $s[$j] = $s[$i]; break;
                case $s[$i] < "\xC0": $s[$j] = "\xC2"; $s[++$j] = $s[$i]; break;
                default: $s[$j] = "\xC3"; $s[++$j] = \chr(\ord($s[$i]) - 64); break;
            }
        }

        return substr($s, 0, $j);
    }

    function utf8_to_iso8859_1($string): string {
        $s = (string) $string;
        $len = \strlen($s);

        for ($i = 0, $j = 0; $i < $len; ++$i, ++$j) {
            switch ($s[$i] & "\xF0") {
                case "\xC0":
                case "\xD0":
                    $c = (\ord($s[$i] & "\x1F") << 6) | \ord($s[++$i] & "\x3F");
                    $s[$j] = $c < 256 ? \chr($c) : '?';
                    break;

                case "\xF0":
                    ++$i;
                    // no break

                case "\xE0":
                    $s[$j] = '?';
                    $i += 2;
                    break;

                default:
                    $s[$j] = $s[$i];
            }
        }

        return substr($s, 0, $j);
    }
 ?>