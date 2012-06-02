<?php


require_once 'classes/Conexao.php';

require_once 'vo/UsuarioVO.php';
require_once 'vo/ClientesVO.php';
require_once 'vo/EndPrincipaisVO.php';
require_once 'vo/EndEntregaVO.php';
require_once 'vo/EndCobrancaVO.php';
require_once 'vo/DocClienteVO.php';
require_once 'vo/CepsVO.php';
require_once 'vo/TransportadorasVO.php';



class Cadastros extends Conexao
{
	
	/**
	 * Função Busca Cliente Através do CPF/CNPJ ou NOME_RAZAO
	 * @author Marconi César
	 * @name buscarClienteCPF
	 */
	public function buscarClientes($valor, $codEmpresa)
	{
		//RETIRA CARACTERES DE VALOR INFORMADO		
		
		$valor = str_replace('.','',$valor);
		$valor = str_replace(',','',$valor);
		$valor = str_replace('-','',$valor);	
		
		if(is_numeric($valor))
		{
			$tb = 'DOC_CPF_CNPJ';			
		}else{			
			$tb = 'NOME_RAZAO';
		}
		
		//$tb = 'NOME_RAZAO';
		$query0 = "SELECT * FROM  clientes WHERE ".$tb."  LIKE  '%$valor%' AND EMPRESA_ID=$codEmpresa";
							
		//EXECUTA CONSULTA
		$result0 = $this->conn->query($query0);
						
		//VERIFICA SE EXISTE LINHAS
		if($result0->num_rows == 0)
		{
			return 'ERRO'; 
		}
		
		while ($row = $result0->fetch_assoc())
		{
			
			$cliente = new ClientesVO();
			
			$cliente->ID = $row['ID'];
			$cliente->CATEGORIAS_ID = $row['CATEGORIAS_ID'];
			$cliente->EMPRESA_ID = $row['EMPRESA_ID'];
			$cliente->STATUS_2 = $row['STATUS_2'];
			$cliente->TIPO_PESSOA = $row['TIPO_PESSOA'];
			$cliente->DOC_CPF_CNPJ = $row['DOC_CPF_CNPJ'];
			$cliente->DOC_RG_INSC_ESTADUAL = $row['DOC_RG_INSC_ESTADUAL'];
			$cliente->TRATAMENTO = $row['TRATAMENTO'];
			$cliente->NOME_RAZAO = $row['NOME_RAZAO'];
			$cliente->NOME_FANTASIA = $row['NOME_FANTASIA'];
			$cliente->CONTATO = $row['CONTATO'];
			$cliente->SEXO = $row['SEXO'];
			$cliente->DATA_NASCIMENTO = $row['DATA_NASCIMENTO'];
			$cliente->TELEFONE1 = $row['TELEFONE1'];
			$cliente->TELEFONE2 = $row['TELEFONE2'];
			$cliente->CELULAR1 = $row['CELULAR1'];
			$cliente->CELULAR2 = $row['CELULAR2'];
			$cliente->DATA_CADASTRO = $row['DATA_CADASTRO'];
			$cliente->EMAIL = $row['EMAIL'];
			$cliente->MSN = $row['MSN'];
			$cliente->COMO_NOS_CONHECEU = $row['COMO_NOS_CONHECEU'];
			$cliente->OBS = $row['OBS'];
			$cliente->TRANSPORTADORA_PADRAO = $row['TRANSPORTADORA_PADRAO'];
			$cliente->FORMA_PGTO_PADRAO = $row['FORMA_PGTO_PADRAO'];
			$cliente->TABELA_PRECO_PADRAO = $row['TABELA_PRECO_PADRAO'];
			
			$clientes[] = $cliente;
			
		}
		
		return $clientes;
	}
	
	
	/**
	 * Função Busca Cliente Através do CPF, e Retorna um Objeto FiadorVO
	 * @author Marconi César
	 * @name buscarClienteCPF
	 */
	public function buscarClienteCPF($cpf)
	{
		$cpf = str_replace('.', '', $cpf);
		$cpf = str_replace('-', '', $cpf);
		
		$query = "SELECT * FROM clientes WHERE DOC_CPF_CNPJ LIKE '%$cpf%'";
		$result = $this->conn->query($query);
		
		
		if($result->num_rows == 0){return 'ERRO';}
		
		$row = $result->fetch_assoc();
		
		$fiador = new FiadorVO();
		$fiador->ID = $row['ID'];
		$fiador->DOC_CPF = $row['DOC_CPF_CNPJ'];
		$fiador->DOC_RG = $row['DOC_RG_INSC_ESTADUAL'];
		$fiador->TRATAMENTO = $row['TRATAMENTO'];
		$fiador->NOME = $row['NOME_RAZAO'];
			$ano = substr($row['DATA_NASCIMENTO'], 0, 4);
			$mes = substr($row['DATA_NASCIMENTO'], 5, 2);
			$dia = substr($row['DATA_NASCIMENTO'], 8, 2);
		$fiador->DATA_NASCIMENTO = $dia.$mes.$ano;
		$fiador->TELEFONE1 = $row['TELEFONE1'];
		$fiador->TELEFONE2 = $row['TELEFONE2'];
		$fiador->CELULAR1 = $row['CELULAR1'];
		$fiador->CELULAR2 = $row['CELULAR2'];
		$fiador->EMAIL = $row['EMAIL'];
		$fiador->MSN = $row['MSN'];
	
		
		return $fiador;		
	}
	
	/**
	 * Função Lista Clientes de Um Empresa Informada
	 * @author Marconi César
	 * @name listarClientes
	 */
	public function listarClientes($codEmpresa)
	{
				
		$query = "SELECT * FROM clientes WHERE EMPRESA_ID = '$codEmpresa' ORDER by NOME_RAZAO ASC";
		$result = $this->conn->query($query);		
		
		if($result->num_rows == 0)
		{
			return 'ERRO';
		}
		
		while ($row = $result->fetch_assoc() ) {
			
			$cliente = new ClientesVO();
			
			$cliente->ID = $row['ID'];
			$cliente->CATEGORIAS_ID = $row['CATEGORIAS_ID'];
			$cliente->EMPRESA_ID = $row['EMPRESA_ID'];
			$cliente->STATUS_2 = $row['STATUS_2'];
			$cliente->TIPO_PESSOA = $row['TIPO_PESSOA'];
			$cliente->DOC_CPF_CNPJ = $row['DOC_CPF_CNPJ'];
			$cliente->DOC_RG_INSC_ESTADUAL = $row['DOC_RG_INSC_ESTADUAL'];
			$cliente->TRATAMENTO = $row['TRATAMENTO'];
			$cliente->NOME_RAZAO = $row['NOME_RAZAO'];
			$cliente->NOME_FANTASIA = $row['NOME_FANTASIA'];
			$cliente->CONTATO = $row['CONTATO'];
			$cliente->SEXO = $row['SEXO'];
			$cliente->DATA_NASCIMENTO = $row['DATA_NASCIMENTO'];
			$cliente->TELEFONE1 = $row['TELEFONE1'];
			$cliente->TELEFONE2 = $row['TELEFONE2'];
			$cliente->CELULAR1 = $row['CELULAR1'];
			$cliente->CELULAR2 = $row['CELULAR2'];
			$cliente->DATA_CADASTRO = $row['DATA_CADASTRO'];
			$cliente->EMAIL = $row['EMAIL'];
			$cliente->MSN = $row['MSN'];
			$cliente->COMO_NOS_CONHECEU = $row['COMO_NOS_CONHECEU'];
			$cliente->OBS = $row['OBS'];
			$cliente->TRANSPORTADORA_PADRAO = $row['TRANSPORTADORA_PADRAO'];
			$cliente->FORMA_PGTO_PADRAO = $row['FORMA_PGTO_PADRAO'];
			$cliente->TABELA_PRECO_PADRAO = $row['TABELA_PRECO_PADRAO'];
			
			$clientes[] = $cliente;
			
		}
		
		return $clientes;
	}
	
	/**
	 * Função Seleciona o Cliente Informado Pelo COD e Retorna ClientesVO
	 * @author Marconi César
	 * @name buscarClienteCPF
	 */
	public function selecionarCliente($codCliente)
	{
		$query = "SELECT * FROM clientes WHERE ID = '$codCliente'";
		$result = $this->conn->query($query);
				
		if($result->num_rows == 0){return 'ERRO';}
		$row = $result->fetch_assoc();
		
		$cliente = new ClientesVO();
		
		$cliente->ID = $row['ID'];
		$cliente->CATEGORIAS_ID = $row['CATEGORIAS_ID'];
		$cliente->EMPRESA_ID = $row['EMPRESA_ID'];
		$cliente->STATUS_2 = $row['STATUS_2'];
		$cliente->TIPO_PESSOA = $row['TIPO_PESSOA'];
		$cliente->DOC_CPF_CNPJ = $row['DOC_CPF_CNPJ'];
		$cliente->DOC_RG_INSC_ESTADUAL = $row['DOC_RG_INSC_ESTADUAL'];
		$cliente->TRATAMENTO = $row['TRATAMENTO'];
		$cliente->NOME_RAZAO = $row['NOME_RAZAO'];
		$cliente->NOME_FANTASIA = $row['NOME_FANTASIA'];
		$cliente->CONTATO = $row['CONTATO'];
		$cliente->SEXO = $row['SEXO'];
			$ano = substr($row['DATA_NASCIMENTO'], 0, 4);
			$mes = substr($row['DATA_NASCIMENTO'], 5, 2);
			$dia = substr($row['DATA_NASCIMENTO'], 8, 2);
		$cliente->DATA_NASCIMENTO = $dia.$mes.$ano;
		$cliente->TELEFONE1 = $row['TELEFONE1'];
		$cliente->TELEFONE2 = $row['TELEFONE2'];
		$cliente->CELULAR1 = $row['CELULAR1'];
		$cliente->CELULAR2 = $row['CELULAR2'];
		$cliente->DATA_CADASTRO = $row['DATA_CADASTRO'];
		$cliente->EMAIL = $row['EMAIL'];
		$cliente->MSN = $row['MSN'];
		$cliente->COMO_NOS_CONHECEU = $row['COMO_NOS_CONHECEU'];
			$cliente->OBS = $row['OBS'];
			$cliente->TRANSPORTADORA_PADRAO = $row['TRANSPORTADORA_PADRAO'];
			$cliente->FORMA_PGTO_PADRAO = $row['FORMA_PGTO_PADRAO'];
			$cliente->TABELA_PRECO_PADRAO = $row['TABELA_PRECO_PADRAO'];
		
		return $cliente;
		
		
	}
	
