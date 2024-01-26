<?php 
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once $_SESSION["directorioPaterno"].'angoschool/manipulacaoDadosMae.php';
    include_once $_SESSION["directorioPaterno"].'angoschool/areaDireccaoProvincial/funcoesAuxiliares.php';

  class manipulacaoDados extends manipulacaoDadosMae{
    public $idAnoActual=0; 
    public $numAnoActual=0;
    public $modeloPauta="_mod_2020";
    private $caminhoRetornar="";

    function __construct($caminhoAbsoluto, $areaVisualizada=""){

        $caminho = explode($_SESSION["barrasCaminhos"], $caminhoAbsoluto);
        $this->caminhoRetornar = "";
        for($i=1; $i<=count($caminho)-$_SESSION["numeroRecursividade"]; $i++){
          $this->caminhoRetornar .="../";
        }
         parent::__construct($caminhoAbsoluto, $areaVisualizada);

        $sobreAno = $this->selectArray("anolectivo", "*", "estado=:estado", ["V"], "idPAno DESC");
        $this->idAnoActual = valorArray($sobreAno, "idPAno");
        $this->numAnoActual = valorArray($sobreAno, "numAno");
    }

    
    
}

?>