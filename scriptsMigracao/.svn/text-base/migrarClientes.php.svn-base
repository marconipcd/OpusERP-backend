<?php
	ini_set('max_execution_time','200000');

	
	//$conexaoRemota = mysql_pconnect("192.168.20.13","root","37261827");
	$conexaoLocal = mysql_pconnect("localhost","root","root");

	$limparCliente = "truncate table db_opus.clientes";
	$limparEndPrincipais = "truncate table db_opus.enderecos_principais";
	$limparEndEntrega = "truncate table db_opus.enderecos_entrega";
	$limparEndCobranca = "truncate table db_opus.enderecos_cobranca";
	$limparDocClientes = "truncate table db_opus.doc_clientes";
	
	$resultLimparCliente = mysql_query($limparCliente, $conexaoLocal);
	$resultEndPrincipais= mysql_query($limparEndPrincipais, $conexaoLocal);
	$resultEndEntrega  = mysql_query($limparEndEntrega, $conexaoLocal);
	$resultEndCobranca = mysql_query($limparEndCobranca, $conexaoLocal);
	$resultDocClientes = mysql_query($limparDocClientes, $conexaoLocal);
	
	$limparClientesBloqueado = "truncate table db_opus.clientes_bloqueado";
	$resultLimparCliente = mysql_query($limparClientesBloqueado, $conexaoLocal)or die(mysql_error());
	
	$queryRadius = "SELECT * FROM radius.pessoa ORDER by codigoPessoa"; 
	$resultRadius = mysql_query($queryRadius,$conexaoLocal)or die(mysql_error());
	
	
	while ($rowRadius = mysql_fetch_assoc($resultRadius))
	{
		
		//VARIAVEIS CLIENTE
		$ID = $rowRadius['codigoPessoa'];
		$plano = $rowRadius['plano'];
		$planoCliente = $rowRadius['planoCliente'];
		
		if($rowRadius['grupo1'] == '65')
		{
			//INTERNET - ADEMIR DE SOUZA PINTO FILHO
			 $EMPRESA_ID = '1';	
		}
		if($rowRadius['grupo1'] == '56')
		{
			//INFORMATICA - ADEMIR DE SOUZA PINTO FILHO
			 $EMPRESA_ID = '2';	
		}
		if($rowRadius['grupo1'] == '64')
		{
			//GRAFICA - ADEMIR DE SOUZA PINTO FILHO
			 $EMPRESA_ID = '3';	
		}
		
		 $STATUS_2= $rowRadius['status'];
		
		if(strlen($rowRadius['cpf']) == 11)
		{
		 	$TIPO_PESSOA = 'F';
		}else
		{
		 	$TIPO_PESSOA = 'J';
		}
		
		
		 $DOC_CPF_CNPJ= $rowRadius['cpf'];
		 $DOC_RG_INSC_ESTADUAL= $rowRadius['rg'];
		 $TRATAMENTO= $rowRadius['tratamento'];
		 $NOME_RAZAO= strtoupper($rowRadius['textoNome']);
		 $CONTATO= strtoupper($rowRadius['contato']);
		 $SEXO= strtoupper($rowRadius['sexo']);
		
		 
		 
		 $DATA_NAsc = str_replace('/', '', trim($rowRadius['dataNascimento']));
		 $DATA_NAsc = str_replace('_', '', trim($DATA_NAsc));
		 
		 $dia = substr($DATA_NAsc, 0, 2);
		 $mes = substr($DATA_NAsc, 2, 2);
		 $ano = substr($DATA_NAsc, 4, 4);
		
		 $DATA_NASCIMENTO = $ano.'-'.$mes.'-'.$dia;
		 
		
		 
		 if($DATA_NASCIMENTO == '--')
		 {
		 	$DATA_NASCIMENTO = '1800-01-01';
		 }
		 
		 if($DATA_NASCIMENTO == '-00-00')
		 {
		 	$DATA_NASCIMENTO = '1800-01-01';
		 }
		 
		 if($DATA_NASCIMENTO == '-0-00')
		 {
		 	$DATA_NASCIMENTO = '1800-01-01';
		 }
		
		 if($DATA_NASCIMENTO == '-1-00')
		 {
		 	$DATA_NASCIMENTO = '1800-01-01';
		 }
				
			
		
		 
		 $TELEFONE1= $rowRadius['telefone'];
		 $TELEFONE2= $rowRadius['telefone2'];
		 $CELULAR1= $rowRadius['celular1'];
		 $CELULAR2= $rowRadius['celular2'];
		 $DATA_CADASTRO= $rowRadius['data_cadastro'];
		 $EMAIL= $rowRadius['email'];
		 $MSN= $rowRadius['msn'];
		
		 //VARIAVEIS ENDERECO
		 $CEP = str_replace('-', '', $rowRadius['codigoCep']);
		 $CEP = str_replace(' ', '', $CEP);
		 $ENDERECO= $rowRadius['textoEndereco'];
		 $NUMERO= $rowRadius['numero'];
		 $COMPLEMENTO= $rowRadius['complemento'];
		 $BAIRRO= $rowRadius['bairro'];
		
		if($rowRadius['textoCidade'] == 'BJM')
		{
		 	$CIDADE= 'BELO JARDIM';
		}else
		{
		 	$CIDADE=$rowRadius['textoCidade'];
		}
		
		 $UF= $rowRadius['ufEstado'];
		 $PAIS= 'BRASIL';
		 $REFERENCIA= $rowRadius['referencia'];
		
		//VARIAVEIS DOCS
		 $COMPOVANTE_ENDERECO = $rowRadius['comprovanteenderecodoc'];
		 $RG_CONTRATO_SOCIAL= $rowRadius['rgcontratosocialdoc'];
		 $CPF_CNPJ= $rowRadius['cpfcnpjdoc'];
		 $CONTRATO_ASSINADO= $rowRadius['contratoassinadodoc'];
		 	 	 	
		
		//echo $DATA_NASCIMENTO.'<br/>';
		$queryOpus = "INSERT INTO db_opus.clientes (
						ID ,CATEGORIAS_ID, EMPRESA_ID ,STATUS_2 ,TIPO_PESSOA ,DOC_CPF_CNPJ ,DOC_RG_INSC_ESTADUAL ,TRATAMENTO ,NOME_RAZAO ,NOME_FANTASIA, CONTATO ,SEXO ,
						DATA_NASCIMENTO ,TELEFONE1 ,TELEFONE2 ,CELULAR1 ,CELULAR2 ,DATA_CADASTRO ,EMAIL ,MSN, COMO_NOS_CONHECEU, OBS, TRANSPORTADORA_PADRAO,  FORMA_PGTO_PADRAO,  TABELA_PRECO_PADRAO)VALUES 
			('$ID', '1','$EMPRESA_ID', '$STATUS_2', '$TIPO_PESSOA', '$DOC_CPF_CNPJ', '$DOC_RG_INSC_ESTADUAL', '$TRATAMENTO', '$NOME_RAZAO', '', 
			'$CONTATO', '$SEXO', '$DATA_NASCIMENTO', '$TELEFONE1', '$TELEFONE2', '$CELULAR1', '$CELULAR2', '$DATA_CADASTRO', '$EMAIL', '$MSN','','','','','' )";
		
			
		
		  $resultOpus = mysql_query($queryOpus, $conexaoLocal)or die(mysql_error());

		//mysql_select_db("db_opus", $conexaoLocal);
		$queryOpus2 = "INSERT INTO db_opus.enderecos_principais (
						FIADORES_ID ,CLIENTES_ID ,CEP ,ENDERECO ,NUMERO ,COMPLEMENTO ,BAIRRO ,
						CIDADE ,UF ,PAIS ,REFERENCIA)VALUES (
						0, '$ID', '$CEP', '$ENDERECO', '$NUMERO', '$COMPLEMENTO', '$BAIRRO', '$CIDADE', '$UF', '$PAIS', '$REFERENCIA')";
		
		
		
		 $resultOpus2 = mysql_query($queryOpus2, $conexaoLocal)or die(mysql_error());
		 
		 
		 $queryEndEntre = "INSERT INTO db_opus.enderecos_entrega (
						CLIENTES_ID ,CEP ,ENDERECO ,NUMERO ,COMPLEMENTO ,BAIRRO ,
						CIDADE ,UF ,PAIS ,REFERENCIA)VALUES (
						'$ID', '$CEP', '$ENDERECO', '$NUMERO', '$COMPLEMENTO', '$BAIRRO', '$CIDADE', '$UF', '$PAIS', '$REFERENCIA')";
		
		
		
		 $resultEndEntre = mysql_query($queryEndEntre, $conexaoLocal)or die(mysql_error());
		 
		 $queryEndCob = "INSERT INTO db_opus.enderecos_cobranca (
						CLIENTES_ID ,CEP ,ENDERECO ,NUMERO ,COMPLEMENTO ,BAIRRO ,
						CIDADE ,UF ,PAIS ,REFERENCIA)VALUES (
						'$ID', '$CEP', '$ENDERECO', '$NUMERO', '$COMPLEMENTO', '$BAIRRO', '$CIDADE', '$UF', '$PAIS', '$REFERENCIA')";
		
		
		
		 $resultEndCob = mysql_query($queryEndCob, $conexaoLocal)or die(mysql_error());
		 
		 
		 $queryVerificaCEP = "SELECT * FROM db_opus.cep WHERE CEP LIKE '$CEP'";
		 $resultVerificaCEP =mysql_query($queryVerificaCEP, $conexaoLocal)or die(mysql_error());
		 $nRowVerificaP = mysql_num_rows($resultVerificaCEP);
		 if($nRowVerificaP == 0)
		 {
		 	$queryCadastrarCEP = "INSERT INTO  db_opus.cep (CEP ,ENDERECO ,BAIRRO ,CIDADE ,UF ,PAIS)
			VALUES ('$CEP',  '$ENDERECO',  '$BAIRRO',  '$CIDADE',  '$UF',  '$PAIS')";
		 	$resultCadastrarCEP = mysql_query($queryCadastrarCEP, $conexaoLocal)or die(mysql_error());
		 }
		 
		
		if($COMPOVANTE_ENDERECO != '1')
		{
		 	$COMPOVANTE_ENDERECO = '0';
		}
		
		if($RG_CONTRATO_SOCIAL != '1')
		{
		 	$RG_CONTRATO_SOCIAL = '0';
		}
		if($CPF_CNPJ != '1')
		{
		 	$CPF_CNPJ = '0';
		}
		if($CONTRATO_ASSINADO != '1')
		{
		 	$CONTRATO_ASSINADO = '0';
		}
		
		
		//mysql_select_db("db_opus", $conexaoLocal);
		
		$queryDoc = "INSERT INTO db_opus.doc_clientes (
				CLIENTES_ID ,COMPOVANTE_ENDERECO ,RG_CONTRATO_SOCIAL ,CPF_CNPJ ,CONTRATO_ASSINADO
				)VALUES ('$ID', '$COMPOVANTE_ENDERECO', '$RG_CONTRATO_SOCIAL', '$CPF_CNPJ', '$CONTRATO_ASSINADO')";
		 $resultDoc = mysql_query($queryDoc, $conexaoLocal)or die(mysql_error());
		
	
		if($planoCliente == 'bloqueado')
		{
			if($STATUS_2 == 'ATIVO')
			{
				
				
				$query2 = "SELECT * FROM acesso_cliente WHERE CLIENTES_ID=$ID";
				$result2 = mysql_query($query2);
				$nRow2 = mysql_num_rows($result2);
				if($nRow2 > 0)
				{
					$row2 = mysql_fetch_assoc($result2);
					$PLANOS_ACESSO_ID = $row2['PLANOS_ACESSO_ID'];
										
					$query1 = "INSERT INTO db_opus.clientes_bloqueado (CLIENTES_ID, PLANOS_ACESSO_ID) VALUES ($ID,$PLANOS_ACESSO_ID)";
					$result1 = mysql_query($query1)or die (mysql_error());
				}
			}
		}
		
	
	}

	

