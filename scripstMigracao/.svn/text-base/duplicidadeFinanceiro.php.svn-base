<?php

	ini_set('max_execution_time','200000');

	$conexaoRemota = mysql_pconnect("localhost","root","37261827");
	mysql_select_db("radius",$conexaoRemota);
	
	$queryRadius = "SELECT id, cliente,
		STATUS , emissao, nNumero, count( id )
		FROM contasapagar
		GROUP BY nNumero
		HAVING count( id ) >1";
	$resultRadius = mysql_query($queryRadius,$conexaoRemota);
	
	
	while ($rowRadius = mysql_fetch_assoc($resultRadius))
	{
		
		$nNumero = $rowRadius['nNumero'];
		
		$queryPesquisar = "SELECT * FROM contasapagar WHERE nNumero = '$nNumero'";
		$resultPesquisar = mysql_query($queryPesquisar,$conexaoRemota);
		while($rowPesquisar = mysql_fetch_assoc($resultPesquisar))
		{
			$ID = $rowPesquisar['id'];
			
						
			

					$queryPesquisarDuplicidade = "SELECT * FROM contasapagar ORDER by nNumero DESC";
					$resultPesquisarDuplicidade	= mysql_query($queryPesquisarDuplicidade, $conexaoRemota);
					$rowPesquisa = mysql_fetch_assoc($resultPesquisarDuplicidade);
					$novoNumero = $rowPesquisa['nNumero']+1;
					
					$queryUpdateNumero = "UPDATE contasapagar SET nNumero = '$novoNumero' WHERE id ='$ID'";
					$resultUpdateNumero = mysql_query($queryUpdateNumero,$conexaoRemota);
						
		
		
			
		}
		
	}