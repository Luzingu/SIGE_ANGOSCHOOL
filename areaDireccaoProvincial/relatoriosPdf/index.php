<?php 
	if(session_status()!==PHP_SESSION_ACTIVE){
      session_start();
    }
	if($_SESSION["tipoUsuario"]=="aluno"){
		echo "<script>window.location ='../areaAluno'</script>";
	}else if($_SESSION["cargoUsuarioLogado"]=="Coordenador de Pais e Encarregados de Educação"){
		echo "<script>window.location ='../areaCoordPaisEncarregado'</script>";
	}else{
		echo "<script>window.location ='../areaProfessor'</script>";	
	}
?>