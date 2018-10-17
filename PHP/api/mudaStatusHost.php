<?php
/*
 * API DE CHECAGEM DE HOSTS DO USUARIO
 *
 * Parametros esperados de entrada:
 *      * Name

 *  exemplo json esperado
 *
 *
{
	"name":"Meu Docker1"
}
 Saidas:
(200) Muda o valor do status do host
(403) Erro na conexão do banco ou usuario possui dois hosts iguais
 */

//Recebe variaveis por Json (Retorna array indexado na variavel $obj)//
include "include/json/json_rec.php";

//Valida no banco as informações recebidas//
include "include/db/connect.php";
	
	
	//Inicia Sessão//
	session_start();
	
	//Parametros de entrada//
	$id = $_SESSION['id_user'];
	$name = $obj["name"];
	
	//Flag de Status//
	$status=0;
	
	//Procura o Id do host//
	try {
		//Verifica se o host é daquele usuario//
		$stmt = $dbh->prepare("SELECT idhost,status FROM hosts WHERE id_usu= :ID and nome=:NOME");

		//Vinculando parametros//
		$stmt->bindParam(":ID", $id);
		$stmt->bindParam(":NOME", $name);
		
		//Realmente realiza a execucao da query//
		$stmt->execute();
		
		//Quantidade de resultados//
		$qtd_result = $stmt->rowCount();
		
		//Pega todos os resultados//
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		//Checa se somente um resultado é retornado//
		if($qtd_result == 1){
			
			//Inversão da flag de status//
			foreach ($result as $row) {
				$status=$row["status"];
				
				if($status ==0){
					$status=1;
				}
				else{
					$status=0;
				}

				//Inversão do status do host//
				try {
					$stmt = $dbh->prepare("UPDATE hosts SET status=:STATUS WHERE idhost=:IDHOST");
					
					//Vinculando parametros//
					$stmt->bindParam(":STATUS", $status);	
					$stmt->bindParam(":IDHOST", $row["idhost"]);
					
					//Realmente realiza a execucao da query//
					$stmt->execute();
					
					//Responde ao usuario//
					http_response_code(200);
					
					//Sai da execucao//
					exit ($status);
				}catch (Exception $e) {
					http_response_code(403);
					exit("Exceção capturada:". $e->getMessage());
				}
				
				
			}
		}else{
			//Responde ao usuario//
			http_response_code(403);
			
			//Sai da execucao//
			exit("Usuario não possue esse host");
		}		
	}catch (Exception $e) {
		//Responde ao usuario//
		http_response_code(403);
		
		//Sai da execucao//
		exit("Exceção capturada:". $e->getMessage());
	}
?>