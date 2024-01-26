<?php 
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');

    class mapaForcaTrabalho extends funcoesAuxiliares{

        function __construct($caminhoAbsoluto){
            parent::__construct("Rel-Lista de Pessoal Docente");
            $this->idPAno = $this->idAnoActual;
            $this->numAno();
            $this->tamanhoFolha = isset($_GET["tamanhoFolha"])?$_GET["tamanhoFolha"]:"A3";
            if($this->tamanhoFolha!="A0" || $this->tamanhoFolha!="A1"){
                $this->tamanhoFolha="A0";
            }
            $this->html="<html>
            <head>
                <title>Lista de Pessoal Docente</title>
                <style>
                  table tr td{
                    padding:3px;
                  }
                </style>
            </head>
            <body>
            <div class='cabecalho'>
            <div><div style='margin-top:20px; margin-left:50px; width:800px; position:absolute;' style='".$this->maiuscula."'>".$this->assinaturaDirigentes(7)."</div></div>".$this->cabecalho();
              
            $this->html .="<p style='".$this->text_center.$this->miniParagrafo.$this->maiuscula."'>Levantamento Estatístico</p><br/>
            <p style='".$this->text_center.$this->maiuscula.$this->bolder.$this->sublinhado."'>Mapa de Pessoal Docente</p>
                
            </div>";

            if($this->verificacaoAcesso->verificarAcesso("", ["listaAgentes"], [], "")){             
                $this->mapa();
            }else{
              $this->negarAcesso();
            }
        }

         private function mapa(){ 

            $this->lista =$this->selectArray("entidadesprimaria", [], ["escola.idEntidadeEscola"=>$_SESSION['idEscolaLogada'], "escola.estadoActividadeEntidade"=>"A", "escola.tipoPessoal"=>"docente"], ["escola"]);

            $this->lista=$this->anexarTabela2($this->lista, "escolas","escola", "idPEscola", "idEntidadeEscola");
            $this->lista=$this->anexarTabela($this->lista, "div_terit_provincias", "idPProvincia", "provincia");
            $this->lista=$this->anexarTabela($this->lista, "div_terit_municipios", "idPMunicipio", "municipio");
            $this->lista=$this->anexarTabela($this->lista, "div_terit_comunas", "idPComuna", "comuna");

           $camposColspan = array();
           $camposColspan[] =0;
           $camposColspan[] =1;
           $camposColspan[] =2;
           $camposColspan[] =3;
           $camposColspan[] =4;
           $camposColspan[] =5;
           $camposColspan[] =6;
           $camposColspanJaListados = array();

           $cabecalho[] = array('titulo' =>"Nome Completo", 'tituloDb'=>"nomeEntidade", "classCSS"=>"");
           $cabecalho[] = array('titulo' =>"Data de<br/>Nascimento", 'tituloDb'=>"dataNascEntidade", "classCSS"=>"text-align:center;");

           $cabecalho[] = array('titulo' =>"Idade", 'tituloDb'=>"idadeEntidade", "classCSS"=>"text-align:center;");
           $cabecalho[] = array('titulo' =>"Sexo", 'tituloDb'=>"generoEntidade", "classCSS"=>"text-align:center;");

            $cabecalho[] = array('titulo' =>"Número", 'tituloDb'=>"biEntidade", "campoMae"=>"Bilhete de Identidade ou Passaporte", "id"=>"0", "totalColunas"=>3, "classCSS"=>"text-align:center;");

            $cabecalho[] = array('titulo' =>"Data<br/>Emissão", 'tituloDb'=>"dataEBIEntidade" , "Bilhete de Identidade ou Passaporte", "id"=>"0", "totalColunas"=>3, "classCSS"=>"text-align:center;");

           $cabecalho[] = array('titulo' =>"Data de<br/>Caducidade", 'tituloDb'=>"dataCaducBI", "campoMae"=>"Bilhete de Identidade ou Passaporte", "id"=>"0", "totalColunas"=>3, "classCSS"=>"text-align:center;");

           $cabecalho[] = array('titulo' =>"Naturalidade", 'tituloDb'=>"nomeMunicipio", "classCSS"=>"");

           $cabecalho[] = array('titulo' =>"Pai", 'tituloDb'=>"paiEntidade", "campoMae"=>"Filhação", "id"=>"1", "totalColunas"=>2, "classCSS"=>"");
           $cabecalho[] = array('titulo' =>"Mãe", 'tituloDb'=>"maeEntidade", "campoMae"=>"Filhação", "id"=>"1", "totalColunas"=>2, "classCSS"=>"");

           $cabecalho[] = array('titulo' =>"Nº<br/>Agente", 'tituloDb'=>"numeroAgenteEntidade", "classCSS"=>"text-align:center;");
           $cabecalho[] = array('titulo' =>"Nº INSS", 'tituloDb'=>"numSegSocial", "classCSS"=>"text-align:center;");
           $cabecalho[] = array('titulo' =>"Categoria", 'tituloDb'=>"categoriaEntidade", "classCSS"=>"");
           $cabecalho[] = array('titulo' =>"Função", 'tituloDb'=>"funcaoEnt", "objecto"=>"escola", "classCSS"=>"");

           $cabecalho[] = array('titulo' =>"Nº", 'tituloDb'=>"numDespacho", "campoMae"=>"Despacho de Nomeação", "id"=>"2", "totalColunas"=>2, "classCSS"=>"text-align:center;");
           $cabecalho[] = array('titulo' =>"Data", 'tituloDb'=>"dataDespacho", "campoMae"=>"Despacho de Nomeação", "id"=>"2", "totalColunas"=>2, "classCSS"=>"text-align:center;");

           $cabecalho[] = array('titulo' =>"Habilitações<br/>Literárias", 'tituloDb'=>"nivelAcademicoEntidade", "classCSS"=>"");
           $cabecalho[] = array('titulo' =>"Especialidade", 'tituloDb'=>"cursoP", "classCSS"=>"");
           $cabecalho[] = array('titulo' =>"Local de<br/>Formação", 'tituloDb'=>"localP", "classCSS"=>"");

           $cabecalho[] = array('titulo' =>"Ensino<br/>Médio", 'tituloDb'=>"cursoEnsinoMedio", "campoMae"=>"Especialidade", "id"=>"3", "totalColunas"=>4, "classCSS"=>"");
           $cabecalho[] = array('titulo' =>"Ensino<br/>Superior", 'tituloDb'=>"cursoLicenciatura", "campoMae"=>"Especialidade", "id"=>"3", "totalColunas"=>4, "classCSS"=>"");
           $cabecalho[] = array('titulo' =>"Mestrado", 'tituloDb'=>"cursoMestrado", "campoMae"=>"Especialidade", "id"=>"3", "totalColunas"=>4, "classCSS"=>"");
           $cabecalho[] = array('titulo' =>"Doutoramento", 'tituloDb'=>"cursoDoutoramento", "campoMae"=>"Especialidade", "id"=>"3", "totalColunas"=>4, "classCSS"=>"");

           $cabecalho[] = array('titulo' =>"Com<br>Formação<br/>Pedagógica", 'tituloDb'=>"comFormPedag", "campoMae"=>"Área de Formação", "id"=>"4", "totalColunas"=>2, "classCSS"=>"");
           $cabecalho[] = array('titulo' =>"Sem<br>Formação<br/>Pedagógica", 'tituloDb'=>"semFormPedag", "campoMae"=>"Área de Formação", "id"=>"4", "totalColunas"=>2, "classCSS"=>"");

           $cabecalho[] = array('titulo' =>"Disciplinas<br/>Leccionadas", 'tituloDb'=>"disciplinaLecciona", "classCSS"=>"");

            $cabecalho[] = array('titulo' =>"Turmas que<br/>Leccionadas", 'tituloDb'=>"turmaLecciona", "classCSS"=>"");

            $cabecalho[] = array('titulo' =>"Data de Inicio de<br/>Serviço na Escola", 'tituloDb'=>"dataInicioFuncoesEntidade", "objecto"=>"escola", "classCSS"=>"text-align:center;");

            $cabecalho[] = array('titulo' =>"Nº de Tempo<br/>de Serviço na<br/>EScola", 'tituloDb'=>"numeroTermpoServico", "classCSS"=>"text-align:center;");

            $cabecalho[] = array('titulo' =>"Data de<br/>Inicio de<br/>Serviço noutra Escola", 'tituloDb'=>"dataInicOutraEsc", "campoMae"=>"Número de Anos de<br/>Serviço Comprovada", "id"=>"5", "totalColunas"=>2, "classCSS"=>"text-align:center;");

           
            $cabecalho[] = array('titulo' =>"No Sistema<br/>Educativo", 'tituloDb'=>"dataInicEduc", "campoMae"=>"Número de Anos de<br/>Serviço Comprovada", "id"=>"5", "totalColunas"=>3, "classCSS"=>"text-align:center;");

            $cabecalho[] = array('titulo' =>"Natureza do<br/>Vínculo", 'tituloDb'=>"naturezaVinc", "objecto"=>"escola", "classCSS"=>"text-align:center;");
            $cabecalho[] = array('titulo' =>"Contacto", 'tituloDb'=>"numeroTelefoneEntidade", "classCSS"=>"text-align:center;");

            $cabecalho[] = array('titulo' =>"Professor", 'tituloDb'=>"cargoProfessor", "campoMae"=>"Carga Horária<br/> do Docente", "id"=>"6", "totalColunas"=>2, "classCSS"=>"text-align:center;");
            $cabecalho[] = array('titulo' =>"Pedagógico", "objecto"=>"escola", 'tituloDb'=>"cargoPedagogicoEnt", "campoMae"=>"Carga Horária<br/> do Docente", "id"=>"6", "totalColunas"=>2, "classCSS"=>"text-align:center;");


            $this->html .="<table style='".$this->tabela." width:100%;'><tr  style='".$this->corDanger."'><td rowspan='2' style='".$this->text_center.$this->border()."'>Nº</td>";
            foreach ($cabecalho as $cab) {
                if(!isset($cab["id"])){
                    $this->html .="<td rowspan='2' style='".$this->text_center.$this->border()."'>".$cab["titulo"]."</td>";
                }else{
                    $ja="nao";
                    foreach ($camposColspanJaListados as $campoL) {
                        if($campoL==$cab["id"]){
                            $ja="sim";
                        }
                    }
                    if($ja=="nao"){
                        $camposColspanJaListados[]=$cab["id"];
                        $this->html .="<td colspan='".$cab["totalColunas"]."' style='".$this->text_center.$this->border()."'>".$cab["campoMae"]."</td>";
                    }
                }
            }
            $this->html .="</tr><tr style='".$this->corDanger."'>";
                foreach ($camposColspan as $campo) {
                    foreach ($cabecalho as $cb) {
                        if(isset($cb["id"]) && $cb["id"]==$campo){
                            $this->html .="<td style='".$this->text_center.$this->border().$this->corDanger."'>".$cb["titulo"]."</td>";
                        }
                    }
                }
            $this->html .="</tr>";


            $i=0;
            foreach ($this->lista as $profs) {

                $divisaoProfessores = $this->selectArray("divisaoprofessores", [], ["idDivEntidade"=>$profs["idPEntidade"], "idDivEscola"=>$_SESSION["idEscolaLogada"], "idDivAno"=>$this->idPAno]);

                $i++;
                if($i%2==0){
                   $this->html .="<tr style='".$this->backGround("rgb(220,220,220)")."'>";
                }else{
                    $this->html .="<tr>";
                }

                $this->html .="<td style='".$this->text_center.$this->border()."'>".completarNumero($i)."</t>";
               foreach ($cabecalho as $cab) {
                   $campo = $cab["tituloDb"];
                   if(isset($cab["objecto"])){
                    $valorTabel = isset($profs["escola"][$campo])?$profs["escola"][$campo]:""; 
                   }else{
                    $valorTabel = isset($profs[$campo])?$profs[$campo]:"";
                   }
                   
                   $cssAdicional="";
                    if($cab["tituloDb"]=="dataNascEntidade" || $cab["tituloDb"]=="dataEBIEntidade" || $cab["tituloDb"]=="dataCaducBI" || $cab["tituloDb"]=="dataDespacho" || $cab["tituloDb"]=="dataInicioFuncoesEntidade" || $cab["tituloDb"]=="dataInicOutraEsc" || $cab["tituloDb"]=="dataInicEduc"){
                        $valorTabel = converterData($valorTabel);

                    }else if($cab["tituloDb"]=="idadeEntidade"){
                        $valorTabel = calcularIdade(explode("-", $this->dataSistema)[0], $profs["dataNascEntidade"])." Anos";
                    }else if($cab["tituloDb"]=="tempoServOutraEsc"){
                        $valorTabel .=" Anos";
                    }else if($cab["tituloDb"]=="cursoP" || $cab["tituloDb"]=="localP"){

                        if($profs["nivelAcademicoEntidade"]=="Médio"){
                           $areaFormacao = $profs["cursoEnsinoMedio"];
                           $escola = $profs["escolaEnsinoMedio"];
                        }else if($profs["nivelAcademicoEntidade"]=="Licenciado" || $profs["nivelAcademicoEntidade"]=="Bacharel"){
                            $areaFormacao = $profs["cursoLicenciatura"];
                            $escola = $profs["escolaLicenciatura"];
                        }else if($profs["nivelAcademicoEntidade"]=="Mestre"){
                            $areaFormacao = $profs["cursoMestrado"];
                            $escola = $profs["escolaMestrado"];
                        }else if($profs["nivelAcademicoEntidade"]=="Doutor"){
                           $areaFormacao = $profs["cursoDoutoramento"];
                           $escola = $profs["escolaDoutoramento"];
                        }else{
                            $areaFormacao="";
                            $escola ="";
                        }
                        if($cab["tituloDb"]=="cursoP"){
                            $valorTabel = $areaFormacao;
                        }else{
                            $valorTabel = $escola;
                        }
                    }else if($cab["tituloDb"]=="comFormPedag"){
                        if($profs["comFormPedag"]=="V"){
                            $cssAdicional ="darkGreen";
                        }
                        $valorTabel="";
                    }else if($cab["tituloDb"]=="semFormPedag"){
                        if($profs["comFormPedag"]!="V"){
                            $cssAdicional ="darkRed";
                        }
                        $valorTabel="";
                    }else if($cab["tituloDb"]=="disciplinaLecciona"){
                        $disc="";
                        //abreviacaoDisciplina1
                        foreach (distinct2($divisaoProfessores, "abreviacaoDisciplina1") as $disciplina) {
                            if($disc!=""){
                                $disc .=", ";
                            }
                            $disc .=$disciplina;                            
                        }
                        $cssAdicional=$this->maiuscula;
                        $valorTabel = $disc;
                    }else if($cab["tituloDb"]=="turmaLecciona"){
                        $tur="";
                        $cursosProfessor=array(); 
                        foreach ($this->selectDistinct("divisaoprofessores", "idDivCurso", ["idDivEntidade"=>$profs["idPEntidade"], "idDivEscola"=>$_SESSION["idEscolaLogada"], "idDivAno"=>$this->idPAno]) as $curso) {
                            $cursosProfessor[] = $curso;                            
                        }
                        $classsesProfessor=array();
                        foreach ($this->selectDistinct("divisaoprofessores", "classe", ["idDivEntidade"=>$profs["idPEntidade"], "idDivEscola"=>$_SESSION["idEscolaLogada"], "idDivAno"=>$this->idPAno]) as $classes) {
                            $classsesProfessor[] = $classes;
                        }

                        foreach (distinct2($divisaoProfessores, "classe") as $classe) {
                            if($classe>=10){
                                foreach(distinct2($divisaoProfessores, "idPNomeCurso") as $curso){
                                    foreach(distinct2(condicionadorArray($divisaoProfessores, ["classe=".$classe, "idPNomeCurso=".$curso]), "designacaoTurmaDiv") as $turma){

                                        if($tur!=""){
                                            $tur .=", ";
                                        }
                                        $tur .= $this->selectUmElemento("nomecursos", "abrevCurso", ["idPNomeCurso"=>$curso]).$classe.$turma;
                                        
                                    }
                                }
                            }else{
                                foreach(distinct2(condicionadorArray($divisaoProfessores, ["classe=".$classe]), "designacaoTurmaDiv") as $turma){

                                    if($tur!=""){
                                        $tur .=", ";
                                    }
                                    $tur .= $this->selectUmElemento("nomecursos", "abrevCurso", ["idPNomeCurso"=>$curso]).$classe.$turma;
                                }
                            }
                        }
                        $valorTabel = $tur;
                    }else if($cab["tituloDb"]=="cargoProfessor"){

                        $valorTabel=count($this->selectArray("horario", ["idPEscola"], ["idPEscola"=>$_SESSION['idEscolaLogada'], "idHorAno"=>$this->idAnoActual, "idPEntidade"=>$profs["idPEntidade"]]));
                    }



                   $this->html .="<td style='".$this->border().$cab["classCSS"].$cssAdicional."'>".$valorTabel."</td>";
               }
               $this->html .="</tr>";
            }

            $this->html .="</table><br/>
            <div style='width:600px;'>
            <table style='".$this->tabela."width:100%;'>
            <tr style='".$this->corDanger."'><td style='".$this->border().$this->bolder.$this->text_center."' colspan='4'>DOCENTES</td></tr>
            <tr style='".$this->corDanger."'><td style='".$this->border().$this->bolder.$this->text_center."'></td><td style='".$this->border().$this->text_center."'>MASCULINO</td><td style='".$this->border().$this->text_center."'>FEMININO</td><td style='".$this->border().$this->text_center."'>TOTAL</td></tr>
            <tr><td class='' style='width:180px;".$this->border()."'>DOUTORES</td><td style='".$this->border().$this->text_center."'>".completarNumero($this->numeroDocentePorNivel("Doutor", "M"))."</td><td style='".$this->border().$this->text_center."'>".completarNumero($this->numeroDocentePorNivel("Doutor", "F"))."</td><td style='".$this->border().$this->text_center."'>".completarNumero($this->numeroDocentePorNivel("Doutor", "TOT"))."</td></tr>

            <tr><td class='' style='".$this->border()."'>MESTRES</td><td style='".$this->border().$this->text_center."'>".completarNumero($this->numeroDocentePorNivel("Mestre", "M"))."</td><td style='".$this->border().$this->text_center."'>".completarNumero($this->numeroDocentePorNivel("Mestre", "F"))."</td><td style='".$this->border().$this->text_center."'>".completarNumero($this->numeroDocentePorNivel("Mestre", "TOT"))."</td></tr>

            <tr><td style='".$this->border()."'>LICENCIADOS</td><td style='".$this->border().$this->text_center."'>".completarNumero($this->numeroDocentePorNivel("Licenciado", "M"))."</td><td style='".$this->border().$this->text_center."'>".completarNumero($this->numeroDocentePorNivel("Licenciado", "F"))."</td><td style='".$this->border().$this->text_center."'>".completarNumero($this->numeroDocentePorNivel("Licenciado", "TOT"))."</td></tr>
            <tr><td style='".$this->border()."'>BACHAREIS</td><td style='".$this->border().$this->text_center."'>".completarNumero($this->numeroDocentePorNivel("Bacharel", "M"))."</td><td style='".$this->border().$this->text_center."'>".completarNumero($this->numeroDocentePorNivel("Bacharel", "F"))."</td><td style='".$this->border().$this->text_center."'>".completarNumero($this->numeroDocentePorNivel("Bacharel", "TOT"))."</td></tr>
            <tr><td style='".$this->border()."'>TÉCNICOS MÉDIDOS</td><td style='".$this->border().$this->text_center."'>".completarNumero($this->numeroDocentePorNivel("Médio", "M"))."</td><td style='".$this->border().$this->text_center."'>".completarNumero($this->numeroDocentePorNivel("Médio", "F"))."</td><td style='".$this->border().$this->text_center."'>".completarNumero($this->numeroDocentePorNivel("Médio", ""))."</td></tr>

            <tr><td style='".$this->border().$this->text_center."'>TOTAL</td><td style='".$this->border().$this->text_center."'>".completarNumero($this->numeroDocentePorNivel("TOT", "M"))."</td><td style='".$this->border().$this->text_center."'>".completarNumero($this->numeroDocentePorNivel("TOT", "F"))."</td><td style='".$this->border().$this->text_center."'>".completarNumero($this->numeroDocentePorNivel("TOT", "TOT"))."</td></tr>
            </table>
            </div>

            <div style='width:600px; margin-top:-216px; margin-left:900px;'>
                <table style='".$this->tabela."'>
                    <tr style='".$this->corDanger."'><td style='".$this->border().$this->text_center.$this->bolder."'></td><td style='".$this->border().$this->text_center.$this->bolder."'>MASCULINOS</td><td style='".$this->border().$this->text_center.$this->bolder."'>FEMININOS</td></tr>

                    <tr><td style='".$this->border()."'>COM FORMAÇÃO PEDAGÓGICA</td><td style='".$this->border().$this->text_center."'>".completarNumero($this->numeroComFormPedag("V", "M"))."</td><td style='".$this->border().$this->text_center."'>".completarNumero($this->numeroComFormPedag("V", "F"))."</td></tr>

                    <tr><td style='".$this->border()."'>SEM FORMAÇÃO PEDAGÓGICA</td><td style='".$this->border().$this->text_center."'>".completarNumero($this->numeroComFormPedag("F", "M"))."</td><td style='".$this->border().$this->text_center."'>".completarNumero($this->numeroComFormPedag("F", "F"))."</td></tr>

                    <tr><td style='".$this->border()."'>TOTAL</td><td style='".$this->border().$this->text_center."'>".completarNumero($this->numeroComFormPedag("TOT", "M"))."</td><td style='".$this->border().$this->text_center."'>".completarNumero($this->numeroComFormPedag("TOT", "F"))."</td></tr>

                </table>
            </div>";




            $this->html .="<div style='margin-top:100px;'><p  style='font-size:16pt;".$this->maiuscula.$this->text_center."'>".$this->rodape()."</p><br/><br/><div style='".$this->maiuscula."'>".$this->assinaturaDirigentes("mengi")."</div><div>";
            

            $this->exibir("", "Mapa de Levantamento da Força de Trabalho-".$this->numAno, "", $this->tamanhoFolha, "landscape");
        }

        private function numeroDocentePorNivel($nivel, $sexo){
            $contador=0;
            foreach ($this->lista as $prof) {
              if(seComparador($nivel, $prof["nivelAcademicoEntidade"]) && seComparador($sexo, $prof["generoEntidade"])){
                $contador++;
              }
            }
            return $contador;
        }
        private function numeroComFormPedag($comForm, $sexo){
          $contador=0;
          foreach ($this->lista as $prof) {
            if(seComparador($comForm, $prof["comFormPedag"]) && seComparador($sexo, $prof["generoEntidade"])){
              $contador++; 
            }
          }
          return $contador;
        }
    }

new mapaForcaTrabalho(__DIR__);
    
    
  
?>