<?php


require_once 'classes/Conexao.php';

require_once 'vo/GraficoClientesVO.php';


class Relatorios extends Conexao
{
	public function gerarDadosGraficosCliente($codEmpresa)
	{
		if($codEmpresa == '3')
		{
			$date = '%Y-%m-%d';
		}else{
			$date = '%Y-%m';
		}
		
		//RETORNA A QTD DE CLIENTES CADASTRADOS NOS ULTIMIOS 12 MESES CASO HAJA
		$query = "SELECT DATE_FORMAT(DATA_CADASTRO, '%Y-%m')as DATA, count( DATA_CADASTRO ) as QTD 
		FROM clientes WHERE EMPRESA_ID = '$codEmpresa' GROUP BY DATE_FORMAT(DATA_CADASTRO, '%Y-%m')  
		ORDER BY ID DESC LIMIT 12";
		//
		
		$result = $this->conn->query($query);
		
		if($result->num_rows ==0)
		{
			return 'ERRO';
		}
		while($row = $result->fetch_assoc())
		{
			$graficoCliente = new GraficoClientesVO();
			
						
			
			if(substr($row['DATA'], -2) == '01')
			{
				$data = 'Janeiro/'.substr($row['DATA'], 0, 4);
			}
			if(substr($row['DATA'], -2) == '02')
			{
				$data = 'Fevereiro/'.substr($row['DATA'], 0, 4);
			}			
			if(substr($row['DATA'], -2) == '03')
			{
				$data = 'Março/'.substr($row['DATA'], 0, 4);
			}
			if(substr($row['DATA'], -2) == '04')
			{
				$data = 'Abril/'.substr($row['DATA'], 0, 4);
			}
			if(substr($row['DATA'], -2) == '05')
			{
				$data = 'Maio/'.substr($row['DATA'], 0, 4);
			}
			if(substr($row['DATA'], -2) == '06')
			{
				$data = 'Junho/'.substr($row['DATA'], 0, 4);
			}
			if(substr($row['DATA'], -2) == '07')
			{
				$data = 'Julho/'.substr($row['DATA'], 0, 4);
			}
			if(substr($row['DATA'], -2) == '08')
			{
				$data = 'Agosto/'.substr($row['DATA'], 0, 4);
			}
			if(substr($row['DATA'], -2) == '09')
				{
				$data = 'Setembro/'.substr($row['DATA'], 0, 4);
			}
			if(substr($row['DATA'], -2) == '10')
			{
				$data = 'Outubro/'.substr($row['DATA'], 0, 4);
			}
			if(substr($row['DATA'], -2) == '11')
			{
				$data = 'Novembro/'.substr($row['DATA'], 0, 4);
			}
			if(substr($row['DATA'], -2) == '12')
			{
				$data = 'Dezembro/'.substr($row['DATA'], 0, 4);	
			}
			
			
				
			
			$graficoCliente->DATA = $data;
			$graficoCliente->QTD = $row['QTD'];
			
			$graficoClientes[] = $graficoCliente;
		}
		
		return $graficoClientes;
		
		
	}
}