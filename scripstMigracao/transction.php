<?php
	$conexaoRemota = mysql_pconnect("localhost","root","root");
	
	mysql_query("SET AUTOCOMMIT=0",$conexaoRemota);
	mysql_query("START TRANSACTION",$conexaoRemota);
	
	
	$resultado = true;
	
	//CADASTRA CLIENTE
	$query0 = "INSERT INTO trans.cliente (ID, NOME) VALUES (NULL, 'MARCONI')";
	if(!$result0 = mysql_query($query0,$conexaoRemota))
	{		
		$resultado = false;
	}
	
	//CADASTRA CLIENTE
	$query0 = "INSERT INTO trans.cliente (ID, NOME) VALUES (NULL, 'JULIANO',)";
	if(!$result0 = mysql_query($query0,$conexaoRemota))
	{		
		$resultado = false;
	}
	
	
	
	
	
	if($resultado)
	{
		//mysql_query("COMMIT",$conexaoRemota);	
	}else{
		mysql_query("ROOLBACK",$conexaoRemota);
	}
	
	
	
	
?>