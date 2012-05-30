<?php
/**
 * Conexao com MySQL
 * Essa classe herda a superclasse mysqli
 * e necessario que a extensao mysqli esteja habilitada
 *
 * @author Marconi César
 */
class Conexao
{
    
	/* constructor */
	function __construct()
	{
		$this->conn = new mysqli("localhost","root","root","db_opus");
        }
}

?>