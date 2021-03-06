<?php

require_once 'classes/Conexao.php';

require_once 'classes/Financeiro.php';
require_once 'vo/TitulosVO.php';
require_once 'vo/TotaisVO.php';
require_once 'vo/ContasReceberVO.php';
require_once 'vo/FormasPgtoVO.php';
require_once 'vo/contasBancariasVO.php';

ini_set('max_execution_time','200000');
ini_set('memory_limit','1024M');

class Financeiro extends Conexao
{
	public function gerarReciboBoleto($cod)
	{		
		$query = "SELECT contas_receber. * , clientes.NOME_RAZAO FROM contas_receber INNER JOIN clientes ON contas_receber.CLIENTES_ID = clientes.ID
		WHERE contas_receber.ID =$cod";			
		$result = $this->conn->query($query);
		$nRow = $result->num_rows;
		if($nRow == 0)
		{
			return 'GERAR RECIBO BOLETO: NENHUM BOLETO ENCONTRADO COM O CODIGO PASSADO '.$cod;
		}
		$row = $result->fetch_assoc();
		
		
		$dataHoje = date('d/m/Y');
		$hora = date('h:m');
		$user_logado = $_SESSION["nome"];
		
		//unlink("./util2/os/OS_PRINT.txt");
		$fp = fopen("./util/Recibos/BOLETO".$cod.".txt", "w+");
		
		$quebra = chr(13).chr(10);
		
		$linha =  nl2br(str_pad('d i g i t a l', 48, " ", STR_PAD_BOTH));
		$linha .= nl2br(str_pad("Rua Adjar Maciel, 35 Centro Belo Jardim/PE", 48, " ", STR_PAD_BOTH));
		$linha .= nl2br(str_pad("CEP: 55.150-040 Fone: (81)3726.3125", 48, " ", STR_PAD_BOTH));
		$linha .= nl2br(str_pad("CNPJ: 07.578.965/0001-05 IE: 18.3.050.0329", 48, " ", STR_PAD_BOTH));
		$linha .= nl2br(str_pad("www.digitalonline.com.br", 48, " ", STR_PAD_BOTH));
		$linha .= $quebra;
		$linha .= $quebra;
		$linha .= $quebra;
		$linha .= nl2br(str_pad("RECIBO DE PAGAMENTO", 48, " ", STR_PAD_BOTH));
		$linha .= nl2br(str_pad("------------------------------------------------", 48, " ", STR_PAD_BOTH));
		$linha .= nl2br(str_pad("EMISSAO: ".$dataHoje." HORA: ".$hora."", 48, " ", STR_PAD_BOTH));
		$linha .= nl2br(str_pad("------------------------------------------------", 48, " ", STR_PAD_BOTH));	
		$linha .= $quebra;
		$linha .= $quebra;
		$linha .= nl2br(str_pad("INFORMACOES DO BOLETO", 48, " ", STR_PAD_BOTH));
		$linha .= nl2br(str_pad("------------------------------------------------", 48, " ", STR_PAD_BOTH));	
		$linha .= $quebra;
		$linha .= nl2br("Cliente..: ".$row['NOME_RAZAO']."").$quebra;
		$linha .= nl2br("Cod Boleto: ".$row['ID']."").$quebra;
		$linha .= nl2br("N. Doc.: ".$row['N_DOC']."").$quebra;
		$linha .= nl2br("Valor.: ".$row['VALOR_PAGAMENTO']."").$quebra;
		$linha .= nl2br("Data Venc.: ".$row['DATA_VENCIMENTO']."").$quebra;
		$linha .= nl2br(str_pad("------------------------------------------------", 48, " ", STR_PAD_BOTH));
		$linha .= $quebra;
		$linha .= $quebra;
		$linha .= nl2br(str_pad("INFORMACOES DE PAGAMENTO", 48, " ", STR_PAD_BOTH));
		$linha .= nl2br(str_pad("------------------------------------------------", 48, " ", STR_PAD_BOTH));	
		$linha .= $quebra;
		$linha .= nl2br("Data Pag..: ".$row['DATA_PAGAMENTO']."").$quebra;
		$linha .= nl2br("Valor Pgto.: ".$row['VALOR_PAGAMENTO']."").$quebra;
		$linha .= nl2br("Forma Pgto.: ".$row['FORMA_PGTO']."").$quebra;
		$linha .= nl2br("Tipo.: ".$row['TIPO_BAIXA']."").$quebra;
		$linha .= nl2br(str_pad("------------------------------------------------", 48, " ", STR_PAD_BOTH));	
		$linha .= $quebra;
		$linha .= $quebra;
		$linha .= $quebra;
		$linha .= nl2br(str_pad("----------------------------------------", 48, " ", STR_PAD_BOTH));
		$linha .= nl2br(str_pad("Assinatura", 48, " ", STR_PAD_BOTH));
		$linha .= $quebra;
		$linha .= $quebra;
		$linha .= $quebra;
		$linha .= $quebra;
		$linha .= $quebra;
		$linha .= $quebra;
		$linha .= $quebra;
		$linha .= $quebra;
		$linha .= $quebra;
		$linha .= $quebra;
		$linha .= $quebra;
		$linha .= $quebra;
		$escreve = fwrite($fp, $linha);

		// Fecha o arquivo
		fclose($fp);
	
		return 'util/Recibos/BOLETO'.$cod.'.txt';
	}
	//TITULOS A RECEBER
	public function selecionarContasReceber($codTitulo)
	{
		$query = "
				SELECT contas_receber . * , clientes.NOME_RAZAO
				FROM contas_receber
				LEFT JOIN clientes ON contas_receber.CLIENTES_ID = clientes.ID
				WHERE contas_receber.CLIENTES_ID = clientes.ID
				AND contas_receber.ID = $codTitulo" ;
		
		if(!$result = $this->conn->query($query))
		{
			 return $this->conn->error; 
		}else{
			
		$row = $result->fetch_assoc();
			
					$titulo = new ContasReceberVO();					
					
					$titulo->ID = $row['ID'];
					$titulo->NOME_RAZAO = $row['NOME_RAZAO'];
					$titulo->CLIENTES_ID = $row['CLIENTES_ID'];
					$titulo->EMPRESA_ID = $row['EMPRESA_ID'];
					$titulo->N_DOC = $row['N_DOC'];
					$titulo->N_NUMERO = $row['N_NUMERO'];
					$titulo->VALOR_TITULO = $row['VALOR_TITULO'];
					$titulo->VALOR_PAGAMENTO = $row['VALOR_PAGAMENTO'];
					$titulo->DATA_EMISSAO = $row['DATA_EMISSAO'];
					$titulo->DATA_VENCIMENTO = $row['DATA_VENCIMENTO'];
					$titulo->DATA_PAGAMENTO = $row['DATA_PAGAMENTO'];
					$titulo->DATA_BAIXA = $row['DATA_BAIXA'];
					$titulo->DATA_EXCLUSAO = $row['DATA_EXCLUSAO'];
					$titulo->FORMA_PGTO = $row['FORMA_PGTO'];
					$titulo->TIPO_BAIXA = $row['TIPO_BAIXA'];
					$titulo->CONTROLE = $row['CONTROLE'];
					$titulo->CENTRO_CUSTO = $row['CENTRO_CUSTO'];
					$titulo->STATUS_2 = $row['STATUS_2'];
					$titulo->DESBLOQUEAR = $row['DESBLOQUEAR'];
					$titulo->BLOQUEAR = $row['BLOQUEAR'];
					$titulo->DESBLOQUEADO = $row['DESBLOQUEADO'];
					$titulo->BLOQUEADO = $row['BLOQUEADO'];
					
					
					return $titulo;
		}
	}
	
	public function listarContasReceber($codEmpresa, $status)
	{
		$query = "
				SELECT contas_receber . * , clientes.NOME_RAZAO
				FROM contas_receber
				LEFT JOIN clientes ON contas_receber.CLIENTES_ID = clientes.ID
				WHERE contas_receber.EMPRESA_ID = '$codEmpresa'
				AND contas_receber.STATUS_2 LIKE '%$status%' AND contas_receber.STATUS_2 != 'EXCLUIDO' ORDER by contas_receber.DATA_VENCIMENTO ASC";
		
		$result = $this->conn->query($query);
		$nRow = $result->num_rows;
		
		
			while ($row = $result->fetch_assoc())
			{
					$titulo = new ContasReceberVO();					
					
					$titulo->ID = $row['ID'];
					$titulo->NOME_RAZAO = $row['NOME_RAZAO'];
					$titulo->CLIENTES_ID = $row['CLIENTES_ID'];
					$titulo->EMPRESA_ID = $row['EMPRESA_ID'];
					$titulo->N_DOC = $row['N_DOC'];
					$titulo->N_NUMERO = $row['N_NUMERO'];
					$titulo->VALOR_TITULO = $row['VALOR_TITULO'];
					$titulo->VALOR_PAGAMENTO = $row['VALOR_PAGAMENTO'];
					$titulo->DATA_EMISSAO = $row['DATA_EMISSAO'];
					$titulo->DATA_VENCIMENTO = $row['DATA_VENCIMENTO'];
					$titulo->DATA_PAGAMENTO = $row['DATA_PAGAMENTO'];
					$titulo->DATA_BAIXA = $row['DATA_BAIXA'];
					$titulo->DATA_EXCLUSAO = $row['DATA_EXCLUSAO'];
					$titulo->FORMA_PGTO = $row['FORMA_PGTO'];
					$titulo->TIPO_BAIXA = $row['TIPO_BAIXA'];
					$titulo->CONTROLE = $row['CONTROLE'];
					$titulo->CENTRO_CUSTO = $row['CENTRO_CUSTO'];
					$titulo->STATUS_2 = $row['STATUS_2'];
					$titulo->DESBLOQUEAR = $row['DESBLOQUEAR'];
					$titulo->BLOQUEAR = $row['BLOQUEAR'];
					$titulo->DESBLOQUEADO = $row['DESBLOQUEADO'];
					$titulo->BLOQUEADO = $row['BLOQUEADO'];
					
					$titulos[] = $titulo;	
			}
					return $titulos;
		
	}
	
