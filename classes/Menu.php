<?php

require_once 'classes/Conexao.php';
require_once 'vo/MenuFacilVO.php';
require_once 'vo/SubModuloVO.php';

class Menu extends Conexao
{
	public function montarMenu($codUsuario, $codEmpresa, $funcao)
	{
		if($funcao == 'admin')
		{
			$sql = "SELECT modulos_empresa.*, modulo.* FROM modulos_empresa LEFT JOIN modulo ON  modulo.ID = modulos_empresa.MODULO_ID
					WHERE  modulos_empresa.EMPRESA_ID ='$codEmpresa' ORDER by modulo.ORDEM ASC";
			$query = $this->conn->query($sql);
			
		}else
		{
			//$sql = "SELECT * FROM modulo_usuario WHERE USUARIO_ID = '$codUsuario' AND EMPRESA_ID = '$codEmpresa' ORDER by ID";
			$sql = "SELECT modulo_usuario.*, modulo.* FROM modulo_usuario LEFT JOIN modulo ON  modulo.ID = modulo_usuario.MODULO_ID
					WHERE  modulo_usuario.USUARIO_ID ='$codUsuario'AND EMPRESA_ID='$codEmpresa' ORDER by modulo.ORDEM ASC";
			$query = $this->conn->query($sql);
		}
	
		
		$retorno  = '<root>'."\n";  
		$retorno  .= '<menuitem label="Inicio" />'."\n";
		
		while($row = $query->fetch_assoc())
		{
			$codModulo = $row['MODULO_ID'];
									
			$retorno  .= '<menuitem label="'.$row['NOME_MODULO'].'" >'."\n";  
                    	
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
						$retorno  .= '<menuitem label="'.$rowSub['NOME'].'" id="'.$rowSub['ID'].'" />'."\n";  
					}
					
				}
			
    
			$retorno  .= ' </menuitem>'."\n";  
			
	
				
			
			     
		}
		
				//ADICIONA MENU DE CONFIGURA��ES COM FUN��O ADMINISTRATIVA
				if($funcao == 'admin')
				{
					$retorno  .= '<menuitem label="Configurações" >'."\n";
//					$retorno  .= '<menuitem label="M�dulos" />'."\n";
//					$retorno  .= '<menuitem label="Empresas" />'."\n";
					$retorno  .= '<menuitem label="Usuários" />'."\n";
					$retorno  .= '<menuitem label="Impressoras" />'."\n";
					$retorno  .= '<menuitem label="Logs" />'."\n";   
					$retorno  .= '</menuitem>'."\n";
					$retorno  .= '<menuitem label="Bugs" />'."\n";
				}
		
		
		$retorno  .= '</root>';
		
		
	
		
		return $retorno;
		
	}
	
	public function montarMenuFacil($codUsuario, $codEmpresa, $funcao)
	{
		if($funcao == 'admin')
		{
			$sql = "SELECT modulo.*, modulos_empresa.* FROM modulo 
				LEFT JOIN modulos_empresa ON modulos_empresa.MODULO_ID = modulo.ID
				WHERE  modulos_empresa.EMPRESA_ID ='$codEmpresa' ORDER by modulo.ORDEM ASC";
			$query = $this->conn->query($sql);
			
		}else
		{
			$sql = "SELECT modulo.*, modulo_usuario.* FROM modulo 
				LEFT JOIN modulo_usuario ON modulo_usuario.MODULO_ID = modulo.ID
				WHERE  modulo_usuario.EMPRESA_ID =$codEmpresa AND modulo_usuario.USUARIO_ID = '$codUsuario' ORDER by modulo.ORDEM ASC";
			
			//$sql = "SELECT * FROM modulo_usuario WHERE USUARIO_ID = '$codUsuario' AND EMPRESA_ID = '$codEmpresa' ORDER by ID";
			$query = $this->conn->query($sql);
		}
	
		
		
		
		while($row = $query->fetch_assoc())
		{
			$menuFacil = new MenuFacilVO();
			
			$menuFacil->ID_MODULO = $row['MODULO_ID'];
			$menuFacil->NOME_MODULO = $row['NOME_MODULO'];
			$menuFacil->ICONE_MODULO =  $row['ICONE_MODULO'];
			
			
			$menuFacils[] = $menuFacil;
		}
		
		return $menuFacils;
		
	}
	public function listarSubModuloPorModulo($idModulo, $idEmpresa)
	{
		
			$sql = "SELECT submodulo.*, submodulo_empresa.* FROM submodulo 
				LEFT JOIN submodulo_empresa ON submodulo_empresa.SUBMODULO_ID = submodulo.ID
				WHERE  submodulo_empresa.EMPRESA_ID ='$idEmpresa' AND submodulo.MODULO_ID = '$idModulo'";
			
			//$sql = "SELECT * FROM modulo_usuario WHERE USUARIO_ID = '$codUsuario' AND EMPRESA_ID = '$codEmpresa' ORDER by ID";
			$query = $this->conn->query($sql);
		
	
		
		
		
		while($row = $query->fetch_assoc())
		{
			$subModulo = new SubModuloVO();
			
			$subModulo->ID = $row['ID'];	
			$subModulo->MODULO_ID = $row['MODULO_ID']; 	
			$subModulo->NOME = $row['NOME'];
			$subModulo->DESCRICAO = $row['DESCRICAO'];

			$subModulos[] = $subModulo;
		}
		
		return $subModulos;
	}
	
}