<?php
  session_start();
  include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/funcoesAuxiliares.php';
  include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/manipulacaoDadosDoAjax.php';

    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{

      function __construct(){
        parent::__construct();

        $this->nomeEscola = trim(filter_input(INPUT_POST, "nomeEscola", FILTER_SANITIZE_STRING));

        $this->estadoEscola = trim(filter_input(INPUT_POST, "estadoEscola", FILTER_SANITIZE_STRING));
        $this->idPEscola = trim(filter_input(INPUT_POST, "idPEscola", FILTER_SANITIZE_NUMBER_INT));
        $this->privacidade  = trim(filter_input(INPUT_POST, "privacidade", FILTER_SANITIZE_STRING));
        $this->pais = trim(filter_input(INPUT_POST, "pais", FILTER_SANITIZE_STRING));
        $this->provincia = trim(filter_input(INPUT_POST, "provincia", FILTER_SANITIZE_STRING));
        $this->municipio = trim(filter_input(INPUT_POST, "municipio", FILTER_SANITIZE_STRING));
        $this->comuna = trim(filter_input(INPUT_POST, "comuna", FILTER_SANITIZE_STRING));

        $this->tipoInstituicao = trim(filter_input(INPUT_POST, "tipoInstituicao", FILTER_SANITIZE_STRING));
        $this->categoriaInstituicao = trim(filter_input(INPUT_POST, "categoriaInstituicao", FILTER_SANITIZE_STRING));

        $this->abrevNomeEscola = trim(filter_input(INPUT_POST, "abrevNomeEscola", FILTER_SANITIZE_STRING));

        $this->abrevNomeEscola2 = trim(filter_input(INPUT_POST, "abrevNomeEscola2", FILTER_SANITIZE_STRING));

        $this->periodosEscolas = trim(filter_input(INPUT_POST, "periodosEscolas", FILTER_SANITIZE_STRING));

        $this->criterioEscolhaTurno = trim(filter_input(INPUT_POST, "criterioEscolhaTurno", FILTER_SANITIZE_STRING));

        if($this->accao=="salvarEscola"){
            if($this->verificacaoAcesso->verificarAcesso("", ["escolas00"])){
               $this->salvarEscola();
            }
        }else if($this->accao=="editarEscola"){
            if($this->verificacaoAcesso->verificarAcesso("", ["escolas00"])){
                $this->editarEscola();
            }
        }else if($this->accao=="excluirEscola"){
             if($this->verificacaoAcesso->verificarAcesso("", ["escolas00"])){
                $this->excluirEscola();
             }
        }

      }

      private function salvarEscola(){
        $jaExistemNumero="V";
            while ($jaExistemNumero=="V"){
                $characters= "1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ";
                $numeroUnico = "1111ANGOS".substr(str_shuffle($characters), 0, 2);
                if(count($this->selectArray("escolas", ["idPEscola"], ["numeroInternoEscola"=>$numeroUnico]))<=0){
                  $jaExistemNumero="F";
                }
            }

            if($this->inserir("escolas", "idPEscola", "nomeEscola, estadoEscola, numeroInternoEscola, privacidadeEscola, pais, provincia, municipio, comuna, tipoInstituicao, periodosEscolas, abrevNomeEscola, abrevNomeEscola2, corCabecalhoTabelas, corLetrasCabecalhoTabelas, alturaCartEstudante, tamanhoCartEstudante, corCart1, corCart2, corLetrasCart, corBordasCart, criterioEscolhaTurno, chaveUnicaEscola, serieFactura, comprovativo, insigniaUsar, cabecalhoPrincipal, rodapePrincipal", [$this->nomeEscola, $this->estadoEscola, $numeroUnico, $this->privacidade, $this->pais, $this->provincia, $this->municipio, $this->comuna, $this->tipoInstituicao, $this->periodosEscolas, $this->abrevNomeEscola, $this->abrevNomeEscola2, "#ffc000", "#000000", "210.00", "800.00", "#7c0644", "#fff2cc", "#000000", "#ffc000", $this->criterioEscolhaTurno, $numeroUnico, "MNG", "A6", "republica", "Ministério da Educação<br>".$this->nomeEscola, $this->nomeEscola.", aos "])=="sim"){

              $idPEscola = $this->selectUmElemento("escolas", "idPEscola", ["numeroInternoEscola"=>$numeroUnico]);

              $this->inserirObjecto("escolas", "contrato", "idPContrato", "idEscolaContrato", [$idPEscola], ["idPEscola"=>$idPEscola]);

              foreach($this->selectArray("entidadesprimaria", ["idPEntidade"], ["ninjaF5"=>"A"]) as $a)
              {
                  $this->inserirObjecto("entidadesprimaria", "escola", "idP_Escola", "idFEntidade, idEntidadeEscola, nivelSistemaEntidade, chaveEnt, estadoActividadeEntidade", [$a["idPEntidade"], $idPEscola, 0, $a["idPEntidade"]."-".$idPEscola, "I"], ["idPEntidade"=>$a["idPEntidade"]]);
              }

              if($this->tipoInstituicao=="escola"){

                $idAnoActual =$this->selectUmElemento("anolectivo", "idPAno", ["estado"=>"V"]);

                $this->inserirObjecto("anolectivo", "anos_lectivos", "idPAnoE", "idAnoEscola, idFAno, estadoAnoL", [$idPEscola, $idAnoActual, "V"], ["idPAno"=>$idAnoActual]);
                $this->inserirObjecto("escolas", "anexos", "idPAnexo", "idAnexoEscola, identidadeAnexo", [$idPEscola, "Sede"], ["idPEscola"=>$idPEscola]);

              }
              $this->listar();
            }else{
              echo "FNão foi fossível cadastrar a escola";
            }

      }

      private function editarEscola(){
        if($this->editar("escolas", "nomeEscola, estadoEscola, privacidadeEscola, pais, provincia, municipio, comuna, tipoInstituicao, periodosEscolas, abrevNomeEscola, abrevNomeEscola2, criterioEscolhaTurno", [$this->nomeEscola, $this->estadoEscola, $this->privacidade, $this->pais, $this->provincia, $this->municipio, $this->comuna, $this->tipoInstituicao, $this->periodosEscolas, $this->abrevNomeEscola, $this->abrevNomeEscola2, $this->criterioEscolhaTurno], ["idPEscola"=>$this->idPEscola])=="sim"){

          $this->editar("horario", "nomeEscola, pais, provincia, abrevNomeEscola2, abrevNomeEscola, comuna, municipio", [$this->nomeEscola, $this->pais, $this->provincia, $this->abrevNomeEscola2, $this->comuna, $this->provincia], ["idPEscola"=>$this->idPEscola]);

          $this->editar("divisaoprofessores", "nomeEscola, pais, provincia, abrevNomeEscola2, abrevNomeEscola, comuna, municipio", [$this->nomeEscola, $this->pais, $this->provincia, $this->abrevNomeEscola2, $this->comuna, $this->provincia], ["idPEscola"=>$this->idPEscola]);

          $this->listar();
        }else{
          echo "FNão possível editar os dados da escola.";
        }
      }

      private function excluirEscola(){
        if(count($this->selectArray("entidadesprimaria", ["idPEntidade"], ["escola.idEntidadeEscola"=>$this->idPEscola], ["escola"], 1))>0 ||
          count($this->selectArray("alunosmatriculados", ["idPMatricula"], ["escola.idMatEscola"=>$this->idPEscola], ["escola"], 1))>0){
          echo "FNão podes excluir esta escola.";
        }else{

          $this->editar("escolas", "nomeEscola, estadoEscola, numeroInternoEscola, privacidadeEscola, pais, provincia, municipio, comuna, tipoInstituicao, periodosEscolas, abrevNomeEscola, abrevNomeEscola2, corCabecalhoTabelas, corLetrasCabecalhoTabelas, alturaCartEstudante, tamanhoCartEstudante, corCart1, corCart2, corLetrasCart, corBordasCart, criterioEscolhaTurno, chaveUnicaEscola", [null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null], ["idPEscola"=>$this->idPEscola]);
          $this->listar();

        }
      }
      private function listar(){
        echo json_encode($this->selectArray("escolas", [], ["nomeEscola"=>array('$ne'=>null)], [], "", [], ["nomeEscola"=>1]));
      }
    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>
