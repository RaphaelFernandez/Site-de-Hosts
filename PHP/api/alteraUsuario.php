<?php
/*
 * API DE ALTERAÇÃO DE USUARIO
 *
 * Parametros esperados de entrada:
 *      * Nome antigo
 *      * Nome novo
 * 
 *  exemplo json esperado
 *
 *
{
	"old_name":"rfernandez"
	"new_name" : "jlopes"
}
 *
 Saidas:
(202) Nome de usuario alterado com sucesso
(403) Usuario invalido,Nome de usuario já em uso,Erro no DB,Informaçoes erradas
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
		if(!(isset($obj['old_name']) AND isset($obj['new_name']) )) {
			//Faltou algum campo então nao é aprovada a movimentacao//
			
			//Responde ao usuario//
			http_response_code(403);
			
			//Sai da execução//
			exit("Houve algum erro campos em branco");
		}
		else{
			//Parametros de entrada//
			$id = $_SESSION['id_user'];
			$old_name =$obj["old_name"];
			$new_name =$obj["new_name"];
			
			try {
				//Checa o  antigo nome do usuario//
				$stmt = $dbh->prepare("SELECT nome FROM usuarios WHERE idusuario= :ID AND nome=:NOME");

				//Vinculando parametros//
				$stmt->bindParam(":ID", $id);
				$stmt->bindParam(":NOME", $old_name);

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
					exit ("Houve algum erro, o nome de usuario nao existe.");	
				}
				else{
					try{
						//Checa o nome do usuario//
						$stmt = $dbh->prepare("SELECT nome FROM usuarios WHERE  nome=:NOME");

						//Vinculando parametros//
						$stmt->bindParam(":NOME", $new_name);

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
							exit ("Este nome de usuario ja existe.");	
						}
						else{
							try{
								//Prepara para a query
								$stmt = $dbh->prepare("UPDATE usuarios SET nome =:NOVO_NOME WHERE nome=:ANTIGO_NOME AND idusuario=:ID");
								
								// bindParam ajuda evitar SQLinjection
								// Vinculando parametros
								$stmt->bindParam(":ANTIGO_NOME",$old_name);
								$stmt->bindParam(":NOVO_NOME",$new_name);
								$stmt->bindParam(":ID",$id);
								// Realmente realiza a execucao da query
								$stmt -> execute();
										
								// Retorna o codigo 201 (Criado)
								http_response_code(201);
								exit("Usuario alterado com sucesso");
								
								
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