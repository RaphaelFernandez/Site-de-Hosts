<?php
/*
 * API DE EXCLUSÃO DE HOST
 *
 * Parametros esperados de entrada:
 *      * nome
 *      * porta ssh
 *      * porta web
 *  exemplo json esperado
 *
 *
{
	"nome":"docker1"
	"portassh" : "8080"
	"portaweb" : "220"
}
 *
 Saidas:
(202) Host deletado com sucesso
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
		if(!(isset($obj['name']) AND isset($obj['ssh']) AND isset($obj['web']))) {
			//Faltou algum campo então nao é aprovada a movimentacao//
			
			//Responde ao usuario//
			http_response_code(403);
			
			//Sai da execução//
			exit("Houve algum erro campos em branco");
		}
		else{
			//Parametros de entrada//
			$id = $_SESSION['id_user'];
			$name =$obj["name"];
			$ssh_port =$obj["ssh"];
			$web_port =$obj["web"];
			
			try {
				//Checa o host no banco//
				$stmt = $dbh->prepare("SELECT idhost FROM hosts WHERE id_usu= :ID AND nome=:NAME AND porta_ssh=:PORTASSH AND porta_web=:PORTAWEB");

				//Vinculando parametros//
				$stmt->bindParam(":ID", $id);
				$stmt->bindParam(":NAME", $name);
				$stmt->bindParam(":PORTASSH", $ssh_port);
				$stmt->bindParam(":PORTAWEB", $web_port);

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
					exit ("Houve algum erro, o banco não possui esse registro.");	
				}
				else{
					//Para cada elemento do resultado//
					foreach ($result as $row) {
							try{
								//Checa o host no banco//
								$stmt = $dbh->prepare("DELETE FROM hosts WHERE idhost=:IDHOST;");

								//Vinculando parametros//
								$stmt->bindParam(":IDHOST", $row["idhost"]);
								//Realmente realiza a execucao da query//
								$stmt->execute();
							}catch (Exception $e) {
								//Responde ao usuario//
								http_response_code(403);
								
								//Sai da execução//
								exit("Exceção capturada:". $e->getMessage());
							}
							//Responde ao usuario//
							http_response_code(202);
							
							//Sai da execução//
							exit ();
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