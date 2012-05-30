<?php
		
		
			
		
			
			//----------3 ROTINA BLOQUEIO
			//----------
			
				
			//VERIFICA SE EXISTEM BOLETOS PARA BLOQUEAR
			$queryBoletosBloquear = "SELECT * FROM contas_receber WHERE EMPRESA_ID=$codEmpresa AND BLOQUEAR='S' 
													AND (BLOQUEADO != 'S' OR BLOQUEADO IS NULL)  AND STATUS_2 = 'ABERTO' ORDER by ID";
			$resultBoletosBloquear = mysql_query($queryBoletosBloquear);
			$nRowBoletosBloquear = mysql_num_rows($resultBoletosBloquear);
			
			
			if($nRowBoletosBloquear > 0)
			{
				while ( $rowBoletosBloquear = mysql_fetch_assoc($resultBoletosBloquear) ) 
				{
						//DADOS DO BOLETO					
						$idCliente = $rowBoletosBloquear['CLIENTES_ID'];
						$idBoleto = $rowBoletosBloquear['ID'];
						
					
						//1-VERIFICAR SE O MÓDULO DE ACESSO ESTA DISPONÍVEL PARA A EMPRESA ATUAL, SE TIVER CUMPRE O PASSO 3
						$query2 = "SELECT * FROM modulos_empresa WHERE MODULO_ID=12 AND EMPRESA_ID=$codEmpresa";
						$result2 = mysql_query($query2);
						$nRow2 = mysql_num_rows($result2);
						if($nRow2 > 0)
						{
								//2-TENTA LOCALIZAR CREDENCIAIS DE ACESSO
								
								$query3 = "SELECT * FROM acesso_cliente WHERE CLIENTES_ID=$idCliente";
								$result3 = mysql_query($query3);
								$nRow3 = mysql_num_rows($result3);
								if($nRow3 > 0)
								{
									//DADOS DE ACESSO
									$row3 = mysql_fetch_assoc($result3);
									$idServidor = $row3['SERVIDORES_ID'];
									$idPlano = $row3['PLANOS_ACESSO_ID'];
									$idInterface = $row3['INTERFACE_ID'];
									$username = $row3['LOGIN'];
									
									//3-TENTA LOCALIZAR SERVIDOR DA CREDENCIAL DO CLIENTE
									$query4 = "SELECT * FROM servidores WHERE ID=$idServidor";
									$result4 = mysql_query($query4);
									$nRow4 = mysql_num_rows($result4);
									if($nRow4 == 0)
									{
										return 'ROTINA BLOQUEAR - ERRO AO LOCALIZAR O SERVIDOR';
									} 
									$row4 = mysql_fetch_assoc($result4);
									//DADOS DO SERVIDOR
									$ip = $row4['ENDERECO_IP'];
									$usuario = $row4['USUARIO'];
									$senha = $row4['SENHA'];
									$porta = $row4['PORTA_API'];
									
									//5-TENTA LOCALIZAR A INTERFACE DA CREDENCIAL DO CLIENTE
									$query5 = "SELECT * FROM interface WHERE ID=$idInterface";
									$result5 = mysql_query($query5);
									$nRow5 = mysql_num_rows($result5);
									if($nRow5 == 0){
										return 'ROTINA BLOQUEAR - ERRO AO LOCALIZAR A INTERFACE DO CLIENTE';							
									}
									$row5 = mysql_fetch_assoc($result5);
									
									//DEFINE TIPO DE AUTENTICACAO
									if($row5['HOSTPOT'] == 1)
									{
										$tipoAutenBanda = 1;
									}
									if($row5['PPOE'] == 1)
									{
										$tipoAutenBanda = 2;
									}
									if($row5['SIMPLES_QUEUE'] == 1)
									{
										$tipoAutenBanda = 3;
									}
									if($row5['HOTSPOT_RADIUS'] == 1)
									{
										$tipoAutenBanda = 4;
									}	
									
									
									//PROCURA POR ID DO PLANO BLOQUEADO DA INTERFACE SELECIONADA
									$query7 = "SELECT * FROM planos_acesso WHERE NOME LIKE 'BLOQUEADO' AND INTERFACE_ID=$idInterface";
									$result7 = mysql_query($query7);
									$nRow7 = mysql_num_rows($result7);
									if($nRow7 == 0)
									{
										return 'ROTINA BLOQUEAR - NAO FOI POSSIVEL ACHAR O PLANO DE ACESSO BLOQUEADO NA INTERFACE SELECIONADA';
									}
									$row7 = mysql_fetch_assoc($result7);
									$idPlanoBloqueado = $row7['ID'];
									$NomePlano = $idPlanoBloqueado.'_BLOQUEADO';	
																
									//TIPO DE AUTENTICAÇÃO E BANDA 4 - HOTSPOT_RADIUS
									if($tipoAutenBanda == 4)
									{
										
										//BUSCAR SERVIDOR RADIUS ASSOCIADO;
									   $query6 = "SELECT * FROM servidores_radius WHERE ID=$idServidor";
									   $result6 = mysql_query($query6);
									   $nRow6 = mysql_num_rows($result6);
									   if($nRow6 == 0)
									   {
									   		return 'ROTINA BLOQUEAR - SERVIDOR RADIUS NAO ENCONTRADO';
									   	}
									  $row6 = mysql_fetch_assoc($result6);
					   
									   //DEFINE PARAMETROS DO SERVIDOR RADIUS
									   $ipRadius = $row6['IP_FREERADIUS'];
									   $userMysql = $row6['USERNAME_MYSQL'];
									   $senhaMysql = $row6['PASSWORD_MYSQL'];
									   $bancoMysql = $row6['BANCO_DADOS'];
									   
			
									   //TENTA CONECTAR AO SERVIDOR MYSQL
									   if($con = mysql_pconnect($ipRadius, $userMysql, $senhaMysql))
									   {
										mysql_select_db($bancoMysql);
										$query6 = "UPDATE radusergroup SET groupname='$NomePlano' WHERE username LIKE '$username'";
										if(!$result6 = mysql_query($query6))
										{
											return 'ROTINA BLOQUEAR - NAO FOI POSSIVEL ATULIZAR PLANO DE ACESSO NO SERVIDOR RADIUS REMOTO '.mysql_error();
										}
									   }else{
									   		return 'ROTINA BLOQUEAR - NAO FOI POSSIVEL CONECTAR AO SERVIDOR RADIUS REMOTO '.mysql_error();
									   }
									   
									   mysql_close($con);
										
									}//TIPO DE AUTENTICAÇÃO E BANDA 4 - HOTSPOT_RADIUS
									
									
									//RETORNA PARA SERVIDOR MYSQL LOCAL									
									new BaseClass();
																	
									
									//ATUALIZA CREDENCIAL DE ACESSO PARA BLOQUEADO
									$query8 = "UPDATE acesso_cliente SET PLANOS_ACESSO_ID=$idPlanoBloqueado WHERE CLIENTES_ID=$idCliente";
									if(!$result8 = mysql_query($query8))
									{
										return 'ROTINA BLOQUEAR - NAO FOI POSSIVEL ATUALIZAR A CREDENCIAL DO CLIENTE '.mysql_error();
									}					
										
									//4-ATUALIZA LISTA DE CLIENTES BLOQUEADOS
									$query9 = "SELECT * FROM clientes_bloqueado WHERE CLIENTES_ID=$idCliente";
									$result9 = mysql_query($query9);
									$nRow9 = mysql_num_rows($result9);
									if($nRow9 == 0)
									{
										$query10 = "INSERT INTO clientes_bloqueado (CLIENTES_ID, PLANOS_ACESSO_ID) VALUES ($idCliente, $idPlano)";
										if(!$result10 = mysql_query($query10))
										{
											return 'ROTINA BLOQUEIO - ERRO AO CADASTRAR CLIENTE EM LISTA DE BLOQUEADOS';
										}
									}//NROW CLIENTES BLOQUEADOS								
								}//NROW ACESSO CLIENTE 					
						}//N_ROW MÓDULO ACESSO
								
						//ATUALIZAR TITULO COMO BLOQUEADO
						$query1 = "UPDATE contas_receber SET BLOQUEADO='S' WHERE ID='$idBoleto'";
						if(!$result1 = mysql_query($query1))
						{
							return 'ROTINA BLOQUEAR -	ERRO AO ATUALIZAR CONTAS_RECEBER BLOQUEADO '.mysql_error();
						}						
					//5-ABRE CRM DE INFORMANDO ATRASO DE CLIENTES								
				}//WHILE BOLETOS A BLOQUEAR				
			}//NUM_ROW_BOLETOS A BLOQUEAR	
		}//CASE MODULO FIANCEIRO
		
		return 'ok';
?>