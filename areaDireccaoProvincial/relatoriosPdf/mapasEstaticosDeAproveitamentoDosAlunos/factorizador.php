<?php 
	class factorizador{

		function factorCursoClasseTurma($idPEscola, $classeAnalizador, $idPCurso, $classe, $turma, $tituloDb, $genero){

			//Para os matriculados que não dependem a nenhum factor para o cáculo de percentagemm
	        $valorMaximoRetorno = $classeAnalizador->quanto($idPEscola, $idPCurso, $classe, "TOT", $tituloDb, "TOT");

	        //Valor Máximo sem o condicionador genero
	         $valorMaximoRetornoNaLinha = $classeAnalizador->quanto($idPEscola, $idPCurso, $classe, $turma, $tituloDb, "TOT");

	        //Valor Retornado  com todos condicionadores...
	        $valorRetorno = $classeAnalizador->quanto($idPEscola, $idPCurso, $classe, $turma, $tituloDb, $genero);

	        if($tituloDb=="matriculados" && $genero=="TOT"){
	        	//para não ir buscar sempre o tot de matriculados
	            $this->totalMatriculados = $valorMaximoRetornoNaLinha+$classeAnalizador->quanto($idPEscola, $idPCurso, $classe, $turma, "transfEntrada", "TOT");
	        }
	        if($tituloDb=="avaliados" && $genero=="TOT"){
	        	//Para não ir buscar sempre o tot de matriculados
	            $this->totalAvaliados = $valorMaximoRetornoNaLinha;
	        }

	        $valorFinal=$valorRetorno;
	        if($genero=="%"){
	            if($tituloDb=="matriculados"){
	                    $valorFinal = $classeAnalizador->percentagem($valorMaximoRetorno, $valorMaximoRetornoNaLinha);
	            }else if($tituloDb=="desistentes" || $tituloDb=="avaliados" || $tituloDb=="naoAvaliados"){
	                    $valorFinal = $classeAnalizador->percentagem($this->totalMatriculados, $valorMaximoRetornoNaLinha);
	            }else if($tituloDb=="aprovados" || $tituloDb=="reprovados"){
	                $valorFinal = $classeAnalizador->percentagem($this->totalAvaliados, $valorMaximoRetornoNaLinha);
	            }else if($tituloDb=="transfSaida" || $tituloDb=="transfEntrada"){

	            	$luzinguLuame = $classeAnalizador->quanto($idPEscola, $idPCurso, $classe, $turma, "matriculados", "TOT");

	                $valorFinal = $classeAnalizador->percentagem($luzinguLuame, $valorMaximoRetornoNaLinha);
	            }
	        }else{
	            $valorFinal = $valorRetorno;
	        }
	        return $valorFinal;
		}

		function factorCursoClasse($idPEscola, $classeAnalizador, $idPCurso, $classe, $turma, $tituloDb, $genero){

			//Para os matriculados que não dependem a nenhum factor para o cáculo de percentagemm
	        $valorMaximoRetorno = $classeAnalizador->quanto($idPEscola, $idPCurso, "TOT", "TOT", $tituloDb, "TOT");

	        //Valor Máximo sem o condicionador genero
	         $valorMaximoRetornoNaLinha = $classeAnalizador->quanto($idPEscola, $idPCurso, $classe, "TOT", $tituloDb, "TOT");

	        //Valor Retornado  com todos condicionadores...
	        $valorRetorno = $classeAnalizador->quanto($idPEscola, $idPCurso, $classe, "TOT", $tituloDb, $genero);

	        if($tituloDb=="matriculados" && $genero=="TOT"){
	                $this->totalMatriculados = $valorMaximoRetornoNaLinha+$classeAnalizador->quanto($idPEscola, $idPCurso, $classe, "TOT", "transfEntrada", "TOT");
	        }
	        if($tituloDb=="avaliados" && $genero=="TOT"){
	                $this->totalAvaliados = $valorMaximoRetornoNaLinha;
	        }

	        $valorFinal=$valorRetorno;
	        if($genero=="%"){
	            if($tituloDb=="matriculados"){
	                    $valorFinal = $classeAnalizador->percentagem($valorMaximoRetorno, $valorMaximoRetornoNaLinha);
	            }else if($tituloDb=="desistentes" || $tituloDb=="avaliados" || $tituloDb=="naoAvaliados"){
	                 
	                 $valorFinal = $classeAnalizador->percentagem($this->totalMatriculados, $valorMaximoRetornoNaLinha);

	            }else if($tituloDb=="aprovados" || $tituloDb=="reprovados"){
	                $valorFinal = $classeAnalizador->percentagem($this->totalAvaliados, $valorMaximoRetornoNaLinha);
	            }else if($tituloDb=="transfSaida" || $tituloDb=="transfEntrada"){

	            	$luzinguLuame = $classeAnalizador->quanto($idPEscola, $idPCurso, $classe, "TOT", "matriculados", "TOT");

	                $valorFinal = $classeAnalizador->percentagem($luzinguLuame, $valorMaximoRetornoNaLinha);
	            }
	        }else{
	            $valorFinal = $valorRetorno;
	        }
	        return $valorFinal;
		}

		function factorCurso($idPEscola, $classeAnalizador, $idPCurso, $classe, $turma, $tituloDb, $genero){

			//Para os matriculados que não dependem a nenhum factor para o cáculo de percentagemm
	        $valorMaximoRetorno = $classeAnalizador->quanto($idPEscola, $idPCurso, "TOT", "TOT", $tituloDb, "TOT");


	        //Valor Máximo sem o condicionador genero
	         $valorMaximoRetornoNaLinha = $classeAnalizador->quanto($idPEscola, $idPCurso, "TOT", "TOT", $tituloDb, "TOT");

	        //Valor Retornado  com todos condicionadores...
	        $valorRetorno = $classeAnalizador->quanto($idPEscola, $idPCurso, "TOT", "TOT", $tituloDb, $genero);

	        if($tituloDb=="matriculados" && $genero=="TOT"){
	                $this->totalMatriculados = $valorMaximoRetornoNaLinha+$classeAnalizador->quanto($idPEscola, $idPCurso, "TOT", "TOT", "transfEntrada", "TOT");
	        }
	        if($tituloDb=="avaliados" && $genero=="TOT"){
	                $this->totalAvaliados = $valorMaximoRetornoNaLinha;
	        }

	        $valorFinal=$valorRetorno;
	        if($genero=="%"){
	            if($tituloDb=="matriculados"){
	                    $valorFinal = $classeAnalizador->percentagem($valorMaximoRetorno, $valorMaximoRetornoNaLinha);
	            }else if($tituloDb=="desistentes" || $tituloDb=="avaliados" || $tituloDb=="naoAvaliados"){
	                    $valorFinal = $classeAnalizador->percentagem($this->totalMatriculados, $valorMaximoRetornoNaLinha);
	            }else if($tituloDb=="aprovados" || $tituloDb=="reprovados"){
	                $valorFinal = $classeAnalizador->percentagem($this->totalAvaliados, $valorMaximoRetornoNaLinha);
	            }else if($tituloDb=="transfSaida" || $tituloDb=="transfEntrada"){

	            	$luzinguLuame = $classeAnalizador->quanto($idPEscola, $idPCurso, "TOT", "TOT", "matriculados", "TOT");

	                $valorFinal = $classeAnalizador->percentagem($luzinguLuame, $valorMaximoRetornoNaLinha);
	            }
	        }else{
	            $valorFinal = $valorRetorno;
	        } 
	        return $valorFinal;
		}

		function factorEscola($idPEscola, $classeAnalizador, $idPCurso, $classe, $turma, $tituloDb, $genero){

			//Para os matriculados que não dependem a nenhum factor para o cáculo de percentagemm
	        $valorMaximoRetorno = $classeAnalizador->quanto($idPEscola, "TOT", "TOT", "TOT", $tituloDb, "TOT");

	        //Valor Máximo sem o condicionador genero
	         $valorMaximoRetornoNaLinha = $classeAnalizador->quanto($idPEscola, $idPCurso, "TOT", "TOT", $tituloDb, "TOT");

	        //Valor Retornado  com todos condicionadores...
	        $valorRetorno = $classeAnalizador->quanto($idPEscola, $idPCurso, "TOT", "TOT", $tituloDb, $genero);

	        if($tituloDb=="matriculados" && $genero=="TOT"){
	            $this->totalMatriculados = $valorMaximoRetornoNaLinha+$classeAnalizador->quanto($idPEscola, "TOT", "TOT", "TOT", "transfEntrada", "TOT");
	        }
	        if($tituloDb=="avaliados" && $genero=="TOT"){
	            $this->totalAvaliados = $valorMaximoRetornoNaLinha;
	        }

	        $valorFinal=$valorRetorno;
	        if($genero=="%"){
	            if($tituloDb=="matriculados"){
	                    $valorFinal = $classeAnalizador->percentagem($valorMaximoRetorno, $valorMaximoRetornoNaLinha);
	            }else if($tituloDb=="transfSaida" || $tituloDb=="desistentes" || $tituloDb=="avaliados" || $tituloDb=="naoAvaliados"){
	                    $valorFinal = $classeAnalizador->percentagem($this->totalMatriculados, $valorMaximoRetornoNaLinha);
	            }else if($tituloDb=="aprovados" || $tituloDb=="reprovados"){
	                $valorFinal = $classeAnalizador->percentagem($this->totalAvaliados, $valorMaximoRetornoNaLinha);
	            }else if($tituloDb=="transfSaida" || $tituloDb=="transfEntrada"){

	            	$luzinguLuame = $classeAnalizador->quanto($idPEscola, "TOT", "TOT", "TOT", "matriculados", "TOT");

	                $valorFinal = $classeAnalizador->percentagem($luzinguLuame, $valorMaximoRetornoNaLinha);
	            }
	        }else{
	            $valorFinal = $valorRetorno;
	        }
	        return $valorFinal;
		}
	}
	


?>