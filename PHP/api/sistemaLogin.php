<?php
/*
 * API DE VALIDACAO DE USUARIO
 * Parametros esperados de entrada:
 *      * email
 *      * senha

 *  exemplo json esperado
 *
 *
{
	"username":"joao"
	"senha" : "redes"
}
 *
 Saidas:
(202) Usuario e senha validos
(403) Usuario e ou senha invalidos
 */

// Recebe variaveis por Json (Retorna array indexado na variavel $obj)
include "include/json/json_rec.php";

// Valida no banco as informações recebidas
include "include/db/connect.php";

// Parameters
$username = $obj["username"];
$password = $obj["password"];


try {
	//Check user in DB//
    $stmt = $dbh->prepare("SELECT idusuario,senha FROM usuarios WHERE nome= :NOME;");

    // Vinculando parametros
    $stmt->bindParam(":NOME", $username);

    // Realmente realiza a execucao da query
    $stmt->execute();

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Para cada elemento de result
    foreach ($result as $row) {
        // Senha correta
        if (password_verify($password, $row['senha'])) {
            // Inicia uma sessao para esse usuario
            session_start();
            // Vincula seu id nessa sessao criada
            $_SESSION['id_user'] = $row['idusuario'];
            // Responde ao usuario
            http_response_code(202);
            // Sai da execucao
			exit ("Sucesso");
        }
    }
}catch (Exception $e) {
    echo 'Exceção capturada: ', $e->getMessage(), "\n";
    http_response_code(403);
    exit("Erro DB");
}

// Saiu do foreach é porque nada foi igual
http_response_code(403);
exit("Usuario ou senha invalidos");

?>