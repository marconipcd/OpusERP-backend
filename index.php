<?php

require_once 'Zend/Amf/Server.php';

//MODULOS
require_once 'classes/Acesso.php';
require_once 'classes/Cadastros.php';
require_once 'classes/ClaroTv.php';
require_once 'classes/Configuracoes.php';
require_once 'classes/Estoque.php';
require_once 'classes/Financeiro.php';
require_once 'classes/Login.php';
require_once 'classes/LogOpus.php';
require_once 'classes/Menu.php';
require_once 'classes/Permissoes.php';
require_once 'classes/Relatorios.php';
require_once 'classes/Suporte.php';
require_once 'classes/Rotinas.php';
require_once 'classes/Bug.php';
require_once 'classes/Pedido.php';

//Objetos VO
require_once 'vo/ClientesVO.php';
require_once 'vo/EndPrincipaisVO.php';
require_once 'vo/EndEntregaVO.php';
require_once 'vo/EndCobrancaVO.php';
require_once 'vo/FiadorVO.php';
require_once 'vo/DocClienteVO.php';
require_once 'vo/ContratosAcessoVO.php';
require_once 'vo/CepsVO.php';
require_once 'vo/osiVO.php';
require_once 'vo/oseVO.php';
require_once 'vo/tipoProblema_OSI.php';
require_once 'vo/motivoCancelamentoVO.php';
require_once 'vo/tipoProblema_OSE.php';
require_once 'vo/tipoOSe_VO.php';
require_once 'vo/ProdutosVO.php';
require_once 'vo/ContasReceberVO.php';
require_once 'vo/FormasPgtoVO.php';
require_once 'vo/TipoServicoOSeVO.php';
require_once 'vo/contasBancariasVO.php';
require_once 'vo/InterfacesVO.php';
require_once 'vo/ServidoresVO.php';
require_once 'vo/GraficoClientesVO.php';
require_once 'vo/MenuFacilVO.php';
require_once 'vo/ProdutosVO.php';
require_once 'vo/UnidadeProdutoVO.php';
require_once 'vo/GrupoProdutoVO.php';
require_once 'vo/FornecedoresVO.php';
require_once 'vo/EcfVendaCabecalhoVO.php';
require_once 'vo/EcfVendaDetalheVO.php';
require_once 'vo/Assinaturas_CtvVO.php';
require_once 'vo/RadacctVO.php';
require_once 'vo/MovimentoEntCabecalhoVO.php';
require_once 'vo/MovimentoEntDetalheVO.php';
require_once 'vo/BugVO.php';
require_once 'vo/DavDetalheVO.php';

//BASE
require_once 'vo/UsuarioVO.php';
require_once 'vo/ModuloVO.php';
require_once 'vo/SubModuloVO.php';
require_once 'vo/PermissoesVO.php';
require_once 'vo/LoginVO.php';
require_once 'vo/CategoriaVO.php';
require_once 'vo/BaseVO.php';
require_once 'vo/TransportadorasVO.php';
require_once 'vo/AcessoClienteVO.php';
require_once 'vo/PlanosAcessoVO.php';
require_once 'vo/ServidoresRadiusVO.php';
require_once 'vo/impressorasVO.php';



$amf = new Zend_Amf_Server();
session_start();

	
$amf->setClass('Configuracoes')
	->setClass('Permissoes')
	->setClass('Menu')
	->setClass('Login')
	->setClass('Cadastros')
	->setClass('Suporte')
	->setClass('Financeiro')
	->setClass('Acesso')	
	->setClass('Relatorios')
	->setClass('Estoque')
	->setClass('ClaroTv')
	->setClass('Rotinas')
	->setClass('LogOpus')
        ->setClass('Bug')
        ->setClass('Pedido')
        ->setClassMap('DavDetalheVO', 'DavDetalheVO')	
	->setClassMap('BugVO', 'BugVO')	
	->setClassMap('MovimentoEntDetalheVO', 'MovimentoEntDetalheVO')	
	->setClassMap('MovimentoEntCabecalhoVO', 'MovimentoEntCabecalhoVO')	
	->setClassMap('RadacctVO', 'RadacctVO')	
	->setClassMap('Assinaturas_CtvVO', 'Assinaturas_CtvVO')	
	->setClassMap('EcfVendaCabecalhoVO', 'EcfVendaCabecalhoVO')	
	->setClassMap('EcfVendaDetalheVO', 'EcfVendaDetalheVO')	
	->setClassMap('FornecedoresVO', 'FornecedoresVO')	
	->setClassMap('GrupoProdutoVO', 'GrupoProdutoVO')	
	->setClassMap('impressorasVO', 'impressorasVO')	
	->setClassMap('GraficoClientesVO', 'GraficoClientesVO')
	->setClassMap('ClientesVO', 'ClientesVO')
	->setClassMap('ContratosAcessoVO', 'ContratosAcessoVO')
	->setClassMap('DocClienteVO', 'DocClienteVO')
	->setClassMap('EndPrincipaisVO', 'EndPrincipaisVO')
	->setClassMap('EndCobrancaVO', 'EndCobrancaVO')
	->setClassMap('EndEntregaVO', 'EndEntregaVO')
	->setClassMap('PermissoesVO', 'PermissoesVO')
	->setClassMap('SubModuloVO', 'SubModuloVO')
	->setClassMap('ModuloVO', 'ModuloVO')
	->setClassMap('UsuarioVO', 'UsuarioVO')
	->setClassMap('FiadorVO', 'FiadorVO')
	->setClassMap('LoginVO', 'LoginVO')
	->setClassMap('CategoriaVO', 'CategoriaVO')
	->setClassMap('CepsVO', 'CepsVO')
	->setClassMap('osiVO', 'osiVO')
	->setClassMap('oseVO', 'oseVO')
	->setClassMap('tipoProblema_OSI', 'tipoProblema_OSI')
	->setClassMap('motivoCancelamentoVO', 'motivoCancelamentoVO')
	->setClassMap('tipoProblema_OSE', 'tipoProblema_OSE')
	->setClassMap('tipoOSe_VO', 'tipoOSe_VO')
	->setClassMap('BaseVO', 'BaseVO')
	->setClassMap('ProdutosVO', 'ProdutosVO')
	->setClassMap('UnidadeProdutoVO', 'UnidadeProdutoVO')
	->setClassMap('ContasReceberVO', 'ContasReceberVO')
	->setClassMap('FormasPgtoVO', 'FormasPgtoVO')
	->setClassMap('TipoServicoOSeVO', 'TipoServicoOSeVO')
	->setClassMap('contasBancariasVO', 'contasBancariasVO')
	->setClassMap('TransportadorasVO', 'TransportadorasVO')
	->setClassMap('InterfacesVO', 'InterfacesVO')
	->setClassMap('ServidoresVO', 'ServidoresVO')
	->setClassMap('AcessoClienteVO', 'AcessoClienteVO')
	->setClassMap('PlanosAcessoVO', 'PlanosAcessoVO')
	->setClassMap('ServidoresRadiusVO', 'ServidoresRadiusVO');
	



echo $amf->handle();