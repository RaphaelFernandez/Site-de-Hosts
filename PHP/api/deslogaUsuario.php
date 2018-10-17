<?php
/*
 * API DE LOGOUT DE USUARIO
 *
 Saidas:
(200) Usuario deslogado com sucesso
 */
	//Inicia a sessão//
	session_start();
	
	//Checa se a sessao possui um id de usuario e o deleta//
	if(isset($_SESSION['id_user'])){
		
		//Deleta id de usuario da sessão//
		unset($_SESSION['id_user']);
		
		//Responde ao usuario//
		http_response_code(202);
		
		//Sai da execução//
		exit("Deslogado com sucesso");
	}

	exit();
?>