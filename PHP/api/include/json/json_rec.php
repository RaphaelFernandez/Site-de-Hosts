<?php
/**
 * Created by PhpStorm.
 * User: lcr07
 * Date: 04/05/2018
 * Time: 17:34
 */


//Recebendo informaçoes da entrada default do php
$body = file_get_contents('php://input');
//Retira espaços em branco
$body = trim($body);

//Gera um array indexado atraves do json recebido
$obj  = json_decode($body,true);
//echo($obj);
?>
