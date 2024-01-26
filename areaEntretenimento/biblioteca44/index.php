<?php session_start();

    if(!isset($_SESSION['tipoUsuario'])){
      echo "<script>window.location='../../'</script>";
    }else if($_SESSION["tipoUsuario"]=="aluno" || $_SESSION["tipoUsuario"]=="professor"){
      include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
      includar("", "areaEscolas");
    }else{
      include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/funcoesAuxiliares.php';
      includar("../../", "areaAdministrador");
    }
    echo "<script>var caminhoRecuar='../../'</script>";

    $manipulacaoDados = new manipulacaoDados();
    $includarHtmls = new includarHtmls();
    $janelaMensagens = new janelaMensagens();
    $conexaoFolhas = new conexaoFolhas();
    $verificacaoAcesso = new verificacaoAcesso();
    $layouts = new layouts();
    $manipulacaoDados->conDb("entretenimento", true);
 ?>
<!DOCTYPE html>
<html lang="pt">

<head>
  <?php $conexaoFolhas->folhasCss();?>
  <style type="text/css">
    .listaCategorias{
      margin-bottom:20px;
    }
    .listaCategorias img{
      width: 170px;
      cursor: pointer;
    }
    .listaCategorias h4{
      margin-top:0px;
      font-weight: bolder;
      white-space: nowrap !important;
      overflow: hidden !important;
      text-overflow: ellipsis !important;
    }

    .listaLivro h4{
      margin-top:4px;
      font-weight: bolder;
      white-space: nowrap !important;
      overflow: hidden !important;
      text-overflow: ellipsis !important;
      text-align: left !important;
      font-size: 11pt;
    }

    .listaLivro div{
      background-color: rgba(0, 0, 255, 0.1);
      padding: 10px;
      margin-bottom: 20px;
      border: rgba(0, 0, 0, 1.0) solid 1px;
      cursor: pointer;
    }
    .listaLivro img{
      width: 100%;
      height: 200px;
    }

    #directorio{
      font-weight: 500;
      font-size:  18pt;
    }
    #directorio .caminho:hover{
      background-color:  rgba(0, 0, 0, 0.2);
      border-radius: 10px;
      padding: 3px;
      cursor: pointer;
      transition: 0.5s;
    }
    @media (max-width: 768px) {
      .listaCategorias img{
        width: 150px;
        cursor: pointer;
      }
      .listaCategorias h4{
        font-size: 11pt;
      }
      .listaLivro img{
        width: 140px;
        height: 170px;
      }

    }
  </style>
</head>

<body>
  <?php
    $janelaMensagens->processar (); 
    if($_SESSION["tipoUsuario"]=="aluno"){
      $layouts->idPArea=1;
      $layouts->designacaoArea="Área do Aluno";
      $layouts->cabecalho();
      $layouts->aside();
    }else if($_SESSION["tipoUsuario"]=="professor"){
      $layouts->idPArea=2;
      $layouts->designacaoArea="Área do Professor";
      $layouts->cabecalho();
      $layouts->aside();
    }else{
      $layouts->idPArea=11;
      $layouts->designacaoArea="Gestão de Empresa";
      $layouts->cabecalho(11);
      $layouts->aside(11);
    }
    echo "<script>var enderecoArquivos='".$manipulacaoDados->enderecoArquivos."'</script>";
  ?>

  <section id="main-content"> 
        <section class="wrapper" id="containers">

          <h3 id="directorio">
            <span id="caminho1" class="caminho">Bibioteca</span>

            <span class="caminho2" style="display: none;"><i class="fa fa-chevron-right"></i> <span id="caminho2" class="caminho"></span></span>
            <span class="caminho3" style="display: none;"><i class="fa fa-chevron-right"></i> <span id="caminho3" class="caminho"></span></span>
            <span class="caminho4" style="display: none;"><i class="fa fa-chevron-right"></i> <strong id="caminho4" class="text-primary"></strong></span>
          </h3>

        <div class="main-body">
            <div class="card" style="min-height:700px;">
               <div class="card-body">
                <?php 
                echo "<script>var livraria=".$manipulacaoDados->selectJson("livraria", [], [], [], "", [], ["tituloLivro"=>1])."</script>";
                echo "<script>var categoriaLivros=".$manipulacaoDados->selectJson("categoriaLivros", [], [], [], "", [], ["nomeCategoria"=>1])."</script>"; ?>

                <div class="row" id="categorias">
                    
                </div>
                <div class="row" id="subCategorias">

                </div>
                <div class="row" id="listaLivros">
                </div>

                <div class="row leitorLivro" style="display:none;">
                  <div class="col-lg-10 col-md-10 col-lg-offset-1 col-md-offset-1">
                    <a href="" download="" class="btn btn-primary"><i class="fa fa-download"></i> Baixar</a><br>
                    <object style="width:100%; height: 1000px;" data="nunca.pdf" id="leitorLivro" type="application/pdf" width="200px;" height="600px">
                    <h1>Seu navegador não suporta PDFs.</h1>
                  </object>
                  </div>
                </div>

               </div>
            </div>
         </div><br>
        <?php $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha(); ?>

