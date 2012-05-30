<?php

	ini_set('max_execution_time','20000000000000000000');

	$empresa = $_GET['EMPRESA_ID'];
	$registro_empresa = $_GET['REGISTRO_EMPRESA'];
	
	
	$conexaoRemota = mysql_pconnect("localhost","root","37261827");	

	if($empresa == 1)
	{
		
		
		
		
	$queryDigitalOnline = "SELECT * FROM radius.contasapagar ORDER by id";
	$resultDigitalOnline = mysql_query($queryDigitalOnline,$conexaoRemota)or die(mysql_error());
	$nRowDigitalOnline = mysql_num_rows($resultDigitalOnline)or die(mysql_error());
	if($nRowDigitalOnline > 0){
		
		
		while ($rowDigitalOline = mysql_fetch_assoc($resultDigitalOnline)or die(mysql_error()))
		{
				//VARIAVEIS ATUAIS;;
				$id = $rowDigitalOline['id']; 
				$ndocumento = $rowDigitalOline['ndocumento'];
				$cliente = $rowDigitalOline['cliente'];
				$valor = $rowDigitalOline['valor'];
				$status = $rowDigitalOline['status'];
				$desbloquear = $rowDigitalOline['desbloquear'];
				$bloquear = $rowDigitalOline['bloquear'];
				$desbloqueado = $rowDigitalOline['desbloqueado'];
				$bloqueado = $rowDigitalOline['bloqueado'];
				$emissao = $rowDigitalOline['emissao'];
				$vencimento = $rowDigitalOline['vencimento'];
				$dataPagamento = $rowDigitalOline['dataPagamento'];
				$dataBaixa = $rowDigitalOline['dataBaixa'];
				$dataExclusao = $rowDigitalOline['dataExclusao'];
				$valorPagamento = $rowDigitalOline['valorPagamento'];
				$formaPgto = $rowDigitalOline['formaPgto'];
				$nNumero = $rowDigitalOline['nNumero'];
				$tipo = $rowDigitalOline['tipo'];
				$controle = $rowDigitalOline['controle'];
				$empresa = $rowDigitalOline['empresa'];
				$centro_de_custo = $rowDigitalOline['centro_de_custo'];
				
				if($desbloquear == 'SIM')
				{
					$desbloquear = 'S';
				}
				
				if($dataBaixa == '')
				{
					$dataBaixa = $dataPagamento;
				}
				if($dataPagamento == '')
				{
					if($dataExclusao == '')
					{
						//INSERT TABELA ATUAL db_opus.contas_receber;				
						$queryInsertContasReceberDigitalOnline = "INSERT INTO db_opus.contas_receber (CLIENTES_ID, EMPRESA_ID, N_DOC, N_NUMERO, VALOR_TITULO, 
						VALOR_PAGAMENTO, DATA_EMISSAO, DATA_VENCIMENTO, FORMA_PGTO, TIPO_BAIXA, CONTROLE, 
						CENTRO_CUSTO, STATUS_2, DESBLOQUEAR, BLOQUEAR, DESBLOQUEADO, BLOQUEADO) VALUES 
						('$cliente', '1', '$ndocumento', '$nNumero', '$valor', '$valorPagamento', '$emissao', '$vencimento',  
						 '$formaPgto', '$tipo', '$controle', '$centro_de_custo', '$status', '$desbloquear', 
						'$bloquear', '$desbloqueado', '$bloqueado')";
					}else
					{
						//INSERT TABELA ATUAL db_opus.contas_receber;				
						$queryInsertContasReceberDigitalOnline = "INSERT INTO db_opus.contas_receber (CLIENTES_ID, EMPRESA_ID, N_DOC, N_NUMERO, VALOR_TITULO, 
						VALOR_PAGAMENTO, DATA_EMISSAO, DATA_VENCIMENTO, DATA_EXCLUSAO, FORMA_PGTO, TIPO_BAIXA, CONTROLE, 
						CENTRO_CUSTO, STATUS_2, DESBLOQUEAR, BLOQUEAR, DESBLOQUEADO, BLOQUEADO) VALUES 
						('$cliente', '1', '$ndocumento', '$nNumero', '$valor', '$valorPagamento', '$emissao', '$vencimento',  
						'$dataExclusao', '$formaPgto', '$tipo', '$controle', '$centro_de_custo', '$status', '$desbloquear', 
						'$bloquear', '$desbloqueado', '$bloqueado')";
					}
					
				}else
				{
					if($dataExclusao == '')
					{
						//INSERT TABELA ATUAL db_opus.contas_receber;				
						$queryInsertContasReceberDigitalOnline = "INSERT INTO db_opus.contas_receber (CLIENTES_ID, EMPRESA_ID, N_DOC, N_NUMERO, VALOR_TITULO, 
						VALOR_PAGAMENTO, DATA_EMISSAO, DATA_VENCIMENTO, DATA_PAGAMENTO, DATA_BAIXA, FORMA_PGTO, TIPO_BAIXA, CONTROLE, 
						CENTRO_CUSTO, STATUS_2, DESBLOQUEAR, BLOQUEAR, DESBLOQUEADO, BLOQUEADO) VALUES 
						('$cliente', '1', '$ndocumento', '$nNumero', '$valor', '$valorPagamento', '$emissao', '$vencimento', '$dataPagamento', '$dataBaixa', 
						 '$formaPgto', '$tipo', '$controle', '$centro_de_custo', '$status', '$desbloquear', 
						'$bloquear', '$desbloqueado', '$bloqueado')";
					}else
					{
						//INSERT TABELA ATUAL db_opus.contas_receber;				
						$queryInsertContasReceberDigitalOnline = "INSERT INTO db_opus.contas_receber (CLIENTES_ID, EMPRESA_ID, N_DOC, N_NUMERO, VALOR_TITULO, 
						VALOR_PAGAMENTO, DATA_EMISSAO, DATA_VENCIMENTO, DATA_PAGAMENTO, DATA_BAIXA, DATA_EXCLUSAO, FORMA_PGTO, TIPO_BAIXA, CONTROLE, 
						CENTRO_CUSTO, STATUS_2, DESBLOQUEAR, BLOQUEAR, DESBLOQUEADO, BLOQUEADO) VALUES 
						('$cliente', '1', '$ndocumento', '$nNumero', '$valor', '$valorPagamento', '$emissao', '$vencimento', '$dataPagamento', '$dataBaixa', 
						'$dataExclusao', '$formaPgto', '$tipo', '$controle', '$centro_de_custo', '$status', '$desbloquear', 
						'$bloquear', '$desbloqueado', '$bloqueado')";
					}
				}
				
				
				
				
				
				$resultInsertContasReceberDigitalOnline = mysql_query($queryInsertContasReceberDigitalOnline)or die(mysql_error());	
		
		}
	}
	}
	
	 if($empresa == 2)
	{
	
	//DIGITAL ;
	$queryDigital = "SELECT * FROM radius.contas_receber_digital ORDER by id";
	$resultDigital = mysql_query($queryDigital,$conexaoRemota)or die(mysql_error());
	$nRowDigital = mysql_num_rows($resultDigital)or die(mysql_error());
	
	if($nRowDigital > 0){
		
	
		while($rowDigital = mysql_fetch_assoc($resultDigital))
		{
			$id = $rowDigital['id'];
			$ndocumento  = $rowDigital['ndocumento'];
			$cliente  = $rowDigital['cliente'];
			$valor   = $rowDigital['valor'];
			$status  = $rowDigital['status'];
			$desbloquear  = $rowDigital['desbloquear'];
			$emissao  = $rowDigital['emissao'];
			$vencimento  = $rowDigital['vencimento']; 
			$dataPagamento  = $rowDigital['dataPagamento'];
			$dataBaixa  = $rowDigital['dataBaixa']; 
			$dataExclusao  = $rowDigital['dataExclusao']; 
			$valorPagamento  = $rowDigital['valorPagamento'];
			$formaPgto  = $rowDigital['formaPgto']; 
			$nNumero  = $rowDigital['nNumero'];
			$tipo  = $rowDigital['tipo']; 
			$controle  = $rowDigital['controle']; 
			$empresa  = $rowDigital['empresa']; 
			$centro_de_custo  = $rowDigital['centro_de_custo'];
			
			//INSERT TABELA ATUAL db_opus.contas_receber | DIGITAL INFORMATICA
			if($dataBaixa == '')
				{
					$dataBaixa = $dataPagamento;
				}
				if($dataPagamento == '')
				{
						//INSERT TABELA ATUAL db_opus.contas_receber;				
						$queryInsertContasReceberDigitalOnline = "INSERT INTO db_opus.contas_receber (CLIENTES_ID, EMPRESA_ID, N_DOC, N_NUMERO, VALOR_TITULO, 
						VALOR_PAGAMENTO, DATA_EMISSAO, DATA_VENCIMENTO, FORMA_PGTO, TIPO_BAIXA, CONTROLE, 
						CENTRO_CUSTO, STATUS_2) VALUES 
						('$cliente', '2', '$ndocumento', '$nNumero', '$valor', '$valorPagamento', '$emissao', '$vencimento',  
						 '$formaPgto', '$tipo', '$controle', '$centro_de_custo', '$status')";
					
					
				}else
				{
					
						//INSERT TABELA ATUAL db_opus.contas_receber;				
						$queryInsertContasReceberDigitalOnline = "INSERT INTO db_opus.contas_receber (CLIENTES_ID, EMPRESA_ID, N_DOC, N_NUMERO, VALOR_TITULO, 
						VALOR_PAGAMENTO, DATA_EMISSAO, DATA_VENCIMENTO, DATA_PAGAMENTO, DATA_BAIXA, FORMA_PGTO, TIPO_BAIXA, CONTROLE, 
						CENTRO_CUSTO, STATUS_2) VALUES 
						('$cliente', '2', '$ndocumento', '$nNumero', '$valor', '$valorPagamento', '$emissao', '$vencimento', '$dataPagamento', '$dataBaixa', 
						 '$formaPgto', '$tipo', '$controle', '$centro_de_custo', '$status')";
					
				}
				$resultInsertContasReceberDigitalOnline = mysql_query($queryInsertContasReceberDigitalOnline)or die(mysql_error());	
			
		}
	}

	}

	
	
	
	
	 if($empresa == 3)
	{
	
	//DIGITAL GR�FICA;
	$queryGrafica = "SELECT * FROM radius.contas_receber_grafica ORDER by id";
	$resultGrafica = mysql_query($queryGrafica,$conexaoRemota)or die(mysql_error());
	$nRowGrafica = mysql_num_rows($resultGrafica)or die(mysql_error());
	
	if($nRowGrafica > 0){
		
	
		while($rowGrafica = mysql_fetch_assoc($resultGrafica))
		{
			$id = $rowGrafica['id'];
			
			$ndocumento  = $rowGrafica['ndocumento'];
			
			if($rowGrafica['cliente'] == '')
			{
				$cliente  = 0000;	
			}else{
				$cliente = $rowGrafica['cliente'];
			}
			$valor   = $rowGrafica['valor'];
			$status  = $rowGrafica['status'];
			$desbloquear  = $rowGrafica['desbloquear'];
			$emissao  = $rowGrafica['emissao'];
			$vencimento  = $rowGrafica['vencimento']; 
			$dataPagamento  = $rowGrafica['dataPagamento'];
			$dataBaixa  = $rowGrafica['dataBaixa']; 
			$dataExclusao  = $rowGrafica['dataExclusao']; 
			$valorPagamento  = $rowGrafica['valorPagamento'];
			$formaPgto  = $rowGrafica['formaPgto']; 
			$nNumero  = $rowGrafica['nNumero'];
			$tipo  = $rowGrafica['tipo']; 
			$controle  = $rowGrafica['controle']; 
			$empresa  = $rowGrafica['empresa']; 
			$centro_de_custo  = $rowGrafica['centro_de_custo'];
			
			//INSERT TABELA ATUAL db_opus.contas_receber | DIGITAL INFORMATICA
			if($dataBaixa == '')
				{
					$dataBaixa = $dataPagamento;
				}
				if($dataPagamento == '')
				{
						//INSERT TABELA ATUAL db_opus.contas_receber;				
						$queryInsertContasReceberDigitalOnline = "INSERT INTO db_opus.contas_receber (CLIENTES_ID, EMPRESA_ID, N_DOC, N_NUMERO, VALOR_TITULO, 
						VALOR_PAGAMENTO, DATA_EMISSAO, DATA_VENCIMENTO, FORMA_PGTO, TIPO_BAIXA, CONTROLE, 
						CENTRO_CUSTO, STATUS_2) VALUES 
						('$cliente', '3', '$ndocumento', '$nNumero', '$valor', '$valorPagamento', '$emissao', '$vencimento',  
						 '$formaPgto', '$tipo', '$controle', '$centro_de_custo', '$status')";
					
					
				}else
				{
					
						//INSERT TABELA ATUAL db_opus.contas_receber;				
						$queryInsertContasReceberDigitalOnline = "INSERT INTO db_opus.contas_receber (CLIENTES_ID, EMPRESA_ID, N_DOC, N_NUMERO, VALOR_TITULO, 
						VALOR_PAGAMENTO, DATA_EMISSAO, DATA_VENCIMENTO, DATA_PAGAMENTO, DATA_BAIXA, FORMA_PGTO, TIPO_BAIXA, CONTROLE, 
						CENTRO_CUSTO, STATUS_2) VALUES 
						('$cliente', '3', '$ndocumento', '$nNumero', '$valor', '$valorPagamento', '$emissao', '$vencimento', '$dataPagamento', '$dataBaixa', 
						 '$formaPgto', '$tipo', '$controle', '$centro_de_custo', '$status')";
					
				}
				$resultInsertContasReceberDigitalOnline = mysql_query($queryInsertContasReceberDigitalOnline)or die(mysql_error());	
			
		}
	}

	}
	
	
	
	
		