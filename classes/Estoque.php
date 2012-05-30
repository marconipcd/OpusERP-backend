<?php

require_once 'classes/Conexao.php';
require_once('util/libs/html2pdf/html2pdf.class.php');

require_once 'vo/ProdutosVO.php';
require_once 'vo/UnidadeProdutoVO.php';
require_once 'vo/GrupoProdutoVO.php';
require_once 'vo/ClientesVO.php';
require_once 'vo/EndEntregaVO.php';
require_once 'vo/MovimentoEntCabecalhoVO.php';
require_once 'vo/MovimentoEntDetalheVO.php';
require_once 'vo/DavDetalheVO.php';

ini_set('max_execution_time','2000');

class Estoque extends Conexao
{
	/**
	 * Função Retorna Cod do Prox. Movimento de Entrada Cabecalho de NF
	 * @author Marconi César
	 * @name pegaCodMovimentoNF
	 */
	public function pegaCodMovimentoNF()
	{
		$query = "SHOW TABLE STATUS LIKE 'movimento_ent_cabecalho'";
		
		$result = $this->conn->query($query); 
		$row = $result->fetch_assoc();	
		
		if($row["Auto_increment"] == null)
		{
		    $row["Auto_increment"] = 1;
		}
				
		return $codigo = $row["Auto_increment"];
	}
	
	/**
	 * Função Cadastra Movimento de Entrada Cabecalho
	 * @author Marconi César
	 * @name cadastrarCabeEntMovNF
	 */
	public function cadastrarCabeEntMovNF(MovimentoEntCabecalhoVO $mecVO)
	{
		$resultado = true;
		$this->conn->autocommit(false);
		
		//LOCALIZA ITENS MOVIMENTO DETALHE DO MOVIMENTO CABECALHO INFORMADO
		$query0 = "SELECT * FROM movimento_ent_detalhe WHERE MOVIMENTO_ENT_CABECALHO_ID='$mecVO->ID'";
		$result0 = $this->conn->query($query0);
		
		
		
		if($result0->num_rows == 0)
		{
			$resultado = false;
			
		}		
		
		$valorTotal = 0;
		while($row0 = $result0->fetch_assoc())
		{
			$valorTotal = $valorTotal+$row0['VALOR_CUSTO'];
			
			$ID = $row0['PRODUTO_ID'];
			$UNIDADE_PRODUTO_ID = $row0['UNIDADE_PRODUTO_ID'];
			$VALOR_CUSTO = $row0['VALOR_CUSTO'];
			$VALOR_VENDA = $row0['VALOR_VENDA'];
			$ICMS = $row0['ICMS'];
			$IPI = $row0['IPI'];
			$DARF = $row0['DARF'];
			$GARANTIA = $row0['GARANTIA'];
			$QTD_ESTOQUE = $row0['QTD'];
			
			//VERIFICAR QUANTIDADE JA EXISTENTE
			$query1 = "SELECT * FROM produto WHERE ID=$ID";
			$result1 = $this->conn->query($query1);
			
			//CASO NAO ENCONTRE O PRODUTO INFORMADO A CONDICAO FALHARA
			if($result1->num_rows == 0)
			{
				$resultado = false;
				
			}		
			
			//RESULTADO DA BUSCA PELO PRODUTO INFORMADO
			$row1 = $result1->fetch_assoc();
						
			//DEFINE A QUANTIDADE JA EXISTENTE
			$qtdAntiga = $row1['QTD_ESTOQUE'];
			
			//DEFINE A NOVA QUANTIDADE
			$qtdNova = $qtdAntiga + $row0['QTD'];
			
			//ATUALIZAR CADASTRO DE PRODUTOS
			$queryUP = "UPDATE  db_opus.produto SET  ID_UNIDADE_PRODUTO =  '$UNIDADE_PRODUTO_ID',
			VALOR_CUSTO =  '$VALOR_CUSTO',VALOR_VENDA =  '$VALOR_VENDA',GARANTIA =  '$GARANTIA',
			QTD_ESTOQUE =  '$QTD_ESTOQUE',
			QTD_ESTOQUE_ANTERIOR =  '$qtdAntiga',TAXA_IPI =  '$IPI',TAXA_ICMS =  '$ICMS' WHERE ID = $ID";
			
			$resultUP = $this->conn->query($queryUP)or die($this->conn->error);
			
		}
		
		$mecVO->QTD_ITENS = $result0->num_rows;
		$mecVO->VALOR_TOTAL = $valorTotal;
		
		
		
		$query = "INSERT INTO movimento_ent_cabecalho (ID,EMPRESA_ID, COD_NF, FORNECEDOR_ID, QTD_ITENS, VALOR_TOTAL)
		VALUES ($mecVO->ID,$mecVO->EMPRESA_ID,$mecVO->COD_NF,$mecVO->FORNECEDOR_ID,$mecVO->QTD_ITENS,$valorTotal)";
		
		$result = $this->conn->query($query);
		
		
		if($this->conn->error)
		{
		    $erro = $this->conn->error;
		    $this->conn->rollback();
		    $this->conn->autocommit(true);
		    return 'ERRO AO CADASTRAR MOVIMENTO CABECALHO2:'.$error;
		}else{
		    
		   
		    if($resultado)
		    {
			$this->conn->commit();
			$this->conn->autocommit(true);
			return 'ok';
		    }else{
			$this->conn->rollback();
			$this->conn->autocommit(true);
			return 'ERRO AO CADASTRAR MOVIMENTO CABECALHO: '.$this->conn->error;
		    }
		}		
	}
	
	/**
	 * Função Cadastra Movimento de Entrada Detalhe(itens)
	 * @author Marconi César
	 * @name cadastrarDetaEntMovNF
	 */
	public function cadastrarDetaEntMovNF(MovimentoEntDetalheVO $medVO)
	{
		$query = "INSERT INTO movimento_ent_detalhe (MOVIMENTO_ENT_CABECALHO_ID, PRODUTO_ID, UNIDADE_PRODUTO_ID, QTD, VALOR_CUSTO,
		VALOR_VENDA, ICMS, IPI, DARF, GARANTIA) VALUES 
		('$medVO->MOVIMENTO_ENT_CABECALHO_ID', '$medVO->PRODUTO_ID', '$medVO->UNIDADE_PRODUTO_ID', 
		'$medVO->QTD', '$medVO->VALOR_CUSTO', '$medVO->VALOR_VENDA', '$medVO->ICMS', '$medVO->IPI',
		'$medVO->DARF', '$medVO->GARANTIA')";
		
		if(!$result = $this->conn->query($query))
		{
			return 'CADASTRAR DETALHE DE ENTRADA DE MOVIMENTO: ERRO '.$this->conn->error;
		}
		
