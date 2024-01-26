<?php 
	
	require "vendor/autoload.php";


	include '../angoschool/manipulacaoDadosMae.php';
	$manipulacao = new manipulacaoDadosMae(__DIR__);



   	$tabelasFilhadas["alunosmatriculados"][]=array("nome"=>"aluno_escola", "id"=>"idFMatricula");
    $tabelasFilhadas["alunosmatriculados"][]=array("nome"=>"alunosreconfirmados", "id"=>"idReconfMatricula") ;
    $tabelasFilhadas["alunosmatriculados"][]=array("nome"=>"avaliacaoanualaluno", "id"=>"idAvalMatricula");
    $tabelasFilhadas["alunosmatriculados"][]=array("nome"=>"turmas", "id"=>"idTurmaMatricula");


    $tabelasFilhadas["alunosmatriculados"][]=array("nome"=>"notasfinaisalunos", "id"=>"idNotaAluno");
    $tabelasFilhadas["alunosmatriculados"][]=array("nome"=>"pautas_mod_2020", "id"=>"idPautaMatricula");
    $tabelasFilhadas["alunosmatriculados"][]=array("nome"=>"pautas_arq_mod_2020", "id"=>"idPautaMatricula");

    $tabelasFilhadas["alunosmatriculados"][]=array("nome"=>"historicocontaaluno", "id"=>"idHistoricoMatricula");
    $tabelasFilhadas["alunosmatriculados"][]=array("nome"=>"dadosatraso", "id"=>"idDAMatricula");
    $tabelasFilhadas["alunosmatriculados"][]=array("nome"=>"cadeirantes", "id"=>"idCadMatricula");
    $tabelasFilhadas["alunosmatriculados"][]=array("nome"=>"transferencia_alunos", "id"=>"idTransfMatricula");
    $tabelasFilhadas["alunosmatriculados"][]=array("nome"=>"historialnotas", "id"=>"idHistMatricula");


    $tabelasFilhadas["escolas"][]=array("nome"=>"contrato_escola", "id"=>"idEscolaContrato");
    $tabelasFilhadas["escolas"][]=array("nome"=>"ano_escola", "id"=>"idAnoEscola");
    $tabelasFilhadas["escolas"][]=array("nome"=>"anexos", "id"=>"idAnexoEscola");
    $tabelasFilhadas["escolas"][]=array("nome"=>"estadoperiodico", "id"=>"idEstadoEscola");

    $tabelasFilhadas["nomedisciplinas"][]=array("nome"=>"disciplinas", "id"=>"idFNomeDisciplina");

    $tabelasFilhadas["nomecursos"][]=array("nome"=>"cursos", "id"=>"idFNomeCurso");

    $tabelasFilhadas["anolectivo"][]=array("nome"=>"ano_escola", "id"=>"idFAno");

    
    $tabelasFilhadas["entidadesprimaria"][]=array("nome"=>"entidade_escola", "id"=>"idFEntidade");
    $tabelasFilhadas["entidadesprimaria"][]=array("nome"=>"avaliacao_desempenho_professor", "id"=>"idAvalProfEnt");

    $tabelasNovoNome["aluno_escola"]="escola";
    $tabelasNovoNome["contrato_escola"]="contrato";
    $tabelasNovoNome["alunosreconfirmados"]="reconfirmacoes";
    $tabelasNovoNome["avaliacaoanualaluno"]="avaliacao_anual";
    $tabelasNovoNome["historialnotas"]="alteracoes_notas";
    $tabelasNovoNome["historicocontaaluno"]="pagamentos";
    $tabelasNovoNome["notasfinaisalunos"]="notas_finais";
    $tabelasNovoNome["pautas_arq_mod_2020"]="arquivo_pautas";
    $tabelasNovoNome["pautas_mod_2020"]="pautas";
    $tabelasNovoNome["listaturmas"]="lista_turmas";
    $tabelasNovoNome["avaliacao_desempenho_professor"]="aval_desemp";
    $tabelasNovoNome["entidade_escola"]="escola";
    $tabelasNovoNome["entidadesonline"]="online";
    $tabelasNovoNome["cadeirantes"]="cadeiras_atraso";
    $tabelasNovoNome["ano_escola"]="anos_lectivos";
    $tabelasNovoNome["transferencia_alunos"]="transferencia";


    $tabelasPrincipal[]=array("nome"=>"alunosmatriculados", "idPrincipal"=>"idPMatricula");

    /*$tabelasPrincipal[]=array("nome"=>"escolas", "idPrincipal"=>"idPEscola");
    $tabelasPrincipal[]=array("nome"=>"entidadesprimaria", "idPrincipal"=>"idPEntidade");
    $tabelasPrincipal[]=array("nome"=>"nomecursos", "idPrincipal"=>"idPNomeCurso");
    $tabelasPrincipal[]=array("nome"=>"galeriafotos", "idPrincipal"=>"");
    

    $tabelasPrincipal[]=array("nome"=>"arquivo_conselho_notas", "idPrincipal"=>"");
    $tabelasPrincipal[]=array("nome"=>"acessoareas", "idPrincipal"=>"");
    $tabelasPrincipal[]=array("nome"=>"backupsenha", "idPrincipal"=>"");
    $tabelasPrincipal[]=array("nome"=>"classesacesso", "idPrincipal"=>"");
    $tabelasPrincipal[]=array("nome"=>"div_terit_comunas", "idPrincipal"=>"");
    $tabelasPrincipal[]=array("nome"=>"div_terit_municipios", "idPrincipal"=>"");
    $tabelasPrincipal[]=array("nome"=>"div_terit_paises", "idPrincipal"=>"");
    $tabelasPrincipal[]=array("nome"=>"div_terit_provincias", "idPrincipal"=>"");
    $tabelasPrincipal[]=array("nome"=>"divisaoprofessores", "idPrincipal"=>"");
    $tabelasPrincipal[]=array("nome"=>"gerenciador_matriculas", "idPrincipal"=>"");
    $tabelasPrincipal[]=array("nome"=>"historicocontaescola", "idPrincipal"=>"");
    $tabelasPrincipal[]=array("nome"=>"mensagens", "idPrincipal"=>"");
    $tabelasPrincipal[]=array("nome"=>"niveisacesso", "idPrincipal"=>"");
    $tabelasPrincipal[]=array("nome"=>"transicao_classes", "idPrincipal"=>"");

    $tabelasPrincipal[]=array("nome"=>"nomedisciplinas", "idPrincipal"=>"idPNomeDisciplina");
    $tabelasPrincipal[]=array("nome"=>"anolectivo", "idPrincipal"=>"idPAno");
    $tabelasPrincipal[]=array("nome"=>"pagamentos_escola", "idPrincipal"=>"");
    $tabelasPrincipal[]=array("nome"=>"niveisacesso", "idPrincipal"=>"");
    $tabelasPrincipal[]=array("nome"=>"listaturmas", "idPrincipal"=>"");
    $tabelasPrincipal[]=array("nome"=>"horario", "idPrincipal"=>"");
    $tabelasPrincipal[]=array("nome"=>"entidadesonline", "idPrincipal"=>"");
    $tabelasPrincipal[]=array("nome"=>"gerenciador_periodo", "idPrincipal"=>"");
    $tabelasPrincipal[]=array("nome"=>"tabelaprecos", "idPrincipal"=>"");*/

    $manipulacao->conDb("backup");
   
    $i=0;
    foreach($tabelasPrincipal as $t){
        $idPrincipal = $t["idPrincipal"];
        $tabela = $t["nome"];
        $i++;
        
        foreach($manipulacao->selectArray($t["nome"], ["falha"=>"sim"], [], "nao") as $a){
            

            $array = array();
            if(isset($tabelasFilhadas[$t["nome"]])){
                foreach($tabelasFilhadas[$t["nome"]] as $tf){
                    
                    $nomeAtributo = $tf["nome"];
                    if(isset($tabelasNovoNome[$tf["nome"]])){
                        $nomeAtributo = $tabelasNovoNome[$tf["nome"]];
                    }

                    $arrayFilial=array();
                    $campos = retornarChaves($manipulacao->selectArray($tf["nome"], [], ["limit"=>1]));

                    echo $tf["nome"];
                    $maeElisa=0;
                    foreach($manipulacao->selectArray($tf["nome"], [$tf["id"]=>"".$a->$idPrincipal.""]) as $b){
                        echo "YES => ".$a["idPMatricula"]."<br/>";
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
            
            if($tabela=="entidadesprimaria"){
                $campos = retornarChaves($manipulacao->selectArray("dadosadicionasentidade", [], ["limit"=>1]));
                
                foreach($manipulacao->selectArray("dadosadicionasentidade", ["idDAEntidade"=>$a["idPEntidade"]]) as $b){

                    foreach($campos as $c){
                        if($c!="idPDA" && $c!="idDAEntidade" && $c!="_id"){
                            if(isset($b->$c)){
                                $array[$c] = luzl($b->$c);
                            }
                        }
                    }
                }
            }

            $camposTabelaPrincipa = retornarChaves($manipulacao->selectArray($t["nome"], [], ["limit"=>1]));
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
            $db = $client->escola;
            $db->alunosmatriculados2->insertOne($array);
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