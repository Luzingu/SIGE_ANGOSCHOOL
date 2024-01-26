<?php 
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
  include_once $_SESSION["directorioPaterno"].'angoschool/funcoesAuxiliares.php';
  curtina($_SESSION["directorioPaterno"].'angoschool/'.directorioEmExecucao().'/funcoesAuxiliares.php');
  curtina($_SESSION["directorioPaterno"].'angoschool/htmlsMae.php');

class includarHtmls extends includarHtmlsMae{
   function __construct($caminhoAbsoluto){
       $caminho = explode(umaOuDuasBarras(), $caminhoAbsoluto); 
        $this->caminhoRetornar = "";
        for($i=1; $i<=count($caminho)-$_SESSION["numeroRecursividade"]; $i++){
          $this->caminhoRetornar .="../";
        }  
          parent::__construct(__DIR__);
        
    }
    function formConfirmarSenhaAdministrador (){?>
      <div class="modal fade" id="confirmarSenhaAdministrador" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
        <form class="modal-dialog">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h4 class="modal-title lead font-weight-bolder" id="myModalLabel">Adicionar novo Ano Lectivo</h4>
              </div>

              <div class="modal-body">                  
                  <div class="row">
                      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                        <input type="password" name="" class="form-control fa-border caixaSenha somenteLetras vazio" id="txtConfirmarSenhar" placeholder="Confirme Aqui a Sua Palavra Passe" required>
                      </div>
                  </div>
              </div>
              <div class="modal-footer">
                  <div class="row">
                    <div class="col-lg-5 col-md-5 col-sm-7 col-xs-7">
                      <input type="submit" class="btn btn-primary col-lg-12 lead btn-lg" value="Confirmar">
                    </div>                    
                  </div>                
              </div>
            </div>
          </form>
    </div>

    <?php } } ?>