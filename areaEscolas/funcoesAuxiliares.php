<?php
  if(session_status()!==PHP_SESSION_ACTIVE){
    session_cache_expire(60);
    session_start();
  }
  include_once ($_SERVER['DOCUMENT_ROOT'].'/angoschool/funcoesAuxiliares.php');

  function valorSessao($nomeSessao){
    if(isset($_SESSION[$nomeSessao])){
      return $_SESSION[$nomeSessao];
    }else{
      return "";
    }
  }

  function escolaPorReferencia(){
    if($_SESSION["idEscolaLogada"]==9){
        return "IPAGZ";
    }else if($_SESSION["idEscolaLogada"]==4){
        return "MS";
    }else{

    }
  }

  function voltarNaAreaPrincial($caminhoRecuar){
    if(!isset($_SESSION["tipoUsuario"])){
      echo "<script>window.location='".$caminhoRecuar."'</script>";
    }else if($_SESSION["tipoUsuario"]=="administrador"){
       echo "<script>window.location='".$caminhoRecuar."areaAdministrador'</script>";
    }else if($_SESSION["tipoUsuario"]=="professor"){
       echo "<script>window.location='".$caminhoRecuar."areaProfessor'</script>";
    }else if($_SESSION["tipoUsuario"]=="aluno"){
       echo "<script>window.location='".$caminhoRecuar."areaAluno'</script>";
    }
  }




  function cursoDir($classe, $nomeCurso){
    if($classe<=9){
      return "";
    }else{
      return "/".$nomeCurso;
    }
  }


    function retornarPeriodoTurma($manipulacaoDados, $idCurso, $classe, $turma, $idPAno=""){
      if($idPAno==""){
        $idPAno = $manipulacaoDados->idAnoActual;
      }
      return $manipulacaoDados->selectUmElemento("listaturmas", "periodoTurma", ["classe"=>$classe, "nomeTurma"=>$turma, "idPEscola"=>$_SESSION["idEscolaLogada"], "idListaAno"=>$idPAno, "idPNomeCurso"=>$idCurso]);
    }
    function retornarSemestreActivo($manipulacaoDados, $idCurso, $classe){
        if($classe<=9){
          return "I";
        }else{
          $plamedi = $manipulacaoDados->selectArray("nomecursos", ["cursos.semestreActivo"], ["idPNomeCurso"=>$idCurso, "cursos.idCursoEscola"=>$_SESSION['idEscolaLogada']], ["cursos"]);
          return valorArray($plamedi, "semestreActivo", "cursos");
        }
    }



    function seEnsinoPrimario($manipulacaoDados=""){
      if($manipulacaoDados==""){
        curtina($_SERVER['DOCUMENT_ROOT'].'/angoschool/'.directorioEmExecucao().'/manipulacaoDados.php');
        $manipulacaoDados = new manipulacaoDados();
      }
      if(valorArray($manipulacaoDados->sobreUsuarioLogado, "nivelEscola")=="primaria" || valorArray($manipulacaoDados->sobreUsuarioLogado, "nivelEscola")=="primBasico" || valorArray($manipulacaoDados->sobreUsuarioLogado, "nivelEscola")=="primMedio" || valorArray($manipulacaoDados->sobreUsuarioLogado, "nivelEscola")=="complexo"){
        return true;
      }
    }


    function seEnsinoBasico($manipulacaoDados=""){
      if($manipulacaoDados==""){
        curtina($_SERVER['DOCUMENT_ROOT'].'/angoschool/'.directorioEmExecucao().'/manipulacaoDados.php');
        $manipulacaoDados = new manipulacaoDados();
      }
      if(valorArray($manipulacaoDados->sobreUsuarioLogado, "nivelEscola")=="basica" || valorArray($manipulacaoDados->sobreUsuarioLogado, "nivelEscola")=="primBasico" || valorArray($manipulacaoDados->sobreUsuarioLogado, "nivelEscola")=="basicoMedio" || valorArray($manipulacaoDados->sobreUsuarioLogado, "nivelEscola")=="complexo"){
        return true;
      }
    }

    function seEnsinoSecundario($manipulacaoDados=""){
      if($manipulacaoDados==""){
        curtina($_SERVER['DOCUMENT_ROOT'].'/angoschool/'.directorioEmExecucao().'/manipulacaoDados.php');
        $manipulacaoDados = new manipulacaoDados();
      }
      if(valorArray($manipulacaoDados->sobreUsuarioLogado, "nivelEscola")=="primMedio" || valorArray($manipulacaoDados->sobreUsuarioLogado, "nivelEscola")=="basicoMedio" || valorArray($manipulacaoDados->sobreUsuarioLogado, "nivelEscola")=="media" || valorArray($manipulacaoDados->sobreUsuarioLogado, "nivelEscola")=="complexo"){
      return true;
      }
    }

    function retornarClasses($varClasseJs="classe", $sePorSemestre="nao", $duracao=4){
        $classes="";
    }

    function retornarClassesPorCurso($manipulacaoDados="", $estadoCurso="", $porPeriodo="sim", $comFinalistas="nao"){

      $classes="";
      $periodos[]="reg";
      if((valorArray($manipulacaoDados->sobreUsuarioLogado, "periodosEscolas")=="regPos" && $porPeriodo=="sim") || $porPeriodo == "yes"){
        $periodos[]="pos";
      }

      $condicaoCurso = ["cursos.idCursoEscola"=>$_SESSION["idEscolaLogada"]];
      if($estadoCurso!=""){
        $condicaoCurso= ["cursos.idCursoEscola"=>$_SESSION["idEscolaLogada"], "cursos.estadoCurso"=>$estadoCurso];
      }
      $listaCursos = $manipulacaoDados->selectArray("nomecursos", [], $condicaoCurso, ["cursos"], "", [], ["ordem"=>1]);

        foreach ($listaCursos as $c) {
          $classes .="<optgroup label='".$c["nomeCurso"]."'>";

          foreach ($periodos as $p) {
            foreach(listarItensObjecto($c, "classes") as $classe) {
              $classes .="<option value='".$p."-".$classe["identificador"]."-".$c["idPNomeCurso"]."'>".$c["abrevCurso"]." - ".$classe["designacao"];
              if($porPeriodo == "sim" || $porPeriodo == "yes"){
                $classes .=" - ".periodoExtenso($p);
              }
              $classes .="</option>";
            }

            if($comFinalistas=="sim"){
              $anosFinalizacao = array();
              foreach ($manipulacaoDados->selectDistinct("alunosmatriculados", "escola.idMatFAno", ["escola.idMatEscola"=>$_SESSION["idEscolaLogada"], "escola.periodoAluno"=>$p, "escola.idMatCurso"=>$c["idPNomeCurso"], "escola.idMatFAno"=>['$ne'=>""]], ["escola"]) as $ano) {
                if($ano["_id"]!="" && $ano["_id"]!=null && $ano["_id"]!=NULL){
                  $anosFinalizacao[]=$ano["_id"];
                }
              }
              foreach(distinct($anosFinalizacao) as $ano){
                $classes .="<option value='".$p."-FIN_".$ano."-".$c["idPNomeCurso"]."'>".$c["abrevCurso"]." - ".$manipulacaoDados->selectUmElemento("anolectivo", "numAno", ["idPAno"=>$ano]);
                if($porPeriodo == "sim" || $porPeriodo == "yes"){
                  $classes .=" - ".periodoExtenso($p);
                }
                $classes .="</option>";
              }
            }
          }
          $classes .="</optgroup>";
        }
        echo $classes;
        return $classes;
    }

    function classeInicial($manipulacaoDados="", $estadoCurso=""){
      if(isset($_SESSION['classeInicial'])){
        return $_SESSION['classeInicial'];
      }else{
        $classeRetorno="";
        $condicaoCurso = ["cursos.idCursoEscola"=>$_SESSION["idEscolaLogada"]];
        if($estadoCurso!=""){
          $condicaoCurso= ["cursos.idCursoEscola"=>$_SESSION["idEscolaLogada"], "cursos.estadoCurso"=>$estadoCurso];
        }
        $array = $manipulacaoDados->selectArray("nomecursos", ["idPNomeCurso"], $condicaoCurso, ["cursos"], 1, [], ["ordem"=>1]);
        $classeRetorno="reg-".$manipulacaoDados->primeiraClasse(valorArray($array, "idPNomeCurso"))."-".valorArray($array, "idPNomeCurso");
        $_SESSION['classeInicial'] = $classeRetorno;
        return $_SESSION['classeInicial'];
      }
    }
    function turnaInicial($manipulacaoDados, $idPAno=""){
      if(isset($_SESSION['turmaInicial']) && $idPAno==""){
        return $_SESSION['turmaInicial'];
      }else{
        if($idPAno==""){
          $idPAno=$manipulacaoDados->idAnoActual;
        }
        $turmaRetorno="------";
        foreach ($manipulacaoDados->selectArray("listaturmas", ["nomeTurma", "classe", "idPNomeCurso"], ["idPEscola"=>$_SESSION["idEscolaLogada"], "idListaAno"=>$idPAno], [], 1, [], ["nomeCurso"=>1, "classe"=>1, "nomeTurma"=>1]) as $tur) {
          $turmaRetorno = $tur["nomeTurma"]."-".$tur["classe"]."-".$tur["idPNomeCurso"];

        }
        return $_SESSION['turmaInicial']=$turmaRetorno;
      }
    }
    function optTurmas($manipulacaoDados){

      if(isset($_SESSION['optTurmas']) && $_SESSION['optTurmas']!=""){
        echo $_SESSION['optTurmas'];
      }else{
        $_SESSION['optTurmas']="";
        foreach(turmasEscola($manipulacaoDados) as $tur){

          $_SESSION['optTurmas'] .="<option value='".$tur["nomeTurma"]."-".$tur["classe"]."-".nelson($tur, "idPNomeCurso")."'>".$tur["abrevCurso"]." - ".classeExtensa($manipulacaoDados, nelson($tur, "idPNomeCurso"), $tur["classe"])." - ".$tur["designacaoTurma"]."</option>";
        }
        echo $_SESSION['optTurmas'];
      }
    }
    function turmasEscola($manipulacaoDados){
      if(!isset($_SESSION['turmasEscola'])>0){
        $_SESSION['turmasEscola']="";
        foreach($manipulacaoDados->turmasEscola() as $tur){
          if($_SESSION['turmasEscola']!=""){
            $_SESSION['turmasEscola'] .="-";
          }
          $idPNomeCurso = isset($tur["idPNomeCurso"])?$tur["idPNomeCurso"]:"";
          $abrevCurso = isset($tur["abrevCurso"])?$tur["abrevCurso"]:"";

          $_SESSION['turmasEscola'] .="idPNomeCurso:".$idPNomeCurso;
          $_SESSION['turmasEscola'] .=",abrevCurso:".$abrevCurso;
          $_SESSION['turmasEscola'] .=",classe:".$tur["classe"];
          $_SESSION['turmasEscola'] .=",sePorSemestre:".nelson($tur, "sePorSemestre");
          $_SESSION['turmasEscola'] .=",nomeTurma:".$tur["nomeTurma"];
          $_SESSION['turmasEscola'] .=",designacaoTurma:".$tur["designacaoTurma"];
          $_SESSION['turmasEscola'] .=",periodoTurma:".$tur["periodoTurma"];
          $_SESSION['turmasEscola'] .=",idPresidenteConselho:".valorArray($tur, "idPresidenteConselho");
        }
      }
      $posicao=0;
      $arrayTurmas=array();
      foreach(explode("-", $_SESSION['turmasEscola']) as $linha){
        foreach(explode(",", $linha) as $atributo){
          $campo = explode(":", $atributo);
          $arrayTurmas[$posicao][$campo[0]]=$campo[1];
        }
        $posicao++;
      }
      return $arrayTurmas;
    }


    function  gerenciadorNomesTurma($t, $classe, $idPCurso, $db){
      $nomeTurma ="";
      if($t==0){
           $nomeTurma="A";
       }else if($t==1){
           $nomeTurma="B";
       }else if($t==2){
           $nomeTurma="C";
       }else if($t==3){
           $nomeTurma="D";
       }else if($t==4){
           $nomeTurma="E";
       }else if($t==5){
           $nomeTurma="F";
       }else if($t==6){
           $nomeTurma="G";
       }else if($t==7){
           $nomeTurma="H";
       }else if($t==8){
           $nomeTurma="I";
       }else if($t==9){
           $nomeTurma="J";
       }else if($t==10){
           $nomeTurma="K";
       }else if($t==11){
           $nomeTurma="L";
       }else if($t==12){
           $nomeTurma="M";
       }else if($t==13){
           $nomeTurma="N";
       }else if($t==14){
           $nomeTurma="O";
       }else if($t==15){
           $nomeTurma="P";
       }else if($t==16){
           $nomeTurma="Q";
       }else if($t==17){
           $nomeTurma="R";
       }else if($t==18){
           $nomeTurma="S";
       }else if($t==19){
           $nomeTurma="T";
       }else if($t==20){
           $nomeTurma="U";
       }else if($t==21){
           $nomeTurma="V";
       }else if($t==22){
           $nomeTurma="W";
       }else if($t==23){
           $nomeTurma="X";
       }else if($t==24){
           $nomeTurma="Y";
       }else if($t==25){
           $nomeTurma="Z";
       }
       if(valorArray($db->sobreEscolaLogada, "codigoTurma")==NULL || valorArray($db->sobreEscolaLogada, "codigoTurma")==""){
          return $nomeTurma;
       }else{
          return $db->selectUmElemento("nomecursos", "abrevCurso", ["idPNomeCurso"=>$idPCurso]).$classe.$nomeTurma.valorArray($db->sobreEscolaLogada, "codigoTurma");
       }
    }

    function retornarNomeDocumento ($nomeDocumento){
      $retorno="";
      if($nomeDocumento==60){
        $retorno ="Certificado do Ensino Primário";
      }else if($nomeDocumento==90){
        $retorno="Certificado do Ensino Básico";
      }else if($nomeDocumento==120){
        $retorno="Certificado do Enisno Médio";
      }else if($nomeDocumento==1200){
        $retorno="Diploma";
      }else if($nomeDocumento==0){
        $retorno="Declaração da Iniciação";
      }else{
        $retorno="Declaração da ".$nomeDocumento.".ª classe";
      }
      return $retorno;
    }
?>
