<?php

	ini_set('max_execution_time','200000');

	$conexaoRemota = mysql_pconnect("localhost","root","root");
	
	$limparOSE = "truncate table db_opus.ose";
	$resultLimparOSE = mysql_query($limparOSE, $conexaoRemota);
		
	$queryRadius = "SELECT * FROM radius.tb_chamados ORDER by id_chamado ASC";
	$resultRadius = mysql_query($queryRadius,$conexaoRemota);
	
	while ($rowRadius = mysql_fetch_assoc($resultRadius))
	{	 
			 
		$id_chamado = $rowRadius['id_chamado'];
		$tipo = $rowRadius['tipo'];
		
		if($rowRadius['dataEx'] == '0000-00-00')
		{
			$dataEx = $rowRadius['data_fechamento'].' '.'00:00:00';	
		}
		else
		{
			$dataEx = $rowRadius['dataEx'].' '.'00:00:00';	
		}
		
		$turno = $rowRadius['turno'];
		$quem_abriu = $rowRadius['quem_abriu'];
		$material = $rowRadius['material'];
		$base = $rowRadius['base'];
		if($rowRadius['data_abertura'] == '')
		{
			$data_abertura = $dataEx;	
		}else
		{
			$data_abertura = $rowRadius['data_abertura'];
		}
		
		$horario = $rowRadius['horario'];
				
		
		
		if(is_numeric(trim($rowRadius['codCliente'])))
		{
			$codCliente = trim($rowRadius['codCliente']);
		}else
		{
			$queryBuscaCliente = "SELECT * FROM db_opus.clientes WHERE NOME_RAZAO LIKE '%$codCliente%'";
			$resultBuscaCliente = mysql_query($queryBuscaCliente);
			$nRowBuscaCliente = mysql_num_rows($resultBuscaCliente);
			if($nRowBuscaCliente >0)
			{
				$rowBuscaCliente = mysql_fetch_assoc($resultBuscaCliente);
				$codCliente = trim($nRowBuscaCliente['ID']);
			}else
			{
				$codCliente = '000';
			}
		}
		
				
		$contato = $rowRadius['contato'];
		$bairro = $rowRadius['bairro'];
		$endereco = $rowRadius['endereco'];
		$referencia = str_replace("'", "", $rowRadius['referencia']);
		$problema = str_replace("'", "", $rowRadius['problema']);
		$motivo = $rowRadius['motivo'];
		$status = $rowRadius['status'];
		$tipoEncaminhamento = $rowRadius['tipoEncaminhamento'];
		
		if($rowRadius['dataAndamento'] == '0000-00-00')
		{
			$dataAndamento = $dataEx;			
		}
		if($rowRadius['dataAndamento'] == '')
		{
			$dataAndamento = $dataEx;			
		}
		else
		{
			$dataAndamento = $rowRadius['dataAndamento'];
		}

		
		$data_fechamento = $rowRadius['data_fechamento'];
		$hora_encaminhado = $rowRadius['hora_encaminhado'];
		$hora_fechado = $rowRadius['hora_fechado'];
		$conclusao = str_replace("'", "", $rowRadius['conclusao']);
		$estaemcasa = $rowRadius['estaemcasa'];
		$tecnico = $rowRadius['tecnico'];
		$prioridade = $rowRadius['prioridade'];
		$obs = $rowRadius['obs'];
		$tipo_servico = $rowRadius['tipo_servico'];
		$notafiscal = $rowRadius['notafiscal'];
		
		$EMPRESA_ID = 1;	
			
			
			//CADASTRANDO
			if($status == 'FECHADO')
			{
				$queryInsert = "INSERT INTO db_opus.ose (ID,EMPRESA_ID, CLIENTES_ID, TIPO, DATA_EX, DATA_ENCAMINHAMENTO, DATA_ABERTURA, DATA_FECHAMENTO, 
				TURNO, BASE, CONTATO, BAIRRO, ENDERECO, REFERENCIA, STATUS_2, TIPO_ENCAMINHAMENTO, MOTIVO, PROBLEMA, CONCLUSAO, AUSENTE, PRIORIDADE, 
				OBS, NOTA_FISCAL, TIPO_SERVICO, TECNICO, OPERADOR) VALUES 
				('$id_chamado','$EMPRESA_ID', '$codCliente', '$tipo', '$dataEx', '$dataAndamento', '$data_abertura', '$data_fechamento', '$turno', '$base', 
				'$contato', '$bairro', '$endereco', '$referencia', '$status', '$tipoEncaminhamento', '$motivo', '$problema', '$conclusao', 
				'$estaemcasa', '$prioridade', '$obs', '$notafiscal', '$tipo_servico', '$tecnico', '$quem_abriu')";
			}
			
			
			if($status == 'ABERTO')
			{
				$queryInsert = "INSERT INTO db_opus.ose (ID,EMPRESA_ID, CLIENTES_ID, TIPO, DATA_EX, DATA_ABERTURA,  
				TURNO, BASE, CONTATO, BAIRRO, ENDERECO, REFERENCIA, STATUS_2, TIPO_ENCAMINHAMENTO, MOTIVO, PROBLEMA, CONCLUSAO, AUSENTE, PRIORIDADE, 
				OBS, NOTA_FISCAL, TIPO_SERVICO, TECNICO, OPERADOR) VALUES 
				('$id_chamado','$EMPRESA_ID', '$codCliente', '$tipo', '$dataEx', '$data_abertura', '$turno', '$base', 
				'$contato', '$bairro', '$endereco', '$referencia', '$status', '$tipoEncaminhamento', '$motivo', '$problema', '$conclusao', 
				'$estaemcasa', '$prioridade', '$obs', '$notafiscal', '$tipo_servico', '$tecnico', '$quem_abriu')";
			}

			if($status == 'EXCLUIR')
			{
				
			}
			if($status == 'EM ANDAMENTO')
			{
				$queryInsert = "INSERT INTO db_opus.ose (ID,EMPRESA_ID, CLIENTES_ID, TIPO, DATA_EX, DATA_ENCAMINHAMENTO, DATA_ABERTURA,  
				TURNO, BASE, CONTATO, BAIRRO, ENDERECO, REFERENCIA, STATUS_2, TIPO_ENCAMINHAMENTO, MOTIVO, PROBLEMA, AUSENTE, PRIORIDADE, 
				OBS, NOTA_FISCAL, TIPO_SERVICO, TECNICO, OPERADOR) VALUES 
				('$id_chamado','$EMPRESA_ID', '$codCliente', '$tipo', '$dataEx', '$dataAndamento', '$data_abertura', '$turno', '$base', 
				'$contato', '$bairro', '$endereco', '$referencia', '$status', '$tipoEncaminhamento', '$motivo', '$problema',  
				'$estaemcasa', '$prioridade', '$obs', '$notafiscal', '$tipo_servico', '$tecnico', '$quem_abriu')";
			}

			
			echo $id_chamado.'</br>';
			$resultInsert = mysql_query($queryInsert)or die(mysql_error());			
			
		}
		
		

		
	