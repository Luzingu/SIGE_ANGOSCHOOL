<?php 
  if(session_status()!==PHP_SESSION_ACTIVE){
      session_cache_expire(60);
      session_start();
    }
  class janelaMensagens{

   function __construct(){
        include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/funcoesAuxiliares.php';
        abrirSessao();            
    }
    
     function processar (){?>
          <div id="janelaEspera" >
              <div class="row">
                <div class="col-lg-6 col-md-6 col-lg-offset-3 col-md-offset-3" >
                  <p id="processador"><i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><br><strong id="mensagem" class="lead">Carregando...</strong></p>
                </div>
              </div>
          </div>
        <?php } function funcoesDaJanelaJs(){?>               
            <script type="text/javascript">

              function mensagensRespostas(identificadorJanela, mensagem){
                  var Toast = Swal.mixin({
                    toast: true,
                    position: 'top-start',
                    showConfirmButton: false,
                    timer: 3000
                  });
                  if(identificadorJanela=="#janelaPergunta"){
                    $(".toast").remove();                
                    chamarJanelaEspera("", "soMascara");
                     $(document).Toasts('create', {
                        class: 'janelaPergunta',
                        title: 'AngosSchool',
                        position: 'topRight',
                        image: caminhoRecuar+'../icones/question.png',
                        body: '<div class="lead" id="janelaPergunta" style="padding:10px; margin:-5px; margin-top:-8px; background-color:#31708f;"><p class="text-center">'+mensagem+'</p><div class="text-center"><div class="text-center"><a href="#" class="btn-success" style="padding:5px; border-radius:10px; text-decoration:none;" id="pergSim"><i class="fa fa-thumbs-up"></i> Sim</a>&nbsp;&nbsp;<a href="#"  class="btn-danger closeToastPergunta" style="padding:5px; border-radius:10px; text-decoration:none;" id="pergNao"><i class="fa fa-thumbs-down"></i> Não</a></div></div>'
                      })
                  }else if(identificadorJanela=="#informacoes"){
                     $(document).Toasts('create', {
                        class: 'janelaInformacao',
                        title: 'LuzinguLuetu LDA',
                        position: 'topLeft',
                        image: caminhoRecuar+'../icones/Info.png',
                        body: '<div class="lead" style="padding:10px; margin:-5px; margin-top:-8px; background-color:#31708f; color:white;"><p>'+mensagem+'</p><div class="text-center"></div>'
                      })                       
                  }else if(identificadorJanela=="#mensagemCerta"){
                      toastr.success(mensagem)
                       $(".toast").addClass("show")
                       fecharJanelaToastPergunta();
                  }else if(identificadorJanela=="#mensagemErrada"){
                      toastr.error(mensagem)
                      $(".toast").addClass("show")
                      fecharJanelaToastPergunta();
                  }
              }

              function mensagensRespostas2(identificadorJanela, mensagem){
                var msgNot = new XMLHttpRequest();
                msgNot.onreadystatechange = function(){
                    if(msgNot.readyState==4){
                        mensagensRespostas(identificadorJanela, mensagem);
                    }
                }
                msgNot.open("GET", "../../", true);
                msgNot.send();
              }


              function chamarJanelaEspera(mensagem, pedido="tudo"){
                  if(pedido=="soMascara"){
                    $("#processador").hide();
                  }else{
                    $("#processador").show();
                  }
                  var alturaCont = $("#containers").height();
                  alturaCont +=200;
                  $("#janelaEspera").css("height", alturaCont+"px");
                   $("#janelaEspera").css("padding-top", "150px");
                  $("#janelaEspera").show();
                  $("#janelaEspera #mensagem").text(mensagem);

              }
              function fecharJanelaEspera(){
                  $("#janelaEspera").hide();
              }
              function fecharJanelaToast(quemEstaFechar="naoSei"){
                if($(".janelaPergunta").is(":visible")==false ||  quemEstaFechar=="naoSei"){
                    if($(".toast").is(":visible")==true){
                        $(".toast").removeClass("show")
                        $(".toast").remove();
                    }
                }
              }

              function fecharJanelaToastPergunta(){
                $(".janelaPergunta").removeClass("show")
                $(".janelaPergunta").remove();
                fecharJanelaEspera();
              }

              function fecharJanelaToastInformacao(){
                $(".janelaInformacao").removeClass("show")
                $(".janelaInformacao").remove();
              }

              function verficarSeFoiFechadoToastPergunta(){
                if($(".janelaPergunta").is(":visible")==false){
                    fecharJanelaEspera();
                   
                }  
              }

              $(document).ready(function(){
                 $("section").click(function(){
                    fecharJanelaToast("section");
                });
                $(".toast").click(function(){
                    $(".toast").addClass("show");
                });
                var rep2=true;
                $("body").bind("mouseenter click", function(){
                      rep2=true;
                    $(".closeToastPergunta").click(function(){
                      if(rep2==true){
                        fecharJanelaToastPergunta();
                        rep2=false;
                      }         
                  })
                })

                var rep3=true;
                $("body").bind("mouseenter click", function(){
                    rep3=true;
                    $(".janelaPergunta").click(function(){
                      if(rep3==true){
                        setTimeout(verficarSeFoiFechadoToastPergunta, 500)           
                        rep3=false;
                      }      
                    })
                })
              })
                  
            </script>

        <?php } } ?>




  <!-- 
    

              $(document).ready(function(){
                  $('.swalDefaultSuccess').click(function() {
                    Toast.fire({
                      icon: 'success',
                      title: 'O Aluno Foi Matriculado com Sucesso!'
                    })
                  }); 


                  $('.swalDefaultInfo').click(function() {
                    Toast.fire({
                      icon: 'info',
                      title: 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr.'
                    })
                  });
                  $('.swalDefaultError').click(function() {
                    Toast.fire({
                      icon: 'error',
                      title: 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr.'
                    })
                  });
                  $('.swalDefaultWarning').click(function() {
                    Toast.fire({
                      icon: 'warning',
                      title: 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr.'
                    })
                  });
                  $('.swalDefaultQuestion').click(function() {
                    Toast.fire({
                      icon: 'question',
                      title: 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr.'
                    })
                  });

                  $('.toastrDefaultSuccess').click(function() {

                    toastr.success('O Aluno Foi Matriculado com Sucesso1!')
                  });

                  $('.toastrDefaultInfo').click(function() {
                    toastr.info('Lorem ipsum dolor sit amet, consetetur sadipscing elitr.')
                  });
                  $('.toastrDefaultError').click(function() {
                    toastr.error('Lorem ipsum dolor sit amet, consetetur sadipscing elitr.')
                  });
                  $('.toastrDefaultWarning').click(function() {
                    toastr.warning('Lorem ipsum dolor sit amet, consetetur sadipscing elitr.')
                  });

                  $('.toastsDefaultDefault').click(function() {
                    $(document).Toasts('create', {
                      title: 'Toast Title',
                      body: 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr.'
                    })
                  });
                  $('.toastsDefaultTopLeft').click(function() {
                    $(document).Toasts('create', {
                      title: 'Toast Title',
                      position: 'topLeft',
                      body: 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr.'
                    })
                  });
                  $('.toastsDefaultBottomRight').click(function() {
                    $(document).Toasts('create', {
                      title: 'Toast Title',
                      position: 'bottomRight',
                      body: 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr.'
                    })
                  });
                  $('.toastsDefaultBottomLeft').click(function() {
                    $(document).Toasts('create', {
                      title: 'Toast Title',
                      position: 'bottomLeft',
                      body: 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr.'
                    })
                  });
                  $('.toastsDefaultAutohide').click(function() {
                    $(document).Toasts('create', {
                      title: 'Toast Title',
                      autohide: true,
                      delay: 750,
                      body: 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr.'
                    })
                  });
                  $('.toastsDefaultNotFixed').click(function() {
                    $(document).Toasts('create', {
                      title: 'Toast Title',
                      fixed: false,
                      body: 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr.'
                    })
                  });
                  $('.toastsDefaultFull').click(function() {
                    $(document).Toasts('create', {
                      body: 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr.',
                      title: 'Toast Title',
                      subtitle: 'Subtitle',
                      icon: 'fas fa-envelope fa-lg',
                    })
                  });
                  $('.toastsDefaultFullImage').click(function() {
                    $(document).Toasts('create', {
                      body: 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr.',
                      title: 'Toast Title',
                      subtitle: 'Subtitle',
                      image: 'dist/img/user3-128x128.jpg',
                      imageAlt: 'User Picture',
                    })
                  });
                  $('.toastsDefaultSuccess').click(function() {
                    $(document).Toasts('create', {
                      class: 'bg-success',
                      title: 'Toast Title',
                      subtitle: 'Subtitle',
                      body: 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr.'
                    })
                  });
                  $('.toastsDefaultInfo').click(function() {
                    $(document).Toasts('create', {
                      class: 'bg-info',
                      title: 'Toast Title',
                      subtitle: 'Subtitle',
                      body: 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr.'
                    })
                  });
                  $('.toastsDefaultWarning').click(function() {
                    $(document).Toasts('create', {
                      class: 'bg-warning',
                      title: 'Toast Title',
                      subtitle: 'Subtitle',
                      body: 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr.'
                    })
                  });
                  $('.toastsDefaultDanger').click(function() {
                    $(document).Toasts('create', {
                      class: 'bg-danger',
                      title: 'Toast Title',
                      subtitle: 'Subtitle',
                      body: 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr.'
                    })
                  });
                  $('.toastsDefaultMaroon').click(function() {
                    $(document).Toasts('create', {
                      class: 'bg-maroon',
                      title: 'Toast Title',
                      subtitle: 'Subtitle',
                      body: 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr.'
                    })
                  });
              })

               


    !-->
        
