


var repet=true;
$(".paginacao").bind("mouseenter click", function(){
    repet=true;
    $(".paginacao ul li").click(function(){
      if(repet==true){

        if($(this).attr("nome")=="avancar"){
           paginacao.avancar();
        }else if($(this).attr("nome")=="recuar"){
          paginacao.recuar();
        }if($(this).attr("nome")=="interno"){
            paginacao.numero($(this).attr("posicao"));
        }
        jaTemPaginacao=true;
        fazerPesquisa();
        repet=false;
      }

    })
});


class Paginacao{
	baraPaginacao(numeroLinhas, numeroDeLinhasPorTabela){
           this.posisaoFinal=0;
           this.posisaoActual=1;
           this.numeroDeLinhasPorTabela = numeroDeLinhasPorTabela;
           this.posisaoFinal =0;
           this.comeco = 0;
           this.final = numeroDeLinhasPorTabela-1;
            $(".paginacao").empty();
            var numeroDeVez = Math.floor(numeroLinhas/numeroDeLinhasPorTabela);
            numeroDeVez = numeroDeVez;                 
           var  sobraLinhas=numeroLinhas-numeroDeVez*numeroDeLinhasPorTabela; 
           var lis ="";
           var ultimoValor=0;
           for (var i=1; i<=numeroDeVez; i++){
                if(i==1){
                    lis +="<li class='numero active pos"+i+"' posicao='"+i+"' nome='interno'><a href='#'>"+i+"</a></li>";
                }else{
                    lis +="<li class='numero pos"+i+"' posicao='"+i+"' nome='interno'><a href='#'>"+i+"</a></li>";
                }                    
                ultimoValor = i;
                this.posisaoFinal = i;
           }
           if(Math.floor(sobraLinhas)>0){
                if(ultimoValor==0){
                    lis +="<li class='numero active pos"+(ultimoValor+1)+"' posicao='"+(ultimoValor+1)+"' nome='interno'><a href='#'>"+(ultimoValor+1)+"</a></li>";
                }else{
                    lis +="<li class='numero pos"+(ultimoValor+1)+"' posicao='"+(ultimoValor+1)+"' nome='interno'><a href='#'>"+(ultimoValor+1)+"</a></li>";
                }
                this.posisaoFinal++;             
           }
           $(".paginacao").append('<ul class="pagination pagination-lg">'+
                '<li class="recuarPag" nome="recuar"><a href="#">&laquo;</a></li>'+lis+
                '<li class="avancarPag" nome="avancar"><a href="#" class="lead">&raquo;</a></li>'+
            '</ul>');          
    }
    recuar (){
        if(this.posisaoActual!=1){
            this.posisaoActual--;
        }else{
            this.posisaoActual=1;
        }
        $(".paginacao ul li.numero").removeClass("active");
        $(".paginacao ul li.pos"+this.posisaoActual).addClass("active");
        this.comeco = this.posisaoActual*this.numeroDeLinhasPorTabela - this.numeroDeLinhasPorTabela;
    	this.final = this.posisaoActual*this.numeroDeLinhasPorTabela-1;
    }

    avancar (){      	
			if(this.posisaoActual!=this.posisaoFinal){
            this.posisaoActual++;
        }else{
            this.posisaoActual=this.posisaoFinal;
        }
        $(".paginacao ul li.numero").removeClass("active");
        $(".paginacao ul li.pos"+this.posisaoActual).addClass("active");

        this.comeco = this.posisaoActual*this.numeroDeLinhasPorTabela - this.numeroDeLinhasPorTabela;
        this.final = this.posisaoActual*this.numeroDeLinhasPorTabela-1;
    }

    numero (posicao){
 		$(".paginacao ul li.numero").removeClass("active");
        $(this).addClass("active");
        $(".paginacao ul li.pos"+posicao).addClass("active");
        this.posisaoActual = posicao;
        this.comeco = this.posisaoActual*this.numeroDeLinhasPorTabela - this.numeroDeLinhasPorTabela;
        this.final = this.posisaoActual*this.numeroDeLinhasPorTabela-1;
    }
}