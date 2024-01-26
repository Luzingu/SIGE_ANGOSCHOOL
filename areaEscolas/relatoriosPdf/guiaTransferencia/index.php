<?php if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');
 
    class relatorioTransferencia extends funcoesAuxiliares{

        function __construct($caminhoAbsoluto){
            parent::__construct("Rel-Guia de Transferência");     
             $this->idPTransferencia = isset($_GET["idPTransferencia"])?$_GET["idPTransferencia"]:null;
             $this->idPMatricula = isset($_GET["idPMatricula"])?$_GET["idPMatricula"]:null;


            $this->aluno = $this->selectArray("alunosmatriculados", [], ["transferencia.idTransfEscolaOrigem"=>$_SESSION['idEscolaLogada'], "transferencia.idTransfAno"=>$this->idAnoActual, "idPMatricula"=>$this->idPMatricula, "transferencia.idPTransferencia"=>$this->idPTransferencia, "escola.idMatEscola"=>$_SESSION['idEscolaLogada']], ["transferencia", "escola"]);

            $this->aluno = $this->anexarTabela2($this->aluno, "escolas", "transferencia", "idPEscola", "idTransfEscolaDestino"); 


            $this->aluno = $this->anexarTabela($this->aluno, "div_terit_paises", "idPPais", "paisNascAluno");
            $this->aluno = $this->anexarTabela($this->aluno, "div_terit_provincias", "idPProvincia", "provNascAluno");
            $this->aluno = $this->anexarTabela($this->aluno, "div_terit_municipios", "idPMunicipio", "municNascAluno");
            $this->aluno = $this->anexarTabela($this->aluno, "div_terit_comunas", "idPComuna", "comunaNascAluno");


            $this->periodoAluno = valorArray($this->aluno, "periodoAluno", "escola");

            if($this->periodoAluno=="reg"){
                $this->periodoAluno="regular";
            }else{
                $this->periodoAluno="Pós-Laboral";
            }
            $this->idPCurso = valorArray($this->aluno, "idMatCurso", "escola");
            $this->classe = valorArray($this->aluno, "classeActualAluno", "escola");
            $this->turma = valorArray($this->aluno, "turmaTransferencia", "transferencia");
            $this->idPAno = valorArray($this->aluno, "idTransfAno", "transferencia");

            if(valorArray($this->aluno, "idTransfEscolaDestino", "transferencia")=="" || valorArray($this->aluno, "idTransfEscolaDestino", "transferencia")==NULL){

                $this->nomeEscolaTransf = valorArray($this->aluno, "nomeEscolaDestino", "transferencia");

                $this->sobreProvincia = $this->selectArray("div_terit_provincias", [], ["idPProvincia"=>valorArray($this->aluno, "nomeProvinciaDestino", "transferencia")]);

                $this->sobreMunicipio = $this->selectArray("div_terit_municipios", [], ["idPMunicipio"=>valorArray($this->aluno, "nomeMunicipioDestino", "transferencia")]);

                $this->sobreComuna = $this->selectArray("div_terit_comunas", [], ["idPComuna"=>valorArray($this->aluno, "nomeComunaDestino", "transferencia")]);
            }else{
                $this->nomeEscolaTransf = valorArray($this->aluno, "nomeEscola");

                $this->sobreProvincia = $this->selectArray("div_terit_provincias", [], ["idPProvincia"=>valorArray($this->aluno, "provincia")]);

                $this->sobreMunicipio = $this->selectArray("div_terit_municipios", [], ["idPMunicipio"=>valorArray($this->aluno, "municipio")]);

                $this->sobreComuna = $this->selectArray("div_terit_comunas", [], ["idPComuna"=>valorArray($this->aluno, "comuna")]);
            }

            $styleHtmlBody ="font-size: 13pt !important; margin: 30px !important; margin-top: 20px; font-family: Times New Roman !important;";
            $this->nomeCurso();
            $this->numAno();

            $this->html="<html style='".$styleHtmlBody."'>
            <head>
                <title>Guia de Transferência</title>
                
            </head>
            <body style='".$styleHtmlBody."'>";
            
            

            if($this->verificacaoAcesso->verificarAcesso("", ["novaTrasferencia", "transferenciaEfectuada"], [$this->classe, $this->idPCurso], "")){
                $this->modeloIPAG();                           
            }else{
              $this->negarAcesso();
            }
            
        }

         private function modeloIPAG(){
            if(valorArray($this->aluno, "sexoAluno")=="M"){
                $art1="o";
                $art2 ="";
            }else{
                 $art1="a";
                $art2 ="a";
            }

            $this->html .=$this->fundoDocumento("../../../").$this->cabecalho()." 
            <p style='".$this->text_center.$this->miniParagrafo.$this->bolder.$this->sublinhado."line-height: 20px;'>GUIA DE TRANSFERÊNCIA N.º &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/".valorArray($this->sobreUsuarioLogado, "abrevNomeEscola")."/".explode("-", valorArray($this->aluno, "dataTransferencia", "transferencia"))[0]."</p><br/>
            <p style='".$this->text_justify." text-indent:0px; line-height: 26px;'>A pedido do(a) encarregado(a) da educação d".$art1." alun".$art1." <strong style='".$this->maiuscula."'>".valorArray($this->aluno, "nomeAluno")."</strong> filh".$art1." de ".tratarCamposVaziosComEComercial(valorArray($this->aluno, "paiAluno"), 15)." e de ".tratarCamposVaziosComEComercial(valorArray($this->aluno, "maeAluno"), 15).", natural ".valorArray($this->aluno, "preposicaoComuna2")." ".valorArray($this->aluno, "nomeComuna").", município ".valorArray($this->aluno, "preposicaoMunicipio2")." ".valorArray($this->aluno, "nomeMunicipio").", província ".valorArray($this->aluno, "preposicaoProvincia2")." ".valorArray($this->aluno, "nomeProvincia").", nascid".$art1." aos ".dataExtensa(valorArray($this->aluno, "dataNascAluno")).", portador".$art2." do Bilhete de Identidade n.º ".tratarCamposVaziosComEComercial(valorArray($this->aluno, "biAluno"), 10).", passado pelo arquivo de identificação de Luanda, aos ".dataExtensa(valorArray($this->aluno, "dataEBIAluno")).", alun".$art1." matriculad".$art1.", para o ano lectivo ".$this->numAno;

             if($this->classe>=10){
                if($this->tipoCurso=="pedagogico"){
                    $this->html .=", Curso: ".$this->areaFormacaoCurso.", Opção: ".$this->nomeCurso;
                }else if($this->tipoCurso=="tecnico"){
                    $this->html .=", Área de Formação: ".$this->areaFormacaoCurso.", Curso: ".$this->nomeCurso;
                }else{
                    $this->html .=", Curso: ".$this->nomeCurso;
                }
             }
             
             if(valorArray($this->sobreUsuarioLogado, "periodosEscolas")=="regPos"){
                $this->html .=" (em regime ". $this->periodoAluno.")";
             }
            $this->html .=" na ".$this->classeExtensa.", turma ".$this->turma.".</p>
            <p style='line-height: 26px;".$this->text_justify."'>É transferid".$art1." para comuna ".valorArray($this->sobreComuna, "preposicaoComuna2")." ".valorArray($this->sobreComuna, "nomeComuna").", município ".valorArray($this->sobreMunicipio, "preposicaoMunicipio2")." ".valorArray($this->sobreMunicipio, "nomeMunicipio").", província ".valorArray($this->sobreProvincia, "preposicaoProvincia2")." ".valorArray($this->sobreProvincia, "nomeProvincia")."</strong>, no(a) <strong>".$this->nomeEscolaTransf."</strong>.</p>
            

            <p style='".$this->bolder."'>Faz acompanhar em anexo os seguintes documentos:</p><ol style='line-height: 25px;'>";

            $documentos = explode(";", valorArray($this->aluno, "documentosAnexados", "transferencia"));
            $contador=0;
            foreach ($documentos as $documento) {
                $contador++;
                $this->html .="<div>".$contador.". ".trim($documento)."</div>";
            }
            $this->html .="</ol>
            
            <p style='padding-left:10px; line-height:26px; padding-right:10px;".$this->text_justify."margin-top:10px;'>Por ser verdade e me ter sido solicitada, mandei passar a presente guia que vai por mim assinada e autenticada com o carrimbo a óleo em uso neste ".explode(" ", valorArray($this->sobreEscolaLogada, "nomeEscola"))[0].".</p>
            
            <p style='padding-left:10px; padding-right:10px;'>".$this->rodape().".</p>
            <div style='margin-top:-15px;'>".$this->assinaturaDirigentes(7)."</div>";
            
            $this->exibir("", "Guia de Transferência-".valorArray($this->aluno, "nomeAluno"));
        }
    }

new relatorioTransferencia(__DIR__);
    
    
  
?>