	/**
	 * Função Seleciona A Categoria Informada Pelo COD e Retorna o Nome da Categoria
	 * @author Marconi César
	 * @name selecionarCategoria
	 */
	public function selecionarCategoria($codCategoria)
	{
		$query = "SELECT * FROM categorias WHERE ID = '$codCategoria'";
		$result = $this->conn->query($query);		
		if($result->num_rows ==0){return 'ERRO';}
		$row = $result->fetch_assoc();
		
		return $row['NOME'];	
	}
	
	/**
	 * Função Seleciona Endereco Principal do Cliente e Retorna Objeto EndPrincipaisVO
	 * @author Marconi César
	 * @name selecionarEnderecoPrincipalCliente
	 */
	public function selecionarEnderecoPrincipalCliente($codCliente)
	{
		$query = "SELECT * FROM enderecos_principais WHERE CLIENTES_ID = '$codCliente'";
		$result = $this->conn->query($query);
		
		
		if($result->num_rows == 0){return 'ERRO';}
			
			$row = $result->fetch_assoc();
		
			$endPrincipal = new EndPrincipaisVO();
			
			$endPrincipal->ID = $row['ID'];
			$endPrincipal->FIADORES_ID = $row['FIADORES_ID'];
			$endPrincipal->CLIENTES_ID = $row['CLIENTES_ID'];
			$endPrincipal->CEP = $row['CEP'];
			$endPrincipal->ENDERECO = $row['ENDERECO'];
			$endPrincipal->NUMERO = $row['NUMERO'];
			$endPrincipal->COMPLEMENTO = $row['COMPLEMENTO'];
			$endPrincipal->BAIRRO = $row['BAIRRO'];
			$endPrincipal->CIDADE = $row['CIDADE'];
			$endPrincipal->UF = $row['UF'];
			$endPrincipal->PAIS = $row['PAIS'];
			$endPrincipal->REFERENCIA = $row['REFERENCIA'];
		
		
		return $endPrincipal;
	}
	
	/**
	 * Função Seleciona Endereco de Cobranca do Cliente e Retorna Objeto EndCobrancaVO
	 * @author Marconi César
	 * @name selecionarEndCobCliente
	 */
	public function selecionarEndCobCliente($codCliente)
	{
		$query = "SELECT * FROM enderecos_cobranca WHERE CLIENTES_ID = '$codCliente'";
		$result = $this->conn->query($query);
				
		if($result->num_rows == 0){return 'ERRO';}
			
			$row = $result->fetch_assoc();
		
			$endCobranca = new EndCobrancaVO();
			
			$endCobranca->ID = $row['ID'];
			$endCobranca->CLIENTES_ID = $row['CLIENTES_ID'];
			$endCobranca->CEP = $row['CEP'];
			$endCobranca->ENDERECO = $row['ENDERECO'];
			$endCobranca->NUMERO = $row['NUMERO'];
			$endCobranca->COMPLEMENTO = $row['COMPLEMENTO'];
			$endCobranca->BAIRRO = $row['BAIRRO'];
			$endCobranca->CIDADE = $row['CIDADE'];
			$endCobranca->UF = $row['UF'];
			$endCobranca->PAIS = $row['PAIS'];
			$endCobranca->REFERENCIA = $row['REFERENCIA'];
			$endCobranca->ENDERECO_PRINCIPAL = $row['ENDERECO_PRINCIPAL'];		
		
		return $endCobranca;
	}
	
	
	/**
	 * Função Seleciona Endereco de Entrega do Cliente e Retorna Objeto EndEntregaVO
	 * @author Marconi César
	 * @name selecionarEndEntrCliente
	 */
	public function selecionarEndEntrCliente($codCliente)
	{
		$query = "SELECT * FROM enderecos_entrega WHERE CLIENTES_ID = '$codCliente'";
		$result = $this->conn->query($query);
		
		
		if($result->num_rows == 0){return 'ERRO';}
			
			$row = $result->fetch_assoc();		
			$endEntrega = new EndEntregaVO();
			
			$endEntrega->ID = $row['ID'];
			$endEntrega->CLIENTES_ID = $row['CLIENTES_ID'];
			$endEntrega->CEP = $row['CEP'];
			$endEntrega->ENDERECO = $row['ENDERECO'];
			$endEntrega->NUMERO = $row['NUMERO'];
			$endEntrega->COMPLEMENTO = $row['COMPLEMENTO'];
			$endEntrega->BAIRRO = $row['BAIRRO'];
			$endEntrega->CIDADE = $row['CIDADE'];
			$endEntrega->UF = $row['UF'];
			$endEntrega->PAIS = $row['PAIS'];
			$endEntrega->REFERENCIA = $row['REFERENCIA'];
			$endEntrega->ENDERECO_PRINCIPAL = $row['ENDERECO_PRINCIPAL'];	
		
		return $endEntrega;
	}
	
	/**
	 * Função Seleciona Fiador do Cliente e Retorna Objeto FiadorVO
	 * @author Marconi César
	 * @name selecionarFiadorCliente
	 */
	public function selecionarFiadorCliente($codCliente)
	{
		
		$query = "SELECT * FROM fiadores WHERE CLIENTES_ID = '$codCliente'";
		$result = $this->conn->query($query);
				
		if($result->num_rows == 0)
		{
		   return 'CLIENTE SEM FIADOR';
		}
		
		   $row = $result->fetch_assoc();			
		   $fiador = new FiadorVO();
			
		   $fiador->ID = $row['ID'];
		   $fiador->CLIENTES_ID = $row['CLIENTES_ID'];
		   $fiador->DOC_CPF = $row['DOC_CPF'];
		   $fiador->DOC_RG = $row['DOC_RG'];
		   $fiador->TRATAMENTO = $row['TRATAMENTO'];
		   $fiador->NOME = $row['NOME'];
			$ano = substr($row['DATA_NASCIMENTO'], 0, 4);
			$mes = substr($row['DATA_NASCIMENTO'], 5, 2);
			$dia = substr($row['DATA_NASCIMENTO'], 8, 2);
		   $fiador->DATA_NASCIMENTO = $dia.$mes.$ano;
		   $fiador->TELEFONE1 = $row['TELEFONE1'];
		   $fiador->TELEFONE2 = $row['TELEFONE2'];
		   $fiador->CELULAR1 = $row['CELULAR1'];
		   $fiador->CELULAR2 = $row['CELULAR2'];
		   $fiador->EMAIL = $row['EMAIL'];
		   $fiador->MSN = $row['MSN'];		
		
		return $fiador;
	}
	
	/**
	 * Função Seleciona Endereco do Fiador do Cliente e Retorna Objeto EndPrincipaisVO
	 * @author Marconi César
	 * @name selecionarFiadorCliente
	 */
	public function enderecoFiadorCliente($codCliente)
	{
		
		$queryFiador = "SELECT * FROM fiadores WHERE CLIENTES_ID = '$codCliente'";
		$resultFiador = $this->conn->query($queryFiador);
				
		if($resultFiador->num_rows == 0)
		{
		    return 'NENHUM FIADOR ENCONTRADO';
		}
		$rowFiador = $resultFiador->fetch_assoc();
		$codFiador = $rowFiador['ID'];
		
		$queryEndFiador = "SELECT * FROM enderecos_principais WHERE FIADORES_ID = '$codFiador'";
		$resultEndFiador = $this->conn->query($queryEndFiador);
		
		
		if($resultEndFiador->num_rows == 0)
		{
			return 'ENDERECO DO FIADOR NAUM FOI ENCONTRADO';
		}
			
			$rowEndFiador = $resultEndFiador->fetch_assoc();			
			$endPrincipal = new EndPrincipaisVO();
			
			$endPrincipal->ID = $rowEndFiador['ID'];
			$endPrincipal->FIADORES_ID = $rowEndFiador['FIADORES_ID'];
			$endPrincipal->CLIENTES_ID = $rowEndFiador['CLIENTES_ID'];
			$endPrincipal->CEP = $rowEndFiador['CEP'];
			$endPrincipal->ENDERECO = $rowEndFiador['ENDERECO'];
			$endPrincipal->NUMERO = $rowEndFiador['NUMERO'];
			$endPrincipal->COMPLEMENTO = $rowEndFiador['COMPLEMENTO'];
			$endPrincipal->BAIRRO = $rowEndFiador['BAIRRO'];
			$endPrincipal->CIDADE = $rowEndFiador['CIDADE'];
			$endPrincipal->UF = $rowEndFiador['UF'];
			$endPrincipal->PAIS = $rowEndFiador['PAIS'];
			$endPrincipal->REFERENCIA = $rowEndFiador['REFERENCIA'];
				
		
		return $endPrincipal;		
	}
	
