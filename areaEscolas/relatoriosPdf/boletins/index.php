<?php if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');

    class boletins extends funcoesAuxiliares{

        function __construct($caminhoAbsoluto){
            parent::__construct("Rel-Boletim");

            $this->classe = isset($_GET["classe"])?$_GET["classe"]:null;
            $this->turma = isset($_GET["turma"])?$_GET["turma"]:null;
            $this->idPCurso = isset($_GET["idPCurso"])?$_GET["idPCurso"]:null;
            $this->trimestre = isset($_GET["trimestreApartir"])?$_GET["trimestreApartir"]:null;
            $this->idPAno = isset($_GET["idPAno"])?$_GET["idPAno"]:$this->idAnoActual;
            $this->idAlunosVisualizar = isset($_GET["idAlunosVisualizar"])?$_GET["idAlunosVisualizar"]:"";

            $this->arrayAlunos=array();
            foreach(explode(",", $this->idAlunosVisualizar) as $id){
                $this->arrayAlunos[]=intval($id);
            }
            if(isset($_GET["idPMatricula"])){
                $this->idPMatricula = $_GET['idPMatricula'];
                $this->sobreAluno($this->idPMatricula, ["reconfirmacoes.idReconfAno", "reconfirmacoes.idReconfEscola", "escola.idMatEscola", "escola.idMatCurso", "reconfirmacoes.nomeTurma", "reconfirmacoes.classeReconfirmacao"]);
                $verificador = listarItensObjecto($this->sobreAluno, "reconfirmacoes", ["idReconfAno=".$this->idPAno, "idReconfEscola=".$_SESSION['idEscolaLogada']]);
                $this->idPCurso = valorArray($this->sobreAluno, "idMatCurso", "escola");
                $this->turma = valorArray($verificador, "nomeTurma");
                $this->classe = valorArray($verificador, "classeReconfirmacao");
                $this->arrayAlunos[]=intval($this->idPMatricula);
            } 
             $this->trimestreExtensa="I Trimestre";
            if($this->trimestre=="I"){
              $this->trimNumero=1;
                $this->trimestreExtensa="do I Trimestre";
            }else if($this->trimestre=="II"){
                $this->trimNumero=2;
                $this->trimestreExtensa="do II Trimestre";
            }else if($this->trimestre=="III"){
              $this->trimNumero=3;
                $this->trimestreExtensa="do III Trimestre";
            }else{
              $this->trimNumero=4;
                 $this->trimestreExtensa="Final";
            }
            $styleHtmlBody="font-size: 11.5pt; margin-top: 10px; margin-bottom: 0px;"; 

            $this->nomeCurso();
            $this->numAno();
            $this->html .="<html style='".$styleHtmlBody."'>
            <head>
                <title>Boletim ".$this->trimestreExtensa."</title>
                <style>
                    .tabela tr td{
                        border-left: solid black 1px;
                        border-bottom: solid black 1px;
                        padding:2px;

                    }
                    .tabela2 tr td{
                        border-left: solid black 1px;
                        border-bottom: solid black 1px;
                        padding:1px;
                        font-size:11pt;
                    }
                </style>
            </head>

            <body style='".$styleHtmlBody."'>
            "; 
            if($this->verificacaoAcesso->verificarAcesso("", ["relatorioAluno", "impressaoPersonalizada"], [$this->classe, $this->idPCurso], "")){
                $this->boletim();
            }else{
              $this->negarAcesso();
            }
            
        }

         private function boletim(){
            
            $notaMedia=10;
            if($this->classe<=5){
                $notaMedia=5;
            }

            $assinaturaDirector = $this->assinaturaDirigentes(7);
            $assinaturaPedagogico = $this->assinaturaDirigentes("mengi"); 

            $this->precoBoletim = $this->preco("boletim", $this->classe, $this->idPCurso);

            $this->nomeTurma("", "", "", $this->idPAno);

            $curriculoClasse = $this->disciplinas ($this->idPCurso, $this->classe, valorArray($this->sobreTurma, "periodoTurma"), "", array(), [58, 59, 60, 231, 232, 233], ["idPNomeDisciplina", "disciplinas.classeDisciplina", "nomeDisciplina", "disciplinas.semestreDisciplina", "escola.beneficiosDaBolsa", "disciplinas.semestreDisciplina", "disciplinas.ordenacao"], $this->idPAno);

            $tipo="pautas";
            if($this->idPAno!=$this->idAnoActual){
                $tipo="arquivo_pautas";
            }

            $nomeAlunoUnicoExibir="";


            $campos = ["nomeAluno", "numeroInterno", "reconfirmacoes.mfT1", "reconfirmacoes.mfT2", "reconfirmacoes.mfT3", "reconfirmacoes.mfT4", "grupo", "sexoAluno", "fotoAluno", "reconfirmacoes.classeReconfirmacao", "reconfirmacoes.nomeTurma", "idPMatricula", "reconfirmacoes.idReconfAno", "reconfirmacoes.idReconfEscola", "reconfirmacoes.observacaoF", "reconfirmacoes.seAlunoFoiAoRecurso", $tipo.".classePauta", $tipo.".semestrePauta", "escola.periodoAluno", $tipo.".idPautaCurso", $tipo.".idPautaDisciplina", $tipo.".idPautaAno", "pagamentos.idHistoricoAno", "pagamentos.referenciaPagamento", "pagamentos.idHistoricoEscola", "pagamentos.codigoEmolumento", "pagamentos.idPHistoricoConta", "escola.beneficiosDaBolsa"];

            $camposAvaliacoes = $this->cabecalhoBoletim ($this->idPAno, $this->idPCurso, $this->classe, $this->trimestre);            
            foreach($camposAvaliacoes as $campo){
                $campos[]=$tipo.".".$campo["identUnicaDb"];

            }

            if($this->trimestre!="IV"){ 
                $campos[]=$tipo.".numeroFaltas".$this->trimestre;
                $campos[]=$tipo.".comportamento".$this->trimestre;
                $campos[]=$tipo.".assiduidade".$this->trimestre;
            }
            if($this->idPAno==$this->idAnoActual){
                $this->papaJipe($this->idPCurso, $this->classe, $this->turma);
            }
            foreach ($this->alunosPorTurma($this->idPCurso, $this->classe, $this->turma, $this->idPAno, $this->arrayAlunos, $campos) as $todos) {
                  
                $this->precoBoletim = $this->preco("boletim", $this->classe, $this->idPCurso, "", $todos);


                $nomeAlunoUnicoExibir = $todos["nomeAluno"];

                if($todos["reconfirmacoes"]["classeReconfirmacao"]<=6){
                    $this->notaMinima=5;
                }else{
                    $this->notaMinima=10;
                }

                $art1="o";
                $art2 ="";
                if($todos["sexoAluno"]=="F"){
                    $art1="a";
                    $art2 ="a";
                }

                $artigo1="o";
                $artigo2="";
                if(valorArray($this->sobreTurma, "generoEntidade")=="F"){
                    $artigo1="a";
                    $artigo2="a";
                }
            

            $this->html .=" 
            <div style='page-break-after: always;'>".$this->fundoDocumento("../../../")."
            
            <div style='border:solid black 2px; padding:5px; height:180px;'>
                <div style='padding-top:20px;'>";

                $src = '../../../Ficheiros/Escola_'.$_SESSION['idEscolaLogada'].'/Icones/'.valorArray($this->sobreUsuarioLogado, "logoEscola");
                if(!file_exists($src) || valorArray($this->sobreUsuarioLogado, "logoEscola")==NULL || valorArray($this->sobreUsuarioLogado, "logoEscola")==""){
                  $src = '../../../icones/insignia.jpg';
                }

                $this->html .="<img src='".$src."' style='height:130px; width:120px;'></div>

                <div style='margin-left:125px; margin-top:-200px;'>
                <div style='min-height:70px;'>".$this->cabecalho("nao", "text-align:left;", "text-transform:uppercase;","width:420px; height:35px;")."
                </div>

                    <p style='".$this->bolder.$this->text_center.$this->miniParagrafo."'>BOLETIM DE NOTAS - ";
                    if($this->trimestre=="IV"){
                        $this->html .="FINAL";
                    }else{
                        $this->html .=$this->trimestre." TRIMESTRE";
                    }
                    $this->html .="</p>
                    <p style='".$this->bolder.$this->miniParagrafo.$this->text_center." font-size:18pt;'>".$this->numAno."</p>

                    <div id='fotoAluno'>
                        <div style='margin-top: -130px; text-align: right; width: 100%;'>
                            <img src='";
                    $foto= "../../../fotoUsuarios/".$todos["fotoAluno"];
                    if(!file_exists($foto)){
                        $foto= "../../../fotoUsuarios/default.png";
                    }
                    $this->html .=$foto."' style='border:solid #428bca 1px; border-radius: 10px; width: 120px; height: 130px;'>
                        </div>
                    </div>

                </div>
               </div>
               <div style='border:solid black 2px; margin-top:10px; background-color: rgba(0, 0, 0, 0.5); color:white;".$this->text_center." padding-top:5px;'>
                <strong>Dados do Aluno</strong>

                <div style='border:solid black 2px; padding:5px; background-color:white;color:black; margin-top:5px; margin-bottom:5px; border-left:none; border-right:none;'>
                    <table class='tabela' style='width:100%; '>

                        <tr>
                            <td style='".$this->text_right."'>Nome:</td>
                            <td colspan='3'><strong>".$todos["nomeAluno"]."</strong></td>
                        </tr>
                        <tr>
                            <td style='".$this->text_right."'>Sexo:</td>
                            <td><strong>".generoExtenso($todos["sexoAluno"])."</strong></td>
                            <td style='".$this->text_right."'>N.º Interno:</td>
                            <td><strong>".$todos["numeroInterno"]."</strong></td>
                        </tr>";

                        if($todos["reconfirmacoes"]["classeReconfirmacao"]>=10){
                             if($this->tipoCurso=="tecnico"){
                                $this->html .="
                                <tr>
                                <td style='".$this->text_right."'>Área de Formação:</td><td><strong>".$this->areaFormacaoCurso."</strong></td>

                                    <td style='".$this->text_right."'>Curso:</td><td><strong>".$this->nomeCurso."</strong></td>
                                </tr>";
                            }else if($this->tipoCurso=="pedagogico"){
                                $this->html .="
                                <tr>
                                <td style='".$this->text_right."'>Curso:</td><td><strong>".$this->areaFormacaoCurso."</strong></td>

                                    <td style='".$this->text_right."'>Opção:</td><td><strong>".$this->nomeCurso."</strong></td>
                                </tr>";
                            }else{
                                $this->html .="
                                <tr>
                                    <td style='".$this->text_right."'>Curso:</td><td colspan='3'><strong>".$this->nomeCurso."</strong></td>
                                </tr>";
                            }
                        }
                        $this->html .="
                        <tr>
                        <td style='".$this->text_right."'>Classe:</td><td><strong>".classeExtensa($this, $this->idPCurso, $todos["reconfirmacoes"]["classeReconfirmacao"])."</strong></td>

                            <td style='".$this->text_right."'>Turma:</td><td><strong>".valorArray($this->sobreTurma, "designacaoTurma", "reconfirmacoes")."</strong></td>
                        </tr>
                        <tr>
                        <td style='".$this->text_right."'>Período:</td><td><strong>".valorArray($this->sobreTurma, "periodoT")."</strong>";
                        if($this->trimNumero==4){
                            $this->html .="<td style='".$this->text_right."'>Observação Final:</td><td><strong>".$this->observacaoFinal(valorArray($todos, "observacaoF", "reconfirmacoes"), valorArray($todos, "seAlunoFoiAoRecurso", "reconfirmacoes"))."</strong></td>";
                        }                    
                    $this->html .="
                        </tr></table>
                </div>
                <strong>Aproveitamento</strong>

                <div style='border:solid black 2px; padding:5px; background-color:white;color:black;margin-top:5px; border-left:none; border-right:none;'>
                        <table class='tabela2' style='width:100%;'><tr><td style='".$this->text_center."'><strong>Disciplina</strong></td>";

                        foreach($camposAvaliacoes as $campo){
                            $this->html .="<td style='".$this->text_center."'><strong>".$campo["designacao1"]."</strong></td>";
                        }
                        if(!($this->idPAno==1 || $this->idPAno==842 || $this->idPAno==9266)  && $this->trimestre!="IV"){
                            $this->html .="<td style='".$this->text_center."'><strong>N.º F</strong></td><td style='".$this->text_center."'><strong>Comport.</strong></td><td style='".$this->text_center."'><strong>Assiduidade</strong></td>";   
                        }
                        $this->html .="</tr>";

                        $contador=0;
                        $mediaMt=0;
                        $contadorFaltas=0;

                        if(!$this->seJaFezPagamento($todos)){

                            $this->html .='<tr style="'.$this->bolder.'"><td style="'.$this->text_center.$this->vermelha.' padding-top:100px; padding-bottom:100px; font-size:16pt; border:none;" colspan="'.count($camposAvaliacoes).'"><strong>AINDA NÃO FEZ PAGAMENTO DO BOLETIM</strong></td>';
                        }else{
                            $condicaoNota = ["classePauta=".$todos["reconfirmacoes"]["classeReconfirmacao"]];

                            if($todos["reconfirmacoes"]["classeReconfirmacao"]>=10){
                                $condicaoNota[]= "idPautaCurso=".$this->idPCurso;
                            }
                            if($this->idPAno!=$this->idAnoActual){
                                $condicaoNota[]= "idPautaAno=".$this->idPAno;
                            }
                            $arrayNotas = listarItensObjecto($todos, $tipo, $condicaoNota);
                            $notasAlunos=array();
                            $i=0;
                            foreach($arrayNotas as $nota){
                                foreach($curriculoClasse as $curriculo){
                                    if($curriculo["disciplinas"]["classeDisciplina"]==$nota["classePauta"] && $curriculo["disciplinas"]["semestreDisciplina"]==$nota["semestrePauta"] && $curriculo["idPNomeDisciplina"]==$nota["idPautaDisciplina"] ){
                                        
                                        $notasAlunos[$i]=$nota;
                                        $notasAlunos[$i]["nomeDisciplina"]=$curriculo["nomeDisciplina"];
                                        $notasAlunos[$i]["ordenacao"]=$curriculo["disciplinas"]["ordenacao"];
                                        $i++;             
                                    }
                                }
                            }

                            $notasAlunos = ordenar($notasAlunos, "ordenacao ASC");

                            foreach ($notasAlunos  as $pauta) {

                                $this->html .='<tr><td class="lead">'.$pauta["nomeDisciplina"].'</td>';

                                foreach($camposAvaliacoes as $campo){
                                    $this->html .=$this->tratarVermelha(nelson($pauta, $campo["identUnicaDb"]), "", $campo["notaMedia"], $campo["cd"]); 
                                }

                                if(!($this->idPAno==1 || $this->idPAno==842 || $this->idPAno==9266)  && $this->trimestre!="IV"){
                                    $contadorFaltas += intval(nelson($pauta, "numeroFaltas".$this->trimestre));

                                    $this->html .="<td style='".$this->text_center.$this->vermelha."'>".nelson($pauta, "numeroFaltas".$this->trimestre)."</td>
                                        <td style='".$this->text_center."'>".$this->bomMau(nelson($pauta, "comportamento".$this->trimestre))."</td>
                                        <td style='".$this->text_center."'>".$this->bomMau(nelson($pauta, "assiduidade".$this->trimestre))."</td>";
                                }    
                                $this->html .="</tr>";
                            }

                            $this->html .='<tr style="'.$this->bolder.'"><td style="'.$this->text_center.'" colspan="'.count($camposAvaliacoes).'"><strong>Média</strong></td>'.$this->tratarVermelha(number_format(floatval(valorArray($todos, "mfT".$this->trimNumero, "reconfirmacoes")), 0), "", $notaMedia);

                            if(!($this->idPAno==1 || $this->idPAno==842 || $this->idPAno==9266) && $this->trimestre!="IV"){
                                $this->html .="<td style='".$this->text_center.$this->vermelha."'>".$contadorFaltas."</td>";
                            }
                            $this->html .="</tr>";
                    }
                    $this->html .="</table>
                    <p style='".$this->text_left.$this->maiuscula."'>".$this->rodape()."</p>";

                    if($_SESSION['idEscolaLogada']==27){
                        $this->html .="<div class='mainFooter' style='margin-top:-10px;'>
                            <div class='foot1' style='width:50%;'>".$this->porAssinatura("O(a) Director(a) de Turma", "", "", 30)."</div>
                            <div class='foot2' style='width:50%; margin-top:-110px; margin-left:50%; width:50%;".$this->text_center."' >".$assinaturaPedagogico."
                            </div>
                        </div>";
                    }else{
                        $this->html .="<div class='mainFooter' style='margin-top:-10px;'>
                            <div class='foot1' style='width:50%;'>".$assinaturaPedagogico."</div>
                            <div class='foot2' style='width:50%; margin-top:-110px; margin-left:50%; width:50%;".$this->text_center."' >".$assinaturaDirector."
                            </div>
                        </div>";
                    }

                    $this->html .="<br/><br/>

                    </div> 
            </div></div>";
          }
          $this->html .="</body></html>";
        

          if(count($this->arrayAlunos)>0){
            $this->exibir("", "Boletim-".$nomeAlunoUnicoExibir."-".$this->trimestreExtensa."-".$this->numAno);
          }else{
            $this->exibir("Ficheiros/Escola_".$_SESSION['idEscolaLogada']."/".$this->idPAno."/Boletins", "Boletins-".$this->nomeCursoAbr."-".$this->classe."-".$this->turma."-".$this->trimestre."-".$this->numAno, "Boletins-".$this->idPCurso."-".$this->classe."-".$this->turma."-".$this->trimestre."-".$this->idPAno);
          }
        }

        private function obs($nota, $i=1){
            $i=1;
            if((int)$nota<5*$i){
                return "<td style='".$this->text_center."'><span style='".$this->vermelha."'>Mau</span></td>";
            }else if((int)$nota<7*$i){
                return "<td style='".$this->text_center."'><span style='".$this->azul."'>Suficiente</span></td>";
            }else if((int)$nota<8.5*$i){
                return "<td style='".$this->text_center."'><span style='".$this->azul."'>Bom</span></td>";
            }else{
                return "<td style='".$this->text_center."'><span style='".$this->verde."'>M. Bom</span></td>";
            }
        }
        private function observacaoFinal($obs, $seAlunoFoiAoRecurso){
            if($obs=="A"){
                return "<span style='".$this->verde."'>Apto(A)</span>";
            }else if($obs=="TR"){
                return "<span style='".$this->verde."'>Transita</span>";
            }else if($obs=="D"){
                return "<span style='".$this->vermelha."'>Desistente</span>";
            }else if($obs=="N"){
                return "<span style='".$this->vermelha."'>Matricula Anulada</span>";
            }else if($obs=="EF"){
                return "<span style='".$this->vermelha."'>Excluído por Faltas</span>";
            }else if($obs=="D"){
                return "<span style='".$this->vermelha."'>Rep. por Indisciplina</span>";
            }else if($obs=="F"){
                return "<span style='".$this->vermelha."'>Rep. por Faltas</span>";
            }else{
                if($seAlunoFoiAoRecurso=="A"){
                    return "<span style='".$this->azul."'>Recurso</span>";
                }else{
                    return "<span style='".$this->vermelha."'>Não Apto(a)</span>";
                }
            }
        }

        private function seJaFezPagamento($todos){
            if((int)$this->precoBoletim<=0){
                return true;
            }else{
                $array = listarItensObjecto($todos, "pagamentos", ["idHistoricoAno=".$this->idPAno, "referenciaPagamento=".$this->trimestre, "idHistoricoEscola=".$_SESSION['idEscolaLogada'], "codigoEmolumento=boletim"]);


                if(count($array)>0){

                    $this->editarItemObjecto("alunos_".$todos["grupo"], "pagamentos", "estadoPagamento", ["A"], ["idPMatricula"=>valorArray($todos, "idPMatricula")], ["idPHistoricoConta"=>valorArray($array, "idPHistoricoConta")]);
                    
                    $this->editarItemObjecto("payments", "itens", "estadoItem", ["A"], ["identificadorCliente"=>valorArray($todos, "idPMatricula")], ["idPHistoricoConta"=>valorArray($array, "idPHistoricoConta")]);

                    return true;
                }else{
                    return false;
                }
            }
        }

    }

new boletins(__DIR__);
  
?>