		return 'ok';
	}	
	
	
	/**
	 * Função Utilizada Para Calcular Valor Passados por Uma String Separados por |(PIPE)
	 * @author Marconi César
	 * @name calcularTotal
	 */
	public function calcularTotal($codPrevenda,$valores)
	{
		$arrValores = explode('|', $valores);
				
			foreach ($arrValores as $arrValores2)
			{
				$valorTotal += $arrValores2;
			}
			
			$query = "UPDATE ecf_pre_venda_cabecalho SET VALOR=$valorTotal WHERE ID='$codPrevenda'";
			$result = $this->conn->query($query);
		
		return $valorTotal;
	}
	
	/**
	 * Função Utilizada Para Calcular Valor Passados por Uma String Separados por |(PIPE)
	 * De Um DAV
	 * @author Marconi César
	 * @name calcularTotal
	 */
	public function calcularTotalDAV($codDAV,$valores)
	{
		$arrValores = explode('|', $valores);
				
			foreach ($arrValores as $arrValores2)
			{
				$valorTotal += $arrValores2;
			}
			
			$query = "UPDATE dav_cabecalho SET SUBTOTAL=$valorTotal,VALOR=$valorTotal WHERE ID='$codDAV'";
			$result = $this->conn->query($query);
		
		return $valorTotal;
	}
	
	
	/**
	 * Função Utilizada Para Fechar um DAV
	 * @author Marconi César
	 * @name calcularTotal
	 */
	public function fecharDAV($codDAV, $formaPgto, $desconto, $acrescimo)
	{
		//VERIFICA SE FOI DADOS ALGUM DESCONTO
		
		//VERIFICA SE FOI DADO ALGUM ACRESCIMO
		
		//GERA PDF
		$content = "<page backtop='0mm' backbottom='0mm' backleft='10mm' backright='10mm' >";
                $content .= "teste";							
		$content .= "</page>";		
		
		$html2pdf = new HTML2PDF('P','A4','pt', true, 'ISO-8859-1', array(0, 0, 0, 0));
		$html2pdf->writeHTML($content);
		$html2pdf->Output('DAV'.$codDAV.'.pdf',true);
		
		
	}
	
	/**
	 * Função Utilizada Para Listar Produtos Cadastrados de Uma Empresa Informada
	 * @author Marconi César
	 * @name listarProdutos
	 */
	public function listarProdutos($codEmpresa)
	{
		$query = "SELECT * FROM produto WHERE EMPRESA_ID='$codEmpresa' ORDER by NOME ASC";
		$result = $this->conn->query($query);
		$nRow = $result->num_rows;
		if($nRow ==0){return 'ERRO';}
		
		while ($row = $result->fetch_assoc())
		{
			$produto = new ProdutosVO();
			
			$produto->ID = $row['ID'];
			$produto->EMPRESA_ID = $row['EMPRESA_ID'];
			$produto->FORNECEDOR_ID = $row['FORNECEDOR_ID'];
			$produto->GRUPO_ID = $row['GRUPO_ID'];
			$produto->ID_UNIDADE_PRODUTO = $row['ID_UNIDADE_PRODUTO'];
			$produto->GTIN = $row['GTIN'];
			$produto->CODIGO_INTERNO = $row['CODIGO_INTERNO'];
			$produto->NOME = $row['NOME'];
			$produto->DESCRICAO = $row['DESCRICAO'];
			$produto->DESCRICAO_PDV = $row['DESCRICAO_PDV'];
			$produto->VALOR_CUSTO = $row['VALOR_CUSTO'];
			$produto->VALOR_VENDA = $row['VALOR_VENDA'];
			$produto->QTD_ESTOQUE = $row['QTD_ESTOQUE'];
			$produto->QTD_ESTOQUE_ANTERIOR = $row['QTD_ESTOQUE_ANTERIOR'];
			$produto->ESTOQUE_MIN = $row['ESTOQUE_MIN'];
			$produto->ESTOQUE_MAX = $row['ESTOQUE_MAX'];
			$produto->GARANTIA = $row['GARANTIA'];
			$produto->IAT = $row['IAT'];
			$produto->IPPT = $row['IPPT'];
			$produto->NCM = $row['NCM'];
			$produto->TIPO_ITEM_SPED = $row['TIPO_ITEM_SPED'];
			$produto->DATA_ESTOQUE = $row['DATA_ESTOQUE'];
			$produto->HORA_ESTOQUE = $row['HORA_ESTOQUE'];
			$produto->TAXA_IPI = $row['TAXA_IPI'];
			$produto->TAXA_ISSQN = $row['TAXA_ISSQN'];
			$produto->TAXA_PIS = $row['TAXA_PIS'];
			$produto->TAXA_COFINS = $row['TAXA_COFINS'];
			$produto->TAXA_ICMS = $row['TAXA_ICMS'];
			$produto->CST = $row['CST'];
			$produto->CSOSN = $row['CSOSN'];
			$produto->TOTALIZADOR_PARCIAL = $row['TOTALIZADOR_PARCIAL'];
			$produto->ECF_ICMS_ST = $row['ECF_ICMS_ST'];
			$produto->CODIGO_BALANCA = $row['CODIGO_BALANCA'];
			$produto->PAF_P_ST = $row['PAF_P_ST'];
			$produto->HASH_TRIPA = $row['HASH_TRIPA'];
			$produto->HASH_INCREMENTO  = $row['HASH_INCREMENTO'];
			
			$produtos[] = $produto;
		}
		
		return $produtos;
		
	}
	
	
	/**
	 * Função procura Produtos de uma empresa Informada pelo Nome do Produto, retorna Objeto ProdutosVO
	 * @author Marconi César
	 * @name procurarProdutos
	 */
	public function procurarProdutos($codEmpresa, $nome)
	{
		$query = "SELECT * FROM produto WHERE EMPRESA_ID='$codEmpresa' AND NOME LIKE '%$nome%' ORDER by NOME ASC";
		$result = $this->conn->query($query);
		$nRow = $result->num_rows;
		if($nRow ==0){return 'ERRO';}
		
		while ($row = $result->fetch_assoc())
		{
			$produto = new ProdutosVO();
			
			$produto->ID = $row['ID'];
			$produto->EMPRESA_ID = $row['EMPRESA_ID'];
			$produto->FORNECEDOR_ID = $row['FORNECEDOR_ID'];
			$produto->GRUPO_ID = $row['GRUPO_ID'];
			$produto->ID_UNIDADE_PRODUTO = $row['ID_UNIDADE_PRODUTO'];
			$produto->GTIN = $row['GTIN'];
			$produto->CODIGO_INTERNO = $row['CODIGO_INTERNO'];
			$produto->NOME = $row['NOME'];
			$produto->DESCRICAO = $row['DESCRICAO'];
			$produto->DESCRICAO_PDV = $row['DESCRICAO_PDV'];
			$produto->VALOR_CUSTO = $row['VALOR_CUSTO'];
			$produto->VALOR_VENDA = $row['VALOR_VENDA'];
			$produto->QTD_ESTOQUE = $row['QTD_ESTOQUE'];
			$produto->QTD_ESTOQUE_ANTERIOR = $row['QTD_ESTOQUE_ANTERIOR'];
			$produto->ESTOQUE_MIN = $row['ESTOQUE_MIN'];
			$produto->ESTOQUE_MAX = $row['ESTOQUE_MAX'];
			$produto->GARANTIA = $row['GARANTIA'];
			$produto->IAT = $row['IAT'];
			$produto->IPPT = $row['IPPT'];
			$produto->NCM = $row['NCM'];
			$produto->TIPO_ITEM_SPED = $row['TIPO_ITEM_SPED'];
			$produto->DATA_ESTOQUE = $row['DATA_ESTOQUE'];
			$produto->HORA_ESTOQUE = $row['HORA_ESTOQUE'];
			$produto->TAXA_IPI = $row['TAXA_IPI'];
			$produto->TAXA_ISSQN = $row['TAXA_ISSQN'];
			$produto->TAXA_PIS = $row['TAXA_PIS'];
			$produto->TAXA_COFINS = $row['TAXA_COFINS'];
			$produto->TAXA_ICMS = $row['TAXA_ICMS'];
			$produto->CST = $row['CST'];
			$produto->CSOSN = $row['CSOSN'];
			$produto->TOTALIZADOR_PARCIAL = $row['TOTALIZADOR_PARCIAL'];
			$produto->ECF_ICMS_ST = $row['ECF_ICMS_ST'];
			$produto->CODIGO_BALANCA = $row['CODIGO_BALANCA'];
			$produto->PAF_P_ST = $row['PAF_P_ST'];
			$produto->HASH_TRIPA = $row['HASH_TRIPA'];
			$produto->HASH_INCREMENTO  = $row['HASH_INCREMENTO'];
			
			$produtos[] = $produto;
		}
		
		return $produtos;
	}
	
	
	/**
	 * Função Cadastrar Produto, cadastra um Objeto ProdutosVO na tabela produto
	 * @author Marconi César
	 * @name cadastrarProduto
	 */
	public function cadastrarProduto(ProdutosVO $produto)
	{
		$produto->DATA_ESTOQUE = date('Y-m-d');
		$produto->HORA_ESTOQUE = date('h:m:s');
		
		$this->conn->autocommit(false);
		
		$query = "INSERT INTO produto (EMPRESA_ID,FORNECEDOR_ID, GRUPO_ID, ID_UNIDADE_PRODUTO, GTIN, CODIGO_INTERNO, NOME, DESCRICAO, DESCRICAO_PDV, VALOR_VENDA, 
		QTD_ESTOQUE, ESTOQUE_MIN, ESTOQUE_MAX, IAT, IPPT, NCM, TIPO_ITEM_SPED, DATA_ESTOQUE, HORA_ESTOQUE, TAXA_IPI, 
		TAXA_ISSQN, TAXA_PIS, TAXA_COFINS, TAXA_ICMS, CST, CSOSN, TOTALIZADOR_PARCIAL, ECF_ICMS_ST, PAF_P_ST) 
		VALUES ('$produto->EMPRESA_ID','$produto->FORNECEDOR_ID','$produto->GRUPO_ID', '$produto->ID_UNIDADE_PRODUTO', '$produto->GTIN', '$produto->CODIGO_INTERNO', '$produto->NOME', 
		'$produto->DESCRICAO', '$produto->DESCRICAO_PDV', '$produto->VALOR_VENDA', '$produto->QTD_ESTOQUE',  
		'$produto->ESTOQUE_MIN', '$produto->ESTOQUE_MAX', '$produto->IAT', 
		'$produto->IPPT', '$produto->NCM', '$produto->TIPO_ITEM_SPED', '$produto->DATA_ESTOQUE', '$produto->HORA_ESTOQUE', 
		'$produto->TAXA_IPI','$produto->TAXA_ISSQN','$produto->TAXA_PIS','$produto->TAXA_COFINS', '$produto->TAXA_ICMS', 
		'$produto->CST', '$produto->CSOSN', '$produto->TOTALIZADOR_PARCIAL', '$produto->ECF_ICMS_ST', '$produto->PAF_P_ST' 
		)";
		
		$result = $this->conn->query($query);
		
		
		if($this->conn->error)
		{
			$this->conn->rollback();
			$this->conn->autocommit(true);
			
			return $this->conn->error;
		}else{
			$this->conn->commit();
			$this->conn->autocommit(true);
			
			return 'ok';
		}
	}
	
	
	/**
	 * Função Altera Situacao da PreVenda Cabecalho para Cancelado
	 * @author Marconi César
	 * @name cancelarPreVenda
	 */
	public function cancelarPreVenda(EcfVendaCabecalhoVO $ecfVC)
	{
		$query = "UPDATE ecf_pre_venda_cabecalho SET SITUACAO='$ecfVC->SITUACAO' WHERE ID='$ecfVC->ID';";
		if(!$result = $this->conn->query($query))
		{
			return 'ERRO CANCELAR PRE-VENDA: '.$this->conn->error;
		}
		
		return 'ok';
	}
	
	/**
	 * Função Altera Situacao da PreVenda Cabecalho para Fechado
	 * @author Marconi César
	 * @name fecharPreVenda
	 */
	public function fecharPreVenda(EcfVendaCabecalhoVO $ecfVC, ClientesVO $cliente, EndEntregaVO $endEntrega, $qtdParc,
				       $pVenc, $dataEntrega, $horaEntrega)
	{
		
		
		$resultado = true;
		$this->conn->autocommit(false);
		
		//BUSCAR FORMA DE PAGAMENTO INFORMADA
		$query0 = "SELECT * FROM formas_pagamento WHERE ID=$ecfVC->FORMAS_PAGAMENTO_ID";
		$result0 = $this->conn->query($query0);
		
		//CASO NAO ENCONTRE NENHUM PLANO, A CONDICAO FALHARA
		if($result0->num_rows == 0)
		{
			$resultado = false;
		}
		
		//RESULTADO DA BUSCA
		$row0 = $result0->fetch_assoc();
		
		//DEFINE NOME DA FORMA DE PGTO
		$NOME_FORMA_PGTO = $row0['NOME'];
		
		//PROCURAR PREVENDA CABECALHO
		$query1 = "SELECT * FROM ecf_pre_venda_cabecalho WHERE ID=$ecfVC->ID";
		$result1 = $this->conn->query($query1);
			
		if($result1->num_rows == 0)
		{
			$resultado = false;
		}
			
		//RESULTADO DA BUSCA PELA PREVENDA CABECALHO
		$row1 = $result1->fetch_assoc();
		$ecfVC->VALOR = $row1['VALOR'];
		
		if($NOME_FORMA_PGTO == 'BANCO')
		{
						
						
			//DEFINE O VALOR DE CADA BOLETO
			if($qtdParc > 1)
			{
				$VALOR_BOLETO = $ecfVC->VALOR / $qtdParc;
			}else{
				$VALOR_BOLETO = $ecfVC->VALOR;
			}
						
			//DEFINE A QUANTIDADE DE TITULOS
			
				
			
			$quantidade = $qtdParc;
			$_prazo = 0;
			
			if($quantidade != 0)
			{
				//-INICIO-LACO DE CRIACAO DE BOLETOS
				for($i=0;$i < $quantidade;$i++)
				{
			
					$sequencia = $i;
				
					if($i ==0)
					{
						$sequencia++;
					}else if($i == $sequencia)
					{
						$sequencia++;
					}
						
					//DEFINE NUMERO DE SEQUENCIA
					$sequencia2 = str_pad($sequencia, 2, "0", STR_PAD_LEFT);
					$ano = date('y');
	
					
					//PROCURA POR TITULOS JA GERADOS DO CLIENTE
					$queryUltimoBoleto = "SELECT * FROM contas_receber WHERE CLIENTES_ID=$cliente->ID ORDER by ID DESC";
					$resultUltimoBoleto = $this->conn->query($queryUltimoBoleto);					
				
				
					//VERIFICA N_NUMERO E FORMATA 
					if($resultUltimoBoleto->num_rows == 0)
					{
						$ultimoNumero = str_pad("1", 6, "0", STR_PAD_LEFT);
					}else
					{
						$rowUltimoBoleto = $resultUltimoBoleto->fetch_assoc();	
						$ultimoValor = ltrim(substr($rowUltimoBoleto['N_NUMERO'], -6)+1, "0");    		
						$ultimoNumero = str_pad($ultimoValor, 6, "0", STR_PAD_LEFT);    		
					}
				 
					//DEFINE NOSSO NUMERO NO FORMATO    	
					$NumeroNovo = $cliente->ID.$ultimoNumero;
						
						
					//DEFINE DATA DO PRIMEIRO BOLETO
					$_dia = substr($pVenc, 0, 2);
					$_mes = substr($pVenc, 3, 2);
					$_ano  = substr($pVenc, 6, 4);
				
					$_ts = mktime(0,0,0,$_mes+$_prazo,$_dia,$_ano);
					$_data = date('Y-m-d',$_ts);
								
					//DEFINE DATA DE EMISSAO DO(S) TITULOS		
					$emissao = date('Y-m-d');
						
						
					//DEFINE N_DOC		
					$ndocumento = 'PV/'.ltrim($ecfVC->ID, "0");
				
				
					//PROCURA CLIENTE
					$query0 = "SELECT * FROM clientes WHERE ID=$cliente->ID";
					$result0 = $this->conn->query($query0);
				
					if($result0->num_rows == 0)
					{
						$resultado = false;					
					}
					$row0 = $result0->fetch_assoc();
				
					//DEFINE COD DA EMPRESA
					$COD_EMPRESA = $row0['EMPRESA_ID'];
					$codigoCliente = $cliente->ID;
				
					
					//CADASTRA TITULO
					$sql = "INSERT INTO contas_receber (N_DOC,CLIENTES_ID,DATA_VENCIMENTO,VALOR_TITULO, STATUS_2, DATA_EMISSAO, N_NUMERO, 
					CONTROLE, EMPRESA_ID) VALUES
					('$ndocumento', '$codigoCliente', '$_data','$VALOR_BOLETO', 'ABERTO', '$emissao','$NumeroNovo', '$sequencia2', '$COD_EMPRESA')";
					$this->conn->query($sql);
					
					//SOMA 1 MES AO PRAZO ATUAL
					$_prazo += 1;
					
				
				}//-FIM-LACO DE CRIACAO DE BOLETOS
			}
			
		}//GERACAO DE TITULOS
		
		
		//ATUALIZA A FORMA DE PGTO E A SITUACAO PARA FECHADO
		$query = "UPDATE ecf_pre_venda_cabecalho SET SITUACAO='$ecfVC->SITUACAO', FORMAS_PAGAMENTO_ID=$ecfVC->FORMAS_PAGAMENTO_ID WHERE ID='$ecfVC->ID'";
		if(!$result = $this->conn->query($query))
		{
			return 'ERRO FECHAR PRE-VENDA: '.$this->conn->error;
		}
		
		
		
		//-INICIO-ENVIO DE PREVENDA
		//DEFINE A EMPRESA
		$EMPRESA_ID = $row1['EMPRESA_ID'];
		
		//DEFINE O NUMERO DA PREVENDA FORMATADO
		$N_PREVENDA = str_pad( $row1['ID'], 20, '0', STR_PAD_LEFT );
		
		//DEFINE O VALOR TOTAL FORMATADO
		$SUB_TOTAL = number_format($row1['VALOR'], 2, '.', '');
		
		//PROCURA POR ITENS DA PREVENDA
		$query2 = "SELECT pvd.*, p.*, undP.NOME as UNIDADE
			   FROM ecf_pre_venda_detalhe as pvd, produto as p, unidade_produto as undP 
			   WHERE pvd.ID_PRODUTO = p.ID AND p.ID_UNIDADE_PRODUTO = undP.ID AND
			   pvd.ID_ECF_PRE_VENDA_CABECALHO=$ecfVC->ID AND CANCELADO='N'";
		$result2 = $this->conn->query($query2);
		
		//CASO NAO EXISTA ITENS DA PREVENDA A CONDICAO FALHARA
		if($result2->num_rows == 0)
		{
			$resultado = false;
			
		}
		
		//DEFINE A QUANTIDADE DE ITENS FORMATADO	
		$QTD_ITENS = number_format($result2->num_rows, 3, '.', '');
		
		//PROCURA PROX. COD DE ARQUIVO TXT DE PREVENDA GERADO
		$query = "SHOW TABLE STATUS LIKE 'registro_txt_prevenda'";
		$result = $this->conn->query($query);
		
		//RESULTADO DA BUSCA POR PROX CODIGO DE ARQUIVOS TXT DE PREVENDA GERADOS
		$row = $result->fetch_assoc();	
		
		//DEFINE O COD DO ARQUIVO DE PRE-VENDA                			
		$codigo = str_pad( $row["Auto_increment"], 8, '0', STR_PAD_LEFT ); 
		
		//CADASTRA NOVO COD DE ARQUIVO GERADO NA TABELA REGISTRO_TXT_PREVENDA
		$query1 = "INSERT INTO registro_txt_prevenda (EMPRESA_ID) VALUES ($EMPRESA_ID)";
		$result1 = $this->conn->query($query1);
		
		
		//CRIA NOVO ARQUIVO NO DIRETORIO PADRAO DE PREVENDA			
		$fp = fopen("./util/djsystem/prevenda/".$codigo.".djp", "w+");
		
		//DEFINE UMA QUEBRA DE LINHA
		$quebra = chr(13).chr(10);
		
		//DEFINE DATA E HORA DE EMISSAO DA PREVENDA
		$DATA_EMISSAO = date('dmYHms');
		
		//-INICIO---REGISTRO PRE
		$escreve = fwrite($fp, 'PRE|'.$N_PREVENDA.'|'.$DATA_EMISSAO.'|0|'.$cliente->NOME_RAZAO.'|'.$cliente->DOC_CPF_CNPJ.'|1|'.$SUB_TOTAL.'|0.00|0.00|'.$QTD_ITENS.'|||||||||||||0|'.$quebra);	
		//-FIM---REGISTRO PRE
		
		
		//-INICIO---REGISTRO PIT--LOOP
		$SEQUENCIA = 0;
		while($row2 = $result2->fetch_assoc())
		{
			//PARAMETROS PREVENDA DETALHE
			$ORDEM = $row2['ORDEM'];
			$COD_EXTERNO_PRODUTO = $row2['ID_PRODUTO'];
			$QTD =number_format($row2['QUANTIDADE'], 3, '.', '');
			$PRECO_UNITARIO =number_format($row2['VALOR_UNITARIO'], 3, '.', '');
			$VALOR_TOTAL =number_format($row2['VALOR_TOTAL'], 2, '.', '');
		
			//$row2 = $result2->fetch_assoc();
			$COD_BARRAS = $row2['GTIN'];
			$DESCRICAO = $row2['DESCRICAO'];
		
			$UNIDADE = $row2['UNIDADE'];
			$ST = $row2['PAF_P_ST'];
			$ICMS = number_format($row2['TAXA_ICMS'], 2, '.', '');
			$QTD_ESTOQUE = $row2['QTD_ESTOQUE'];
			$IPPT = $row2['IPPT'];
		
			if($IPPT == 'T')
			{
			$PRODUCAO = 'N';
			}else{
			$PRODUCAO = 'S';
			}
			$SEQUENCIA++;
			
			//ESCREVE REGISTRO PIT
			$escreve = fwrite($fp, 'PIT|'.$SEQUENCIA.'|'.$COD_EXTERNO_PRODUTO.'|'.$QTD.'|'.$PRECO_UNITARIO.'|0.00|0.00|'.$VALOR_TOTAL.'|'.$COD_BARRAS.'|'.$DESCRICAO.'|0|'.$UNIDADE.'|'.$ST.'|'.$ICMS.'|N|N|N|'.$PRODUCAO.'|'.$QTD_ESTOQUE.'|'.$quebra);
			
		}//-FIM---REGISTRO PIT--LOOP
		
		
		//-INICIO-REGISTRO PEN	
		if($endEntrega->CLIENTES_ID != '')
		{
			//DEFINE DIA DE ENTREGA
			$diaEntrega = substr(0, 2, $dataEntrega);
			
			//DEFINE MES DE ENTREGA
			$mesEntrega = substr(3, 2, $dataEntrega);
			
			//DEFINE ANO DE ENTREGA
			$anoEntrega = substr(6, 4, $dataEntrega);
			
			//DEFINE HORA DE ENTREGA
			$horaEntrega = str_replace(':','',$horaEntrega);
			
			//DEFINE DATA E HORA COMPLETA DE ENTREGA
			$dataEntrega = $diaEntrega.''.$mesEntrega.''.$anoEntrega.''.$horaEntrega;
			
			//ESCREVE REGISTRO PEN
			$escreve = fwrite($fp, 'PEN|'.$endEntrega->ENDERECO.'|'.$endEntrega->NUMERO.'|'.$endEntrega->COMPLEMENTO.'|'.$endEntrega->BAIRRO.'|'.$endEntrega->CIDADE.'|'.$endEntrega->UF.'|'.$endEntrega->CEP.'|'.$endEntrega->REFERENCIA.'|'.$dataEntrega.'|'.$quebra);
		
			//ATUALIZA ENDERECO DE ENTREGA
			$queryUpEndereco = "UPDATE enderecos_entrega SET REFERENCIA='$endEntrega->REFERENCIA', COMPLEMENTO='$endEntrega->COMPLEMENTO' WHERE CLIENTES_ID='$endEntrega->CLIENTES_ID'";
			$resultUpEndereco = $this->conn->query($queryUpEndereco);
			
			//ABRE CHAMADO DE ENTREGA PARA O CLIENTE
			//-IMPLEMENTAR
		}		
		
		//FECHA ARQUIVO
		fclose($fp);				
		//-FIM-ENVIO DE PREVENDA
		
		
		
		//-INICIO-CRIACAO DE CUPOM DE PV				
		$dataHoje = date('d/m/Y');
		$hora = date('h:m');		
		
		//unlink("./util2/os/OS_PRINT.txt");
		$fp = fopen("./util/cupons/prevenda/PV".$ecfVC->ID.".txt", "w+");
		
		$quebra = chr(13).chr(10);
		
		$linha =  nl2br(str_pad("PRE-VENDA", 48, " ", STR_PAD_BOTH));
		$linha .= $quebra;		
		$linha .= $quebra;
		$linha .=  nl2br(str_pad("d i g i t a l", 48, " ", STR_PAD_BOTH));
		$linha .= $quebra;
		$linha .= $quebra;
		$linha .= nl2br(str_pad("Rua Adjar Maciel, 35 Centro Belo Jardim/PE", 48, " ", STR_PAD_BOTH));
		$linha .= $quebra;
		$linha .= nl2br(str_pad("CEP: 55.150-040 Fone: (81)3726.3125", 48, " ", STR_PAD_BOTH));
		$linha .= $quebra;
		$linha .= nl2br(str_pad("CNPJ: 07.578.965/0001-05 IE: 18.3.050.0329", 48, " ", STR_PAD_BOTH));
		$linha .= $quebra;
		$linha .= nl2br(str_pad("www.digitalonline.com.br", 48, " ", STR_PAD_BOTH));
		$linha .= $quebra;
		$linha .= $quebra;
		$linha .= $quebra;
		$linha .= nl2br(str_pad("------------------------------------------------", 48, " ", STR_PAD_BOTH));
		$linha .= $quebra;
		$linha .= nl2br(str_pad("NAO E DOCUMENTO FISCAL", 48, " ", STR_PAD_BOTH));
		$linha .= $quebra;
		$linha .= nl2br(str_pad("NAO E VÁLIDO COMO RECIBO GARANTIA DE MERCADORIA", 48, " ", STR_PAD_BOTH));
		$linha .= $quebra;
		$linha .= nl2br(str_pad("NAO E VÁLIDO COMO GARANTIA DE MERCADORIA", 48, " ", STR_PAD_BOTH));
		$linha .= $quebra;
		$linha .= nl2br(str_pad("------------------------------------------------", 48, " ", STR_PAD_BOTH));
		$linha .= $quebra;
		$linha .= $quebra;
		$linha .= $quebra;			
		$linha .= nl2br(str_pad("------------------------------------------------", 48, " ", STR_PAD_BOTH));
		$linha .= $quebra;
		$linha .= "DOC N:".$ecfVC->ID;
		$linha .= $quebra;
		$linha .= "EMISSAO: ".$dataHoje." HORA: ".$hora;
		$linha .= $quebra;
		$linha .= nl2br(str_pad("------------------------------------------------", 48, " ", STR_PAD_BOTH));
		$linha .= $quebra;
		$linha .= "CONDICAO PAG:".$NOME_FORMA_PGTO;
		$linha .= $quebra;
		$linha .= nl2br(str_pad("------------------------------------------------", 48, " ", STR_PAD_BOTH));
		$linha .= $quebra;
		$linha .= "VENDEDOR: NAO INFORMADO";
		$linha .= $quebra;
		$linha .= nl2br(str_pad("------------------------------------------------", 48, " ", STR_PAD_BOTH));
		$linha .= $quebra;
		$linha .= "Cliente: ".$cliente->NOME_RAZAO;
		$linha .= $quebra;
		$linha .= "CPF: ".$cliente->DOC_CPF_CNPJ;
		$linha .= $quebra;
		$linha .= "Tel: ".$cliente->TELEFONE1."/".$cliente->TELEFONE2."/".$cliente->CELULAR1."/".$cliente->CELULAR2;
		$linha .= $quebra;
		
		$linha .= "################################################";
		$linha .= $quebra;
		$linha .= "CODIGO        DESCRICAO DO PRODUTO";
		$linha .= $quebra;
		$linha .= "GARANTIA         QUANT.   UN       x      PRECO";
		$linha .= $quebra;
		$linha .= "TOTAL";
		$linha .= $quebra;
		$linha .= "################################################";
		$linha .= $quebra;
		
		
		//PROCURA POR ITENS DA PREVENDA
		$query3 = "SELECT pvd.*, p.*, undP.NOME as UNIDADE
			   FROM ecf_pre_venda_detalhe as pvd, produto as p, unidade_produto as undP 
			   WHERE pvd.ID_PRODUTO = p.ID AND p.ID_UNIDADE_PRODUTO = undP.ID AND
			   pvd.ID_ECF_PRE_VENDA_CABECALHO=$ecfVC->ID AND CANCELADO='N'";
		$result3 = $this->conn->query($query3);
		
		//CASO NAO EXISTA ITENS DA PREVENDA A CONDICAO FALHARA
		if($result3->num_rows == 0)
		{
			$resultado = false;
			
		}
		
		while($row3 = $result3->fetch_assoc())
		{
		
			$ORDEM = $row3['ORDEM'];			
			$QTD =number_format($row3['QUANTIDADE'], 3, '.', '');
			$PRECO_UNITARIO =number_format($row3['VALOR_UNITARIO'], 3, '.', '');
			$VALOR_TOTAL =number_format($row3['VALOR_TOTAL'], 2, '.', '');			
		
			$linha .= $row3['ID_PRODUTO']."          ".$row3['DESCRICAO_PDV'];
			$linha .= $quebra;
			$linha .= "NENHUMA        ".$QTD."     ".$row3['UNIDADE']."    x       ".$PRECO_UNITARIO;
			$linha .= $quebra;
			$linha .= $VALOR_TOTAL;
			$linha .= $quebra;
		
		}
		
		$linha .= "----------";
		$linha .= $quebra;
		$linha .= $ecfVC->VALOR;
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
		$linha .= $quebra;
		$escreve = fwrite($fp, $linha);

		// Fecha o arquivo
		fclose($fp);		
		//-FIM-CRIACAO DE CUPOM PREVENDA
		
				
		
		if($this->conn->error)
		{
			$this->conn->rollback();
			$this->conn->autocommit(true);
			
			$arrR[] = "ERRO";				
			return $arrR;
		}else{
			if($resultado)
			{
				$this->conn->commit();
				$this->conn->autocommit(true);
				
				
				$arrR[] = "/util/djsystem/prevenda/".$codigo.".djp";
				$arrR[] = "/util/cupons/prevenda/PV".$ecfVC->ID.".txt";
				
				//return "/util/djsystem/prevenda/".$codigo.".djp";
				return $arrR;
			}else{
				$this->conn->rollback();
				$this->conn->autocommit(true);
			
				$arrR[] = "ERRO";				
				return $arrR;
			}
			
			
		}
		
	}
	
	/**
	 * Função Altera Cadastro de Produto
	 * @author Marconi César
	 * @name alterarProduto
	 */
	public function alterarProduto(ProdutosVO $produto)
	{
		$query = "UPDATE produto SET FORNECEDOR_ID='$produto->FORNECEDOR_ID',GRUPO_ID='$produto->GRUPO_ID',
		ID_UNIDADE_PRODUTO='$produto->ID_UNIDADE_PRODUTO',GTIN='$produto->GTIN',CODIGO_INTERNO='$produto->CODIGO_INTERNO',NOME='$produto->NOME'
		,DESCRICAO='$produto->DESCRICAO',DESCRICAO_PDV='$produto->DESCRICAO_PDV',VALOR_VENDA='$produto->VALOR_VENDA',QTD_ESTOQUE='$produto->QTD_ESTOQUE'
		,QTD_ESTOQUE_ANTERIOR='$produto->QTD_ESTOQUE_ANTERIOR',ESTOQUE_MIN='$produto->ESTOQUE_MIN',ESTOQUE_MAX='$produto->ESTOQUE_MAX',IAT='$produto->IAT'
		,IPPT='$produto->IPPT',NCM='$produto->NCM',TIPO_ITEM_SPED='$produto->TIPO_ITEM_SPED',DATA_ESTOQUE='$produto->DATA_ESTOQUE',HORA_ESTOQUE='$produto->HORA_ESTOQUE' 
		,TAXA_IPI='$produto->TAXA_IPI',TAXA_ISSQN='$produto->TAXA_ISSQN',TAXA_PIS='$produto->TAXA_PIS',TAXA_COFINS='$produto->TAXA_COFINS'
		,TAXA_ICMS='$produto->TAXA_ICMS',CST='$produto->CST',CSOSN='$produto->CSOSN',TOTALIZADOR_PARCIAL='$produto->TOTALIZADOR_PARCIAL'  
		,ECF_ICMS_ST='$produto->ECF_ICMS_ST',PAF_P_ST='$produto->PAF_P_ST' WHERE ID=$produto->ID";
		
		if(!$result = $this->conn->query($query))
		{
			return 'ERRO ALTERAR CADASTRO DE PRODUTO: '.$this->conn->error;
		}else{
			return 'ok';
		}	
	}
	
	/**
	 * Função Exclui um Cadastro de Produto
	 * @author Marconi César
	 * @name alterarProduto
	 */
	public function excluirProduto($codProduto)
	{
		$query = "DELETE FROM produto WHERE ID='$codProduto'";
		
		if(!$result = $this->conn->query($query))
		{
			return 'ERRO EXCLUIR CADASTRO DE PRODUTO: '.$this->conn->error;
		}else{
			return 'ok';
		}
	}
	
	/**
	 * Função Listar Unidades Produto da Empresa Informada Retorna Objeto UnidadeProdutoVO
	 * @author Marconi César
	 * @name listarUnidadeProduto
	 */
	public function listarUnidadeProduto($codEmpresa)
	{
		$query = "SELECT * FROM unidade_produto WHERE EMPRESA_ID = '$codEmpresa'";
		$result = $this->conn->query($query);
		
		if($result->num_rows == 0){return 'ERRO';}
		
		while ($row = $result->fetch_assoc())
		{
			$unidadeProduto = new UnidadeProdutoVO();
			
			$unidadeProduto->ID = $row['ID'];
			$unidadeProduto->EMPRESA_ID = $row['EMPRESA_ID'];
			$unidadeProduto->NOME = $row['NOME'];
			$unidadeProduto->PODE_FRACIONAR = $row['PODE_FRACIONAR'];
			$unidadeProduto->DESCRICAO = $row['DESCRICAO'];
			
			$unidadeProdutos[] = $unidadeProduto;
		}
		
		return $unidadeProdutos;
	}
	
	/**
	 * Função Lista Grupos de Produtos da Empresa Informada e Retorna Objeto GrupoProdutoVO
	 * @author Marconi César
	 * @name listarGruposProduto
	 */
	public function listarGruposProduto($codEmpresa)
	{
		$query = "SELECT * FROM grupo_produto WHERE EMPRESA_ID=$codEmpresa";
		$result = $this->conn->query($query);
		
		if($result->num_rows == 0){return 'ERRO';}
		
		while ($row = $result->fetch_assoc($result)) {
			$grupoProduto = new GrupoProdutoVO();
			
			$grupoProduto->ID = $row['ID'];
			$grupoProduto->EMPRESA_ID = $row['EMPRESA_ID'];
			$grupoProduto->NOME_GRUPO = $row['NOME_GRUPO'];			
			
			$grupoProdutos[] = $grupoProduto;
		}
		
		return $grupoProdutos;		
	}

	/**
	 * Função Cadastra Grupo de Produto
	 * @author Marconi César
	 * @name cadastrarGrupoProduto
	 */
	public function cadastrarGrupoProduto(GrupoProdutoVO $grupoProduto)
	{
		$query = "INSERT INTO grupo_produto (EMPRESA_ID, NOME_GRUPO) VALUES ($grupoProduto->EMPRESA_ID, '$grupoProduto->NOME_GRUPO');";
		if(!$result = $this->conn->query($query))
		{
			return 'ERRO CADASTRAR GRUPO DE PRODUTO: '.$this->conn->error;
		}else{
			return 'ok';
		}		
	}
	
	
	/**
	 * Função Altera Cadastro de grupo de produto
	 * @author Marconi César
	 * @name editarGrupoProduto
	 */
	public function editarGrupoProduto(GrupoProdutoVO $grupoProduto)
	{
		$query = "UPDATE grupo_produto SET NOME_GRUPO='$grupoProduto->NOME_GRUPO' WHERE ID='$grupoProduto->ID'";
		if(!$result = $this->conn->query($query))
		{
			return 'ERRO ALTERAR GRUPO DE PRODUTO: '.$this->conn->error;
		}else{
			return 'ok';
		}		
	}
	
	/**
	 * Função Exclui Cadastro de grupo de produto
	 * @author Marconi César
	 * @name excluirGrupoProduto
	 */
	public function excluirGrupoProduto($cod)
	{
		$query = "DELETE FROM grupo_produto WHERE ID='$cod'";
		if(!$result = $this->conn->query($query))
		{
			return 'ERRO EXCLUIR GRUPO DE PRODUTO: '.$this->conn->error;
		}else{
			return 'ok';
		}		
	}
	
	/**
	 * Função Lista Fornecedores de Uma Empresa Informada
	 * @author Marconi César
	 * @name listarFornecedores
	 */
	public function listarFornecedores($codEmpresa)
	{
		$query = "SELECT * FROM fornecedores WHERE EMPRESA_ID=$codEmpresa ORDER by RAZAO_SOCIAL";
		$result = $this->conn->query($query);
		
		if($result->num_rows ==0){return 'ERRO';}
		
		while ($row = $result->fetch_assoc()) {
			$fornecedor = new FornecedoresVO();
			
			$fornecedor->ID = $row['ID'];
			$fornecedor->EMPRESA_ID = $row['EMPRESA_ID'];
			$fornecedor->RAZAO_SOCIAL = $row['RAZAO_SOCIAL'];
			$fornecedor->FANTASIA = $row['FANTASIA'];
			$fornecedor->CNPJ = $row['CNPJ'];
			$fornecedor->ENDERECO = $row['ENDERECO'];
			$fornecedor->BAIRRO = $row['BAIRRO'];
			$fornecedor->CIDADE = $row['CIDADE'];
			$fornecedor->UF = $row['UF'];
			$fornecedor->CEP = $row['CEP'];
			$fornecedor->FONE1 = $row['FONE1'];
			$fornecedor->FONE2 = $row['FONE2'];
			$fornecedor->FAX = $row['FAX'];
			$fornecedor->DTPVENDAS = $row['DTPVENDAS'];
			$fornecedor->EMAIL = $row['EMAIL'];
			$fornecedor->HOME_PAGE = $row['HOME_PAGE'];
			$fornecedor->REPRESENTANTE = $row['REPRESENTANTE'];
			$fornecedor->NOME_REPRESENTANTE = $row['NOME_REPRESENTANTE'];
			$fornecedor->CONTATO_REPRESENTANTE = $row['CONTATO_REPRESENTANTE'];
			$fornecedor->CIDADE_REPRESENTATE = $row['CIDADE_REPRESENTATE'];
			$fornecedor->UF_REPRESENTANTE = $row['UF_REPRESENTANTE'];
			$fornecedor->FONE1_REPRESENTANTE = $row['FONE1_REPRESENTANTE'];
			$fornecedor->FONE2_REPRESENTANTE = $row['FONE2_REPRESENTANTE'];
			$fornecedor->FAX_REPRESENTANTE = $row['FAX_REPRESENTANTE'];
			$fornecedor->CEL_REPRESENTANTE = $row['CEL_REPRESENTANTE'];
			$fornecedor->DATA_CADASTRO = $row['DATA_CADASTRO'];
			
			$fornecedors[] = $fornecedor;
		}
		
		return $fornecedors;
	}
	
	/**
	 * Função Cadastra um Novo cadastro de Pré-Vendas da Empresa Informada
	 * @author Marconi César
	 * @name abrirPreVenda
	 */
	public function abrirPreVenda($codEmpresa)
	{
		$dataPV = date('Y-m-d');
		$horaPV = date('h:m:s');
		
		$query = "INSERT INTO ecf_pre_venda_cabecalho (EMPRESA_ID, DATA_PV, HORA_PV, SITUACAO, VALOR) VALUES ('$codEmpresa','$dataPV', '$horaPV', 'A', 0)";
		$result = $this->conn->query($query);
		
		
			
		//PROCURA ULTIMA PR�-VENDA
		$queryUltimaPV = "SELECT * FROM ecf_pre_venda_cabecalho ORDER by ID DESC";
		$resultUltimaPV = $this->conn->query($queryUltimaPV);
		$rowUltimaPV = $resultUltimaPV->fetch_assoc();
		
		return $rowUltimaPV['ID'];	
	}
	
	
	
	public function teste()
	{
		$query = "INSERT INTO bases (ID, EMPRESA_ID, NOME) VALUES (NULL, '2', 'efsdfsd')";
		$result = $this->conn->query($query);
	}
	
	/**
	 * Função Cadastra Pré-Venda Detalhe
	 * @author Marconi César
	 * @name cadastrarEcfVendaDetalhe
	 */
	public function cadastrarEcfVendaDetalhe(EcfVendaDetalheVO $ecfVD)
	{
		$resultado = true;
		$this->conn->autocommit(false);
		
		//CADASTRA PRE-VENDA DETALHE
		$query = "INSERT INTO ecf_pre_venda_detalhe (ID_PRODUTO, ID_ECF_PRE_VENDA_CABECALHO,ORDEM, QUANTIDADE, VALOR_UNITARIO, VALOR_TOTAL, CANCELADO) 
		VALUES ($ecfVD->ID_PRODUTO, $ecfVD->ID_ECF_PRE_VENDA_CABECALHO,$ecfVD->ORDEM, $ecfVD->QUANTIDADE, $ecfVD->VALOR_UNITARIO, $ecfVD->VALOR_TOTAL, '$ecfVD->CANCELADO')";
		$result = $this->conn->query($query);
		
		
		//BUSCA A QTD ATUAL DO PRODUTO
		$query0 = "SELECT * FROM produto WHERE ID=$ecfVD->ID_PRODUTO";
		$result0 = $this->conn->query($query0);	
		$row0 = $result0->fetch_assoc();
		$QTD_ESTOQUE = explode('.',$row0['QTD_ESTOQUE']);		
		$QTD_VENDIDA = $ecfVD->QUANTIDADE;
		$NOVA_QTD_ESTOQUE = $QTD_ESTOQUE[0] - $QTD_VENDIDA;
		
		if($result0->num_rows == 0)
		{
			$resultado = false;				
		}
		
		
		//ATUALIZA QTD DO PRODUTO
		$query1 = "UPDATE produto SET QTD_ESTOQUE=$NOVA_QTD_ESTOQUE WHERE ID='$ecfVD->ID_PRODUTO'";
		$result1 = $this->conn->query($query1);
		
		
		
		if($this->conn->error)
		{
			$this->conn->rollback();
			$this->conn->autocommit(true);
			return 'ERRO AO CADASTRAR PRE-VENDA DETALHE: '.$this->conn->error;
		}else{
			if($resultado)
			{
				$this->conn->commit();
				$this->conn->autocommit(true);
				
				return 'ok';
			}else{
				$this->conn->rollback();
				$this->conn->autocommit(true);
				
				return 'ERRO AO CADASTRAR PRE-VENDA DETALHE: '.$this->conn->error;
			}
		}	
		
	}
	
	
	/**
	 * Função Cancelar Pré-Venda Detalhe
	 * @author Marconi César
	 * @name cancelarEcfVendaDetalhe
	 */
	public function cancelarEcfVendaDetalhe(EcfVendaDetalheVO $ecfVD)
	{
		$resultado = true;
		$this->conn->autocommit(false);
		
		//ALTERA SITUACAO DO CADASTRO PARA CANCELADO=S
		$query = "UPDATE ecf_pre_venda_detalhe SET CANCELADO='S' WHERE  ID_ECF_PRE_VENDA_CABECALHO=$ecfVD->ID_ECF_PRE_VENDA_CABECALHO
			AND ORDEM=$ecfVD->ORDEM";
		$result = $this->conn->query($query);
		
		
		//BUSCA A QTD ATUAL DO PRODUTO
		$query0 = "SELECT * FROM produto WHERE ID=$ecfVD->ID_PRODUTO";
		$result0 = $this->conn->query($query0);
		
		if($result0->num_rows == 0)
		{
			$resultado = false;
		}
		$row0 = $result0->fetch_assoc();
		$QTD_ESTOQUE = explode('.',$row0['QTD_ESTOQUE']); 		
		$QTD_VENDIDA = $ecfVD->QUANTIDADE;
		$NOVA_QTD_ESTOQUE = $QTD_ESTOQUE[0] + $QTD_VENDIDA;
		
		//ATUALIZA QTD DO PRODUTO
		$query1 = "UPDATE produto SET QTD_ESTOQUE=$NOVA_QTD_ESTOQUE WHERE ID='$ecfVD->ID_PRODUTO'";
		$result1 = $this->conn->query($query1);
			
		if($this->conn->error)
		{
			$this->conn->rollback();
			$this->conn->autocommit(true);
			return 'ERRO AO CANCELAR PRE-VENDA DETALHE: '.$this->conn->error;
		}else{
			if($resultado)
			{
				$this->conn->commit();
				$this->conn->autocommit(true);
				
				return 'ok';
			}else{
				$this->conn->rollback();
				$this->conn->autocommit(true);
				
				return 'ERRO AO CANCELAR PRE-VENDA DETALHE: '.$this->conn->error;
			}
		}
		
	}
	
	
	/**
	 * Função Cadastra um Novo Orcamento DAV
	 * @author Marconi César
	 * @name abrirPreVenda
	 */
	public function abrirDav(ClientesVO $cliente)
	{
		$this->conn->autocommit(false);
		
		$data = date('Y-m-d');
		$hora = date('h:m:s');
		
		$query = "INSERT INTO dav_cabecalho (ID, CLIENTES_ID, EMPRESA_ID, NUMERO_DAV, NUMERO_ECF, CCF, COO, NOME_DESTINATARIO,
		CPF_CNPJ_DESTINATARIO, DATA_EMISSAO, HORA_EMISSAO, SITUACAO, TAXA_ACRESCIMO, ACRESCIMO, TAXA_DESCONTO, DESCONTO, SUBTOTAL,
		VALOR, IMPRESSO, HASH_TRIPA, HASH_INCREMENTO)
		VALUES (NULL, '$cliente->ID', '$cliente->EMPRESA_ID', NULL, NULL, NULL, NULL, '$cliente->NOME_RAZAO', '$cliente->DOC_CPF_CNPJ', '$data',
		'$hora', 'A', '0', '0', '0', '0', '0', '0', 'N', NULL, NULL)";
		
		$this->conn->query($query);
		
		//VERIFICA SE EXISTEM ERROS
		if($this->conn->error)
		{
			$this->conn->rollback();
			$this->conn->autocommit(true);
			
			return 'ERRO';
		}else{
			$this->conn->commit();
			$this->conn->autocommit(true);
			
			$query1 = "SELECT * FROM dav_cabecalho ORDER by ID DESC LIMIT 0,1";
			$result1 = $this->conn->query($query1);
			
			if($result1->num_rows == 0)
			{
				return 'ERRO';
			}else{
				$row1 = $result1->fetch_assoc();
				return $row1['ID'];
			}
		}
		
		
	}
	
	public function cadastrarDavDetalhe(DavDetalheVO $davD)
	{
		$this->conn->autocommit(false);
		
		$data = date('Y-m-d');
		
		$query = "INSERT INTO dav_detalhe (ID_DAV_CABECALHO, ID_PRODUTO, NUMERO_DAV,
		DATA_EMISSAO, ITEM, QUANTIDADE, VALOR_UNITARIO, VALOR_TOTAL, CANCELADO, MESCLA_PRODUTO,
		GTIN_PRODUTO, NOME_PRODUTO, UNIDADE_PRODUTO)
		VALUES ( '$davD->ID_DAV_CABECALHO', '$davD->ID_PRODUTO', '$davD->ID_DAV_CABECALHO', '$data',
		'$davD->ITEM', '$davD->QUANTIDADE', '$davD->VALOR_UNITARIO', '$davD->VALOR_TOTAL', 'N', 'N',
		'$davD->GTIN_PRODUTO', '$davD->NOME_PRODUTO', '$davD->UNIDADE_PRODUTO')";
		$this->conn->query($query);
		
		if($this->conn->error)
		{
			$this->conn->rollback();
			$this->conn->autocommit(true);
			
			return 'ERRO: '.$this->conn->error;
		}else{
			$this->conn->commit();
			$this->conn->autocommit(true);
			return 'ok';
		}
	}
	
	
}