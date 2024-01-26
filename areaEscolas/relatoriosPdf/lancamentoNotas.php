<?php 
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    
    include_once ('../funcoesAuxiliares.php');
    include_once ('../funcoesAuxiliaresDb.php');

    class relatorio extends funcoesAuxiliares{
        function __construct($caminhoAbsoluto){
            parent::__construct("Rel-Control de Laçamento de Notas");  

            $this->idPCurso = isset($_GET["idPCurso"])?$_GET["idPCurso"]:"";
            $this->classe = isset($_GET["classe"])?$_GET["classe"]:"";
            $this->trimestre = isset($_GET["trimestre"])?$_GET["trimestre"]:"";
            if($this->trimestre=="IV"){
                $this->trimestreExtensa="EXTENSA";
            }else{
                $this->trimestreExtensa=$this->trimestre." TRIMESTRE";
            }
            $this->pautador=array();
            $this->numAno();
            $this->nomeCurso();

           if($this->verificacaoAcesso->verificarAcessoAlteracao(["aPedagogica"], "", [])){
                $this->visualizar();
           }else{
                $this->negarAcesso();
           }
        }

        public function visualizar(){

            $this->html .="<html style='margin:0px; margin-left:15px; margin-right:30px; margin-top:25px; margin-left:30px;'>
            <head>
                <title>Controlo de Lançamento de Notas</title>
            </head>
            <body>".$this->fundoDocumento("../../").$this->cabecalho()." 
            
            <p  style='".$this->text_center.$this->bolder.$this->maiuscula."'>CONTROLO DE LANÇAMENTO DE NOTAS</p>";
            if($this->classe>=10){
                if($this->tipoCurso=="pedagogico"){
                    $this->html .="<p style='".$this->miniParagrafo.$this->maiuscula."'>CURSO: <strong>".$this->areaFormacaoCurso."</strong></p>
                    <p style='".$this->miniParagrafo.$this->maiuscula."'>OPÇÃO: <strong>".$this->nomeCurso."</strong></p>";
                }else if($this->tipoCurso=="tecnico"){
                    $this->html .="<p style='".$this->miniParagrafo.$this->maiuscula."'>ÁREA DE FORMAÇÃO: <strong>".$this->areaFormacaoCurso."</strong></p>
                    <p style='".$this->miniParagrafo.$this->maiuscula."'>CURSO: <strong>".$this->nomeCurso."</strong></p>";
                }else{
                    $this->html .="<p style='".$this->miniParagrafo.$this->maiuscula."'>CURSO: <strong>".$this->nomeCurso."</strong></p>";
                }  
            }
            $this->html .="
            <p style='".$this->miniParagrafo.$this->maiuscula."'>CLASSE: <strong>".$this->classeExt."</strong></p>
            <p style='".$this->miniParagrafo.$this->maiuscula."'>PERÍODO: <strong>".$this->trimestreExtensa."</strong></p><br/><br/>";

        $listaTurmas =  array_filter(turmasEscola($this), function ($mamale){
            return ($mamale["classe"]==$this->classe && ($mamale["idPNomeCurso"]==$this->idPCurso || $this->classe<=9));
        });

        $condicao =  ["disciplinas.idDiscEscola"=>$_SESSION['idEscolaLogada'], "disciplinas.classeDisciplina"=>$this->classe, "disciplinas.estadoDisciplina"=>"A"];
        if($this->classe>=10){
            $condicao["disciplinas.idDiscCurso"]=$this->idPCurso;
        }
        $disciplinas=array();
        foreach($this->selectDistinct("nomedisciplinas", "idPNomeDisciplina", $condicao, ["disciplinas"]) as $disciplina){
            
            $nomeDisciplina = $this->selectUmElemento("nomedisciplinas", "abreviacaoDisciplina2", ["idPNomeDisciplina"=>$disciplina["_id"]]);
            $disciplinas[]=["idPNomeDisciplina"=>$disciplina["_id"], "nomeDisciplina"=>$nomeDisciplina];
        }


        $this->html .="<table style='".$this->tabela."'><tr style='".$this->corDanger.$this->bolder."'><td style='".$this->border()."'>Turmas</td>";

        

        foreach ($disciplinas as $tur) {
            $this->html .="<td style='".$this->border().$this->text_center."'>".$tur["designacaoTurma"]."</td>";

            $pautasTurma = $this->alunosPorTurma($this->idPCurso, $this->classe, $turma["nomeTurma"], $this->idAnoActual, array(), ["pautas.".$campo, "pautas.idPautaDisciplina"], ["pautas"], $condicaoAdicional);           
        }
        $this->html .="</tr>";

        
        $condicaoAdicional = ["pautas.classePauta"=>$classe];
        if($this->classe>=10){
            $condicaoAdicional["pautas.idPautaCurso"]=$this->idPCurso;
        }
        if($this->trimestre=="I" || $this->trimestre=="II" || $this->trimestre=="III"){
            $campo="mt".$this->trimestre;
        }else{
            $campo="exame";
        }


        foreach($this->selectDistinct("nomedisciplinas", "idPNomeDisciplina", $condicao)  as $disciplina){

            $this->html .="<tr><td style=".$this->border().">".$this->selectUmElemento("nomedisciplinas", "nomeDisciplina", ["idPNomeDisciplina"=>$disciplina["_id"]])."</td>";
            foreach ($listaTurmas as $tur) {
              $this->html .="<td style='".$this->border().$this->text_center."'>".$this->seJaLancou($tur["nomeTurma"], $disciplina["_id"], $tur["periodoTurma"], $this->pautador[$tur["nomeTurma"]])."</td>";
            }
            $this->html .="</tr>";
        }
        $this->html .="</table>
        <p style='".$this->maiuscula.$this->text_center."'>".$this->rodape()."</p>".$this->assinaturaDirigentes(8)."
        
        </body></html>";

        //$this->exibir("", "Controlo de Lançamento de Notas-".$this->nomeCurso."-".$this->classe."-".$this->trimestreExtensa, "", "A4", "landscape");
        }

        function seJaLancou($turma, $idPNomeDisciplina, $periodoTurma, $pautador){

            $condicaoNota="mtI";
            if($this->trimestre=="II"){
                $condicaoNota="mtII";
            }else if($this->trimestre=="III"){
                $condicaoNota="mtIII";
            }else if($this->trimestre=="IV"){
                $condicaoNota="exame";
            }

            $arrayAlunos=array();
            foreach(array_filter($pautador, function($mamale) use ($idPNomeDisciplina){
              return $mamale["idPautaDisciplina"]==$idPNomeDisciplina;
            }) as $nota){
              if(!($nota["avaliacao_anual"]["observacaoF"]=="D" || $nota["avaliacao_anual"]["observacaoF"]=="N" || $nota["avaliacao_anual"]["observacaoF"]=="N" || $nota["avaliacao_anual"]["observacaoF"]=="F")){
                $arrayAlunos[]=$nota;
              }
            }
            $totAlunos = count($arrayAlunos);
            $totLancado = $this->contarDados($arrayAlunos, $condicaoNota);

            if($totLancado>($totAlunos*0.5)){
                return "<img src='".$_SERVER['DOCUMENT_ROOT']."/angoschool/icones/Good.png' style='width:12px; height:12px; margin-bottom:-30px !important;'>";
            }else{
                if($this->classe<=4){
                  $divisaoProfessor =$this->selectArray("divisaoprofessores", ["classe"=>$this->classe, "idPEscola"=>$_SESSION["idEscolaLogada"], "idDivAno"=>$this->idAnoActual, "nomeTurmaDiv"=>$turma]);
                }else{
                  $divisaoProfessor =$this->selectCondClasseCurso("array", "divisaoprofessores", [], ["classe"=>$this->classe, "idPEscola"=>$_SESSION["idEscolaLogada"], "idDivAno"=>$this->idAnoActual, "nomeTurmaDiv"=>$turma, "idPNomeDisciplina"=>$idPNomeDisciplina], $this->classe, ["idPNomeCurso"=>$this->idPCurso]);
                }
                return "<img src='".$_SERVER['DOCUMENT_ROOT']."/angoschool/icones/Error.png' style='width:12px;'><br/><span style='font-size:8pt;'>".valorArray($divisaoProfessor, "nomeEntidade")."</span>";
            }      
      }
      
      function contarDados($array, $object){
        $contador=0;
        foreach($array as $a){
          if(isset($a[$object]) && $a[$object]!=NULL && $a[$object]!=""){
            $contador++;
          }
        }
        return $contador;
      }
    }
    new relatorio(__DIR__);
?>