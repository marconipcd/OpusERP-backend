<?php

	ini_set('max_execution_time','20000000');

	$conexaoRemota = mysql_pconnect("localhost","root","37261827");
	
	$COD_CLIENTE = $_GET['COD'];
	
	$query0 = "SELECT * 
	FROM  db_opus.contas_receber 
	WHERE  CLIENTES_ID =$COD_CLIENTE AND STATUS_2 !='ABERTO' ORDER by ID";

	$result0 = mysql_query($query0,$conexaoRemota)or die(mysql_error());
	
	$sequencia = 1;
	while($row0 = mysql_fetch_assoc($result0))
	{
						
		$ID = $row0['ID'];
		$CLIENTE_ID = $row0['CLIENTES_ID'];		
		
		for($i=0;$i <= 100000000;$i++)
			{
				$sequencia++;
				$digito2 = str_pad($sequencia, 6, "0", STR_PAD_LEFT);
				$nNumero = $CLIENTE_ID.$digito2;
				//PROCURA SE JÁ EXISTE ALGUM N_NUMERO IGUAL
				$query2 = "SELECT * FROM db_opus.contas_receber WHERE N_NUMERO=$nNumero";
				$result2 = mysql_query($query2);
				$nRow2 = mysql_num_rows($result2);
				if($nRow2 == 0)
				{
					break;
				}
			}
		
		echo $nNumero.'</br>';	
		
		
		$query1 = "UPDATE db_opus.contas_receber SET N_NUMERO=$nNumero WHERE ID=$ID AND CLIENTES_ID=$COD_CLIENTE AND STATUS_2 !='ABERTO'";
		$result1 = mysql_query($query1)or die (mysql_error());
	}

?>
