<?php

	ini_set('max_execution_time','200000');

	$conexaoRemota = mysql_pconnect("localhost","root","root");
	
	$limparOSI = "truncate table osi";
	$resultLimparOSI = mysql_query($limparOSI, $conexaoRemota);
		
	$queryRadius = "SELECT * FROM radius.manutencoes ORDER by os";
	$resultRadius = mysql_query($queryRadius,$conexaoRemota);
	
	while ($rowRadius = mysql_fetch_assoc($resultRadius))
	{
		 
		  $EMPRESA_ID= '1';
		  $CLIENTES_ID = $rowRadius['codigo_cliente'];
		  $DATA_ENTRADA = $rowRadius['data_entrada'];
		  $DATA_AGENDAMENTO = $rowRadius['data_agendada'];
		  $DATA_CONCLUSAO = $rowRadius['data_conclusao'];
		  $CONTATO = $rowRadius['contato'];
		  $EQUIPAMENTO = $rowRadius['equipamento'];
		  $ACESSORIOS = $rowRadius['acessorios'];
		  $OBSERVACAO = $rowRadius['manu_observacao'];
		  $DIAS_EM_MANUTENCAO = $rowRadius['dias_conserto'];
		  $OPERADOR = $rowRadius['recebido_por'];
		  $TECNICO = $rowRadius['tecnico'];
		  $PROBLEMA = $rowRadius['problema'];
		  $CONCLUSAO = $rowRadius['laudo'];
		  $VALOR = $rowRadius['valor'];
		  $NF_GARANTIA = $rowRadius['nf_garantia'];
		  $STATUS_2 = $rowRadius['status'];
		   
	 	 
	 	 $queryInsert = "INSERT INTO  db_opus.osi (
			EMPRESA_ID ,CLIENTES_ID ,DATA_ENTRADA ,DATA_AGENDAMENTO, DATA_ECAMINHAMENTO ,DATA_CONCLUSAO ,CONTATO ,
			EQUIPAMENTO ,ACESSORIOS ,OBSERVACAO ,DIAS_EM_MANUTENCAO ,OPERADOR ,TECNICO ,PROBLEMA ,
			CONCLUSAO ,VALOR ,NF_GARANTIA ,STATUS_2
			)VALUES (
			'$EMPRESA_ID',  '$CLIENTES_ID',  '$DATA_ENTRADA', '$DATA_AGENDAMENTO',  '$DATA_AGENDAMENTO',  '$DATA_CONCLUSAO',  '$CONTATO',  '$EQUIPAMENTO',  
			'$ACESSORIOS',  '$OBSERVACAO',  '$DIAS_EM_MANUTENCAO',  '$OPERADOR',  '$TECNICO',  '$PROBLEMA',  '$CONCLUSAO',  '$VALOR',  '$NF_GARANTIA',  
			'$STATUS_2')";
	 	 
	 	 $resultInsert = mysql_query($queryInsert,$conexaoRemota)or die(mysql_error());	 
	 
	}