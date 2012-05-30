<?php

require_once 'classes/Conexao.php';

require_once 'vo/LoginVO.php';
require_once 'vo/UsuarioVO.php';

class  Login extends Conexao
{
	/**
	 * Função Utilizada Para Fazer Login no Sistema
	 * @author Marconi César
	 * @name Logar
	 */
	public function Logar(UsuarioVO $usuario)
	{
		$passwordMD5 =  md5($usuario->PASSWORD_2);
		$query = "SELECT * 
			FROM usuario
			WHERE USERNAME LIKE '$usuario->USERNAME'
			
			LIMIT 0 , 1";
		
		$result = $this->conn->query($query);
		
		if($result->num_rows > 0)
		{
			$row = $result->fetch_assoc();
			$usuario = new UsuarioVO();
			$usuario->ID = $row['ID'];
			$usuario->USERNAME = $row['USERNAME'];
			$usuario->PASSWORD_2 = $row['PASSWORD_2'];
			$usuario->FUNCAO = $row['FUNCAO'];
			
			return $usuario;
		}else
		{
			return 'ERRO';
		}
	}
	
	/**
	 * Função Retorna Lista de Empresas do Usuário Informado
	 * @author Marconi César
	 * @name EmpresaUsuario
	 * @param codUSuario
	 */
	public function EmpresaUsuario($codUSuario)
	{
		$query = "SELECT eu.*, e.* FROM empresas_usuario as eu, empresa as e
				WHERE eu.USUARIO_ID ='$codUSuario' AND e.ID = eu.EMPRESA_ID";
		$result = $this->conn->query($query);
		
		
		while ($row = $result->fetch_assoc())
		{
						
			$empresa = new empresaVO();
			$empresa->ID = $row['ID'];
			$empresa->RAZAO_SOCIAL= $row['RAZAO_SOCIAL'];
			$empresa->NOME_FANTASIA= $row['NOME_FANTASIA'];
			$empresa->CNPJ= $row['CNPJ'];
			$empresa->INSCRICAO_ESTADUAL= $row['INSCRICAO_ESTADUAL'];
			$empresa->INSCRICAO_MUNICIPAL= $row['INSCRICAO_MUNICIPAL'];
			$empresa->MATRIZ_FILIAL= $row['MATRIZ_FILIAL'];
			$empresa->ENDERECO= $row['ENDERECO'];
			$empresa->COMPLEMENTO= $row['COMPLEMENTO'];
			$empresa->BAIRRO= $row['BAIRRO'];
			$empresa->CIDADE= $row['CIDADE'];
			$empresa->UF= $row['UF'];
			$empresa->CEP= $row['CEP'];
			$empresa->FONE1= $row['FONE1'];
			$empresa->FONE2= $row['FONE2'];
			$empresa->DATA_CADASTRO= $row['DATA_CADASTRO'];		
			
			$empresas[] = $empresa;
		}		
		
		return $empresas;
	}
	
	
	/**
	 * Função Altera A Senha do Usuário Informado
	 * @author Marconi César
	 * @name AlterarSenha
	 */
	public function AlterarSenha($codUser, $senha)
	{
		$passwordMD5 =  md5($senha);
		$this->conn->autocommit(false);
		
		
		$query = "UPDATE usuario SET PASSWORD_2='$passwordMD5' WHERE ID='$codUser'";
		$result = $this->conn->query($query);
		
		if($result->error)
		{
			$this->conn->rollback();
			$this->conn->autocommit(true);
		}else{
			$this->conn->commit();
			$this->conn->autocommit(true);
		}
		
	}
		
}

