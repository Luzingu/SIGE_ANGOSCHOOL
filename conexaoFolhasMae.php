<?php
if(session_status()!==PHP_SESSION_ACTIVE){
  session_cache_expire(60);
  session_start();
}
include_once ($_SERVER['DOCUMENT_ROOT'].'/angoschool/funcoesAuxiliares.php');

class conexaoFolhasMae{
    function __construct(){
    }

    public function folhasCssMae(){
      $protocolo = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS']=="on") ? "https" : "http");
      $this->enderecoArquivos = $protocolo."://".$_SERVER['SERVER_NAME']."/angoschool/";
      ?>
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link href="<?php echo $this->enderecoArquivos; ?>icones/logoAngoSchool4.jpeg" rel="icon">
      <title>AngoSchool</title>


      <!-- summernote -->
      <link rel="stylesheet" href="<?php echo $this->enderecoArquivos; ?>bibliotecas/plugins/summernote/summernote-bs4.css">

      <link href="<?php echo $this->enderecoArquivos; ?>bibliotecas/materialize-v1.0.0/materialize/css/materialize.css" rel="stylesheet">
       <link href="<?php echo $this->enderecoArquivos; ?>bibliotecas/css/bootstrap-theme.css" rel="stylesheet">
      <link href="<?php echo $this->enderecoArquivos; ?>bibliotecas/css/elegant-icons-style.css" rel="stylesheet" />
      <link href="<?php echo $this->enderecoArquivos; ?>bibliotecas/css/font-awesome.min.css" rel="stylesheet"/>
      <link href="<?php echo $this->enderecoArquivos; ?>bibliotecas/css/style.css" rel="stylesheet">
      <link href="<?php echo $this->enderecoArquivos; ?>bibliotecas/vendor/fontawesome-free/css/all.min.css" rel="stylesheet"/>

      <link rel="stylesheet" href="<?php echo $this->enderecoArquivos; ?>bibliotecas/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
      <!-- Toastr -->
      <link rel="stylesheet" href="<?php echo $this->enderecoArquivos; ?>bibliotecas/toastr/toastr.css">
      <!-- Theme style -->
      <link rel="stylesheet" href="<?php echo $this->enderecoArquivos; ?>bibliotecas/css/adminlte.css">

      <link href="<?php echo $this->enderecoArquivos; ?>bibliotecas/css/bootstrap.css" rel="stylesheet" />
      <link rel="stylesheet" type="text/css" href="<?php echo $this->enderecoArquivos; ?>estilo.css">
      <link rel="stylesheet" type="text/css" href="<?php echo $this->enderecoArquivos; ?>classes.css">


      <!-- Google Font: Source Sans Pro -->

      <link rel="stylesheet" href="<?php echo $this->enderecoArquivos; ?>bibliotecas/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
      <link rel="stylesheet" href="<?php echo $this->enderecoArquivos; ?>bibliotecas/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">



      <style type="text/css">
       #containers{
        padding: 15px;
       }
     </style>

      <script src="<?php echo $this->enderecoArquivos; ?>bibliotecas/jquery-3.4.1.min.js"></script>
      <script src="<?php echo $this->enderecoArquivos; ?>bibliotecas/js/bootstrap.bundle.min.js"></script>
      <script src="<?php echo $this->enderecoArquivos; ?>bibliotecas/js/bootstrap.min.js"></script>


   <?php } public function folhasJsMae($login="nao") {
    $protocolo = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS']=="on") ? "https" : "http");
    $this->enderecoArquivos = $protocolo."://".$_SERVER['SERVER_NAME']."/angoschool/";
    ?>
    <script src="<?php echo $this->enderecoArquivos; ?>bibliotecas/js/jquery.scrollTo.min.js"></script>
    <script src="<?php echo $this->enderecoArquivos; ?>bibliotecas/js/jquery.nicescroll.js" type="text/javascript"></script>
      <!-- custom select -->
      <script src="<?php echo $this->enderecoArquivos; ?>bibliotecas/js/jquery.customSelect.min.js"></script>
      <script src="<?php echo $this->enderecoArquivos; ?>bibliotecas/assets/chart-master/Chart.js"></script>
      <!--custome script for all page-->
      <script src="<?php echo $this->enderecoArquivos; ?>bibliotecas/js/scripts.js"></script>

      <!-- ./wrapper -->

      <!-- SweetAlert2 -->
      <script src="<?php echo $this->enderecoArquivos; ?>bibliotecas/sweetalert2/sweetalert2.min.js"></script>
      <!-- Toastr -->
      <script src="<?php echo $this->enderecoArquivos; ?>bibliotecas/toastr/toastr.min.js"></script>
      <!-- AdminLTE App -->
      <script src="<?php echo $this->enderecoArquivos; ?>bibliotecas/js/adminlte.min.js"></script>
      <!-- AdminLTE for demo purposes -->
      <script src="<?php echo $this->enderecoArquivos; ?>bibliotecas/js/demo.js"></script>

      <!-- Summernote -->
      <script src="<?php echo $this->enderecoArquivos; ?>bibliotecas/plugins/summernote/summernote-bs4.min.js"></script>

      <script src="<?php echo $this->enderecoArquivos; ?>bibliotecas/plugins/datatables/jquery.dataTables.min.js"></script>
      <script src="<?php echo $this->enderecoArquivos; ?>bibliotecas/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
      <script src="<?php echo $this->enderecoArquivos; ?>bibliotecas/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
      <script src="<?php echo $this->enderecoArquivos; ?>bibliotecas/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
      <script src="<?php echo $this->enderecoArquivos; ?>bibliotecas/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
      <script src="<?php echo $this->enderecoArquivos; ?>bibliotecas/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
      <script src="<?php echo $this->enderecoArquivos; ?>bibliotecas/plugins/jszip/jszip.min.js"></script>
      <script src="<?php echo $this->enderecoArquivos; ?>bibliotecas/plugins/pdfmake/pdfmake.min.js"></script>
      <script src="<?php echo $this->enderecoArquivos; ?>bibliotecas/plugins/pdfmake/vfs_fonts.js"></script>
      <script src="<?php echo $this->enderecoArquivos; ?>bibliotecas/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
      <script src="<?php echo $this->enderecoArquivos; ?>bibliotecas/plugins/datatables-buttons/js/buttons.print.min.js"></script>
      <script src="<?php echo $this->enderecoArquivos; ?>bibliotecas/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
      <script src="<?php echo $this->enderecoArquivos; ?>bibliotecas/pdfJS/build/pdf.js"></script>

      <?php  if($login!="sim"){ ?>
        <script type="text/javascript" src="<?php echo $this->enderecoArquivos; ?>paginacao.js"></script>
        <script type="text/javascript" src="<?php echo $this->enderecoArquivos; ?>notificacaoInstatanea.js"></script>
      <?php  } ?>

      <script type="text/javascript" src="<?php echo $this->enderecoArquivos; ?>envioComAjax.js"></script>

      <script type="text/javascript" src="<?php echo $this->enderecoArquivos; ?>scriptSuperGlobal3.js"></script>

  <?php }  }


?>