	/**
	 * Função Seleciona Doc do Cliente e Retorna Objeto DocClienteVO
	 * @author Marconi César
	 * @name selecionarDocCliente
	 */
	public function selecionarDocCliente($codCliente)
	{
		$query = "SELECT * FROM doc_clientes WHERE CLIENTES_ID = '$codCliente'";
		$result = $this->conn->query($query);
		
		
		if($result->num_rows == 0){return 'ERRO';}
			$row = $result->fetch_assoc();
		
			$docCliente = new DocClienteVO();
			
			$docCliente->ID = $row['ID'];
			$docCliente->CLIENTES_ID = $row['CLIENTES_ID'];
			$docCliente->COMPOVANTE_ENDERECO = $row['COMPOVANTE_ENDERECO'];
			$docCliente->RG_CONTRATO_SOCIAL = $row['RG_CONTRATO_SOCIAL'];
			$docCliente->CPF_CNPJ = $row['CPF_CNPJ'];
			$docCliente->CONTRATO_ASSINADO = $row['CONTRATO_ASSINADO'];
			
			return $docCliente;		
	}
	
	
	public function procurarClientesImportacao($busca, $filtro, $nEmpresa, $codUsuario)
	{
		if($filtro == null)
		{
			$campo = 'NOME_RAZAO';
		}
		if($filtro == 'NOME')
		{
			$campo = 'NOME_RAZAO';
		}
		if($filtro == 'CPF')
		{
			$campo = 'DOC_CPF_CNPJ';
		}
		
		$busca = strtoupper($busca);
		
		if($campo == 'DOC_CPF_CNPJ')
		{
			$busca = str_replace('.', '', $busca);
			$busca = str_replace('-', '', $busca);
			$busca = str_replace('/', '', $busca);
		}
		
	
			//LISTA TODS OS CLIENTES QUE NAUM S�O DA EMPRESA ATUAL
			$query = "SELECT clientes.*,  empresa.NOME_FANTASIA as NOME_EMPRESA from clientes
						Left Join empresa on clientes.EMPRESA_ID = empresa.ID
						WHERE ".$campo." LIKE '%$busca%' AND EMPRESA_ID != '$nEmpresa'";
					
			$result = $this->conn->query($query);
			$nRow = $result->num_rows;
			//CASO N�O TENHA RETORNA ERRO
			if($nRow == 0){return 'ERRO';}
		
		while ($row = $result->fetch_assoc()) {
			
			$codEmpresa = $row['EMPRESA_ID'];
			
			$queryEmpresa = "SELECT *  FROM  empresas_usuario WHERE  USUARIO_ID ='$codUsuario' AND  EMPRESA_ID ='$codEmpresa'";
			$resultEmpresa = $this->conn->query($queryEmpresa);
			$nRowEmpresa = $resultEmpresa->num_rows;
			
			$cliente = new ClientesVO();
			
			$cliente->ID = $row['ID'];
			$cliente->CATEGORIAS_ID = $row['CATEGORIAS_ID'];
			$cliente->EMPRESA_ID = $row['EMPRESA_ID'];
			$cliente->NOME_EMPRESA = $row['NOME_EMPRESA'];
			$cliente->STATUS_2 = $row['STATUS_2'];
			$cliente->TIPO_PESSOA = $row['TIPO_PESSOA'];
			$cliente->DOC_CPF_CNPJ = $row['DOC_CPF_CNPJ'];
			$cliente->DOC_RG_INSC_ESTADUAL = $row['DOC_RG_INSC_ESTADUAL'];
			$cliente->TRATAMENTO = $row['TRATAMENTO'];
			$cliente->NOME_RAZAO = $row['NOME_RAZAO'];
			$cliente->CONTATO = $row['CONTATO'];
			$cliente->SEXO = $row['SEXO'];
			$cliente->DATA_NASCIMENTO = $row['DATA_NASCIMENTO'];
			$cliente->TELEFONE1 = $row['TELEFONE1'];
			$cliente->TELEFONE2 = $row['TELEFONE2'];
			$cliente->CELULAR1 = $row['CELULAR1'];
			$cliente->CELULAR2 = $row['CELULAR2'];
			$cliente->DATA_CADASTRO = $row['DATA_CADASTRO'];
			$cliente->EMAIL = $row['EMAIL'];
			$cliente->MSN = $row['MSN'];
			$cliente->COMO_NOS_CONHECEU = $row['COMO_NOS_CONHECEU'];
			$cliente->OBS = $row['OBS'];
			$cliente->TRANSPORTADORA_PADRAO = $row['TRANSPORTADORA_PADRAO'];
			$cliente->FORMA_PGTO_PADRAO = $row['FORMA_PGTO_PADRAO'];
			$cliente->TABELA_PRECO_PADRAO = $row['TABELA_PRECO_PADRAO'];
			
			
			//if($nRowEmpresa > 0)
			//{
				$clientes[] = $cliente;
			//}
			
		}
		
			return $clientes;
		
	}
	public function procurarClientes($busca, $filtro, $nEmpresa, $codUsuario)
	{
		if($filtro == null)
		{
			$campo = 'NOME_RAZAO';
		}
		if($filtro == 'NOME')
		{
			$campo = 'NOME_RAZAO';
		}
		if($filtro == 'CPF')
		{
			$campo = 'DOC_CPF_CNPJ';
		}
		
		$busca = strtoupper($busca);
		
		if($campo == 'DOC_CPF_CNPJ')
		{
			$busca = str_replace('.', '', $busca);
			$busca = str_replace('-', '', $busca);
			$busca = str_replace('/', '', $busca);
		}
		
		if(strlen($nEmpresa) > 0)
		{
			$query = "SELECT * FROM clientes WHERE ".$campo." LIKE '%$busca%' AND EMPRESA_ID = '$nEmpresa' ";
		}else 
		{
			$query = "SELECT * FROM clientes WHERE ".$campo." LIKE '%$busca%' ";
		}
		$result = $this->conn->query($query);
		$nRow = $result->num_rows;
		
		if($nRow == 0){return 'ERRO';}
		
		while ($row = $result->fetch_assoc()) {
			
			$queryEmpresa = "SELECT *  FROM  empresas_usuario WHERE  USUARIO_ID ='$codUsuario' AND  EMPRESA_ID ='$nEmpresa'";
			$resultEmpresa = $this->conn->query($queryEmpresa);
			$nRowEmpresa = $resultEmpresa->num_rows;
			
			$cliente = new ClientesVO();
			
			$cliente->ID = $row['ID'];
			$cliente->CATEGORIAS_ID = $row['CATEGORIAS_ID'];
			$cliente->EMPRESA_ID = $row['EMPRESA_ID'];
			$cliente->STATUS_2 = $row['STATUS_2'];
			$cliente->TIPO_PESSOA = $row['TIPO_PESSOA'];
			$cliente->DOC_CPF_CNPJ = $row['DOC_CPF_CNPJ'];
			$cliente->DOC_RG_INSC_ESTADUAL = $row['DOC_RG_INSC_ESTADUAL'];
			$cliente->TRATAMENTO = $row['TRATAMENTO'];
			$cliente->NOME_RAZAO = $row['NOME_RAZAO'];
			$cliente->CONTATO = $row['CONTATO'];
			$cliente->SEXO = $row['SEXO'];
			$cliente->DATA_NASCIMENTO = $row['DATA_NASCIMENTO'];
			$cliente->TELEFONE1 = $row['TELEFONE1'];
			$cliente->TELEFONE2 = $row['TELEFONE2'];
			$cliente->CELULAR1 = $row['CELULAR1'];
			$cliente->CELULAR2 = $row['CELULAR2'];
			$cliente->DATA_CADASTRO = $row['DATA_CADASTRO'];
			$cliente->EMAIL = $row['EMAIL'];
			$cliente->MSN = $row['MSN'];
			$cliente->COMO_NOS_CONHECEU = $row['COMO_NOS_CONHECEU'];
			$cliente->OBS = $row['OBS'];
			$cliente->TRANSPORTADORA_PADRAO = $row['TRANSPORTADORA_PADRAO'];
			$cliente->FORMA_PGTO_PADRAO = $row['FORMA_PGTO_PADRAO'];
			$cliente->TABELA_PRECO_PADRAO = $row['TABELA_PRECO_PADRAO'];
			
			if($nRowEmpresa > 0)
			{
			$clientes[] = $cliente;
			}
			
		}
		
			return $clientes;
		
	}	
	public function pegarCodCliente()
	{
		$query = "SHOW TABLE STATUS LIKE 'clientes'";
				$result = $this->conn->query($query);
				$row = $result->fetch_assoc();	
		
				if($row["Auto_increment"] == null)
				{
					$row["Auto_increment"] = 1;
				}
				
				return $codigo = $row["Auto_increment"];
	}
	public function cadastrarCliente(ClientesVO $cliente, EndPrincipaisVO $endPrincipal, EndEntregaVO $endEntrega, EndCobrancaVO $endCobranca,
										FiadorVO $fiador, EndPrincipaisVO $endFiador, DocClienteVO $docCliente)
	{
		
		$cliente->DOC_CPF_CNPJ = str_replace('.', '', $cliente->DOC_CPF_CNPJ);
		$cliente->DOC_CPF_CNPJ = str_replace('-', '', $cliente->DOC_CPF_CNPJ);
		$cliente->DOC_CPF_CNPJ = str_replace('/', '', $cliente->DOC_CPF_CNPJ);
		
			//VERIFICAR DE CPF J� EXISTE
			$queryCPF = "SELECT * FROM clientes WHERE DOC_CPF_CNPJ LIKE '$cliente->DOC_CPF_CNPJ' AND EMPRESA_ID LIKE '$cliente->EMPRESA_ID'";
			$resultCPF = $this->conn->query($queryCPF);
			$nRowCPF = $resultCPF->num_rows;
			if($nRowCPF > 0){return 'CPF EXISTE';}
		
		
		$dia = substr($cliente->DATA_NASCIMENTO, 0, 2);
		$mes = substr($cliente->DATA_NASCIMENTO, 2, 2);
		$ano = substr($cliente->DATA_NASCIMENTO, 4, 4);
		$dataNascimento = $ano.'-'.$mes.'-'.$dia;	
		
		$dataCadastro = date('Y-m-d h:s:i');
		
		
		
		//CADASTRA CLIENTE
		$query = "INSERT INTO clientes (
			CATEGORIAS_ID, EMPRESA_ID ,STATUS_2 ,TIPO_PESSOA ,DOC_CPF_CNPJ ,DOC_RG_INSC_ESTADUAL ,TRATAMENTO ,NOME_RAZAO ,NOME_FANTASIA, CONTATO ,SEXO ,DATA_NASCIMENTO ,
			TELEFONE1 ,TELEFONE2 ,CELULAR1 ,CELULAR2 ,DATA_CADASTRO ,EMAIL ,MSN,COMO_NOS_CONHECEU, OBS, TRANSPORTADORA_PADRAO, FORMA_PGTO_PADRAO, TABELA_PRECO_PADRAO)VALUES (
			'$cliente->CATEGORIAS_ID', '$cliente->EMPRESA_ID', '$cliente->STATUS_2', '$cliente->TIPO_PESSOA', '$cliente->DOC_CPF_CNPJ', '$cliente->DOC_RG_INSC_ESTADUAL', 
			'$cliente->TRATAMENTO', '$cliente->NOME_RAZAO', '$cliente->NOME_FANTASIA','$cliente->CONTATO', '$cliente->SEXO', '$dataNascimento', '$cliente->TELEFONE1', 
			'$cliente->TELEFONE2', '$cliente->CELULAR1', '$cliente->CELULAR2', '$dataCadastro', '$cliente->EMAIL', '$cliente->MSN', '$cliente->COMO_NOS_CONHECEU','$cliente->OBS'
			, '$cliente->TRANSPORTADORA_PADRAO' , '$cliente->FORMA_PGTO_PADRAO', '$cliente->TABELA_PRECO_PADRAO')";
		
		
		
