<?php
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_cache_expire(60);
      session_start();
    }
    use Dompdf\Dompdf;
    include_once ($_SERVER['DOCUMENT_ROOT'].'/angoschool/funcoesAuxiliares.php');
    curtina($_SERVER['DOCUMENT_ROOT'].'/angoschool/bibliotecas/vendor/autoload.php');

    curtina($_SERVER['DOCUMENT_ROOT'].'/angoschool/bibliotecas/qrcode/qrlib.php');
    
    curtina($_SERVER['DOCUMENT_ROOT'].'/angoschool/'.directorioEmExecucao().'/manipulacaoDados.php');
    curtina($_SERVER['DOCUMENT_ROOT'].'/angoschool/verificadorAcesso.php');
    curtina($_SERVER['DOCUMENT_ROOT'].'/angoschool/'.directorioEmExecucao().'/funcoesAuxiliares.php');
    curtina($_SERVER['DOCUMENT_ROOT'].'/angoschool/leitorNumeros.php');

    class funcoesAuxiliaresMae extends manipulacaoDados{
        public $html="";
        public $dompdf1="";
        public $leitorNumero="";
        public $verificacaoAcesso="";

        function __construct($areaVisualizada=""){
            $this->dompdf = new DOMPDF();
            $this->leitorNumero = new leitorNumero();
            $this->html="";
            ini_set('memory_limit', '500M');

            parent::__construct($areaVisualizada);
            $this->estilizadorCssEmPhp();
            $this->verificacaoAcesso = new verificacaoAcesso();
            $this->manterUsuarioOnline1();
        }


        private function manterUsuarioOnline1(){
            if(isset($_SESSION["areaActual"])){
                $dataSaida = $this->dataSistema.$this->tempoSistema;
                $estadoExpulsao="I";
                $idPOnline="";

                $this->editar("entidadesonline", "dataSaida, horaSaida", [$this->dataSistema, $this->tempoSistema], ["idPOnline"=>(int)$_SESSION["idPOnline"]]);
            }       
        }


        public function qrCode($texto, $caminhoRetornar, $tamanho=50, $altura=50){
            //set it to writable location, a place for temp generated PNG files
            $PNG_TEMP_DIR = "../listaTurmas/";
            
            //html PNG location prefix
            $PNG_WEB_DIR = '../listaTurmas/';
            
            $filename = $PNG_TEMP_DIR.'test.png';
            
            //processing form input
            //remember to sanitize user input in real-life solution !!!
            $errorCorrectionLevel = 'L';
            
            unlink($caminhoRetornar."icones/qr.png");
            QRcode::png($texto, $caminhoRetornar."icones/qr.png", "L", 4, 2);
            //display generated file
            return '<img src="'.$caminhoRetornar.'icones/'.basename($caminhoRetornar."icones/qr.png").'" style="width:'.$tamanho.'px; height:'.$altura.'px"/>';
        }

        
        function porAssinatura($label, $nome="", $rubrica="", $tamanhoLinha=""){
            $retorno="";
            $linha="";
        
            if($tamanhoLinha==""){
                $totalLinha = (strlen($nome)+2);

                if(strlen($label)>$totalLinha){
                    $totalLinha = strlen($label)+4;
                }
                if($totalLinha<23){
                    $totalLinha=23;
                }

                for($i=0; $i<=$totalLinha; $i++){
                    $linha .="_";
                }
            }else if($tamanhoLinha=="nada"){
                $linha = "&nbsp;";
            }else{
                if($tamanhoLinha<23){
                    $tamanhoLinha=23;
                }
                for($i=0; $i<=$tamanhoLinha; $i++){
                    $linha .="_";
                }
            }

            $retorno .="<p style='".$this->tituloDirigente."'><strong>".$label."</strong></p>";
            if($rubrica==""){
                $retorno .="<p style='".$this->linhaDirigente."margin-top:-10px;'>".$linha."</p>";
            }else{
                $retorno .= "<p style='".$this->linhaDirigente.$this->text_center."'>".$rubrica."</p>";
            }
            $retorno .="<p style='".$this->linhaDirigente.$this->text_center."'><strong>".$nome."</strong></p>";
            return $retorno;
        }

        function exibir($directorio, $nomeDownload, $nomeArquivo="", $tamanhoFolha="A4", $orientacao="portrait"){  
            
            if($this->podesExectar=="sim"){

                $formatoDocumento = $this->selectUmElemento("entidadesprimaria", "formatoDocumentoEnt", ["idPEntidade"=>$_SESSION["idUsuarioLogado"]]);
                if($formatoDocumento=="word" && !seAluno()){
                    $this->exibirWord($nomeDownload);
                }else if($formatoDocumento=="excel" && !seAluno()){
                    $this->exibirExcel($nomeDownload);
                }else{
                    $this->dompdf->load_html($this->html);
                    $this->dompdf->setPaper($tamanhoFolha, $orientacao);
                    //Renderizar o html
                    $this->dompdf->render();
                    //Exibibir a página
                    $this->dompdf->stream(
                        str_replace("/", "-", $nomeDownload), 
                        array(
                            "Attachment" => false //Para realizar o download somente alterar para true
                        )
                    );

                    $this->guardarArquivoNoServidor($directorio, str_replace("/", "-", $nomeArquivo).".pdf");
                }
                
            }
        }
        private function exibirWord($nomeDocumento){
            header("Content-Type: application/vnd.msword");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("content-disposition: attachment;filename=".$nomeDocumento.".doc");
            echo utf8_decode($this->html);
        }

        function nomeDirigente($cargo){

            $dirigente = $this->selectArray("entidadesprimaria", ["generoEntidade", "nomeEntidade", "tituloNomeEntidade"], ["escola.idEntidadeEscola"=>$_SESSION["idEscolaLogada"], "escola.nivelSistemaEntidade"=>$cargo, "escola.estadoActividadeEntidade"=>"A"], ["escola"]);

            $this->sexoDirigente = valorArray($dirigente, "generoEntidade");
            if($this->sexoDirigente=="F"){
                $this->art1Dirigente="a";
                $this->art2Dirigente="a";
            }else{
                $this->art1Dirigente="o";
                $this->art2Dirigente="";
            }
            $this->nomeDirigente = valorArray($dirigente, "nomeEntidade");
            $this->tituloNomeDirigente= valorArray($dirigente, "tituloNomeEntidade");
            return valorArray($dirigente, "nomeEntidade");
        }

        private function exibirExcel($nomeDocumento){
            $nomeArquivo = $nomeDocumento.'.xls';
            header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
            header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
            header ("Cache-Control: no-cache, must-revalidate");
            header ("Pragma: no-cache");
            header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
            header ("Content-Disposition: attachment; filename=\"{$nomeArquivo}\"" );
            header ("Content-Description: PHP Generated Data" );
            echo utf8_decode($this->html);
        }
 
        public function tratarVermelha($nota, $css="", $notaMedia, $numeroCasasDecimais="indef"){

            if(is_numeric($nota) && $numeroCasasDecimais!="indef"){
                $nota = number_format(floatval($nota), intval($numeroCasasDecimais), ",", ".");
            }

            if($nota==""){
                return "<td style='".$this->text_center.$this->border().$css."'></td>";
            }else if(floatval($nota)<$notaMedia){
                return "<td style='".$this->text_center.$this->vermelha.$this->border().$css."'>".$nota."</td>";
            }else{
                return "<td style='".$this->text_center.$this->azul.$this->border().$css."'>".$nota."</td>";
            }  
        }

        public function retornarNotaExtensa($nota){
            return $this->leitorNumero->numeroExtenso($nota);                
        }

        public function negarAcesso(){
            $this->html ="<html>
            <head>
                <title>ACESSO NEGADO</title>
            </head>
            <body style='background-color:red; margin:-37px; margin-left:-45px; margin-right:-45px; padding:40px; '><br/><br/><br/><p style='font-size:16pt;".$this->text_center.$this->maiuscula."; font-family: algerian;'>Este documento não é permitido a visualização a qualquer usuário. PARA TER ACESSO AOS DADOS DEVE ESTAR DEVIDAMENTE AUTORIZADO.</p>
            <div style='margin-top:50px;".$this->text_center."'><img src='".$this->enderecoArquivos."icones/cadeado.png' style='width:300px; height:400px;'></div>
            <br/><br/><br/><p style='font-size:16pt; font-family: algerian;".$this->text_center."'>PARA MAIS INFORMAÇÕES CONTACTE OS ADMINISTRADORES DO ANGOSCHOOL.<br/><br/><strong>926930664</strong></p>
                
            </body>
            </html>
            ";
            $this->exibir("", "", "", "Acesso negado...");
        }
        private function estilizadorCssEmPhp(){
            $this->text_center="text-align:center;";
            $this->text_justify="text-align:justify;";
            $this->text_right="text-align:right;";
            $this->text_left="text-align:left;";
            $this->miniParagrafo = "margin-bottom:-15px;";
            $this->maxParagrafo = "margin-bottom:30px;";
            $this->mini_miniParagrafo = "margin-bottom:-19px;";
            $this->bolder = "font-weight: bolder;";
            $this->minuscula ="text-transform: lowercase;";
             $this->maiuscula ="text-transform: uppercase;";
            $this->capitalize ="text-transform: capitalize;";
            $this->insignia_medio="width: 70px;height: 70px;";
            $this->insignia_mini_medio ="width: 100px; height: 100px;";
            $this->corPrimary="background-color: #428bca;";

            $this->corDanger="background-color: ".valorArray($this->sobreUsuarioLogado, "corCabecalhoTabelas")."; color:".valorArray($this->sobreUsuarioLogado, "corLetrasCabecalhoTabelas").";";

            $this->tabela ="border: solid black 1px;border-spacing: 0px;";
            $this->sublinhado ="text-decoration: underline;";
            $this->linhaDirigente = "margin-bottom: -15px;text-align: center;";
            $this->tituloDirigente = $this->nomeDirigente = "text-align: center;";
            $this->vermelha ="color: red;";
            $this->verde ="color: darkgreen;";
            $this->azul ="color: darkblue;";
            $this->displayNone ="display: none;";
        }
        public function border($color="black", $tamanho="1px", $tipo="solid"){
            return "border:".$tipo." ".$color." ".$tamanho.";";
        }
        public function tamanhoCss($tamanho){
            return "width:".$tamanho.";";
        }
        public function backGround($cor){
            return "background-color:".$cor.";";
        }

        public function fontSize($tamanho){
            return "font-size:".$tamanho.";";
        }

        public function fundoDocumento($caminhoRecuar, $posicaoDocumento="vertical"){

            $estadoMarcaDeAgua =$this->selectUmElemento("escolas", "estado", ["idPEscola"=>$_SESSION["idEscolaLogada"], "estadoperiodico.objecto"=>"marcaAgua"], ["estadoperiodico"]); 

            if(valorArray($this->sobreUsuarioLogado, "formatoDocumentoEnt")!="excel" && valorArray($this->sobreUsuarioLogado, "formatoDocumentoEnt")!="word" && $estadoMarcaDeAgua=="V"){

                if($posicaoDocumento=="vertical"){
                    return "<div style='border:none !important; position:absolute;".$this->text_center."padding-right:40px;'><img src='".$caminhoRecuar."Ficheiros/Escola_".$_SESSION['idEscolaLogada']."/Icones/".valorArray($this->sobreUsuarioLogado, 'logoEscola')."' style='position:absolute; margin-left:50px; border:none !important; margin-top:200px; opacity:0.15; width:650px; height:650px;'></div>";
                }else{
                    return "<div style='border:none !important; position:absolute;".$this->text_center."padding-right:40px;'><img src='".$caminhoRecuar."Ficheiros/Escola_".$_SESSION['idEscolaLogada']."/Icones/".valorArray($this->sobreUsuarioLogado, 'logoEscola')."' style='position:absolute; margin-left:180px; border:none !important; margin-top:70px; opacity:0.15; width:650px; height:650px;'></div>";
                }
                
            }else{
                return "";
            }
            
        }
        public function avaliacaoQualitativa($nota, $i=1){
            if((int)$nota<5*$i){
                return "<span style='".$this->vermelha."'>Mau</span>";
            }else if((int)$nota<7*$i){
                return "<span style='".$this->azul."'>Suficiente</span>";
            }else if((int)$nota<8.5*$i){
                return "<span style='".$this->azul."'>Bom</span>";
            }else{
                return "<span style='".$this->verde."'>Muito Bom</span>";
            }
        }
        public function bomMau($nota){
            if($nota=="Mau"){
                return "<span style='".$this->vermelha."'>Mau</span>";
            }else if($nota=="Suficiente"){
                return "<span style='".$this->azul."'>Suficiente</span>";
            }else if($nota=="Bom"){
                return "<span style='".$this->azul."'>Bom</span>";
            }else if($nota=="Muito Bom"){
                return "<span style='".$this->verde."'>Muito Bom</span>";
            }
        }

        public function contadorDadosPorIdade($array, $valorSexo="", $sinalIgual1="", $valor1="", $sinalIgual2="", $valor2="", $dataEmReferencia="", $nomeCampoSexo="generoEntidade", $nomeCampoDataNascimento="dataNascEntidade"){

            $dataEmReferencia = ($dataEmReferencia=="")?$this->dataSistema:$dataEmReferencia;

            $novoArray=array();
            foreach($array as $a){
                if(seComparador($valorSexo, $a->$nomeCampoSexo) && seComparador($valor1, calcularIdade(explode("-", $dataEmReferencia)[0], $a->$nomeCampoDataNascimento), $sinalIgual1) && seComparador($valor2, calcularIdade(explode("-", $dataEmReferencia)[0], $a->$nomeCampoDataNascimento), $sinalIgual2) ){
                    $novoArray[]=$a;
                }
            }
            return $novoArray;
        }

        function guardarArquivoNoServidor($directorio="", $nomeArquivo=""){ 

            /*if($directorio!="" && $nomeArquivo!=""){

                if(is_dir($this->caminhoRetornar.$directorio)){
                    if(file_exists($this->caminhoRetornar.$directorio."/".$nomeArquivo)){
                        unlink($this->caminhoRetornar.$directorio."/".$nomeArquivo);
                    }
                  file_put_contents($this->caminhoRetornar.$directorio."/".$nomeArquivo,  $this->dompdf->output());
                }
            }*/
        }
    }
?>