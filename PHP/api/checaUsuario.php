<?php
/*
 * API DE CHECAGEM DE USUARIO
 *
 Saidas:
(202) Usuario valido
(403) Usuario invalido
 */

// Recebe variaveis por Json (Retorna array indexado na variavel $obj)
include "include/json/json_rec.php";

// Valida no banco as informações recebidas
include "include/db/connect.php";

session_start();
if (!(isset($_SESSION['id_user']) )){
	http_response_code(403);
	exit("Usuario nao possui uma sessão valida");
}
else{
	//Parametros de entrada//
	$id = $_SESSION['id_user'];
	
	try {
		//Checa o usuario no banco//
		$stmt = $dbh->prepare("SELECT nome,email FROM usuarios WHERE idusuario= :ID;");

		//Vinculando parametros//
		$stmt->bindParam(":ID", $id);

		//Realmente realiza a execucao da query//
		$stmt->execute();

		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

		//Para cada elemento do resultado//
		foreach ($result as $row) {
				//Responde ao usuario//
				http_response_code(200);
				
				//Sai da execução//
				exit (json_encode($row));
			
		}
	}catch (Exception $e) {
		//Responde ao usuario//
		http_response_code(403);
		
		//Sai da execução//
		exit("Exceção capturada:". $e->getMessage());
	}
	
}
?>