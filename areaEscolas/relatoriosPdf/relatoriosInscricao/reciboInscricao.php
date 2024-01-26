<?php if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');

    class lista extends funcoesAuxiliares{

        function __construct($caminhoAbsoluto){
            parent::__construct("Rel-Recibo de Inscrição");
            $this->idPAluno = isset($_GET["idPAluno"])?$_GET["idPAluno"]:null;
            $this->idPAno = isset($_GET["idPAno"])?$_GET["idPAno"]:$this->idAnoActual;
            $this->numAno();
            $this->conDb("inscricao");
  
            $this->aluno = $this->selectArray("alunos", [], ["idPAluno"=>$this->idPAluno, "idAlunoAno"=>$this->idPAno, "idAlunoEscola"=>$_SESSION['idEscolaLogada']], ["inscricao"]);

            if((valorArray($this->aluno, "idAlunoEntidade")==$_SESSION["idUsuarioLogado"] || $this->verificacaoAcesso->verificarAcesso("", ["inscricaoNovaInscricao"], [], ""))){
                $this->recibo();               
            }else{
                 $this->negarAcesso();
            }
            
        }

        private function recibo(){
            
          $this->conDb();
           if(valorArray($this->aluno, "sexoAluno")=="M"){
                $this->art1="o";
                $this->art2 ="";
            }else{
                $this->art1="a";
                $this->art2 ="a";
            }
             $periodo = valorArray($this->aluno, "periodoAluno");
             if($periodo=="reg"){
                $periodo="Regular";
             }else if($periodo=="pos"){
                $periodo="Pós-Laboral";
             }
                        
            $numero=0;
            
            $this->html .="
           <html style='margin:0px;'>
            <head>
                <title>Comprovatico de Inscrição</title>
                <style>
                    .tabela tr td{
                        border-left: solid black 1px;
                        border-bottom: solid black 1px;
                        padding:1px;

                    }

                    .tabela2 tr td{
                        border-left: solid black 1px;
                        border-bottom: solid black 1px;
                        padding:4px;

                    }
                </style>
            </head>
           <body style='margin:20px; margin-bottom:0px; margin-top:10px; padding-top:10px;'>".$this->fundoDocumento("../../../")."
           
           <div style='border:solid black 2px; padding:5px; height:120px;'>
            <div style='padding-top:10px;'><img src='".$_SERVER['DOCUMENT_ROOT']."/angoschool/icones/insignia.jpg' style='height:90px; width:90px;'></div>
            <div style='margin-left:125px; margin-top:-200px;'> 
                <p style='".$this->miniParagrafo." margin-top:-0px;'>REPÚBLICA DE ANGOLA</p>
                <p style='".$this->miniParagrafo."'>GOVERNO PROVINCIAL DO ZAIRE</p>
                <p style='".$this->miniParagrafo."'>GABINETE PROVINCIAL DA EDUCAÇÃO</p>
                <p style='".$this->maiuscula.$this->miniParagrafo."'>";

              if($_SESSION["idEscolaLogada"]==17 && $this->tipoCurso=="geral"){
                
                    $this->html .="Liceu do Tuku<br/>Mbanza Kongo";
                
              }else{
                    $this->html .=valorArray($this->sobreUsuarioLogado, "tituloEscola");
              }
              $this->html .="</p>
                <p style='".$this->bolder.$this->text_center.$this->miniParagrafo."'>BOLETIM DE INSCRIÇÃO</p>
                <p style='".$this->bolder.$this->miniParagrafo.$this->text_center." font-size:18pt;  margin-top:10px;'>".$this->numAno."</p>
            </div>
           </div>
           <div style='border:solid black 2px; margin-top:10px; background-color: rgba(0, 0, 0, 0.5); color:white;".$this->text_center." padding-top:5px;'>
            <strong>Dados do Aluno</strong>

            <div style='border:solid black 2px; padding:5px; background-color:white;color:black; margin-top:5px; margin-bottom:5px; border-left:none; border-right:none;'>
                <table class='tabela' style='width:100%; '>
                    <tr>
                        <td style='".$this->text_right." border:none;'></td><td colspan='2' style='border:none;'></td>

                        <td style='".$this->text_right."border:none;'>Data:</td><td><strong>".valorArray($this->aluno, "dataInscricao", "inscricao")."</strong></td>
                    </tr>
                    <tr>
                        <td style='".$this->text_right."'>Nome:</td>
                        <td colspan='5'><strong>".valorArray($this->aluno, "nomeAluno")."</strong></td>
                    </tr>
                    <tr>
                        <td style='".$this->text_right."'>Sexo:</td>
                        <td><strong>".generoExtenso(valorArray($this->aluno, "sexoAluno"))."</strong></td>
                        <td style='".$this->text_right."'>N.º BI:</td>
                        <td colspan='2'><strong>".valorArray($this->aluno, "biAluno")."</strong></td>
                    </tr>

                    <tr>
                        <td style='".$this->text_right."'>Data de Nasc.:</td>
                        <td colspan='2'><strong>".dataExtensa(valorArray($this->aluno, "dataNascAluno"))."</strong></td>
                        <td style='".$this->text_right."'>País:</td>
                        <td><strong>".$this->selectUmElemento("div_terit_paises", "nomePais", ["idPPais"=>valorArray($this->aluno, "paisNascAluno")])."</strong></td>
                    </tr>
                    <tr>
                        <td style='".$this->text_right."'>Província:</td>
                        <td colspan='2'><strong>".$this->selectUmElemento("div_terit_provincias", "nomeProvincia", ["idPProvincia"=>valorArray($this->aluno, "provNascAluno")])."</strong></td>
                        <td style='".$this->text_right."'>Municipio:</td>
                        <td><strong>".$this->selectUmElemento("div_terit_municipios", "nomeMunicipio", ["idPMunicipio"=>valorArray($this->aluno, "municNascAluno")])."</strong></td>
                    </tr>

                    <tr>
                        <td style='".$this->text_right."'>Pai:</td>
                        <td><strong>".valorArray($this->aluno, "paiAluno")."</strong></td>
                        <td style='".$this->text_right."'>Mãe:</td>
                        <td colspan='2'><strong>".valorArray($this->aluno, "maeAluno")."</strong></td>
                    </tr>
                    <tr>
                        <td style='".$this->text_right."'>Residência:</td>
                        <td><strong>".$this->selectUmElemento("div_terit_municipios", "nomeMunicipio", ["idPMunicipio"=>valorArray($this->sobreUsuarioLogado, "municipio")])."</strong></td>
                        <td style='".$this->text_right."'>N.º de Telefone:</td>
                        <td colspan='2'>".valorArray($this->aluno, "telefoneAluno")."</td>
                    </tr>
                </table>
            </div>
            
            <strong>Dados Académicos</strong>

            <div style='border:solid black 2px; padding:5px; background-color:white;color:black;margin-top:5px; border-left:none; border-right:none;'>
                    <table class='tabela' style='width:100%; '>

                    <tr>
                        <td style='".$this->text_right."'>Código:</td><td colspan='5'><strong>".valorArray($this->aluno, "codigoAluno")."</strong></td>
                    </tr>";

                    $this->conDb();

                    $this->html .="
                    <tr>
                        <td colspan='6' style='".$this->bolder.$this->text_center."'>Opções (1)</td>
                    </tr>";


                        $sobreCurso = $this->selectArray("nomecursos", [], ["idPNomeCurso"=>valorArray($this->aluno, "idInscricaoCurso", "inscricao")]);

                        if(valorArray($sobreCurso, "tipoCurso")=="tecnico"){
                            $this->html .="
                            <tr>
                            <td style='".$this->text_right."'>Área de Formação:</td><td><strong>".valorArray($sobreCurso, "areaFormacaoCurso")."</strong></td>

                                <td style='".$this->text_right."'>Curso:</td><td><strong>".valorArray($sobreCurso, "nomeCurso")."</strong></td>
                                <td style='".$this->text_right."'>Período:</td><td><strong>".periodoExtenso(valorArray($this->aluno, "periodoInscricao", "inscricao"))."</strong></td>
                            </tr>";
                        }else if(valorArray($sobreCurso, "tipoCurso")=="pedagogico"){
                            $this->html .="
                            <tr>
                            <td style='".$this->text_right."'>Curso:</td><td><strong>".valorArray($sobreCurso, "areaFormacaoCurso")."</strong></td>

                                <td style='".$this->text_right."'>Opção:</td><td><strong>".valorArray($sobreCurso, "nomeCurso")."</strong></td>
                                <td style='".$this->text_right."'>Período:</td><td><strong>".periodoExtenso(valorArray($this->aluno, "periodoInscricao", "inscricao"))."</strong></td>
                            </tr>";
                        }else{
                            $this->html .="
                            <tr>
                                <td style='".$this->text_right."'>Curso:</td><td colspan='3'><strong>".valorArray($sobreCurso, "nomeCurso")."</strong></td>
                                <td style='".$this->text_right."'>Período:</td><td><strong>".periodoExtenso(valorArray($this->aluno, "periodoInscricao", "inscricao"))."</strong></td>
                            </tr>";
                        }

                    $sobreEntidade = $this->selectArray("entidadesprimaria", ["nomeEntidade", "generoEntidade"], ["idPEntidade"=>valorArray($this->aluno, "idAlunoEntidade")]);
                         
                    $this->html .="                        
                    </table>

                    <div style='width: 50%;'>";
                    if(valorArray($sobreEntidade, "generoEntidade")=="M"){
                        $this->html .=$this->porAssinatura("O Funcionário", valorArray($sobreEntidade, "nomeEntidade"));
                    }else{
                        $this->html .=$this->porAssinatura("A Funcionária", valorArray($sobreEntidade, "nomeEntidade"));
                    }

                    $this->html .= "
                    </div>
                    <div style='width: 50%;margin-top:-300px; margin-left:50%;'>".$this->porAssinatura("<span style='".$this->maiuscula."'>".$this->art1."</span> Alun".$this->art1, valorArray($this->aluno, "nomeAluno"), "", strlen(valorArray($this->aluno, "nomeAluno")))."
                    </div><br/></div> 
                </div>   
           
           ";
           
           
           
           //Última Folha do recibo...

           $this->html .="<br><hr><br><div>
           <div style='border:solid black 2px; padding:5px; height:55px;'>
            <div style='padding-top:20px;'></div>
            <div style='margin-top:-200px;'>
                <p style='".$this->maiuscula.$this->miniParagrafo.$this->text_center."'>";

              if($_SESSION["idEscolaLogada"]==17 && $this->tipoCurso=="geral"){
                
                    $this->html .="Liceu do Tuku<br/>Mbanza Kongo";
                
              }else{
                    $this->html .=valorArray($this->sobreUsuarioLogado, "nomeEscola");
              }
              $this->html .="</p>
                <p style='".$this->bolder.$this->text_center.$this->miniParagrafo."'>COMPROVATIVO DE INSCRIÇÃO</p>
            </div>
           </div>
           <div style='border:solid black 2px; background-color: rgba(0, 0, 0, 0.5); color:white;".$this->text_center." padding-top:5px;'>
            <strong>Dados do Aluno</strong>

            <div style='border:solid black 2px; padding:5px; background-color:white;color:black; margin-top:5px; margin-bottom:5px; border-left:none; border-right:none;'>
                <table class='tabela2' style='width:100%; '>
                    <tr>
                        <td style='".$this->text_right." border:none;'></td><td colspan='2' style='border:none;'></td>

                        <td style='".$this->text_right."border:none;'>Data:</td><td><strong>".valorArray($this->aluno, "dataInscricao", "inscricao")."</strong></td>
                    </tr>
                    <tr>
                        <td style='".$this->text_right."'>Nome Completo:</td>
                        <td colspan='5'><strong>".valorArray($this->aluno, "nomeAluno")."</strong></td>
                    </tr>
                    <tr>
                        <td style='".$this->text_right."'>Sexo:</td>
                        <td><strong>".generoExtenso(valorArray($this->aluno, "sexoAluno"))."</strong></td>
                        <td style='".$this->text_right."'>N.º BI:</td>
                        <td colspan='2'><strong>".valorArray($this->aluno, "biAluno")."</strong></td>
                    </tr>

                </table>
            </div>
            <strong>Dados Académicos</strong>

            <div style='border:solid black 2px; padding:5px; background-color:white;color:black;margin-top:5px; border-left:none; border-right:none;'>
                    <table class='tabela' style='width:100%; '>

                    <tr>
                        <td style='".$this->text_right."'>Código:</td><td colspan='5'><strong>".valorArray($this->aluno, "codigoAluno")."</strong></td>
                    </tr>
                    <tr>
                        <td colspan='6' style='".$this->bolder.$this->text_center."'>Opções (1)</td>
                    </tr>";


                        $sobreCurso = $this->selectArray("nomecursos", [], ["idPNomeCurso"=>valorArray($this->aluno, "idInscricaoCurso", "inscricao")]);

                        if(valorArray($sobreCurso, "tipoCurso")=="tecnico"){
                            $this->html .="
                            <tr>
                            <td style='".$this->text_right."'>Área de Formação:</td><td><strong>".valorArray($sobreCurso, "areaFormacaoCurso")."</strong></td>

                                <td style='".$this->text_right."'>Curso:</td><td><strong>".valorArray($sobreCurso, "nomeCurso")."</strong></td>
                                <td style='".$this->text_right."'>Período:</td><td><strong>".periodoExtenso(valorArray($this->aluno, "periodoInscricao", "inscricao"))."</strong></td>
                            </tr>";
                        }else if(valorArray($sobreCurso, "tipoCurso")=="pedagogico"){
                            $this->html .="
                            <tr>
                            <td style='".$this->text_right."'>Curso:</td><td><strong>".valorArray($sobreCurso, "areaFormacaoCurso")."</strong></td>

                                <td style='".$this->text_right."'>Opção:</td><td><strong>".valorArray($sobreCurso, "nomeCurso")."</strong></td>
                                <td style='".$this->text_right."'>Período:</td><td><strong>".periodoExtenso(valorArray($this->aluno, "periodoInscricao", "inscricao"))."</strong></td>
                            </tr>";
                        }else{
                            $this->html .="
                            <tr>
                                <td style='".$this->text_right."'>Curso:</td><td colspan='3'><strong>".valorArray($sobreCurso, "nomeCurso")."</strong></td>
                                <td style='".$this->text_right."'>Período:</td><td><strong>".periodoExtenso(valorArray($this->aluno, "periodoInscricao", "inscricao"))."</strong></td>
                            </tr>";
                        }
                    $sobreEntidade = $this->selectArray("entidadesprimaria", ["generoEntidade", "nomeEntidade"], ["idPEntidade"=>valorArray($this->aluno, "idAlunoEntidade")]);
                         

                       $this->html .="                        
                    </table>";
                    if(valorArray($sobreEntidade, "generoEntidade")=="M"){
                        $this->html .=$this->porAssinatura("O Funcionário", valorArray($sobreEntidade, "nomeEntidade"));
                    }else{
                        $this->html .=$this->porAssinatura("A Funcionária", valorArray($sobreEntidade, "nomeEntidade"));
                    }

                $this->html .="<br/></div>              
               
            </div></body></html>";
            
           $this->exibir("", "Recibo de Inscrição-".valorArray($this->aluno, "nomeAluno"));
        }
    }

new lista(__DIR__);
    
    
  
?>