		if(!$result = $this->conn->query($query))
		{
			return 'ERRO CADASTRO CLIENTE: '.$this->conn->error;
		}
		
		
		
		//CADASTRA ENDERECO PRINCIPAL DO CLIENTE
		$endPrincipal->FIADORES_ID = '0';
		$queryEndPrincipal = "INSERT INTO enderecos_principais (FIADORES_ID, CLIENTES_ID, CEP, ENDERECO, NUMERO, COMPLEMENTO, BAIRRO, 
		CIDADE, UF, PAIS, REFERENCIA) VALUES ('$endPrincipal->FIADORES_ID', '$cliente->ID', '$endPrincipal->CEP',  '$endPrincipal->ENDERECO', '$endPrincipal->NUMERO', 
		'$endPrincipal->COMPLEMENTO', '$endPrincipal->BAIRRO', '$endPrincipal->CIDADE', '$endPrincipal->UF', '$endPrincipal->PAIS', 
		'$endPrincipal->REFERENCIA')";
		
		
		if(!$resultEndPrincipal = $this->conn->query($queryEndPrincipal))
		{
			return 'ERRO CADASTRO PRINCIPAL DE ENDERECO DO CLIENTE: '.$this->conn->error;
		}
		
		
		//VERIFICA SE O CEP EXISTE NA BASE LOCAL
		$queryCepLocalVerifica = "SELECT * FROM cep WHERE CEP LIKE '%$endPrincipal->CEP%'";
		$resultCepLocalVerifica = $this->conn->query($queryCepLocalVerifica);
		$nRowCepLocalVerifica = $resultCepLocalVerifica->num_rows;
		
	
		
		if($nRowCepLocalVerifica == 0)
		{
			$queryInsertCepLocal = "INSERT INTO cep (ID ,CEP ,ENDERECO ,BAIRRO ,CIDADE ,UF ,PAIS)
				VALUES (NULL , '$endPrincipal->CEP', '$endPrincipal->ENDERECO', '$endPrincipal->BAIRRO', 
				'$endPrincipal->CIDADE', '$endPrincipal->UF', '$endPrincipal->PAIS')";
			
			
			if(!$resultInsertCepLocal = $this->conn->query($queryInsertCepLocal))
			{
				return 'ERRO CADASTRO CEP: '.$this->conn->error;
			}
		}
		
		
		
		//-----------CADASTRA ENDERECO DE ENTREGA DO CLIENTE
			if($endEntrega->CEP == null)
			{
		$queryEndEntrega = "INSERT INTO enderecos_entrega (CLIENTES_ID ,CEP ,ENDERECO ,NUMERO ,COMPLEMENTO ,BAIRRO ,
				CIDADE ,UF ,PAIS ,REFERENCIA ,ENDERECO_PRINCIPAL)VALUES (
				'$cliente->ID', '$endPrincipal->CEP', '$endPrincipal->ENDERECO', '$endPrincipal->NUMERO', '$endPrincipal->COMPLEMENTO', 
				'$endPrincipal->BAIRRO', '$endPrincipal->CIDADE', '$endPrincipal->UF', '$endPrincipal->PAIS', '$endPrincipal->REFERENCIA', 
				'1')";	
			}else
			{	
		$queryEndEntrega = "INSERT INTO enderecos_entrega (CLIENTES_ID ,CEP ,ENDERECO ,NUMERO ,COMPLEMENTO ,BAIRRO ,
				CIDADE ,UF ,PAIS ,REFERENCIA ,ENDERECO_PRINCIPAL)VALUES (
				'$cliente->ID', '$endEntrega->CEP', '$endEntrega->ENDERECO', '$endEntrega->NUMERO', '$endEntrega->COMPLEMENTO', 
				'$endEntrega->BAIRRO', '$endEntrega->CIDADE', '$endEntrega->UF', '$endEntrega->PAIS', '$endEntrega->REFERENCIA', 
				'$endEntrega->ENDERECO_PRINCIPAL')";
		
			//VERIFICA SE O CEP EXISTE NA BASE LOCAL
			$queryCepLocalVerifica = "SELECT * FROM cep WHERE CEP LIKE '%$endEntrega->CEP%'";
			$resultCepLocalVerifica = $this->conn->query($queryCepLocalVerifica);
			$nRowCepLocalVerifica = $resultCepLocalVerifica->num_rows;
			
			if($nRowCepLocalVerifica == 0)
			{
				$queryInsertCepLocal = "INSERT INTO cep (ID ,CEP ,ENDERECO ,BAIRRO ,CIDADE ,UF ,PAIS)
					VALUES (NULL , '$endEntrega->CEP', '$endEntrega->ENDERECO', '$endEntrega->BAIRRO', 
					'$endEntrega->CIDADE', '$endEntrega->UF', '$endEntrega->PAIS')";
				
				
				if(!$resultInsertCepLocal = $this->conn->query($queryInsertCepLocal))
				{
					return 'ERRO CADASTRO CEP ENDERECO ENTREGA: '.$this->conn->error;
				}
			}
			
			}
		
				if(!$resultEndEntrega = $this->conn->query($queryEndEntrega))
				{
					return 'ERRO CADASTRO ENDERECO DE ENTREGA: '.$this->conn->error;
				}
		//-----------CADASTRA ENDERECO DE ENTREGA DO CLIENTE
		
		
		//-----------CADASTRA ENDERECO DE COBRANCA DO CLIENTE
			if($endCobranca->CEP == null)
			{
		$queryEndCobranca = "INSERT INTO enderecos_cobranca (CLIENTES_ID, CEP, ENDERECO, NUMERO, COMPLEMENTO, BAIRRO, CIDADE, UF, PAIS, REFERENCIA, 
			ENDERECO_PRINCIPAL) VALUES ('$cliente->ID', '$endPrincipal->CEP', '$endPrincipal->ENDERECO', '$endPrincipal->NUMERO', '$endPrincipal->COMPLEMENTO', 
			'$endPrincipal->BAIRRO','$endPrincipal->CIDADE', '$endPrincipal->UF', '$endPrincipal->PAIS', '$endPrincipal->REFERENCIA', 
			'1')";
			}else
			{
		$queryEndCobranca = "INSERT INTO enderecos_cobranca (CLIENTES_ID, CEP, ENDERECO, NUMERO, COMPLEMENTO, BAIRRO, CIDADE, UF, PAIS, REFERENCIA, 
			ENDERECO_PRINCIPAL) VALUES ('$cliente->ID', '$endCobranca->CEP', '$endCobranca->ENDERECO', '$endCobranca->NUMERO', '$endCobranca->COMPLEMENTO', 
			'$endCobranca->BAIRRO','$endCobranca->CIDADE', '$endCobranca->UF', '$endCobranca->PAIS', '$endCobranca->REFERENCIA', 
			'$endCobranca->ENDERECO_PRINCIPAL')";
		
		
			//VERIFICA SE O CEP EXISTE NA BASE LOCAL
			$queryCepLocalVerifica = "SELECT * FROM cep WHERE CEP LIKE '%$endCobranca->CEP%'";
			$resultCepLocalVerifica = $this->conn->query($queryCepLocalVerifica);
			$nRowCepLocalVerifica = $resultCepLocalVerifica->num_rows;
			
			if($nRowCepLocalVerifica == 0)
			{
				$queryInsertCepLocal = "INSERT INTO cep (ID ,CEP ,ENDERECO ,BAIRRO ,CIDADE ,UF ,PAIS)
					VALUES (NULL , '$endCobranca->CEP', '$endCobranca->ENDERECO', '$endCobranca->BAIRRO', 
					'$endCobranca->CIDADE', '$endCobranca->UF', '$endCobranca->PAIS')";
				
				
				if(!$resultInsertCepLocal = $this->conn->query($queryInsertCepLocal))
				{
					return 'ERRO CADASTRO CEP ENDERECO DE COBRANCA: '.$this->conn->error;
				}
				
			}
		
			}
		
				if(!$resultEndCobranca = $this->conn->query($queryEndCobranca))
				{
					return 'ERRO CADASTRO ENDERECO DE COBRANCA: '.$this->conn->error;
				}
		//-----------CADASTRA ENDERECO DE COBRANCA DO CLIENTE
		
		
		//-----------CADASTRA FIADOR, CASO HOUVER
		if($fiador->DOC_CPF != $cliente->DOC_CPF_CNPJ)
		{
			if($fiador->NOME != null)
			{
				
		$diaF = substr($fiador->DATA_NASCIMENTO, 0, 2);
		$mesF = substr($fiador->DATA_NASCIMENTO, 2, 2);
		$anoF = substr($fiador->DATA_NASCIMENTO, 4, 4);
		$dataNascimentoFiador = $anoF.'-'.$mesF.'-'.$diaF;
		
		//PEGA PROX COD FIADOR
		$queryCodFiador = "SHOW TABLE STATUS LIKE 'fiadores'";
		$resultCodFiador = $this->conn->query($queryCodFiador);
		$rowCodFiador = $resultCodFiador->fetch_assoc();	
		$codigoFiador = $rowCodFiador["Auto_increment"];
		
				
				
		$queryFiador = "INSERT INTO fiadores (CLIENTES_ID, DOC_CPF, DOC_RG, TRATAMENTO, NOME, DATA_NASCIMENTO, TELEFONE1, TELEFONE2, CELULAR1, 
			CELULAR2, EMAIL, MSN) VALUES ('$cliente->ID', '$fiador->DOC_CPF', '$fiador->DOC_RG', '$fiador->TRATAMENTO', '$fiador->NOME', 
			'$dataNascimentoFiador', '$fiador->TELEFONE1', '$fiador->TELEFONE2', '$fiador->CELULAR1', '$fiador->CELULAR2', '$fiador->EMAIL', 
			'$fiador->MSN')";
		
		
			if(!$resultFiador = $this->conn->query($queryFiador))
				{
					return 'ERRO CADASTRO FIADOR: '.$this->conn->error;
				}
		
		
			//CADASTRA ENDERECO FIADOR
		$queryEndFiador = "INSERT INTO enderecos_principais (FIADORES_ID, CLIENTES_ID, CEP, ENDERECO, NUMERO, COMPLEMENTO, BAIRRO, 
			CIDADE, UF, PAIS, REFERENCIA) VALUES ('$codigoFiador', '$endFiador->CLIENTES_ID', '$endFiador->CEP',  '$endFiador->ENDERECO', '$endFiador->NUMERO', 
			'$endFiador->COMPLEMENTO', '$endFiador->BAIRRO', '$endFiador->CIDADE', '$endFiador->UF', '$endFiador->PAIS', 
			'$endFiador->REFERENCIA')";
			
			
			if(!$resultEndFiador = $this->conn->query($queryEndFiador))
				{
					return 'ERRO CADASTRO ENDERECO FIADOR: '.$this->conn->error;
				}
			
			
			//VERIFICA SE O CEP EXISTE NA BASE LOCAL
			$queryCepLocalVerifica = "SELECT * FROM cep WHERE CEP LIKE '%$endFiador->CEP%'";
			$resultCepLocalVerifica = $this->conn->query($queryCepLocalVerifica);
			$nRowCepLocalVerifica = $resultCepLocalVerifica->num_rows;
			
			if($nRowCepLocalVerifica == 0)
			{
				$queryInsertCepLocal = "INSERT INTO cep (ID ,CEP ,ENDERECO ,BAIRRO ,CIDADE ,UF ,PAIS)
					VALUES (NULL , '$endFiador->CEP', '$endFiador->ENDERECO', '$endFiador->BAIRRO', 
					'$endFiador->CIDADE', '$endFiador->UF', '$endFiador->PAIS')";
				
				
			if(!$resultInsertCepLocal = $this->conn->query($queryInsertCepLocal))
				{
					return 'ERRO CADASTRO CEP ENDERECO FIADOR: '.$this->conn->error;
				}
			}
			
			
			}
		}
		//-----------CADASTRA FIADOR, CASO HOUVER
	
			
		//-----------DOC CLIENTES --INATIVO
