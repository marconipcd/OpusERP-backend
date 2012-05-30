<?php

ini_set('max_execution_time','200000');

	$conexaoRemota = mysql_pconnect("localhost","root","root");
	
	$limparFiadores = "truncate table fiadores";
	$resultLimasFiadores = mysql_query($limparFiadores, $conexaoRemota);


	$queryRadius = "SELECT * FROM radius.pessoa ORDER by codigoPessoa";
	$resultRadius = mysql_query($queryRadius,$conexaoRemota);
	
		while ($rowRadius = mysql_fetch_assoc($resultRadius))
		{
			$ID = $rowRadius['codigoPessoa'];
			
			$queryFiador = "SELECT * FROM radius.fiadores WHERE codigocliente = '$ID'";
			$resultFiador = mysql_query($queryFiador, $conexaoRemota);
			$nRow = mysql_num_rows($resultFiador);
			
			if($nRow != 0){
				
				$rowFiador = mysql_fetch_assoc($resultFiador);
				
				//VARIAVEIS FIADOR
				$id = $rowFiador['id'];
				$cpfFiador = $rowFiador['cpfFiador'];
				$rgfiador = $rowFiador['rgfiador'];
				$tratamentofiador = $rowFiador['tratamentofiador'];
				$nomefiador = ltrim($rowFiador['nomefiador']);
				
					$DATA_NAsc = str_replace('/', '', trim($rowRadius['dataNascimento']));
		 			$DATA_NAsc = str_replace('_', '', trim($DATA_NAsc));
				
					$dia = substr($DATA_NAsc, 0,2);
					$mes = substr($DATA_NAsc, 2,2);
					$ano = substr($DATA_NAsc, 4,4);
				
				$datanascimentofiador = $ano.'-'.$mes.'-'.$dia;
				$cepfiador = $rowFiador['cepfiador'];
				$enderecofiador = $rowFiador['enderecofiador'];
				$numerofiador = $rowFiador['numerofiador'];
				$complementofiador = $rowFiador['complementofiador'];
				$bairrofiador = $rowFiador['bairrofiador'];
				$cidadefiador = $rowFiador['cidadefiador'];
				$uffiador = $rowFiador['uffiador'];
				$paisfiador = $rowFiador['paisfiador'];
				$referenciafiador = $rowFiador['referenciafiador'];
				$telefoneresidencialfiador = $rowFiador['telefoneresidencialfiador'];
				$telefonecomercialfiador = $rowFiador['telefonecomercialfiador'];
				$celular1fiador = $rowFiador['celular1fiador'];
				$celular2fiador = $rowFiador['celular2fiador'];
				$emailfiador = $rowFiador['emailfiador'];
				$msnfiador = $rowFiador['msnfiador'];
				
				
				if($nomefiador != '')
				{
					$queryPegarID = "SHOW TABLE STATUS LIKE 'fiadores'";
					$resultPegarID = mysql_query($queryPegarID,$conexaoRemota);
					$row = mysql_fetch_assoc($resultPegarID);	
		
					
						//$row["Auto_increment"] = 1;
					
				
					$codigo = $row["Auto_increment"];
					echo $ID.'</br>';
					$queryInsert = "INSERT INTO db_opus.fiadores (
					ID, CLIENTES_ID ,DOC_CPF ,DOC_RG ,TRATAMENTO ,NOME ,DATA_NASCIMENTO ,TELEFONE1 ,TELEFONE2 ,CELULAR1 ,
					CELULAR2 ,EMAIL ,MSN)VALUES ('$codigo','$ID', '$cpfFiador', '$rgfiador', '$tratamentofiador', '$nomefiador', '$datanascimentofiador', 
					'$telefoneresidencialfiador', '$telefonecomercialfiador', '$celular1fiador', '$celular2fiador', '$emailfiador', '$msnfiador')";
					
					$resultInsert = mysql_query($queryInsert, $conexaoRemota)or die(mysql_error());	 
					
										
					
					$queryInsert2 = "INSERT INTO db_opus.enderecos_principais (
					FIADORES_ID ,CLIENTES_ID ,CEP ,ENDERECO ,NUMERO ,COMPLEMENTO ,BAIRRO ,CIDADE ,UF ,PAIS ,REFERENCIA)
					VALUES ('$id', '$codigo', '$cepfiador', '$enderecofiador', '$numerofiador', '$complementofiador', '$bairrofiador', '$cidadefiador', 
					'$uffiador', '$paisfiador', '$referenciafiador')";
					
					$resultInsert2 = mysql_query($queryInsert2,$conexaoRemota)or die(mysql_error());	 
				}
				
			}
			
		}