<?php

ini_set('max_execution_time','2000000');

		$conexaoLocal = mysql_pconnect("localhost","root","root");
		$xml = simplexml_load_file("PRODUTOS_DBF.XML");
		$xml2 = simplexml_load_file("FORNECE_DBF.XML");
		
		//MIGRAR FORNECEDORES
		foreach($xml2->ROW as $row2)
		{		
			
			
			$query = "INSERT INTO db_opus.fornecedores (EMPRESA_ID, RAZAO_SOCIAL, FANTASIA, CNPJ, ENDERECO, BAIRRO, CIDADE, UF, CEP, FONE1, FONE2, FAX, 
			DTPVENDAS, EMAIL, HOME_PAGE, REPRESENTANTE, NOME_REPRESENTANTE, CONTATO_REPRESENTANTE, CIDADE_REPRESENTATE, UF_REPRESENTANTE, 
			FONE1_REPRESENTANTE, FONE2_REPRESENTANTE, FAX_REPRESENTANTE, CEL_REPRESENTANTE, DATA_CADASTRO) VALUES (0, '$row2->RAZAO', '$row2->FANTASIA', 
			'$row2->CNPJ', '$row2->ENDERECO', '$row2->BAIRRO', 
			'23', 'pe', '123', '123', '123', '123', '123', '123', '123', '123', '123', '123', '123', 'pe', '123', '123', '123', '123', '2012-04-16')";
	
			$result = mysql_query($query,$conexaoLocal) or die(mysql_error());
		}

		//MIGRAR PRODUTOS
		foreach($xml->ROW as $row)
		{	
			$row->DESCRICAO = str_replace("'", "", $row->DESCRICAO);
			$DESC_PDV = substr($row->DESCRICAO, 0, 30);
			$VALOR_CUSTO = str_replace(",", ".", $row->CUSTO);
			$VALOR_VENDA = str_replace(",", ".", $row->VENDA);
			$LUCRO = str_replace(",", ".", $row->LUCRO);
			$IPI = str_replace(",", ".", $row->IPI);
			$ICMS = str_replace(",", ".", $row->ICMS);
			$DATA = date('Y-m-d');
			$HORA = date('h:m:s');
			
			//OBTEM A QUANTIDADE DE PRODUTOS EM R$
			$QTD = $row->SALDO;
			
			
			if($row->GRUPO == 0001)
			{
				$EMPRESA = 3;
				$GRUPO = 0004;
			}else if($row->GRUPO == 0002)
			{
				$EMPRESA = 2;
				$GRUPO = 0001;
			}else if($row->GRUPO == 0003)
			{
				$EMPRESA = 3;
				$GRUPO = 0003;
			}else if($row->GRUPO == 0004)
			{
				$EMPRESA = 2;
				$GRUPO = 0002;
			}else if($row->GRUPO == 0005)
			{
				$EMPRESA = 3;
				$GRUPO = 0005;
			}else{
				$EMPRESA = 0;
				$GRUPO = 0;
			}
			
			
			if($EMPRESA == 2)
			{
				$UNIDADE = 2;
			}else if($EMPRESA == 3)
			{
				$UNIDADE = 17;
			}else{
				$UNIDADE = 0;	
			}
			
			//PEGAR COD FORNECEDOR
			$queryCodFornecedor = "SELECT * FROM db_opus.fornecedores where CNPJ LIKE '$row->FORNECEDOR'";
			$resultCodFornecedor = mysql_query($queryCodFornecedor);
			$nRowCodFornecedor = mysql_num_rows($resultCodFornecedor);
			if($nRowCodFornecedor == 1)
			{
				$rowCodFornecedor = mysql_fetch_assoc($resultCodFornecedor);
				$COD_FORNECEDOR = $rowCodFornecedor['ID'];
			}else{
				$COD_FORNECEDOR = 0;
			}
					
			
			echo $row->DESCRICAO.'<br/>';
			$query = "INSERT INTO db_opus.produto (EMPRESA_ID, FORNECEDOR_ID, GRUPO_ID, ID_UNIDADE_PRODUTO, GTIN, CODIGO_INTERNO, NOME, DESCRICAO, 
			DESCRICAO_PDV, VALOR_CUSTO, VALOR_VENDA, QTD_ESTOQUE, QTD_ESTOQUE_ANTERIOR, ESTOQUE_MIN, ESTOQUE_MAX, IAT, IPPT, NCM, TIPO_ITEM_SPED, 
			DATA_ESTOQUE, HORA_ESTOQUE, TAXA_IPI, TAXA_ISSQN, TAXA_PIS, TAXA_COFINS, TAXA_ICMS, CST, CSOSN, TOTALIZADOR_PARCIAL, ECF_ICMS_ST, 
			CODIGO_BALANCA, PAF_P_ST, LUCRO, GARANTIA) VALUES ($EMPRESA, $COD_FORNECEDOR, $GRUPO,$UNIDADE, '$row->CODBARRAS', '$row->CODIGO', '$row->DESCRICAO', '$row->DESCRICAO', 
			'$DESC_PDV', 
			$VALOR_CUSTO, $VALOR_VENDA, $QTD, 0.000000, 10.000000, 4000.000000, 'A', 'T', '40129010', '04', '$DATA', '$HORA', 
			$IPI, 0, 0, 0, $ICMS, '0', 
			'0', '', 'NN', 0, 'N', $LUCRO, '$row->GARANTIA')";
			
			$result = mysql_query($query,$conexaoLocal) or die(mysql_error());
			
		}
		
		
		//OBTEM REGISTRO DE FORNECEDORES
		$queryFornecedores = "SELECT * FROM db_opus.fornecedores ORDER by ID";
		$resultFornecedores = mysql_query($queryFornecedores);
		
		while ($rowFornecedores = mysql_fetch_assoc($resultFornecedores))
		{
			$COD_FORNECEDOR = $rowFornecedores['ID'];
			
			//VERIFICA SE ALGUM PRODUTO PERTENCE AO FORNECEDOR ATUAL
			$queryProduto = "SELECT * FROM db_opus.produto WHERE FORNECEDOR_ID=$COD_FORNECEDOR";
			$resultProduto = mysql_query($queryProduto);
			$nRowProduto = mysql_num_rows($resultProduto);
			if($nRowProduto > 0)
			{
				$rowProduto = mysql_fetch_assoc($resultProduto);
				$EMPRESA_ID = $rowProduto['EMPRESA_ID'];
				
				
			}else{
				$EMPRESA_ID = 2;
			}
			
			$queryAtualizarFornecedor = "UPDATE fornecedores SET EMPRESA_ID = $EMPRESA_ID WHERE ID=$COD_FORNECEDOR";
			$resultAtualizarFornecedor = mysql_query($queryAtualizarFornecedor)or mysql_error();
		}
		
		
		