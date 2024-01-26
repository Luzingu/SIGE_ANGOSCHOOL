<?php 
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    if(!isset($_SESSION["directorioPaterno"])){
      $protocolo = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS']=="on") ? "https" : "http");
      echo "<script>window.location='".$protocolo."://".$_SERVER["HTTP_HOST"]."'</script>";
    }
    include_once $_SESSION["directorioPaterno"].'angoschool/areaDireccaoProvincial/funcoesAuxiliares.php';
    include_once $_SESSION["directorioPaterno"].'angoschool/areaDireccaoProvincial/funcoesAuxiliaresDb.php';

    class mapaForcaTrabalho extends funcoesAuxiliares{

        function __construct($caminhoAbsoluto){
            $this->caminhoRetornar = retornarCaminhoRecuarArquivosPhp(__DIR__);

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
            <div><div style='margin-top:20px; margin-left:50px; width:800px; position:absolute;' style='".$this->maiuscula."'>".$this->assinaturaDirigentes("Director")."</div></div>".$this->cabecalho();
              
            $this->html .="<p style='".$this->text_center.$this->miniParagrafo.$this->maiuscula."'>Levantamento Estatístico</p><br/>
            <p style='".$this->text_center.$this->maiuscula.$this->bolder.$this->sublinhado."'>Mapa de Pessoal Docente</p>
            </div>";

            if($this->verificacaoAcesso->verificarAcessoAlteracao(["aDirectoria", "aAdministrativa", "aPedagogica"], "", "", "")){                   
                $this->mapa();
            }else{
              $this->negarAcesso();
            }
        }

         private function mapa(){
            $this->lista=array();
            foreach($this->selectArray("escolas LEFT JOIN div_terit_comunas ON idPComuna=comuna LEFT JOIN div_terit_municipios ON idPMunicipio=municipio LEFT JOIN div_terit_provincias ON idPProvincia=provincia", "DISTINCT idPEscola", "tipoInstituicao=:tipoInstituicao AND provincia=:provincia AND idPEscola!=4 AND privacidadeEscola=:privacidadeEscola AND estadoEscola=:estadoEscola", ["escola", valorArray($this->sobreUsuarioLogado, "provincia"), "Pública", "A"], "nomeEscola ASC") as $a){
                $this->lista = array_merge($this->lista, $this->selectArray("entidadesprimaria LEFT JOIN dadosadicionasentidade ON idDAEntidade=idPEntidade LEFT JOIN entidade_escola ON idFEntidade=idPEntidade LEFT JOIN div_terit_paises ON idPPais=paisNascEntidade LEFT JOIN div_terit_municipios ON idPMunicipio=municNascEntidade LEFT JOIN div_terit_provincias ON idPProvincia=provNascEntidade LEFT JOIN div_terit_comunas ON idPComuna=comunaNascEntidade LEFT JOIN escolas ON idPEscola=idEntidadeEscola", "*", "estadoActividadeEntidade=:estadoActividadeEntidade AND idEntidadeEscola=:idEntidadeEscola AND efectividade=:efectividade AND tipoPessoal=:tipoPessoal", ["A", $a->idPEscola, "V", "docente"], "nomeEntidade ASC"));

            }

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
           $cabecalho[] = array('titulo' =>"Função", 'tituloDb'=>"funcaoEnt", "classCSS"=>"");

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

            $cabecalho[] = array('titulo' =>"Data de Inicio de<br/>Serviço na Escola", 'tituloDb'=>"dataInicioFuncoesEntidade", "classCSS"=>"text-align:center;");

            $cabecalho[] = array('titulo' =>"Nº de Tempo<br/>de Serviço na<br/>EScola", 'tituloDb'=>"numeroTermpoServico", "classCSS"=>"text-align:center;");

            $cabecalho[] = array('titulo' =>"Data de<br/>Inicio de<br/>Serviço noutra Escola", 'tituloDb'=>"dataInicOutraEsc", "campoMae"=>"Número de Anos de<br/>Serviço Comprovada", "id"=>"5", "totalColunas"=>2, "classCSS"=>"text-align:center;");

           
            $cabecalho[] = array('titulo' =>"No Sistema<br/>Educativo", 'tituloDb'=>"dataInicEduc", "campoMae"=>"Número de Anos de<br/>Serviço Comprovada", "id"=>"5", "totalColunas"=>3, "classCSS"=>"text-align:center;");

            $cabecalho[] = array('titulo' =>"Natureza do<br/>Vínculo", 'tituloDb'=>"naturezaVinc", "classCSS"=>"text-align:center;");
            $cabecalho[] = array('titulo' =>"Contacto", 'tituloDb'=>"numeroTelefoneEntidade", "classCSS"=>"text-align:center;");

            $cabecalho[] = array('titulo' =>"Professor", 'tituloDb'=>"cargoProfessor", "campoMae"=>"Carga Horária<br/> do Docente", "id"=>"6", "totalColunas"=>2, "classCSS"=>"text-align:center;");
            $cabecalho[] = array('titulo' =>"Pedagógico", 'tituloDb'=>"cargoPedagogicoEnt", "campoMae"=>"Carga Horária<br/> do Docente", "id"=>"6", "totalColunas"=>2, "classCSS"=>"text-align:center;");


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
                $i++;
                if($i%2==0){
                   $this->html .="<tr style='".$this->backGround("rgb(220,220,220)")."'>";
                }else{
                    $this->html .="<tr>";
                }

                $this->html .="<td style='".$this->text_center.$this->border()."'>".completarNumero($i)."</t>";
               foreach ($cabecalho as $cab) {
                   $campo = $cab["tituloDb"];
                   $valorTabel = isset($profs->$campo)?$profs->$campo:"";
                   $cssAdicional="";
                    if($cab["tituloDb"]=="dataNascEntidade" || $cab["tituloDb"]=="dataEBIEntidade" || $cab["tituloDb"]=="dataCaducBI" || $cab["tituloDb"]=="dataDespacho" || $cab["tituloDb"]=="dataInicioFuncoesEntidade" || $cab["tituloDb"]=="dataInicOutraEsc" || $cab["tituloDb"]=="dataInicEduc"){
                        $valorTabel = converterData($valorTabel);

                    }else if($cab["tituloDb"]=="idadeEntidade"){
                        $valorTabel = calcularIdade(explode("-", $this->dataSistema)[0], $profs->dataNascEntidade)." Anos";
                    }else if($cab["tituloDb"]=="tempoServOutraEsc"){
                        $valorTabel .=" Anos";
                    }else if($cab["tituloDb"]=="cursoP" || $cab["tituloDb"]=="localP"){

                        if($profs->nivelAcademicoEntidade=="Médio"){
                           $areaFormacao = $profs->cursoEnsinoMedio;
                           $escola = $profs->escolaEnsinoMedio;
                        }else if($profs->nivelAcademicoEntidade=="Licenciado" || $profs->nivelAcademicoEntidade=="Bacharel"){
                            $areaFormacao = $profs->cursoLicenciatura;
                            $escola = $profs->escolaLicenciatura;
                        }else if($profs->nivelAcademicoEntidade=="Mestre"){
                            $areaFormacao = $profs->cursoMestrado;
                            $escola = $profs->escolaMestrado;
                        }else if($profs->nivelAcademicoEntidade=="Doutor"){
                           $areaFormacao = $profs->cursoDoutoramento;
                           $escola = $profs->escolaDoutoramento;
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
                        if($profs->comFormPedag=="V"){
                            $cssAdicional ="darkGreen";
                        }
                        $valorTabel="";
                    }else if($cab["tituloDb"]=="semFormPedag"){
                        if($profs->comFormPedag!="V"){
                            $cssAdicional ="darkRed";
                        }
                        $valorTabel="";
                    }else if($cab["tituloDb"]=="disciplinaLecciona"){
                        $disc="";
                        foreach ($this->selectArray("divisaoprofessores LEFT JOIN nomedisciplinas ON idPNomeDisciplina=idDivDisciplina", "DISTINCT abreviacaoDisciplina1", "idDivEntidade=:idDivEntidade AND idDivEscola=:idDivEscola AND idDivAno=:idDivAno", [$profs->idPEntidade, $profs->idPEscola, $this->idPAno], "nomeDisciplina ASC") as $disciplina) {

                            if($disc==""){
                                $disc .=$disciplina->abreviacaoDisciplina1;
                            }else{
                                $disc .=", ".$disciplina->abreviacaoDisciplina1;
                            }                            
                        }
                        $cssAdicional=$this->maiuscula;
                        $valorTabel = $disc;
                    }else if($cab["tituloDb"]=="turmaLecciona"){

                        $cursosProfessor=array(); 
                        foreach ($this->selectArray("divisaoprofessores", "DISTINCT idDivCurso", "idDivEntidade=:idDivEntidade AND idDivEscola=:idDivEscola AND idDivAno=:idDivAno", [$profs->idPEntidade, $profs->idPEscola, $this->idPAno], "idDivCurso ASC") as $curso) {
                            $cursosProfessor[] = $curso->idDivCurso;                            
                        }
                        $classsesProfessor=array();
                        foreach ($this->selectArray("divisaoprofessores", "DISTINCT classe", "idDivEntidade=:idDivEntidade AND idDivEscola=:idDivEscola AND idDivAno=:idDivAno", [$profs->idPEntidade, $profs->idPEscola, $this->idPAno], "classe ASC") as $classes) {
                            $classsesProfessor[] = $classes->classe;
                        }
                        $tur="";
                        foreach ($cursosProfessor as $curso) {
                            foreach ($classsesProfessor as $classe) {
                               foreach ($this->selectArray("divisaoprofessores", "DISTINCT designacaoTurmaDiv", "idDivEntidade=:idDivEntidade AND idDivEscola=:idDivEscola AND idDivAno=:idDivAno AND classe=:classe AND idDivCurso=:idDivCurso", [$profs->idPEntidade, $profs->idPEscola, $this->idPAno, $classe, $curso], "designacaoTurmaDiv ASC") as $turma) {

                                    $jp = $this->selectUmElemento("nomecursos", "abrevCurso", "idPNomeCurso=:idPNomeCurso", [$curso]).$classe.$turma->designacaoTurmaDiv;

                                    if($tur==""){
                                        $tur .=$jp;
                                    }else{
                                        $tur .=", ".$jp;
                                    }
                                }
                            }
                        }
                        $valorTabel = $tur;
                    }else if($cab["tituloDb"]=="cargoProfessor"){
                
                    $valorTabel = count($this->selectArray("horario LEFT JOIN divisaoprofessores ON idDivDisciplina=idHorDisc LEFT JOIN nomedisciplinas ON idDivDisciplina=idPNomeDisciplina LEFT JOIN nomecursos ON idHorCurso=idPNomeCurso LEFT JOIN listaturmas ON   idListaEscola=idDivEscola", "*", "idHorEscola=:idHorEscola AND idHorAno=:idHorAno AND idDivEntidade=:idDivEntidade AND idDivEscola=idHorEscola AND divisaoprofessores.classe=horario.classe AND nomeTurmaDiv=turma AND idDivAno=idHorAno AND (idHorCurso=idDivCurso OR idDivCurso IS NULL) AND idListaAno=idDivAno AND listaturmas.classe=horario.classe AND nomeTurma=nomeTurmaDiv AND (idListaCurso=idDivCurso OR idListaCurso IS NULL)", [$profs->idPEscola, $this->idPAno, $profs->idPEntidade]));
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
            <tr><td class='' style='width:180px;".$this->border()."'>DOUTORES</td><td style='".$this->border().$this->text_center."'>".completarNumero($this->numeroDocentePorNivel("Doutor", "M"))."</td><td style='".$this->border().$this->text_center."'>".completarNumero($this->numeroDocentePorNivel("Doutor", "F"))."</td><td style='".$this->border().$this->text_center."'>".completarNumero($this->numeroDocentePorNivel("Doutor", ""))."</td></tr>

            <tr><td class='' style='".$this->border()."'>MESTRES</td><td style='".$this->border().$this->text_center."'>".completarNumero($this->numeroDocentePorNivel("Mestre", "M"))."</td><td style='".$this->border().$this->text_center."'>".completarNumero($this->numeroDocentePorNivel("Mestre", "F"))."</td><td style='".$this->border().$this->text_center."'>".completarNumero($this->numeroDocentePorNivel("Mestre", ""))."</td></tr>

            <tr><td style='".$this->border()."'>LICENCIADOS</td><td style='".$this->border().$this->text_center."'>".completarNumero($this->numeroDocentePorNivel("Licenciado", "M"))."</td><td style='".$this->border().$this->text_center."'>".completarNumero($this->numeroDocentePorNivel("Licenciado", "F"))."</td><td style='".$this->border().$this->text_center."'>".completarNumero($this->numeroDocentePorNivel("Licenciado", ""))."</td></tr>
            <tr><td style='".$this->border()."'>BACHAREIS</td><td style='".$this->border().$this->text_center."'>".completarNumero($this->numeroDocentePorNivel("Bacharel", "M"))."</td><td style='".$this->border().$this->text_center."'>".completarNumero($this->numeroDocentePorNivel("Bacharel", "F"))."</td><td style='".$this->border().$this->text_center."'>".completarNumero($this->numeroDocentePorNivel("Bacharel", ""))."</td></tr>
            <tr><td style='".$this->border()."'>TÉCNICOS MÉDIDOS</td><td style='".$this->border().$this->text_center."'>".completarNumero($this->numeroDocentePorNivel("Médio", "M"))."</td><td style='".$this->border().$this->text_center."'>".completarNumero($this->numeroDocentePorNivel("Médio", "F"))."</td><td style='".$this->border().$this->text_center."'>".completarNumero($this->numeroDocentePorNivel("Médio", ""))."</td></tr>
            <tr><td style='".$this->border().$this->text_center."'>TOTAL</td><td style='".$this->border().$this->text_center."'>".completarNumero($this->numeroDocentePorNivel("", "M"))."</td><td style='".$this->border().$this->text_center."'>".completarNumero($this->numeroDocentePorNivel("", "F"))."</td><td style='".$this->border().$this->text_center."'>".completarNumero($this->numeroDocentePorNivel("", ""))."</td></tr>
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

            $this->html .="<div style='margin-top:100px;'><p  style='font-size:16pt;".$this->maiuscula.$this->text_center."'>".$this->rodape()."</p><br/><br/><div style='".$this->maiuscula."'>".$this->assinaturaDirigentes(["CDARH"])."</div><div>";
            

            $this->exibir("", "Mapa de Levantamento da Força de Trabalho-".$this->numAno, "", $this->tamanhoFolha, "landscape");
        }

        private function numeroDocentePorNivel($nivel, $sexo){
            $contador=0;
            foreach ($this->lista as $prof) {
              if(seComparador($nivel, $prof->nivelAcademicoEntidade) && seComparador($sexo, $prof->generoEntidade)){
                $contador++;
              }
            }
            return $contador;
        }
        private function numeroComFormPedag($comForm, $sexo){
          $contador=0;
          foreach ($this->lista as $prof) {
            if(seComparador($comForm, $prof->comFormPedag) && seComparador($sexo, $prof->generoEntidade)){
              $contador++; 
            }
          }
          return $contador;
        }
    }

new mapaForcaTrabalho(__DIR__);
    
    
  
?>