<?php

require_once 'classes/Conexao.php';

require_once 'vo/ConfiguracoesVO.php';
require_once 'vo/tipoProblema_OSI.php';

require_once 'vo/ModuloVO.php';
require_once 'vo/SubModuloVO.php';
require_once 'vo/EmpresaVO.php';
require_once 'vo/UsuarioVO.php';
require_once 'vo/PermissoesVO.php';
require_once 'vo/PermissoesVO.php';
require_once 'vo/impressorasVO.php';


class Configuracoes extends Conexao
{
	
	public function listarModulos()
	{
		$query = "SELECT * FROM modulo ORDER by ID";
		$result = $this->conn->query($query);
		
		while ($row = $result->fetch_assoc())
		{
			$modulo = new ModuloVO();
			
			$modulo->ID = $row['ID'];
			$modulo->NOME_MODULO = $row['NOME_MODULO'];
			$modulo->DESCRICAO = $row['DESCRICAO'];
			
			$modulos[] = $modulo;
		}
		
		return $modulos;
	}
	
	public function listarModulosEmpresa($cod)
	{
		$query = "SELECT * FROM modulos_empresa WHERE EMPRESA_ID = '$cod'";
		$result = $this->conn->query($query);
		$nRow1 = $result->num_rows;
		
		if($nRow1 > 0)
		{
			while ($row = $result->fetch_assoc())
			{
				$idModulo = $row['MODULO_ID'];
				
				$query2 = "SELECT * FROM modulo WHERE ID = '$idModulo'";
				$result2 = $this->conn->query($query2);
				$nRow = $result->num_rows;
				$row2 = $result->fetch_assoc();
				
				if($nRow > 0)
				{
					$modulo = new ModuloVO();
				
					$modulo->ID = $row2['ID'];
					$modulo->NOME_MODULO = $row2['NOME_MODULO'];
					$modulo->DESCRICAO = $row2['DESCRICAO'];
					
					$modulos[] = $modulo;
				}			
				
			}
		}else
		{
			return 'ERRO';
		}
		
		return $modulos;
	}
	
