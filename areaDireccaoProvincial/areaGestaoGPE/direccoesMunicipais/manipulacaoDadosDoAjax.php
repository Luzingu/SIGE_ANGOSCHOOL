<?php 
  session_start();
  include_once $_SESSION["directorioPaterno"].'angoschool/areaDireccaoProvincial/funcoesAuxiliares.php';
  include_once $_SESSION["directorioPaterno"].'angoschool/areaDireccaoProvincial/manipulacaoDadosDoAjax.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{

      function __construct($caminhoAbsoluto){
        parent::__construct();

        $this->caminhoRetornar =retornarCaminhoRecuarArquivosPhp(__DIR__);
        include_once $this->caminhoRetornar.'funcoesAuxiliares.php';
          
        $this->nomeEscola = trim(filter_input(INPUT_POST, "nomeEscola", FILTER_SANITIZE_STRING));
        $this->enderecoEscola = trim(filter_input(INPUT_POST, "enderecoEscola", FILTER_SANITIZE_STRING));
   
        $this->estadoEscola = trim(filter_input(INPUT_POST, "estadoEscola", FILTER_SANITIZE_STRING));
        $this->idPEscola = trim(filter_input(INPUT_POST, "idPEscola", FILTER_SANITIZE_NUMBER_INT));
        $this->tituloEscola = isset($_POST["tituloEscola"])?$_POST["tituloEscola"]:"";
        $this->privacidade  = trim(filter_input(INPUT_POST, "privacidade", FILTER_SANITIZE_STRING));
        $this->nivelEscola = trim(filter_input(INPUT_POST, "nivelEscola", FILTER_SANITIZE_STRING));
        $this->pais = trim(filter_input(INPUT_POST, "pais", FILTER_SANITIZE_STRING));
        $this->provincia = trim(filter_input(INPUT_POST, "provincia", FILTER_SANITIZE_STRING));
        $this->municipio = trim(filter_input(INPUT_POST, "municipio", FILTER_SANITIZE_STRING));
        $this->comuna = trim(filter_input(INPUT_POST, "comuna", FILTER_SANITIZE_STRING));

        $this->tipoInstituicao = trim(filter_input(INPUT_POST, "tipoInstituicao", FILTER_SANITIZE_STRING));

        $this->abrevNomeEscola = trim(filter_input(INPUT_POST, "abrevNomeEscola", FILTER_SANITIZE_STRING));

        $this->abrevNomeEscola2 = trim(filter_input(INPUT_POST, "abrevNomeEscola2", FILTER_SANITIZE_STRING));

        if($this->accao=="salvarEscola"){
            if($this->verificacaoAcesso->verificarAcessoAlteracao(["aGestGPE"], "", "", "FNão tens permissão de alterar os dados.")){
               $this->salvarEscola();
            }
        }else if($this->accao=="editarEscola"){
            if($this->verificacaoAcesso->verificarAcessoAlteracao(["aGestGPE"], "", "", "FNão tens permissão de alterar os dados.")){
                $this->editarEscola();
            }
        }else if($this->accao=="excluirEscola"){
             if($this->verificacaoAcesso->verificarAcessoAlteracao(["aGestGPE"], "", "", "FNão tens permissão de alterar os dados.")){
                $this->excluirEscola();
             }
        }        
          
      }

      private function salvarEscola(){
        $jaExistemNumero="V";
            while ($jaExistemNumero=="V"){
                $characters= "123456789";
                $numeroUnico = "1111ANGOS".substr(str_shuffle($characters), 0, 2);
                if(count($this->selectArray("escolas", "*", "numeroInternoEscola=:numeroInternoEscola", [$numeroUnico]))<=0){
                  $jaExistemNumero="F";
                }
            }

            if($this->inserir("escolas", "nomeEscola, endereco, estadoEscola, numeroInternoEscola, tituloEscola, privacidadeEscola, pais, provincia, municipio, comuna, tipoInstituicao, abrevNomeEscola, abrevNomeEscola2, tipoPacoteEscola, corCabecalhoTabelas, corLetrasCabecalhoTabelas, alturaCartEstudante, tamanhoCartEstudante, corCart1, corCart2, corLetrasCart, corBordasCart, chaveUnicaEscola", [$this->nomeEscola, $this->enderecoEscola, $this->estadoEscola, $numeroUnico, $this->tituloEscola, $this->privacidade, valorArray($this->sobreUsuarioLogado, "pais"), valorArray($this->sobreUsuarioLogado, "provincia"), $this->municipio, $this->comuna, $this->tipoInstituicao, $this->abrevNomeEscola, $this->abrevNomeEscola2, "Especialista", "#ffc000", "#000000", "210.00", "800.00", "#7c0644", "#fff2cc", "#000000", "#ffc000", valorArray($this->sobreUsuarioLogado, "pais")."-".valorArray($this->sobreUsuarioLogado, "provincia")."-".$this->municipio])=="sim"){

              $arr = $this->selectArray("escolas", "*", "numeroInternoEscola=:numeroInternoEscola", [$numeroUnico], "idPEscola DESC LIMIT 1");

              $idPEscola = valorArray($arr, "idPEscola");

              $this->inserir("contrato_escola", "idEscolaContrato", [$idPEscola]);
              $this->inserir("entidade_escola", "idFEntidade, idEntidadeEscola, nivelSistemaEntidade, chaveEnt, estadoActividadeEntidade", [35, $idPEscola, "Usuário_Master", "35-".$idPEscola, "I"]);

              $this->listar();
            }else{
              echo "FNão foi fossível cadastrar a escola";
            }
     
      }

      private function editarEscola(){
        if($this->editar("escolas", "nomeEscola, endereco, estadoEscola, tituloEscola, privacidadeEscola, pais, provincia, municipio, comuna, tipoInstituicao, abrevNomeEscola, abrevNomeEscola2, tipoPacoteEscola, chaveUnicaEscola", [$this->nomeEscola, $this->enderecoEscola, $this->estadoEscola, $this->tituloEscola, $this->privacidade, valorArray($this->sobreUsuarioLogado, "pais"), valorArray($this->sobreUsuarioLogado, "provincia"), $this->municipio, $this->comuna, $this->tipoInstituicao, $this->abrevNomeEscola, $this->abrevNomeEscola2, "Especialista", valorArray($this->sobreUsuarioLogado, "pais")."-".valorArray($this->sobreUsuarioLogado, "provincia")."-".$this->municipio], "idPEscola=:idPEscola", [$this->idPEscola])=="sim"){
          $this->listar();
        }else{
          echo "FNão possível editar os dados da escola.";
        }
      }

      private function excluirEscola(){
        if($this->eliminarEntidadeComposta("escolas", "idPEscola=:idPEscola", [$this->idPEscola], ["ano_escola WHERE idAnoEscola=:idAnoEscola", "contrato_escola WHERE idEscolaContrato=:idEscolaContrato", "anexos WHERE idAnexoEscola=:idAnexoEscola", "acessoareas WHERE idAcessoEscola=:idAcessoEscola", "entidade_escola WHERE idEntidadeEscola=:idEntidadeEscola"], [$this->idPEscola, $this->idPEscola, $this->idPEscola, $this->idPEscola, $this->idPEscola])=="sim"){
          $this->listar();
        }else{
          echo "FNão foi possível excluir a escola.";
        }
      }
      private function listar(){
        echo $this->selectJson("escolas LEFT JOIN div_terit_comunas ON idPComuna=comuna LEFT JOIN div_terit_municipios ON idPMunicipio=municipio", "*", "tipoInstituicao=:tipoInstituicao AND provincia=:provincia", ["DM", valorArray($this->sobreUsuarioLogado, "provincia")], "nomeEscola ASC");
      }
    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>