<?php

	ini_set('max_execution_time','20000000');

	$conexaoRemota = mysql_pconnect("localhost","root","root");
	
	$limparACESSO = "truncate table db_opus.acesso_cliente";
	$resultLimparACESSO = mysql_query($limparACESSO, $conexaoRemota);
		
	$limparRADCHEK= "truncate table db_opus.radcheck";
	$resultLimparRADCHECK = mysql_query($limparRADCHEK, $conexaoRemota);
	
	$limparRADUSERGROUP = "truncate table db_opus.radusergroup";
	$resultLimparRADUSERGROUP = mysql_query($limparRADUSERGROUP, $conexaoRemota);

	$queryRadius = "SELECT * FROM radius.pessoa WHERE status = 'ATIVO' AND grupo1 = '65' ORDER by codigoPessoa";
	$resultRadius = mysql_query($queryRadius,$conexaoRemota)or die(mysql_error());
	
	
	while ($rowRadius = mysql_fetch_assoc($resultRadius))
	{
		//---PEGA CLIENTE_ID;
		$CLIENTES_ID = $rowRadius['codigoPessoa'];
		
		//--PEGA INTERFACE_ID;
		//RADIO
		if($rowRadius['acesso'] == 'RADIO')
		{
			$INTERFACE_ID = 3;	
			
			//---PEGA PLANO_ACESSO;
			if($rowRadius['plano'] == '150k')
			{
				$PLANO_ID = 7;			
			}
			if($rowRadius['plano'] == '300k')
			{
				$PLANO_ID = 8;			
			}
			if($rowRadius['plano'] == '600k')
			{
				$PLANO_ID = 9;			
			}
			if($rowRadius['plano'] == '1M')
			{
				$PLANO_ID = 10;			
			}
			if($rowRadius['plano'] == '2M')
			{
				$PLANO_ID = 11;			
			}
			
		
		}
		//RADIO DIRETO
		if($rowRadius['acesso'] == 'RADIO' && $rowRadius['plano'] == '1M EMPRESA')
		{
			$INTERFACE_ID = 2;	
			$PLANO_ID = 22;	
		}
		if($rowRadius['acesso'] == 'RADIO' && $rowRadius['plano'] == '2M EMPRESA')
		{
			$INTERFACE_ID = 2;	
			$PLANO_ID = 23;			
			
		}
		if($rowRadius['acesso'] == 'RADIO' && $rowRadius['plano'] == '1M DEDICADO')
		{
			$INTERFACE_ID = 2;	
			$PLANO_ID = 24;	
		}
		
		
		
		//CABO
		if($rowRadius['acesso'] == 'CABO')
		{
			$INTERFACE_ID = 5;

			//---PEGA PLANO_ACESSO;
			if($rowRadius['plano'] == '150k')
			{
				$PLANO_ID = 17;			
			}
			if($rowRadius['plano'] == '300k')
			{
				$PLANO_ID = 18;			
			}
			if($rowRadius['plano'] == '600k')
			{
				$PLANO_ID = 19;			
			}
			if($rowRadius['plano'] == '1M')
			{
				$PLANO_ID = 20;			
			}
			if($rowRadius['plano'] == '2M')
			{
				$PLANO_ID = 21;			
			}			
			
		}
		
		//CABO DIRETO
		if($rowRadius['acesso'] == 'CABO' && $rowRadius['plano'] == '1M EMPRESA')
		{
			$INTERFACE_ID = 6;	
			$PLANO_ID = 25;	
		}
		if($rowRadius['acesso'] == 'CABO' && $rowRadius['plano'] == '2M EMPRESA')
		{
			$INTERFACE_ID = 6;	
			$PLANO_ID = 26;			
			
		}
		if($rowRadius['acesso'] == 'CABO' && $rowRadius['plano'] == '1M DEDICADO')
		{
			$INTERFACE_ID = 6;	
			$PLANO_ID = 27;	
		}
		
		
		
		//---DEFINE SERVIDOR
		$SERVIDOR_ID = 1;
		
		//---PEGA BASE_ID
		if($rowRadius['antena'] == 'BASE01')
		{
			$BASE_ID = 1;	
		}
		if($rowRadius['antena'] == 'BASE01 AP01')
		{
			$BASE_ID = 2;	
		}  
		if($rowRadius['antena'] == 'BASE01 AP02')
		{
			$BASE_ID = 3;	
		}
		if($rowRadius['antena'] == 'BASE02')
		{
			$BASE_ID = 4;	
		}
		if($rowRadius['antena'] == 'BASE02 CABO')
		{
			$BASE_ID = 5;	
		}
		if($rowRadius['antena'] == 'BASE03')
		{
			$BASE_ID = 6;	
		}
		if($rowRadius['antena'] == 'BASE03 AP01')
		{
			$BASE_ID = 7;	
		}
		if($rowRadius['antena'] == 'BASE03 AP02')
		{
			$BASE_ID = 8;	
		}
		if($rowRadius['antena'] == 'BASE04')
		{
			$BASE_ID = 9;	
		}
		if($rowRadius['antena'] == 'BASE04 CABO')
		{
			$BASE_ID = 10;	
		}
		if($rowRadius['antena'] == 'BASE05')
		{
			$BASE_ID = 11;	
		}
		if($rowRadius['antena'] == 'BASE06')
		{
			$BASE_ID = 12;	
		}
		if($rowRadius['antena'] == 'BASE07')
		{
			$BASE_ID = 13;	
		}
		if($rowRadius['antena'] == 'BASE08')
		{
			$BASE_ID = 14;	
		}
		if($rowRadius['antena'] == 'BASE08 CABO')
		{
			$BASE_ID = 15;	
		}
		if($rowRadius['antena'] == 'BASE09')
		{
			$BASE_ID = 16;	
		}
		if($rowRadius['antena'] == 'BASE10')
		{
			$BASE_ID = 17;	
		}
		if($rowRadius['antena'] == 'BASE10 CABO')
		{
			$BASE_ID = 18;	
		}
		if($rowRadius['antena'] == 'CABO')
		{
			$BASE_ID = 19;	
		}
		if($rowRadius['antena'] == '* CABO DESATIVADO *')
		{
			$BASE_ID = 20;	
		}
		if($rowRadius['antena'] == '* DESATIVADO *')
		{
			$BASE_ID = 21;	
		}
		
		
		//---PEGAR MATERIAL_ACESSO_ID
		$queryMaterialAcessoID = "SELECT * FROM  radius.produtos_clientes WHERE id_cliente = '$CLIENTES_ID'";
		$resultMaterialAcessoID = mysql_query($queryMaterialAcessoID)or die(mysql_error());
		$nRowMaterialAcessoID = mysql_num_rows($resultMaterialAcessoID);
		if($nRowMaterialAcessoID > 0)
		{
			$rowMaterialAcessoID = mysql_fetch_assoc($resultMaterialAcessoID);
			$MATERIAL_ACESSO_ID = $rowMaterialAcessoID['id_produto'];	
		}else
		{
			$MATERIAL_ACESSO_ID = 0;
		}
		 
		
		//---CONTRATOS_ACESSO_ID
		if($rowRadius['regime'] == 'COMODATO')
		{
			$CONTRATOS_ACESSO_ID = 2;	
		}
		else if($rowRadius['regime'] == 'PROPRIO')
		{
			$CONTRATOS_ACESSO_ID = 1;	
		}else if($rowRadius['tipoContrato'] == 'GRATIS')
		{
			$CONTRATOS_ACESSO_ID = 3;
		}else{
			$CONTRATOS_ACESSO_ID = 0;	
		}
		
		
		
		//---DEFINE EMPRESA_ID
		$EMPRESA_ID = 1;
		
		//---PEGA USUARIO;
		$queryLogin = "SELECT * FROM  radius.radcheck WHERE cliente = '$CLIENTES_ID'";
		$resultLogin = mysql_query($queryLogin)or die(mysql_error());
		$nRowLogin = mysql_num_rows($resultLogin);
		if($nRowLogin > 0)
		{
			$rowLogin = mysql_fetch_assoc($resultLogin);
			$LOGIN = $rowLogin['username'];	
			
			//---PEGA SENHA;
			$querySenha = "SELECT * FROM  radius.radcheck WHERE username LIKE '$LOGIN' AND attribute LIKE 'Password'";
			$resultSenha = mysql_query($querySenha)or die(mysql_error());
			$nRowSenha = mysql_num_rows($resultSenha);
			if($nRowSenha >0)
			{
				$rowSenha = mysql_fetch_assoc($resultSenha);
				$SENHA = $rowSenha['value'];
			}else
			{
				$SENHA = 'N_ENCONTRADO';
			}
			
			//---PEGA ENDERECO MAC
			$queryMac = "SELECT * FROM  radius.radcheck WHERE username LIKE '$LOGIN' AND attribute LIKE 'Calling-Station-ID'";
			$resultMac = mysql_query($queryMac)or die(mysql_error());
			$nRowMac = mysql_num_rows($resultMac);
			if($nRowMac > 0)
			{
				$rowMac = mysql_fetch_assoc($resultMac);
				$ENDERECO_MAC = $rowMac['value'];
			}else {
				$ENDERECO_MAC = 'N ENCONTRADO';
			}
			
			//---PEGA ENDERECO IP
			$queryIp = "SELECT * FROM  radius.radcheck WHERE username LIKE '$LOGIN' AND attribute LIKE 'Framed-IP-Address'";
			$resultIp = mysql_query($queryIp)or die(mysql_error());
			$nRowIp = mysql_num_rows($resultIp);
			if($nRowIp >0)
			{
				$rowIp = mysql_fetch_assoc($resultIp);
				$ENDERECO_IP = $rowIp['value'];
			}else{
				$ENDERECO_IP = 'N ENCONTRADO';
			}
			
			
			
		}else {
			$LOGIN = 'N ENCONTRADO';
			$SENHA = 'N_ENCONTRADO';
			$ENDERECO_MAC = 'N ENCONTRADO';
			$ENDERECO_IP = 'N ENCONTRADO';
		}
		 
		//---PEGA DATA_VENC_CONTRATO
		
		
		$DATA_VENC_CONTRATO = rtrim($rowRadius['vencContrato']);
		//---PEGA DATA_CADASTRO
		$DATA_CADASTRO = rtrim(substr($rowRadius['data_cadastro'], 10));
		if (!eregi("^[0-9]{4}-[0-9]{2}-[0-9]{2}$", rtrim($DATA_CADASTRO))) {
			$DATA_CADASTRO = date('Y-m-d');
		}
		
//		if($rowRadius['data_cadastro'] == '__/__/____')
//		{
//			$DATA_CADASTRO = date('Y-m-d');
//		}
//		
//		if($rowRadius['data_cadastro'] == '')
//		{
//			$DATA_CADASTRO = date('Y-m-d');
//		}
//		
		if($rowRadius['vencContrato'] == '__/__/____')
		{
			$queryDataVencUltimoBoleto = "SELECT * FROM contasapagar WHERE cliente = '$CLIENTES_ID' ORDER by DESC id LIMIT 0,1";
			$resultDataVencUltimoBoleto = mysql_query($queryDataVencUltimoBoleto);
			$nRowDataVencUltimoBoleto = mysql_num_rows($resultDataVencUltimoBoleto);
			if($nRowDataVencUltimoBoleto >0)
			{
				$rowDataVencUltimoBoleto = mysql_fetch_assoc($resultDataVencUltimoBoleto);
				$DATA_VENC_CONTRATO = $rowDataVencUltimoBoleto['vencimento'];
			}else
			{
				$DATA_VENC_CONTRATO = $DATA_CADASTRO;	
			}
		}
		
		if($rowRadius['vencContrato'] == '')
		{
			$queryDataVencUltimoBoleto = "SELECT * FROM contasapagar WHERE cliente = '$CLIENTES_ID' ORDER by DESC id LIMIT 0,1";
			$resultDataVencUltimoBoleto = mysql_query($queryDataVencUltimoBoleto);
			$nRowDataVencUltimoBoleto = mysql_num_rows($resultDataVencUltimoBoleto);
			if($nRowDataVencUltimoBoleto >0)
			{
				$rowDataVencUltimoBoleto = mysql_fetch_assoc($resultDataVencUltimoBoleto);
				$DATA_VENC_CONTRATO = $rowDataVencUltimoBoleto['vencimento'];
			}else
			{
				$DATA_VENC_CONTRATO = $DATA_CADASTRO;	
			}
			
			
		}
		
		//---PEGA STATUS
		$STATUS = 'ATIVO';
		
		if (!eregi("^[0-9]{4}-[0-9]{2}-[0-9]{2}$", rtrim($DATA_VENC_CONTRATO))) {
			if(eregi("^[0-9]{2}/[0-9]{2}/[0-9]{4}", rtrim($DATA_VENC_CONTRATO)))
			{
				//CONVERTER DATA;
				$data = $DATA_VENC_CONTRATO;
				$data_nova = implode(preg_match("~\/~", $data) == 0 ? "/" : "-", array_reverse(explode(preg_match("~\/~", $data) == 0 ? "-" : "/", $data)));
				$DATA_VENC_CONTRATO = rtrim($data_nova);
			}
		}
		
		
		//---CADASTRA NA TABELA ACESSO_CLIENTE OS CONTRATOS EXISTENTES
		$queryInsertAcessoCliente = "INSERT INTO db_opus.acesso_cliente (INTERFACE_ID, PLANOS_ACESSO_ID, SERVIDORES_ID, BASES_ID, MATERIAL_ACESSO_ID, 
		CONTRATOS_ACESSO_ID, EMPRESA_ID, CLIENTES_ID, LOGIN, SENHA, ENDERECO_IP, ENDERECO_MAC, DATA_VENC_CONTRATO, DATA_CADASTRO, STATUS_2) 
		VALUES ('$INTERFACE_ID', '$PLANO_ID', '$SERVIDOR_ID', '$BASE_ID', '$MATERIAL_ACESSO_ID', '$CONTRATOS_ACESSO_ID', '$EMPRESA_ID', 
		'$CLIENTES_ID', '$LOGIN', '$SENHA', '$ENDERECO_IP', '$ENDERECO_MAC', '$DATA_VENC_CONTRATO', '$DATA_CADASTRO', '$STATUS')"; 	 
		
		$resultInsertAcessoCliente = mysql_query($queryInsertAcessoCliente)or die(mysql_error());
		
		//---CADASTRA NA TABELA RADCHECK, USUARIO, SENHA, MAC
		$queryRadCheck = "INSERT INTO radcheck(username,attribute,op,value)VALUES
			('$LOGIN', 'Password','==','$SENHA'),
			('$LOGIN', 'Calling-Station-ID', '==' , '$ENDERECO_MAC'),
			('$LOGIN', 'Framed-IP-Address' , '==' , '$ENDERECO_IP')";
			
			$resultRadCheck = mysql_query($queryRadCheck);
		
			$nomePlanoRadius = $INTERFACE_ID.'_'.$rowRadius['plano'];
			//---CADASTRA NA TABELA RADUSERGROUP REFERENCIAS DE PLANOS
			$queryRadUserGroup = "INSERT INTO radusergroup(username,groupname,priority)VALUES
			('$LOGIN', '$nomePlanoRadius','1')";
			
			$resultRadUSerGroup = mysql_query($queryRadUserGroup);
	}