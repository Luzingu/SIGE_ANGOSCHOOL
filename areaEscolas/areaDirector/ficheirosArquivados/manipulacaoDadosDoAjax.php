<?php 
	session_start();
	include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/manipulacaoDadosDoAjax.php';
    
    class manipulacaoDadosDoAjaxInterno extends manipulacaoDadosAjax{
    	private $nomeCurso = "";
        private $idCursoCoordenador = "";
        private $idPCurso = "";
        private $estadoCurso = "";
        private $duracao =0;
        private $numeroCpp=0;

    	function __construct($caminhoAbsoluto){
    		parent::__construct();
            if($this->accao=="abrirLink"){
                $this->abrirLink();
            }
    	}

        private function abrirLink(){
            $idPAno = isset($_GET["idPAno"])?$_GET["idPAno"]:"";
            $luzingu = isset($_GET["luzingu"])?$_GET["luzingu"]:"";
            $trimestreReferencia = isset($_GET["trimestreReferencia"])?$_GET["trimestreReferencia"]:"";
            $trimestreReferencia = isset($_GET["trimestreReferencia"])?$_GET["trimestreReferencia"]:"";
            $referencia = isset($_GET["referencia"])?$_GET["referencia"]:"";

            $piter = explode("-", $luzingu);
            $turma=$piter[0];
            $classe=$piter[1];
            $idPCurso=$piter[2];

            $folder = "";
            $file = "";
            if($referencia=="listaTurmas"){
                $folder = "Lista_Turmas";
                $file = "Lista-".$idPCurso."-".$classe."-".$turma."-".$idPAno;
            }else if($referencia=="relacaoNominal"){
                $folder = "Lista_Turmas";
                $file = "Relacao_Nominal-".$idPCurso."-".$classe."-".$turma."-".$idPAno;
            }else if($referencia=="cartoesEstudantes"){

                $folder = "Cartoes_de_Estudantes";
                $file = "Cartoes-".$idPCurso."-".$classe."-".$turma."-".$idPAno;
            }else if($referencia=="exemplarMiniPauta"){
                $folder = "Lista_Turmas";
                $file = "Exemplar-Mini-Pautas-".$idPCurso."-".$classe."-".$turma."-".$idPAno;
            }else if($referencia=="mapaControlFaltas"){
                $folder = "Lista_Turmas";
                $file = "Mapa_Controlo_Faltas-".$idPCurso."-".$classe."-".$turma."-".$idPAno;
            }else if($referencia=="mapaControlFaltas2"){
                $folder = "Lista_Turmas";
                $file = "Mapa_Controlo_Faltas2-".$idPCurso."-".$classe."-".$turma."-".$idPAno;
            }else if($referencia=="mapaAvaliacaoAlunos"){
                $folder = "Lista_Turmas";
                $file = "Mapa_Avaliacao-".$idPCurso."-".$classe."-".$turma."-".$idPAno;
            }else if($referencia=="horarioTurma"){
                $folder = "Horarios_Turmas";
                $file = "Horario_Turma-".$idPCurso."-".$classe."-".$turma."-".$idPAno;
            }else if($referencia=="boletins"){
                $folder = "Boletins";
                $file = "Boletins-".$idPCurso."-".$classe."-".$turma."-".$trimestreReferencia."-".$idPAno;
            }else if($referencia=="pautaGeral"){
                $folder = "Pautas";
               $file = "Pauta_Geral-Mod1-".$idPCurso."-".$classe."-".$turma."-".$trimestreReferencia."-".$idPAno;
            }else if($referencia=="aproveitamentoGeral"){
                $folder = "Pautas";
               $file = "Pauta_Geral-Mod2-".$idPCurso."-".$classe."-".$turma."-IV-".$idPAno;
            }else if($referencia=="resumoNotas"){
                $folder = "Pautas";
               $file = "Resumo_Notas-".$idPCurso."-".$classe."-".$turma."-".$idPAno;
            }else if($referencia=="mapaAproveitamentoCurso"){
                $folder = "Estatisticas";
               $file = "Mapa_de_Aproveitamento_Geral_por_Curso-".$idPCurso."-".$trimestreReferencia."-".$idPAno;
            }else if($referencia=="mapaAproveitamentoGeral"){
                $folder = "Estatisticas";
               $file = "Mapa_de_Aproveitamento_Geral-".$trimestreReferencia."-".$idPAno;
            }else if($referencia=="mapaAproveitamentoPorDisciplina"){
                $folder = "Estatisticas";
               $file = "Mapa_de_Aproveitamento_por_Disciplina-".$trimestreReferencia."-".$idPAno;
            }else if($referencia=="mapaAproveitamentoPorDisciplinaCurso"){
                $folder = "Estatisticas";
               $file = "Mapa_de_Aproveitamento_por_Disciplina_por_Curso-".$idPCurso."-".$trimestreReferencia."-".$idPAno;
            }else if($referencia=="alunosTransitaramDeficiencia"){
                $folder = "Estatisticas";
               $file = "Resumo_de_Alunos_que_transitaram_com_deficiencia-".$idPCurso."-".$idPAno;
            }else if($referencia=="alunosSubmetidosAoRecurso"){
                $folder = "Estatisticas";
               $file = "Resumo_de_Alunos_a_serem_submetidos_a_exame_de_recurso-".$idPCurso."-".$idPAno;
            }else if($referencia=="resumoMatriculas"){
                $folder = "Estatisticas";
               $file = "Resumo_Matricula-".$idPAno;
            }else if($referencia=="mapaFrequencias"){
                $folder = "Estatisticas";
               $file = "Mapa_Frequencias-".$idPAno;
            }else if($referencia=="mapaFrequenciasPorTurma"){
                $folder = "Estatisticas";
               $file = "Mapa_Frequencias_por_Turma-".$idPAno;
            }else if($referencia=="estisticaDeAlunosRepetentes"){
                $folder = "Estatisticas";
               $file = "Estatitica_Alunos_Repetentes-".$idPAno;
            }
            
            if(file_exists($this->caminhoRetornar."Ficheiros/Escola_".$_SESSION['idEscolaLogada']."/".$idPAno."/".$folder."/".$file.".pdf")){
                echo $this->caminhoRetornar."Ficheiros/Escola_".$_SESSION['idEscolaLogada']."/".$idPAno."/".$folder."/".$file.".pdf";
            }
        }

    }
    new manipulacaoDadosDoAjaxInterno(__DIR__);
?>