//			$queyDoc = "INSERT INTO doc_clientes (
//				CLIENTES_ID ,COMPOVANTE_ENDERECO ,RG_CONTRATO_SOCIAL ,CPF_CNPJ ,CONTRATO_ASSINADO)
//				VALUES ('$cliente->ID', '$docCliente->COMPOVANTE_ENDERECO', '$docCliente->RG_CONTRATO_SOCIAL', 
//				'$docCliente->CPF_CNPJ', '$docCliente->CONTRATO_ASSINADO')";
//			
//			
//			if(!$resultDoc = $this->conn->query($queyDoc))
//				{
//					return 'ERRO CADASTRO DOC: '.$this->conn->error;
//				}
		//-----------DOC CLIENTES
		
		
		return 'ok';
			
		
	}
	public function excluirCliente($codCliente)
	{
		$query = "DELETE FROM clientes WHERE ID = '$codCliente'";
		if(!$result = $this->conn->query($query))
		{
			return 'EXCLUIR CLIENTE: '.$this->conn->error;
		}
		
		return 'ok';		
	}
	public function editarCliente(ClientesVO $cliente, EndPrincipaisVO $endPrincipal, EndEntregaVO $endEntrega, EndCobrancaVO $endCobranca,
										FiadorVO $fiador, EndPrincipaisVO $endFiador, DocClienteVO $docCliente)
	{
		$dia = substr($cliente->DATA_NASCIMENTO, 0, 2);
		$mes = substr($cliente->DATA_NASCIMENTO, 2, 2);
		$ano = substr($cliente->DATA_NASCIMENTO, 4, 4);
		$dataNascimento = $ano.'-'.$mes.'-'.$dia;	
		
		$dataCadastro = date('Y-m-d h:s:i');
		
		$cliente->DOC_CPF_CNPJ = str_replace('.', '', $cliente->DOC_CPF_CNPJ);
		$cliente->DOC_CPF_CNPJ = str_replace('-', '', $cliente->DOC_CPF_CNPJ);
		$cliente->DOC_CPF_CNPJ = str_replace('/', '', $cliente->DOC_CPF_CNPJ);
		
		//ATUALIZA CADASTRO DE CLIENTE
		$query = "UPDATE clientes SET CATEGORIAS_ID = '$cliente->CATEGORIAS_ID', STATUS_2 =  '$cliente->STATUS_2',
			TIPO_PESSOA ='$cliente->TIPO_PESSOA',DOC_RG_INSC_ESTADUAL ='$cliente->DOC_RG_INSC_ESTADUAL',
			TRATAMENTO ='$cliente->TRATAMENTO',
			NOME_RAZAO ='$cliente->NOME_RAZAO',NOME_FANTASIA ='$cliente->NOME_FANTASIA',CONTATO ='$cliente->CONTATO',SEXO ='$cliente->SEXO',DATA_NASCIMENTO ='$dataNascimento',
			TELEFONE1 ='$cliente->TELEFONE1',TELEFONE2 ='$cliente->TELEFONE2',CELULAR1 ='$cliente->CELULAR1',CELULAR2 ='$cliente->CELULAR2',
			EMAIL ='$cliente->EMAIL',MSN ='$cliente->MSN',COMO_NOS_CONHECEU ='$cliente->COMO_NOS_CONHECEU',OBS ='$cliente->OBS', TRANSPORTADORA_PADRAO ='$cliente->TRANSPORTADORA_PADRAO'
			, FORMA_PGTO_PADRAO ='$cliente->FORMA_PGTO_PADRAO', TABELA_PRECO_PADRAO ='$cliente->TABELA_PRECO_PADRAO' WHERE  ID ='$cliente->ID'";
		
		if(!$result = $this->conn->query($query))
		{
			return 'ERRO ATUALIZA CADASTRO PRINCIPAL DE CLIENTE: '.$this->conn->error;
		}
		
		
		
		
		//ATUALIZA CADASTRO ENDERECO PRINCIPAL DO CLIENTE
		$queryEndPrincipal = "UPDATE  enderecos_principais SET  FIADORES_ID ='$endPrincipal->FIADORES_ID', 
		CLIENTES_ID ='$endPrincipal->CLIENTES_ID',
			CEP = '$endPrincipal->CEP', ENDERECO ='$endPrincipal->ENDERECO',NUMERO ='$endPrincipal->NUMERO',COMPLEMENTO ='$endPrincipal->COMPLEMENTO',
			BAIRRO ='$endPrincipal->BAIRRO',CIDADE ='$endPrincipal->CIDADE',UF =  '$endPrincipal->UF',PAIS ='$endPrincipal->PAIS',
			REFERENCIA ='$endPrincipal->REFERENCIA' WHERE  ID = '$endPrincipal->ID'";
		
				
		if(!$resultEndPrincipal = $this->conn->query($queryEndPrincipal))
		{
			return 'ERRO ATUALIZA ENDERECO PRINCIPAL : '.$this->conn->error;
		}
		
		
		//-----------CADASTRA ENDERECO DE ENTREGA DO CLIENTE
		$queryEndEntrega = "UPDATE enderecos_entrega SET  CEP =  '$endEntrega->CEP',ENDERECO =  '$endEntrega->ENDERECO',NUMERO =  '$endEntrega->NUMERO',
						COMPLEMENTO =  '$endEntrega->COMPLEMENTO',BAIRRO = '$endEntrega->BAIRRO',CIDADE = '$endEntrega->CIDADE',
						UF =  '$endEntrega->UF',PAIS =  '$endEntrega->PAIS',REFERENCIA =  '$endEntrega->REFERENCIA',ENDERECO_PRINCIPAL =  
						'$endEntrega->ENDERECO_PRINCIPAL' 
						WHERE  ID ='$endEntrega->ID'";	
			
		
		if(!$resultEndEntrega = $this->conn->query($queryEndEntrega))
		{
			return 'ERRO ATUALIZA ENDERECO ENTREGA : '.$this->conn->error;
		}
		//-----------CADASTRA ENDERECO DE ENTREGA DO CLIENTE
		
		
		//-----------CADASTRA ENDERECO DE COBRANCA DO CLIENTE
		$queryEndCobranca = "UPDATE enderecos_cobranca SET  CEP =  '$endCobranca->CEP',ENDERECO =  '$endCobranca->ENDERECO',NUMERO =  '$endCobranca->NUMERO',
						COMPLEMENTO =  '$endCobranca->COMPLEMENTO',BAIRRO = '$endCobranca->BAIRRO',CIDADE = '$endCobranca->CIDADE',
						UF =  '$endCobranca->UF',PAIS =  '$endCobranca->PAIS',REFERENCIA =  '$endCobranca->REFERENCIA',ENDERECO_PRINCIPAL =  
						'$endCobranca->ENDERECO_PRINCIPAL' 
						WHERE  ID ='$endCobranca->ID'";
			
		
		if(!$resultEndCobranca = $this->conn->query($queryEndCobranca))
		{
			return 'ERRO ATUALIZA ENDERECO COBRANCA : '.$this->conn->error;
		}
		//-----------CADASTRA ENDERECO DE COBRANCA DO CLIENTE
		
		
		//-----------CADASTRA FIADOR, CASO HOUVER
		
			if($fiador->NOME != null)
			{
				$diaF = substr($fiador->DATA_NASCIMENTO, 0, 2);
					$mesF = substr($fiador->DATA_NASCIMENTO, 2, 2);
					$anoF = substr($fiador->DATA_NASCIMENTO, 4, 4);
					$dataNascimentoFiador = $anoF.'-'.$mesF.'-'.$diaF;
				
		//PROCURA FIADOR EXISTENTE
		$queryFiadorCliente = "SELECT * FROM fiadores WHERE CLIENTES_ID = '$cliente->ID'";
		$resultFiadorCliente = $this->conn->query($queryFiadorCliente);
		$nRowFiadorCliente = $resultFiadorCliente->num_rows;
		if($nRowFiadorCliente > 0){
			$rowFiadorCliente = $resultFiadorCliente->fetch_assoc();
			$codFiadorExistente = $rowFiadorCliente['ID'];
			
			//ATUALIZA FIADOR EXISTENTE.
			
					
		$queryFiador = "UPDATE  fiadores SET DOC_CPF = '$fiador->DOC_CPF', DOC_RG = '$fiador->DOC_RG',TRATAMENTO =  '$fiador->TRATAMENTO',
		NOME = '$fiador->NOME',DATA_NASCIMENTO = '$dataNascimentoFiador',TELEFONE1 = '$fiador->TELEFONE1',TELEFONE2 =  '$fiador->TELEFONE2',
		CELULAR1 = '$fiador->CELULAR1',CELULAR2 = '$fiador->CELULAR2',EMAIL = '$fiador->EMAIL',MSN = '$fiador->MSN' 
		WHERE ID ='$codFiadorExistente'";		
		if(!$resultFiador = $this->conn->query($queryFiador))
		{
			return 'ERRO ATUALIZA FIADOR : '.$this->conn->error;
		}
		
						
		//ATUALIZA ENDERECO FIADOR
		$queryEndFiador = "UPDATE  enderecos_principais SET  FIADORES_ID ='$endFiador->FIADORES_ID', CLIENTES_ID ='$endFiador->CLIENTES_ID',
			CEP = '$endPrincipal->CEP',ENDERECO ='$endFiador->ENDERECO',NUMERO ='$endFiador->NUMERO',COMPLEMENTO ='$endFiador->COMPLEMENTO',
			BAIRRO ='$endFiador->BAIRRO',CIDADE ='$endFiador->CIDADE',UF =  '$endFiador->UF',PAIS ='$endFiador->PAIS',
			REFERENCIA ='$endFiador->REFERENCIA' WHERE  FIADORES_ID = '$codFiadorExistente'";
			
			
			if(!$resultEndFiador = $this->conn->query($queryEndFiador))
			{
				return 'ERRO ATUALIZA ENDERECO FIADOR : '.$this->conn->error;
			}
		}else
		{
			//CADASTRA NOVO FIADOR.
			
			//PEGA PROX COD FIADOR
		$queryCodFiador = "SHOW TABLE STATUS LIKE 'fiadores'";
		$resultCodFiador = $this->conn->query($queryCodFiador);
		$rowCodFiador = $resultCodFiador->fetch_assoc();	
		$codigoFiador = $rowCodFiador["Auto_increment"];
			
			$queryFiador = "INSERT INTO fiadores (CLIENTES_ID, DOC_CPF, DOC_RG, TRATAMENTO, NOME, DATA_NASCIMENTO, TELEFONE1, TELEFONE2, CELULAR1, 
			CELULAR2, EMAIL, MSN) VALUES ('$cliente->ID', '$fiador->DOC_CPF', '$fiador->DOC_RG', '$fiador->TRATAMENTO', '$fiador->NOME', 
			'$dataNascimentoFiador', '$fiador->TELEFONE1', '$fiador->TELEFONE2', '$fiador->CELULAR1', '$fiador->CELULAR2', '$fiador->EMAIL', 
			'$fiador->MSN')";
		
		
			if(!$resultFiador = $this->conn->query($queryFiador))
			{
				return 'ERRO CADASTRAR FIADOR : '.$this->conn->error;
			}
		
			//CADASTRA ENDERECO FIADOR
		$queryEndFiador = "INSERT INTO enderecos_principais (FIADORES_ID, CLIENTES_ID, CEP, ENDERECO, NUMERO, COMPLEMENTO, BAIRRO, 
			CIDADE, UF, PAIS, REFERENCIA) VALUES ('$codigoFiador', '$endFiador->CLIENTES_ID', '$endFiador->CEP',  '$endFiador->ENDERECO', '$endFiador->NUMERO', 
			'$endFiador->COMPLEMENTO', '$endFiador->BAIRRO', '$endFiador->CIDADE', '$endFiador->UF', '$endFiador->PAIS', 
			'$endFiador->REFERENCIA')";
			
			
			if(!$resultEndFiador = $this->conn->query($queryEndFiador))
			{
				return 'ERRO CADASTRAR ENDERECO FIADOR : '.$this->conn->error;
			}
		}
				
					
		
		
				
		
			
			}
		//-----------CADASTRA FIADOR, CASO HOUVER
		
			
		//-----------DOC CLIENTES--INATIVO
