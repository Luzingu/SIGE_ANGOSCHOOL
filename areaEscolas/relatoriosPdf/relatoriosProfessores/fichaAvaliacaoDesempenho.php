<?php if(session_status()!==PHP_SESSION_ACTIVE){
      session_cache_expire(60);
      session_start();
    }
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');

    class mapaForcaTrabalho extends funcoesAuxiliares{

        function __construct($caminhoAbsoluto){
            
            parent::__construct("Rel-Ficha de Avaliação de Desempenho");
            $this->numAno=0;
            $this->trimestre = isset($_GET["trimestre"])?$_GET["trimestre"]:null;
            $this->idPAno = isset($_GET["idPAno"])?$_GET["idPAno"]:null;
             $this->idPEntidade = isset($_GET["idPEntidade"])?$_GET["idPEntidade"]:null;
            $this->numAno();
            if(!($this->trimestre=="I" || $this->trimestre=="II" || $this->trimestre=="III" || $this->trimestre=="IV")){
                $this->trimestre=="I";
            }
            if($this->verificacaoAcesso->verificarAcesso("", "relatorioFuncionario", array(), "")){

                if($this->idPAno==1 || $this->idPAno==842){
                    $this->entidade = $this->selectArray("entidadesprimaria", [], ["idPEntidade"=>$this->idPEntidade, "escola.idEntidadeEscola"=>$_SESSION['idEscolaLogada'], "aval_desemp.idAvalProfAno"=>$this->idPAno, "aval_desemp.idAvalProfEscola"=>$_SESSION['idEscolaLogada']], ["escola", "aval_desemp"]);

                    if(valorArray($this->entidade, "tipoPessoal", "escola")=="docente"){
                        $this->docente2020();
                    }else{
                        $this->naoDocente2020();
                    }
                }else{
                    $this->entidade = $this->selectArray("entidadesprimaria", [], ["idPEntidade"=>$this->idPEntidade, "escola.idEntidadeEscola"=>$_SESSION['idEscolaLogada'], "aval_desemp.idAvalEntAno"=>$this->idPAno, "aval_desemp.idAvalEntEscola"=>$_SESSION['idEscolaLogada']], ["escola", "aval_desemp"]);

                    if(valorArray($this->entidade, "tipoPessoal", "escola")=="docente"){
                        if($this->trimestre=="IV"){
                            $this->docente2022IV();
                        }else{
                            $this->docente2022I();
                        }   
                    }else{
                        $this->naodocente2022();
                    }
                    
                }                                 
            }else{
              $this->negarAcesso();
            }
        }
        private function docente2022I(){

            $comissAval =$this->selectArray("comissAvalDesempProfessor", [], ["idEscola"=>$_SESSION['idEscolaLogada'], "idAno"=>$this->idPAno, "trimestre"=>$this->trimestre]);

            $coordenador =$this->selectArray("entidadesprimaria", ["nomeEntidade", "escola.funcaoEnt"], ["idPEntidade"=>valorArray($comissAval, "coordenador")], ["escola"]);

            $arrayAvaliacoes[] = array("designacao"=>"Qualidade do Processo de Ensino e Aprendizagem", "explicacao"=>"Avalia os conhecimentos teóricos e práticos relacionados com a preparação da acção didáctica", "id"=>"qualProcEnsAprend", "coeficiente"=>"0,2");

            $arrayAvaliacoes[] = array("designacao"=>"Progresso do Aluno ou Desenvolvimento do Aluno", "explicacao"=>"O trabalho do professor resulta no progresso aceitável e mensurável do aluno em termos de capacidade de leitura, escrita, cálculo e reflexão", "id"=>"PA", "coeficiente"=>"0,3");

            $arrayAvaliacoes[] = array("designacao"=>"Responsabilidade", "explicacao"=>"Avalia a capacidade de prever, julgar e assumir as consequências dos actos e o dever patriótico", "id"=>"resposabilidade", "coeficiente"=>"0,1");

            $arrayAvaliacoes[] = array("designacao"=>"Aperfeiçoamento Profissional e Inovação Pedagógica", "explicacao"=>"Avalia o interesse demonstrado em melhorar os conhecimentos profissionais, em corrigir defeitos e pontos fracos e a facilidade de procurar soluções para os problemas independentemente da intervenção do superior hierárquico e sem perda de generalidade, ajusta as novas tarefas com a realidade", "id"=>"aperfProfissional", "coeficiente"=>"0,2");

            $arrayAvaliacoes[] = array("designacao"=>"Relações humanas", "explicacao"=>"Avalia a facilidade de estabelecer e manter boas relações com as pessoas com que e para quem trabalha, o interesse em criar bom ambiente de trabalho e facilidade de integração e cooperação em trabalho de grupo.", "id"=>"relHum", "coeficiente"=>"0,2");

            $descricao[] = array("id"=>"qualProcEnsAprend", "titulo"=>"1-Não planifica os conteúdos<br>2-Não se empenha no cumprimento da matéria programada.<br>3-Misturou apenas 25% das aulas programadas.", "pontuacao"=>5);
            $descricao[] = array("id"=>"qualProcEnsAprend", "titulo"=>"1-Planifica algumas vezes.<br>2-Empenha-se pouco no cumprimento da matéria programada<br>3-Misturou 50% das aulas programadas", "pontuacao"=>10);
            $descricao[] = array("id"=>"qualProcEnsAprend", "titulo"=>"1-Planifica a matéria com regularidade.<br>2-Empenha-se no cumprimento da matéria programada<br>3-Misturou até 75% da matéria programada", "pontuacao"=>15);
            $descricao[] = array("id"=>"qualProcEnsAprend", "titulo"=>"1-Planifica sempre os conteúdos.<br>2-Empenha-se bastante no cumprimento da matéria programada<br>3-Misturou até 100% da matéria programada.", "pontuacao"=>20);

            $descricao[] = array("id"=>"PA", "titulo"=>"1-O trabalho do professor não resulta no crescimento aceitável do aluno<br>2-o nível de aprendizagem dos alunos é muito fraco.", "pontuacao"=>5);
            $descricao[] = array("id"=>"PA", "titulo"=>"1-O trabalho do professor resulta no crescimento lento dos alunos.<br>2-O nível de aprendizagem dos alunos é regular.", "pontuacao"=>10);
            $descricao[] = array("id"=>"PA", "titulo"=>"1-O trabalho do professor resulta no progresso aceitável e mensurável do desenvolvimento do aluno.<br>2-O nível de aprendizagem dos alunos é bom", "pontuacao"=>15);
            $descricao[] = array("id"=>"PA", "titulo"=>"1-O trabalho do professor resulta em alto nível de realizações com todos os alunos.<br>2-O nível de aprendizagem dos alunos é muito bom.", "pontuacao"=>20);

            $descricao[] = array("id"=>"resposabilidade", "titulo"=>"1-É normalmente pouco cumpridor das normas disciplinares, faltando-lhe capacidade de prever, julgar e assumir as consequências dos actos e com pouco sentido patriótico;<br>2-Atrasa com frequência e comete muitas faltas.<br>3-Dificilmente entrega o expediente.", "pontuacao"=>5);

            $descricao[] = array("id"=>"resposabilidade", "titulo"=>"1-É normalmente pouco disciplinado e responsável, inspirando algum cuidado no tocante à capacidade de prever, julgar e assumir as consequências dos actos.<br>2-Atrasa algumas vezes e comete algumas faltas.<br>3-Entrega o expediente quando pressionado.", "pontuacao"=>10);

            $descricao[] = array("id"=>"resposabilidade", "titulo"=>"1-É disciplinado e assume as responsabilidades inerentes ao trabalho.<br>2-Dificilmente atrasa e comete poucas faltas.<br>3-Entrega o expediente dentro do prazo.", "pontuacao"=>15);
            $descricao[] = array("id"=>"resposabilidade", "titulo"=>"1-É muito disciplinado, assume plenamente as suas responsabilidades e manifesta um elevado sentido patriótico.<br>2-Não atrasa e não comete faltas.<br>3-Entrega o expediente antes do prazo.", "pontuacao"=>20);

            $descricao[] = array("id"=>"aperfProfissional", "titulo"=>"Mostra pouco interesse em adquirir novos conhecimentos. Não participa em acções de formação e revela resistência à mudança. Não consegue ultrapassar a rotina. Não se esforça em procurar soluções ou desenvolver novos métodos de trabalho, sem intervenção do superior hierárquico.", "pontuacao"=>5);
            $descricao[] = array("id"=>"aperfProfissional", "titulo"=>"Mostra algum interesse em aumentar os seus conhecimentos e aperfeiçoar o seu trabalho, hesite perante situações menos frequentes. Esforça-se na busca de soluções ou novos métodos de trabalho, embora os resultados nem sempre sejam adequados ou oportunos.", "pontuacao"=>10);
            $descricao[] = array("id"=>"aperfProfissional", "titulo"=>"Revela interesse em aumentar os seus conhecimentos e em aperfeiçoar o seu trabalho. Participa em algumas acções de formação. Adapta-se às novas exigências e a situações pouco frequentes. Esforça-se por resolver problemas e criar novos métodos de trabalho, apresentando sugestões normalmente adequadas e oportunas.", "pontuacao"=>15);
            $descricao[] = array("id"=>"aperfProfissional", "titulo"=>"Revela interesse metódico e sistemático em melhorar os conhecimentos profissionais e a qualidade do trabalho. A sua adaptação à mudança é excelente destacando-se no desempenho e em resolver problemas, desenvolver e criar novos métodos de trabalho. As soluções apresentadas são sempre adequadas e oportunas.", "pontuacao"=>20);

            $descricao[] = array("id"=>"relHum", "titulo"=>"Provoca atritos frequentes e pouco contribui para a existência de um bom ambiente de trabalho.", "pontuacao"=>5);
            $descricao[] = array("id"=>"relHum", "titulo"=>"Pouco contribui para a existência de um bom ambiente de trabalho, tem dificuldade de se relacionar com os colegas, alunos e encarregados de educação, de se integrar quase sempre passivo no trabalho de grupo.", "pontuacao"=>10);
            $descricao[] = array("id"=>"relHum", "titulo"=>"Contribui sempre para um bom ambiente de trabalho, estabelece relações cordiais de trabalho com os colegas, alunos e encarregados de educação e integra-se no grupo com espírito de cooperação, quando expressamente solicitado.", "pontuacao"=>15);
            $descricao[] = array("id"=>"relHum", "titulo"=>"A sua maneira de estar incentiva sempre um bom ambiente de trabalho e integra-se com muita facilidade no grupo, intervindo com eficiência no desenvolvimento harmonioso dos trabalhos.", "pontuacao"=>20);

            $this->html .="<html style='margin-left:60px; margin-right:50px; margin-top:29px; margin-bottom:30px;'>
            <head>
                <title>Ficha de Avaliação de Desempenho</title>
                <style>
                </style>
            </head>
            <body>
            <div class='cabecalho'>";

            $this->html .="<p style='".$this->text_center.$this->miniParagrafo."'></p><p style='".$this->text_center.$this->miniParagrafo."'><img src='".$_SERVER['DOCUMENT_ROOT']."/angoschool/icones/insignia.jpg' style='".$this->insignia_medio."'></p>
            <p style='".$this->text_center.$this->miniParagrafo.$this->bolder."'>REPÚBLICA DE ANGOLA</p>
            <p style='".$this->text_center.$this->bolder."'>MINISTÉRIO DA EDUCAÇÃO</p>
            <p style='".$this->bolder."'>FICHA DE AVALIAÇÃO DE DESEMPENHO TRIMESTRAL DO EDUCADOR DE INFÂNCIA E DO PROFESSOR DO ENSINO PRIMÁRIO E SECUNDÁRIO</p>";

            $this->html .="<table style='width:100%".$this->tabela."'>
                <tr><td style='".$this->border().$this->bolder."'>PERÍODO: ".$this->trimestre." TRIMESTRE</td><tr>
                <tr><td style='".$this->border().$this->bolder.$this->maiuscula."'>PROVÍNCIA ".valorArray($this->sobreUsuarioLogado, "preposicaoProvincia2")." ".valorArray($this->sobreUsuarioLogado, "nomeProvincia")."</td><tr>
                <tr><td style='".$this->border().$this->bolder.$this->maiuscula."'>MUNICIPIO ".valorArray($this->sobreUsuarioLogado, "preposicaoMunicipio2")." ".valorArray($this->sobreUsuarioLogado, "nomeMunicipio")."</td><tr>
                <tr><td style='".$this->border().$this->bolder.$this->maiuscula."'>ESCOLA: ".valorArray($this->sobreUsuarioLogado, "nomeEscola")."</td><tr>
                <tr><td style='".$this->border()."'><span style='".$this->bolder."'>Nome Completo:</span> ".valorArray($this->entidade, "nomeEntidade")."</td><tr>
                <tr><td style='".$this->border()."'><span style='".$this->bolder."'>Categoria:</span> ".valorArray($this->entidade, "categoriaEntidade")."</td><tr>
                <tr><td style='".$this->border()."'><span style='".$this->bolder."'>Agente n.º:</span> ".valorArray($this->entidade, "numeroAgenteEntidade")."</td><tr>
                <tr><td style='".$this->border()."'><span style='".$this->bolder."'>Data de Avaliação:</span> ".dataExtensa(valorArray($this->entidade, "dataAvaliacao".$this->trimestre, "aval_desemp"))."</td><tr>
            </table>
            <div style='".$this->border().$this->text_center."margin-top:10px; padding-top:0px;'>
                <p style='".$this->bolder."margin-top:0px;margin-bottom:0px;'>Período a que respeita avaliação</p>
                <p style='margin-top:0px; margin-bottom:0px;'>".dataExtensa(valorArray($comissAval, "dataInicial"))." a ".dataExtensa(valorArray($comissAval, "dataFinal"))."</p>
            </div>
            <p style='".$this->bolder."margin-bottom:0px;'>PONTUAÇÃO DOS INDICADORES DE AVALIAÇÃO</p>
            <table style='".$this->tabela."font-size:11pt'>
                <tr style='".$this->text_center."'>
                    <td style='".$this->border().$this->bolder."width:15%'>Coeficiente<br>de Reparação</td>
                    <td style='".$this->border().$this->bolder."width:70%'>Descrição</td>
                    <td style='".$this->border().$this->bolder."'>Pontuação</td>
                    <td style='".$this->border().$this->bolder."'>Marcar</td>
                </tr>";

            $i=0;
            foreach($arrayAvaliacoes as $a){
                $i++;
                $this->html .="<tr>
                    <td style='".$this->border().$this->bolder."' colspan='4'>".$i.". ".$a["designacao"]."</td>
                </tr>
                <tr>
                    <td style='".$this->border()."' colspan='4'>".$a["explicacao"]."</td>
                </tr>";

                $pontoObtido = valorArray($this->entidade, $a["id"].$this->trimestre, "aval_desemp");
                $contador=0;
                foreach (array_filter($descricao, function ($mamale) use ($a){
                    return $mamale["id"]==$a["id"];
                }) as $desc){
                    
                    $marcador="";
                    if($pontoObtido>($desc["pontuacao"]-5) && $pontoObtido<=$desc["pontuacao"]){
                        $marcador="X";
                    }

                    $contador++;

                    $this->html .="<tr>";
                    if($contador==1){
                        $this->html .="<td style='".$this->border().$this->bolder.$this->text_center."' rowspan='4'>".$a["coeficiente"]."</td>";
                    }
                    $this->html .="
                        <td style='".$this->border()."'>".$desc["titulo"]."</td>
                        <td style='".$this->border().$this->bolder.$this->text_center."'>".$desc["pontuacao"]."</td>
                        <td style='".$this->border().$this->bolder.$this->text_center."'>".$marcador."</td>
                    </tr>";
                }
            }

            $factores[]=array("titulo"=>"Qualidade do processo de ensino aprendizagem", "tituloDb"=>"qualProcEnsAprend".$this->trimestre, "cotacao"=>0.2);
            $factores[]=array("titulo"=>"Progresso do aluno ou desenvolvimento do aluno", "tituloDb"=>"PA".$this->trimestre, "cotacao"=>0.3);
            $factores[]=array("titulo"=>"Responsabilidade", "tituloDb"=>"resposabilidade".$this->trimestre, "cotacao"=>0.1);
            $factores[]=array("titulo"=>"Aperfeiçoamento profissional e Inovação pedagógica", "tituloDb"=>"aperfProfissional".$this->trimestre, "cotacao"=>0.2);
            $factores[]=array("titulo"=>"Relações Humanas", "tituloDb"=>"relHum".$this->trimestre, "cotacao"=>0.2);

            $soma=0;
            foreach($factores as $fact){
                $soma +=floatval(valorArray($this->entidade, $fact["tituloDb"], "aval_desemp"))*$fact["cotacao"];
            }
            $soma = number_format($soma, 0);
            $this->html .="</table><br>
            <table style='".$this->tabela."width:100%;'>
                <tr style='".$this->bolder.$this->text_center."'>
                    <td style='".$this->border()."'>N.º</td><td style='".$this->border()."'>Indicadores</td><td style='".$this->border()."'>Pontos</td>
                </tr>";

                foreach($factores as $fact){
                    $this->html .="
                    <tr>
                        <td style='".$this->border().$this->text_center."'>1</td><td style='".$this->border()."'>".$fact["titulo"]."</td><td style='".$this->border().$this->text_center."'>".(floatval(valorArray($this->entidade, $fact["tituloDb"], "aval_desemp"))*$fact["cotacao"])."</td>
                    </tr>";
                }

                $this->html .="
                <tr>
                    <td style='".$this->border().$this->text_center.$this->bolder."' colspan='3'>Classificação total</td>
                </tr>
                <tr>
                    <td style='".$this->border().$this->text_center."'>1</td><td style='".$this->border()."'>Quantitativa</td><td style='".$this->border().$this->text_center."'>".$soma."</td>
                </tr>
                <tr>
                    <td style='".$this->border().$this->text_center."'>2</td><td style='".$this->border()."'>Qualitativa</td><td style='".$this->border().$this->text_center."'>".$this->classificacao2($soma)."</td>
                </tr>
            </table>
            <p style='".$this->bolder.$this->text_center."'>Apreciação geral<br>(comentários dos avaliadores)</p>

            <p style='".$this->bolder.$this->miniParagrafo."'>Comentário</p>

            <p style='".$this->text_justify."line-height:25px;'>".valorArray($this->entidade, "comentario".$this->trimestre, "aval_desemp").".</p>

            <p style='".$this->bolder.$this->text_center."'>Assinatura</p>

            Nome: <strong>".valorArray($coordenador, "nomeEntidade")."</strong><br/>
                Função: <strong>".valorArray($coordenador, "funcaoEnt", "escola")."</strong><br/>
                Data: ".dataExtensa(valorArray($this->entidade, "dataAvaliacao".$this->trimestre, "aval_desemp"))."<br/>
                <p style='".$this->text_center."'>O Avaliador</p>
                <p style='".$this->text_center."'>_______________________________</p><br/></div>

                <p style='".$this->text_center.$this->bolder."'>Concordância com a avaliação</p>

                <p style='".$this->text_center."'>Concordo <span style='".$this->border()."'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Não concordo <span style='".$this->border()."'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></p>

                 <p style='".$this->text_center."'>O Avaliado</p>
                <p style='".$this->text_center."'>_______________________________</p><br/>
                Data: ____/_____/__________<br/>
                <p style='".$this->text_center.$this->maiuscula."'>O Homologante</p>
                <p style='".$this->text_center."'>_______________________________</p><br/><br/>
            ";
            
            $this->exibir("", "Ficha de Avaliação de Desempenho - ".valorArray($this->entidade, "nomeEntidade")." ".$this->numAno);
        }

        private function docente2022IV(){

            $factores[]=array("titulo"=>"Qualidade do processo de ensino aprendizagem", "tituloDb"=>"qualProcEnsAprend", "cotacao"=>0.2);
            $factores[]=array("titulo"=>"Progresso do aluno ou desenvolvimento do aluno", "tituloDb"=>"PA", "cotacao"=>0.3);
            $factores[]=array("titulo"=>"Responsabilidade", "tituloDb"=>"resposabilidade", "cotacao"=>0.1);
            $factores[]=array("titulo"=>"Aperfeiçoamento profissional e Inovação pedagógica", "tituloDb"=>"aperfProfissional", "cotacao"=>0.2);
            $factores[]=array("titulo"=>"Relações Humanas", "tituloDb"=>"relHum", "cotacao"=>0.2);

            $comissAval =$this->selectArray("comissAvalDesempProfessor", [], ["idEscola"=>$_SESSION['idEscolaLogada'], "idAno"=>$this->idPAno, "trimestre"=>$this->trimestre]);

            $coordenador =$this->selectArray("entidadesprimaria", ["nomeEntidade", "escola.funcaoEnt"], ["idPEntidade"=>valorArray($comissAval, "coordenador")], ["escola"]);

            $this->html .="<html style='margin-left:60px; margin-right:40px; margin-top:25px; margin-bottom:30px;'>
            <head>
                <title>Ficha de Avaliação de Desempenho</title>
                <style>
                </style>
            </head>
            <body>
            <div class='cabecalho'>";

            $this->html .="<p style='".$this->text_center.$this->miniParagrafo."'></p><p style='".$this->text_center.$this->miniParagrafo."'><img src='".$_SERVER['DOCUMENT_ROOT']."/angoschool/icones/insignia.jpg' style='".$this->insignia_medio."'></p>
            <p style='".$this->text_center.$this->miniParagrafo.$this->bolder."'>REPÚBLICA DE ANGOLA</p>
            <p style='".$this->text_center.$this->bolder."'>MINISTÉRIO DA EDUCAÇÃO</p>
            <p style='".$this->bolder."'>FICHA DE AVALIAÇÃO DE DESEMPENHO ANUAL DO EDUCADOR DE INFÂNCIA E DO PROFESSOR DO ENSINO PRIMÁRIO E SECUNDÁRIO</p>";

            $this->html .="<table style='width:100%".$this->tabela."'>
                <tr><td style='".$this->border().$this->bolder.$this->maiuscula."'>PROVÍNCIA ".valorArray($this->sobreUsuarioLogado, "preposicaoProvincia2")." ".valorArray($this->sobreUsuarioLogado, "nomeProvincia")."</td><tr>
                <tr><td style='".$this->border().$this->bolder.$this->maiuscula."'>MUNICIPIO ".valorArray($this->sobreUsuarioLogado, "preposicaoMunicipio2")." ".valorArray($this->sobreUsuarioLogado, "nomeMunicipio")."</td><tr>
                <tr><td style='".$this->border().$this->bolder.$this->maiuscula."'>ESCOLA: ".valorArray($this->sobreUsuarioLogado, "nomeEscola")."</td><tr>
                <tr><td style='".$this->border()."'><span style='".$this->bolder."'>Nome Completo:</span> ".valorArray($this->entidade, "nomeEntidade")."</td><tr>
                <tr><td style='".$this->border()."'><span style='".$this->bolder."'>Categoria:</span> ".valorArray($this->entidade, "categoriaEntidade")."</td><tr>
                <tr><td style='".$this->border()."'><span style='".$this->bolder."'>Agente n.º:</span> ".valorArray($this->entidade, "numeroAgenteEntidade")."</td><tr>
                <tr><td style='".$this->border()."'><span style='".$this->bolder."'>Data de Avaliação:</span> ".dataExtensa(valorArray($this->entidade, "dataAvaliacao".$this->trimestre, "aval_desemp"))."</td><tr>
            </table>
            <div style='".$this->border().$this->text_center."margin-top:10px; padding-top:0px;'>
                <p style='".$this->bolder."margin-top:0px;margin-bottom:0px;'>Período a que respeita avaliação</p>
                <p style='margin-top:0px; margin-bottom:0px;'>".dataExtensa(valorArray($comissAval, "dataInicial"))." a ".dataExtensa(valorArray($comissAval, "dataFinal"))."</p>
            </div><br>
            <table style='".$this->tabela."font-size:10.5pt; width:100%;'>
                <tr style='".$this->bolder.$this->text_center."'>
                    <td style='".$this->border()."'>N.º</td><td style='".$this->border()."'>Indicadores</td><td style='".$this->border()."'>I Trim.</td><td style='".$this->border()."'>II Trim.</td><td style='".$this->border()."'>III Trim.</td><td style='".$this->border()."'>Média</td>
                </tr>";

                $trimestres=array("I", "II", "III", "IV");
                $valores=array();
                $soma=array("I"=>0, "II"=>0, "III"=>0, "IV"=>0);
                foreach($factores as $fact){
                    foreach($trimestres as $trimestre){

                        $valor = number_format(floatval(valorArray($this->entidade, $fact["tituloDb"].$trimestre, "aval_desemp"))*$fact["cotacao"], 1);
                        $valores[$fact["tituloDb"]][$trimestre]= $valor;
                        $soma[$trimestre] +=$valor;
                    }
                }

                $i=0;
                foreach($factores as $fact){
                    $i++;
                   $this->html .="
                    <tr><td style='".$this->border().$this->text_center."'>".completarNumero($i)."</td><td style='".$this->border()."'>".$fact["titulo"]."</td>";

                    foreach($trimestres as $trimestre){
                        $this->html .="<td style='".$this->border().$this->text_center."'>".$valores[$fact["tituloDb"]][$trimestre]."</td>";
                    }
                    $this->html .="</tr>"; 
                }

                $this->html .="
                <tr>
                    <td style='".$this->border()."' colspan='2'>Quantitativa</td>";
                    foreach($trimestres as $trimestre){
                        $this->html .="<td style='".$this->border().$this->text_center."'>".$soma[$trimestre]."</td>";
                    }
                $this->html .="
                </tr>
                <tr>
                    <td style='".$this->border()."' colspan='2'>Qualitativa</td>";
                    foreach($trimestres as $trimestre){
                        $this->html .="<td style='".$this->border().$this->text_center."'>".$this->classificacao2($soma[$trimestre])."</td>";
                    }
                $this->html .="
                </tr>

            </table><br>";

            $this->html .="
            <table style='".$this->tabela."width:100%;'>
                <tr style='".$this->bolder.$this->text_center."'>
                    <td style='".$this->border()."' colspan='3'>Classificação Anual dos Indicadores</td>
                </tr>
                <tr style='".$this->bolder.$this->text_center."'>
                    <td style='".$this->border()."'>N.º</td><td style='".$this->border()."'>Indicadores</td><td style='".$this->border()."'>Pontos</td>
                </tr>";
            $i=0;
            foreach($factores as $fact){
                $i++;
                $this->html .="
                <tr>
                    <td style='".$this->border().$this->text_center."'>".completarNumero($i)."</td><td style='".$this->border()."'>".$fact["titulo"]."</td><td style='".$this->border().$this->text_center."'>".$valores[$fact["tituloDb"]]["IV"]."</td>
                </tr>";
            }

            $this->html .="<tr>
                    <td style='".$this->border().$this->text_center.$this->bolder."' colspan='3'>Classificação Final</td>
                </tr>
                <tr>
                    <td style='".$this->border().$this->text_center."'>1</td><td style='".$this->border()."'>Quantitativa</td><td style='".$this->border().$this->text_center."'>".$soma["IV"]."</td>
                </tr>
                <tr>
                    <td style='".$this->border().$this->text_center."'>2</td><td style='".$this->border()."'>Qualitativa</td><td style='".$this->border().$this->text_center."'>".$this->classificacao2($soma["IV"])."</td>
                </tr>
            </table>
            <p style='".$this->bolder.$this->text_center."'>Apreciação geral<br>(comentários dos avaliadores)</p>

            <p style='".$this->bolder.$this->miniParagrafo."'>Comentário 1</p>

            <p style='".$this->text_justify."line-height:25px;'>".valorArray($this->entidade, "comentarioI", "aval_desemp").".</p>

            <br> <br><p style='".$this->bolder.$this->miniParagrafo."'>Comentário 2</p>
            <p style='".$this->text_justify."line-height:25px;'>".valorArray($this->entidade, "comentarioII", "aval_desemp").".</p>
            <p style='".$this->bolder.$this->miniParagrafo."'>Comentário 3</p>
            <p style='".$this->text_justify."line-height:25px;'>".valorArray($this->entidade, "comentarioIII", "aval_desemp").".</p>
            <p style='".$this->bolder.$this->miniParagrafo."'>Comentário Final</p>
            <p style='".$this->text_justify."line-height:25px;'>".valorArray($this->entidade, "comentarioIV", "aval_desemp").".</p>

            <p style='".$this->bolder.$this->text_center."'>Assinatura</p>

            Nome: <strong>".valorArray($coordenador, "nomeEntidade")."</strong><br/>
                Função: <strong>".valorArray($coordenador, "funcaoEnt", "escola")."</strong><br/>
                Data: ".dataExtensa(valorArray($this->entidade, "dataAvaliacao".$this->trimestre, "aval_desemp"))."<br/>
                <p style='".$this->text_center."'>O Avaliador</p>
                <p style='".$this->text_center."'>_______________________________</p><br/></div>

                <p style='".$this->text_center.$this->bolder."'>Concordância com a avaliação</p>

                <p style='".$this->text_center."'>Concordo <span style='".$this->border()."'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Não concordo <span style='".$this->border()."'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></p>

                 <p style='".$this->text_center."'>O Avaliado</p>
                <p style='".$this->text_center."'>_______________________________</p><br/>
                Data: ____/_____/__________<br/>
                <p style='".$this->text_center.$this->maiuscula."'>O Homologante</p>
                <p style='".$this->text_center."'>_______________________________</p><br/><br/>
            ";
            
            $this->exibir("", "Ficha de Avaliação de Desempenho - ".valorArray($this->entidade, "nomeEntidade")." ".$this->numAno);
        }

        private function naodocente2022(){

            $comissAval =$this->selectArray("comissAvalDesempPessoalNaoDocente", [], ["idEscola"=>$_SESSION['idEscolaLogada'], "idAno"=>$this->idPAno]);

            $coordenador =$this->selectArray("entidadesprimaria", ["nomeEntidade", "escola.funcaoEnt"], ["idPEntidade"=>valorArray($comissAval, "coordenador")], ["escola"]);

            $arrayAvaliacoes[] = array("designacao"=>"Capacidade de análise Profissional", "id"=>"CAP", "coeficiente"=>"0,1");
            $arrayAvaliacoes[] = array("designacao"=>"Interesse", "id"=>"interesse", "coeficiente"=>"0,1");
            $arrayAvaliacoes[] = array("designacao"=>"Conhecimentos ligados ao trabalho", "id"=>"CLT", "coeficiente"=>"0,1");
            $arrayAvaliacoes[] = array("designacao"=>"Organização", "id"=>"organizacao", "coeficiente"=>"0,1");
            $arrayAvaliacoes[] = array("designacao"=>"Sigilo Profissional", "id"=>"SP", "coeficiente"=>"0,1");
            $arrayAvaliacoes[] = array("designacao"=>"Criatividade", "id"=>"criatividade", "coeficiente"=>"0,1");
            $arrayAvaliacoes[] = array("designacao"=>"Relacionamento interpessoal", "id"=>"RIP", "coeficiente"=>"0,1");
            $arrayAvaliacoes[] = array("designacao"=>"Atenção", "id"=>"atencao", "coeficiente"=>"0,1");
            $arrayAvaliacoes[] = array("designacao"=>"Pontualidade e Assiduidade", "id"=>"PA", "coeficiente"=>"0,1");
            $arrayAvaliacoes[] = array("designacao"=>"Disciplina", "id"=>"disciplina", "coeficiente"=>"0,1");

            $descricao[] = array("id"=>"CAP", "titulo"=>"Excelente Capacidade de análise", "pontuacao"=>20);
            $descricao[] = array("id"=>"CAP", "titulo"=>"Boa capacidade de análise", "pontuacao"=>15);
            $descricao[] = array("id"=>"CAP", "titulo"=>"Capacidade de análise razoável", "pontuacao"=>10);
            $descricao[] = array("id"=>"CAP", "titulo"=>"Fraca capacidade de análise", "pontuacao"=>5);

             $descricao[] = array("id"=>"interesse", "titulo"=>"Revela muito interesse metódico e sistemático no trabalho", "pontuacao"=>20);
            $descricao[] = array("id"=>"interesse", "titulo"=>"Revela algum interesse sistemático no trabalho", "pontuacao"=>15);
            $descricao[] = array("id"=>"interesse", "titulo"=>"Revela pouco interesse no trabalho", "pontuacao"=>10);
            $descricao[] = array("id"=>"interesse", "titulo"=>"Desinteresse e fraco envolvimento no trabalho", "pontuacao"=>5);

            $descricao[] = array("id"=>"CLT", "titulo"=>"Excelente conhecimento ao trabalho", "pontuacao"=>20);
            $descricao[] = array("id"=>"CLT", "titulo"=>"Bom conhecimento ao trabalho", "pontuacao"=>15);
            $descricao[] = array("id"=>"CLT", "titulo"=>"Pouco conhecimento ao trabalho", "pontuacao"=>10);
            $descricao[] = array("id"=>"CLT", "titulo"=>"Sem conhecimento ligado ao trabalho", "pontuacao"=>5);

            $descricao[] = array("id"=>"organizacao", "titulo"=>"Muito organizado e elevado sentido de responsabilidade", "pontuacao"=>20);
            $descricao[] = array("id"=>"organizacao", "titulo"=>"Organizado e com muito sentido de responsabilidade ", "pontuacao"=>15);
            $descricao[] = array("id"=>"organizacao", "titulo"=>"Pouco organizado e pouco responsável", "pontuacao"=>10);
            $descricao[] = array("id"=>"organizacao", "titulo"=>"Desorganizado e irresponsável", "pontuacao"=>5);

            $descricao[] = array("id"=>"SP", "titulo"=>"Muito sigilo", "pontuacao"=>20);
            $descricao[] = array("id"=>"SP", "titulo"=>"Sigiloso", "pontuacao"=>15);
            $descricao[] = array("id"=>"SP", "titulo"=>"Pouco sigiloso", "pontuacao"=>10);
            $descricao[] = array("id"=>"SP", "titulo"=>"Sem sigilo", "pontuacao"=>5);

            $descricao[] = array("id"=>"criatividade", "titulo"=>"Altamente Criativo", "pontuacao"=>20);
            $descricao[] = array("id"=>"criatividade", "titulo"=>"Muito Criativo", "pontuacao"=>15);
            $descricao[] = array("id"=>"criatividade", "titulo"=>"Pouco Criativo", "pontuacao"=>10);
            $descricao[] = array("id"=>"criatividade", "titulo"=>"Sem Criatividade", "pontuacao"=>5);


            $descricao[] = array("id"=>"RIP", "titulo"=>"Muito interativo e incentiva excelente ambiente de trabalho ", "pontuacao"=>20);
            $descricao[] = array("id"=>"RIP", "titulo"=>"Interativo e incentiva bom ambiente de trabalho", "pontuacao"=>15);
            $descricao[] = array("id"=>"RIP", "titulo"=>"Pouco interativo e incentiva pouco ambiente de trabalho", "pontuacao"=>10);
            $descricao[] = array("id"=>"RIP", "titulo"=>"Provoca atritos frequentes não incentiva o ambiente de trabalho", "pontuacao"=>5);

            $descricao[] = array("id"=>"atencao", "titulo"=>"Excelente atenção pelo trabalho", "pontuacao"=>20);
            $descricao[] = array("id"=>"atencao", "titulo"=>"Muita atenção pelo trabalho", "pontuacao"=>15);
            $descricao[] = array("id"=>"atencao", "titulo"=>"Pouca atenção pelo trabalho", "pontuacao"=>10);
            $descricao[] = array("id"=>"atencao", "titulo"=>"Sem atenção pelo trabalho", "pontuacao"=>5);


            $descricao[] = array("id"=>"PA", "titulo"=>"Pontual e assíduo", "pontuacao"=>20);
            $descricao[] = array("id"=>"PA", "titulo"=>"Falta e atrasa Poucas Vezes", "pontuacao"=>15);
            $descricao[] = array("id"=>"PA", "titulo"=>"Falta e atrasa frequentemente", "pontuacao"=>10);
            $descricao[] = array("id"=>"PA", "titulo"=>"Falta e atrasa muito", "pontuacao"=>5);

            $descricao[] = array("id"=>"disciplina", "titulo"=>"Exemplar", "pontuacao"=>20);
            $descricao[] = array("id"=>"disciplina", "titulo"=>"Disciplinado(a)", "pontuacao"=>15);
            $descricao[] = array("id"=>"disciplina", "titulo"=>"Ocasionalmente disciplinado(a)", "pontuacao"=>10);
            $descricao[] = array("id"=>"disciplina", "titulo"=>"Indisciplinado(a)", "pontuacao"=>5);


            $this->html .="<html style='margin-left:70px; margin-right:50px; margin-top:29px; margin-bottom:30px;'>
            <head>
                <title>Ficha de Avaliação de Desempenho</title>
                <style>
                </style>
            </head>
            <body>
            <div class='cabecalho'>";

            $this->html .="<p style='".$this->text_center.$this->miniParagrafo."'></p><p style='".$this->text_center.$this->miniParagrafo."'><img src='".$_SERVER['DOCUMENT_ROOT']."/angoschool/icones/insignia.jpg' style='".$this->insignia_medio."'></p>
            <p style='".$this->text_center.$this->miniParagrafo.$this->bolder."'>REPÚBLICA DE ANGOLA</p>
            <p style='".$this->text_center.$this->bolder."'>MINISTÉRIO DA EDUCAÇÃO</p>
            <p style='".$this->bolder."'>FICHA DE AVALIAÇÃO E DESEMPENHO DO TÉCNICO PEDAGÓGICO E ESPECIALISTA DA ADMINISTRAÇÃO DA EDUCAÇÃO</p>";

            $this->html .="<table style='width:100%".$this->tabela."'>
                <tr><td style='".$this->border().$this->bolder.$this->maiuscula."'>PROVÍNCIA ".valorArray($this->sobreUsuarioLogado, "preposicaoProvincia2")." ".valorArray($this->sobreUsuarioLogado, "nomeProvincia")."</td><tr>
                <tr><td style='".$this->border().$this->bolder.$this->maiuscula."'>MUNICIPIO ".valorArray($this->sobreUsuarioLogado, "preposicaoMunicipio2")." ".valorArray($this->sobreUsuarioLogado, "nomeMunicipio")."</td><tr>
                <tr><td style='".$this->border().$this->bolder.$this->maiuscula."'>".valorArray($this->sobreUsuarioLogado, "nomeEscola")."</td><tr>
                <tr><td style='".$this->border()."'><span style='".$this->bolder."'>Nome Completo:</span> ".valorArray($this->entidade, "nomeEntidade")."</td><tr>
                <tr><td style='".$this->border()."'><span style='".$this->bolder."'>Categoria:</span> ".valorArray($this->entidade, "categoriaEntidade")."</td><tr>
                <tr><td style='".$this->border()."'><span style='".$this->bolder."'>Agente n.º:</span> ".valorArray($this->entidade, "numeroAgenteEntidade")."</td><tr>
                <tr><td style='".$this->border()."'><span style='".$this->bolder."'>Data de Avaliação:</span> ".dataExtensa(valorArray($this->entidade, "dataAvaliacao", "aval_desemp"))."</td><tr>
            </table>
            <div style='".$this->border().$this->text_center."margin-top:10px; padding-top:0px;'>
                <p style='".$this->bolder."margin-top:0px;margin-bottom:0px;'>Período a que respeita avaliação</p>
                <p style='margin-top:0px; margin-bottom:0px;'>".dataExtensa(valorArray($comissAval, "dataInicial"))." a ".dataExtensa(valorArray($comissAval, "dataFinal"))."</p>
            </div>

            <!--
            <p style='".$this->bolder."margin-bottom:0px;'>PONTUAÇÃO DOS INDICADORES DE AVALIAÇÃO</p>
            <table style='".$this->tabela."font-size:11.5pt; width:100%'>
            <tr style='".$this->text_center."'>
                <td style='".$this->border().$this->bolder."width:15%'>Coeficiente<br>de Reparação</td>
                <td style='".$this->border().$this->bolder."width:60%'>Descrição</td>
                <td style='".$this->border().$this->bolder."'>Pontuação</td>
                <td style='".$this->border().$this->bolder."'>Marcar</td>
            </tr>";

            $i=0;
            foreach($arrayAvaliacoes as $a){
                $i++;
                $this->html .="<tr>
                    <td style='".$this->border().$this->bolder."' colspan='4'>".$i.". ".$a["designacao"]."</td>
                </tr>";

                $pontoObtido = valorArray($this->entidade, $a["id"], "aval_desemp");
                $contador=0;
                foreach (array_filter($descricao, function ($mamale) use ($a){
                    return $mamale["id"]==$a["id"];
                }) as $desc){
                    
                    $marcador="";
                    if($pontoObtido>($desc["pontuacao"]-5) && $pontoObtido<=$desc["pontuacao"]){
                        $marcador="X";
                    }

                    $contador++;

                    $this->html .="<tr>";
                    if($contador==1){
                        $this->html .="<td style='".$this->border().$this->bolder.$this->text_center."' rowspan='4'>".$a["coeficiente"]."</td>";
                    }
                    $this->html .="
                        <td style='".$this->border()."'>".$desc["titulo"]."</td>
                        <td style='".$this->border().$this->bolder.$this->text_center."'>".$desc["pontuacao"]."</td>
                        <td style='".$this->border().$this->bolder.$this->text_center."'>".$marcador."</td>
                    </tr>";
                }
            }

            $factores[] = array("titulo"=>"Capacidade de análise Profissional", "tituloDb"=>"CAP", "cotacao"=>0.1);
            $factores[] = array("titulo"=>"Interesse", "tituloDb"=>"interesse", "cotacao"=>0.1);
            $factores[] = array("titulo"=>"Conhecimentos ligados ao trabalho", "tituloDb"=>"CLT", "cotacao"=>0.1);
            $factores[] = array("titulo"=>"Organização", "tituloDb"=>"organizacao", "cotacao"=>0.1);
            $factores[] = array("titulo"=>"Sigilo Profissional", "tituloDb"=>"SP", "cotacao"=>0.1);
            $factores[] = array("titulo"=>"Criatividade", "tituloDb"=>"criatividade", "cotacao"=>0.1);
            $factores[] = array("titulo"=>"Relacionamento interpessoal", "tituloDb"=>"RIP", "cotacao"=>0.1);
            $factores[] = array("titulo"=>"Atenção", "tituloDb"=>"atencao", "cotacao"=>0.1);
            $factores[] = array("titulo"=>"Pontualidade e Assiduidade", "tituloDb"=>"PA", "cotacao"=>0.1);
            $factores[] = array("titulo"=>"Disciplina", "tituloDb"=>"disciplina", "cotacao"=>0.1);

            $soma=0;
            foreach($factores as $fact){
                $soma +=floatval(valorArray($this->entidade, $fact["tituloDb"], "aval_desemp"))*$fact["cotacao"];
            }
            $soma = number_format($soma, 0);
            $this->html .="</table>!--><br>
            <table style='".$this->tabela."width:100%;'>
                <tr style='".$this->bolder.$this->text_center."'>
                    <td style='".$this->border()."'>N.º</td><td style='".$this->border()."'>Indicadores</td><td style='".$this->border()."'>Pontos</td>
                </tr>"; 

                $i=0;
                foreach($factores as $fact){
                    $i++;
                    $this->html .="
                    <tr>
                        <td style='".$this->border().$this->text_center."'>".completarNumero($i)."</td><td style='".$this->border()."'>".$fact["titulo"]."</td><td style='".$this->border().$this->text_center."'>".(floatval(valorArray($this->entidade, $fact["tituloDb"], "aval_desemp"))*$fact["cotacao"])."</td>
                    </tr>";
                }

                $this->html .="
                <tr>
                    <td style='".$this->border().$this->text_center.$this->bolder."' colspan='3'>Classificação total</td>
                </tr>
                <tr>
                    <td style='".$this->border().$this->text_center."'>1</td><td style='".$this->border()."'>Quantitativa</td><td style='".$this->border().$this->text_center."'>".$soma."</td>
                </tr>
                <tr>
                    <td style='".$this->border().$this->text_center."'>2</td><td style='".$this->border()."'>Qualitativa</td><td style='".$this->border().$this->text_center."'>".$this->classificacao2($soma)."</td>
                </tr>
            </table>
            <p style='".$this->bolder.$this->text_center."'>Apreciação geral<br>(comentários dos avaliadores)</p>

            <p style='".$this->bolder.$this->miniParagrafo."'>Comentário</p>

            <p style='".$this->text_justify."line-height:25px;'>".valorArray($this->entidade, "comentario", "aval_desemp").".</p>

            <p style='".$this->bolder.$this->text_center."'>Assinatura</p>

            Nome: <strong>".valorArray($coordenador, "nomeEntidade")."</strong><br/>
                Função: <strong>".valorArray($coordenador, "funcaoEnt", "escola")."</strong><br/>
                Data: ".dataExtensa(valorArray($this->entidade, "dataAvaliacao", "aval_desemp"))."<br/>
                <p style='".$this->text_center."'>O Avaliador</p>
                <p style='".$this->text_center."'>_______________________________</p><br/></div>

                <p style='".$this->text_center.$this->bolder."'>Concordância com a avaliação</p>

                <p style='".$this->text_center."'>Concordo <span style='".$this->border()."'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Não concordo <span style='".$this->border()."'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></p>

                 <p style='".$this->text_center."'>O Avaliado</p>
                <p style='".$this->text_center."'>_______________________________</p><br/>
                Data: ____/_____/__________<br/>
                <p style='".$this->text_center.$this->maiuscula."'>O Homologante</p>
                <p style='".$this->text_center."'>_______________________________</p><br/><br/>
            ";
            
            $this->exibir("", "Ficha de Avaliação de Desempenho - ".valorArray($this->entidade, "nomeEntidade")." ".$this->numAno);
        }

        private function docente2020(){

            $total = valorArray($this->entidade, "qualProcEnsAprend", "aval_desemp")+valorArray($this->entidade, "aperfProfissional", "aval_desemp")+valorArray($this->entidade, "inovPedag", "aval_desemp")+valorArray($this->entidade, "resposabilidade", "aval_desemp")+valorArray($this->entidade, "relHumTrabalho", "aval_desemp");

            $this->html .="<html style='margin-left:70px; margin-right:50px; margin-top:70px; margin-bottom:0px;'>
            <head>
                <title>Ficha de Avaliação de Desempenho</title>
                <style>
                </style>
            </head>
            <body>
            <div class='cabecalho'>";
            if($_SESSION["idEscolaLogada"]==16){
                $this->html .="<p style='".$this->text_center.$this->miniParagrafo."'></p><p style='".$this->text_center.$this->miniParagrafo."'><img src='".$_SERVER['DOCUMENT_ROOT']."/angoschool/icones/insignia.jpg' style='".$this->insignia_medio."'></p>
            <p style='".$this->text_center.$this->miniParagrafo."'>REPÚBLICA DE ANGOLA</p>
            <p style='".$this->text_center."'>MINISTÉRIO DA EDUCAÇÃO</p>
            <p style='".$this->miniParagrafo."'>a) <span style='".$this->maiuscula."'>PROVÍNCIA DO ".valorArray($this->sobreUsuarioLogado, "provincia")."</span></p>
            <p style='".$this->miniParagrafo."'>b) <span style='".$this->maiuscula."'>MUNICIPIO DE ".valorArray($this->sobreUsuarioLogado, "municipio")."</span></p>
            
            <p>c) <span style='".$this->maiuscula."'>".valorArray($this->sobreUsuarioLogado, "nomeEscola")."</span></p>
            
            <p style='".$this->bolder.$this->text_center."line-height:25px;'>Ficha de Avaliação de desempenho do pessoal docente do<br/>Ensino Primário e Secundário</p><br/>";
            }else{
                $this->html .=$this->cabecalho()."<p style='".$this->bolder.$this->text_center.$this->maiuscula."line-height:25px;'>Ficha de Avaliação de desempenho do pessoal docente do<br/>Ensino Primário e Secundário</p><br/>";
            }
            
            $this->html .="</div><p>Nome: <strong>".valorArray($this->entidade, "nomeEntidade")."</strong></p>
            <p>Categoria: <strong>".valorArray($this->entidade, "categoriaEntidade")."</strong></p>
            <p>Escola: <strong>".valorArray($this->sobreUsuarioLogado, "nomeEscola")."</strong></p>
            <p>CIF: _______________</strong></p>
            <p>Agente: <strong>".valorArray($this->entidade, "numeroAgenteEntidade")."</strong></p>
            <p>Data de avaliação: _____/_______/___________</p><br/>

            <div style='".$this->border().$this->text_center.$this->bolder." padding:5px;'>Período a que respeita a avaliação
                de<br/><br/>_______/______/_________</div>
            <div style='border:solid black 1px; padding:5px;'>
                <p style='".$this->bolder.$this->text_center."'>Pontuação dos Factores de Avaliação</p>
                <table style='width:100%; border-spaccing:0px; font-size:12pt;'>
                    <tr>
                        <td style='width:15px;'>1.</td><td style='".$this->text_left."'>Qualidade do processo de ensino aprendizagem ..................................................</td><td style='".$this->border().$this->text_center."width:120px;'>".valorArray($this->entidade, "qualProcEnsAprend", "aval_desemp")."</td>
                    </tr>
                    <tr>
                        <td style='width:15px;'>2.</td><td style='".$this->text_left."'>Aperfeiçoamento profissional ...............................................................................</td><td style='".$this->border().$this->text_center."width:120px;'>".valorArray($this->entidade, "aperfProfissional", "aval_desemp")."</td>
                    </tr>
                    <tr>
                        <td style='width:15px;'>3.</td><td style='".$this->text_left."'>Inovação Pedagógica ............................................................................................</td><td style='".$this->border().$this->text_center."width:120px;'>".valorArray($this->entidade, "inovPedag", "aval_desemp")."</td>
                    </tr>
                    <tr>
                        <td style='width:15px;'>4.</td><td style='".$this->text_left."'>Responsabilidade .................................................................................................</td><td style='".$this->border().$this->text_center."width:120px;'>".valorArray($this->entidade, "resposabilidade", "aval_desemp")."</td>
                    </tr>
                    <tr>
                        <td style='width:15px;'>5.</td><td style='".$this->text_left."'>Relações humanas no trabalho ............................................................................</td><td style='".$this->border().$this->text_center."width:120px;'>".valorArray($this->entidade, "relHumTrabalho", "aval_desemp")."</td>
                    </tr>
                    <tr>
                        <td style='width:15px;'>6.</td><td style='".$this->text_left."'>Pontuação Total Obtida .......................................................................................</td><td style='".$this->border().$this->text_center."width:120px;'>".$total."</td>
                    </tr>
                    <tr>
                        <td style='width:15px;'>7.</td><td style='".$this->text_left."'>Avaliação de desempenho ...................................................................................</td><td style='".$this->border().$this->text_center."width:120px;'>".number_format(($total/10), 0)."</td>
                    </tr>
                    <tr>
                        <td style='width:15px;'>8.</td><td style='".$this->text_left."'>Apreciação geral .................................................................................................</td><td style='".$this->border().$this->text_center."width:120px;'>".$this->classificacao(($total/10))."</td>
                    </tr>
                </table>
            </div>
            <div style='page-break-before: always;'>
            
                <p style='".$this->bolder.$this->text_center."'>Apreciação Geral<br/>(Comentários do Avaliador)</p>
                _______________________________________________________________________________<br/><br/>
                _______________________________________________________________________________<br/><br/>
                _______________________________________________________________________________<br/><br/>
                _______________________________________________________________________________

                <br/><br/><br/><div style='".$this->maiuscula."'>";
                $this->nomeDirigente("Director");
            $this->html .="Nome: <strong>".$this->nomeDirigente."</strong><br/>
                Função: <strong>Director</strong><br/><br/>
                Data: ____/_____/__________<br/><br/>
                <p style='".$this->text_center."'>O Avaliador</p>
                <p style='".$this->text_center."'>_______________________________</p><br/></div>

                <p style='".$this->text_center.$this->bolder."'>Concordância com a avaliação</p>

                <p style='".$this->text_center."'>Concordo: <span style='".$this->border()."'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Não concordo: <span style='".$this->border()."'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></p>

                 <p style='".$this->text_center.$this->maiuscula."'>O Avaliado</p>
                <p style='".$this->text_center."'>_______________________________</p><br>
                
                <div style='".$this->maiuscula."'>
                Nome: <strong>José Luís Amélia</strong><br/>
                Função: <strong>Director do Gabinete Provincial da Educação</strong><br/><br/>
                Data: ____/_____/__________<br/><br/></div>

                <p style='".$this->bolder.$this->text_center."'>Comentário</p>
                _______________________________________________________________________________<br/><br/>
                _______________________________________________________________________________<br/><br/>
                _______________________________________________________________________________<br/><br/>
                _______________________________________________________________________________<br/><br/>

                <p style='".$this->text_center.$this->maiuscula."'>O Homologante</p>
                <p style='".$this->text_center."'>_______________________________</p><br/><br/>
            </div>";
            
            $this->exibir("", "Ficha de Avaliação de Desempenho - ".valorArray($this->entidade, "nomeEntidade")." ".$this->numAno);
        }

        private function naoDocente2020(){

            $total = intval(valorArray($this->entidade, "qualTrabalho", "aval_desemp"))+intval(valorArray($this->entidade, "aperfProf", "aval_desemp"))+intval(valorArray($this->entidade, "responsabilidade", "aval_desemp"))+intval(valorArray($this->entidade, "relHumTrabalho", "aval_desemp"))+intval(valorArray($this->entidade, "epiritoIniciativa", "aval_desemp"));

            $this->html .="<html style='margin-left:70px; margin-right:50px; margin-top:70px; margin-bottom:0px;'>
            <head>
                <title>Ficha de Avaliação de Desempenho</title>
                <style>
                </style>
            </head>
            <body>
            <div class='cabecalho'>";
            
            if($_SESSION["idEscolaLogada"]==16){
                $this->html .="<p style='".$this->text_center.$this->miniParagrafo."'></p><p style='".$this->text_center.$this->miniParagrafo."'><img src='".$_SERVER['DOCUMENT_ROOT']."/angoschool/icones/insignia.jpg' style='".$this->insignia_medio."'></p>
                <p style='".$this->text_center.$this->miniParagrafo."'>REPÚBLICA DE ANGOLA</p>
                <p style='".$this->text_center."'>MINISTÉRIO DA EDUCAÇÃO</p>
                <p style='".$this->miniParagrafo."'>a) <span style='".$this->maiuscula."'>PROVÍNCIA DO ".valorArray($this->sobreUsuarioLogado, "provincia")."</span></p>
                <p style='".$this->miniParagrafo."'>b) <span style='".$this->maiuscula."'>MUNICIPIO DE ".valorArray($this->sobreUsuarioLogado, "municipio")."</span></p>
                
                <p>c) <span style='".$this->maiuscula."'>".valorArray($this->sobreUsuarioLogado, "nomeEscola")."</span></p>
                
                <p style='".$this->bolder.$this->text_center."line-height:25px;'>Ficha de Avaliação de desempenho dos técnicos e especialistas de administração da educação</p><br/>";
            }else{
                $this->html .=$this->cabecalho()."<p style='".$this->bolder.$this->text_center.$this->maiuscula."line-height:25px;'>Ficha de Avaliação de desempenho dos técnicos e especialistas de administração da educação</p><br/>";
            }
            
            $this->html .="
            </div><p>Nome: <strong>".valorArray($this->entidade, "nomeEntidade")."</strong></p>
            <p>Categoria: <strong>".valorArray($this->entidade, "categoriaEntidade")."</strong></p>
            <p>Escola: <strong>".valorArray($this->sobreUsuarioLogado, "nomeEscola")."</strong></p>
            <p>CIF: _______________</strong></p>
            <p>Agente: <strong>".valorArray($this->entidade, "numeroAgenteEntidade")."</strong></p>
            <p>Data de avaliação: _____/_______/___________</p><br/>

            <div style='".$this->border().$this->text_center.$this->bolder." padding:5px;'>Período a que respeita a avaliação
                de<br/><br/>_______/______/_________</div>
            <div style='border:solid black 1px; padding:5px;'>
                <p style='".$this->bolder.$this->text_center."'>Pontuação dos factores de Avaliação</p>
                <table style='width:100%; border-spaccing:0px; font-size:12pt;'>
                    <tr>
                        <td style='width:15px;'>1.</td><td style='".$this->text_left."'>Qualidade de Trabalho ....................................................................</td><td style='".$this->border().$this->text_center."width:120px;'>".valorArray($this->entidade, "qualTrabalho", "escola")."</td>
                    </tr>
                    <tr>
                        <td style='width:15px;'>2.</td><td style='".$this->text_left."'>Aperfeiçoamento profissional ...............................................................................</td><td style='".$this->border().$this->text_center."width:120px;'>".valorArray($this->entidade, "aperfProf", "aval_desemp")."</td>
                    </tr>
                    <tr>
                        <td style='width:15px;'>3.</td><td style='".$this->text_left."'>Espírito de Iniciativa .............................................................................................</td><td style='".$this->border().$this->text_center."width:120px;'>".valorArray($this->entidade, "epiritoIniciativa")."</td>
                    </tr>
                    <tr>
                        <td style='width:15px;'>4.</td><td style='".$this->text_left."'>Responsabilidade .................................................................................................</td><td style='".$this->border().$this->text_center."width:120px;'>".valorArray($this->entidade, "responsabilidade", "aval_desemp")."</td>
                    </tr>
                    <tr>
                        <td style='width:15px;'>5.</td><td style='".$this->text_left."'>Relações humanas no trabalho .............................................................................</td><td style='".$this->border().$this->text_center."width:120px;'>".valorArray($this->entidade, "relHumTrabalho", "aval_desemp")."</td>
                    </tr>
                    <tr>
                        <td style='width:15px;'>6.</td><td style='".$this->text_left."'>Pontuação Total Obtida .......................................................................................</td><td style='".$this->border().$this->text_center."width:120px;'>".$total."</td>
                    </tr>
                    <tr>
                        <td style='width:15px;'>7.</td><td style='".$this->text_left."'>Avaliação de desempenho ..................................................................................</td><td style='".$this->border().$this->text_center."width:120px;'>".number_format(($total/10), 0)."</td>
                    </tr>
                    <tr>
                        <td style='width:15px;'>8.</td><td style='".$this->text_left."'> Apreciação Geral ...............................................................................................</td><td style='".$this->border().$this->text_center."width:120px;'>".$this->classificacao(($total/10))."</td>
                    </tr>
                </table>
            </div>
            <div style='page-break-before: always;'>
            
                <p style='".$this->bolder.$this->text_center."'>Apreciação Geral<br/>(Comentários do Avaliador)</p>
                _______________________________________________________________________________<br/><br/>
                _______________________________________________________________________________<br/><br/>
                _______________________________________________________________________________<br/><br/>
                _______________________________________________________________________________

                <br/><br/><div style='".$this->maiuscula."'>";
                $this->nomeDirigente("Director");
            $this->html .="Nome: <strong>".$this->nomeDirigente."</strong><br/>
                Função: <strong>Director</strong><br/><br/>
                Data: ____/_____/__________<br/><br/>
                <p style='".$this->text_center."'>O Avaliador</p>
                <p style='".$this->text_center."'>_______________________________</p><br/></div>

                <p style='".$this->text_center.$this->bolder."'>Concordância com a avaliação</p>

                <p style='".$this->text_center."'>Concordo: <span style='".$this->border()."'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Não concordo: <span style='".$this->border()."'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></p>

                 <p style='".$this->text_center."'>O Avaliado</p>
                <p style='".$this->text_center."'>_______________________________</p><br/>

                <div style='".$this->maiuscula."'>
                Nome: <strong>José Luís Amélia</strong><br/>
                Função: <strong>Director do Gabinete Provincial da Educação</strong><br/><br/>
                Data: ____/_____/__________<br/><br/></div>

                <p style='".$this->bolder.$this->text_center."'>Comentário</p>
                _______________________________________________________________________________<br/><br/>
                _______________________________________________________________________________<br/><br/>
                _______________________________________________________________________________<br/><br/>
                _______________________________________________________________________________<br/><br/>

                <p style='".$this->text_center.$this->maiuscula."'>O Homologante</p>
                <p style='".$this->text_center."'>_______________________________</p><br/><br/>
            </div>";
            
    
            $this->exibir("", "Mapa Geral de Avaliação de Desempenho-".$this->numAno);
        }

        private function classificacao($classificacao){
            if($classificacao<=9){
                return "Medíocre";
            }else if($classificacao<14){
                return "Suficiente";
            }else if($classificacao<=17){
                return "Bom";
            }else if($classificacao<=20){
                return "Muito Bom";
            }
        }

        private function classificacao2($classificacao){
            if($classificacao<=9){
                return "Mau";
            }else if($classificacao<14){
                return "Suficiente";
            }else if($classificacao<=17){
                return "Bom";
            }else if($classificacao<=20){
                return "Muito Bom";
            }
        }
    }

new mapaForcaTrabalho(__DIR__);
    
    
  
?>