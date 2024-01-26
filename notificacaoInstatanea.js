var idPMensagemClicada="";
$(document).ready(function(){
    var repeti=true;
    $("header #messages").bind("click mouseenter", function(){
      repeti=true;

       $("header #messages li a.msgLer").click(function(){
        if(repeti==true){
          idPMensagemClicada = $(this).attr("idPMensagem");
          $("header #messages li .rodape").removeClass("hidden");
          $("header #messages li#msg"+idPMensagemClicada+" .rodape").addClass("hidden");
          $("header #messages form").addClass("hidden");
          $("header #messages li form#"+$(this).attr("idPMensagem")).removeClass("hidden");
          $("header #messages li form#"+$(this).attr("idPMensagem")+" textarea").focus()
            repeti=false;
        }
      })
       $("header #messages li a.msgVisualizada").click(function(){
        if(repeti==true){
          idPMensagemClicada = $(this).attr("idPMensagem");
          $("header #messages li#msg"+idPMensagemClicada+" a.msgVisualizada").html('<i class="fa fa-spinner fa-spin"></i>')
          markMensagemAsRead();
            repeti=false;
        }
      })

      $("header #messages form").submit(function(){
        if(repeti==true){
          $("header #messages form#"+idPMensagemClicada+" button").html('<i class="fa fa-spinner fa-spin fa-fw"></i>')
          envoirMensagem(new FormData(this));
          repeti=false;
          return false;
        }
      })
    })
    
})

  newMensagens();
setInterval(function(){
  if($("header #messages").is(":visible")==false){
    newMensagens();
  }  
}, 10000)

function newMensagens(){
    var httpMensagem = new XMLHttpRequest();
    httpMensagem.onreadystatechange = function(){
        if(httpMensagem.readyState==4){
          var html="";
          var i=0;
          $("header .numeroTotalMensagens").text(JSON.parse(httpMensagem.responseText.trim()).length);
          if(JSON.parse(httpMensagem.responseText.trim()).length==1){
              html+='<div class="notify-arrow notify-arrow-blue"></div>'+
              '<li>'+
                '<p class="blue">Tens 1 nova messagem!</p>'+
              '</li>'; 
            }else{
              html+='<div class="notify-arrow notify-arrow-blue"></div>'+
              '<li>'+
                '<p class="blue">Tens '+JSON.parse(httpMensagem.responseText.trim()).length+' novas messagens!</p>'+
              '</li>';
            } 
          JSON.parse(httpMensagem.responseText.trim()).forEach(function(dado){
            i++;
            if(i<=7){
              html +='<li idPMensagem="'+dado.idPMensagem+'" id="msg'+dado.idPMensagem+'" class="liMensagem">'+
                    '<span class="photo"><img alt="avatar" src="'+enderecoArquivos+"/fotoUsuarios/"+dado.fotoEmissor+'"></span>'+
                    '<div class="subject">'+
                    '<span class="from lead">&nbsp;&nbsp;'+dado.nomeEmissor+'</span>'+
                    '<span class="hora"><br/>&nbsp;&nbsp;'+dado.horaMensagem+'-'+converterData(dado.dataMensagem)+'</span>'+
                    '</div>'+
                    '<span class="messagem text-justify" idPMensagem="'+dado.idPMensagem+'" style="font-size:10pt !important; color:black !important;">'+dado.textoMensagem+
                    '</span><br/>'+
                    '<div class="rodape text-center"><a href="#" idPMensagem="'+dado.idPMensagem+'" class="msgLer">Responder</a>'+
                    '<a href="#" class="msgVisualizada" idPMensagem="'+dado.idPMensagem+'">Visualizada</a>'+
                    '<a href="'+enderecoArquivos+'/areaInterConexao/mensagem/index.php?usuario='+dado.emissor+'" class="visualizarMensagens btn-warning">Chat</a></div>'+
                    
                    '<form id="'+dado.idPMensagem+'" class="hidden">'+
                    '<div class="form-group input-group col-lg-12 col-md-12 col-sm-12 col-xs-12">'+                          
                     '<textarea placeholder="Digite aqui a sua mensagem ..." class="form-control"'+
                     ' style="font-size: 11pt; height: 60px; max-width:100%;" name="mensagemCaixa" autocomplete="off" required=""></textarea>'+
                      '<span class="input-group-addon" style="padding: 0px; background-color: #428bca;">'+
                        '<button type="submit" id="btnSubmit" style="font-size: 12pt; border:none; background-color: #428bca;" class="btn"><i class="fas fa-location-arrow"></i></button>'+
                     '</span>'+      
                      '</div>'+
                      '<input type="hidden" name="idPMensagem" value="'+dado.idPMensagem+'">'+
                      '<input type="hidden" name="idPUsuario" value="'+dado.idEmissor+'">'+
                      '<input type="hidden" name="usuario" value="'+dado.emissor+'">'+
                       '<input type="hidden" name="action" value="envoirMensagem">'+
                    '</form>'+
              '</li>'
            }
            
          })
            $("header #messages").html(html);
        }

    }
    httpMensagem.open("GET", enderecoArquivos+"/noficacaoInstatanea.php?tipoAcesso=pegarNovasMensagens", true);
    httpMensagem.send();
}

function envoirMensagem(form){
  var httpEnviarMensagem = new XMLHttpRequest();
  httpEnviarMensagem.onreadystatechange = function(){
      if(httpEnviarMensagem.readyState==4){
        newMensagens();
      } 
  }
  httpEnviarMensagem.open("POST", enderecoArquivos+"msgInteractiva.php", true);
  httpEnviarMensagem.send(form);
}

function markMensagemAsRead(){
  var markMensagemAsRead = new XMLHttpRequest();
  markMensagemAsRead.onreadystatechange = function(){
      if(markMensagemAsRead.readyState<4){

      }else{        
          newMensagens();
      } 
  }
  markMensagemAsRead.open("GET", enderecoArquivos+"/msgInteractiva.php?idPMensagemClicada="
    +idPMensagemClicada+"&tipoAcesso=markMensagemAsRead", true);
  markMensagemAsRead.send();
}
