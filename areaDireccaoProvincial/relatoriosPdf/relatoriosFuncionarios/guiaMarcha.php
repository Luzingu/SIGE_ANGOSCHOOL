<?php if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }

    include_once $_SESSION["directorioPaterno"].'angoschool/areaDireccaoProvincial/funcoesAuxiliares.php';
    include_once $_SESSION["directorioPaterno"].'angoschool/areaDireccaoProvincial/funcoesAuxiliaresDb.php';

    class relatorioTransferencia extends funcoesAuxiliares{

        function __construct($caminhoAbsoluto){
            $this->caminhoRetornar = retornarCaminhoRecuarArquivosPhp(__DIR__);
            parent::__construct("Rel-Guia de Marcha");

            $this->numeroGuiaMarcha = isset($_GET["numeroGuiaMarcha"])?$_GET["numeroGuiaMarcha"]:null;
            $this->pais = isset($_GET["pais"])?$_GET["pais"]:null;
            $this->provincia = isset($_GET["provincia"])?$_GET["provincia"]:null;
            $this->municipio = isset($_GET["municipio"])?$_GET["municipio"]:null;
            $this->comuna = isset($_GET["comuna"])?$_GET["comuna"]:null;
            $this->motivo = isset($_GET["motivo"])?$_GET["motivo"]:null;
            $this->assinante = isset($_GET["assinante"])?$_GET["assinante"]:null;
            $this->idPProfessor = isset($_GET["idPProfessor"])?$_GET["idPProfessor"]:null;

            $this->professor =$this->selectArray("entidadesprimaria LEFT JOIN dadosadicionasentidade ON idDAEntidade=idPEntidade LEFT JOIN entidade_escola ON idPEntidade=idFEntidade LEFT JOIN escolas ON idEntidadeEscola=idPEscola LEFT JOIN div_terit_provincias ON idPProvincia=provincia LEFT JOIN div_terit_municipios ON idPMunicipio=municipio LEFT JOIN div_terit_comunas ON idPComuna=comuna", "*", "idPEntidade=:idPEntidade AND provincia=:provincia AND estadoActividadeEntidade=:estadoActividadeEntidade", [$this->idPProfessor, valorArray($this->sobreUsuarioLogado, "provincia"), "A"]);

            $this->html="<html>
            <head>
                <title>Guia de Marcha</title>
            </head>
            <body>".$this->fundoDocumento();
 
            if($this->verificacaoAcesso->verificarAcessoAlteracao(["aRelEstatistica"])){                                
                $this->modeloIMNE();
            }else{
              $this->negarAcesso();
            }
        }

         private function modeloIMNE(){
            if(valorArray($this->professor, "generoEntidade")=="M"){
                $art1="o";
                $art2 ="";
            }else{
                 $art1="a";
                $art2 ="a";
            }

            $sobrePais = $this->selectArray("div_terit_paises", "*", "idPPais=:idPPais", [$this->pais]);
            $sobreProvincia = $this->selectArray("div_terit_provincias", "*", "idPProvincia=:idPProvincia", [$this->provincia]);
            $sobreMunicipio = $this->selectArray("div_terit_municipios", "*", "idPMunicipio=:idPMunicipio", [$this->municipio]);
            $sobreComuna = $this->selectArray("div_terit_comunas", "*", "idPComuna=:idPComuna", [$this->comuna]);

            $destino="";
            if($this->pais==valorArray($this->professor, "pais")){

                if($this->provincia==valorArray($this->professor, "provincia")){
                    $destino = valorArray($sobreMunicipio, "preposicaoMunicipio2")." ".valorArray($sobreMunicipio, "nomeMunicipio").", comuna ".valorArray($sobreComuna, "preposicaoComuna2")." ".valorArray($sobreComuna, "nomeComuna");
                }else{
                    $destino = valorArray($sobreProvincia, "preposicaoProvincia2")." ".valorArray($sobreProvincia, "nomeProvincia").", no municipio ".valorArray($sobreMunicipio, "preposicaoMunicipio2")." ".valorArray($sobreMunicipio, "nomeMunicipio").", comuna ".valorArray($sobreComuna, "preposicaoComuna2")." ".valorArray($sobreComuna, "nomeComuna");
                }
            }else{
                $destino = valorArray($sobrePais, "nomePais").", na província ".valorArray($sobreProvincia, "preposicaoProvincia2")." ".valorArray($sobreProvincia, "nomeProvincia").", no municipio ".valorArray($sobreMunicipio, "preposicaoMunicipio2")." ".valorArray($sobreMunicipio, "nomeMunicipio").", comuna ".valorArray($sobreComuna, "preposicaoComuna2")." ".valorArray($sobreComuna, "nomeComuna");
            }

            $this->html .=$this->cabecalho()."<br/>
            <p  style='margin-top:10px;".$this->maiuscula.$this->sublinhado.$this->bolder.$this->text_center."'>GUIA DE MARCHA N.º ".completarNumero($this->numeroGuiaMarcha)."/".valorArray($this->sobreUsuarioLogado, "abrevNomeEscola")."/".explode("-", $this->dataSistema)[0]."</p>

            <p style='margin-top:25px;".$this->text_justify."line-height:25px;'>Por este <span>".valorArray($this->sobreUsuarioLogado, "nomeEscola")."</span>, se faz constar as autoridades a quem o conhecimento desta competir que segue viagem do ".$this->selectUmElemento("div_terit_municipios", "nomeMunicipio", "idPMunicipio=:idPMunicipio", [valorArray($this->professor, "municipio")])." para ".$destino.", ".$art1." Senhor".$art2." <span style='".$this->vermelha.$this->bolder."'>".valorArray($this->professor, "nomeEntidade")."</span>, funcionário desta instituição com as funções de ".valorArray($this->professor, "categoriaEntidade").", que ali se desloca, ".$this->motivo.".</p>

                <div style='height: 25px;border-bottom: dashed black 1.2px; margin-top:-20px;'></div>
                <div style='height: 25px;border-bottom: dashed black 1.2px;'></div>
                <div style='height: 25px;border-bottom: dashed black 1.2px;'></div>

            <p style='margin-top:45px;".$this->text_justify."line-height:28px;margin-top:50px;'>E, para que se não lhe ponha impedimento, mandei passar a presente Guia de Marcha que vai por mim assinada e autenticada com carimbo a óleo em uso nesta Escola.</p><br/>
            <p style='margin-top:-20px;".$this->bolder."'>Sem outro assunto, renovadas saudações.</p>
            <p  style='margin-top:10px;".$this->text_justify."'>".$this->rodape()."</p>
            <div style='margin-top:0px;'>".$this->assinaturaDirigentes("DP")."</div>";
            
           $this->exibir("", "Guia de Marcha-".valorArray($this->professor, "nomeEntidade"));
        }
    }

new relatorioTransferencia(__DIR__);
    
    
  
?>