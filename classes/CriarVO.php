<?php

require_once 'classes/Conexao.php';

class CriarVO extends Conexao
{
	/**
	 * Fun��o Utilizada Para Retornar Um Objeto VO de Uma Tabela Informada
	 * @author Marconi C�sar
	 * @name LerTb
	 */
	public function LerTb($tabela)
	{
		$query = "SHOW COLUMNS FROM db_opus.".$tabela."";
		$result = $this->conn->query($query);

		while ($result->fetch_assoc()) {
			echo 'public $'.$row['Field'].';'."<br/>";			
		}
	}
}