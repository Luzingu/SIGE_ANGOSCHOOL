<?php 
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
    include_once ('../../funcoesAuxiliares.php');
    include_once ('../../funcoesAuxiliaresDb.php');

    class mapaForcaTrabalho extends funcoesAuxiliares{

        function __construct($caminhoAbsoluto){

            parent::__construct("Rel-Mapa de Control de Professores por Disciplina");
            $this->idPAno = $this->idAnoActual;
            $this->numAno();
            $this->tamanhoFolha = isset($_GET["tamanhoFolha"])?$_GET["tamanhoFolha"]:"A3";

            $this->html="<html>
            <head>
                <title>Mapa de Controlo de Professores por Disciplina, Nacionalidade e Carga Horária</title>
                <style>
                    table tr td{
                        padding:3px;
                    }
                </style>
            </head>
            <body>";


            if($this->verificacaoAcesso->verificarAcesso("", ["listaAgentes"], [], "")){                  
                $this->mapa();
            }else{
              $this->negarAcesso();
            }
        }

         private function mapa(){

           $this->html .="<div class='cabecalho'>
            <div><div style='margin-top:20px; width:400px; position:absolute;".$this->maiuscula."'>".$this->assinaturaDirigentes(7)."</div></div>".$this->cabecalho()."
            </div>
            </div>
            <p  style='".$this->text_center.$this->bolder."'>MAPA DE CONTROLO DE PROFESSORES POR DISCIPLINA, NACIONALIDADE E CARGA HORÁRIA</p>";

            $this->html .="<table style='width:100%;".$this->tabela."'>
                <tr style='".$this->corDanger.$this->bolder."'>
                    <td style='".$this->text_center.$this->border()."' rowspan='3'>Disciplina que Lecciona</td><td colspan='10' style='".$this->text_center.$this->border()."'>Grau Acadêmico</td><td style='".$this->text_center.$this->border()."' colspan='3' rowspan='2'>Sub-Total</td><td style='".$this->text_center.$this->border()."' rowspan='3'>Nacionalidade</td><td style='".$this->text_center.$this->border()."' rowspan='3'>Tempos/<br/>Semana</td><td colspan='2' style='".$this->text_center.$this->border()."' rowspan='2'>Condição</td><td style='".$this->text_center.$this->border()."' rowspan='3'>Escola que<br/>pertence</td>
                </tr>
                <tr style='".$this->corDanger.$this->bolder."'><td style='".$this->text_center.$this->border()."' colspan='2'>Médio</td><td style='".$this->text_center.$this->border()."' colspan='2'>Bacharel</td><td style='".$this->text_center.$this->border()."' colspan='2'>Licenciado</td><td style='".$this->text_center.$this->border()."' colspan='2'>Mestre</td><td style='".$this->text_center.$this->border()."' colspan='2'>Doutor</td></tr>
                <tr style='".$this->corDanger.$this->bolder."'>
                    <td style='".$this->text_center.$this->border()."'>M</td> <td style='".$this->text_center.$this->border()."'>F</td>
                    <td style='".$this->text_center.$this->border()."'>M</td> <td style='".$this->text_center.$this->border()."'>F</td>
                    <td style='".$this->text_center.$this->border()."'>M</td> <td style='".$this->text_center.$this->border()."'>F</td>
                    <td style='".$this->text_center.$this->border()."'>M</td> <td style='".$this->text_center.$this->border()."'>F</td>
                    <td style='".$this->text_center.$this->border()."'>M</td> <td style='".$this->text_center.$this->border()."'>F</td>
                    <td style='".$this->text_center.$this->border()."'>M</td> <td style='".$this->text_center.$this->border()."'>F</td><td style='".$this->text_center.$this->border()."'>MF</td><td style='".$this->text_center.$this->border()."'>Efectivo</td><td style='".$this->text_center.$this->border()."'>Colabo<br/>rador</td>
                </tr>";

            $nomeDisciplinas = $this->selectArray("nomedisciplinas", ["idPNomeDisciplina", "nomeDisciplina"]);
            $listaDisciplinas = array();
            foreach ($this->selectDistinct("nomedisciplinas", "idPNomeDisciplina", ["disciplinas.idDiscEscola"=>$_SESSION['idEscolaLogada'], "disciplinas.estadoDisciplina"=>"A"], ["disciplinas"]) as $disciplina) { 

                $luzingu="";
                foreach($nomeDisciplinas as $ok){
                    if($ok["idPNomeDisciplina"]==$disciplina["_id"]){
                        $luzingu = $ok["nomeDisciplina"];
                        break;
                    }
                }
                $array =array_filter($nomeDisciplinas, function ($mamale) use ($disciplina){
                    return $mamale["idPNomeDisciplina"]==$disciplina["_id"];
                });
                $listaDisciplinas [] = array("idPNomeDisciplina"=>$disciplina["_id"], "nomeDisciplina"=>$luzingu);
            }
            $cargos[]="Médio";
            $cargos[]="Bacharel";
            $cargos[]="Licenciado";
            $cargos[]="Mestre";
            $cargos[]="Doutor";

            $i=0;
            foreach ($listaDisciplinas as $lista) {
                $i++;
                if($i%2==0){
                   $this->html .="<tr style='".$this->backGround("rgb(220,220,220)")."'>";
                }else{
                    $this->html .="<tr>";
                }

                $this->html .="<td style='".$this->border()."'>".$lista["nomeDisciplina"]."</td>";

                foreach ($cargos as $c) {

                   $this->numeroProfessores($lista["idPNomeDisciplina"], $c, "M", "idDivEntidade");
                    $this->html .="<td style='".$this->text_center.$this->border()."'>".completarNumero($this->contador)."</td>";
                    $this->numeroProfessores($lista["idPNomeDisciplina"], $c, "F", "idDivEntidade");
                    $this->html .="<td style='".$this->text_center.$this->border()."'>".completarNumero($this->contador)."</td>";
                }

                $this->numeroProfessores($lista["idPNomeDisciplina"], "", "M", "idDivEntidade");
                $this->html .="<td style='".$this->text_center.$this->border()."'>".completarNumero($this->contador)."</td>";
                $this->numeroProfessores($lista["idPNomeDisciplina"], "", "F", "idDivEntidade");
                $this->html .="<td style='".$this->text_center.$this->border()."'>".completarNumero($this->contador)."</td>";

                $this->numeroProfessores($lista["idPNomeDisciplina"], "", "", "idDivEntidade");
                $this->html .="<td style='".$this->text_center.$this->border()."'>".completarNumero($this->contador)."</td>";

                $this->numeroProfessores($lista["idPNomeDisciplina"], "", "", "paisNascEntidade");
                $this->html .="<td style='".$this->text_center.$this->border()."'>".$this->acumulador."</td>";

                 $this->tempoSemanal($lista["idPNomeDisciplina"]);
                $this->html .="<td style='".$this->text_center.$this->border()."'>".$this->contador."</td>";

                $this->numeroProfessores($lista["idPNomeDisciplina"], "", "", "naturezaVinc");
                $this->html .="<td style='".$this->text_center.$this->border()."'>".$this->acumulador."</td>";

                $this->html .="<td style='".$this->text_center.$this->border()."'></td>";

                $this->html .="<td style='".$this->maiuscula.$this->text_center.$this->border()."'>".valorArray($this->sobreUsuarioLogado, "abrevNomeEscola")." ".valorArray($this->sobreUsuarioLogado, "municipio")."</td>";


                $this->html .="</tr>";
            }



            $this->html .="</table><div style='margin-top:20px;'><p style='".$this->maiuscula."' style='font-size:16pt;'>".$this->rodape()."</p><br/><br/><div style='".$this->maiuscula."'>".$this->assinaturaDirigentes("mengi")."</div><div>";
            

            $this->exibir("", "Mapa de Controlo de Professores por Disciplina, Nacionalidade e Carga Horária-".$this->numAno, "", $this->tamanhoFolha, "landscape");
        }
        private function numeroProfessores($idPNomeDisciplina, $nivel, $genero, $campo){
            $this->contador=0;
            $this->acumulador="";

            /*$condicao="";
            if($nivel!=""){
                $condicao .=" AND nivelAcademicoEntidade='".$nivel."'";
            }
            if($idPNomeDisciplina!=""){
                $condicao .=" AND idDivDisciplina=".$idPNomeDisciplina;
            }

            if($genero!=""){
                $condicao .=" AND generoEntidade='".$genero."'";
            }

            foreach ($this->selectArray("divisaoprofessores LEFT JOIN entidadesprimaria ON idPEntidade=idDivEntidade LEFT JOIN dadosadicionasentidade ON idDAEntidade=idPEntidade LEFT JOIN entidade_escola ON idPEntidade=idFEntidade", "DISTINCT ".$campo, "idDivEscola=:idDivEscola AND idEntidadeEscola=idDivEscola AND idDivEntidade IS NOT NULL AND idDivAno=:idDivAno AND estadoActividadeEntidade=:estadoActividadeEntidade".$condicao, [$_SESSION["idEscolaLogada"], $this->idPAno, "A"]) as $divisao) {
                $this->contador++;
                if($this->acumulador==""){
                    $this->acumulador .= retornarNacionalidade($divisao->$campo);
                }else if($this->acumulador==""){
                    $this->acumulador .= ", ".retornarNacionalidade($divisao->$campo);
                }
            }*/
            $this->acumulador="";
        }

        private function tempoSemanal($idPNomeDisciplina){
            $this->contador=0;
            $this->acumulador="";

            foreach ($this->selectArray("horario", [], ["idPEscola"=>$_SESSION["idEscolaLogada"], "idHorAno"=>$this->idPAno, "idPNomeDisciplina"=>$idPNomeDisciplina]) as $divisao) {
                $this->contador++;
            }
        }
    }

new mapaForcaTrabalho(__DIR__);
    
    
  
?>