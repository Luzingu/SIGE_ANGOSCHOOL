<?php
if(session_status()!==PHP_SESSION_ACTIVE){
  session_start();
}
  include_once $_SESSION["directorioPaterno"].'angoschool/funcoesAuxiliares.php';

  curtina($_SESSION["directorioPaterno"].'angoschool/conexaoFolhasMae.php');
  curtina($_SESSION["directorioPaterno"].'angoschool/'.directorioEmExecucao().'/funcoesAuxiliares.php');
class conexaoFolhas extends conexaoFolhasMae{

	 private $caminhoRetornar ="";
   function __construct($caminhoAbsoluto){
      $caminho = explode($_SESSION["barrasCaminhos"], $caminhoAbsoluto);
      $this->caminhoRetornar = "";
      for($i=1; $i<=count($caminho)-$_SESSION["numeroRecursividade"]; $i++){
        $this->caminhoRetornar .="../";
      }
      parent::__construct($caminhoAbsoluto);
    } 

    public function folhasCss(){
        $this->folhasCssMae(); ?>
        <link href="index.css" rel="stylesheet" />
    <?php } 

    public function folhasJs() {
        $this->folhasJsMae(); ?>

        <script type="text/javascript" src="<?php echo $this->caminhoRetornar; ?>areaDireccaoProvincial/scriptGlobal.js"></script>
       <script type="text/javascript" src="script.js"></script>
       <script type="text/javascript" src="script1.js"></script>
       <script type="text/javascript" src="script2.js"></script>
       <script type="text/javascript" src="script3.js"></script>
       <script type="text/javascript" src="script4.js"></script>
    <?php  } 
   
   }
		

?>