	public function excluirModuloEmpresa($codModulo,$codEmpresa)
	{
		$query = "DELETE FROM modulos_empresa WHERE MODULO_ID = '$codModulo' AND EMPRESA_ID = '$codEmpresa'";
		$result = $this->conn->query($query);	
		
		return 'OK';
	}
	public function excluirSubModuloEmpresa($codSubModulo,$codEmpresa)
	{
		$query = "DELETE FROM submodulo_empresa WHERE SUBMODULO_ID = '$codSubModulo' AND EMPRESA_ID = '$codEmpresa'";
		$result = $this->conn->query($query);	
		
		return 'OK';
	}
	
	
	public function listarSubModulos($CodModulo)
	{
		$query = "SELECT * FROM submodulo WHERE MODULO_ID = '$CodModulo'";
		$result = $this->conn->query($query);
		$nrow = $result->num_rows;
		
		if($nrow >0)
		{
		
			while ($row = $result->fetch_assoc())
			{
				$subModulo = new SubModuloVO();
				
				$subModulo->ID = $row['ID'];
				$subModulo->MODULO_ID = $row['MODULO_ID'];
				$subModulo->NOME = $row['NOME'];
				$subModulo->DESCRICAO = $row['DESCRICAO'];
				
				$subModulos[] = $subModulo;
			}
			
			return $subModulos;
		}else
		{
			return 'ERRO';
		}
		
	}
	
	
	public function listarSubModulosEmpresa($CodModulo, $CodEmpresa)
	{
		$query = "SELECT * FROM submodulo WHERE MODULO_ID = '$CodModulo'";
		$result = $this->conn->query($query);
		$nRow = $result->num_rows;
		
		if($nRow > 0)
		{
			while ($row = $result->fetch_assoc())
			{
			$codSubModulo = $row['ID'];
			
			$query2 = "SELECT * FROM submodulo_empresa WHERE SUBMODULO_ID = '$codSubModulo' AND EMPRESA_ID = '$CodEmpresa'";
			$result2 = $this->conn->query($query2);
			$nRow2 = $result2->num_rows;
			
			if($nRow2 > 0)
			{
				$subModulo = new SubModuloVO();
				
				$subModulo->ID = $row['ID'];
				$subModulo->MODULO_ID = $row['MODULO_ID'];
				$subModulo->NOME = $row['NOME'];
				$subModulo->DESCRICAO = $row['DESCRICAO'];
				
				$subModulos[] = $subModulo;
			}
			}
			
		}else
		{
			return 'ERRO';
		}
		
			return $subModulos;
	}
	
	
	public function cadastrarModulo(ModuloVO $modulo)
	{
		$query = "INSERT INTO modulo ( ID ,NOME_MODULO ,DESCRICAO)
					VALUES (NULL , '$modulo->NOME_MODULO', '$modulo->DESCRICAO')";
		
		$result = $this->conn->query($query);
	}
	public function cadastrarModuloEmpresa($codModulo, $codEmpresa)
	{
		$queryV = "SELECT * FROM modulos_empresa WHERE MODULO_ID = '$codModulo' AND EMPRESA_ID = '$codEmpresa'"; 
		$resultV = $this->conn->query($queryV);
		$nRowV = $resultV->num_rows;
		
		if($nRowV > 0)
		{
			return 'ERRO';
		}else 
		{
			$query = "INSERT INTO modulos_empresa (
			ID ,MODULO_ID ,EMPRESA_ID)VALUES (
			NULL , '$codModulo', '$codEmpresa')";
		
			$result = $this->conn->query($query);
			
			return 'OK';
		}
		
		
	}
	
	public function cadastrarSubModuloEmpresa($codSubModulo, $codEmpresa)
	{
		$queryV = "SELECT * FROM submodulo_empresa WHERE SUBMODULO_ID  = '$codSubModulo' AND EMPRESA_ID = '$codEmpresa'"; 
		$resultV = $this->conn->query($queryV);
		$nRowV = $result->num_rows;
		
		if($nRowV > 0)
		{
			return 'ERRO';
		}else 
		{
			$query = "INSERT INTO submodulo_empresa (
			SUBMODULO_ID ,EMPRESA_ID)VALUES (
			'$codSubModulo', '$codEmpresa')";
		
			$result = $this->conn->query($query);
			
			return 'OK';
		}
		
		
	}
	
	public function selecionarModulo($cod)
	{
		$query = "SELECT * FROM modulo WHERE ID = '$cod'";
		$result = $this->conn->query($query);
		$row = $result->fetch_assoc();
		
			$modulo = new ModuloVO();
			
			$modulo->ID = $row['ID'];
			$modulo->NOME_MODULO = $row['NOME_MODULO'];
			$modulo->DESCRICAO = $row['DESCRICAO'];
			
			
		
		
		return $modulo;
	}
	
	public function editarModulo(ModuloVO $modulo)
	{
		$query = "UPDATE modulo SET NOME_MODULO = '$modulo->NOME_MODULO',
					DESCRICAO = '$modulo->DESCRICAO' WHERE ID = '$modulo->ID'";
		
		$result = $this->conn->query($query);
	}
	public function excluirModulo($cod)
	{
		$query = "DELETE FROM modulo WHERE modulo.ID = '$cod'";
		$result = $this->conn->query($query);
		
	}
	
	
	public function cadastrarSubModulo(SubModuloVO $submodulo)
	{
		$query = "INSERT INTO submodulo ( ID, MODULO_ID, NOME, DESCRICAO)
					VALUES (NULL , '$submodulo->MODULO_ID', '$submodulo->NOME', '$submodulo->DESCRICAO')";
		
		$result = $this->conn->query($query);
	}
	public function selecionarSubModulo($cod)
	{
		
		$query = "SELECT * FROM submodulo WHERE ID = '$cod'";
		$result = $this->conn->query($query);
		$row = $result->fetch_assoc();
		
			$subModulo = new SubModuloVO();
			
			$subModulo->ID = $row['ID'];
			$subModulo->MODULO_ID = $row['MODULO_ID'];
			$subModulo->NOME = $row['NOME'];
			$subModulo->DESCRICAO = $row['DESCRICAO'];
			
			
		
		
		return $subModulo;
	}
	public function editarSubModulo(SubModuloVO $submodulo)
	{
		$query = "UPDATE submodulo SET NOME = '$submodulo->NOME',	
		DESCRICAO = '$submodulo->DESCRICAO' WHERE ID = '$submodulo->ID'";
		
		$result = $this->conn->query($query);
	}
	public function excluirSubModulo(SubModuloVO $submodulo)
	{
		$query = "DELETE FROM submodulo WHERE ID = '$cod'";
		$result = $this->conn->query($query);
	}
	
	
	//EMPRESA
	//-----------------------------------------
	
	public function listarEmpresas()
		{
			$query = "SELECT * FROM empresa ORDER by ID ASC";
			$result = $this->conn->query($query);
			
			while ($row = $result->fetch_assoc())
			{
				$empresa = new EmpresaVO();
				
				$empresa->ID = $row['ID'];
				$empresa->RAZAO_SOCIAL = $row['RAZAO_SOCIAL'];
				$empresa->NOME_FANTASIA = $row['NOME_FANTASIA'];
				$empresa->CNPJ = $row['CNPJ'];
				$empresa->INSCRICAO_ESTADUAL = $row['INSCRICAO_ESTADUAL'];
				$empresa->INSCRICAO_MUNICIPAL = $row['INSCRICAO_MUNICIPAL'];
				$empresa->MATRIZ_FILIAL = $row['MATRIZ_FILIAL'];
				$empresa->ENDERECO = $row['ENDERECO'];
				$empresa->COMPLEMENTO = $row['COMPLEMENTO'];
				$empresa->BAIRRO = $row['BAIRRO'];
				$empresa->CIDADE = $row['CIDADE'];
				$empresa->UF = $row['UF'];
				$empresa->CEP = $row['CEP'];
				$empresa->FONE1 = $row['FONE1'];
				$empresa->FONE2 = $row['FONE2'];
				$empresa->DATA_CADASTRO = $row['DATA_CADASTRO'];
				
				$empresas[] = $empresa;
			}
			
			return $empresas;
		}
		public function cadastrarEmpresas(EmpresaVO $_empresa)
		{
			$data = date('Y-m-d');
			
			$query = "INSERT INTO empresa (
			RAZAO_SOCIAL ,NOME_FANTASIA ,CNPJ ,INSCRICAO_ESTADUAL ,INSCRICAO_MUNICIPAL ,MATRIZ_FILIAL ,
			ENDERECO ,COMPLEMENTO ,BAIRRO ,CIDADE ,UF ,CEP ,FONE1 ,FONE2 ,DATA_CADASTRO)
			VALUES (
			'$_empresa->RAZAO_SOCIAL', '$_empresa->NOME_FANTASIA', '$_empresa->CNPJ', '$_empresa->INSCRICAO_ESTADUAL', 
			'$_empresa->INSCRICAO_MUNICIPAL', '$_empresa->MATRIZ_FILIAL', '$_empresa->ENDERECO', '$_empresa->COMPLEMENTO', 
			'$_empresa->BAIRRO', '$_empresa->CIDADE', '$_empresa->UF', '$_empresa->CEP', '$_empresa->FONE1', '$_empresa->FONE2', 
			'$data')";
			
			$result = $this->conn->query($query);
			
		}	
	
	
	
	
	//USUARIO
	//-----------------------------------------
	public function listarUsuarios()
	{
		$query = "SELECT * from usuario order by ID";
		$result = $this->conn->query($query);
		$nRow = $result->num_rows;
		
		if($nRow == 0)
		{
			return 'ERRO';
		}
		
		while ($row = $result->fetch_assoc()) {
			
						
			$usuario = new UsuarioVO();
			//FAZER JOIN AMANHA;;
			$usuario->ID = $row['ID'];
			$usuario->USERNAME = $row['USERNAME'];
			$usuario->PASSWORD_2 = $row['PASSWORD_2'];
			$usuario->FUNCAO = $row['FUNCAO'];
			
			$usuarios[] = $usuario;
			
		}
		
			return $usuarios;
	}
	public function selecionarUsuario($cod)
	{
		$query = "SELECT * from usuario WHERE ID = '$cod'";
		$result = $this->conn->query($query);
		$nRow = $result->num_rows;
		
		if($nRow == 0)
		{
			return 'ERRO';
		}
		
			$row = $result->fetch_assoc();
			
						
			$usuario = new UsuarioVO();
			//FAZER JOIN AMANHA;;
			$usuario->ID = $row['ID'];
			$usuario->USERNAME = $row['USERNAME'];
			$usuario->PASSWORD_2 = $row['PASSWORD_2'];
			$usuario->FUNCAO = $row['FUNCAO'];
			
			
			
		
			return $usuario;
	}
	public function listarEmpresasUsuarios($codUsuario)
	{
		$query = "SELECT * FROM empresas_usuario WHERE USUARIO_ID = '$codUsuario' ORDER by ID ";
		$result = $this->conn->query($query);
		$nRow = $result->num_rows;
		
		if($nRow == 0)
		{
			return 'ERRO';
		}
		
		while ($row = $result->fetch_assoc()) {
			
			$codEmpresa = $row['EMPRESA_ID'];
			
			$queryEmpresa = "SELECT * FROM empresa WHERE ID = '$codEmpresa'";
			$resultEmpresa = $this->conn->query($queryEmpresa);
			$nRowEmpresa = $resultEmpresa->num_rows;
			$rowEmpresa = $resultEmpresa->fetch_assoc();
			
			if($nRowEmpresa == 0)
			{
				return 'ERRO';
			}
			
						
			
			
			$empresa = new EmpresaVO();
				
				$empresa->ID = $rowEmpresa['ID'];
				$empresa->RAZAO_SOCIAL = $rowEmpresa['RAZAO_SOCIAL'];
				$empresa->NOME_FANTASIA = $rowEmpresa['NOME_FANTASIA'];
				$empresa->CNPJ = $rowEmpresa['CNPJ'];
				$empresa->INSCRICAO_ESTADUAL = $rowEmpresa['INSCRICAO_ESTADUAL'];
				$empresa->INSCRICAO_MUNICIPAL = $rowEmpresa['INSCRICAO_MUNICIPAL'];
				$empresa->MATRIZ_FILIAL = $rowEmpresa['MATRIZ_FILIAL'];
				$empresa->ENDERECO = $rowEmpresa['ENDERECO'];
				$empresa->COMPLEMENTO = $rowEmpresa['COMPLEMENTO'];
				$empresa->BAIRRO = $rowEmpresa['BAIRRO'];
				$empresa->CIDADE = $rowEmpresa['CIDADE'];
				$empresa->UF = $rowEmpresa['UF'];
				$empresa->CEP = $rowEmpresa['CEP'];
				$empresa->FONE1 = $rowEmpresa['FONE1'];
				$empresa->FONE2 = $rowEmpresa['FONE2'];
				$empresa->DATA_CADASTRO = $rowEmpresa['DATA_CADASTRO'];
				
				$empresas[] = $empresa;
			
		}
		
			return $empresas;
	}
	public function listarModulosEmpresaXml($codEmpresa, $codUsuario)
	{
		$sql = "SELECT * FROM modulos_empresa WHERE EMPRESA_ID = '$codEmpresa'";
		$query = $this->conn->query($sql);
	
		
		$retorno  = '<root>'."\n";  
		
		
		while($row = $query->fetch_assoc())
		{
			$codModulo = $row['MODULO_ID'];
			
			$queryNomeModulo = "SELECT * FROM modulo WHERE ID = '$codModulo'";
			$resultNomeModulo = $this->conn->query($queryNomeModulo);
			$rowNomeModulo = $resultNomeModulo->fetch_assoc();
			
			$queryPermissoes = "SELECT * FROM modulo_usuario WHERE EMPRESA_ID = '$codEmpresa' AND MODULO_ID = '$codModulo'
								AND USUARIO_ID = '$codUsuario'";
			
			$resultPermissoes = $this->conn->query($queryPermissoes);
			$nRowPermissoes = $resultPermissoes->num_rows;
			if($nRowPermissoes != 0)
			{
				$visivel = 's';
			}else
			{
				$visivel = 'n';
			}
		
			
			
			$retorno  .= '<menuitem label="'.$rowNomeModulo['NOME_MODULO'].'" id="'.$rowNomeModulo['ID'].'"  selected="'.$visivel.'" >'."\n";  
                    	
				//$querySubModulosEmpresa = "SELECT * FROM submodulo_empresa WHERE SUBMODULO_ID ='' AND EMPRESA_ID =1";	
			
				//RECUPERANDO SUB-MODULOS
				$querySub = "SELECT * FROM submodulo WHERE MODULO_ID = '$codModulo' ORDER by ID";
				$resultSub = $this->conn->query($querySub);
				
				while($rowSub = $resultSub->fetch_assoc())
				{
					$codSubModulo = $rowSub['ID'];
					$querySub2 = "SELECT * FROM submodulo_empresa WHERE SUBMODULO_ID = '$codSubModulo' AND EMPRESA_ID = '$codEmpresa'";
					$resultSub2 = $this->conn->query($querySub2);
					$nrowSub = $resultSub2->num_rows;
					$rowSub2 = $resultSub2->fetch_assoc();
					
					if($nrowSub > 0)
					{
						$retorno  .= '<menuitem label="'.$rowSub['NOME'].'" id="'.$rowSub['ID'].'" clickable="true"  />'."\n";  
					}
					
				}
			
    
			$retorno  .= ' </menuitem>'."\n";  
			
	
				
			
			     
		}
		
						
		
		$retorno  .= '</root>';
		
		
	
		
		return $retorno;
		
		
		
	}
	public function listarPermissoesUsuario($codUsuario, $codEmpresa, $codSubModulo)
	{
		
		$query = "SELECT * FROM permissoes WHERE SUBMODULO_ID = '$codSubModulo' AND USUARIO_ID ='$codUsuario' AND EMPRESA_ID ='$codEmpresa'";
		$result = $this->conn->query($query);
		$nRow = $result->num_rows;
		
		if($nRow == 0)
		{
			return 'ERRO';
		}
		
		$row = $result->fetch_assoc();
		
		
		$permissao = new PermissoesVO();
		$permissao->CADASTRAR = $row['CADASTRAR'];
		$permissao->ALTERAR = $row['ALTERAR'];	
		$permissao->EXCLUIR = $row['EXCLUIR'];	
		$permissao->VISUALIZAR = $row['VISUALIZAR']; 
		
		
		
		return $permissao;
	}
	
	
	public function salvarAlterarcoesPermissoes($codUsuario, $codEmpresa, $codSubModulo, PermissoesVO $permissoes)
	{
		$query = "SELECT * FROM permissoes WHERE SUBMODULO_ID = '$codSubModulo' AND USUARIO_ID ='$codUsuario' AND EMPRESA_ID ='$codEmpresa'";
		$result = $this->conn->query($query);
		$nRow = $result->num_rows;
		
		if($nRow == 0)
		{
			//CADASTRAR
			
			$queryCadastro = "INSERT INTO permissoes (
			SUBMODULO_ID ,USUARIO_ID ,EMPRESA_ID ,CADASTRAR ,ALTERAR ,EXCLUIR ,VISUALIZAR
			)VALUES ('$codSubModulo', '$codUsuario', '$codEmpresa', '$permissoes->CADASTRAR', '$permissoes->ALTERAR', '$permissoes->EXCLUIR', '$permissoes->VISUALIZAR')";
			$resultCadastro = $this->conn->query($queryCadastro);
			
		}else
		{
			//ATUALIZAR

			$row = $result->fetch_assoc();
			$codPermissao = $row['ID'];
			$queryAtualizar = "UPDATE permissoes SET CADASTRAR = '$permissoes->CADASTRAR',
			ALTERAR = '$permissoes->ALTERAR', EXCLUIR = '$permissoes->EXCLUIR', 
			VISUALIZAR = '$permissoes->VISUALIZAR' WHERE ID = '$codPermissao'";
			$resultAtualizar = $this->conn->query($queryAtualizar);
		}
	}
	
	
	public function cadastrarUsuario(UsuarioVO $usuario, $codEmpresasArr)
	{
		$passwordMD5 =  md5($usuario->PASSWORD_2);
		//$passwordMD5base64 = base64_encode($passwordMD5);

		//CADASTRA USU�RIO
		$query = "INSERT INTO usuario (
		ID ,USERNAME ,PASSWORD_2 ,FUNCAO)
		VALUES (NULL , '$usuario->USERNAME', '$passwordMD5', '$usuario->FUNCAO')";		
		$result = $this->conn->query($query);
		
		//PEGA COD DO ULTIMO USU�RIO CADASTRADO;
		$queryUltimoUsuario = "SELECT * FROM usuario ORDER BY ID DESC LIMIT 0 , 1";
		$result2 = $this->conn->query($queryUltimoUsuario);
		$row2 = $result2->fetch_assoc();
		$codUsuario = $row2['ID'];

		$ids_get = explode(",", $codEmpresasArr);
			
			
			
			foreach ($ids_get as $id2)
			{
				//ADICIONA USUARIO A EMPRESA LOGADA;
				$queryUsuarioEmpresa = "INSERT INTO empresas_usuario (
				USUARIO_ID ,EMPRESA_ID)VALUES ('$codUsuario', '$id2')";
				$result3 = $this->conn->query($queryUsuarioEmpresa);
			
			}
		
		return $codUsuario;
	}
	
	
	public function cadastrarUsuarioEmpresa($codUsuario, $codEmpresa)
	{
		
		$query0 = "SELECT * FROM empresas_usuario WHERE USUARIO_ID = '$codUsuario' AND EMPRESA_ID = '$codEmpresa'";
		$result0 = $this->conn->query($query0);
		$nRow0 = $result0->num_rows;
		
		if($nRow0 > 0)
		{
			return 'ERRO';
		}
		
		$query = "INSERT INTO empresas_usuario (
			ID ,USUARIO_ID ,EMPRESA_ID)VALUES (NULL , '$codUsuario', '$codEmpresa')	";
		
		$result = $this->conn->query($query);	
	}
	public function permissoesUsuarioModulo($codUsuario, $codEmpresa,$codModulo)
	{
		$query = "SELECT * FROM modulo_usuario WHERE EMPRESA_ID = '$codEmpresa' AND MODULO_ID = '$codModulo' AND USUARIO_ID = '$codUsuario'";
		$result = $this->conn->query($query);
		$nRow = $result->num_rows;
		
		if($nRow == 0)
		{
			$queryInsert = "INSERT INTO modulo_usuario (
			EMPRESA_ID ,MODULO_ID ,USUARIO_ID)VALUES ('$codEmpresa', '$codModulo', '$codUsuario')";
			$resultInsert = $this->conn->query($queryInsert);
			
			return 'CADASTRADO PERMISSAO';
		}else
		{
			$queryDelete = "DELETE FROM modulo_usuario WHERE EMPRESA_ID = '$codEmpresa' AND MODULO_ID = '$codModulo' AND USUARIO_ID = '$codUsuario' ";
			$resultDelete = $this->conn->query($queryDelete);
			
			return 'REMOVIDO PERMISSAO';
		}
	}
	public function excluirUsuario($codUsuario)
	{
		//EXCLUIR USU�RIO
		$query1 = "DELETE FROM usuario WHERE ID='$codUsuario'";
		$result1 = $this->conn->query($query1);
		
		//EXCLUIR M�DULOS USUARIO
		$query2 = "DELETE FROM modulo_usuario WHERE USUARIO_ID='$codUsuario'";
		$result2 = $this->conn->query($query2);
		
		//EXCLUIR PERMISS�ES SUBMODULO
		$query3 = "DELETE FROM permissoes WHERE USUARIO_ID='$codUsuario'";
		$result3 = $this->conn->query($query3);
		
		//EXCLUIR EMPRESA USUARIOS
		$query4 = "DELETE FROM empresas_usuario WHERE USUARIO_ID='$codUsuario'";
		$result4 = $this->conn->query($query4);
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	public function listarTiposDeServicos()
	{
		$query = "SELECT * FROM configtiposdeservicos ORDER by nome ASC";
		$result = $this->conn->query($query);
		
		while ($row = $result->fetch_assoc())
		{
			$tipoProblema = new ConfiguracoesVO();
			
			$tipoProblema->id = $row['id'];
			$tipoProblema->nome_servico = $row['nome'];
			
			
			$tipoProblemas[] = $tipoProblema;
		}
		
		

		return $tipoProblemas;
	}
	
	
	//------------------------------Metodos para Listar Chamados-------------------------------------------//
	public function listarTiposDeProblema()
	{
		$query = "SELECT * FROM configtiposdeproblemas ORDER by desc_TipoProblema ASC";
		$result = $this->conn->query($query);
		
		while ($row = $result->fetch_assoc())
		{
			$tipoProblema = new ConfiguracoesVO();
			
			$tipoProblema->id = $row['id'];
			$tipoProblema->desc_TipoProblema = $row['desc_TipoProblema'];
			
			
			$tipoProblemas[] = $tipoProblema;
		}
		
		

		return $tipoProblemas;
		
		
			
	}
	public function listarTiposDeCancelamentos()
	{
		$query = "SELECT * FROM configtiposdecancelamentos ORDER by desc_TipoCancelameto ASC";
		$result = $this->conn->query($query);
		
		while ($row = $result->fetch_assoc())
		{
			$tipoCancelamento = new ConfiguracoesVO();
			
			$tipoCancelamento->id = $row['id'];
			$tipoCancelamento->desc_TipoCancelameto = $row['desc_TipoCancelameto'];
			
			
			$tipoCancelamentos[] = $tipoCancelamento;
		}
		
		

		return $tipoCancelamentos;
		
		
			
	}
	public function listarConfPontosChamados()
	{
		$query = "SELECT * FROM configpontoschamados";
		$result = $this->conn->query($query);
		
		while ($row = $result->fetch_assoc())
		{
			$confgPontoChamado = new ConfiguracoesVO();
			
			$confgPontoChamado->id = $row['id'];
			$confgPontoChamado->tipo_chamado = $row['tipo_chamado'];
			$confgPontoChamado->ponto_chamado = $row['ponto_chamado'];
			$confgPontoChamado->meta = $row['meta'];
			$confgPontoChamado->gerarBoleto = $row['gerarBoleto'];
			$confgPontoChamado->valorTitulo = $row['valorTitulo'];
			
			
			$confgPontoChamados[] = $confgPontoChamado;
		}
		
		

		return $confgPontoChamados;
		
		
			
	}
	//-----------------------------------Metodos de Cadastro---------------------------------------------------//
	public function CadastrarTiposDeProblema($descricao)
	{
		//Implementar Busca por Nomes Iguais.
		
		$query = "INSERT INTO configtiposdeproblemas (id ,desc_TipoProblema)VALUES (NULL , '$descricao')";
		$result = $this->conn->query($query);
	}
	public function CadastrarTiposDeCancelamento($descricao)
	{
		//Implementar Busca por Nomes Iguais.
		
		$query = "INSERT INTO configtiposdecancelamentos (id ,desc_TipoCancelameto)VALUES (NULL , '$descricao')";
		$result = $this->conn->query($query);
	}
	public function CadastrarConfigPontosChamados($tipoChamado, $pontoChamado, $metaChamado, $gerarBoleto, $valorTitulo)
	{
		//Implementar Busca por Nomes Iguais.
		
		$query = "INSERT INTO configpontoschamados (id ,tipo_chamado,ponto_chamado, meta, gerarBoleto, valorTitulo)VALUES (NULL , '$tipoChamado', '$pontoChamado', '$metaChamado', '$gerarBoleto', '$valorTitulo')";
		$result = $this->conn->query($query);
	}
	//-------------------------------Metodos de Exclusao-----------------------------------------------------//
	public function ExcluirTiposDeProblema($id)
	{
		$query = "DELETE FROM configtiposdeproblemas WHERE id = '$id'";
		$this->conn->query($query);
	}
	public function ExcluirTiposDeCancelamento($id)
	{
		$query = "DELETE FROM configtiposdecancelamentos WHERE id = '$id'";
		$this->conn->query($query);
	}
	public function ExcluirConfigPontosChamados($id)
	{
		$query = "DELETE FROM configpontoschamados WHERE id = '$id'";
		$this->conn->query($query);
	}
	

		public function listarTipoProOSI()
		{
			$query = "SELECT * FROM confg_tipo_problema_osi";
			$result = $this->conn->query($query);
			$nrow = $result->num_rows;
			
			if($nrow > 0)
			{
				while($row = $result->fetch_assoc())
				{
					$tipo = new tipoProblema_OSI();
					
					$tipo->id = $row['id'];
					$tipo->problema = $row['problema'];
					$tipo->valor = $row['valor'];
					
					$tipos[] = $tipo;
				}
				
				return $tipos;
			}
			
		}
		public function cadastrarTipoProOSI(tipoProblema_OSI $tipo)
		{
				$query = "INSERT INTO confg_tipo_problema_osi (problema ,valor)VALUES ('$tipo->problema', '$tipo->valor')";
				$result = $this->conn->query($query);
				
				return $result;
		}
		public function editarTipoProOSI(tipoProblema_OSI $tipo)
		{
				$query = "UPDATE confg_tipo_problema_osi SET problema = '$tipo->problema',valor = '$tipo->valor' WHERE id ='$tipo->id'";
				$result = $this->conn->query($query);
				
				return $result;
		}
		public function excluirTipoProOSI($id)
		{
				$query = "DELETE FROM confg_tipo_problema_osi WHERE id = '$id'";
				$result = $this->conn->query($query);
				
				return $result;
		}
	
		
		//IMPRESSORAS
		public function listarImpressoras($id)
		{
			$query = "SELECT * FROM impressoras WHERE EMPRESA_ID = '$id'";
			$result = $this->conn->query($query);
			$nRow = $result->num_rows;
			if($nRow == 0)
			{
				return 'ERRO';
			}
			
			while ($row = $result->fetch_assoc())
			{
				$impressora = new impressorasVO();
				
				$impressora->ID = $row['ID'];
				$impressora->EMPRESA_ID = $row['EMPRESA_ID'];
				$impressora->NOME = $row['NOME'];
				$impressora->END_IMPRESSORA = $row['END_IMPRESSORA'];
				$impressora->DEFAULT_2 = $row['DEFAULT_2'];
				
				$impressoras[] = $impressora;
				
			}
			
			return $impressoras;
		}
		public function cadastrarImpressora(impressorasVO $impressora)
		{
			if($impressora->DEFAULT_2 == 'true')
			{
				//VERIFICAR ALGUMA IMPRESSORA PADR�O
				$queryVerificar = "SELECT * FROM impressoras WHERE DEFAULT_2 LIKE '%true%'";
				$resultVerificar = $this->conn->query($queryVerificar);
				$nRowVerificar = $resultVerificar->num_rows;
				if($nRowVerificar > 0)
				{
					$rowVerifica = $resultVerificar->fetch_assoc();
					$ideVerifica = $rowVerifica['ID'];
					//ALTERAR DEFAULT
					$queryAlteraDefault = "UPDATE impressoras SET DEFAULT_2='false' WHERE ID='$ideVerifica'";
					$resultAlteraDefault = $this->conn->query($queryAlteraDefault);
				}
			}
			
			$query = "INSERT INTO impressoras (EMPRESA_ID, NOME, END_IMPRESSORA, DEFAULT_2) VALUES 
			('$impressora->EMPRESA_ID', '$impressora->NOME', '$impressora->END_IMPRESSORA','$impressora->DEFAULT_2')";
			if(!$result = $this->conn->query($query))
			{
				return $this->conn->error;
			}else{
				return 'ok';
			}
		}
		public function editarImpressora(impressorasVO $impressora)
		{
			if($impressora->DEFAULT_2 == 'true')
			{
				//VERIFICAR ALGUMA IMPRESSORA PADR�O
				$queryVerificar = "SELECT * FROM impressoras WHERE DEFAULT_2 LIKE '%true%'";
				$resultVerificar = $this->conn->query($queryVerificar);
				$nRowVerificar = $resultVerificar->num_rows;
				if($nRowVerificar > 0)
				{
					$rowVerifica = $resultVerificar->fetch_assoc();
					$ideVerifica = $rowVerifica['ID'];
					//ALTERAR DEFAULT
					$queryAlteraDefault = "UPDATE impressoras SET DEFAULT_2='false' WHERE ID='$ideVerifica'";
					$resultAlteraDefault = $this->conn->query($queryAlteraDefault);
				}
			}
			
			$query = "UPDATE impressoras SET NOME='$impressora->NOME', END_IMPRESSORA='$impressora->END_IMPRESSORA', DEFAULT_2='$impressora->DEFAULT_2' 
			WHERE ID='$impressora->ID'";
			if(!$result = $this->conn->query($query))
			{
				return $this->conn->error;
			}else{
				return 'ok';
			}
		}
		public function excluirImpressora($id)
		{
			$query = "DELETE FROM impressoras WHERE ID='$id'";
			if(!$result = $this->conn->query($query))
			{
				return $this->conn->error;
			}else{
				return 'ok';
			}		
		}
	
}