//			$queyDoc = "UPDATE  doc_clientes SET  CLIENTES_ID =  '$docCliente->CLIENTES_ID',
//					COMPOVANTE_ENDERECO =  '$docCliente->COMPOVANTE_ENDERECO',RG_CONTRATO_SOCIAL =  '$docCliente->RG_CONTRATO_SOCIAL',
//					CPF_CNPJ =  '$docCliente->CPF_CNPJ',CONTRATO_ASSINADO =  '$docCliente->CONTRATO_ASSINADO' 
//					WHERE  ID = '$docCliente->ID'";
//			
//			$resultDoc = $this->conn->query($queyDoc);
		//-----------DOC CLIENTES
		
			return 'ok';
	}
	
	
	//----CEPS---------------------//
	public function listarCeps()
	{
		$query = "SELECT * FROM  cep";
		$result = $this->conn->query($query);
		$nRow = $result->num_rows;
		if($nRow == 0){return 'ERRO';}
		
		while ($row = $result->fetch_assoc())
		{
			$cep = new CepsVO();
			$cep->ID = $row['ID'];
			$cep->CEP = $row['CEP']; 
			$cep->ENDERECO = $row['ENDERECO'];
			$cep->BAIRRO = $row['BAIRRO'];
			$cep->CIDADE = $row['CIDADE'];
			$cep->UF = $row['UF'];
			$cep->PAIS = $row['PAIS'];
			
			$ceps[] = $cep;
		}
		
		return $ceps;
	}
	public function buscarCEP($filtro, $valor)
	{
		
		$query = "SELECT * FROM cep WHERE 
		".$filtro." LIKE '%$valor%'";
		
		$result = $this->conn->query($query);
		$nRow = $result->num_rows;
		if($nRow == 0){ return 'ERRO'; }
			while($row = $result->fetch_assoc())
			{
			
				$cep = new CepsVO();
				
				$cep->ID = $row['ID']; 
				$cep->CEP = $row['CEP']; 
				$cep->ENDERECO = $row['ENDERECO'];
				$cep->BAIRRO = $row['BAIRRO'];
				$cep->CIDADE = $row['CIDADE'];
				$cep->UF = $row['UF'];
				$cep->PAIS = $row['PAIS'];
				$ceps[] = $cep;
			}
			
			return $ceps;
		
	}
	public function procurarCep($cep)
	{
		$query = "SELECT * FROM cep WHERE CEP LIKE '$cep'";
		$result = $this->conn->query($query);
		$nRow = $result->num_rows;
		if($nRow == 0)
		{
			return 'inexistente';
		}else
		{
			$row = $result->fetch_assoc();
			
			$cep = new CepsVO();
			
			$cep->ID = $row['ID']; 
			$cep->CEP = $row['CEP']; 
			$cep->ENDERECO = $row['ENDERECO'];
			$cep->BAIRRO = $row['BAIRRO'];
			$cep->CIDADE = $row['CIDADE'];
			$cep->UF = $row['UF'];
			$cep->PAIS = $row['PAIS'];
			
			return $cep;
		}
		
		
	}
	public function selecionarCep($cod)
	{
		$query = "SELECT * FROM  cep WHERE ID = '$cod'";
		$result = $this->conn->query($query);
		$nRow = $result->num_rows;
		if($nRow == 0){return 'ERRO';}
		$row = $result->fetch_assoc();
		
			$cep = new CepsVO();
			$cep->ID = $row['ID'];
			$cep->CEP = $row['CEP']; 
			$cep->ENDERECO = $row['ENDERECO'];
			$cep->BAIRRO = $row['BAIRRO'];
			$cep->CIDADE = $row['CIDADE'];
			$cep->UF = $row['UF'];
			$cep->PAIS = $row['PAIS'];
		
		return $cep;
	}	
	public function editarCep(CepsVO $cep)
	{
		$query = "UPDATE cep SET  CEP =  '$cep->CEP',
			ENDERECO =  '$cep->ENDERECO',BAIRRO =  '$cep->BAIRRO',
			CIDADE =  '$cep->CIDADE',UF =  '$cep->UF',PAIS =  '$cep->PAIS' WHERE ID = '$cep->ID'";
		
		$result = $this->conn->query($query);
	}
	public function cadastrarCep(CepsVO $cep)
	{
		$query = "INSERT INTO  cep (
		CEP ,ENDERECO ,BAIRRO ,CIDADE ,UF ,PAIS)
		VALUES ('$cep->CEP',  '$cep->ENDERECO',  '$cep->BAIRRO',  '$cep->CIDADE',  '$cep->UF',  '$cep->PAIS')";
		
		$result = $this->conn->query($query);
	}
	public function excluirCep(CepsVO $cep)
	{
		$query = "DELETE FROM cep WHERE ID = '$cep->ID'";
		$result = $this->conn->query($query);
	}
	
	
	//---CONTRATOS--------------//
	public function listarContratos($cod)
	{
		$query = "SELECT * FROM contratos WHERE EMPRESA_ID = '$cod' ORDER by ID DESC";
		$result = $this->conn->query($query);
		
		
		while($row = $result->fetch_assoc())
		{
			$contrato = new ContratosVO();
			
			$contrato->ID = $row['ID'];
			$contrato->EMPRESA_ID = $row['EMPRESA_ID'];			
			$contrato->NOME = $row['NOME'];
			$contrato->DESCRICAO = $row['DESCRICAO'];
			$contrato->CLAUSULAS = $row['CLAUSULAS'];
			$contrato->VIGENCIA = $row['VIGENCIA'];
			$contrato->GERAR_TITULO = $row['GERAR_TITULO'];
			$contrato->VARLOR_TITULO = $row['VARLOR_TITULO'];
			
			$contratos[] = $contrato;
		}
		
		return $contratos;
	}
	public function selecionarContrato($id)
	{
		$query = "SELECT * FROM contratos WHERE id = '$id'";
		$result = $this->conn->query($query);
		
		$row = $result->fetch_assoc();
		
			$contrato = new ContratosVO();
			
			$contrato->ID = $row['ID'];
			$contrato->EMPRESA_ID = $row['EMPRESA_ID'];
			$contrato->NOME = $row['NOME'];
			$contrato->DESCRICAO = $row['DESCRICAO'];
			$contrato->CLAUSULAS = $row['CLAUSULAS'];
			$contrato->VIGENCIA = $row['VIGENCIA'];
			$contrato->GERAR_TITULO = $row['GERAR_TITULO'];
			$contrato->VARLOR_TITULO = $row['VARLOR_TITULO'];
			
					
		
		return $contrato;
	}
	public function editarContrato(ContratosVO $_contrato)
	{
		
		$clausulas = str_replace('TEXTFORMAT', 'P style="padding:0; margin:0;"', $_contrato->CLAUSULAS);
		if($_contrato->GERAR_TITULO == 'SIM')
		{
			$_contrato->GERAR_TITULO = 's';
		}else
		{
			$_contrato->GERAR_TITULO = 'n';
		}
		
		
		$query = "UPDATE contratos SET EMPRESA_ID = '$_contrato->EMPRESA_ID', NOME = '$_contrato->NOME',
				DESCRICAO = '$_contrato->DESCRICAO',CLAUSULAS = '$clausulas',
				VIGENCIA = '$_contrato->VIGENCIA',GERAR_TITULO = '$_contrato->GERAR_TITULO',
				VARLOR_TITULO = '$_contrato->VARLOR_TITULO' WHERE ID = '$_contrato->ID'";
		$resul = $this->conn->query($query);
		
	
	}
	public function cadastrarContrato(ContratosVO $_contrato)
	{
		if($_contrato->GERAR_TITULO == 'SIM')
		{
			$_contrato->GERAR_TITULO = 's';
		}else
		{
			$_contrato->GERAR_TITULO = 'n';
		}
		
		$query = "INSERT INTO contratos (
			EMPRESA_ID, NOME ,DESCRICAO ,CLAUSULAS ,VIGENCIA ,GERAR_TITULO ,VARLOR_TITULO
			)VALUES ('$_contrato->EMPRESA_ID', '$_contrato->NOME', '$_contrato->DESCRICAO', '$_contrato->CLAUSULAS', 
			'$_contrato->VIGENCIA', '$_contrato->GERAR_TITULO', '$_contrato->VARLOR_TITULO')";
		$resul = $this->conn->query($query);
		
		
	}
	public function excluirContrato(ContratosVO $_contrato)
	{
		$query = "DELETE FROM contratos WHERE 	ID = '$_contrato->ID'";
		$resul = $this->conn->query($query);
		
	}
	public function pegarValorTitulo($nomeContrato)
	{
		$query = "SELECT * FROM contratos WHERE nomeContrato LIKE '$nomeContrato'";
		$result = $this->conn->query($query);
		$row = $result->fetch_assoc();
		
		$valorTitulo = $row['valorTitulo'];
		
		return $valorTitulo;
	}
	
		
	//---CATEGORIAS--------------//
	public function listarCategorias($codEmpresa)
	{
		$query = "SELECT * FROM categorias WHERE EMPRESA_ID = '$codEmpresa'";
		$result = $this->conn->query($query);
		$nRow = $result->num_rows;
		if($nRow == 0){return 'ERRO';}
		
		
		while ($row = $result->fetch_assoc()) {
			
			$categoria = new CategoriaVO();
			
			$categoria->ID = $row['ID'];
			$categoria->EMPRESA_ID = $row['EMPRESA_ID'];
			$categoria->NOME = $row['NOME'];
			
			$categorias[] = $categoria;
		}
		
		return $categorias;
	}
	public function cadastrarCategoria(CategoriaVO $categoria)
	{
		$categoria->NOME = strtoupper($categoria->NOME);
		$query = "INSERT INTO categorias (
EMPRESA_ID ,NOME)
VALUES ('$categoria->EMPRESA_ID', '$categoria->NOME')";
		$result = $this->conn->query($query);
	}
	public function editarCategoria(CategoriaVO $categoria)
	{
		$query = "UPDATE categorias SET NOME = '$categoria->NOME' WHERE ID ='$categoria->ID'";
		$result = $this->conn->query($query);		 
	
	}
	public function excluirCategoria(CategoriaVO $categoria)
	{
		$query = "DELETE FROM categorias WHERE ID = '$categoria->ID'";
		$result = $this->conn->query($query);
		
	}	
	
	
	//---TRANSPORTADORAS-----------//
	public function listarTransportadoras($empresa)
	{
		$query = "SELECT * FROM transportadoras WHERE EMPRESA_ID = '$empresa'";
		$result = $this->conn->query($query);
		$nRow = $result->num_rows;
		if($nRow == 0){return 'ERRO';}
		
		while ($row = $result->fetch_assoc()) {
			$transportadora = new TransportadorasVO();
			$transportadora->ID = $row['ID'];
		  	$transportadora->EMPRESA_ID = $row['EMPRESA_ID'];
		  	$transportadora->NOME = $row['NOME'];
		  	$transportadora->FANTASIA = $row['FANTASIA'];
		  	$transportadora->TIPO_PESSOA = $row['TIPO_PESSOA'];
		  	$transportadora->CNPJ = $row['CNPJ'];
		  	$transportadora->IE = $row['IE'];
		  	$transportadora->CEP = $row['CEP'];
		  	$transportadora->ENDERECO = $row['ENDERECO'];
		  	$transportadora->NUMERO = $row['NUMERO'];
		  	$transportadora->COMPLEMENTO = $row['COMPLEMENTO'];
		  	$transportadora->BAIRRO = $row['BAIRRO'];
		  	$transportadora->CIDADE = $row['CIDADE'];
		  	$transportadora->UF = $row['UF'];
		  	$transportadora->PAIS = $row['PAIS'];
		  	$transportadora->REFERENCIA = $row['REFERENCIA'];
		  	$transportadora->FONE = $row['FONE'];
		  	$transportadora->FAX = $row['FAX'];
		  	$transportadora->SITE = $row['SITE'];
		  	$transportadora->EMAIL = $row['EMAIL'];
		  	$transportadora->OBSERVACAO = $row['OBSERVACAO'];
		  	
		  	$transportadoras[] = $transportadora;
			
		}
		
		return $transportadoras;
	}
	public function cadastrarTransportadora(TransportadorasVO $transportadora)
	{
		$query = "INSERT INTO transportadoras 
		(ID, EMPRESA_ID, NOME, FANTASIA, TIPO_PESSOA, CNPJ, IE, CEP, ENDERECO, NUMERO, COMPLEMENTO, BAIRRO, 
		CIDADE, UF, PAIS, REFERENCIA, FONE, FAX, SITE, EMAIL, OBSERVACAO) VALUES 
		(NULL, '$transportadora->EMPRESA_ID', '$transportadora->NOME', '$transportadora->FANTASIA', '$transportadora->TIPO_PESSOA', '$transportadora->CNPJ', 
		'$transportadora->IE', '$transportadora->CEP', '$transportadora->ENDERECO', '$transportadora->NUMERO', '$transportadora->COMPLEMENTO', 
		'$transportadora->BAIRRO', '$transportadora->CIDADE', '$transportadora->UF', '$transportadora->PAIS', '$transportadora->REFERENCIA', 
		'$transportadora->FONE', '$transportadora->FAX', '$transportadora->SITE', '$transportadora->EMAIL', '$transportadora->OBSERVACAO')";
		
		$result = $this->conn->query($query);
	}
	public function editarTransportadora(TransportadorasVO $transp)
	{
		$query = "UPDATE  transportadoras SET  EMPRESA_ID =  '$transp->EMPRESA_ID',NOME =  '$transp->NOME',FANTASIA =  '$transp->FANTASIA',
		TIPO_PESSOA =  '$transp->TIPO_PESSOA',CNPJ =  '$transp->CNPJ',
		IE =  '$transp->IE',CEP =  '$transp->CEP',ENDERECO =  '$transp->ENDERECO',NUMERO =  '$transp->NUMERO',COMPLEMENTO =  '$transp->COMPLEMENTO',
		BAIRRO =  '$transp->BAIRRO',CIDADE =  '$transp->CIDADE',UF =  '$transp->UF',PAIS =  '$transp->PAIS',REFERENCIA =  '$transp->REFERENCIA',
		FONE =  '$transp->FONE',FAX =  '$transp->FAX',SITE =  '$transp->SITE',EMAIL =  '$transp->EMAIL',
		OBSERVACAO =  '$transp->OBSERVACAO' WHERE ID ='$transp->ID'";
		
		$result = $this->conn->query($query);
	}
	public function excluirTransportadora($cod)
	{
		$query = "DELETE FROM transportadoras WHERE ID = '$cod'";
		$result = $this->conn->query($query);
	}
	public function selecionarTransportadora($cod)
	{
		$query = "SELECT * FROM transportadoras WHERE ID LIKE '%$cod%'";
		$result = $this->conn->query($query);
		$nRow = $result->num_rows;
		if($nRow == 0){return 'ERRO';}
		$row = $result->fetch_assoc();
		
			$transportadora = new TransportadorasVO();
			
			$transportadora->ID = $row['ID'];
		  	$transportadora->EMPRESA_ID = $row['EMPRESA_ID'];
		  	$transportadora->NOME = $row['NOME'];
		  	$transportadora->FANTASIA = $row['FANTASIA'];
		  	$transportadora->TIPO_PESSOA = $row['TIPO_PESSOA'];
		  	$transportadora->CNPJ = $row['CNPJ'];
		  	$transportadora->IE = $row['IE'];
		  	$transportadora->CEP = $row['CEP'];
		  	$transportadora->ENDERECO = $row['ENDERECO'];
		  	$transportadora->NUMERO = $row['NUMERO'];
		  	$transportadora->COMPLEMENTO = $row['COMPLEMENTO'];
		  	$transportadora->BAIRRO = $row['BAIRRO'];
		  	$transportadora->CIDADE = $row['CIDADE'];
		  	$transportadora->UF = $row['UF'];
		  	$transportadora->PAIS = $row['PAIS'];
		  	$transportadora->REFERENCIA = $row['REFERENCIA'];
		  	$transportadora->FONE = $row['FONE'];
		  	$transportadora->FAX = $row['FAX'];
		  	$transportadora->SITE = $row['SITE'];
		  	$transportadora->EMAIL = $row['EMAIL'];
		  	$transportadora->OBSERVACAO = $row['OBSERVACAO'];
		  	
		  	return $transportadora;
	}
	
	//---CPF----------------------//
	public function validarCPF($cpf)
		  {
		
		  	if($cpf == '000.000.000-00')
	 		{
	 			return 'false2';
	 		}
		   else if($cpf == '00.000.000/0000-00')
	 		{
	 			return 'false2';
	 		}
	
		 //Retira ponto hifen e barras
		$cpf = str_replace(".","", $cpf);
		$cpf = str_replace("-","", $cpf);
		$cpf = str_replace("/","", $cpf);
		
		//Verifica se � CPF ou CNPJ
		
		$qtd = strlen($cpf);
	//return;
		
		if($qtd == '11'){
		

 		//VERIFICA SE O QUE FOI INFORMADo � N�MERO
 		
			if(!is_numeric($cpf)) {

				$status = false;
 			
			}else {
  			
				//VERIFICA
			  if( ($cpf == '11111111111') || ($cpf == '22222222222') ||
			   ($cpf == '33333333333') || ($cpf == '44444444444') ||
			   ($cpf == '55555555555') || ($cpf == '66666666666') ||
			   ($cpf == '77777777777') || ($cpf == '88888888888') ||
			   ($cpf == '99999999999') || ($cpf == '00000000000') ) {
			   $status = false;
			  }else {
			   //PEGA O DIGITO VERIFIACADOR
   				$dv_informado = substr($cpf, 9,2);

			   for($i=0; $i<=8; $i++) {
			    	$digito[$i] = substr($cpf, $i,1);
			   }
			
			   //CALCULA O VALOR DO 10� DIGITO DE VERIFICA��O
			   $posicao = 10;
			   $soma = 0;

			   for($i=0; $i<=8; $i++) {
				    $soma = $soma + $digito[$i] * $posicao;
				    $posicao = $posicao - 1;
			   }

   				$digito[9] = $soma % 11;

			   if($digito[9] < 2) {
			    	$digito[9] = 0;
			   }else {
    				$digito[9] = 11 - $digito[9];
   				}

			   //CALCULA O VALOR DO 11� DIGITO DE VERIFICA��O
			   $posicao = 11;
			   $soma = 0;
			
			   for ($i=0; $i<=9; $i++) {
			    $soma = $soma + $digito[$i] * $posicao;
			    $posicao = $posicao - 1;
			   }
			
			   $digito[10] = $soma % 11;

			   if ($digito[10] < 2) {
			    $digito[10] = 0;
			   }
			   else {
			    $digito[10] = 11 - $digito[10];
  				 }

			  //VERIFICA SE O DV CALCULADO � IGUAL AO INFORMADO
			  $dv = $digito[9] * 10 + $digito[10];
			  if ($dv != $dv_informado) {
			   $status = false;
			  }
			  else
			   $status = true;
			  }//FECHA ELSE
			  }//FECHA ELSE(is_numeric)
	
	return $status;
	
		}else{
			
		//Pegando Primeiro DV Informado
		$dvInformado1 = substr($cpf, 12,1);
		
		//Pegando Segundo DV Informado
		$dvInformado2 = substr($cpf, 13,1);
		
		//Dividindo Numeros
		for($i=0; $i<=11; $i++) {
			$digito[$i] = substr($cpf, $i,1);
		   }   
		   
		   //Multiplica��es
		   
		   $r1 = $digito[0] * 5;
		   $r2 = $digito[1] * 4;
		   $r3 = $digito[2] * 3;
		   $r4 = $digito[3] * 2;
		   $r5 = $digito[4] * 9;
		   $r6 = $digito[5] * 8;
		   $r7 = $digito[6] * 7;
		   $r8 = $digito[7] * 6;
		   $r9 = $digito[8] * 5;
		   $r10 = $digito[9] * 4;
		   $r11 = $digito[10] * 3;
		   $r12 = $digito[11] * 2;
		   
		   //Soma
		   
		   $soma = $r1+$r2+$r3+$r4+$r5+$r6+$r7+$r8+$r9+$r10+$r11+$r12;
		   
		   //Divis�o
		   
		   $soma = $soma % 11;
		   
		   if($soma < 2){
				
				$soma = 0;
		   }else{
			
				$soma = 11 - $soma;
					}
				$dv1 = $soma;
	
			if($dv1 == $dvInformado1){
				
				//Pegando Segundo DV
				
				
				//Dividindo Numeros
		for($i=0; $i<=12; $i++) {
			$digito2[$i] = substr($cpf, $i,1);
		   }   
				 //Multiplica��es
		   
		   $rr1 = $digito2[0] * 6;
		   $rr2 = $digito2[1] * 5;
		   $rr3 = $digito2[2] * 4;
		   $rr4 = $digito2[3] * 3;
		   $rr5 = $digito2[4] * 2;
		   $rr6 = $digito2[5] * 9;
		   $rr7 = $digito2[6] * 8;
		   $rr8 = $digito2[7] * 7;
		   $rr9 = $digito2[8] * 6;
		   $rr10 = $digito2[9] * 5;
		   $rr11 = $digito2[10] * 4;
		   $rr12 = $digito2[11] * 3;
		   $rr13 = $digito2[12] * 2;
				
			//Soma
		   
		   $soma2 = $rr1+$rr2+$rr3+$rr4+$rr5+$rr6+$rr7+$rr8+$rr9+$rr10+$rr11+$rr12+$rr13;
		   
		    //Divis�o
		   
		   $soma2 = $soma2 % 11;
		   
		   if($soma2 < 2){
				
				$soma2 = 0;
		   }else{
			
				$soma2 = 11 - $soma2;
					}
				$dv2 = $soma2;
				
				if($dv2 == $dvInformado2){
				
				$status = true;
				}else{
				$status = 'false2';	
				}
				
			}else{
				$status = 'false2';	
			}
				return $status;
			
			
			
			
			}
				return $sucesso = 'Sucesso';	
		  }	
		  
		
		
}