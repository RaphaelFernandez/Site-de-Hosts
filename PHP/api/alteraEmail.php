<?php
/*
 * API DE ALTERAÇÃO DE EMAIL
 *
 * Parametros esperados de entrada:
 *      * Nome antigo
 *      * Nome novo
 * 
 *  exemplo json esperado
 *
 *
{
	"old_name":"rapha_fernandez@hotmail.com"
	"new_name" : "joao_lopes@maua.br"
}
 *
 Saidas:
(202) Email alterado com sucesso
(403) Usuario invalido,Email já em uso,Erro no DB,Informaçoes erradas
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
		if(!(isset($obj['old_email']) AND isset($obj['new_email']) )) {
			//Faltou algum campo então nao é aprovada a movimentacao//
			
			//Responde ao usuario//
			http_response_code(403);
			
			//Sai da execução//
			exit("Houve algum erro campos em branco");
		}
		else{
			//Parametros de entrada//
			$id = $_SESSION['id_user'];
			$old_email =$obj["old_email"];
			$new_email =$obj["new_email"];
			
			try {
				//Checa o  antigo nome do usuario//
				$stmt = $dbh->prepare("SELECT nome FROM usuarios WHERE idusuario= :ID AND email=:EMAIL");
			
				//Vinculando parametros//
				$stmt->bindParam(":ID", $id);
				$stmt->bindParam(":EMAIL", $old_email);

				//Realmente realiza a execucao da query//
				$stmt->execute();
				
				//Pega todos os resultados//
				$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

				//Exibe a quantidade de itens encontrados na query//
				$qtd_result = $stmt->rowCount();
				
				if($qtd_result !=1){
					//Responde ao usuario//
					http_response_code(403);
						
					//Sai da execução//
					exit ("Houve algum erro, o email de usuario nao existe.");	
				}
				else{
					try{
						//Checa o nome do usuario//
						$stmt = $dbh->prepare("SELECT nome FROM usuarios WHERE  email=:EMAIL");

						//Vinculando parametros//
						$stmt->bindParam(":EMAIL", $new_email);

						//Realmente realiza a execucao da query//
						$stmt->execute();
						
						//Pega todos os resultados//
						$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

						//Exibe a quantidade de itens encontrados na query//
						$qtd_result = $stmt->rowCount();
						
						if($qtd_result ==1){
							//Responde ao usuario//
							http_response_code(403);
								
							//Sai da execução//
							exit ("Este email ja existe.");	
						}
						else{
							try{
								//Prepara para a query
								$stmt = $dbh->prepare("UPDATE usuarios SET email =:NOVO_EMAIL WHERE email=:ANTIGO_EMAIL AND idusuario=:ID");
								
								// bindParam ajuda evitar SQLinjection
								// Vinculando parametros
								$stmt->bindParam(":ANTIGO_EMAIL",$old_email);
								$stmt->bindParam(":NOVO_EMAIL",$new_email);
								$stmt->bindParam(":ID",$id);
								// Realmente realiza a execucao da query
								$stmt -> execute();
										
								// Retorna o codigo 201 (Criado)
								http_response_code(201);
								exit("Email alterado com sucesso");
								
								
							}catch (Exception $e) {
							//Responde ao usuario//
							http_response_code(403);
							
							//Sai da execução//
							exit("Exceção capturada:". $e->getMessage());
							}
						}
						
					}catch (Exception $e) {
					//Responde ao usuario//
					http_response_code(403);
					
					//Sai da execução//
					exit("Exceção capturada:". $e->getMessage());
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