<?php
/*
 * API DE CHECAGEM DE HOSTS DO USUARIO
 *
 Saidas:
(200) Hosts estão ok e serão retornados.
(204) Usuario nao possui hosts.
(403) Erro na conexão do banco.
 */

//Recebe variaveis por Json (Retorna array indexado na variavel $obj)
include "include/json/json_rec.php";

//Valida no banco as informações recebidas
include "include/db/connect.php";
	
	//Inicia sessão//
	session_start();
	
	//Array contendo a tabela//
	$table = array();
	
	//Array contendo o total de hosts//
	$total=array();
	
	//Parametros de entrada//
	$id = $_SESSION['id_user'];
	
	try {
		//Checa hosts daquele usuario//
		$stmt = $dbh->prepare("SELECT * FROM hosts WHERE id_usu= :ID");

		//Vinculando parametros//
		$stmt->bindParam(":ID", $id);
		
		//Realmente realiza a execucao da query//
		$stmt->execute();
		
		//Quantidade de resultados//
		$qtd_result = $stmt->rowCount();
		
		//Pega todos os resultados//
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		//Checa se o resultado retornado é vazio//
		if(count($result) == 0){
				//Responde ao usuario//
				http_response_code(204);
				
				//Sai da execução//
				exit();
		}
		else {
			//Para cada elemento de result uma linha é montada//
			foreach ($result as $row) {
				
				//Linha basica//
				$line='<tr style="background-color: white;"><td class="text-center" id="name">'.$row["nome"].'</td><td class="text-center" id="ssh_port">'.$row["porta_ssh"].'</td><td class="text-center" id="web_port">'.$row["porta_web"].'</td>';
				
				if($row["status"] ==0){
					
					//Se o host esta ativo//
					$line .='<td class="text-center powerOn" id="status">Desativado</td><td class="text-center"><div id="confirmCancel"><a class="btn btn-large btn-success " id="powerOn"><i class="fas fa-power-off fa-fw"></i></a>&nbsp&nbsp<a class="btn btn-large btn-danger " id="delete"><i class="fas fa-times fa-fw"></i></a></div></td></tr>';
				}
				else{	
					//Se o host esta desativado//
					$line .='<td class="text-center powerOff" id="status" >Ativo</td><td class="text-center"><div id="confirmCancel"><a class="btn btn-large btn-danger " id="powerOn"><i class="fas fa-power-off fa-fw"></i></a>&nbsp&nbsp<a class="btn btn-large btn-danger " id="delete"><i class="fas fa-times fa-fw"></i></a></div></td></tr>';
				}
				//Popula o array da tabela que sera retornado//		
				array_push($table,$line);	
			}
			
			//Popula o array final que sera retornado//	
			array_push($total,array(
			  'table' => $table,
			  'total' => $qtd_result
			));
			
			//Responde ao usuario//
			http_response_code(200);
			
			//Sai da execucao//
			exit (json_encode($total));
		}
	}catch (Exception $e) {
		// Responde ao usuario//
		http_response_code(403);
		
		//Sai da execucao//
		exit("Exceção capturada:". $e->getMessage());
	}
?>