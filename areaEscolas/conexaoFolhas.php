<?php
if(session_status()!==PHP_SESSION_ACTIVE){
  session_cache_expire(60);
  session_start();
}
  include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/funcoesAuxiliares.php';

  curtina($_SERVER['DOCUMENT_ROOT'].'/angoschool/conexaoFolhasMae.php');
  curtina($_SERVER['DOCUMENT_ROOT'].'/angoschool/'.directorioEmExecucao().'/funcoesAuxiliares.php');
  class conexaoFolhas extends conexaoFolhasMae{

    function __construct(){
    } 
    public function folhasCss(){
        $this->folhasCssMae(); ?>
        <link href="index.css" rel="stylesheet" />
    <?php } 

    public function folhasJs() {
        $this->folhasJsMae(); ?>        
        <script type="text/javascript" src="<?php echo $this->enderecoArquivos; ?>areaEscolas/scriptGlobal1.js"></script>
       <script type="text/javascript" src="script.js"></script>
       <script type="text/javascript" src="script1.js"></script>
       <script type="text/javascript" src="script2.js"></script>
       <script type="text/javascript" src="script3.js"></script>
       <script type="text/javascript" src="script4.js"></script>
       <script type="text/javascript" src="script5.js"></script>
    <?php  } 
   
   }
		

?>