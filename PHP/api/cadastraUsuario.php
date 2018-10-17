<?php
/*
 * API DE CADASTRAMENTO DE USUARIO
 * Parametros esperados de entrada:
 *      * Nome completo
 *      * email
 *      * senha
 *  exemplo json esperado
 *
 *
 {
	"nome":"nome_Completo_usuario5",
	"email" : "email5",
	"senha" : "12345678",

}
 *
 Saidas:
(201) Se inseriu com sucesso
(403) + Nome de usuario ou email ja existente
(403) + Houve algum erro (Se algum campo nao veio preenchido)
 */

//Recebe variaveis por Json (Retorna array indexado na variavel $obj)//
    include "include/json/json_rec.php";

//Valida no banco as informações recebidas//
   include "include/db/connect.php";


   //Variavel geral de ID//
    $id=0;

	//Parametros de entrada//
	$email = $obj['email'];
    $name = $obj['username'];
    $password = $obj['password'];
	´
    // Verifica se veio algum campo em branco//
    if(!(isset($obj['username']) AND isset($obj['email']) AND isset($obj['password']))) {
        //Faltou algum campo então nao é aprovada a movimentacao//
		
		//Responde ao usuario//
		http_response_code(403);
		
		//Sai da execução//
		exit("Houve algum erro campos em branco");
    }
    //Todos os campos vieram preenchidos//
    else{
        // Verifica no banco se ja tem algum usuario com aquele nome ou email escolhido//
        //Prepara para a query//
        $stmt = $dbh->prepare("SELECT idusuario FROM usuarios WHERE email= :EMAIL OR nome =:NAME");

        //Vinculando parametros//
        $stmt->bindParam(":EMAIL",$email);
        $stmt->bindParam(":NAME",$name);

        // Realmente realiza a execucao da query//
        $stmt -> execute();
		
        //Exibe a quantidade de itens encontrados na query//
        $qtd_result = $stmt->rowCount();

        //Validando se tem usuario com aquela senha//
        if($qtd_result  > 0){
            //Responde ao usuario//
			http_response_code(403);
		
			//Sai da execução//
            exit("Email ou Usuario já existente");
        }
        else {
            // Validar dados e se estiver tudo correto cadastrar no DB//
			$options = array("cost"=>4);
			$password=password_hash($password, PASSWORD_BCRYPT, $options);
			echo($password);
            try {
                //Realiza insercao User//
                $stmt = $dbh->prepare("INSERT INTO usuarios (nome,senha,email) VALUES (:NAME,:PASSWORD,:EMAIL)");

                //Vinculando parametros//
				$stmt->bindParam(":NAME", $name);
                $stmt->bindParam(":PASSWORD", $password);
				$stmt->bindParam(":EMAIL", $email);
				
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
			exit("Usuario cadastrado com sucesso!");
        }
    }
?>