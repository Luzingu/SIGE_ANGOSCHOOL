<?php 
  include_once $_SESSION["directorioPaterno"].'angoschool/funcoesAuxiliares.php';
  
  function returnCategoriaInstituicao($categoria){
      if($categoria=="politecnico"){
        return "Politécnico";
      }else if($categoria=="magisterio"){
        return "Magistério";
      }else if($categoria=="liceu"){
        return "Liceu";
      }
   }

   function classeInicial($manipulacaoDados="", $idPEscola){
      $classeRetorno="";
      if(seEnsinoSecundario($idPEscola)){
         foreach ($manipulacaoDados->selectArray("cursos LEFT JOIN nomecursos ON idPNomeCurso=idFNomeCurso", "*", "idCursoEscola=:idCursoEscola AND estadoCurso=:estadoCurso", [$idPEscola, "A"], "nomeCurso ASC LIMIT 1") as $c) {
            $classeRetorno="reg-10-".$c->idPNomeCurso;
         }          
      }

      if(seEnsinoBasico($idPEscola)){
         $classeRetorno="reg-7-";
      }
      if(seEnsinoPrimario($idPEscola)){
        $classeRetorno="reg-0-";
      }
      return $classeRetorno;
    }

    function seEnsinoPrimario($idPEscola){
      curtina($_SESSION["directorioPaterno"].'angoschool/'.directorioEmExecucao().'/manipulacaoDados.php');
      $manipulacaoDados = new manipulacaoDados(__DIR__, "sim");

      $nivelEscola = $manipulacaoDados->selectUmElemento("escolas", "nivelEscola", "idPEscola=:idPEscola", [$idPEscola]);
      if($nivelEscola=="primaria" || $nivelEscola=="primBasico" || $nivelEscola=="primMedio" || $nivelEscola=="complexo"){
        return true;
      }
    }


    function seEnsinoBasico($idPEscola){
      curtina($_SESSION["directorioPaterno"].'angoschool/'.directorioEmExecucao().'/manipulacaoDados.php');
      $manipulacaoDados = new manipulacaoDados(__DIR__, "sim");

      $nivelEscola = $manipulacaoDados->selectUmElemento("escolas", "nivelEscola", "idPEscola=:idPEscola", [$idPEscola]);
      if($nivelEscola=="basica" || $nivelEscola=="primBasico" || $nivelEscola=="basicoMedio" || $nivelEscola=="complexo"){
        return true;
      }
    }

    function seEnsinoSecundario($idPEscola){
      curtina($_SESSION["directorioPaterno"].'angoschool/'.directorioEmExecucao().'/manipulacaoDados.php');
      $manipulacaoDados = new manipulacaoDados(__DIR__, "sim");

      $nivelEscola = $manipulacaoDados->selectUmElemento("escolas", "nivelEscola", "idPEscola=:idPEscola", [$idPEscola]);
      if($nivelEscola=="primMedio" || $nivelEscola=="basicoMedio" || $nivelEscola=="media" || $nivelEscola=="complexo"){
      return true;
      }      
    }

    function retornarClassesPorCurso($manipulacaoDados="", $estadoCurso="", $porPeriodo="sim", $comFinalistas="nao", $comAlunosSemCurso="nao", $idPEscola=""){
        $classes="";

        $periodos[]="reg";
        if(valorArray($manipulacaoDados->sobreUsuarioLogado, "periodosEscolas")=="regPos" && $porPeriodo=="sim"){
          $periodos[]="pos";
        }         
        if($estadoCurso!=""){
          $estadoCurso="  AND estadoCurso='".$estadoCurso."'";
        }
          if(seEnsinoPrimario($idPEscola)){
            $classes .="<optgroup label='Primária'><option value='reg-0-'>Iniciação</option>";

            foreach ($periodos as $p) {
              for ($i=1; $i<=6 ; $i++) { 
                  $classes .="<option value='".$p."-".$i."-'>".classeExtensa($i, "nao", "sim");

                  if($porPeriodo=="sim"){
                    $classes .=" - ".periodoExtenso($p);
                  }
                  $classes.="</option>";
              }

              if($comFinalistas=="sim"){
                foreach ($manipulacaoDados->selectArray("anolectivo LEFT JOIN aluno_escola ON idPAno=idMatFAnoEP LEFT JOIN alunosmatriculados ON idPMatricula=idFMatricula", "DISTINCT idMatFAnoEP", "idMatEscola=:idMatEscola AND periodoAluno=:periodoAluno", [$idPEscola, $p], "numAno ASC") as $ano) {

                  $classes .="<option value='".$p."-EP_".$ano->idMatFAnoEP."'>TÉC-PRIM - ".$manipulacaoDados->selectUmElemento("anolectivo", "numAno", "idPAno=:idPAno", [$ano->idMatFAnoEP]);
                  if($porPeriodo=="sim"){
                    $classes .=" - ".periodoExtenso($p);
                  }
                  $classes .="</option>";
                }
              }

            }
            $classes .="</optgroup>";
          }
          if(seEnsinoBasico($idPEscola)){
             $classes .="<optgroup label='Iº Ciclo'>";
            foreach ($periodos as $p) {
              for ($i=7; $i<=9 ; $i++) { 
                  $classes .="<option value='".$p."-".$i."-'>".classeExtensa($i, "nao", "sim");

                  if($porPeriodo=="sim"){
                    $classes .=" - ".periodoExtenso($p);
                  }
                  $classes .="</option>";
              }

              if($comFinalistas=="sim"){
                foreach ($manipulacaoDados->selectArray("anolectivo LEFT JOIN aluno_escola ON idPAno=idMatFAnoEB LEFT JOIN alunosmatriculados ON idPMatricula=idFMatricula", "DISTINCT idMatFAnoEB", "idMatEscola=:idMatEscola AND periodoAluno=:periodoAluno", [$idPEscola, $p], "numAno ASC") as $ano) {

                  $classes .="<option value='".$p."-EB_".$ano->idMatFAnoEB."'>TÉC-BÁSICO - ".$manipulacaoDados->selectUmElemento("anolectivo", "numAno", "idPAno=:idPAno", [$ano->idMatFAnoEB]);
                  if($porPeriodo=="sim"){
                    $classes .=" - ".periodoExtenso($p);
                  }
                  $classes .="</option>";
                }
              }
            }
            $classes .="</optgroup>";
          }
          if(seEnsinoSecundario($idPEscola)){
            $classes .="<optgroup label='IIº Ciclo'>";

            foreach ($periodos as $p) {

              if($comAlunosSemCurso=="sim" && seEnsinoBasico($idPEscola)){
                  $classes .="<option value='".$p."-LUZ_L'>PENDENTES";
                  if($porPeriodo=="sim"){
                    $classes .=" - ".periodoExtenso($p);
                  }
                  $classes .="</option>";
              }

             foreach ($manipulacaoDados->selectArray("cursos LEFT JOIN nomecursos ON idPNomeCurso=idFNomeCurso", "*", "idCursoEscola=:idCursoEscola".$estadoCurso, [$idPEscola], "nomeCurso ASC") as $c) {
               
                for ($i=10; $i<=(9+$c->duracao); $i++) { 
                  $classes .="<option value='".$p."-".$i."-".$c->idPNomeCurso."'>".$c->abrevCurso." - ".classeExtensa($i, $c->sePorSemestre, "sim");
                  if($porPeriodo=="sim"){
                    $classes .=" - ".periodoExtenso($p);
                  }
                  $classes .="</option>";
                }
             }
            }          
            $classes .="</optgroup>";
            if($comFinalistas=="sim"){
               $classes .="<optgroup label='Finalista'>";

               foreach ($periodos as $p) {
                foreach ($manipulacaoDados->selectArray("cursos LEFT JOIN nomecursos ON idPNomeCurso=idFNomeCurso", "*", "idCursoEscola=:idCursoEscola".$estadoCurso, [$idPEscola], "nomeCurso ASC") as $c) {

                  foreach ($manipulacaoDados->selectArray("anolectivo LEFT JOIN aluno_escola ON idPAno=idMatFAno LEFT JOIN alunosmatriculados ON idPMatricula=idFMatricula", "DISTINCT idMatFAno", "idMatEscola=:idMatEscola AND idMatCurso=:idMatCurso AND periodoAluno=:periodoAluno", [$idPEscola, $c->idPNomeCurso, $p], "numAno ASC") as $ano) {
                  
                    $classes .="<option value='".$p."-FIN_".$ano->idMatFAno."-".$c->idPNomeCurso."'>".$c->abrevCurso." - ".$manipulacaoDados->selectUmElemento("anolectivo", "numAno", "idPAno=:idPAno", [$ano->idMatFAno]);
                    if($porPeriodo=="sim"){
                      $classes .=" - ".periodoExtenso($p);
                    }
                    $classes .="</option>";
                  }
                }
              }
              $classes .="</optgroup>";
            }
          }
        echo $classes;
    }
?>