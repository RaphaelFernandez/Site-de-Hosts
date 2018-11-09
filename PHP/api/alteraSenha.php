<?php
/*
 * API DE ALTERAÇÃO DE SENHA
 *
 * Parametros esperados de entrada:
 *      * Nome antigo
 *      * Nome novo
 * 
 *  exemplo json esperado
 *
 *
{
	"old_password":"123456"
	"new_password" : "5up!t3r"
}
 *
 Saidas:
(202) Senha alterada com sucesso
(403) Usuario invalido,Erro no DB,Informaçoes erradas
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
		
		
		 // Verifica se veio algum campo em branco//
		if(!(isset($obj['old_password']) AND isset($obj['new_password']) )) {
			//Faltou algum campo então nao é aprovada a movimentacao//
			
			//Responde ao usuario//
			http_response_code(403);
			
			//Sai da execução//
			exit("Houve algum erro campos em branco");
		}
		else{
			//Parametros de entrada//
			$id = $_SESSION['id_user'];
			$old_password =$obj["old_password"];
			$new_password =$obj["new_password"];
			
			
			
			try {
				//Checa o  antigo nome do usuario//
				$stmt = $dbh->prepare("SELECT senha FROM usuarios WHERE idusuario= :ID");
			
				//Vinculando parametros//
				$stmt->bindParam(":ID", $id);

				//Realmente realiza a execucao da query//
				$stmt->execute();
				
				//Pega todos os resultados//
				$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
				
				foreach ($result as $row) {
					// Senha correta
					if (password_verify($old_password, $row['senha'])) {
						
						//Encripta a nova senha//
						$options = array("cost"=>4);
						$password=password_hash($new_password, PASSWORD_BCRYPT, $options);
						
						try {
							//Realiza insercao User//
							$stmt = $dbh->prepare("UPDATE usuarios SET senha =:NOVA_SENHA WHERE idusuario=:ID");

							//Vinculando parametros//
							$stmt->bindParam(":NOVA_SENHA", $password);
							$stmt->bindParam(":ID", $id);
							
							//Realmente executa a query//
							$stmt->execute();

						} catch (Exception $e) {
							//Responde ao usuario//
							http_response_code(403);		
							
							//Sai da execução//
							exit("Exceção capturada:". $e->getMessage());
						}

						//Responde ao usuario//
						http_response_code(201);
						
						//Sai da execução//
						exit("Senha alterada com sucesso com sucesso!");
					}
					else{
						//Responde ao usuario//
						http_response_code(403);
							
						//Sai da execução//
						exit ("Senha incorreta,por favor digite sua senha antiga novamente");
						
					}
				}
			}catch (Exception $e) {
			//Responde ao usuario//
			http_response_code(403);
			
			//Sai da execução//
			exit("Exceção capturada:". $e->getMessage());
			}
		}
	}
?>