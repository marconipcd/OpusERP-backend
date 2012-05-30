<?php

ini_set('max_execution_time','600000000');
require_once 'classes/Conexao.php';

require_once 'vo/AniversariantesVO.php';


class Rotinas extends Conexao
{	
	public function rotinaBloqDesbloqFinanceiro($codEmpresa)
	{	
				
		return 'ok';
	}
	
	public function listarAniversriantes()
	{
		$datahoje = date("d/m/");
		$datahoje1 = date("dm");
		$query = "SELECT * FROM pessoa WHERE (dataNascimento LIKE '$datahoje%' OR dataNascimento LIKE '$datahoje1%' )";
		$result = $this->conn->query($query); 
		$nrows = $result->num_rows;
		
		if($nrows === 0)
		{
			return "Nenhum Aniversariante foi Encontrado";
		}else
		{
		while($row = $result->fetch_assoc())
		{
			$cliente = new AniversariantesVO();
			
			$cliente->codigoPessoa = $row['codigoPessoa'];
			$cliente->tratamento = $row['tratamento'];
			$cliente->textoNome = $row['textoNome'];
			$cliente->telefone = $row['telefone'];
			$cliente->telefone2 = $row['telefone2'];
			$cliente->celular1 = $row['celular1'];
			$cliente->celular2 = $row['celular2'];
			$cliente->email = $row['email'];
			
			$clientes[] = $cliente;
		}
		
			return $clientes;
			
		}
		
	}
	public function verificarAtrasoBoletos()
	{
		$dataHoje = date('Y-m-d');
		$query = "SELECT * FROM contasapagar WHERE status = 'ABERTO' AND vencimento < '$dataHoje' AND bloqueado is NULL";
		$result = $this->conn->query($query);
		
		//CONSULTAR PRAZO LIMITE DE BLOQUEIO
		$queryPrazoBloqueio = "SELECT * FROM bloqueio";
		$resultPrazoBloqueio = $this->conn->query($queryPrazoBloqueio);
		$row_bloqueio = $resultPrazoBloqueio->fetch_assoc();
		
		while ($row = $result->fetch_assoc()) {
			
			$id = $row['id'];
			
			$dataVencimento = explode('-', $row['vencimento']);
			$dataAtual = explode('-', $dataHoje);
			
			$dataAtual = mktime(0,0,0,$dataAtual[1],$dataAtual[2],$dataAtual[0]);
			$dataBoleto = mktime(0,0,0,$dataVencimento[1],$dataVencimento[2],$dataVencimento[0]);  
			$d3 = ($dataAtual-$dataBoleto);
			$dias = round(($d3/60/60/24));
			$prazo = $row_bloqueio['dias'];
			
			if($dias >= $prazo)
			{
				$queryMarcar = "UPDATE contasapagar SET bloquear = 'S' WHERE id ='$id'";
				$resutlMarcar = $this->conn->query($queryMarcar);
			}
			
		}
		
		return 'OK';
	}
	public function bloquear()
	{
		$query = "SELECT contasapagar.*, pessoa.codigoPessoa, pessoa.loginCliente  FROM contasapagar 
		LEFT JOIN pessoa ON contasapagar.cliente = pessoa.codigoPessoa
		WHERE bloquear = 'S' AND desbloqueado IS NULL";
		$result = $this->conn->query($query);
		
		while ($row = $result->fetch_assoc()) {
			
			$id = $row['id'];
			$idCliente = $row['cliente'];
			$username = $row['loginCliente'];
			
			//ATUALIZA BOLETO PARA BLOQUEADO
			$queryUpdate = "UPDATE contasapagar SET bloqueado = 'S' WHERE id ='$id'";
			$resultUpdate = $this->conn->query($queryUpdate);
			
			//ATUALIZAR CAMPO planoCliente PARA PLANO UTILIZADO NO MOMENTO
			$queryUpdate2 = "UPDATE pessoa SET planoCliente = 'bloqueado' WHERE codigoPessoa = '$idCliente'";
			$resultUpdate2 = $this->conn->query($queryUpdate2);
			
			//ATUALIZAR CAMPO value PARA PLANO UTILIZADO NO MOMENTO
			$queryUpdate3 = "UPDATE radreply SET value = 'bloqueado' WHERE username = '$username'";
			$resultUpdate3 = $this->conn->query($queryUpdate3);			
			
		}
		
		return 'OK';
	}
	
	public function desbloquear()
	{
		
		$dataHoje = date('Y-m-d');
		
		//$queryBoletos = "SELECT * FROM contasapagar WHERE status = 'FECHADO' AND desbloquear = 'S' AND desbloqueado is null";
		$queryBoletos = "SELECT contasapagar.*, pessoa.codigoPessoa, pessoa.loginCliente, pessoa.plano  FROM contasapagar 
		LEFT JOIN pessoa ON contasapagar.cliente = pessoa.codigoPessoa
		WHERE desbloquear = 'S' AND desbloqueado is null";
		$resultBoletos = $this->conn->query($queryBoletos);
		
	 	while ($rowBoletos = $resultBoletos->fetch_assoc())
	 	{
	 		$id = $rowBoletos['id'];
			$idCliente = $rowBoletos['cliente'];
			$username = $rowBoletos['loginCliente'];
			$plano = $rowBoletos['plano'];
			
			if($plano == '')
			{
				$plano = '300k';
			}
			
			//ATUALIZA BOLETO PARA BLOQUEADO
			$queryUpdate = "UPDATE contasapagar SET desbloqueado = 'S' WHERE id ='$id'";
			$resultUpdate = $this->conn->query($queryUpdate);
			
			
			//ATUALIZAR CAMPO planoCliente PARA PLANO UTILIZADO NO MOMENTO
			$queryUpdate2 = "UPDATE pessoa SET planoCliente = '$plano' WHERE codigoPessoa = '$idCliente'";
			$resultUpdate2 = $this->conn->query($queryUpdate2);
			
			//ATUALIZAR CAMPO value PARA PLANO UTILIZADO NO MOMENTO
			$queryUpdate3 = "UPDATE radreply SET value = '$plano' WHERE username = '$username'";
			$resultUpdate3 = $this->conn->query($queryUpdate3);
	 	}		
		
			
			
			return $msg = 'OK';
			
			
	}

	
}