	public function procurarContasReceber($empresa, $cliente, $status)	
	{
		
		$query = "
				SELECT contas_receber . * , clientes.NOME_RAZAO
				FROM contas_receber
				LEFT JOIN clientes ON contas_receber.CLIENTES_ID = clientes.ID
				WHERE clientes.EMPRESA_ID = '$empresa'
				AND contas_receber.STATUS_2 LIKE '%$status%' AND contas_receber.STATUS_2 != 'EXCLUIDO' 
				AND clientes.NOME_RAZAO LIKE '%$cliente%' 
				ORDER by contas_receber.DATA_VENCIMENTO ASC";
		
		$result = $this->conn->query($query);
		$nRow = $result->num_rows;
		if($nRow == 0){return 'ERRO';}
		
			while ($row = $result->fetch_assoc())
			{
					$titulo = new ContasReceberVO();					
					
					$titulo->ID = $row['ID'];
					$titulo->NOME_RAZAO = $row['NOME_RAZAO'];
					$titulo->CLIENTES_ID = $row['CLIENTES_ID'];
					$titulo->EMPRESA_ID = $row['EMPRESA_ID'];
					$titulo->N_DOC = $row['N_DOC'];
					$titulo->N_NUMERO = $row['N_NUMERO'];
					$titulo->VALOR_TITULO = $row['VALOR_TITULO'];
					$titulo->VALOR_PAGAMENTO = $row['VALOR_PAGAMENTO'];
					$titulo->DATA_EMISSAO = $row['DATA_EMISSAO'];
					$titulo->DATA_VENCIMENTO = $row['DATA_VENCIMENTO'];
					$titulo->DATA_PAGAMENTO = $row['DATA_PAGAMENTO'];
					$titulo->DATA_BAIXA = $row['DATA_BAIXA'];
					$titulo->DATA_EXCLUSAO = $row['DATA_EXCLUSAO'];
					$titulo->FORMA_PGTO = $row['FORMA_PGTO'];
					$titulo->TIPO_BAIXA = $row['TIPO_BAIXA'];
					$titulo->CONTROLE = $row['CONTROLE'];
					$titulo->CENTRO_CUSTO = $row['CENTRO_CUSTO'];
					$titulo->STATUS_2 = $row['STATUS_2'];
					$titulo->DESBLOQUEAR = $row['DESBLOQUEAR'];
					$titulo->BLOQUEAR = $row['BLOQUEAR'];
					$titulo->DESBLOQUEADO = $row['DESBLOQUEADO'];
					$titulo->BLOQUEADO = $row['BLOQUEADO'];
					
					$titulos[] = $titulo;	
			}
					return $titulos;
	}
	public function editarTitulo($id, $valor, $vencimento, $ndoc)
		{			
			
			$diaI = substr($vencimento, 0, 2);
			$mesI = substr($vencimento, 3, 2);
			$anoI = substr($vencimento, 6, 4);
			
			$dataI = $anoI.'-'.$mesI.'-'.$diaI;		
			
			$query = "UPDATE contas_receber SET VALOR_TITULO='$valor', DATA_VENCIMENTO='$dataI', N_DOC='$ndoc' WHERE ID='$id'";
			$result = $this->conn->query($query);
			
//			///////////////////////////////////////
//		$user_logado = $_SESSION["nome"];
//		$data = date('Y-m-d');
//		$desc = 'Editou o Titulo '.$id;
//		
//		$query_Logs = "INSERT INTO radius.logs (id ,login ,data ,desc)VALUES (NULL , '$user_logado', '$data', '$desc')";
//		$result_logs = $this->conn->query($query_Logs);
//		////////////////////////////////////////////////////////
		}
	public function excluirTitulos($id)
	{
						
			$ids_get = explode(",", $id);
			$data = date('Y-m-d');	
			
			
			foreach ($ids_get as $id2)
			{
				
			$query1 = "SELECT * FROM contas_receber WHERE ID = '$id2'";
			$result1 = $this->conn->query($query1);
			$row1 = $result1->fetch_assoc();
			$vencimento = $row1['DATA_VENCIMENTO'];
			$datahoje = date('Y-m-d');
			$cliente = $row1['CLIENTES_ID'];
			
			//if($vencimento < $datahoje)
			//{
				//Seleciona Cliente
				//$query2 = "SELECT * FROM clientes WHERE ID LIKE '$cliente'";
				//$result2 = $this->conn->query($query2);
				//$row2 = $result2->fetch_assoc();
				//$total2 = $result2->num_rows;
				
				//if($total2 > 0)
				//{
					//IMPLEMENTAR DESBLOQUEIO
					
					//$loginCliente = $row2['loginCliente'];
					//$planoCliente = $row2['plano'];
					//$codigoCliente = $row2['codigoPessoa'];
				
				//Updates
				
				//$query3 = "UPDATE pessoa SET planoCliente = '$planoCliente' WHERE codigoPessoa ='$codigoCliente'";
				//$result3 = $this->conn->query($query3);
				
				//$query4 = "UPDATE radreply SET value = '$planoCliente' WHERE username = '$loginCliente'";
				//$result4 = $this->conn->query($query4);
				
				//}
			//}
			

				$query = "UPDATE contas_receber SET STATUS_2='EXCLUIDO', DATA_EXCLUSAO='$data' WHERE ID = '$id2'";
				$result = $this->conn->query($query);
			
			}
			
			return $result;
			
			
//		///////////////////////////////////////
//			$user_logado = $_SESSION["nome"];
//			
//			$desc = 'Excluiu o Titulo '.$id;
//			
//			$query_Logs = "INSERT INTO radius.logs (id ,login ,data ,desc)VALUES (NULL , '$user_logado', '$data', '$desc')";
//			$result_logs = $this->conn->query($query_Logs);
//		////////////////////////////////////////////////////////
		}
	public function negativarTitulos($id)
	{
		$ids_get = explode(",", $id);
			$data = date('Y-m-d');	
			
			
			foreach ($ids_get as $id2)
			{
				
			$query1 = "SELECT * FROM contas_receber WHERE ID = '$id2'";
			$result1 = $this->conn->query($query1);
			$row1 = $result1->fetch_assoc();
			$vencimento = $row1['DATA_VENCIMENTO'];
			$datahoje = date('Y-m-d');
			$cliente = $row1['CLIENTES_ID'];
			
			//if($vencimento < $datahoje)
			//{
				//Seleciona Cliente
				//$query2 = "SELECT * FROM clientes WHERE ID LIKE '$cliente'";
				//$result2 = $this->conn->query($query2);
				//$row2 = $result2->fetch_assoc();
				//$total2 = $result2->num_rows;
				
				//if($total2 > 0)
				//{
					//IMPLEMENTAR DESBLOQUEIO
					
					//$loginCliente = $row2['loginCliente'];
					//$planoCliente = $row2['plano'];
					//$codigoCliente = $row2['codigoPessoa'];
				
				//Updates
				
				//$query3 = "UPDATE pessoa SET planoCliente = '$planoCliente' WHERE codigoPessoa ='$codigoCliente'";
				//$result3 = $this->conn->query($query3);
				
				//$query4 = "UPDATE radreply SET value = '$planoCliente' WHERE username = '$loginCliente'";
				//$result4 = $this->conn->query($query4);
				
				//}
			//}
			

				$query = "UPDATE contas_receber SET STATUS_2='NEGATIVADO' WHERE ID = '$id2'";
				$result = $this->conn->query($query);
			
			}
			
			return $result;
	}	
	public function calcularBoleto($empesa, $valortitulo, $vencimento)
		{
			$query = "SELECT * FROM conta_bancaria WHERE EMPRESA_ID = '$empesa'"; 
			$result = $this->conn->query($query);
			$row = $result->fetch_assoc();
				
								
				// Variaveis Multa, Juros:
	
				$multaPorc = $row['MULTA'];
				$jurosPorc = $row['JUROS'];
				$taxaBoleto = $row['TAXA_BOLETO'];
				
				$dados_clientes['vencimento'] = $vencimento;
		  		$dados_clientes['valor'] = $valortitulo;
				$dados_clientes['atual'] = $atual = date('d/m/Y');
			
				$atual = $dados_clientes['atual'];
				$dat_venc = $dados_clientes['vencimento'];
				$dat_novo_venc = $dados_clientes['atual'];
				
				$valor_doc = str_replace(",",".",$dados_clientes['valor']);
						
				$dados_clientes['dat_venc'] = str_replace("/","",$dat_venc);
				$dados_clientes['dat_novo_venc'] = str_replace("/","",$dat_novo_venc);
				
				$dia = substr($dados_clientes['dat_venc'], 0, 2);
				$mes = substr($dados_clientes['dat_venc'], 2, 2);
				$ano = substr($dados_clientes['dat_venc'], 4, 4);
				
				$dia2 = substr($dados_clientes['dat_novo_venc'], 0, 2);
				$mes2 = substr($dados_clientes['dat_novo_venc'], 2, 2);
				$ano2 = substr($dados_clientes['dat_novo_venc'], 4, 4);
				
				$dados_clientes['dat_venc'] = $vencimento;
				$dados_clientes['dat_novo_venc'] = $ano2.'-'.$mes2.'-'.$dia2;
				
				$dados_clientes['dat_venc_segundos'] = strtotime($dados_clientes['dat_venc']);
				$dados_clientes['dat_venc_novo_segundos'] = strtotime($dados_clientes['dat_novo_venc']);	
				
				$dados_clientes['dias_variavel'] = ($dados_clientes['dat_venc_novo_segundos'] - $dados_clientes['dat_venc_segundos'])/86400;
				$dias = ceil($dados_clientes['dias_variavel']);
				
				$tolerancia = $row['TOLERANCIA'];

				if($dias > $tolerancia){
						
						$juros = (($jurosPorc * $dias));

						//$juros = (($juros1 * $valor_doc) /100 );


				if($dias==0)
					{$multa = 0; }
				else
				{
				//$multa = (($multaPorc * $valor_doc) / 100);

				$multa = $multaPorc;
				$valorBoleto = $taxaBoleto;
				}
				}
				else{
				$juros = 0;
				$multa = 0;
				$valorBoleto = 0;
				}

				$jurosTotais = $juros + $multa;
				
				//$valor_doc = $valor_doc + $valorBoleto +('10%');
				
				//$valor_doc = $valor_doc+(($valor_doc * $jurosTotais)/100);
				
				
				$valorDocTotal = $valor_doc + $taxaBoleto;
				$acrescimo = $jurosTotais*$valorDocTotal/100;
				
				$novoValorDoc =  $valorDocTotal + $acrescimo;
				

				$dados_clientes['valorFinal'] = number_format($novoValorDoc,2, ',', '.');
				$dados_clientes['multa'] = $multa.'%';
				$dados_clientes['juros'] = $juros.'%';
				$dados_clientes['dias'] = $dias;
				
				return $dados_clientes;
		}	
	public function listarFormasPgto($empresa)
	{
		$query = "SELECT * FROM formas_pagamento WHERE EMPRESA_ID = '$empresa'";
		$result = $this->conn->query($query);
		
		
		while($row = $result->fetch_assoc())
		{
			$formapgto = new FormasPgtoVO();			
			$formapgto->ID = $row['ID']; 
			$formapgto->EMPRESA_ID = $row['EMPRESA_ID'];
			$formapgto->NOME = $row['NOME'];
			
			$formaspgto[] = $formapgto;
		}
		
		return $formaspgto;
	}
	public function baixarBoleto($idTitulo, $valorPago, $formaPgto)
	{						
		$dataPagamento = date('Y-m-d'); 
		$query = "UPDATE contas_receber SET STATUS_2='FECHADO', VALOR_PAGAMENTO='$valorPago', 
		DESBLOQUEAR='S', DATA_PAGAMENTO='$dataPagamento', TIPO_BAIXA='manual', FORMA_PGTO='$formaPgto' WHERE ID='$idTitulo' ";
		$result = $this->conn->query($query);			 
			
		return $result;		
	}
	public function gerarBoletos($empresa,$cliente,$codigoCliente, $valor, $vencimento, $ndocumento, $qtd)
	{
				//Gerar Boletos
				///////////////////////				
							
				$quantidade = $qtd;
   				$_prazo = 0;   
    		  
				if($ndocumento == '')
				{
				for($i=0;$i < $quantidade;$i++)
   				{
		
					$sequencia = $i;
					if($i ==0){
						$sequencia++;
					}else if($i == $sequencia){
						$sequencia++;
					}
					
					$sequencia2 = str_pad($sequencia, 2, "0", STR_PAD_LEFT);
					$ano = date('y');

					
					$queryUltimoBoleto = "SELECT * FROM contas_receber WHERE CLIENTES_ID = '$codigoCliente' ORDER by ID DESC";
					$resultUltimoBoleto = $this->conn->query($queryUltimoBoleto);
					$rowUltimoBoleto = $resultUltimoBoleto->fetch_assoc();			
			    	$nRowUltimoBoleto = $resultUltimoBoleto->num_rows;
			    	if($nRowUltimoBoleto == 0)
			    	{
			    		$ultimoNumero = str_pad("1", 6, "0", STR_PAD_LEFT);
			    	}else
			    	{
			    		$ultimoValor = ltrim(substr($rowUltimoBoleto['N_NUMERO'], -6)+1, "0");
			    		
			    		$ultimoNumero = str_pad($ultimoValor, 6, "0", STR_PAD_LEFT);
			    		
			    	}
			    	
           			$NumeroNovo = $codigoCliente.$ultimoNumero;
					
					
					//$dataPrimeiroBoleto = "10/01/2011";
					$_dia = substr($vencimento, 0, 2);
   					$_mes = substr($vencimento, 3, 2);
   					$_ano  = substr($vencimento, 6, 4);
			
	   				$_ts = mktime(0,0,0,$_mes+$_prazo,$_dia,$_ano);
       				$_data = date('Y-m-d',$_ts);
	   				
	   				
	  				
					$emissao = date('Y-m-d');
					
					
					
					$ndocumento = $codigoCliente.'/'.$ano.'/'.$sequencia2; 
					
					
					
					//Inserindo
					$sql = "INSERT INTO contas_receber (N_DOC,CLIENTES_ID,DATA_VENCIMENTO,VALOR_TITULO, STATUS_2, DATA_EMISSAO, N_NUMERO, 
					CONTROLE, EMPRESA_ID) VALUES
					('$ndocumento', '$codigoCliente', '$_data','$valor', 'ABERTO', '$emissao','$NumeroNovo', '$ndocumento', '$empresa')";
					 
					$resultadoSql = $this->conn->query($sql);
        			
        			//supondo que o vencimento � de 30 em 30 dias 
					
        			$_prazo += 1;
        			
    			
    			}
				}else{
				for($i=0;$i < $quantidade;$i++)
   				{
		
					$sequencia = $i;
					if($i ==0){
						$sequencia++;
					}else if($i == $sequencia){
						$sequencia++;
					}
					
					$sequencia2 = str_pad($sequencia, 2, "0", STR_PAD_LEFT);
					$ano = date('y');

					
       
					$queryUltimoBoleto = "SELECT * FROM contas_receber WHERE CLIENTES_ID = '$codigoCliente' ORDER by ID DESC";
					$resultUltimoBoleto = $this->conn->query($queryUltimoBoleto);
					$rowUltimoBoleto = $resultUltimoBoleto->fetch_assoc();			
			    	$nRowUltimoBoleto = $resultUltimoBoleto->num_rows;
			    	if($nRowUltimoBoleto == 0)
			    	{
			    		$ultimoNumero = str_pad("1", 6, "0", STR_PAD_LEFT);
			    	}else
			    	{
			    		$ultimoValor = ltrim(substr($rowUltimoBoleto['N_NUMERO'], -6)+1, "0");
			    		
			    		$ultimoNumero = str_pad($ultimoValor, 6, "0", STR_PAD_LEFT);
			    		
			    	}
			    	
           			$NumeroNovo = $codigoCliente.$ultimoNumero;
					
					//Serie do ultimo boleto gerado
					$ultimoBoleto = $ultimoNumero+1;
					
					//$dataPrimeiroBoleto = "10/01/2011";
					$_dia = substr($vencimento, 0, 2);
   					$_mes = substr($vencimento, 3, 2);
   					$_ano  = substr($vencimento, 6, 4);
			
	   				$_ts = mktime(0,0,0,$_mes+$_prazo,$_dia,$_ano);
       				$_data = date('Y-m-d',$_ts);
	   				
	   				
	  				
					$emissao = date('Y-m-d');
					
					
									
					$sql = "INSERT INTO contas_receber (N_DOC,CLIENTES_ID,DATA_VENCIMENTO,VALOR_TITULO, STATUS_2, DATA_EMISSAO, N_NUMERO, 
					CONTROLE, EMPRESA_ID) VALUES
					('$ndocumento', '$codigoCliente', '$_data','$valor', 'ABERTO', '$emissao','$NumeroNovo', '$ndocumento', '$empresa')";
					 
					$resultadoSql = $this->conn->query($sql);
        			
        			//supondo que o vencimento � de 30 em 30 dias 
					
        			$_prazo += 1;
        			
    			
    			}
		}
		}
	public function valorRecebido($empresa, $cliente)
	{
			return;
			
		$query = "SELECT sum(contas_receber.VALOR_PAGAMENTO) AS valor_Recebido
			FROM contas_receber
			LEFT JOIN clientes ON contas_receber.CLIENTES_ID = clientes.ID
			WHERE contas_receber.CLIENTES_ID = clientes.ID
			AND contas_receber.STATUS_2 = 'FECHADO' AND clientes.NOME_RAZAO LIKE '%$cliente%' 
			AND contas_receber.EMPRESA_ID = '$empresa' ";
			
			
		$result = $this->conn->query($query);
		$row = $result->fetch_assoc();
			
		if($result->num_rows == 0)
		{
			return 'ERRO';
		}
						
		$valorRecebido = $row['valor_Recebido'];
			
		function formata($numero)
		{
			if(strpos($numero,'.')!='')
			{
				//DIVIDE NUMERO PASSADO POR (.)
				$var=explode('.',$numero);
				
				//SE A QTD DE CARACTERES FOR IGUAL A 4
				if(strlen($var[0])==4)
				{
					$parte1=substr($var[0],0,1);
					$parte2=substr($var[0],1,3);
		     
					if(strlen($var[1])<2)
					{
						$formatado=$parte1.'.'.$parte2.','.$var[1].'0';
					}else
					{
						$formatado=$parte1.'.'.$parte2.','.$var[1];
					}
				}
				//SE A QTD DE CARACTERES FOR IGUAL A 5
				elseif(strlen($var[0])==5)
				{
					$parte1=substr($var[0],0,2);
					$parte2=substr($var[0],2,3);
					
					if(strlen($var[1])<2)
					{
						$formatado=$parte1.'.'.$parte2.','.$var[1].'0';
					}
					else
					{
						$formatado=$parte1.'.'.$parte2.','.$var[1];
					}
				}
				//SE A QTD DE CARACTERES FOR IGUAL A 6
				elseif(strlen($var[0])==6)
				{
					$parte1=substr($var[0],0,3);
					$parte2=substr($var[0],3,3);
					
					if(strlen($var[1])<2)
					{
						$formatado=$parte1.'.'.$parte2.','.$var[1].'0';
					}
					else
					{
						$formatado=$parte1.'.'.$parte2.','.$var[1];
					}
				}
				//SE A QTD DE CARACTERES FOR IGUAL A 7
				elseif(strlen($var[0])==7)
				{
					$parte1=substr($var[0],0,1);
					$parte2=substr($var[0],1,3);
					$parte3=substr($var[0],4,3);
					
					if(strlen($var[1])<2)
					{
						$formatado=$parte1.'.'.$parte2.'.'.$parte3.','.$var[1].'0';
					}
					else
					{
						$formatado=$parte1.'.'.$parte2.'.'.$parte3.','.$var[1];
					}
				}
				//SE A QTD DE CARACTERES FOR IGUAL A 8
				elseif(strlen($var[0])==8)
				{
					$parte1=substr($var[0],0,2);
					$parte2=substr($var[0],2,3);
					$parte3=substr($var[0],5,3);
					
					if(strlen($var[1])<2)
					{
						$formatado=$parte1.'.'.$parte2.'.'.$parte3.','.$var[1].'0';
					}else
					{
						$formatado=$parte1.'.'.$parte2.'.'.$parte3.','.$var[1];
					}
				}
				//SE A QTD DE CARACTERES FOR IGUAL A 9		
				elseif(strlen($var[0])==9)
				{
					$parte1=substr($var[0],0,3);
					$parte2=substr($var[0],3,3);
					$parte3=substr($var[0],6,3);
		     
					if(strlen($var[1])<2)
					{
						$formatado=$parte1.'.'.$parte2.'.'.$parte3.','.$var[1].'0';
					}
					else
					{
						$formatado=$parte1.'.'.$parte2.'.'.$parte3.','.$var[1];
					}
				}
				//SE A QTD DE CARACTERES FOR IGUAL A 10
				elseif(strlen($var[0])==10)
				{
					$parte1=substr($var[0],0,1);
					$parte2=substr($var[0],1,3);
					$parte3=substr($var[0],4,3);
					$parte4=substr($var[0],7,3);
		     
					if(strlen($var[1])<2)
					{
						$formatado=$parte1.'.'.$parte2.'.'.$parte3.'.'.$parte4.','.$var[1].'0';
					}
					else
					{
						$formatado=$parte1.'.'.$parte2.'.'.$parte3.'.'.$parte4.','.$var[1];
					}
				}
				//SE A QTD DE CARACTERES NAO FOR IGUAL A NENHUMA DA CIMA
				else
				{
					if(strlen($var[1])<2)
					{
						$formatado=$var[0].','.$var[1].'0';
					}
					else
					{
						$formatado=$var[0].','.$var[1];
					}
				}
			}
			else
			{
				$var=$numero;
				
				if(strlen($var)==4)
				{
				  $parte1=substr($var,0,1);
				  $parte2=substr($var,1,3);
				  $formatado=$parte1.'.'.$parte2.','.'00';
				}
				elseif(strlen($var)==5)
				{
				  $parte1=substr($var,0,2);
				  $parte2=substr($var,2,3);
				  $formatado=$parte1.'.'.$parte2.','.'00';
				}
				elseif(strlen($var)==6)
				{
				  $parte1=substr($var,0,3);
				  $parte2=substr($var,3,3);
				  $formatado=$parte1.'.'.$parte2.','.'00';
				}
				elseif(strlen($var)==7)
				{
				  $parte1=substr($var,0,1);
				  $parte2=substr($var,1,3);
				  $parte3=substr($var,4,3);
				  $formatado=$parte1.'.'.$parte2.'.'.$parte3.','.'00';
				}
				elseif(strlen($var)==8)
				{
				  $parte1=substr($var,0,2);
				  $parte2=substr($var,2,3);
				  $parte3=substr($var,5,3);
				  $formatado=$parte1.'.'.$parte2.'.'.$parte3.','.'00';
				}
				elseif(strlen($var)==9)
				{
				  $parte1=substr($var,0,3);
				  $parte2=substr($var,3,3);
				  $parte3=substr($var,6,3);
				  $formatado=$parte1.'.'.$parte2.'.'.$parte3.','.'00';
				}
				elseif(strlen($var)==10)
				{
				  $parte1=substr($var,0,1);
				  $parte2=substr($var,1,3);
				  $parte3=substr($var,4,3);
				  $parte4=substr($var,7,3);
				  $formatado=$parte1.'.'.$parte2.'.'.$parte3.'.'.$parte4.','.'00';
				}
				else
				{
				  $formatado=$var.','.'00';
				}
			}
			
			return $formatado;
		}

		return formata($valorRecebido);
			
			
	}
	public function valorAreceber($empresa, $cliente)
		{
			return;
			//$query = "select sum(valor) AS valor_a_recebe FROM contasapagar WHERE status = 'ABERTO' AND cliente LIKE '%$cliente%'";
			$query = "SELECT sum(contas_receber.VALOR_TITULO) AS valor_receber
				FROM contas_receber
				LEFT JOIN clientes ON contas_receber.CLIENTES_ID = clientes.ID
				WHERE contas_receber.CLIENTES_ID = clientes.ID
				AND contas_receber.STATUS_2 = 'ABERTO' AND clientes.NOME_RAZAO LIKE '%$cliente%' 
				AND contas_receber.EMPRESA_ID = '$empresa' ";
			
			
			$result = $this->conn->query($query);
			$row = $result->fetch_assoc();
			
			
						
			$valorAreceber = $row['valor_receber'];
	
			
			
		function formata($numero)
{
	if(strpos($numero,'.')!='')
	{
		   $var=explode('.',$numero);
		   if(strlen($var[0])==4)
		   {
		     $parte1=substr($var[0],0,1);
		     $parte2=substr($var[0],1,3);
		     if(strlen($var[1])<2)
		     {
		     	$formatado=$parte1.'.'.$parte2.','.$var[1].'0';
		     }else
		     {
		     	$formatado=$parte1.'.'.$parte2.','.$var[1];
		     }
		   }
		   elseif(strlen($var[0])==5)
		   {
		     $parte1=substr($var[0],0,2);
		     $parte2=substr($var[0],2,3);
		     if(strlen($var[1])<2)
		     {
		     	$formatado=$parte1.'.'.$parte2.','.$var[1].'0';
		     }
		     else
		     {
		     	$formatado=$parte1.'.'.$parte2.','.$var[1];
		     }
		   }
		   elseif(strlen($var[0])==6)
		   {
		     $parte1=substr($var[0],0,3);
		     $parte2=substr($var[0],3,3);
		     if(strlen($var[1])<2)
		     {
		     	$formatado=$parte1.'.'.$parte2.','.$var[1].'0';
		     }
		     else
		     {
		     	$formatado=$parte1.'.'.$parte2.','.$var[1];
		     }
		   }
		   elseif(strlen($var[0])==7)
		   {
		     $parte1=substr($var[0],0,1);
		     $parte2=substr($var[0],1,3);
		     $parte3=substr($var[0],4,3);
		     if(strlen($var[1])<2)
		     {
		     	$formatado=$parte1.'.'.$parte2.'.'.$parte3.','.$var[1].'0';
		     }
		     else
		     {
		     $formatado=$parte1.'.'.$parte2.'.'.$parte3.','.$var[1];
		     }
		   }
		   elseif(strlen($var[0])==8)
		   {
		     $parte1=substr($var[0],0,2);
		     $parte2=substr($var[0],2,3);
		     $parte3=substr($var[0],5,3);
		     if(strlen($var[1])<2){
		     $formatado=$parte1.'.'.$parte2.'.'.$parte3.','.$var[1].'0';
		     }else{
		     $formatado=$parte1.'.'.$parte2.'.'.$parte3.','.$var[1];
		     }
		   }
		   elseif(strlen($var[0])==9)
		   {
		     $parte1=substr($var[0],0,3);
		     $parte2=substr($var[0],3,3);
		     $parte3=substr($var[0],6,3);
		     if(strlen($var[1])<2)
		     {
		     	$formatado=$parte1.'.'.$parte2.'.'.$parte3.','.$var[1].'0';
		     }
		     else
		     {
		     	$formatado=$parte1.'.'.$parte2.'.'.$parte3.','.$var[1];
		     }
		   }
		   elseif(strlen($var[0])==10)
		   {
		     $parte1=substr($var[0],0,1);
		     $parte2=substr($var[0],1,3);
		     $parte3=substr($var[0],4,3);
		     $parte4=substr($var[0],7,3);
		     if(strlen($var[1])<2)
		     {
		     	$formatado=$parte1.'.'.$parte2.'.'.$parte3.'.'.$parte4.','.$var[1].'0';
		     }
		     else
		     {
		     	$formatado=$parte1.'.'.$parte2.'.'.$parte3.'.'.$parte4.','.$var[1];
		     }
		   }
		   else
		   {
		     if(strlen($var[1])<2)
		     {
		    	 $formatado=$var[0].','.$var[1].'0';
		     }
		     else
		     {
		    	 $formatado=$var[0].','.$var[1];
		     }
		   }
	  }
	  else
	  {
	     $var=$numero;
	   if(strlen($var)==4)
	   {
	     $parte1=substr($var,0,1);
	     $parte2=substr($var,1,3);
	     $formatado=$parte1.'.'.$parte2.','.'00';
	   }
	   elseif(strlen($var)==5)
	   {
	     $parte1=substr($var,0,2);
	     $parte2=substr($var,2,3);
	     $formatado=$parte1.'.'.$parte2.','.'00';
	   }
	   elseif(strlen($var)==6)
	   {
	     $parte1=substr($var,0,3);
	     $parte2=substr($var,3,3);
	     $formatado=$parte1.'.'.$parte2.','.'00';
	   }
	   elseif(strlen($var)==7)
	   {
	     $parte1=substr($var,0,1);
	     $parte2=substr($var,1,3);
	     $parte3=substr($var,4,3);
	     $formatado=$parte1.'.'.$parte2.'.'.$parte3.','.'00';
	   }
	   elseif(strlen($var)==8)
	   {
	     $parte1=substr($var,0,2);
	     $parte2=substr($var,2,3);
	     $parte3=substr($var,5,3);
	     $formatado=$parte1.'.'.$parte2.'.'.$parte3.','.'00';
	   }
	   elseif(strlen($var)==9)
	   {
	     $parte1=substr($var,0,3);
	     $parte2=substr($var,3,3);
	     $parte3=substr($var,6,3);
	     $formatado=$parte1.'.'.$parte2.'.'.$parte3.','.'00';
	   }
	   elseif(strlen($var)==10)
	   {
	     $parte1=substr($var,0,1);
	     $parte2=substr($var,1,3);
	     $parte3=substr($var,4,3);
	     $parte4=substr($var,7,3);
	     $formatado=$parte1.'.'.$parte2.'.'.$parte3.'.'.$parte4.','.'00';
	   }
	   else
	   {
	     $formatado=$var.','.'00';
	   }
	}
	  return $formatado;
}

	return formata($valorAreceber);
			
		}
		
		
	//FORMAS PGTO.
	public function selecionarFormasPgto($id)
	{
		$query = "SELECT * FROM formas_pagamento WHERE ID = '$id'";
		$result = $this->conn->query($query);
		
		
		$row = $result->fetch_assoc();
		
			$formapgto = new FormasPgtoVO();
			
			$formapgto->ID = $row['ID'];
			$formapgto->EMPRESA_ID = $row['EMPRESA_ID'];
			$formapgto->NOME = $row['NOME'];
				
		
		return $formapgto;
	}
	public function cadastrarFormaPgto($empresa, $nome)
	{
		$nome = strtoupper($nome);
		$query = "INSERT INTO formas_pagamento (ID, EMPRESA_ID, NOME) VALUES (NULL,'$empresa', '$nome')";
		$result = $this->conn->query($query);
		
		return $result;
	}
	public function alterarFormasPgto($id, $nome)
	{
		$nome = strtoupper($nome);
		
		$query = "UPDATE formas_pagamento SET NOME = '$nome' WHERE ID = '$id'";
		$result = $this->conn->query($query);
		return $result;
	}
	public function excluirFormasPgto( $ids )
	{ 		
			$ids_get = explode(",", $ids);						
			
			foreach ($ids_get as $id)
			{
				$query = "DELETE FROM formas_pagamento WHERE ID = '$id'";
				$result = $this->conn->query($query);
			
			}
			
		return $result;		
		
	}
	
	//----------CONTAS BANCARIAS---------------------------//
	public function listarContasBancarias($empresa)
	{
		$query = "SELECT * FROM  conta_bancaria WHERE EMPRESA_ID = '$empresa'";
		$result = $this->conn->query($query);
		$nRow = $result->num_rows;
		if($nRow == 0)
		{
			return 'ERRO';
		}
		
		while ($row = $result->fetch_assoc()) {

			$contasBancarias = new contasBancariasVO();
			
			$contasBancarias->ID = $row['ID'];
			$contasBancarias->EMPRESA_ID = $row['EMPRESA_ID'];
			$contasBancarias->NOME_BANCO = $row['NOME_BANCO'];
			$contasBancarias->AGENCIA_BANCO = $row['AGENCIA_BANCO'];
			$contasBancarias->N_CONTA = $row['N_CONTA'];
			$contasBancarias->CONVENIO = $row['CONVENIO'];
			$contasBancarias->CONTRATO = $row['CONTRATO'];
			$contasBancarias->CARTEIRA = $row['CARTEIRA'];
			$contasBancarias->VARIACAO_CARTEIRA = $row['VARIACAO_CARTEIRA'];
			$contasBancarias->IDENTIFICACAO = $row['IDENTIFICACAO'];
			$contasBancarias->CPF_CNPJ = $row['CPF_CNPJ'];
			$contasBancarias->ENDERECO = $row['ENDERECO'];
			$contasBancarias->CIDADE_UF = $row['CIDADE_UF'];
			$contasBancarias->CEDENTE = $row['CEDENTE'];
			$contasBancarias->MULTA = $row['MULTA'];
			$contasBancarias->JUROS = $row['JUROS'];
			$contasBancarias->TOLERANCIA = $row['TOLERANCIA'];
			$contasBancarias->TAXA_BOLETO = $row['TAXA_BOLETO'];
			$contasBancarias->DEMONSTRATIVO1 = $row['DEMONSTRATIVO1'];
			$contasBancarias->DEMONSTRATIVO2 = $row['DEMONSTRATIVO2'];
			$contasBancarias->DEMONSTRATIVO3 = $row['DEMONSTRATIVO3'];
			$contasBancarias->INSTRUCOES1 = $row['INSTRUCOES1'];
			$contasBancarias->INSTRUCOES2 = $row['INSTRUCOES2'];
			$contasBancarias->INSTRUCOES3 = $row['INSTRUCOES3'];
			
			$contasBancariass[] = $contasBancarias;
		}
		
		return $contasBancariass;
	}
	public function cadastrarContaBancaria(contasBancariasVO $conta)
	{
		$query = "INSERT INTO  conta_bancaria (
		EMPRESA_ID ,NOME_BANCO ,AGENCIA_BANCO ,N_CONTA ,CONVENIO ,CONTRATO ,CARTEIRA ,VARIACAO_CARTEIRA ,IDENTIFICACAO ,CPF_CNPJ ,
		ENDERECO ,CIDADE_UF ,CEDENTE ,MULTA ,JUROS ,TOLERANCIA ,TAXA_BOLETO ,DEMONSTRATIVO1 ,DEMONSTRATIVO2 ,DEMONSTRATIVO3 ,INSTRUCOES1 ,
		INSTRUCOES2 ,INSTRUCOES3)VALUES (
		'$conta->EMPRESA_ID',  '$conta->NOME_BANCO',  '$conta->AGENCIA_BANCO',  '$conta->N_CONTA',  '$conta->CONVENIO',  '$conta->CONTRATO',  
		'$conta->CARTEIRA',  '$conta->VARIACAO_CARTEIRA',  '$conta->IDENTIFICACAO',  '$conta->CPF_CNPJ',  '$conta->ENDERECO',  '$conta->CIDADE_UF',  
		'$conta->CEDENTE',  '$conta->MULTA',  '$conta->JUROS',  '$conta->TOLERANCIA',  '$conta->TAXA_BOLETO',  '$conta->DEMONSTRATIVO1',  '$conta->DEMONSTRATIVO2', 
		'$conta->DEMONSTRATIVO3',  '$conta->INSTRUCOES1',  '$conta->INSTRUCOES2',  '$conta->INSTRUCOES3')";
		$result = $this->conn->query($query);	
	}
	public function selecionarContaBancaria($id)
	{
		$query = "SELECT * FROM conta_bancaria WHERE ID = '$id'";
		$result = $this->conn->query($query);
		$nrow = $result->num_rows;
		if($nrow == 0){return 'ERRO';}
		$row = $result->fetch_assoc();
		
			$contasBancarias = new contasBancariasVO();
			$contasBancarias->ID = $row['ID'];
			$contasBancarias->EMPRESA_ID = $row['EMPRESA_ID'];
			$contasBancarias->NOME_BANCO = $row['NOME_BANCO'];
			$contasBancarias->AGENCIA_BANCO = $row['AGENCIA_BANCO'];
			$contasBancarias->N_CONTA = $row['N_CONTA'];
			$contasBancarias->CONVENIO = $row['CONVENIO'];
			$contasBancarias->CONTRATO = $row['CONTRATO'];
			$contasBancarias->CARTEIRA = $row['CARTEIRA'];
			$contasBancarias->VARIACAO_CARTEIRA = $row['VARIACAO_CARTEIRA'];
			$contasBancarias->IDENTIFICACAO = $row['IDENTIFICACAO'];
			$contasBancarias->CPF_CNPJ = $row['CPF_CNPJ'];
			$contasBancarias->ENDERECO = $row['ENDERECO'];
			$contasBancarias->CIDADE_UF = $row['CIDADE_UF'];
			$contasBancarias->CEDENTE = $row['CEDENTE'];
			$contasBancarias->MULTA = $row['MULTA'];
			$contasBancarias->JUROS = $row['JUROS'];
			$contasBancarias->TOLERANCIA = $row['TOLERANCIA'];
			$contasBancarias->TAXA_BOLETO = $row['TAXA_BOLETO'];
			$contasBancarias->DEMONSTRATIVO1 = $row['DEMONSTRATIVO1'];
			$contasBancarias->DEMONSTRATIVO2 = $row['DEMONSTRATIVO2'];
			$contasBancarias->DEMONSTRATIVO3 = $row['DEMONSTRATIVO3'];
			$contasBancarias->INSTRUCOES1 = $row['INSTRUCOES1'];
			$contasBancarias->INSTRUCOES2 = $row['INSTRUCOES2'];
			$contasBancarias->INSTRUCOES3 = $row['INSTRUCOES3'];
		
			return $contasBancarias;
	
	}
	public function editarContaBancaria(contasBancariasVO $conta)
	{
		$query = "UPDATE  conta_bancaria SET  NOME_BANCO =  '$conta->NOME_BANCO',
			AGENCIA_BANCO =  '$conta->AGENCIA_BANCO',N_CONTA =  '$conta->N_CONTA',CONVENIO =  '$conta->CONVENIO',CONTRATO =  '$conta->CONTRATO',
			CARTEIRA =  '$conta->CARTEIRA',VARIACAO_CARTEIRA =  '$conta->VARIACAO_CARTEIRA',IDENTIFICACAO =  '$conta->IDENTIFICACAO',
			CPF_CNPJ =  '$conta->CPF_CNPJ',ENDERECO =  '$conta->ENDERECO',CIDADE_UF =  '$conta->CIDADE_UF',CEDENTE =  '$conta->CEDENTE',
			MULTA =  '$conta->MULTA',JUROS =  '$conta->JUROS',TOLERANCIA =  '$conta->TOLERANCIA',TAXA_BOLETO =  '$conta->TAXA_BOLETO',
			DEMONSTRATIVO1 =  '$conta->DEMONSTRATIVO1',DEMONSTRATIVO2 =  '$conta->DEMONSTRATIVO2',DEMONSTRATIVO3 =  '$conta->DEMONSTRATIVO3',
			INSTRUCOES1 =  '$conta->INSTRUCOES1',INSTRUCOES2 =  '$conta->INSTRUCOES2',INSTRUCOES3 =  '$conta->INSTRUCOES3' 
			WHERE  ID ='$conta->ID'";
		$result = $this->conn->query($query);	
	}
	public function excluirContaBancaria($id)
	{
				
		$query = "DELETE FROM conta_bancaria WHERE ID = '$id'";
		$result = $this->conn->query($query);
	}
	
	//FUN��ES EM ESPERA
	public function liquidarBoletos($empresa, $nomeArquivo)
	{
	  /**
		* FUN��O QUE L� O ARQUIVO RETORNO DO BANCO DO BRASIL   
		* E DA BAIXA NOS BOLETOS BAIXADOS
		* 
		* @author Marconi C�sar
		* @email marconipcd@gmail.com.br
		* 
		*/

		//return 'ok';

		$strCaminhoAbsoluto = './util/upload/';
		$nome = $nomeArquivo;
		$strNomeDoArquivo = 'arquivosRet/'.$nome;
	
		//TENTA ABRIR ARQUIVO 
		if(!$arrArquivo = file($strCaminhoAbsoluto.$strNomeDoArquivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES))
	    return 'N�o foi poss�vel abrir o arquivo de retorno!';
        
		$i = 0;
		foreach($arrArquivo as &$linhas){
        
        //retira \r
        $linhas = str_replace(chr(13), "", $linhas);
        
        //retira \n
        $linhas = str_replace(chr(10), "", $linhas);
        
        //apaga as linhas vazias
        if(empty($linhas))
        unset($arrArquivo[$i]);
                
        //proxima linha
        $i++;
		}

		$HA = $arrArquivo[0];
		$nTotalLinhas = count($arrArquivo);
		$TA = $arrArquivo[$nTotalLinhas-1];
		unset($arrArquivo[0]);
		unset($arrArquivo[$nTotalLinhas-1]);
		sort($arrArquivo);
		$HL = $arrArquivo[0];
		$nTotalLinhas = count($arrArquivo);
		$TL = $arrArquivo[$nTotalLinhas-1];
		unset($arrArquivo[0]);
		unset($arrArquivo[$nTotalLinhas-1]);
		sort($arrArquivo);
		$nRegistros = count($arrArquivo);

	
		for($i=0; $i<$nRegistros; $i++)
		{
	        
	        //obt�m o registro atual
	        $registroAtual = $arrArquivo[$i];
	        //TIRA ESPACOS EM BRANCO
	        $registroAtual = str_replace(" ", "",$registroAtual);
	        //$registroAtual = str_replace(chr(10), "",$registroAtual);
	        
					
			/*
			 * TRATAMENTO DE VALORES
			 */
			$banco_digital =  ltrim(substr($registroAtual, 35, 17), "0");
			$dataPago = substr($registroAtual, -34);
			$dataPago = rtrim(substr($dataPago, 0, 8), "0");
			$valor = substr($registroAtual, -84);
			$valor = ltrim(substr($valor, 0,15), "0");
			$valor2 = $valor;
			$valor2;
			
			
			
			 

 			
			if($banco_digital != '')
			{	
				if(strlen($banco_digital) == '17')
				{
					$nmeros[] = $banco_digital;
				}
				
			}
			if($dataPago != '')
			{
				$datas[] = $dataPago;
				
			}
			if ($valor2 != '')
			{
				$qtd = strlen($valor2);

				
				if($qtd == 13)
				{
					
					$qtd = 2;
					$valor2 = rtrim($valor2,"0");
					$valores[] = number_format($valor2,0,',','.');
				}
				if($qtd == 14)
				{
				
					$casas = 2;
					//$valor2 = rtrim($valor2,"0");
					$qtd2 = strlen($valor2);
					$valor3 = substr($valor2, 0, 2);
					$valor4 = $valor3.".".substr($valor3, -1);
					$valores[] = number_format($valor4,2,',','.');
				}
				if($qtd == 15)
				{
					
					$casas = 1;
					$valor2 = rtrim($valor2,"0");
					$qtd2 = strlen($valor2);
					$valor3 = substr($valor2, 0, $qtd2-$casas);
					$valor4 = $valor3.".".substr($valor2, -1);
					$valores[] = number_format($valor4,2,',','.');
					
				}
				
				
			}
			
				
					
		}
	  	
			//return $valores;
			

			
			
			$tamanho = count($nmeros);
			
			for($i=0;$i<$tamanho;$i++)
			{
				$nNumero = $nmeros[$i];
				$DataPagamento = $datas[$i];
				$ValorPagamento = $valores[$i];
				
				$query = "INSERT INTO registro_liquidado (ID ,NOSSO_NUMERO ,VALOR_PAGO ,DATA_PAGO, STATUS, EMPRESA)VALUES 
				(NULL , '$nNumero', '$ValorPagamento', '$DataPagamento','A','$empresa')";
				$result = $this->conn->query($query);
				
							
				//STATUS A = ABERTO | F=FECHADO
			}
		
		
			$query = "SELECT * FROM registro_liquidado WHERE STATUS = 'A'";
			$result = $this->conn->query($query);
			
			while($row = $result->fetch_assoc())
			{
				$nossoNumero = substr($row['NOSSO_NUMERO'], -10);
				$valorPago = $row['VALOR_PAGO'];
				$dataPgto = substr($row['DATA_PAGO'], 0, 8);
				$empresa = $row['EMPRESA'];
				$idRegistro = $row['ID'];
				
				$dia = substr($dataPgto, 0, 2);
				$mes = substr($dataPgto, 2, 2);
				$ano = substr($dataPgto, 4, 4);
				
				$dataPgto = $ano."-".$mes."-".$dia;			
				$digitalnNumeros = substr($row['NOSSO_NUMERO'], 0, 7);						
				
				$queryBaixa = "SELECT *
				FROM contas_receber WHERE N_NUMERO  LIKE '$nossoNumero'";
				$resultBaixa = $this->conn->query($queryBaixa);
				$nrowBaixa = $resultBaixa->num_rows;
				$row = $resultBaixa->fetch_assoc();
				$idConta = $row['ID'];
				$vencimentoConta = $row['DATA_VENCIMENTO'];
				$codCliente = $row['CLIENTES_ID'];
				
				if($nrowBaixa > 0)
				{
					$diaAtual = date('d');
					$mesAtual = date('m');
					$anoAtual = date('Y');
					
					$vencimentoConta = explode('-', $vencimentoConta);
							
					#setando a primeira data  10/01/2008
					$dataAtual = mktime(0,0,0,$mesAtual,$diaAtual,$anoAtual);
					#setando segunda data 10/02/2008
					$dataBoleto = mktime(0,0,0,$vencimentoConta[1],$vencimentoConta[2],$vencimentoConta[0]);  
					#armazenando o valor da subtracao das datas
					$d3 = ($dataAtual-$dataBoleto);
					#usando o roud para arrendondar os valores
					#converter o tempo em dias
					$dias = round(($d3/60/60/24));
					// condi��o de aplicar bloqueio
					$prazo = '5';
					
					if($dias >= $prazo){
						$desbloquear = 'S';
					}else
					{
						$desbloquear = null;
					}
					
					
					
					$queryUpdate = "UPDATE contas_receber SET 
					STATUS_2 = 'FECHADO',DATA_PAGAMENTO = '$dataPgto',
					VALOR_PAGAMENTO = '$valorPago',FORMA_PGTO = 'BANCO',
					TIPO_BAIXA = 'liquidado',DESBLOQUEAR = '$desbloquear' WHERE ID ='$idConta'";	
					
					$resultUpdate = $this->conn->query($queryUpdate);
					$queryUpdate2= "UPDATE registro_liquidado SET STATUS = 'F' WHERE ID = '$idRegistro'";	
					$resultUpdate2 = $this->conn->query($queryUpdate2);
					
//					if($desbloquear == 'S')
//					{
//						$queryDesbloqueio = "SELECT * FROM CLIENTE WHERE  ID = '$codCliente'";
//						$resultDesbloqueio = $this->conn->query($queryDesbloqueio);
//						$nrowdb = $resultDesbloqueio->num_rows;
//						
//						
//						if($nrowdb >0)
//						{
//							$rowdb = $resultDesbloqueio->fetch_assoc();
//
//							//VARIAVEIS
//							$plano = $rowdb['planoCliente'];
//							$usuario = $rowdb['loginCliente'];
//							
//							$queryUpdatePessoa = "UPDATE pessoa SET 
//							plano = '$plano' WHERE codigoPessoa ='$codCliente'";
//							$resultUpdatePessoa = $this->conn->query($queryUpdatePessoa);
//							
//							$queryUpdateRadius = "UPDATE radreply SET value = '$plano' WHERE username = '$usuario'";
//							$resultUpdateRadius  = $this->conn->query($queryUpdateRadius);
//						}
//					}
				}
			}		
		
			return 'ok';
	
	}
	public function listarTitulos($empresa, $status, $data, $dataI, $dataF, $filtro, $vlr_filtro, $cliente)
		{
			
				//DATAS	
				if($data == 'EMISSAO')
				{
					$data1 = 'DATA_EMISSAO';
				}else if($data == 'VENCIMENTO')
				{
					$data1 = 'DATA_VENCIMENTO';
				}else if($data == 'PAGAMENTO')
				{
					$data1 = 'DATA_PAGAMENTO';
				}
				
				if($dataI != " ")
				{
					$diaIn = substr($dataI, 0, 2);
					$mesIn = substr($dataI, 3, 2);
					$anoIn = substr($dataI, 6, 4);
				
					$dataIn = $anoIn.'-'.$mesIn.'-'.$diaIn;
				}else
				{
					$dataIn = "1800-00-00";
				}
				
				$diaFn = substr($dataF, 0, 2);
				$mesFn = substr($dataF, 3, 2);
				$anoFn = substr($dataF, 6, 4);
				
				$dataFn = $anoFn.'-'.$mesFn.'-'.$diaFn;
				
					//FILTROS	
					if($filtro == 'N. DOCUMENTO')
					{
						$filtro1 = 'N_DOC';
					}else if($filtro == 'N. NUMERO')
					{
						$filtro1 = 'N_NUMERO';
					}else if($filtro == 'VALOR DOC')
					{
						$filtro1 = 'VALOR_TITULO';
					}else if($filtro == 'VALOR PAGO')
					{
						$filtro1 = 'VALOR_PAGAMENTO';
					}else if($filtro == 'CONTROLE')
					{
						$filtro1 = 'CONTROLE';
					}
				
			
			if($data == '')
			{
				if($filtro == '')
				{
				

					$query = "
					SELECT contas_receber . * , clientes.NOME_RAZAO
					FROM contas_receber
					LEFT JOIN clientes ON contas_receber.CLIENTES_ID = clientes.ID
					WHERE contas_receber.CLIENTES_ID = clientes.ID
					AND contas_receber.STATUS_2 LIKE '%$status%' 
					AND clientes.NOME_RAZAO LIKE '%$cliente%' 
					AND contas_receber.EMPRESA_ID=$empresa
					ORDER by contas_receber.DATA_VENCIMENTO ASC";
					
					
				}else 
				{
										
					$query = "SELECT contas_receber . * , clientes.NOME_RAZAO
					FROM contas_receber
					LEFT JOIN clientes ON contas_receber.CLIENTES_ID = clientes.ID
					WHERE contas_receber.CLIENTES_ID = clientes.ID
					AND contas_receber.STATUS_2 LIKE '%$status%'
					AND clientes.NOME_RAZAO LIKE '%$cliente%'
					AND contas_receber.".$filtro1." LIKE '%$vlr_filtro%'
					AND contas_receber.EMPRESA_ID=$empresa
					ORDER BY contas_receber.DATA_VENCIMENTO ASC";
				}
				
			}else
			{
				
								
				
				
				if($filtro == '')
				{
					
					
					$query = "SELECT contas_receber . * , clientes.NOME_RAZAO
					FROM contas_receber
					LEFT JOIN clientes ON contas_receber.CLIENTES_ID = clientes.ID
					WHERE contas_receber.CLIENTES_ID = clientes.ID
					AND contas_receber.STATUS_2 LIKE '%$status%'
					AND clientes.NOME_RAZAO LIKE '%$cliente%'
					AND contas_receber.".$data1." >= '$dataIn'
					AND contas_receber.".$data1." <= '$dataFn'
					AND contas_receber.EMPRESA_ID=$empresa
					ORDER BY contas_receber.DATA_VENCIMENTO ASC";	
					
				}else 
				{
					
					$query = "SELECT contas_receber . * , clientes.NOME_RAZAO
					FROM contas_receber
					LEFT JOIN clientes ON contas_receber.CLIENTES_ID = clientes.ID
					WHERE contas_receber.CLIENTES_ID = clientes.ID
					AND contas_receber.STATUS_2 LIKE '%$status%'
					AND clientes.NOME_RAZAO LIKE '%$cliente%'
					AND contas_receber.".$data1." >= '$dataIn'
					AND contas_receber.".$data1." <= '$dataFn'
					AND contas_receber.".$filtro1." LIKE '%$vlr_filtro%'
					AND contas_receber.EMPRESA_ID=$empresa
					ORDER BY contas_receber.DATA_VENCIMENTO ASC";	
				}
			}
			
			
			
			if(!$result = $this->conn->query($query))
			{
				return $this->conn->error;
			}
			$qtd = $result->num_rows;
			
			
				if($qtd >0)
				{
				while($row = $result->fetch_assoc())
				{
					$titulo = new ContasReceberVO();
					$cliente = $row['cliente'];
					$dataAtual = date('Y-m-d');
					

					
									
					
					$titulo->ID = $row['ID'];
					$titulo->CLIENTES_ID = $row['CLIENTES_ID'];
					$titulo->NOME_RAZAO = $row['NOME_RAZAO'];
					$titulo->EMPRESA_ID = $row['EMPRESA_ID'];
					$titulo->N_DOC = $row['N_DOC'];
					$titulo->N_NUMERO = $row['N_NUMERO'];
					$titulo->VALOR_TITULO = $row['VALOR_TITULO'];
					$titulo->VALOR_PAGAMENTO = $row['VALOR_PAGAMENTO'];
					$titulo->DATA_EMISSAO = $row['DATA_EMISSAO'];
					$titulo->DATA_VENCIMENTO = $row['DATA_VENCIMENTO'];
					$titulo->DATA_PAGAMENTO = $row['DATA_PAGAMENTO'];
					$titulo->DATA_BAIXA = $row['DATA_BAIXA'];
					$titulo->DATA_EXCLUSAO = $row['DATA_EXCLUSAO'];
					$titulo->FORMA_PGTO = $row['FORMA_PGTO'];
					$titulo->TIPO_BAIXA = $row['TIPO_BAIXA'];
					$titulo->CONTROLE = $row['CONTROLE'];
					$titulo->CENTRO_CUSTO = $row['CENTRO_CUSTO'];
					$titulo->STATUS_2 = $row['STATUS_2'];
					$titulo->DESBLOQUEAR = $row['DESBLOQUEAR'];
					$titulo->BLOQUEAR = $row['BLOQUEAR'];
					$titulo->DESBLOQUEADO = $row['DESBLOQUEADO'];
					$titulo->BLOQUEADO = $row['BLOQUEADO'];
					
					
					$titulos[] = $titulo;
					
				}
				
					return $titulos;
				}else
				{
					return $erro = 'Nenhum Registro Foi Encontrado Com Esses Crit�rios de Busca';
				}
		}
	
	
	public function gerarContasReceber(ContasReceberVO $contas, $qtd)
	{
				
		
				//Gerar Boletos
				///////////////////////				
							
				$quantidade = $qtd;
   				$_prazo = 0;   
   				
   				
    		  
				if($contas->N_DOC == '')
				{
					for($i=0;$i < $quantidade;$i++)
	   				{
			
						$sequencia = $i;
						if($i ==0){
							$sequencia++;
						}else if($i == $sequencia){
							$sequencia++;
						}
						
						$sequencia2 = str_pad($sequencia, 2, "0", STR_PAD_LEFT);
						$ano = date('y');
	
						//LOCALIZA O ULTIMO BOLETO DO CLIENTE GERADO
						$queryUltimoBoleto = "SELECT * FROM contas_receber WHERE CLIENTES_ID = '$codigoCliente' ORDER by ID DESC";
						$resultUltimoBoleto = $this->conn->query($queryUltimoBoleto);
						$rowUltimoBoleto = $resultUltimoBoleto->fetch_assoc();			
				    	$nRowUltimoBoleto = $resultUltimoBoleto->num_rows;
				    	
				    	if($nRowUltimoBoleto == 0)
				    	{
				    		$ultimoNumero = str_pad("1", 6, "0", STR_PAD_LEFT);
				    	}else
				    	{
				    		$ultimoValor = ltrim(substr($rowUltimoBoleto['N_NUMERO'], -6)+1, "0");			    		
				    		$ultimoNumero = str_pad($ultimoValor, 6, "0", STR_PAD_LEFT);			    		
				    	}
				    	
	           			$NumeroNovo = $contas->CLIENTES_ID.$ultimoNumero;
						
						
						//$dataPrimeiroBoleto = "10/01/2011";
						$_dia = substr($contas->DATA_VENCIMENTO, 0, 2);
	   					$_mes = substr($contas->DATA_VENCIMENTO, 3, 2);
	   					$_ano  = substr($contas->DATA_VENCIMENTO, 6, 4);
				
		   				$_ts = mktime(0,0,0,$_mes+$_prazo,$_dia,$_ano);
	       				$_data = date('Y-m-d',$_ts);
		   				
		   				$controle = $NumeroNovo.' '.$i;
		  				
						$emissao = date('Y-m-d');					
						$ndocumento = $contas->CLIENTES_ID.'/'.$ano.'/'.$sequencia2; 					
						
						//Inserindo
						$sql = "INSERT INTO db_opus.contas_receber (CLIENTES_ID, EMPRESA_ID, N_DOC, N_NUMERO, VALOR_TITULO, 
						DATA_EMISSAO, DATA_VENCIMENTO, CONTROLE, STATUS_2) VALUES ('$contas->CLIENTES_ID', $contas->EMPRESA_ID, '$contas->N_DOC', 
						'$NumeroNovo', '$contas->VALOR_TITULO', '$emissao', '$_data', 
						'$controle', 'ABERTO')";
						 
						$resultadoSql = $this->conn->query($sql);
	        			
	        			//supondo que o vencimento � de 30 em 30 dias 						
	        			$_prazo += 1;	    			
	    			}
				}else{
				for($i=0;$i < $quantidade;$i++)
   				{
		
					$sequencia = $i;
					if($i ==0){
						$sequencia++;
					}else if($i == $sequencia){
						$sequencia++;
					}
					
					$sequencia2 = str_pad($sequencia, 2, "0", STR_PAD_LEFT);
					$ano = date('y');

					
       
					$queryUltimoBoleto = "SELECT * FROM contas_receber WHERE CLIENTES_ID = '$contas->CLIENTES_ID' ORDER by ID DESC";
					$resultUltimoBoleto = $this->conn->query($queryUltimoBoleto);
					$rowUltimoBoleto = $resultUltimoBoleto->fetch_assoc();			
			    	$nRowUltimoBoleto = $resultUltimoBoleto->num_rows;
			    	if($nRowUltimoBoleto == 0)
			    	{
			    		$ultimoNumero = str_pad("1", 6, "0", STR_PAD_LEFT);
			    	}else
			    	{
			    		$ultimoValor = ltrim(substr($rowUltimoBoleto['N_NUMERO'], -6)+1, "0");
			    		
			    		$ultimoNumero = str_pad($ultimoValor, 6, "0", STR_PAD_LEFT);
			    		
			    	}
			    	
           			$NumeroNovo = $contas->CLIENTES_ID.$ultimoNumero;
					
					//Serie do ultimo boleto gerado
					$ultimoBoleto = $ultimoNumero+1;
					
					//$dataPrimeiroBoleto = "10/01/2011";
					$_dia = substr($contas->DATA_VENCIMENTO, 0, 2);
   					$_mes = substr($contas->DATA_VENCIMENTO, 3, 2);
   					$_ano  = substr($contas->DATA_VENCIMENTO, 6, 4);
			
	   				$_ts = mktime(0,0,0,$_mes+$_prazo,$_dia,$_ano);
       				$_data = date('Y-m-d',$_ts);	   				
	  				
					$emissao = date('Y-m-d');					
					
									
					$sql = "INSERT INTO db_opus.contas_receber (CLIENTES_ID, EMPRESA_ID, N_DOC, N_NUMERO, VALOR_TITULO, 
						DATA_EMISSAO, DATA_VENCIMENTO, CONTROLE, STATUS_2) VALUES ('$contas->CLIENTES_ID', $contas->EMPRESA_ID, '$contas->N_DOC', 
						'$NumeroNovo', '$contas->VALOR_TITULO', '$emissao', '$_data', 
						'$contas->N_DOC', 'ABERTO')";
					 
					$resultadoSql = $this->conn->query($sql);
        			
        			//supondo que o vencimento � de 30 em 30 dias 
					
        			$_prazo += 1;
        			
    			
    			}
		}
		
		 	return 'ok';
		}
	
	
	
}