<?php

require_once 'classes/Conexao.php';
require_once 'vo/LogOpusVO.php';

class LogOpus extends Conexao
{
	public function cadastrarLog(LogOpusVO $logOpus)
	{
		$this->conn->autocommit(false);
		
		$logOpus->USER_LOGADO = strtoupper($logOpus->USER_LOGADO);
		$query = "INSERT INTO log_opus (EMPRESA_ID,DESC_LOG, USER_LOGADO) VALUES " .
				"('$logOpus->EMPRESA_ID','$logOpus->DESC_LOG', '$logOpus->USER_LOGADO')";
		$result = $this->conn->query($query);
		
		if($this->conn->error)
		{
			$this->conn->rollback();
			$this->conn->autocommit(true);
			
			return 'CADASTRAR LOG ERRO: '.$this->conn->error;
		}else{
			$this->conn->commit();
			$this->conn->autocommit(true);
		}
		
	}
	public function listarLog($codEmpresa)
	{

		$query = "SELECT * FROM log_opus WHERE EMPRESA_ID=$codEmpresa ORDER by ID DESC";
		$result = $this->conn->query($query);
		$nRow = $result->num_rows;
		if($nRow == 0)
		{
			return 'ERRO';
		}
		
		while ( $row = $result->fetch_assoc() ) {
			
			$logOpus = new LogOpusVO();
			
			$logOpus->ID = $row['ID'];
			$logOpus->EMPRESA_ID = $row['EMPRESA_ID'];
			$logOpus->DATA_HORA = $row['DATA_HORA'];
			$logOpus->DESC_LOG = $row['DESC_LOG'];
			$logOpus->USER_LOGADO = $row['USER_LOGADO'];
		
			$logOpuss[] = $logOpus;
		}
		
		return $logOpuss;	
	}
}

