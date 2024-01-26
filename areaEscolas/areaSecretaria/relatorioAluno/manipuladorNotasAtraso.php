<?php

function listarNotasAtrasoAlunos($m){

    $sobreAluno = $m->selectArray("alunosmatriculados", [], ["idPMatricula"=>$m->idPMatricula, "pautas.idPautaCurso"=>$m->idPCurso, "pautas.classePauta"=>$m->classe, "dadosatraso.idDEscola"=>$_SESSION['idEscolaLogada'], "dadosatraso.classeAnterior"=>$m->classe, "dadosatraso.idCurso"=>$m->idPCurso], ["escola", "pautas", "dadosatraso"]);
    $sobreAluno = $m->sobreEscreverAluno($sobreAluno, $m->idPCurso);
    $pautaAluno = array();
    $i=0;
    foreach($sobreAluno as $nota){
        foreach($m->disciplinas($m->idPCurso, $m->classe, valorArray($sobreAluno, "periodoAluno", "escola"), "", array(), array(), ["idPNomeDisciplina", "disciplinas.classeDisciplina", "nomeDisciplina", "disciplinas.semestreDisciplina", "disciplinas.semestreDisciplina", "disciplinas.continuidadeDisciplina", "disciplinas.tipoDisciplina"], "todas") as $curriculo){

            if($curriculo["disciplinas"]["classeDisciplina"]==$nota["pautas"]["classePauta"] && $curriculo["disciplinas"]["semestreDisciplina"]==$nota["pautas"]["semestrePauta"] && $curriculo["idPNomeDisciplina"]==$nota["pautas"]["idPautaDisciplina"]){

                $pautaAluno[$i]=$nota;
                $pautaAluno[$i]["nomeDisciplina"]=$curriculo["nomeDisciplina"];
                $pautaAluno[$i]["idPNomeDisciplina"]=$curriculo["idPNomeDisciplina"];
                $pautaAluno[$i]["semestreDisciplina"]=$curriculo["disciplinas"]["semestreDisciplina"];
                $pautaAluno[$i]["continuidadeDisciplina"]=$curriculo["disciplinas"]["continuidadeDisciplina"];
                $pautaAluno[$i]["tipoDisciplina"]=$curriculo["disciplinas"]["tipoDisciplina"];
                $i++;
            }
        }
    }
    $array[0] = $m->cabecalhoTermpAproveitamento(valorArray($sobreAluno, "anoAnterior", "dadosatraso"), $m->idPCurso, $m->classe, "notasAtraso");
    $array[1] = $pautaAluno;
    echo json_encode($array);
}

function gravarNotasAtraso($m){

  $aluno = $m->selectArray("alunosmatriculados", [],["idPMatricula"=>$m->idPMatricula, "escola.idMatEscola"=>$_SESSION['idEscolaLogada']], ["escola"], 1);
    $aluno = $m->sobreEscreverAluno($aluno, $m->idPCurso);

  $podeGravar="nao";
  if(count(listarItensObjecto($aluno, "reconfirmacoes", ["idReconfEscola=".$_SESSION["idEscolaLogada"], "classeReconfirmacao=".$m->classe, "idMatCurso=".$m->idPCurso]))<=0){
    $podeGravar="sim";
  }
  if($podeGravar=="sim"){
    $chave1 = $m->idPMatricula."-".$m->idPCurso."-".$m->classe."-".$_SESSION["idEscolaLogada"];
    $m->inserirObjecto("alunos_".valorArray($aluno, "grupo"), "dadosatraso", "idDAtraso", "idDAMatricula, idDEscola, idCurso, classeAnterior, chaveEA, anoAnterior", [$m->idPMatricula, $_SESSION["idEscolaLogada"], $m->idPCurso, $m->classe, $chave1, 9266], ["idPMatricula"=>$m->idPMatricula]);
    $m->gravarPautasAluno($m->idPMatricula, $m->classe, "nao", array(), $aluno);
  }
  listarNotasAtrasoAlunos($m);
}

function alterarNotasAtraso($m){
    $notas = json_decode($_POST["notas"]);
    $dadosAtraso = json_decode($_POST["dadosAtraso"]);

    if(trim($dadosAtraso[0]->anoLectivo)=="" || $dadosAtraso[0]->anoLectivo==NULL){
      echo "O campo ano lectivo é obrigatório.";
    }else if((trim($dadosAtraso[0]->turma)=="" || trim($dadosAtraso[0]->turma)==NULL)){
      echo "O campo turma é obrigatório.";
    }else if((trim($dadosAtraso[0]->numeroPauta)=="" || trim($dadosAtraso[0]->numeroPauta)==NULL)){
        echo "O campo número de pauta é obrigatório.";
    }else if((trim($dadosAtraso[0]->numero)=="" || trim($dadosAtraso[0]->numero)==NULL)){
        echo "O campo número é obrigatório.";
    }else{

      $sobreAluno = $m->selectArray("alunosmatriculados", [], ["idPMatricula"=>$m->idPMatricula], ["escola"], 1);
      $sobreAluno = $m->sobreEscreverAluno($sobreAluno, $m->idPCurso);

      $m->editarItemObjecto("alunos_".valorArray($sobreAluno, "grupo"), "dadosatraso", "anoAnterior, turmaAnterior, numeroAnterior, numeroPauta", [$dadosAtraso[0]->anoLectivo, $dadosAtraso[0]->turma, $dadosAtraso[0]->numero, $dadosAtraso[0]->numeroPauta], ["idPMatricula"=>$m->idPMatricula], ["idDAtraso"=>$dadosAtraso[0]->idDAtraso]);

      $msgRetorno=array();
      foreach ($notas as $nota) {
        $m->manipuladorPautas->camposAvaliacao = $nota->avaliacoesQuantitativas;
        $m->manipuladorPautas->calcularMfdMod20();
        $m->manipuladorPautas->calcularMediaFinalMod2020();

        $strings="";
        $valores=array();
        foreach($m->manipuladorPautas->camposAvaliacao as $a){
          if($strings!=""){
              $strings .=",";
          }
          $strings .=$a->name;
          $valores[]=$a->valor;
        }
        $m->editarItemObjecto("alunos_".valorArray($sobreAluno, "grupo"), "pautas", $strings, $valores, ["idPMatricula"=>$m->idPMatricula], ["idPPauta"=>$nota->idPPauta]);

        if($nota->tipoCurso=="tecnico"){
          $m->manipuladorPautas->calcularClassificacaoFinalDaDisciplina($m->idPMatricula, $nota->idPDisciplina, "", $nota->semestrePauta, "T");
        }
      }
    }
}
 ?>
