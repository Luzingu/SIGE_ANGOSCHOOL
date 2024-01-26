<?php 
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    
    include_once ('../funcoesAuxiliares.php');
    include_once ('../funcoesAuxiliaresDb.php');

    class certificadoDisciplina extends funcoesAuxiliares{

        function __construct($caminhoAbsoluto){
            parent::__construct("Rel-Plano Curicular");  

            $this->classe = isset($_GET["classe"])?$_GET["classe"]:"";
            $this->idPCurso = isset($_GET["idPCurso"])?$_GET["idPCurso"]:"";
            $this->periodo = isset($_GET["periodo"])?$_GET["periodo"]:"";
            $this->nomeCurso();
           if($this->verificacaoAcesso->verificarAcessoAlteracao(["aDirectoria"], "tratDeclaracao", [12, $this->idPCurso])){
                $this->certificado();
           }else{
                $this->negarAcesso();
           }

        }

        public function certificado(){

            

            $this->html .="<html style='margin:0px; margin-left:15px; margin-right:15px;'>
            <head>
                <title>Plano Curicular</title>
                <style>
                    table tr td{
                        padding:2px;
                    }
                    p{
                        font-size:13pt;
                    }
                </style>
            </head>
            <body>".$this->fundoDocumento("../../")."
                <p style='".$this->text_center.$this->bolder.$this->miniParagrafo."'>PLANO CURRICULAR</p><br/>";
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
                }else{
                    $this->html .="<p style='".$this->maiuscula.$this->miniParagrafo."'>CLASSE: <strong>".$this->classeExt."</strong></p>";
                }
                $this->html .="<p style='".$this->maiuscula.$this->miniParagrafo."'>PERÍODO: <strong>".periodoExtenso($this->periodo)."</strong></p><br/><br/>";

                if($this->classe<=9){
                    $this->arrayDisciplinas = $this->disciplinas("", $this->classe, $this->periodo);
                }else{
                    $this->arrayDisciplinas = $this->disciplinas($this->idPCurso, "", $this->periodo);
                }

                $arrayClasses=array();
                if($this->classe<=6){
                    for($i=1; $i<=6; $i++){
                        $arrayClasses[]=$i;
                    }
                }else if($this->classe<=9){
                    for($i=7; $i<=9; $i++){
                        $arrayClasses[]=$i;
                    }
                }else{
                    for($i=10; $i<=(9+$this->duracaoCurso); $i++){
                        $arrayClasses[]=$i;
                    }
                }

                $this->html .="
                <table style='".$this->tabela."width:100%;'>
                <tr style='".$this->corDanger."'><td style='".$this->border().$this->text_center.$this->bolder."'>Disciplinas</td>";
                foreach($arrayClasses as $a){
                    $this->html .="<td style='".$this->border().$this->text_center.$this->bolder." width:50px;'>".$a.".ª</td>";
                }
                $this->html .="</tr>";

                if($this->classe>=10){
                    foreach(distinct2($this->arrayDisciplinas, "tipoDisciplina") as $tipo){


                        $this->html .="<tr><td style='".$this->bolder.$this->border()."' colspan='".(1+count($arrayClasses))."'>".$tipo."</td></tr>";

                        foreach(array_filter($this->arrayDisciplinas, function($mamale) use ($tipo){
                            return $mamale["tipoDisciplina"]==$tipo;
                        }) as $id){
                            $this->html .="<tr><td style='".$this->border()."'>".$this->selectUmElemento("nomedisciplinas", "nomeDisciplina", ["idPNomeDisciplina"=>$id["idPNomeDisciplina"]])."</td>";
                            foreach($arrayClasses as $a){
                                $this->html .="<td style='".$this->border().$this->text_center.$this->bolder."'>".$this->seTemDisciplina($id["idPNomeDisciplina"], $a)."</td>";
                            }

                            $this->html .="</tr>";
                        }
                    }
                }else{
                    foreach(distinct2($this->arrayDisciplinas, "idPNomeDisciplina") as $id){

                        $this->html .="<tr><td style='".$this->border()."'>".$this->selectUmElemento("nomedisciplinas", "nomeDisciplina", ["idPNomeDisciplina"=>$id["idPNomeDisciplina"]])."</td>";
                        foreach($arrayClasses as $a){
                            $this->html .="<td style='".$this->border().$this->text_center.$this->bolder."'>".$this->seTemDisciplina($id["idPNomeDisciplina"], $a)."</td>";
                        }

                        $this->html .="</tr>";
                    }
                }

               $this->html .="</table></body></html>";

            $this->exibir("","Plano Curricular-".$this->nomeCursoAbr);
        }

        private function seTemDisciplina($idPNomeDisciplina, $classeDisciplina){
            $retorno ="";
            foreach($this->arrayDisciplinas as $aray){
                if($aray["classeDisciplina"]==$classeDisciplina && $aray["idPNomeDisciplina"]==$idPNomeDisciplina){
                    $retorno ="X";
                    break;
                }
            }
            return $retorno;
        }

    }
    new certificadoDisciplina(__DIR__);
?>