<?php
	session_start();
	include_once 'manipulacaoDadosMae.php';
	$m = new manipulacaoDadosMae("");


  /*$caminho = explode($_SESSION["barrasCaminhos"], $caminhoAbsoluto);
  $this->caminhoRetornar = "";
  for($i=1; $i<=count($caminho)-$_SESSION["numeroRecursividade"]; $i++){
    $this->caminhoRetornar .="../";
  }
  parent::__construct($caminhoAbsoluto);*/
    

  /*actualizador($m, "escolas", "div_terit_paises", "pais", "idPPais");
  actualizador($m, "escolas", "div_terit_provincias", "provincia", "idPProvincia");
  actualizador($m, "escolas", "div_terit_municipios", "municipio", "idPMunicipio");
  actualizador($m, "escolas", "div_terit_comunas", "comuna", "idPComuna");*/

  /*actualizador($m, "entidadesprimaria", "div_terit_paises", "paisNascEntidade", "idPPais");
  actualizador($m, "entidadesprimaria", "div_terit_provincias", "provNascEntidade", "idPProvincia");
  actualizador($m, "entidadesprimaria", "div_terit_municipios", "municNascEntidade", "idPMunicipio");
  actualizador($m, "entidadesprimaria", "div_terit_comunas", "comunaNascEntidade", "idPComuna");*/

  //actualizador($m, "divisaoprofessores", "nomecursos", "idDivCurso", "idPNomeCurso");
  //actualizador($m, "divisaoprofessores", "nomedisciplinas", "idDivDisciplina", "idPNomeDisciplina");
  //actualizador($m, "divisaoprofessores", "entidadesprimaria", "idDivEntidade", "idPEntidade");
  //actualizador($m, "divisaoprofessores", "escolas", "idDivEscola", "idPEscola");

  //actualizador($m, "entidadesonline", "escolas", "idOnlineEntEscola", "idPEscola");
  //actualizador($m, "entidadesonline", "entidadesprimaria", "idOnlineEnt", "idPEntidade");
  //actualizador($m, "entidadesonline", "alunosmatriculados", "idOnlineMat", "idPMatricula");

  //actualizador($m, "galeriafotos", "escolas", "idGaleriaEscola", "idPEscola");
  //actualizador($m, "historicocontaescola", "entidadesprimaria", "idHistoricoFuncionario", "idPEntidade");
  //actualizador($m, "listaturmas", "nomecursos", "idListaCurso", "idPNomeCurso");
  //actualizador($m, "listaturmas", "escolas", "idListaEscola", "idPEscola");
  //Falta (Horario, Afonso Luzingu e mensagens)

  /*function actualizador($m, $tb1, $tb2, $id1, $id2){

    foreach($m->selectArray($tb1) as $a){
      $array = $m->selectArray($tb2, [], [$id2=>$a[$id1]]);

      $string="";
      $valores=array();
      foreach(retornarChaves($array) as $chave){
        if($chave!="_id"){

          if(isset($array[0][$chave]) && !is_object($array[0][$chave]) && seTemValorNoArray(camposAnexar(), $chave)){
            if($string!=""){
              $string .=",";
            }
            $string .=$chave;
            $valores[]=$array[0][$chave];
          }
        }
      }
      if($string!=""){
        $m->editar($tb1, $string, $valores, ["_id"=>$a["_id"]]);
      }
    }
  }*/



 ?>