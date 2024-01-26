<?php function perfilFuncionario($manipulador){ 

	echo "<script>var listaValores =".json_encode($manipulador->sobreUsuarioLogado)."</script>";
    $cargoExtenso = valorArray($manipulador->sobreUsuarioLogado, "funcaoEnt", "escola");  ?>
	<div class="row">
	  <div class="col-lg-12">
	    <div class="profile-widget profile-widget-info" >
	      <div class="panel-body">
	        <div class="col-lg-3 col-sm-3 text-center">
	          <h4 class="nomeUsuarioCorente text-center"><?php echo valorArray($manipulador->sobreUsuarioLogado, "nomeEntidade"); ?></h4>
	          <div class="follow-ava text-center">
	            <img src="<?php echo '../fotoUsuarios/'.valorArray($manipulador->sobreUsuarioLogado, 'fotoEntidade'); ?>" class="medio imagemUsuarioCorrente" id="imageProfessor">
	          </div>
	          <h6 class="cargoProfessor text-center"><?php echo valorArray($manipulador->sobreUsuarioLogado, "funcaoEnt", "escola"); ?></h6>
	        </div>
	        <div class="col-lg-3 col-sm-3 follow-info">
	        	
	          <h6 class="outrasInformacoes">
                    <span class="lead"><i class="fa fa-map-marker-alt"></i><strong> <?php echo valorArray($manipulador->sobreUsuarioLogado, "nomeEscola"); ?></strong></span> <br/><br/>
                    <span class="lead"><i class="fa fa-phone"></i> <strong class="numeroTelefone" id="telProfessor"><?php echo valorArray($manipulador->sobreUsuarioLogado, "numeroTelefoneEntidade");?></strong></span><br/><br/>
 
                    <span class="lead"><strong class="numeroTelefone" id="telProfessor"><?php echo valorArray($manipulador->sobreUsuarioLogado, "emailEntidadeEntidade");?></strong></span>
                </h6>
	        </div>
	        <div class="col-lg-3 col-md-3 col-sm-12 follow-info weather-category border" style="padding-top: 0px; border: none !important;">
	          <div class="text-center">
	            <strong class="text-center " id="nivelAcademino"><?php 
	                echo valorArray($manipulador->sobreUsuarioLogado, "nivelAcademicoEntidade")
	             ?></strong>
	          </div>
	        </div>
	        <div class="col-lg-3 col-md-3 col-sm-12 follow-info weather-category border" style="padding-top: 0px; border: none !important;">
	          <div class="text-center">
	            <strong class="text-center " id="areaFormacao">
	              <?php 
	                if(valorArray($manipulador->sobreUsuarioLogado, "nivelAcademicoEntidade")=="Médio"){
	                    echo valorArray($manipulador->sobreUsuarioLogado, "cursoEnsinoMedio");
	                }else if(valorArray($manipulador->sobreUsuarioLogado, "nivelAcademicoEntidade")=="Licenciado"  || valorArray($manipulador->sobreUsuarioLogado, "nivelAcademicoEntidade")=="Bacharel"){
	                    echo valorArray($manipulador->sobreUsuarioLogado, "cursoLicenciatura");
	                }else if(valorArray($manipulador->sobreUsuarioLogado, "nivelAcademicoEntidade")=="Mestre"){
	                    echo valorArray($manipulador->sobreUsuarioLogado, "cursoMestrado");
	                }else if(valorArray($manipulador->sobreUsuarioLogado, "nivelAcademicoEntidade")=="Doutor"){
	                    echo valorArray($manipulador->sobreUsuarioLogado, "cursoDoutoramento");
	                }
	                ?>
	            </strong>
	          </div>
	        </div>

	      </div>
	    </div>
	  </div>
	</div>

	<div class="row">
	  <div class="col-lg-12">
	    <div class="panel">
	      <header class="panel-heading tab-bg-info">
	        <ul class="nav nav-tabs">
	          <li class="active">
	            <a data-toggle="tab" href="#profile" class="lead">
                  <i class="fa fa-user"></i>
                 Perfil
              </a>
	          </li>
	        </ul>
	      </header>
	      <div class="panel-body" >
	        <div class="tab-content">
	          <!-- profile -->
	          <div id="profile" class="tab-pane active" >
	            <div class="panel">
	              <div class="panel-body bio-graph-info">
	                <div class="bio-graph-heading lead col-sm-12 col-xs-12 col-lg-12 col-md-12" id="acercaUsuarioCorente" style="margin-bottom: 30px; text-transform:uppercase;">
	                <?php echo valorArray($manipulador->sobreUsuarioLogado, "nomeEscola"); ?>
	              </div>
	                 
	                 <div class="col-lg-12 col-md-12">
											<form class="form-horizontal" role="form" method="POST" enctype="multipart-data" id="formularioPerfil">

		                    <div class="col-md-3 col-lg-3 lead">
		                      <label class="lead">Formato de Documentos:</label>
		                        <select class="form-control lead" name="formatoDocumento" id="formatoDocumento">
		                          <option value="pdf">PDF</option>
		                          <option value="word">WORD</option>
		                          <option value="excel">EXCEL</option>
		                        </select>
		                    </div>
		                    <div class="col-lg-4 col-md-4">
		                      <label class="lead">Foto:</label>
		                      <input type="file" name="fotoEntidade" value="" accept='.jpg, .png, .jpeg' class="form-control fa-border vazio" id="fotoEntidade">
		                    </div>
		                    <input type="hidden" name="editadoNoPerfilEntidade" value="sim">
			                  <input type="hidden" name="action" value="editarPerfilEntidade">
	                      <div class="col-lg-3 col-md-3"> <br>
                          <button  type="submit" class="btn-success btn lead"><i class="fa fa-check-circle"></i> Alterar</button>
                        </div>
											</form><br><br>
	                  </div>
	              </div>
	            </div>
	           </div>
	        </div>
	      </div>
	    </div>
	  </div>
	</div>
	<script type="text/javascript">
		window.onload=function(){
		    
			fecharJanelaEspera();
		  seAbrirMenu();
		  directorio = "../areaAdministrador/areaGestaoEscolas/escolas00/"; 	  
		  porValoresFormulario();
		  $("#formularioPerfil").submit(function(){
		    if(validarFormularios("#formularioPerfil")==true){
		      actualizarPerfil();      
		    }
		    return false;
		  })
		}
		function porValoresFormulario(){
		  limparFormulario("#formularioPerfil");
		  listaValores.forEach(function(dado){
		      $("#formatoDocumento").val(dado.formatoDocumentoEnt)
		  })

		}

	  function actualizarPerfil(){
      chamarJanelaEspera("...")
      http.onreadystatechange = function(){
        if(http.readyState==4){
          fecharJanelaEspera();
          resultado = http.responseText.trim()
          if(resultado.substring(0,1)=="F") {
            mensagensRespostas('#mensagemErrada', resultado.substring(1,resultado.length));
          }else{
            mensagensRespostas('#mensagemCerta', "Os dados foram alterados com sucesso."); 
          }
        }
      }
    	enviarComPost(new FormData(document.getElementById('formularioPerfil')));
	  }
	</script>
<?php } ?>