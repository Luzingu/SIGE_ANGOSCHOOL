<?php
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/funcoesAuxiliares.php';
    curtina($_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/funcoesAuxiliares.php');
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/funcoesAuxiliaresDb.php';

    class funcoesAuxiliares extends funcoesAuxiliaresMae{
        function __construct($areaVisualizada=""){
            ini_set('memory_limit', '500M');
            parent::__construct($areaVisualizada);            
        }


        function cabecalhoEmpresa(){

            $retorno ="<p style='".$this->miniParagrafo."'></p><p style='".$this->miniParagrafo."'><img src='".$_SERVER['DOCUMENT_ROOT']."/angoschool/icones/logoEmpresa.png' style='".$this->insignia_medio."'></p>
            <p style='".$this->bolder.$this->miniParagrafo."'>LUZINGULUETU - COMÉRCIO GERAL E PRESTAÇÃO DE SERVIÇOS, LDA</p><p style='".$this->miniParagrafo."'>NIF: 5000721670</p>
              <p style='".$this->miniParagrafo."'>Localização: Soyo – Zaire</p>
              <p style='".$this->miniParagrafo."'>Telefone: 926 930 664 - 932 390 059</p>
              <p style='".$this->miniParagrafo."'>Email: luzinguluetu@angoschool.ao</p>
              <p style='"."'>Site: www.angoschool.ao</p>";

              return $retorno;
        }

        function assinaturaDirigentes($cargo, $tamanhoLinha="", $comRubrica="sim", $rubricaAutomaticoObrigatorio="nao"){

            if(is_array($cargo)){
                $i=0;
                foreach ($cargo as $miradi) {
                    if($i==0){
                        $cargo=$miradi;
                    }
                    if($miradi==valorArray($this->sobreUsuarioLogado, "nivelSistemaEntidade")){
                        $cargo=$miradi;
                        break;
                    }
                    $i++;
                }
            }

            $dirigente = $this->selectArray("entidade_escola", ["nivelSistemaEntidade"=>$cargo, "idEntidadeEscola"=>(int)$_SESSION['idEscolaLogada'], "estadoActividadeEntidade"=>"A"], [], [["entidadesprimaria", "idFEntidade=idPEntidade"]]);

            $retorno="";
            $artigo="o";
            $artigo2="";
            
            if(valorArray($dirigente, "generoEntidade")=="F"){
                $artigo="a";
                $artigo2="a";
            }
            $primeiraLinha="";
            if($cargo=="CEO"){
                $primeiraLinha = strtoupper($artigo)." Director".$artigo2." Executiv".$artigo;

            }else if($cargo=="CFO"){
                $primeiraLinha = strtoupper($artigo)." Director".$artigo2." Financeir".$artigo;
            }

            

            $rubrica=""; 
            $estadoExibirAssinaturas= $this->selectUmElemento("estadoperiodico", "estado", ["objecto"=>"exibirAssinaturas", "idEstadoEscola"=>$_SESSION["idEscolaLogada"]]);
            $fotoRubrica="";
            if(($comRubrica=="sim" && $estadoExibirAssinaturas=="V" && seOficialEscolar()) || $rubricaAutomaticoObrigatorio=="sim"){ 

                if($cargo=="CEO"){
                    $fotoRubrica = $this->selectUmElemento("escolas", "assinatura1", ["idPEscola"=>$_SESSION["idEscolaLogada"]]);
                }else if($cargo=="CFO"){
                    $fotoRubrica = $this->selectUmElemento("escolas", "assinatura2", ["idPEscola"=>$_SESSION["idEscolaLogada"]]);
                }

                $rubrica="";
                if(file_exists($this->caminhoRetornar."Ficheiros/Escola_".$_SESSION["idEscolaLogada"]."/Icones/".$fotoRubrica)){

                    $rubrica = "<img src='".$this->caminhoRetornar."Ficheiros/Escola_".$_SESSION["idEscolaLogada"]."/Icones/".$fotoRubrica."' style='width: 170px; height: 70px; margin-top:-30px; margin-bottom:-20px; '>";
                }
            }

            return $this->porAssinatura($primeiraLinha, valorArray($dirigente, "tituloNomeEntidade"), $rubrica, $tamanhoLinha);
        }

    }

?>