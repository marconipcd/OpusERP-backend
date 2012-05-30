<?php

	ini_set('max_execution_time','20000000');

	$conexaoRemota = mysql_pconnect("localhost","root","root");
	
	$query0 = "SELECT * FROM db_opus.contas_receber";
	$result0 = mysql_query($query0,$conexaoRemota)or die(mysql_error());
	$nRrow0 = mysql_num_rows($result0);
	echo $nRrow0.'</br>';
	
	while($row0 = mysql_fetch_assoc($result0))
	{
		//VARIAVEIS
		$CLIENTES_ID = $row0['CLIENTES_ID'];
		$N_NUM_INI = substr($row0['N_NUMERO'], 0, 4);
		
		if($N_NUM_INI != $CLIENTES_ID)
		{
			echo $row0['N_NUMERO'].'</br>';
			
			//VARIAVEIS
			$status = $row0['STATUS_2'];
			if($status != 'ABERTO')
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
			
			$query1 = "UPDATE db_opus.contas_receber SET N_NUMERO=$nNumero WHERE ID=$ID AND CLIENTES_ID=$CLIENTE_ID AND STATUS_2 != 'ABERTO' ";
			$result1 = mysql_query($query1)or die (mysql_error());
				
			}
			
		}
	}

?>