<script type="text/javascript">
    var idPCategoria=""
    var idPSubCategoria=""

    var idPLivro="";
    var arquivoLivro=""
    var tituloLivro=""

   $(document).ready(function(){
      fecharJanelaEspera();
      seAbrirMenu()
    
    directorio = "areaEntretenimento/biblioteca44/";
    listarCategorias()

    $("#caminho1").click(function(){
      idPSubCategoria=""
      $(".caminho2, .caminho3, .caminho4").hide()
      $(".leitorLivro").hide()
      $("#listaLivros").hide()
      listarCategorias()        
    })

    $("#caminho2").click(function(){
      idPLivro="";
      idPSubCategoria=""
      $(".caminho3, .caminho4").hide()
      $(".leitorLivro").hide()
      listarSubCategorias()
      listarLivros()        
    })
    $("#caminho3").click(function(){
      idPLivro="";
      $(".caminho4").hide()
      $(".leitorLivro").hide()
      listarLivros()        
    })


    var repet=true
    $("#listaLivros").bind("click mouseenter", function(){
      repet=true
      $("#listaLivros div.listaLivro").click(function(){
        if(repet==true){
          tituloLivro = $(this).attr("title")

          $("#directorio #caminho4").text($(this).attr("title"))
          $("#directorio .caminho4").show()

          idPLivro = $(this).attr("id")
          arquivoLivro = $(this).attr("arquivo")
          abrirLivro()
          repet=false
        }
      })
    })

    $("#categorias").bind("click mouseenter", function(){
      repet=true
      $("#categorias div").click(function(){
        if(repet==true){
          idPCategoria = $(this).attr("id")
          $("#directorio #caminho2").text($(this).attr("title"))
          $("#directorio .caminho2").show()

          listarSubCategorias()
          listarLivros()
          repet=false
        }
      })
    })

    $("#subCategorias").bind("click mouseenter", function(){
      repet=true
      $("#subCategorias div").click(function(){
        if(repet==true){
          idPSubCategoria = $(this).attr("id")
          $("#directorio #caminho3").text($(this).attr("title"))
          $("#directorio .caminho3").show()
          listarLivros(idPSubCategoria)
          repet=false
        }
      })
    })

   })

   function listarLivros(idSubCategoria=""){

      $("#listaLivros").show()
    $("#listaLivros").empty()
    if(idSubCategoria!=""){
      $("#subCategorias").hide()
    }
    livraria.forEach(function(dado){
      if(dado.idCategoria==idPCategoria && ((idPSubCategoria=="" && dado.idSubCategoria=="" ) || dado.idSubCategoria==idPSubCategoria)){

        $("#listaLivros").append('<div class="col-lg-3 col-md-3 col-sm-6 col-xs-6 text-center listaLivro" arquivo="'+dado.arquivo+'" title="'+dado.tituloLivro+'" id="'+dado.idPLivro+'"><div>'+
          '<h4>'+dado.tituloLivro+'</h4><img id="img'+dado.idPLivro+'" src="'+
          enderecoArquivos+'/icones/iconeDocPdf.png">'+
          '</div></div>')
        gerarCapaLivro(enderecoArquivos+"/livraria/"+dado.arquivo, "img"+dado.idPLivro)
      }
    })    
   }
   function listarSubCategorias(){
    $("#categorias").hide()
    $("#subCategorias").show()
    var htmlLinhas="";
    categoriaLivros.forEach(function(dado){
      if(dado.subCategoria!=undefined && dado.idPCategoria==idPCategoria){
        dado.subCategoria.forEach(function(lista){
          htmlLinhas +='<div class="col-lg-3 col-md-3 col-sm-6 col-xs-6 text-center'+
          ' listaCategorias" title="'+lista.nomeSubCategoria+'" id="'+lista.idPSubCategoria+'">'+
          '<img src="'+enderecoArquivos+'/icones/categoria.png" >'+
          '<h4 class="text-primary">'+lista.nomeSubCategoria+'</h4></div>';
        })
      }
    })
    $("#subCategorias").html(htmlLinhas)
   }

    function listarCategorias(){
      $("#subCategorias").hide()
      $("#categorias").show()
      var htmlLinhas=""
      categoriaLivros.forEach(function(dado){
        htmlLinhas +='<div class="col-lg-3 col-md-3 col-sm-6 col-xs-6 text-center'+
        ' listaCategorias" title="'+dado.nomeCategoria+'" id="'+dado.idPCategoria+'">'+
        '<img src="'+enderecoArquivos+'/icones/categoria.png" >'+
        '<h4 class="text-primary">'+dado.nomeCategoria+'</h4></div>';
      })
      $("#categorias").html(htmlLinhas)
    }

    function abrirLivro(){
      chamarJanelaEspera("...");
      http.onreadystatechange = function(){
        if(http.readyState==4){
          fecharJanelaEspera();
          resultado = http.responseText.trim()
          $(".leitorLivro").show()

          $(".leitorLivro a").attr("href", enderecoArquivos+"/livraria/"+arquivoLivro)

          $(".leitorLivro a").attr("download", enderecoArquivos+"/livraria/"+arquivoLivro)

          $("#subCategorias, #listaLivros").hide()
          $("#leitorLivro").attr("data", enderecoArquivos+"/livraria/"+arquivoLivro)

        }
      }
      enviarComGet("tipoAcesso=lerLivro&idPLivro="+idPLivro);
    }

</script>
