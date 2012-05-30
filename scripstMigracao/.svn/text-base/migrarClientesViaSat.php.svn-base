<?php
	
	ini_set('max_execution_time','200000');
	
	$conexaoLocal = mysql_pconnect("localhost","root","root");

	$limparAssinaturas = "truncate table db_opus.assinaturas_ctv";
	$resultLimparCliente = mysql_query($limparAssinaturas, $conexaoLocal);
	
	$limpaClientes = "DELETE FROM db_opus.clientes WHERE EMPRESA_ID='4'";
	$resultLimpaCliente = mysql_query($limpaClientes, $conexaoLocal);
	
	$queryCliente = "SELECT * FROM db_viasat.clientes"; 
	$resultCliente = mysql_query($queryCliente,$conexaoLocal)or die(mysql_error());
	$nRowClientes = mysql_num_rows($resultCliente);
	
	while ( $rowCliente = mysql_fetch_assoc($resultCliente) ) 
	{
	
		$cod_cliente = $rowCliente['cod_cliente'];
		$proposta = $rowCliente['proposta'];
		$n_contrato = $rowCliente['n_contrato'];
				
		if($rowCliente['tipo_pessoa'] == 'Pessoa Física')
		{
			$tipo_pessoa = 'F';
		}else{
			$tipo_pessoa = 'J';
		}
		
		$nome_razao = strtoupper($rowCliente['nome_razao']);
		$cpf_cnpj = $rowCliente['cpf_cnpj'];
		$rg = $rowCliente['rg'];
		$rg_orgao = $rowCliente['rg_orgao'];
		
		echo $rowCliente['data_nascimento'];
		$dia = substr($rowCliente['data_nascimento'], 0, 2);
		$mes = substr($rowCliente['data_nascimento'], 3, 2);
		$ano = substr($rowCliente['data_nascimento'], 6, 4);
		
		$data_nascimento = $ano.'-'.$mes.'-'.$dia;
		
		if($data_nascimento == '--')
		{
			$data_nascimento = '0000-00-00';
		}else if($data_nascimento == '--0')
		{
			$data_nascimento = '0000-00-00';
		}else if($data_nascimento == '-00-00')
		{
			$data_nascimento = '0000-00-00';
		}
		
		$sexo = $rowCliente['sexo'];
		
	
		$estado_civil = $rowCliente['estado_civil'];
		$profissao = $rowCliente['profissao'];
		$filiacao = $rowCliente['filiacao'];
		$email = $rowCliente['email'];
		$telefone1 = $rowCliente['telefone1'];
		$telefone2 = $rowCliente['telefone2'];
		$telefone3 = $rowCliente['telefone3'];
		$cep = $rowCliente['cep'];
		$endereco = strtoupper($rowCliente['endereco']);
		$numero = $rowCliente['numero'];
		$complemento = strtoupper($rowCliente['complemento']);
		$bairro = strtoupper($rowCliente['bairro']);
		$uf = strtoupper($rowCliente['uf']);
		$cidade = strtoupper($rowCliente['cidade']);
		$ponto_referencia = $rowCliente['ponto_referencia']; 
		$tv_por_assinatura = $rowCliente['tv_por_assinatura'];
		$pacote_escolhido = $rowCliente['pacote_escolhido'];
		$pacote_add_fut_clube_azul = substr($rowCliente['pacote_add_fut_clube_azul'], 0,1);
		$pacote_add_fut_clube_verde = substr($rowCliente['pacote_add_fut_clube_verde'], 0,1);
		$pacote_add_fut_clube_amarelo = substr($rowCliente['pacote_add_fut_clube_amarelo'], 0,1);
		$pacote_add_fut_clube_branco = substr($rowCliente['pacote_add_fut_clube_branco'], 0,1);
		$pacote_add_fut_clube_preto = substr($rowCliente['pacote_add_fut_clube_preto'], 0,1);
		$pacote_add_fut_clube_vermelho = substr($rowCliente['pacote_add_fut_clube_vermelho'], 0,1);
		$pacote_adult_sexyhot = substr($rowCliente['pacote_adult_sexyhot'], 0,1);
		$pacote_adult_playboy = substr($rowCliente['pacote_adult_playboy'], 0,1);
		$pacote_adult_play_sexy = substr($rowCliente['pacote_adult_play_sexy'], 0,1);
		$pacote_adult_forman = substr($rowCliente['pacote_adult_forman'], 0,1);
		$pacote_adult_combate = substr($rowCliente['$pacote_adult_combate'], 0,1);
		$outros_produtos = $rowCliente['pacote_adult_combate'];
		$plano_escolhido = $rowCliente['plano_escolhido'];
		$plano_extra = substr($rowCliente['plano_extra'], 0,1);
		$dia_pagamento = $rowCliente['dia_pagamento'];
		$debito_em_conta = substr($rowCliente['debito_em_conta'],0,1);
		$banco = $rowCliente['banco'];
		$agencia = $rowCliente['agencia'];
		$conta_corrente = $rowCliente['conta_corrente'];
		$titular = $rowCliente['titular'];
		$cpf_titular = $rowCliente['cpf_titular'];
		$confirmacao_via_telefone = substr($rowCliente['confirmacao_via_telefone'], 0, 1); 
		$autorizacao_confirmacao = substr($rowCliente['autorizacao_confirmacao'], 0, 1); 
		$esta_ciente = substr($rowCliente['esta_ciente'],0,1);
		$nome_amigo = $rowCliente['nome_amigo'];
		$telefone_amigo = $rowCliente['telefone_amigo'];
		$aprovacao_credito = $rowCliente['aprovacao_credito'];
		$confirmacao_cliente = $rowCliente['confirmacao_cliente'];
		$documentacao = $rowCliente['documentacao'];
		$v_sales = $rowCliente['v_sales'];
		$consultor_parceiro = $rowCliente['consultor_parceiro']; 
		$empresa = $rowCliente['empresa'];
		$andamento = $rowCliente['andamento'];
		$origem = $rowCliente['origem'];
	
		//CADASTRAR CLIENTE NA EMPRESA 4
		$queryOpus = "INSERT INTO db_opus.clientes (
						EMPRESA_ID ,STATUS_2 ,TIPO_PESSOA ,DOC_CPF_CNPJ ,DOC_RG_INSC_ESTADUAL ,TRATAMENTO ,NOME_RAZAO ,NOME_FANTASIA, CONTATO ,SEXO ,
						DATA_NASCIMENTO ,TELEFONE1 ,TELEFONE2 ,CELULAR1 ,DATA_CADASTRO ,EMAIL , COMO_NOS_CONHECEU, OBS, TRANSPORTADORA_PADRAO,  FORMA_PGTO_PADRAO,  TABELA_PRECO_PADRAO)VALUES 
			('4', 'ATIVO', '$tipo_pessoa', '$cpf_cnpj', '$rg', 'Sr.(a)', '$nome_razao', '', 
			'$nome_razao', '$sexo', '$data_nascimento', '$telefone1', '$telefone2', '$telefone3', '2012-04-29', '$email','','','','','' )";			
		
		  $resultOpus = mysql_query($queryOpus, $conexaoLocal)or die(mysql_error());
		  
		  //PEGA O COD DO CLIENTE
		  $queryCodCliente = "SELECT * FROM db_opus.clientes ORDER by ID DESC";
		  $resultCodCliente = mysql_query($queryCodCliente);
		  $rowCodCliente = mysql_fetch_assoc($resultCodCliente);
		  $codCliente = $rowCodCliente['ID'];
		  
		  //CADASTRA ENDEREÇOS DO CLIENTE
		  $queryOpus2 = "INSERT INTO db_opus.enderecos_principais (
						FIADORES_ID ,CLIENTES_ID ,CEP ,ENDERECO ,NUMERO ,COMPLEMENTO ,BAIRRO ,
						CIDADE ,UF ,PAIS ,REFERENCIA)VALUES (
						0, '$codCliente', '$cep', '$endereco', '$numero', '$complemento', '$bairro', '$cidade', 'PE', 'BRASIL', '$ponto_referencia')";	
		
		 $resultOpus2 = mysql_query($queryOpus2, $conexaoLocal)or die(mysql_error());
		 
		 
		 $queryEndEntre = "INSERT INTO db_opus.enderecos_entrega (
						CLIENTES_ID ,CEP ,ENDERECO ,NUMERO ,COMPLEMENTO ,BAIRRO ,
						CIDADE ,UF ,PAIS ,REFERENCIA)VALUES (
						 '$codCliente', '$cep', '$endereco', '$numero', '$complemento', '$bairro', '$cidade', 'PE', 'BRASIL', '$ponto_referencia')";	
		
		 $resultEndEntre = mysql_query($queryEndEntre, $conexaoLocal)or die(mysql_error());
		 
		 $queryEndCob = "INSERT INTO db_opus.enderecos_cobranca (
						CLIENTES_ID ,CEP ,ENDERECO ,NUMERO ,COMPLEMENTO ,BAIRRO ,
						CIDADE ,UF ,PAIS ,REFERENCIA)VALUES (
						 '$codCliente', '$cep', '$endereco', '$numero', '$complemento', '$bairro', '$cidade', 'PE', 'BRASIL', '$ponto_referencia')";	
		
		 $resultEndCob = mysql_query($queryEndCob, $conexaoLocal)or die(mysql_error());

		 //CADASTRA ASSINATURAS
		 $queryAssinatura = "INSERT INTO db_opus.assinaturas_ctv (" .
		 		"EMPRESA_ID, CLIENTE_ID, PROPOSTA, CONTRATO, TV_POR_ASSINATURA, PACOTE_ADD_FUT_CLUBE_AZUL, PACOTE_ADD_FUT_CLUBE_VERDE, " .
		 		"PACOTE_ADD_FUT_CLUBE_AMARELO, PACOTE_ADD_FUT_CLUBE_BRANCO, PACOTE_ADD_FUT_CLUBE_PRETO, PACOTE_ADD_FUT_CLUBE_VERMELHO, " .
		 		"PACOTE_ADULT_SEXYHOT, PACOTE_ADULT_PLAYBOY, PACOTE_ADULT_FORMAN, PACOTE_ADULT_COMBATE, OUTROS_PRODUTOS, " .
		 		"PLANO_ESCOLHIDO, PONTO_EXTRA, DIA_PAGAMENTO, DEBITO_EM_CONTA, BANCO, AGENCIA, CONTA_CORRENTE, TITULAR, CPF_TITULAR, " .
		 		"CONFIRMACAO_TELEFONE, AUTORIZACAO_CONFIRMACAO, ESTA_CIENTE, NOME_AMIGO, TELEFONE_AMIGO, APROVACAO_CREDITO, " .
		 		"COFNIRMACAO_CLIENTE, DOCUMENTACAO, V_SALES, CONSULTOR_PARCEIRO, EMPRESA, ANDAMENTO, ORIGEM) VALUES " .
		 		"(4, '$codCliente', '$proposta', '$n_contrato', '$tv_por_assinatura', '$pacote_add_fut_clube_azul', '$pacote_add_fut_clube_verde', " .
		 		"'$pacote_add_fut_clube_amarelo', '$pacote_add_fut_clube_branco', '$pacote_add_fut_clube_preto', '$pacote_add_fut_clube_vermelho', " .
		 		"'$pacote_adult_sexyhot', '$pacote_adult_playboy', '$pacote_adult_forman', '$pacote_adult_combate', '$outros_produtos', " .
		 		"'$plano_escolhido', '$plano_extra', '$dia_pagamento', '$debito_em_conta', '$banco', '$agencia', '$conta_corrente', '$titular', '$cpf_titular', " .
		 		"'$confirmacao_via_telefone', '$autorizacao_confirmacao', '$esta_ciente', '$nome_amigo', '$telefone_amigo', '$aprovacao_credito', " .
		 		"'$confirmacao_cliente', '$documentacao', '$v_sales', '$consultor_parceiro', '$empresa', '$andamento', '$origem')";
		 		
		 		$resultAssinatura  = mysql_query($queryAssinatura, $conexaoLocal)or die(mysql_error());
	}
?>
