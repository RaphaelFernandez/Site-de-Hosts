<?php
/*
 * API DE CADASTRAMENTO DE HOST
 * Parametros esperados de entrada:
 *      *Nome Host
 *      *Porta SSH
 *      *Porta Web
 *  exemplo json esperado
 *
 *
 {
	"nomehost":"docker1",
	"portassh":"5000",
	"portaweb":"5001"
 }
 *
 Saidas:
(201) Se inseriu com sucesso
(403) Não esta logado,parametros faltando,id de dono nao existe.
*/
// Recebe variaveis por Json (Retorna array indexado na variavel $obj)
    include "include/json/json_rec.php";
	
// Valida no banco as informações recebidas
   include "include/db/connect.php";

	//Starta sessão//
	session_start();
	
	if (!(isset($_SESSION['id_user']) )){
	http_response_code(403);
	exit("Usuario nao possui uma sessão valida");
	}
	else{
		
		// Verifica se veio algum campo em branco
		if(!(isset($obj['name']) AND isset($obj['sshPort']) AND isset($obj['webPort']))) {
			
			// Faltou algum campo então nao é aprovada a movimentacao
			http_response_code(403);
			exit("Houve algum erro campos em branco");
		}
		// Todos os campos vieram preenchidos
		else{
			
			$id=$_SESSION['id_user'];
			$name =$obj['name'];
			$ssh =$obj['sshPort'];
			$web =$obj['webPort'];
			try{
				// Verifica no banco se existe usuario com aquele id
				$stmt = $dbh->prepare("SELECT idusuario FROM usuarios WHERE idusuario = :ID_DONO");
				// bindParam ajuda evitar SQLinjection
				// Vinculando parametros
				$stmt->bindParam(":ID_DONO",$id);
				// Realmente realiza a execucao da query
				$stmt -> execute();
				
				//Pega os itens encontrados na query
				$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
				
				}catch (Exception $e) {
					echo 'Exceção capturada: ', $e->getMessage(), "\n";
					http_response_code(403);
					exit("Erro DB 1");
				}
			// Verifica se existem o dono
			if(count($result) == 0){
				// Sai e da erro
				http_response_code(403);
				exit("O usuario não existe");
			}
			// Verifica se a porta web ou host ja esta em uso
			try{
				// Verifica no banco se existe alguma porta em uso para as selecionadas
				$stmt = $dbh->prepare("SELECT idhost FROM hosts WHERE porta_ssh = :PORTASSH OR porta_web = :PORTAWEB OR nome = :NOMEHOST");
				//echo("SELECT idhost FROM hosts WHERE porta_ssh =". $ssh. "OR porta_web = ".$web. "OR nome = $nome");
				// bindParam ajuda evitar SQLinjection
				// Vinculando parametros
				$stmt->bindParam(":PORTASSH",$ssh);
				$stmt->bindParam(":PORTAWEB",$web);
				$stmt->bindParam(":NOMEHOST",$name);
				// Realmente realiza a execucao da query
				$stmt -> execute();
				
				//Pega os itens encontrados na query
				$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
			
			}catch (Exception $e) {
				echo 'Exceção capturada: ', $e->getMessage(), "\n";
				http_response_code(403);
				exit("Erro DB 2");
			}
			// Verifica se existem portas iguais
			if(count($result) != 0){
				// Sai e da erro
				http_response_code(403);
				exit("Alguma porta / nome de host, já existe");
			}		
		
			else {
				try{
					//Prepara para a query
					$stmt = $dbh->prepare("INSERT INTO hosts (nome, porta_ssh, porta_web, id_usu) VALUES (:NOMEHOST, :PORTASSH,:PORTAWEB, :ID_DONO)");
					
					// bindParam ajuda evitar SQLinjection
					// Vinculando parametros
					$stmt->bindParam(":NOMEHOST",$name);
					$stmt->bindParam(":PORTASSH",$ssh);
					$stmt->bindParam(":PORTAWEB",$web);
					$stmt->bindParam(":ID_DONO",$id);
					// Realmente realiza a execucao da query
					$stmt -> execute();
							
					// Retorna o codigo 201 (Criado)
					http_response_code(201);
					exit("Host cadastrado");
				}catch (Exception $e) {
					echo 'Exceção capturada: ', $e->getMessage(), "\n";
					http_response_code(403);
					exit("Erro DB 3");
				}
			
			}
		}
	}
?>