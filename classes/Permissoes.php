<?php

require_once 'classes/Conexao.php';
require_once 'vo/PermissoesVO.php';

class Permissoes extends Conexao
{
	public function checarVisualizar($codUsuario, $codSubModulo, $codEmpresa)
	{
		$query = "SELECT * FROM permissoes WHERE SUBMODULO_ID = '$codSubModulo' AND USUARIO_ID = '$codUsuario' AND EMPRESA_ID = '$codEmpresa' ";
		$result = $this->conn->query($query);
		$nRow = $result->num_rows;
		
		if($nRow == 0)
		{
			return 'n';
		}
		
		$row = $result->fetch_assoc();
		
		return $row['VISUALIZAR'];
		
	}
	public function checarPermissoesSub($codUsuario, $codEmpresa, $codSubModulo)
	{
		$query = "SELECT * FROM permissoes WHERE SUBMODULO_ID = '$codSubModulo' AND	USUARIO_ID = '$codUsuario' AND EMPRESA_ID = '$codEmpresa'";
		$result = $this->conn->query($query);
		$nRow = $result->num_rows;
		if($nRow == 0){return 'ERRO';}
		$row = $result->fetch_assoc();
		
			$permissao = new PermissoesVO();
			
			$permissao->CADASTRAR = $row['CADASTRAR'];
			$permissao->ALTERAR = $row['ALTERAR'];
			$permissao->EXCLUIR = $row['EXCLUIR'];
			$permissao->VISUALIZAR = $row['VISUALIZAR'];
			
			return $permissao;
		
	}
	
}