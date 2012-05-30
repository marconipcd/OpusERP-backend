<?php
	ini_set('max_execution_time','200000');

	$conexaoRemota = mysql_pconnect("localhost","root","root");
	
	$limparMATERIAL_ACESSO = "truncate table db_opus.material_acesso";
	$resultLimparACESSO = mysql_query($limparMATERIAL_ACESSO, $conexaoRemota);


	$queryRadius = "SELECT * FROM radius.estoque";
	$resultRadius = mysql_query($queryRadius,$conexaoRemota);
	
		while ($rowRadius = mysql_fetch_assoc($resultRadius))
		{
			$id = $rowRadius['id'];
			$desc_produto = $rowRadius['desc_produto']; 	
			$valor_produto = $rowRadius['valor_produto']; 	
			$qtd_em_estoque = $rowRadius['qtd_em_estoque']; 	
			$qtd_minima = $rowRadius['qtd_minima']; 	
			$total_valor = $rowRadius['total_valor']; 	
			
			$query = "INSERT INTO db_opus.material_acesso (
				ID, EMPRESA_ID ,NOME ,VALOR ,QTD_ESTOQUE ,QTD_MINIMA ,TOTAL_VALOR)
				VALUES ('$id', '1', '$desc_produto', '$valor_produto', '$qtd_em_estoque', '$qtd_minima', '$total_valor')";
			$result = mysql_query($query);
			
		}