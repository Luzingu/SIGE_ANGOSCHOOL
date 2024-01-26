<?php 
	if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
	if($_SESSION["tipoUsuario"]=="aluno"){
		echo "<script>window.location ='../areaAluno'</script>";
	}else{
		echo "<script>window.location ='../areaProfessor'</script>";		
	}
?>

