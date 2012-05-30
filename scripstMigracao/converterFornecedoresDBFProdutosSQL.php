<?php

		$conexaoLocal = mysql_pconnect("localhost","root","root");
		
		$xml = simplexml_load_file("FORNECE_DBF.XML");

		
		
		
		//MIGRAR FORNECEDORES
		foreach($xml->ROW as $row2)
		{
			
				
		
			
			$query = "INSERT INTO db_opus.fornecedores (EMPRESA_ID, RAZAO_SOCIAL, FANTASIA, CNPJ, ENDERECO, BAIRRO, CIDADE, UF, CEP, FONE1, FONE2, FAX, 
			DTPVENDAS, EMAIL, HOME_PAGE, REPRESENTANTE, NOME_REPRESENTANTE, CONTATO_REPRESENTANTE, CIDADE_REPRESENTATE, UF_REPRESENTANTE, 
			FONE1_REPRESENTANTE, FONE2_REPRESENTANTE, FAX_REPRESENTANTE, CEL_REPRESENTANTE, DATA_CADASTRO) VALUES (2, '$row2->RAZAO', '$row2->FANTASIA', 
			'$row2->CNPJ', '$row2->ENDERECO', '$row2->BAIRRO', 
			'23', 'pe', '123', '123', '123', '123', '123', '123', '123', '123', '123', '123', '123', 'pe', '123', '123', '123', '123', '2012-04-16')";
	
			$result = mysql_query($query,$conexaoLocal) or die(mysql_error());
		}