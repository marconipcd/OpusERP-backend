<?php

ini_set('max_execution_time','2000000000000');

require_once 'classes/Conexao.php';
require_once 'classes/routeros_api.php';


require_once 'vo/AcessoClienteVO.php';
require_once 'vo/HistoricoAcessoVO.php';
require_once 'vo/ConsumoBandaVO.php';
require_once 'vo/ClientesVO.php';
require_once 'vo/InterfacesVO.php';
require_once 'vo/ContratosAcessoVO.php';
require_once 'vo/PlanosAcessoVO.php';
require_once 'vo/ServidoresRadiusVO.php';
require_once 'vo/RadacctVO.php';

class Acesso extends Conexao
{
	public function listarAcesso($codEmpresa)
	{
		
		$query = "SELECT acesso_cliente. * , clientes.NOME_RAZAO
			FROM acesso_cliente LEFT JOIN clientes ON clientes.ID = acesso_cliente.CLIENTES_ID WHERE acesso_cliente.EMPRESA_ID = '$codEmpresa'
			ORDER by acesso_cliente.ID DESC";
		$result = $this->conn->query($query);
		$nRow = $result->num_rows;
		if($nRow == 0){return 'ERRO';}
		
				
		while ($row = $result->fetch_assoc()) {
    		
						
			
			$acesso = new AcessoVO();
							
			$acesso->ID = $row['ID']; 
			$acesso->EMPRESA_ID = $row['EMPRESA_ID'];
			$acesso->CLIENTES_ID = $row['CLIENTES_ID'];
			$acesso->NOME_RAZAO = $row['NOME_RAZAO'];
			$acesso->LOGIN = $row['LOGIN'];
			$acesso->SENHA = $row['SENHA'];
			$acesso->ENDERECO_IP = $row['ENDERECO_IP'];
			$acesso->ENDERECO_MAC = $row['ENDERECO_MAC'];
			$acesso->PLANO_ACESSO = $row['PLANO_ACESSO'];
			$acesso->BASE = $row['BASE'];
			$acesso->CONTRATO = $row['CONTRATO'];
			$acesso->REGIME = $row['REGIME'];
			$acesso->MATERIAL_ACESSO = $row['MATERIAL_ACESSO'];
						
			$acessos[] = $acesso;
		}
		
						
		return $acessos;
	}
	public function listarClientesSemAcesso($codEmpresa)
	{
		$query = "SELECT * FROM clientes WHERE STATUS_2 = 'ATIVO' AND EMPRESA_ID = '$codEmpresa' ORDER by NOME_RAZAO ASC";
		$result = $this->conn->query($query);
		
		
		
		while ($row = $result->fetch_assoc()) {

			$codCliente = $row['ID'];
			//VERIFICAR SE TEM ACESSO
			$queryAcesso = "SELECT * FROM acesso_cliente WHERE CLIENTES_ID = '$codCliente'";
			$resultAcesso = $this->conn->query($queryAcesso);
			$nRow = $resultAcesso->num_rows;
		if($nRow == 0){
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
				
				$clientes[] = $cliente;
			}else
			{
				
			}
				
				
		}
		
		
			return $clientes;
		
		
	}
	public function cadastrarAcesso(AcessoVO $acesso)
	{
		//PEGA VALOR DO CONTRATO DE ADES�O SE HOUVER	
		$query_adesao = "SELECT * FROM  contratos WHERE  NOME LIKE  '$acesso->CONTRATO'";
		$result_adesao = $this->conn->query($query_adesao);
		$row_adesao = $result_adesao->fetch_assoc();
		
		$valor_titulo = $row_adesao['VARLOR_TITULO'];
			
			
			if($valor_titulo != '0,00')
			{
				$gerarAdesao = true;
			}else
			{
				$gerarAdesao = false;
			}
				
							
		$codigoCliente = $acesso->CLIENTES_ID;
							
		
		
		$primeiroBoleto = SomarData($acesso->VENCIMENTO_CONTRATO, 0, 0, 0);	
		$vencContrato = SomarData($acesso->VENCIMENTO_CONTRATO, 0, 11, 0);
				
		//CADASTRANDO TABELAS DO RADIUS
			  			  
		$ipMK = $acesso->ENDERECO_IP;

		$ipMK1 = substr($ipMK , 0, -1);
		$ipMK2 = substr($ipMK , -1);
		$ipMK3 = $ipMK2 + 1;
	
		$ip2 = $ipMK1.$ipMK3;
						
	
		//CADASTRANDO IP, MAC, LOGIN E SENHA NO RADIUS
		$queryRadCheck = "INSERT INTO radcheck (id , username, attribute , op, value) 
						VALUES ('', '$acesso->LOGIN' , 'Password' , '==' , '$acesso->SENHA'),
						('', '$acesso->LOGIN' , 'Calling-Station-ID' , '==' , '$acesso->ENDERECO_MAC'),
						('', '$acesso->LOGIN' , 'Framed-IP-Address' , '==' , '$ip2')";
		$resultRadCheck = $this->conn->query($queryRadCheck);	
		
		//CADASTRANDO PLANO DE ACESSO NO RADIUS
		$queryRadreply = "INSERT INTO radreply (id , username, attribute , op, value) VALUES 
						('', '$acesso->LOGIN' , 'Mikrotik-Group' , '==' , '$acesso->PLANO_ACESSO')";
		$resultRadreply = $this->conn->query($queryRadreply);
			   
	    //CADASTRANDO ACESSO
	    $queryAcesso = "INSERT INTO acesso_cliente (
			EMPRESA_ID ,CLIENTES_ID ,LOGIN ,SENHA ,ENDERECO_IP ,ENDERECO_MAC ,PLANO_ACESSO ,
			BASE ,CONTRATO ,REGIME ,MATERIAL_ACESSO)VALUES (
			'$acesso->EMPRESA_ID',  '$acesso->CLIENTES_ID',  '$acesso->LOGIN',  '$acesso->SENHA',  
			'$acesso->ENDERECO_IP',  '$acesso->ENDERECO_MAC',  '$acesso->PLANO_ACESSO',  '$acesso->BASE',  '$acesso->CONTRATO',  
			'$acesso->REGIME',  '$acesso->MATERIAL_ACESSO')";
	    $resultAcesso = $this->conn->query($queryAcesso);
          
	
		//GERAR BOLETOS
	    			
			
		$quantidade = '12';
	
   		$_prazo = 0;
   		$codigoCliente = $acesso->CLIENTES_ID;
   		
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

					$ndocumento = $acesso->CLIENTES_ID.'/'.$ano.'/'.$sequencia2; 
       
					
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
           							
					
					
					$_dia = substr($primeiroBoleto, 0, 2);
   					$_mes = substr($primeiroBoleto, 3, 2);
   					$_ano  = substr($primeiroBoleto, 6, 4);
			
	   				$_ts = mktime(0,0,0,$_mes+$_prazo,$_dia,$_ano);
       				$_data = date('Y-m-d',$_ts);
	   					  				
					$emissao = date('Y-m-d');
					$controle = $i+1;
					
					//Consultar Valor do Plano
					$sqlPlanoEscolhido = "SELECT * FROM  planos_acesso WHERE NOME LIKE '$acesso->PLANO_ACESSO' ";
					$resultadoPlano = $this->conn->query($sqlPlanoEscolhido);
					$row_planoEscolhido = $resultadoPlano->fetch_assoc();
				
					//Valor do Plano Escolhido
					$valorPlano = $row_planoEscolhido['VALOR'];
								
					//$valorPlano = number_format($valorPlano, 2, ",", ".");
					
					//Inserindo				
					$sql = "INSERT INTO contas_receber (N_DOC,CLIENTES_ID,DATA_VENCIMENTO,VALOR_TITULO, STATUS_2, DATA_EMISSAO, N_NUMERO, 
					CONTROLE, EMPRESA_ID) VALUES
					('$ndocumento', '$codigoCliente', '$_data','$valorPlano', 'ABERTO', '$emissao','$NumeroNovo', '$ndocumento', '$acesso->EMPRESA_ID')";
					 
					$resultadoSql = $this->conn->query($sql);
        			
        								
        			$_prazo += 1;
        			
    			
    			}
				
					//Gera ou N�o Adesao
    				if($gerarAdesao == true)
    				{
    					
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
           			
    					
    					$ndocumento = $acesso->CLIENTES_ID;
    					$_data = date('Y-m-d');
    					$valorPlano = $valor_titulo;
    					
    					$sql_gerarTituloAdesao = "INSERT INTO contas_receber (N_DOC,CLIENTES_ID,DATA_VENCIMENTO,VALOR_TITULO, STATUS_2, DATA_EMISSAO, N_NUMERO, 
					CONTROLE, EMPRESA_ID) VALUES
					('$ndocumento', '$codigoCliente', '$_data','$valorPlano', 'ABERTO', '$emissao','$NumeroNovo', '$ndocumento', '$acesso->EMPRESA_ID')";
    					
						$resultadoSql_adesao = $this->conn->query($sql_gerarTituloAdesao);
    				}
				
				
				
		//MIKROTIK
				
		$mascara = '/30';
		$ipCliente = $acesso->ENDERECO_IP.$mascara;
				
		$API = new routeros_api();

		$API->debug = true;

		if ($API->connect('192.168.20.1', 'suporte', 'suportedigi123')) 
		{

		 $API->write('/ip/address/add',false);
		 $API->write('=address='.$ipCliente,false);
		 $API->write('=interface=RADIO',false);
		 $API->write('=comment='.$acesso->NOME_RAZAO);

	 	$ARRAY = $API->read();

   				

 		$API->disconnect();
			  	
		}
	
	
	
		
	}
	public function listarInterfaces($codEmpresa)
	{
		$queryServidorPadrao = "SELECT * FROM  servidores WHERE  EMPRESA_ID = '$codEmpresa' AND  DEFAULT_2 = 1";
		$resultServidorPadrao = $this->conn->query($queryServidorPadrao);
		$nRowServidorPadrao = $resultServidorPadrao->num_rows;
		if($nRowServidorPadrao == 0){return 'ERRO PADRAO';}
		$rowServidorPadrao = $resultServidorPadrao->fetch_assoc();
		$codServidor = $rowServidorPadrao['ID'];
		
		$queryInterfaces = "SELECT * FROM interface WHERE SERVIDORES_ID = '$codServidor'";
		$resultInterfaces = $this->conn->query($queryInterfaces);
		$nRowInterfaces = $resultInterfaces->num_rows;
		while ($rowInterfaces = $resultInterfaces->fetch_assoc()) {
			
			$interface = new InterfacesVO();
		
			$interface->ID = $rowInterfaces['ID'];
			$interface->SERVIDORES_ID = $rowInterfaces['SERVIDORES_ID'];
			$interface->NOME = $rowInterfaces['NOME'];
			
			$interfaces[] = $interface;	
		}		
		
		return $interfaces;
			
	}
	
	
	public function selecionarAcesso($id)
{
		$query = "SELECT acesso_cliente.ID,acesso_cliente.EMPRESA_ID, acesso_cliente.CLIENTES_ID, acesso_cliente.LOGIN, acesso_cliente.SENHA, 
			acesso_cliente.ENDERECO_IP, acesso_cliente.ENDERECO_MAC,  acesso_cliente.DATA_VENC_CONTRATO, acesso_cliente.DATA_CADASTRO, 
			acesso_cliente.STATUS_2,
			clientes.NOME_RAZAO,
			interface.ID as ID_INTERFACE,interface.NOME as NOME_INTERFACE, 
			planos_acesso.ID as ID_PLANO_ACESSO,planos_acesso.NOME as NOME_PLANO_ACESSO,
			servidores.ID as ID_SERVIDOR, servidores.NOME_SERVER, 
			bases.ID as ID_BASE, bases.NOME as NOME_BASE, 
			material_acesso.ID as ID_MATERIAL,material_acesso.NOME as NOME_MATERIAL , 
			contratos_acesso.ID as ID_CONTRATO, contratos_acesso.NOME as NOME_CONTRATO
					FROM acesso_cliente
			    LEFT JOIN clientes ON clientes.ID = acesso_cliente.CLIENTES_ID
			    LEFT JOIN interface ON interface.ID = acesso_cliente.INTERFACE_ID 
			    LEFT JOIN planos_acesso ON planos_acesso.ID = acesso_cliente.PLANOS_ACESSO_ID
			    LEFT JOIN servidores ON servidores.ID = acesso_cliente.SERVIDORES_ID
			    LEFT JOIN bases ON bases.ID = acesso_cliente.BASES_ID    
			    LEFT JOIN material_acesso ON material_acesso.ID = acesso_cliente.MATERIAL_ACESSO_ID
			    LEFT JOIN contratos_acesso ON contratos_acesso.ID = acesso_cliente.CONTRATOS_ACESSO_ID
			    WHERE acesso_cliente.ID = '$id'";
		
		
		
		
		
		$result = $this->conn->query($query);
		$nrow = $result->num_rows;
		if($nrow == 0){return 'ERRO';}
		
		$row = $result->fetch_assoc();
			
			$acessoCliente = new AcessoClienteVO();
			
			$dataHoje = date('Y-m-d');
			$dataAtual = explode('-', $dataHoje);
			$dataVencimento = explode('-', $row['DATA_VENC_CONTRATO']);
			
			$dataAtual = mktime(0,0,0,$dataAtual[1],$dataAtual[2],$dataAtual[0]);
			$dataBoleto = mktime(0,0,0,$dataVencimento[1],$dataVencimento[2],$dataVencimento[0]);  
			$d3 = ($dataBoleto-$dataAtual);
			$dias = round(($d3/60/60/24));
			
			
			$acessoCliente->ID = $row['ID'];
			$acessoCliente->EMPRESA_ID = $row['EMPRESA_ID'];
			$acessoCliente->CLIENTES_ID = $row['CLIENTES_ID'];
			$acessoCliente->LOGIN = $row['LOGIN'];
			$acessoCliente->SENHA = $row['SENHA'];
			$acessoCliente->ENDERECO_IP = $row['ENDERECO_IP'];
			$acessoCliente->ENDERECO_MAC = $row['ENDERECO_MAC'];
			$acessoCliente->NOME_RAZAO = $row['NOME_RAZAO'];
			$acessoCliente->ID_INTERFACE = $row['ID_INTERFACE'];
			$acessoCliente->NOME_INTERFACE = $row['NOME_INTERFACE'];
			$acessoCliente->ID_PLANO_ACESSO = $row['ID_PLANO_ACESSO'];
			$acessoCliente->NOME_PLANO_ACESSO = $row['NOME_PLANO_ACESSO'];
			$acessoCliente->ID_SERVIDOR = $row['ID_SERVIDOR'];
			$acessoCliente->NOME_SERVER = $row['NOME_SERVER'];
			$acessoCliente->ID_BASE = $row['ID_BASE'];
			$acessoCliente->NOME_BASE = $row['NOME_BASE'];
			$acessoCliente->ID_MATERIAL = $row['ID_MATERIAL'];
			$acessoCliente->NOME_MATERIAL = $row['NOME_MATERIAL'];
			$acessoCliente->ID_CONTRATO = $row['ID_CONTRATO'];
			$acessoCliente->NOME_CONTRATO = $row['NOME_CONTRATO'];
			$acessoCliente->DATA_VENC_CONTRATO = $row['DATA_VENC_CONTRATO'];
			$acessoCliente->DATA_CADASTRO = $row['DATA_CADASTRO'];
			$acessoCliente->DIAS_CONTRATO = $dias;
			
			
			
		
		
		return $acessoCliente;
	}
	
	
	public function selecionarAcessoCliente($idCliente)
	{
		$queryIp = "SELECT * FROM radcheck WHERE attribute LIKE 'Framed-IP-Address' AND cliente LIKE '$idCliente'";
		$resultIp = $this->conn->query($queryIp);
		$rowIp = $resultIp->fetch_assoc();
		
		$queryMac = "SELECT * FROM radcheck WHERE attribute LIKE 'Calling-Station-ID' AND cliente LIKE '$idCliente'";
		$resultMac = $this->conn->query($queryMac);
		$rowMac = $resultMac->fetch_assoc();
		
		$querySenha = "SELECT * FROM radcheck WHERE attribute LIKE 'Password' AND cliente LIKE '$idCliente'";
		$resultSenha = $this->conn->query($querySenha);
		$rowSenha = $resultSenha->fetch_assoc();
					
			$Acesso = new AcessoVO();
						
			$Acesso->iPcliente = $rowIp['value'];
			$Acesso->mCaclient = $rowMac['value'];
			$Acesso->SenhaCliente = $rowSenha['value'];
			$Acesso->loginCliente = $rowSenha['username'];
			
			
		return $Acesso;			
	}
	public function excluirAcesso($codigoCliente, $login, $enderecoIp)
	{
		
		 	    
	$radius = "DELETE FROM radcheck WHERE username='$login'";
	$resultado1 = $this->conn->query($radius);
				
	$plano = "DELETE FROM radreply WHERE username='$login' ";
	$resultado2 = $this->conn->query($plano);
				
	$historico = "DELETE FROM radacct WHERE username='$login'";
	$resultado3 = $this->conn->query($historico);
				
	$radcheck2 = "DELETE FROM radcheck2 WHERE loginCliente='$login'";
	$resultado4 = $this->conn->query($radcheck2);

				
	$pessoaAcesso = "UPDATE pessoa SET temAcesso = 'NAO', acesso = '', loginCliente = '',ipCliente = 'p', macCliente = '', nmaquinas = '', tipoContrato = '', vencContrato = '', plano = '', regime = ''  WHERE codigoPessoa = '$codigoCliente'";
    $resultado5 = $this->conn->query($pessoaAcesso);
                
    //MIKROTIK
    /////////////
    $telnet = new PHPTelnet();

	// if the first argument to Connect is blank,
	// PHPTelnet will connect to the local host via 127.0.0.1
	$result = $telnet->Connect('192.168.20.1','suporte+ct','suportedigi123');

	if ($result == 0) 
	{
		//$interface = $_GET['ip'];
		$telnet->DoCommand('/ip address remove [/ip address find address="'.$enderecoIp.'/30"]', $result);
	}

		//////Logs de Acesso
		///////////////////////////////////////
		$user_logado = $_SESSION["nome"];
		$data = date('Y-m-d');
		$desc = 'Excluiu o Acesso de '.$login;
		
		$query_Logs = "INSERT INTO radius.logs (id ,login ,data ,desc)VALUES (NULL , '$user_logado', '$data', '$desc')";
		$result_logs = $this->conn->query($query_Logs);
		////////////////////////////////////////////////////////			 
	}
	public function verificarDuplicidadeLogin($loginCliente)
	{
		$query = "SELECT * FROM radcheck2 WHERE loginCliente LIKE '$loginCliente'";
		$result = $this->conn->query($query);
		$qtd = $result->num_rows;
		
		return $qtd;
	}
	public function validarIp($ip)
	{
			if (!eregi("^([0-9]){1,3}.([0-9]){1,3}.([0-9]){1,3}.([0-9]){1,3}$", $ip)) {
				$erro = 'erro';	
				return $erro;
				}
	}
	public function listarRegistroAcesso($loginCliente)
	{
		$query = "SELECT * FROM radacct WHERE username LIKE '$loginCliente' ORDER BY radacctid ASC";
		$result = $this->conn->query($query);
		//$qtd = $result->num_rows;
		
		while ($row = $result->fetch_assoc()) {
    		
			$Hacesso = new HistoricoAcessoVO();
							
			$Hacesso->acctstarttime = $row['acctstarttime'];
			$Hacesso->acctstoptime = $row['acctstoptime'];
			$Hacesso->acctsessiontime = $row['acctsessiontime'];
			$Hacesso->acctinputoctets = $row['acctinputoctets'];
			$Hacesso->acctoutputoctets = $row['acctoutputoctets'];
			$Hacesso->acctterminatecause = $row['acctterminatecause'];
			$Hacesso->framedipaddress = $row['framedipaddress'];
	
			
			$Hacessos[] = $Hacesso;
		}
		
		
		//////Logs de Acesso
		///////////////////////////////////////
		$user_logado = $_SESSION["nome"];
		$data = date('Y-m-d');
		$desc = 'Listou os Registros de Acesso'.$loginCliente;
		
		$query_Logs = "INSERT INTO radius.logs (id ,login ,data ,desc)VALUES (NULL , '$user_logado', '$data', '$desc')";
		$result_logs = $this->conn->query($query_Logs);
		////////////////////////////////////////////////////////	
		
		
		
		return $Hacessos;
	}
	public function listarConsumoBanda($login)
	{
				
		$query = "SELECT acctstarttime, acctinputoctets,acctoutputoctets, username
FROM radacct 
WHERE username = '$login' ";
		
		$result = $this->conn->query($query);
		
			while($row = $result->fetch_assoc())
			{
				$consumo = new ConsumoBandaVO();
				
				$uploadkb = $row['acctinputoctets'] / 1024;
				$uploadmb = $uploadkb / 1024;
			
				
				$downloadkb = $row['acctoutputoctets'] / 1024;
				$downloadmb = $downloadkb / 1024;
				
				$consumo->upload = substr($uploadmb, 0, 5);
				$consumo->download = substr($downloadmb, 0, 5);
				$consumo->mes = $row['acctstarttime'];
				$consumo->username = $row['username'];
				
				$consumos[] = $consumo;
				
			}
			
			//////Logs de Acesso
		///////////////////////////////////////
		$user_logado = $_SESSION["nome"];
		$data = date('Y-m-d');
		$desc = 'Listou o Consumo de Banda '.$login;
		
		$query_Logs = "INSERT INTO radius.logs (id ,login ,data ,desc)VALUES (NULL , '$user_logado', '$data', '$desc')";
		$result_logs = $this->conn->query($query_Logs);
		////////////////////////////////////////////////////////	
			
			
			return $consumos;
	}
	
	
	
	
	//------------INTERFACES DO SERVIDOR--------------//
	public function listarServidores($empresa)
	{
		$query = "SELECT * FROM servidores WHERE EMPRESA_ID = '$empresa'";
		$result = $this->conn->query($query);
		$nRow = $result->num_rows;
		if($nRow == 0){return 'ERRO';}
		
		while ($row = $result->fetch_assoc())
		{
			$servidor = new ServidoresVO();
			$servidor->ID = $row['ID'];
			$servidor->EMPRESA_ID = $row['EMPRESA_ID'];
			$servidor->NOME_SERVER = $row['NOME_SERVER'];
			$servidor->ENDERECO_IP = $row['ENDERECO_IP'];
			$servidor->USUARIO = $row['USUARIO'];
			$servidor->SENHA = $row['SENHA'];
			$servidor->PORTA_API = $row['PORTA_API'];
			$servidor->DEFAULT_2 = $row['DEFAULT_2'];
			
			$servidors[] = $servidor;
		}
		
		return $servidors;
	}
	public function cadastrarServidor(ServidoresVO $servidor)
	{
		//VERIFICA SE EXISTE UM SERVIDOR PADRAO
		$queryVerificaServerPadrao = "SELECT * FROM servidores WHERE DEFAULT_2 = 1 AND EMPRESA_ID='$servidor->EMPRESA_ID'";
		$resultverificaServerPadrao = $this->conn->query($queryVerificaServerPadrao);
		$nrowVerificaServerPadrao = $resultverificaServerPadrao->num_rows;
		
		if($nrowVerificaServerPadrao == 0)
		{
			if($servidor->DEFAULT_2 == 1)
			{
				$padrao = 1;	
			}else{
				$padrao = 0;
			}
			
		}else {
			$padrao = 0;
			
		}
		
		$query = "INSERT INTO servidores (ID, EMPRESA_ID, NOME_SERVER, ENDERECO_IP, USUARIO, SENHA, PORTA_API, DEFAULT_2) VALUES 
		(NULL, '$servidor->EMPRESA_ID', '$servidor->NOME_SERVER', '$servidor->ENDERECO_IP', '$servidor->USUARIO', '$servidor->SENHA', 
		'$servidor->PORTA_API', '$padrao')";
		
		$result = $this->conn->query($query);
		
	}
	public function listarInterfacesServidor($ip, $porta, $usuario, $senha)
	{
					$API = new routeros_api();
					$API->port = $porta;
				
					$API->debug = true;
					if ($API->connect($ip, $usuario, $senha)) {
					
					
					 $API->write('/interface/print',true);
					// $API->write('?type=wlan');
					 
			
					  $ARRAY = $API->read();
					  
					  return $ARRAY;
					 
					  $API->disconnect();
							  	
					}else{
					   return 'ERRO';
					}
	}
	public function cadastrarInterface(InterfacesVO $interface)
	{
			//CONSULTAR DADOS DO SERVIDOR
			$query = "SELECT * FROM servidores WHERE ID = $interface->SERVIDORES_ID";
			$result = $this->conn->query($query);
			$nRow = $result->num_rows;
			if($nRow == 0){return 'ERRO';}
			$row = $result->fetch_assoc();
			
			$porta = $row['PORTA_API'];
			$ip = $row['ENDERECO_IP'];
			$usuario = $row['USUARIO'];
			$senha = $row['SENHA'];
			
			//PREPARA A INTERFACE NO SERVIDOR
			//
			//--1-MUDAR NOME INSERIR COMENTARIO
		
					//TENTA CONECTAR-SE AO SERVIDOR REMOTAMENTE VIA API
					$API = new routeros_api();
					$API->port = $porta;
				
					$API->debug = true;
					if ($API->connect($ip, $usuario, $senha)) 
					{
						 //RENOMEIA INTERFACE DO CLIENTE;
						 $API->write('/interface/set',false);
						 $API->write('=name=' . $interface->NOME,false);
						 $API->write('=comment=' . $interface->DESCRICAO,false);
						 $API->write('=numbers=' . $interface->NAME_INTERFACE_REMOTA,false);
						 $API->write('=disabled=' . 'no');
						 $API->read();
						 
						 //CRIA SERVIDOR HOTSPOT CASO HOUVER;						 
						 
						 if($interface->HOSTPOT == '1')
						 {
						 	//CRIA EXECESSOES ANTES DE CRIAR O SERVER(WINBOX, API);
						 	//--WINBOX
							 $API->write('/ip/hotspot/walled-garden/ip/add',false);
							 //$API->write('=server=ServerOpus',false);
							 $API->write('=action=accept',false);
							 $API->write('=dst-address=' . $ip,false);
							 $API->write('=protocol=tcp',false);
							 $API->write('=dst-port=8291');
							 $API->read();
							 	//--API
							 $API->write('/ip/hotspot/walled-garden/ip/add',false);
		     				 //$API->write('=server=ServerOpus',false);
							 $API->write('=action=accept',false);
							 $API->write('=dst-address=' . $ip,false);
							 $API->write('=protocol=tcp',false);
							 $API->write('=dst-port=8728');
							 $API->read();
						 
							 //CRIA SERVIDOR HOTSPOT
							 $API->write('/ip/hotspot/add',false);
							 $API->write('=name=' .'sv'.$interface->NOME,false);
							 $API->write('=interface=' . $interface->NOME,false);
							 $API->write('=idle-timeout=00:01:00',false);
							 $API->write('=keepalive-timeout=00:00:30',false);						 
							 $API->write('=disabled=no');						 						 
							 $API->read();
						 }
						 
						if($interface->HOTSPOT_RADIUS == '1')
						 {
						 	//CRIA EXECESSOES ANTES DE CRIAR O SERVER(WINBOX, API);
						 	//--WINBOX
							 $API->write('/ip/hotspot/walled-garden/ip/add',false);
							 //$API->write('=server=ServerOpus',false);
							 $API->write('=action=accept',false);
							 $API->write('=dst-address=' . $ip,false);
							 $API->write('=protocol=tcp',false);
							 $API->write('=dst-port=8291');
							 $API->read();
							 	//--API
							 $API->write('/ip/hotspot/walled-garden/ip/add',false);
		     				 //$API->write('=server=ServerOpus',false);
							 $API->write('=action=accept',false);
							 $API->write('=dst-address=' . $ip,false);
							 $API->write('=protocol=tcp',false);
							 $API->write('=dst-port=8728');
							 $API->read();
							 
							 //CRIA SERVIDOR HOTSPOT
							 $API->write('/ip/hotspot/add',false);
							 $API->write('=name=' .'sv'.$interface->NOME,false);
							 $API->write('=interface=' . $interface->NOME,false);
							 $API->write('=idle-timeout=00:01:00',false);
							 $API->write('=keepalive-timeout=00:00:30',false);						 
							 $API->write('=disabled=no');						 						 
							 $API->read();
						 }
						 
						 //CADASTRAR INTERFACE NO BANCO DE DADOS
						 $queryCadastrarInterface = "INSERT INTO 
						 interface (SERVIDORES_ID, SERVIDOR_RADIUS_ID, NOME, DESCRICAO, HOSTPOT, PPOE, SIMPLES_QUEUE, HOTSPOT_RADIUS) 
						 VALUES ('$interface->SERVIDORES_ID', '$interface->SERVIDOR_RADIUS_ID', '$interface->NOME', '$interface->DESCRICAO', 
						 '$interface->HOSTPOT', '$interface->PPOE','$interface->SIMPLES_QUEUE', '$interface->HOTSPOT_RADIUS')";
						 
						 $resultCadastrarInterface = $this->conn->query($queryCadastrarInterface);
						 
						 
							////CRIA SERVER PROFILE HOSTSPOT
							//$API->write('/ip/hotspot/profile/add',false);
							//$API->write('=name=' . $interface->NOME,false);
							//$API->write('=hotspot-address=' . $ipServerHotspot,false);
							//$API->write('=dns-name=OpusServer');						 
							//$API->read();	
							  	
					}else{
					   return 'ERRO';
					}
			
			
		
		
		
	}
	public function listarInterfacesServidorRemote($idServidor)
	{
		
			//CONSULTA SERVIDOR PELO ID
			$query = "SELECT * FROM servidores WHERE ID = $idServidor";
			$result = $this->conn->query($query);
			$nRow = $result->num_rows;
			if($nRow == 0){return 'ERRO';}
			$row = $result->fetch_assoc();
			
			$porta = $row['PORTA_API'];
			$ip = $row['ENDERECO_IP'];
			$usuario = $row['USUARIO'];
			$senha = $row['SENHA'];
			 
   		
					//TENTA CONECTAR-SE AO SERVIDOR REMOTAMENTE VIA API
					$API = new routeros_api();
					$API->port = $porta;
				
					$API->debug = true;
					if ($API->connect($ip, $usuario, $senha)) 
					{
						//RETORNA LISTA DE INTERFACES DO SERVIDOR			
					 	$API->write('/interface/print',true);
					 	$ARRAY = $API->read();
					  
					  	return $ARRAY;
					 
					  	$API->disconnect();
							  	
					}else{
					   return 'ERRO';
					}
	}
	
	//------------SERVIDORES RADIUS-----------------------//
	public function cadastrarServRadius(ServidoresRadiusVO $servRadius)
	{
		
			//CONSULTAR DADOS DO SERVIDOR
			$query = "SELECT * FROM servidores WHERE ID = $servRadius->SERVIDORES_ID";
			$result = $this->conn->query($query);
			$nRow = $result->num_rows;
			if($nRow == 0){return 'ERRO';}
			$row = $result->fetch_assoc();
			
			$porta = $row['PORTA_API'];
			$ip = $row['ENDERECO_IP'];
			$usuario = $row['USUARIO'];
			$senha = $row['SENHA'];
			
					//TENTA CONECTAR-SE AO SERVIDOR REMOTAMENTE VIA API
					$API = new routeros_api();
					$API->port = $porta;
				
					$API->debug = true;
					if ($API->connect($ip, $usuario, $senha)) 
					{
						 //ADICIONAR SERVIDOR RADIUS NO SERVIDOR MIKROTIK
						 $API->write('/radius/add',false);
						 $API->write('=service=hotspot',false);
						 $API->write('=address=' . $servRadius->IP_FREERADIUS,false);
						 $API->write('=secret=' . $servRadius->SECRET,false);
						 $API->write('=authentication-port=' . $servRadius->AUTHENTICATION_PORT,false);
						 $API->write('=accounting-port=' .  $servRadius->ACCOUNTING_PORT);						 
						 $arr = $API->read();					 
						 
						
				$query = "INSERT INTO db_opus.servidores_radius (SERVIDORES_ID, NOME, IP_FREERADIUS, SECRET, AUTHENTICATION_PORT, ACCOUNTING_PORT, 
				TIMEOUT, USERNAME_MYSQL, PASSWORD_MYSQL, BANCO_DADOS) VALUES 
				('$servRadius->SERVIDORES_ID', '$servRadius->NOME', '$servRadius->IP_FREERADIUS', '$servRadius->SECRET', '$servRadius->AUTHENTICATION_PORT', 
				'$servRadius->ACCOUNTING_PORT', '300', '$servRadius->USERNAME_MYSQL', '$servRadius->PASSWORD_MYSQL', '$servRadius->BANCO_DADOS')";
					
				$result = $this->conn->query($query);
						 
						   $API->disconnect();
				
							return $arr;	
							  	
					}else{
					   return 'ERRO';
					}
			
			
		
		
	}
	public function editarServRadius(ServidoresRadiusVO $servRadius)
	{
			//CONSULTAR DADOS DO SERVIDOR
			$query = "SELECT * FROM servidores WHERE ID = $servRadius->SERVIDORES_ID";
			$result = $this->conn->query($query);
			$nRow = $result->num_rows;
			if($nRow == 0){return 'ERRO';}
			$row = $result->fetch_assoc();
			
			$porta = $row['PORTA_API'];
			$ip = $row['ENDERECO_IP'];
			$usuario = $row['USUARIO'];
			$senha = $row['SENHA'];
			
			
			
			
					//TENTA CONECTAR-SE AO SERVIDOR REMOTAMENTE VIA API
					$API = new routeros_api();
					$API->port = $porta;
				
					$API->debug = true;
					if ($API->connect($ip, $usuario, $senha)) 
					{
						
						 //LOCATIZA SERVER RADIUS NO MIKROTIK
						  $API->write('/radius/print',false);
						  $API->write('?address=' . $servRadius->IP_ANTIGO);
						  $userArray = $API->read();
						  $userID = $userArray[0]['.id'];						  
						   
						
						 //ALTERA SERVIDOR RADIUS NO SERVIDOR MIKROTIK
						 $API->write('/radius/set',false);
						 $API->write('=address=' . $servRadius->IP_FREERADIUS,false);
						 $API->write('=secret=' . $servRadius->SECRET,false);
						 $API->write('=authentication-port=' . $servRadius->AUTHENTICATION_PORT,false);
						 $API->write('=accounting-port=' .  $servRadius->ACCOUNTING_PORT, false);
						 $API->write('=numbers=' .  $userID);						 
						 $API->read();		

					
						 
						 
						
				$queryInsert = "UPDATE servidores_radius SET NOME='$servRadius->NOME', IP_FREERADIUS='$servRadius->IP_FREERADIUS', SECRET='$servRadius->SECRET', AUTHENTICATION_PORT='$servRadius->AUTHENTICATION_PORT', 
				ACCOUNTING_PORT='$servRadius->ACCOUNTING_PORT', TIMEOUT='$servRadius->TIMEOUT', USERNAME_MYSQL='$servRadius->USERNAME_MYSQL', 
				PASSWORD_MYSQL='$servRadius->PASSWORD_MYSQL', BANCO_DADOS='$servRadius->BANCO_DADOS' WHERE ID='$servRadius->ID'";
					
				$resultInsert = $this->conn->query($queryInsert)or die($this->conn->error);
						 
							
							  	
					}else{
					   return 'ERRO';
					}
			
	}
	public function selecionarServRadius($codServRadius)
	{
		$query = "SELECT servidores_radius. *, servidores.NOME_SERVER from servidores_radius
		LEFT JOIN servidores ON servidores.ID = servidores_radius.SERVIDORES_ID
		WHERE servidores_radius.ID = $codServRadius";
		
		$result = $this->conn->query($query);
		$nRow = $result->num_rows;
		if($nRow == 0){return 'ERRO';}
		
		$row = $result->fetch_assoc();
		
			$servRadius = new ServidoresRadiusVO();
			
			$servRadius->ID = $row['ID'];
			$servRadius->SERVIDORES_ID = $row['SERVIDORES_ID']; 
			$servRadius->NOME_SERVIDOR = $row['NOME_SERVER'];
			$servRadius->NOME = $row['NOME'];
			$servRadius->IP_FREERADIUS = $row['IP_FREERADIUS'];
			$servRadius->SECRET = $row['SECRET'];
			$servRadius->AUTHENTICATION_PORT = $row['AUTHENTICATION_PORT'];
			$servRadius->ACCOUNTING_PORT = $row['ACCOUNTING_PORT'];
			$servRadius->TIMEOUT = $row['TIMEOUT'];
			$servRadius->USERNAME_MYSQL = $row['USERNAME_MYSQL'];
			$servRadius->PASSWORD_MYSQL = $row['PASSWORD_MYSQL'];
			$servRadius->BANCO_DADOS = $row['BANCO_DADOS'];
			
				
		
		
			return $servRadius;
	}
	//------------SICRONIZA��O SERVER-------------------//
	public function configurarServer($interfaceSelect, InterfacesVO $interface, $planosAcesso, $faixaClientes, 
										$definirManualmente,$usarDHCP,$ip,$usuario,$senha,$porta)
	{
 			
		
						//VARIAVEIS ESTANCIADAS API
						$API = new routeros_api();
						$API->port = $porta;					
						$API->debug = true;
		
		
						
						
						if ($API->connect($ip, $usuario, $senha)) {					
						
						 //RENOMEIA INTERFACE DO CLIENTE;
						 $API->write('/interface/set',false);
						 $API->write('=name=' . $interface->NOME,false);
						 $API->write('=comment=' . $interface->DESCRICAO,false);
						 $API->write('=numbers=' . $interfaceSelect);
						 $API->read();
						 
						 //ADICIONAR IP INTERFACE CLIENTES;
						 $API->write('/ip/address/add',false);
						 $API->write('=address=' . $faixaClientes,false);
						 $API->write('=interface=' .  $interface->NOME);
						 $API->read();
						 
						 //CADASTRA INTERFACE NO BANCO DE DADOS;
							$queryInterface = "INSERT INTO interface (SERVIDORES_ID, NOME, DESCRICAO, HOSTPOT, PPOE, SIMPLES_QUEUE, 
							HOTSPOT_RADIUS) VALUES ( '$interface->SERVIDORES_ID', '$interface->NOME', '$interface->DESCRICAO', '$interface->HOSTPOT', 
							'$interface->PPOE', '$interface->SIMPLES_QUEUE', '$interface->HOTSPOT_RADIUS')";							
							$resultInterface = $this->conn->query($queryInterface);
						 
							//CASO S� UTILIZE HOTSPOT
					if($interface->HOSTPOT == 1){
							
							//CRIA EXECESSOES ANTES DE CRIAR O SERVER(WINBOX, API);
						 	//--WINBOX
						 $API->write('/ip/hotspot/walled-garden/ip/add',false);
						 //$API->write('=server=ServerOpus',false);
						 $API->write('=action=accept',false);
						 $API->write('=dst-address=' . $ip,false);
						 $API->write('=protocol=tcp',false);
						 $API->write('=dst-port=8291');
						 $API->read();
						 	//--API
						 $API->write('/ip/hotspot/walled-garden/ip/add',false);
	     				 //$API->write('=server=ServerOpus',false);
						 $API->write('=action=accept',false);
						 $API->write('=dst-address=' . $ip,false);
						 $API->write('=protocol=tcp',false);
						 $API->write('=dst-port=8728');
						 $API->read();	
						 
						 //PEGANDO IP DO SERVER
						 $arrayIp = explode('/', $faixaClientes);
						 $ipServerHotspot = $arrayIp[0];					 						   
						 
						 //CRIA SERVER PROFILE HOSTSPOT
						 $API->write('/ip/hotspot/profile/add',false);
						 $API->write('=name=' . $interface->NOME,false);
						 $API->write('=hotspot-address=' . $ipServerHotspot,false);
						 $API->write('=dns-name=OpusServer');						 
						 $API->read();		

						 //CRIA SERVIDOR HOTSPOT
						 $API->write('/ip/hotspot/add',false);
						 $API->write('=name=' .'sv'.$interface->NOME,false);
						 $API->write('=interface=' . $interface->NOME,false);
						 $API->write('=idle-timeout=00:01:00',false);
						 $API->write('=keepalive-timeout=00:00:30',false);						 
						 $API->write('=disabled=no');						 						 
						 $API->read();
						 
						 //PEGA O ID DA ULTIMA INTERFACE CADASTRADA
							 $queryUltimaInterface = "SELECT * FROM interface ORDER by ID DESC LIMIT 0, 1";
							 $resultUltimaInterface = $this->conn->query($queryUltimaInterface);
							 $rowUltimaInterface = $resultUltimaInterface->fetch_assoc();
							 $codUltimaInterface = $rowUltimaInterface['ID'];
						 
						 
						 //CRIAR USERS PROFILES HOTSPOT
						 $registroPlano = count($planosAcesso);
						 for($i=0;$i<$registroPlano;$i++)
						 {
						 	 $API->write('/ip/hotspot/user/profile/add',false);
							 $API->write('=name=' . $planosAcesso[$i]->nome,false);
							 $API->write('=idle-timeout=04:00:00',false);
							 $API->write('=keepalive-timeout=00:01:00',false);
							 $API->write('=rate-limit=' . $planosAcesso[$i]->upload.'/'.$planosAcesso[$i]->download,false);						 
							 $API->write('=status-autorefresh=00:03:00',false);
							 $API->write('transparent-proxy=no');						 						 
							 $API->read();		

							 $nomePlano = $planosAcesso[$i]->nome;
							 $download = $planosAcesso[$i]->download;
							 $upload = $planosAcesso[$i]->upload;
							 
							 //CADASTRAR PLANOS NO BANDO MYSQL
							 $queryInterface = "INSERT INTO planos_acesso (INTERFACE_ID, NOME, DOWNLOAD, UPLOAD) VALUES 
							 ('$codUltimaInterface', '$nomePlano', '$download', '$upload')";							
							 $resultInterface = $this->conn->query($queryInterface);							 
						 }						
					}
					
						//CASO UTILIZE HOTSPOT + FREERADIUS EXTERNO
					if($interface->HOTSPOT_RADIUS == 1){
						
						
						//CRIA EXECESSOES ANTES DE CRIAR O SERVER(WINBOX, API);
						 	//--WINBOX
						 $API->write('/ip/hotspot/walled-garden/ip/add',false);
						 //$API->write('=server=ServerOpus',false);
						 $API->write('=action=accept',false);
						 $API->write('=dst-address=' . $ip,false);
						 $API->write('=protocol=tcp',false);
						 $API->write('=dst-port=8291');
						 $API->read();
						 	//--API
						 $API->write('/ip/hotspot/walled-garden/ip/add',false);
	     				 //$API->write('=server=ServerOpus',false);
						 $API->write('=action=accept',false);
						 $API->write('=dst-address=' . $ip,false);
						 $API->write('=protocol=tcp',false);
						 $API->write('=dst-port=8728');
						 $API->read();	

						 //VERIFICA SE EXISTE ALGUM SERVIDOR RADIUS CADASTRADO PARA O SERVIDOR MIKROTIK ATUAL
						 $queryServerRadius = "SELECT * FROM servidores_radius WHERE SERVIDORES_ID = '$interface->SERVIDORES_ID'";
						 $resultServerRadius = $this->conn->query($queryServerRadius);
						 $nRowServerRadius = $resultServerRadius->num_rows;
						 if($nRowServerRadius > 0)
						 {
						 	$rowServerRadius = $resultServerRadius->fetch_assoc();
						 	
						 	$servidorRadius = new ServidorRadiusVO();
						 	$servidorRadius->ID = $rowServerRadius['ID'];
							$servidorRadius->SERVIDORES_ID = $rowServerRadius['SERVIDORES_ID']; 
							$servidorRadius->IP_FREERADIUS = $rowServerRadius['IP_FREERADIUS'];
							$servidorRadius->SECRET = $rowServerRadius['SECRET'];
							$servidorRadius->AUTHENTICATION_PORT = $rowServerRadius['AUTHENTICATION_PORT'];
							$servidorRadius->ACCOUNTING_PORT = $rowServerRadius['ACCOUNTING_PORT'];
							$servidorRadius->TIMEOUT = $rowServerRadius['TIMEOUT'];
							$servidorRadius->USERNAME_MYSQL = $rowServerRadius['USERNAME_MYSQL'];
							$servidorRadius->PASSWORD_MYSQL = $rowServerRadius['PASSWORD_MYSQL'];
							
							//CONFIGURA O SERVER RADIUS DENTRO DO MIKROTIK							
						 	$API->write('/radius/add',false);
						 	$API->write('=service=hotspot',false);
						 	$API->write('=-address=' . $servidorRadius->IP_FREERADIUS,false);
						 	$API->write('=secret=' . $servidorRadius->SECRET,false);
						 	$API->write('=authentication-port=' . $servidorRadius->AUTHENTICATION_PORT,false);
						 	$API->write('=accounting-port=' . $servidorRadius->ACCOUNTING_PORT,false);
						 	$API->write('=timeout=' . $servidorRadius->TIMEOUT);						 							 
						 	$API->read();						 
						 	
						 }else
						 {
						 	return 'SERVIDOR RADIUS N ENCONTRADO';
						 }						
						 
						 //PEGANDO IP DO SERVER
						 $arrayIp = explode('/', $faixaClientes);
						 $ipServerHotspot = $arrayIp[0];					 						   
						 
						 //CRIA SERVER PROFILE HOSTSPOT E DEFINE AUTENTICACAO VIA RADIUS
						 $API->write('/ip/hotspot/profile/add',false);
						 $API->write('=name=' . $interface->NOME,false);
						 $API->write('=hotspot-address=' . $ipServerHotspot,false);
						 $API->write('=dns-name=OpusServer',false);
						 $API->write('=use-radius=yes',false);
						 $API->write('=radius-accounting=yes');						 
						 $API->read();						

						 //CRIA SERVIDOR HOTSPOT
						 $API->write('/ip/hotspot/add',false);
						 $API->write('=name=' .'sv'.$interface->NOME,false);
						 $API->write('=interface=' . $interface->NOME,false);
						 $API->write('=idle-timeout=00:01:00',false);
						 $API->write('=keepalive-timeout=00:00:30',false);						 
						 $API->write('=disabled=no');						 						 
						 $API->read();
						 
						 //PEGA O ID DA ULTIMA INTERFACE CADASTRADA
							 $queryUltimaInterface = "SELECT * FROM interface ORDER by ID DESC LIMIT 0, 1";
							 $resultUltimaInterface = $this->conn->query($queryUltimaInterface);
							 $rowUltimaInterface = $resultUltimaInterface->fetch_assoc();
							 $codUltimaInterface = $rowUltimaInterface['ID'];						 
						 
						 //CRIAR USERS PROFILES HOTSPOT
						 $registroPlano = count($planosAcesso);
						 for($i=0;$i<$registroPlano;$i++)
						 {
						 	 $API->write('/ip/hotspot/user/profile/add',false);
							 $API->write('=name=' . $planosAcesso[$i]->nome,false);
							 $API->write('=idle-timeout=04:00:00',false);
							 $API->write('=keepalive-timeout=00:01:00',false);
							 $API->write('=rate-limit=' . $planosAcesso[$i]->upload.'/'.$planosAcesso[$i]->download,false);						 
							 $API->write('=status-autorefresh=00:03:00',false);
							 $API->write('transparent-proxy=no');						 						 
							 $API->read();		

							 $nomePlano = $planosAcesso[$i]->nome;
							 $download = $planosAcesso[$i]->download;
							 $upload = $planosAcesso[$i]->upload;
							 
							 //CADASTRAR PLANOS NO BANDO MYSQL
							 $queryInterface = "INSERT INTO planos_acesso (INTERFACE_ID, NOME, DOWNLOAD, UPLOAD) VALUES 
							 ('$codUltimaInterface', '$nomePlano', '$download', '$upload')";							
							 $resultInterface = $this->conn->query($queryInterface);	
							 							 
							 $nomePlano = $codUltimaInterface.'_'.$nomePlano;
							 
							 // CADASTRA PLANO NA TABELA MYSQL DO RADIUS
							 $valueDownloadUpload = $download.'/'.$upload;
							 $queryInsertRadius = "INSERT INTO radgrouprapley(id,groupname,attribute,op,value) VALUES 
							 (NULL, '$nomePlano', 'Mikrotik-Rate-Limit', ':=', '$valueDownloadUpload'";
							 $resultInsertRadius  = $this->conn->query($queryInsertRadius);
						 }						
					}
					
					
					
					
					
					
						 
						 //DESCONECTA DO SERVIDOR
						 $API->disconnect();
								  	
						}else 
						{
							return 'ERRO';	
						}
						
										
						
						
						
		
		
						
		
		//C�DIGO PARA CADASTRAR SERVIDOR RADIUS
		//INSERT INTO db_opus.servidores_radius (SERVIDORES_ID, IP_FREERADIUS, SECRET, AUTHENTICATION_PORT, ACCOUNTING_PORT, TIMEOUT, USERNAME_MYSQL, PASSWORD_MYSQL) VALUES (1, '192.168.20.13', '123456', '1812', '1813', '300', 'root', '37261827');
		
		
		
		
	}
	
	
	//------------CONTRATOS-DE-ACESSO------------------///
	public function listarContratosAcesso($codEmpresa)
	{
		$query = "SELECT * FROM contratos_acesso WHERE EMPRESA_ID = '$codEmpresa'";
		$result = $this->conn->query($query);
		$nRow = $result->num_rows;
		if($nRow ==0){return 'ERRO';}
		
		while ($row = $result->fetch_assoc())
		{
			$contrato = new ContratosAcessoVO();
			
			$contrato->ID = $row['ID'];
		  	$contrato->EMPRESA_ID = $row['EMPRESA_ID'];
		  	$contrato->NOME = $row['NOME'];
		  	$contrato->CLAUSULAS = $row['CLAUSULAS'];
		  	$contrato->VIGENCIA = $row['VIGENCIA'];
		  	$contrato->VALOR_ADESAO = $row['VALOR_ADESAO'];
		  	$contrato->VALOR_INSTALACAO = $row['VALOR_INSTALACAO'];
		  	$contrato->REGIME = $row['REGIME'];
		  	
		  	$contratos[] = $contrato;
		}	
		
		
		return $contratos;
	}
	public function selecionarContratoAcesso($codContrato)
	{
		$query = "SELECT * FROM contratos_acesso WHERE ID = '$codContrato'";
		$result = $this->conn->query($query);
		$nRow = $result->num_rows;
		if($nRow ==0){return 'ERRO';}
		
		$row = $result->fetch_assoc();
	
			$contrato = new ContratosAcessoVO();
			
			$contrato->ID = $row['ID'];
		  	$contrato->EMPRESA_ID = $row['EMPRESA_ID'];
		  	$contrato->NOME = $row['NOME'];
		  	$contrato->CLAUSULAS = $row['CLAUSULAS'];
		  	$contrato->VIGENCIA = $row['VIGENCIA'];
		  	$contrato->VALOR_ADESAO = $row['VALOR_ADESAO'];
		  	$contrato->VALOR_INSTALACAO = $row['VALOR_INSTALACAO'];
		  	$contrato->REGIME = $row['REGIME'];
		  	
		  
		
		
		return $contrato;
	}
	public function cadastrarContratoAcesso(ContratosAcessoVO $contrato)
	{
		$query = "INSERT INTO contratos_acesso (EMPRESA_ID, NOME, CLAUSULAS, VIGENCIA, VALOR_ADESAO, REGIME) 
		VALUES ('$contrato->EMPRESA_ID', '$contrato->NOME', '$contrato->CLAUSULAS', '$contrato->VIGENCIA', '$contrato->VALOR_ADESAO', 
		'$contrato->REGIME')";
		
		if(!$result = $this->conn->query($query))
		{
			return $this->conn->error;
		}else{
			return 'OK';
		}
	}
	public function editarContratoAcesso(ContratosAcessoVO $contrato)
	{
		$query = "UPDATE contratos_acesso SET NOME='$contrato->NOME', CLAUSULAS='$contrato->CLAUSULAS', VIGENCIA='$contrato->VIGENCIA', 
		VALOR_ADESAO='$contrato->VALOR_ADESAO',REGIME='$contrato->REGIME' WHERE ID='$contrato->ID'";
		
		if(!$result = $this->conn->query($query))
		{
			return $this->conn->error;
		}else{
			return 'OK';
		}		
	}
	public function excluirContratoAcesso($codContrato)
	{
		$query = "DELETE FROM contratos_acesso WHERE ID='$codContrato'";		
		$result = $this->conn->query($query);
	}
	
	
	//------------CREDENCIAIS DE ACESSO----------------------//
	public function listarCredenciaisAcesso($codEmpresa)
	{
		$query = "SELECT acesso_cliente.ID,acesso_cliente.EMPRESA_ID, acesso_cliente.CLIENTES_ID, acesso_cliente.LOGIN, acesso_cliente.SENHA, 
			acesso_cliente.ENDERECO_IP, acesso_cliente.ENDERECO_MAC,  acesso_cliente.DATA_VENC_CONTRATO, acesso_cliente.DATA_CADASTRO, 
			acesso_cliente.STATUS_2,
			clientes.NOME_RAZAO,
			interface.ID as ID_INTERFACE,interface.NOME as NOME_INTERFACE, 
			planos_acesso.ID as ID_PLANO_ACESSO,planos_acesso.NOME as NOME_PLANO_ACESSO,
			servidores.ID as ID_SERVIDOR, servidores.NOME_SERVER, 
			bases.ID as ID_BASE, bases.NOME as NOME_BASE, 
			material_acesso.ID as ID_MATERIAL,material_acesso.NOME as NOME_MATERIAL , 
			contratos_acesso.ID as ID_CONTRATO, contratos_acesso.NOME as NOME_CONTRATO
					FROM acesso_cliente
			    LEFT JOIN clientes ON clientes.ID = acesso_cliente.CLIENTES_ID
			    LEFT JOIN interface ON interface.ID = acesso_cliente.INTERFACE_ID 
			    LEFT JOIN planos_acesso ON planos_acesso.ID = acesso_cliente.PLANOS_ACESSO_ID
			    LEFT JOIN servidores ON servidores.ID = acesso_cliente.SERVIDORES_ID
			    LEFT JOIN bases ON bases.ID = acesso_cliente.BASES_ID    
			    LEFT JOIN material_acesso ON material_acesso.ID = acesso_cliente.MATERIAL_ACESSO_ID
			    LEFT JOIN contratos_acesso ON contratos_acesso.ID = acesso_cliente.CONTRATOS_ACESSO_ID
			    WHERE acesso_cliente.EMPRESA_ID = '$codEmpresa' AND acesso_cliente.STATUS_2 LIKE 'ATIVO'
			    ORDER by ID DESC";
		
		
		
		
		
		$result = $this->conn->query($query);
		$nrow = $result->num_rows;
		if($nrow == 0){return 'ERRO';}
		
		while ($row = $result->fetch_assoc()) {
			
			$acessoCliente = new AcessoClienteVO();
			
			$dataHoje = date('Y-m-d');
			$dataAtual = explode('-', $dataHoje);
			$dataVencimento = explode('-', $row['DATA_VENC_CONTRATO']);
			
			$d1 = mktime(0,0,0,$dataAtual[1],$dataAtual[2],$dataAtual[0]);
			$d2 = mktime(0,0,0,$dataVencimento[1],$dataVencimento[2],$dataVencimento[0]);  
			$d3 = ($d2-$d1);
			$dias = ($d3/2592000);
			$dias = explode('.', $dias);
			$meses = $dias[0];
			
		
		
			
	
			  
			
			
			
			$acessoCliente->ID = $row['ID'];
			$acessoCliente->EMPRESA_ID = $row['EMPRESA_ID'];
			$acessoCliente->CLIENTES_ID = $row['CLIENTES_ID'];
			$acessoCliente->LOGIN = $row['LOGIN'];
			$acessoCliente->SENHA = $row['SENHA'];
			$acessoCliente->ENDERECO_IP = $row['ENDERECO_IP'];
			$acessoCliente->ENDERECO_MAC = $row['ENDERECO_MAC'];
			$acessoCliente->NOME_RAZAO = $row['NOME_RAZAO'];
			$acessoCliente->ID_INTERFACE = $row['ID_INTERFACE'];
			$acessoCliente->NOME_INTERFACE = $row['NOME_INTERFACE'];
			$acessoCliente->ID_PLANO_ACESSO = $row['ID_PLANO_ACESSO'];
			$acessoCliente->NOME_PLANO_ACESSO = $row['NOME_PLANO_ACESSO'];
			$acessoCliente->ID_SERVIDOR = $row['ID_SERVIDOR'];
			$acessoCliente->NOME_SERVER = $row['NOME_SERVER'];
			$acessoCliente->ID_BASE = $row['ID_BASE'];
			$acessoCliente->NOME_BASE = $row['NOME_BASE'];
			$acessoCliente->ID_MATERIAL = $row['ID_MATERIAL'];
			$acessoCliente->NOME_MATERIAL = $row['NOME_MATERIAL'];
			$acessoCliente->ID_CONTRATO = $row['ID_CONTRATO'];
			$acessoCliente->NOME_CONTRATO = $row['NOME_CONTRATO'];
			$acessoCliente->DATA_VENC_CONTRATO = $row['DATA_VENC_CONTRATO'];
			$acessoCliente->DATA_CADASTRO = $row['DATA_CADASTRO'];
			$acessoCliente->DIAS_CONTRATO = $meses;
			
			$acessoClientes[] = $acessoCliente;
			
		}
		
		
		
		
		return $acessoClientes;
	}
	
	public function listarHitoricoAcesso($codAcesso)
	{
		//SELECIONA ACESSO
		$query = "SELECT * FROM acesso_cliente WHERE ID=$codAcesso";
		if(!$result = $this->conn->query($query))
		{
			return $this->conn->error;
		}
		$nRow = $result->num_rows;
		if($nRow == 0)
		{
			return 'CREDENCIAIS DE ACESSI - NENHUM REGISTRO ENCONTRADO';
		}
		$row = $result->fetch_assoc();
		
		//VARIAVEIS
		$USUARIO = $row['LOGIN'];
		
		
		//VERIFICAR INTERFACE SE UTILIZA RADIUS
		
		//BUSCAR REGISTRO DE ACESSO NO RADIUS
		$query1 = "SELECT * FROM radacct WHERE username='$USUARIO' ORDER by radacctid DESC";
		if(!$result1 = $this->conn->query($query1))
		{
			return $this->conn->error;
		}
		$nRow1 = $result1->num_rows;
		if($nRow1 == 0)
		{
			return 'BUSCA DE REGISTRO DE ACESSO, NENHUM REGISTRO FOI ENCONTRADO';
		}
		
		function convertTime($miliseconds)
		{
			if($miliseconds == 0)
			{
				return '00:00:00';
			}
			
			$seconds= $miliseconds;
			//for seconds
			if($seconds> 0)
			{
				$sec= "" . ($seconds%60);
				if($seconds % 60 <10)
				{
					$sec= "0" . ($seconds%60);
				}
			}
			//for mins
			if($seconds > 60)
			{
				$mins= "". ($seconds/60%60);
				if(($seconds/60%60)<10)
				{
					$mins= "0" . ($seconds/60%60);
				}
			}else
			{
			$mins= "00";
			}
			//for hours
			if($mins/60 > 60)
			{
				$hours= "". ($mins/60/60);
				if(($mins/60/60) < 10)
				{
					$hours= "0" . ($mins/60/60);
				}
			}else
			{
				$hours= "00";
			}

			return $time_format= "" . $hours . ":" . $mins . ":" . $sec; //00:15:00
		}
		
		
		function ByteSize($bytes) 
    {
    $size = $bytes / 1024;
    if($size < 1024)
        {
        $size = number_format($size, 2);
        $size .= ' KB';
        } 
    else 
        {
        if($size / 1024 < 1024) 
            {
            $size = number_format($size / 1024, 2);
            $size .= ' MB';
            } 
        else if ($size / 1024 / 1024 < 1024)  
            {
            $size = number_format($size / 1024 / 1024, 2);
            $size .= ' GB';
            } 
        }
    return $size;
    } 
		
		while ( $row1 = $result1->fetch_assoc() ) {
			
			$radacct = new RadacctVO();
			
			$radacct->radacctid = $row1['radacctid'];
			$radacct->acctsessionid = $row1['acctsessionid'];
			$radacct->acctuniqueid = $row1['acctuniqueid'];
			$radacct->username = $row1['username'];
			$radacct->groupname = $row1['groupname'];
			$radacct->realm = $row1['realm'];
			$radacct->nasipaddress = $row1['nasipaddress'];
			$radacct->nasportid = $row1['nasportid'];
			$radacct->nasporttype = $row1['nasporttype'];
			$radacct->acctstarttime = $row1['acctstarttime'];
			$radacct->acctstoptime = $row1['acctstoptime'];
			$radacct->acctsessiontime = convertTime($row1['acctsessiontime']);
			$radacct->acctauthentic = $row1['acctauthentic'];
			$radacct->connectinfo_start = $row1['connectinfo_start'];
			$radacct->connectinfo_stop = $row1['connectinfo_stop'];
			$radacct->acctinputoctets =ByteSize($row1['acctinputoctets']);
			$radacct->acctoutputoctets = ByteSize($row1['acctoutputoctets']);
			$radacct->calledstationid = $row1['calledstationid'];
			$radacct->callingstationid = $row1['callingstationid'];
			$radacct->acctterminatecause = $row1['acctterminatecause'];
			$radacct->servicetype = $row1['servicetype'];
			$radacct->framedprotocol = $row1['framedprotocol'];
			$radacct->framedipaddress = $row1['framedipaddress'];
			$radacct->acctstartdelay = $row1['acctstartdelay'];
			$radacct->acctstopdelay = $row1['acctstopdelay'];
			$radacct->xascendsessionsvrkey = $row1['xascendsessionsvrkey'];
			if($radacct->acctstoptime == '')
			{
				$radacct->status = 'ABERTA';
			}else{
				$radacct->status = 'FECHADA';
			}
			
			$radaccts[] = $radacct;
				
		}
		
		return $radaccts;
	}
	public function listarClientesScRedenciais($codEmpresa)
	{
		$query = "SELECT * FROM clientes WHERE EMPRESA_ID = '$codEmpresa' AND STATUS_2 = 'INATIVO'
		AND NOT EXISTS (SELECT * from acesso_cliente WHERE CLIENTES_ID = clientes.ID) ORDER by NOME_RAZAO ASC ";
		$result = $this->conn->query($query);
		$nRow = $result->num_rows;
		if($nRow == 0){return 'ERRO';}
		
		while($row = $result->fetch_assoc())
		{

				$cliente = new ClientesVO();
				
				$cliente->ID = $row['ID'];
				$cliente->NOME_RAZAO = $row['NOME_RAZAO'];
				
				$clientes[] = $cliente;
				
			
			
			
		}
		
				return $clientes;
		
	}
	public function listarClientesCtEncerrado($codEmpresa)
	{
		$query = "SELECT acesso_cliente. * , clientes.NOME_RAZAO
			FROM acesso_cliente LEFT JOIN clientes ON clientes.ID = acesso_cliente.CLIENTES_ID 
      WHERE acesso_cliente.EMPRESA_ID = '$codEmpresa' AND acesso_cliente.STATUS_2 = 'ENCERRADO'
			ORDER by acesso_cliente.ID DESC";
		
		$result = $this->conn->query($query);
		$nRow = $result->num_rows;
		if($nRow == 0){return 'ERRO';}
		
		while($row = $result->fetch_assoc())
		{

				$cliente = new ClientesVO();
				
				$cliente->ID = $row['ID'];
				$cliente->NOME_RAZAO = $row['NOME_RAZAO'];
				
				$clientes[] = $cliente;		
		}	
				return $clientes;		
	}
	public function listarInterfacesPorServidor($codEmpresa)
	{
		$query = "SELECT * FROM interface WHERE SERVIDORES_ID = (SELECT servidores.ID from servidores WHERE DEFAULT_2 = 1 AND 
		EMPRESA_ID = '$codEmpresa')";
		$result = $this->conn->query($query);
		$nRow = $result->num_rows;
		if($nRow == 0){return 'ERRO';}
		
		while($row =$result->fetch_assoc())
		{
			$interface = new InterfacesVO();
			$interface->ID = $row['ID'];
			$interface->SERVIDORES_ID = $row['SERVIDORES_ID']; 
			$interface->SERVIDOR_RADIUS_ID = $row['SERVIDOR_RADIUS_ID'];	
			$interface->NOME = $row['NOME'];
			$interface->DESCRICAO = $row['DESCRICAO'];
			$interface->HOSTPOT = $row['HOSTPOT'];
			$interface->PPOE = $row['PPOE'];
			$interface->SIMPLES_QUEUE = $row['SIMPLES_QUEUE'];
			$interface->HOTSPOT_RADIUS = $row['HOTSPOT_RADIUS'];
			
			$interfaces[] = $interface;
		}
		
		return $interfaces;
	}
	public function selecionarInterface($codInterface)
	{
		$query = "SELECT interface.*, servidores_radius.ID as ID_SERVIDOR_RADIUS, 
		servidores_radius.NOME as NOME_SERVIDOR_RADIUS, 
		servidores.ID as ID_SERVIDOR, servidores.NOME_SERVER as NOME_SERVIDOR
		FROM interface
		LEFT JOIN servidores_radius ON servidores_radius.ID = interface.SERVIDOR_RADIUS_ID
		LEFT JOIN servidores ON servidores.ID = interface.SERVIDORES_ID
		WHERE interface.ID = $codInterface";
		
		$result = $this->conn->query($query);
		$nRow = $result->num_rows;
		if($nRow == 0){return 'ERRO';}
		
		$row = $result->fetch_assoc();
		
			$interface = new InterfacesVO();
			$interface->ID = $row['ID'];
			$interface->SERVIDORES_ID = $row['SERVIDORES_ID']; 
			$interface->SERVIDOR_RADIUS_ID = $row['SERVIDOR_RADIUS_ID'];			
			$interface->NOME = $row['NOME'];
			$interface->DESCRICAO = $row['DESCRICAO'];
			$interface->HOSTPOT = $row['HOSTPOT'];
			$interface->PPOE = $row['PPOE'];
			$interface->SIMPLES_QUEUE = $row['SIMPLES_QUEUE'];
			$interface->HOTSPOT_RADIUS = $row['HOTSPOT_RADIUS'];			
			$interface->ID_SERVIDOR_RADIUS= $row['ID_SERVIDOR_RADIUS'];
			$interface->NOME_SERVIDOR_RADIUS= $row['NOME_SERVIDOR_RADIUS'];
			$interface->ID_SERVIDOR= $row['ID_SERVIDOR'];
			$interface->NOME_SERVIDOR= $row['NOME_SERVIDOR'];
		
		
		return $interface;
	}
	public function listarInterfacesPorServidorSelecionado($codServidor)
	{
		$query = "SELECT * FROM interface WHERE SERVIDORES_ID = $codServidor";
		$result = $this->conn->query($query);
		$nRow = $result->num_rows;
		if($nRow == 0){return 'ERRO';}
		
		while($row = $result->fetch_assoc())
		{
			$interface = new InterfacesVO();
			$interface->ID = $row['ID'];
			$interface->SERVIDORES_ID = $row['SERVIDORES_ID']; 
			$interface->SERVIDOR_RADIUS_ID = $row['SERVIDOR_RADIUS_ID'];	
			$interface->NOME = $row['NOME'];
			$interface->DESCRICAO = $row['DESCRICAO'];
			$interface->HOSTPOT = $row['HOSTPOT'];
			$interface->PPOE = $row['PPOE'];
			$interface->SIMPLES_QUEUE = $row['SIMPLES_QUEUE'];
			$interface->HOTSPOT_RADIUS = $row['HOTSPOT_RADIUS'];
			
			$interfaces[] = $interface;
		}
		
		return $interfaces;
	}
	public function listarServidoresRadius($codServidor)
	{
		$query = "SELECT * FROM servidores_radius where SERVIDORES_ID = $codServidor";
		$result = $this->conn->query($query);
		$nRow = $result->num_rows;
		if($nRow == 0){return 'ERRO';}
		
		while($row = $result->fetch_assoc())
		{
			$servidorRadius = new ServidoresRadiusVO();
			
			$servidorRadius->ID = $row['ID'];
			$servidorRadius->SERVIDORES_ID= $row['SERVIDORES_ID'];
			$servidorRadius->NOME= $row['NOME'];
			$servidorRadius->IP_FREERADIUS= $row['IP_FREERADIUS'];
			$servidorRadius->SECRET= $row['SECRET'];
			$servidorRadius->AUTHENTICATION_PORT= $row['AUTHENTICATION_PORT'];
			$servidorRadius->ACCOUNTING_PORT= $row['ACCOUNTING_PORT'];
			$servidorRadius->TIMEOUT= $row['TIMEOUT'];
			$servidorRadius->USERNAME_MYSQL= $row['USERNAME_MYSQL'];
			$servidorRadius->PASSWORD_MYSQL= $row['PASSWORD_MYSQL'];
			$servidorRadius->BANCO_DADOS= $row['BANCO_DADOS'];
			
			$servidorRadiusS[] = $servidorRadius;
		}
		
		return $servidorRadiusS;
	}
	public function excluirInterface($codInterface)
	{
		$query = "DELETE FROM interface WHERE ID = $codInterface";
		$result = $this->conn->query($query);
	}
	public function listarPlanosAcessoPorInterfaces($codInterface)
	{
		$query = "SELECT * FROM planos_acesso WHERE INTERFACE_ID = '$codInterface'";
		$result = $this->conn->query($query);
		$nRow = $result->num_rows;
		if($nRow == 0){return 'ERRO';}
		
		while ($row = $result->fetch_assoc()) {
			
			$plano = new PlanosAcessoVO();
			
			$plano->ID = $row['ID'];
			$plano->INTERFACE_ID = $row['INTERFACE_ID'];
			$plano->NOME = $row['NOME'];
			$plano->RATE_LIMIT = $row['RATE_LIMIT'];
			$plano->SHARED_USERS = $row['SHARED_USERS'];
			$plano->SESSION_TIMEOUT = $row['SESSION_TIMEOUT'];
			$plano->KEEPALIVE_TIMEOUT = $row['KEEPALIVE_TIMEOUT'];
			$plano->IDLE_TIMEOUT = $row['IDLE_TIMEOUT'];
			$plano->STATUS_AUTOREFRESH = $row['STATUS_AUTOREFRESH'];			
			$plano->VALOR = $row['VALOR'];
			
			$planos[] = $plano;
		}
		return $planos;
	}
	public function editarCredencialAcesso(AcessoClienteVO $aC)
	{
		$resultado = true;
		$this->conn->autocommit(false);
		
		//COLOCADO PARA DESVIAR ERRO DE SINTAXE
		$aC->ENDERECO_MAC = $aC->ENDERECO_MAC2;
		
		//PROCURA INTERFACE INFORMADA
		$queryAutenBandaInterface = "SELECT * FROM interface WHERE ID = '$aC->ID_INTERFACE'";
		$resultAutenBandaInterface = $this->conn->query($queryAutenBandaInterface);
		
		//CASO NAO ENCONTRE A INTERFACE INFORMADA, CONDICAO FALHARA
		if($resultAutenBandaInterface->num_rows == 0)
		{
		   $resultado = false;
		}
				
		//RESULTADO DA PROCURA POR INTERFACE
		$rowAutenBandaInterface = $resultAutenBandaInterface->fetch_assoc();		
		 
		//DEFINE NOME INTERFACE
		 $nomeInterface =$rowAutenBandaInterface['NOME']; 
		
		//DEFINE SERVIDOR
		$codServidor = $rowAutenBandaInterface['SERVIDORES_ID'];
		
		//DEFINE COD DO SERVIDOR RADIUS
		$codServidorRadius = $rowAutenBandaInterface['SERVIDOR_RADIUS_ID'];
				
		//DEFINE TIPO DE AUTENTICACAO
		if($rowAutenBandaInterface['HOSTPOT'] == 1)
		{
			$tipoAutenBanda = 1;
		}
		if($rowAutenBandaInterface['PPOE'] == 1)
		{
			$tipoAutenBanda = 2;
		}
		if($rowAutenBandaInterface['SIMPLES_QUEUE'] == 1)
		{
			$tipoAutenBanda = 3;
		}
		if($rowAutenBandaInterface['HOTSPOT_RADIUS'] == 1)
		{
			$tipoAutenBanda = 4;
		}
		
		//PROCURA SERVIDOR INFORMADO		
		$queryServidorDefault = "SELECT * FROM servidores WHERE ID=$codServidor";
		$resultServidorDefault = $this->conn->query($queryServidorDefault);
		
		//CASO NAO ENCONTRE O SERVIDOR, A CONDICAO FALHARA
		if($resultServidorDefault->num_rows == 0)
		{
		     $resultado = false;
		}
		
		//RESULTADO DA CONSULTA POR SERVIDOR
		$rowServidorDefault = $resultServidorDefault->fetch_assoc();
		
		//DEFINE IP DO SERVIDOR
		$ip = $rowServidorDefault['ENDERECO_IP'];
		
		//DEFINE USUARIO DO SERVIDOR
		$usuario = $rowServidorDefault['USUARIO'];
		
		//DEFINE SENHA DO SERVIDOR
		$senha = $rowServidorDefault['SENHA'];
		
		//DEFINE PORTA DO SERVIDOR
		$porta = $rowServidorDefault['PORTA_API'];
	
		//INSTANCIA A CLASSE ROUTEROS_API()
		$API = new routeros_api();
		$API->port = $porta;					
		$API->debug = true;
	
		//CASO A INTERFACE UTILIZE SOMENTE HOTSPOT
		if($tipoAutenBanda == 1)
		{	
			//SE CONECTAR-SE AO SERVIDOR	
			if ($API->connect($ip, $usuario, $senha))
			{
			
			  //PROCURA USUARIO NO HOTSPOT
			  $API->write('/ip/hotspot/user/print',false);
			  $API->write('?name=' . $aC->LOGIN);
			  $userArray = $API->read();
			  $userID = $userArray[0]['.id'];
			  
			  //REMOVE O USUARIO DO HOTSPOT		 					  
			  $API->write('/ip/hotspot/user/remove',false);
			  $API->write('=numbers=' . $userID);
			  $API->read();	
									
			   //CADASTRA USUARIO NO HOTSPOT NOVAMENTE
			  $API->write('/ip/hotspot/user/add',false);
			  $API->write('=address=' . $aC->ENDERECO_IP,false);
			  $API->write('=mac-address=' . $aC->ENDERECO_MAC,false);
			  $API->write('=profile=' . $aC->NOME_PLANO_ACESSO,false);
			  $API->write('=server=' . 'sv'.$aC->NOME_INTERFACE,false);
			  $API->write('=name=' . $aC->LOGIN,false);
			  $API->write('=password=' . $aC->SENHA,false);
			  $API->write('=comment=' . $aC->NOME_RAZAO);
			  $API->read();						  
			  			 
			}
		}	
			//CASO UTILIZE SIMPLES QUEUE, ALTERA SOMENTE O MAC
			if($tipoAutenBanda == 3)
			{
				//PROCURA PLANO DE ACESSO INFORMADO
				$queryPegarValorPlano = "SELECT * FROM planos_acesso WHERE ID = '$aC->ID_PLANO_ACESSO'";
				$resultPegarValorPlano = $this->conn->query($queryPegarValorPlano);
				
				//CASO NAO ENCONTRE O PLANO, CONDICAO FALHARA
				if($resultPegarValorPlano->num_rows == 0)
				{
				   $resultado = false;
				}
				
				//RESULTADO DA BUSCA PELO PLANO
				$rowPegarValorPlano = $resultPegarValorPlano->fetch_assoc();
				
				//DEFINE O DOWNLOAD
				$DOWNLOAD = $rowPegarValorPlano['DOWNLOAD'];
				
				//DEFINE ENDERECO IP
				$ipMK1 = substr($aC->ENDERECO_IP, 0, -1);
				$ipMK2 = substr($aC->ENDERECO_IP , -1);
				$ipMK3 = $ipMK2 + 1;			
				$ip2 = $ipMK1.$ipMK3;
		
				//CADASTRA CONTROLE DE BANDA NO QUEUE DO SERVIDOR
				$API->write('/queue/simple/add',false);
				$API->write('=name=' . "<".$aC->NOME_INTERFACE."> ".$aC->NOME_RAZAO,false);
				$API->write('=target-addresses=' . $ip2,false);
				$API->write('=max-limit=' . $DOWNLOAD.'/'.$UPLOAD);			
				$API->read();			
			
			}
			
			
			 //CASO UTILIZE RADIUS + HOTSPOT
			if($tipoAutenBanda == 4)
			{
				
			   //BUSCAR SERVIDOR RADIUS ASSOCIADO;
			   $queryServidorRadius = "SELECT * FROM servidores_radius WHERE ID = $codServidorRadius";
			   $resultServidorRadius = $this->conn->query($queryServidorRadius);
			   
			   if($resultServidorRadius->num_rows == 0)
			   {
				$resultado = false;
			   }
			   
			   //RESULTADO DA BUSCA POR SERVIDOR RADIUS
			   $rowServidorRadius = $resultServidorRadius->fetch_assoc();
		   
			   //DEFINE IP DO SERVIDOR RADIUS
			   $ipRadius = $rowServidorRadius['IP_FREERADIUS'];
			   
			   //DEFINE USUARIO DO BD DO SERVIDOR RADIUS
			   $userMysql = $rowServidorRadius['USERNAME_MYSQL'];
			   
			   //DEFINE SENHA DO BD DO SERVIDOR RADIUS
			   $senhaMysql = $rowServidorRadius['PASSWORD_MYSQL'];
			   
			   //DEFINE NOME DO BANCO DO BD DO SERVIDOR RADIUS
			   $bancoMysql = $rowServidorRadius['BANCO_DADOS'];
			   
			  

			   //CRIA NOVA CONEXAO
			   $mysqliR = new mysqli($ipRadius,$userMysql,$senhaMysql,$bancoMysql);
			
			   //SETA AUTOCOMMIT FALSE NO SERVIDOR REMOTO
			   $mysqliR->autocommit(false);
				
			   //ATUALIZA SENHA DO USUARIO 		   			   	
			   $queryRadCheckUPsenha = "UPDATE radcheck SET  value='$aC->SENHA' WHERE attribute='Password'AND username='$aC->LOGIN'";
			   $resultRadCheckUPsenha = $mysqliR->query($queryRadCheckUPsenha);
			   
			   //ATUALIZA MAC DO USUARIO
			   $queryRadCheckUPmac = "UPDATE radcheck SET  value='$aC->ENDERECO_MAC' WHERE attribute='Calling-Station-ID' AND username='$aC->LOGIN'";
			   $resultRadCheckUPmac = $mysqliR->query($queryRadCheckUPmac);						
			   
			   if($mysqliR->error)
			   {				
				$resultado = false;
			   }
			   
			   
			}
			
						
			//ATUALIZA ACESSO_CLIENTE
			$query = "UPDATE acesso_cliente SET SENHA='$aC->SENHA', ENDERECO_MAC='$aC->ENDERECO_MAC2' WHERE ID='$aC->ID'";
			$result = $this->conn->query($query);
			
			
			//VERIFICACOES
			if($this->conn->error)
			{
				$this->conn->rollback();
				$this->conn->autocommit(true);
				
				return 'ERRO ALTERAR ACESSO DO CLIENTE: '.$this->conn->error;
			}else{
				if($resultado)
				{
					$this->conn->commit();
					$this->conn->autocommit(true);
					
					$mysqliR->commit();
					$mysqliR->autocommit(true);
					$mysqliR->close();
					
					return 'ok';
				}else{
					$this->conn->rollback();
					$this->conn->autocommit(true);
					
					$mysqliR->rollback();
					$mysqliR->autocommit(true);
					
					return 'ERRO ALTERAR ACESSO DO CLIENTE: '.$this->conn->error.$mysqliR->error;
				}
				
			}
		
	}
	public function cadastrarCredenciaisAcesso(AcessoClienteVO $aC)
	{
		//VARIAVEL CONDICAO
		$resultado = true;
		
		//DESABILITA AUTO COMIT PARA O MYSQL
		$this->conn->autocommit(false);
	
	
		//DEFINE DATA DE CADASTRO
		$aC->DATA_CADASTRO = date('Y-m-d');

		//CONSULTA VIGENCIA DO CONTRATO PELO COD DO CONTRATO INFORMADO
		$queryVigenciaContrato = "SELECT * FROM contratos_acesso WHERE ID = '$aC->ID_CONTRATO'";
		$resultVigenciaContrato = $this->conn->query($queryVigenciaContrato);
		
		//CASO NAO ENCONTRE O CONTRATO, CONDICAO FALHA
		if($resultVigenciaContrato->num_rows == 0)
		{
		    $resultado = false;
		}
		
		//RESULTADO DA CONSULTA PELO CONTRATO
		$rowVigenciaContrato = $resultVigenciaContrato->fetch_assoc();
		
		//VIGENCIA DO CONTRATO
		$vigenciaContrato = $rowVigenciaContrato['VIGENCIA'];
		
		//VALOR DE VIGENCIA DO CONTRATO
		$valorAdesao = $rowVigenciaContrato['VALOR_ADESAO'];
		
		//VALOR DE INSTALACAO DO CONTRATO
		$valorInstalacao = $rowVigenciaContrato['VALOR_INSTALACAO'];
		
		//DEFINIR DATA DE TERMINO DE CONTRATO
		$data = $aC->DATA_CADASTRO;
		$data = explode("-", $data);
    		
		//SOMA A DATA DE CADASTRO + VIGENCIA E OBTEM A DATA DE TERMINO DO CONTRATO
		$newData = date('Y-m-d', mktime(0, 0, 0, $data[1]+$vigenciaContrato, $data[2], $data[0]));
    		
		//DEFINE DATA DE VENC. DO CONTRATO
    		$aC->DATA_VENC_CONTRATO = $newData;
    		
		//DEFINE O IP QUE SERÁ CADASTRADO NA INTERFACE DO SERVIDOR
    		$ipMK1 = substr($aC->ENDERECO_IP, 0, -1);
		$ipMK2 = substr($aC->ENDERECO_IP , -1);
		$ipMK3 = $ipMK2 ;
		$ip2 = $ipMK1.$ipMK3;
		
		
		//CADASTRA NOVO ACESSO DO CLIENTE NA TABELA DE ACESSO_CLIENTE
		$query = "INSERT INTO acesso_cliente (INTERFACE_ID, PLANOS_ACESSO_ID, SERVIDORES_ID, BASES_ID, MATERIAL_ACESSO_ID, 
		CONTRATOS_ACESSO_ID, EMPRESA_ID, CLIENTES_ID, LOGIN, SENHA, ENDERECO_IP, ENDERECO_MAC, DATA_VENC_CONTRATO, DATA_CADASTRO, STATUS_2) VALUES 
		('$aC->ID_INTERFACE', '$aC->ID_PLANO_ACESSO', '$aC->ID_SERVIDOR', '$aC->ID_BASE', '$aC->ID_MATERIAL', '$aC->ID_CONTRATO', 
		'$aC->EMPRESA_ID','$aC->CLIENTES_ID' , '$aC->LOGIN', '$aC->SENHA', '$ip2', '$aC->ENDERECO_MAC', '$aC->DATA_VENC_CONTRATO',
		'$aC->DATA_CADASTRO', 'ATIVO')";
		$result = $this->conn->query($query);
		
		//PROCURA POR CADASTRO DO SERVIDOR PADRAO DA EMPRESA LOGADA
		$queryServidorDefault = "SELECT * FROM servidores WHERE DEFAULT_2 = 1 AND EMPRESA_ID = '$aC->EMPRESA_ID'";
		$resultServidorDefault = $this->conn->query($queryServidorDefault);
		
		//CASO NAO ACHE SERVIDOR PADRAO A CONDICAO FALHA
		if($resultServidorDefault->num_rows == 0)
		{
			$resultado = false;
		}
		
		//RESULTADO DA CONSULTA POR SERVIDOR PADRAO DA EMPRESA
		$rowServidorDefault = $resultServidorDefault->fetch_assoc();
		
		//IP DO SERVIDOR
		$ip = $rowServidorDefault['ENDERECO_IP'];
		
		//USUARIO DO SERVIDOR
		$usuario = $rowServidorDefault['USUARIO'];
		
		//SENHA DO SERVIDOR
		$senha = $rowServidorDefault['SENHA'];
		
		//PORTA DO SERVIDOR
		$porta = $rowServidorDefault['PORTA_API'];
 			
		//CONSULTA INTERFACE ESCOLHIDA 		
		$queryAutenBandaInterface = "SELECT * FROM interface WHERE ID = '$aC->ID_INTERFACE'";
		$resultAutenBandaInterface = $this->conn->query($queryAutenBandaInterface);
		
		//CASO NAO ENCONTRE A INTERFACE A CONDICAO FALHARA
		if($resultAutenBandaInterface->num_rows == 0)
		{
			$resultado = false;
		}
		
		//RESULTADO DA CONSULTA POR INTERFACE		
		$rowAutenBandaInterface = $resultAutenBandaInterface->fetch_assoc();		
		
		//DEFINE NOME INTERFACE
		$nomeInterface =$rowAutenBandaInterface['NOME']; 
		
		//DEFINE SERVIDOR
		$codServidor = $rowAutenBandaInterface['SERVIDORES_ID'];
		
		//DEFINE COD DO SERVIDOR RADIUS
		$codServidorRadius = $rowAutenBandaInterface['SERVIDOR_RADIUS_ID'];		
		
		//DEFINE TIPO DE AUTENTICACAO
		if($rowAutenBandaInterface['HOSTPOT'] == 1)
		{
			$tipoAutenBanda = 1;
		}
		if($rowAutenBandaInterface['PPOE'] == 1)
		{
			$tipoAutenBanda = 2;
		}
		if($rowAutenBandaInterface['SIMPLES_QUEUE'] == 1)
		{
			$tipoAutenBanda = 3;
		}
		if($rowAutenBandaInterface['HOTSPOT_RADIUS'] == 1)
		{
			$tipoAutenBanda = 4;
		}			
		
		
		
		//BUSCA PELO PLANO DE ACESSO ESCOLHIDO
		$queryPegarValorPlano = "SELECT * FROM planos_acesso WHERE ID = '$aC->ID_PLANO_ACESSO'";
		$resultPegarValorPlano = $this->conn->query($queryPegarValorPlano);
		$nRowPegarValorPlano = $resultPegarValorPlano->num_rows;
		
		//CASO NAO ENCONTRE O PLANO DE ACESSO A CONDICAO, FALHA!
		if($nRowPegarValorPlano == 0)
		{
			$resultado = false;
		}
		
		//RESULTADO DA BUSCA POR VALOR DE ACESSO
		$rowPegarValorPlano = $resultPegarValorPlano->fetch_assoc();
		
		//VALOR PLANO DE ACESSO
		$valorPlano = $rowPegarValorPlano['VALOR'];
		
		
		
		//-INICIO-GERACAO DE TITULOS BANCARIOS------------------//	
		//---------------------------------------------//					
		
		//DEFINE A QUANTIDADE DE TITULOS DE ACORDO COM A VIGENCIA DO CONTRATO, ESTIPULADA EM MESES
		$quantidade = $vigenciaContrato;
		
		//DEFINE PRAZO 0
   		$_prazo = 0;   
    		  
		
		//INICIA LACO DE CADASTROS DE TITULOS 		
		for($i=0;$i < $quantidade;$i++)
   		{
		
			//DEFINE SEQUENCIA DE BOLETOS
			$sequencia = $i;
			if($i ==0)
			{
			  $sequencia++;
			}else if($i == $sequencia)
			{
			  $sequencia++;
			}
					
			$sequencia2 = str_pad($sequencia, 2, "0", STR_PAD_LEFT);
			$ano = date('y');

			//PROCURA POR TITULOS JA CADASTRADOS DO MESMO CLIENTE		
			$queryUltimoBoleto = "SELECT * FROM contas_receber WHERE CLIENTES_ID = '$aC->CLIENTES_ID' ORDER by ID DESC";
			$resultUltimoBoleto = $this->conn->query($queryUltimoBoleto);
			$rowUltimoBoleto = $resultUltimoBoleto->fetch_assoc();			
			
			//CASO NAO EXISTAM TITULOS JA CADASTRADOS, INICIA-SE A CONTAGEM DE NOSSO NUMERO DE 000001
			//CASO EXISTAM, ATRIBUI-SE O NUMERO DO ULTIMO+1
			if($resultUltimoBoleto->num_rows == 0)
			{
			    $ultimoNumero = str_pad("1", 6, "0", STR_PAD_LEFT);
			}else
			{
			    $ultimoValor = ltrim(substr($rowUltimoBoleto['N_NUMERO'], -6)+1, "0");
			    $ultimoNumero = str_pad($ultimoValor, 6, "0", STR_PAD_LEFT);    		
			}
			    	
           		//DEFINE SE A FORMATACAO DO NOSSO NUMERO COMPLETA SENDO COD_CLIENTE+SEQUENCIA DE TITULOS
			$NumeroNovo = $aC->CLIENTES_ID.$ultimoNumero;
					
			
			//DATA DO PRIMEIRO INFORMADA NO CADASTRO		
			//$dataPrimeiroBoleto = "10/01/2011";
			$_dia = substr($aC->DATA_BOLETO, 0, 2);
   			$_mes = substr($aC->DATA_BOLETO, 3, 2);
   			$_ano  = substr($aC->DATA_BOLETO, 6, 4);
			
	   		$_ts = mktime(0,0,0,$_mes+$_prazo,$_dia,$_ano);
       			$_data = date('Y-m-d',$_ts);
	   		
			//DEFINE DATA DE EMISSAO DE TITULO
			$emissao = date('Y-m-d');
			
			//DEFINE N_DOC DO TITULO							
			$ndocumento = $aC->CLIENTES_ID.'/'.$ano.'/'.$sequencia2; 
					
					
			//CADASTRA TITULO NA TABELA CONTAS_RECEBER
			$sql = "INSERT INTO contas_receber (N_DOC,CLIENTES_ID,DATA_VENCIMENTO,VALOR_TITULO, STATUS_2, DATA_EMISSAO, N_NUMERO, 
			CONTROLE, EMPRESA_ID) VALUES
			('$ndocumento', '$aC->CLIENTES_ID', '$_data','$valorPlano', 'ABERTO', '$emissao','$NumeroNovo', '$ndocumento', '$aC->EMPRESA_ID')";
					 
			$resultadoSql = $this->conn->query($sql);
        			
        		//INCREMENTA + 1 MES AO PRAZO	
        		$_prazo += 1;
        			
    			
    		}//TERMINA O LACO DE CADASTRAMENTO DE TITULOS	
				        			
    		//-INICIO-CASO O CONTRATO ESCOLHIDO COBRE ADESAO
    		if(strlen($valorAdesao) > 0)
    		{
			//VERIFICA SE EXISTEM TITULOS JA CADASTRADOS DO CLIENTE
    			$queryUltimoBoletoAdesao = "SELECT * FROM contas_receber WHERE CLIENTES_ID = '$aC->CLIENTES_ID' ORDER by ID DESC LIMIT 0,1";
			$resultUltimoBoletoAdesao = $this->conn->query($queryUltimoBoletoAdesao);
			$rowUltimoBoletoAdesao = $resultUltimoBoletoAdesao->fetch_assoc();			

			///CASO NAO EXISTAM TITULOS JA CADASTRADOS, INICIA-SE A CONTAGEM DE NOSSO NUMERO DE 000001
			//CASO EXISTAM, ATRIBUI-SE O NUMERO DO ULTIMO+1
		    	if($resultUltimoBoletoAdesao->num_rows == 0)
			{
				$ultimoNumeroAdesao = str_pad("1", 6, "0", STR_PAD_LEFT);
			}else
			{
				$ultimoValorAdesao = ltrim(substr($rowUltimoBoletoAdesao['N_NUMERO'], -6)+1, "0");
			    	$ultimoNumeroAdesao = str_pad($ultimoValorAdesao, 6, "0", STR_PAD_LEFT);		    		
			}
			    
			//DEFINE O NOSSO NUMERO PARA O TITULO DE ADESAO	
           		$NumeroNovoAdesao = $aC->CLIENTES_ID.$ultimoNumeroAdesao;
           		
			//DEFINE N_DOC NO BOLETO DE ADESAO	
    			$nDocAdesao  = $aC->CLIENTES_ID.'/ADESAO';
			
			//DEFINE VALOR DO CONTRATO DE ADESAO
    			$valorAdesao = $rowVigenciaContrato['VALOR_ADESAO'];
			
			//SE O VALOR DE ADESAO FOR DIFERENTE DE 0,00, GERA O TITULO
    			if($valorAdesao != "0,00")
			{
				$queryAdesao = "INSERT INTO contas_receber (N_DOC,CLIENTES_ID,DATA_VENCIMENTO,VALOR_TITULO, STATUS_2, DATA_EMISSAO, N_NUMERO, 
				CONTROLE, EMPRESA_ID) VALUES
				('$nDocAdesao', '$aC->CLIENTES_ID', '$aC->DATA_CADASTRO','$valorAdesao', 'ABERTO','$aC->DATA_CADASTRO','$NumeroNovoAdesao', '$nDocAdesao', '$aC->EMPRESA_ID')";
			 
				$resultAdesao = $this->conn->query($queryAdesao);
    			}
    		}//-FIM-CASO O CONTRATO ESCOLHIDO COBRE ADESAO
				
		//ATUALIZA STATUS DO CADASTRO DO CLIENTE PARA ATIVO
		$query1 = "UPDATE clientes SET STATUS_2='ATIVO' WHERE ID=$aC->CLIENTES_ID";
		$result1 = $this->conn->query($query1);
				
		//CADASTRA CREDENCIAIS NO RADIUS E ENDERECO IP NO SERVIDOR
			
		//VARIAVEIS ESTANCIADAS API
		$API = new routeros_api();
		$API->port = $porta;					
		$API->debug = true;
				
		//CASO CONSIGA DE CONECTAR AO SERVIDOR
		if ($API->connect($ip, $usuario, $senha))
		{
			//DEFINE O IP DA INTERFACE DO SERVIDOR
			$ipMK1 = substr($aC->ENDERECO_IP, 0, -1);
			$ipMK2 = substr($aC->ENDERECO_IP , -1);
			$ipMK3 = $ipMK2 - 1;
			$ip2 = $ipMK1.$ipMK3;
			
			//ADICIONA INTERFACE PARA O CLIENTE NO SERVIDOR
			$API->write('/ip/address/add',false);
			$API->write('=address=' . $ip2.'/30',false);
			$API->write('=interface=' .  $aC->NOME_INTERFACE, false);
			$API->write('=comment=' . $aC->NOME_RAZAO);
			$API->read();
		
			//CASO UTILIZE HOTSPOT
			if($tipoAutenBanda == 1)
			{								
						
			 //CADASTRA USUARIO NO HOTSPOT
			  $API->write('/ip/hotspot/user/add',false);
			  $API->write('=address=' . $aC->ENDERECO_IP,false);
			  $API->write('=mac-address=' . $aC->ENDERECO_MAC,false);
			  $API->write('=profile=' . $aC->NOME_PLANO_ACESSO,false);
			  $API->write('=server=' . 'sv'.$aC->NOME_INTERFACE,false);
			  $API->write('=name=' . $aC->LOGIN,false);
			  $API->write('=password=' . $aC->SENHA,false);
			  $API->write('=comment=' . $aC->NOME_RAZAO);
			  $API->read();			  			  
			 
			}
			
			//CASO UTILIZE SIMPLES QUEUE
			if($tipoAutenBanda == 3)
			{
				
				$queryPegarValorPlano = "SELECT * FROM planos_acesso WHERE ID = '$aC->ID_PLANO_ACESSO'";
				$resultPegarValorPlano = $this->conn->query($queryPegarValorPlano);
				$nRowPegarValorPlano = $resultPegarValorPlano->num_rows;
				if($nRowPegarValorPlano == 0){return 'ERRO: NENHUM PLANO FOI ENCONTRADO';}
				$rowPegarValorPlano = $resultPegarValorPlano->fetch_assoc();
				$DOWNLOAD = $rowPegarValorPlano['DOWNLOAD'];
				
				
				$ipMK1 = substr($aC->ENDERECO_IP, 0, -1);
				$ipMK2 = substr($aC->ENDERECO_IP , -1);
				$ipMK3 = $ipMK2 + 1;
			
				$ip2 = $ipMK1.$ipMK3;
		
				//CADASTRA CONTROLE DE BANDA NO QUEUE DO SERVIDOR
				$API->write('/queue/simple/add',false);
				$API->write('=name=' . "<".$aC->NOME_INTERFACE."> ".$aC->NOME_RAZAO,false);
				$API->write('=target-addresses=' . $ip2,false);
				$API->write('=max-limit=' . $DOWNLOAD.'/'.$UPLOAD);			
				$API->read();	
				
				//queue simple add name=TESTE target-addresses=18.18.0.2 max-limit=800k/800k
			}
			
			
			 //CASO UTILIZE RADIUS + HOTSPOT
			if($tipoAutenBanda == 4)
			{
				
			   //BUSCAR SERVIDOR RADIUS ASSOCIADO;
			   $queryServidorRadius = "SELECT * FROM servidores_radius WHERE ID = $codServidorRadius";
			   $resultServidorRadius = $this->conn->query($queryServidorRadius);
			   $nRowServidorRadius = $resultServidorRadius->num_rows;
			   if($nRowServidorRadius == 0)
			   {
				$resultado = false;
			   }
			   
			   //RESULTADO DA BUSCA POR SERVIDOR RADIUS
			   $rowServidorRadius = $resultServidorRadius->fetch_assoc();
		   
			   //DEFINE PARAMETROS DO SERVIDOR RADIUS
			   $ipRadius = $rowServidorRadius['IP_FREERADIUS'];
			   $userMysql = $rowServidorRadius['USERNAME_MYSQL'];
			   $senhaMysql = $rowServidorRadius['PASSWORD_MYSQL'];
			   $bancoMysql = $rowServidorRadius['BANCO_DADOS'];

			   //CRIA NOVA CONEXAO PARA SERVIDOR MYSQL-REMOTO
			   $mysqlR = new mysqli($ipRadius,$userMysql,$senhaMysql,$bancoMysql);
			   $mysqlR->autocommit(false);
		   	
			   //DEFINE IP DO CLIENTE	   	
			   $ipMK1 = substr($aC->ENDERECO_IP, 0, -1);			
			   $ipMK2 = substr($aC->ENDERECO_IP , -1);
			   $ipMK3 = $ipMK2;
			   $ip2 = $ipMK1.$ipMK3;
			   	
			   //CADASTRA IP,SENHA,LOGIN E MAC NA TABELA RADCHECK
			   $queryRadCheck = "INSERT INTO radcheck(username,attribute,op,value)VALUES
			   ('$aC->LOGIN', 'Password','==','$aC->SENHA'),
			   ('$aC->LOGIN', 'Calling-Station-ID', '==' , '$aC->ENDERECO_MAC'),
			   ('$aC->LOGIN', 'Framed-IP-Address' , '==' , '$ip2')";				
			   $resultRadCheck = $mysqlR->query($queryRadCheck);
			   		
			   //DEFINE NOME DO PLANO DE ACESSO NO FORMATO COD_INTERFACE+_+NOME DO PLANO DE ACESSO	
			   $nomePlanoRadius = $aC->ID_INTERFACE.'_'.$aC->NOME_PLANO_ACESSO;
				
			   //CADASTRA USUARIO DE PLANO NA TABELA RADUSERGROUP
			   $queryRadUserGroup = "INSERT INTO radusergroup(username,groupname,priority )VALUES
			   ('$aC->LOGIN','$nomePlanoRadius','1')";								
			   $resultRadUserGroup = $mysqlR->query($queryRadUserGroup);
			   	
		   
			   //VERIFCA SE HOUVE ALGUM ERRO DURANTE OS CADASTRO DO RADIUS
			   //CASO HAJA, DESCARTA AS ALTERACOES E FALHA A CONDICAO
			   //CASO NAUM HAJA NENHUM ERRO, PROSSEGUE COM ALTERACOES E LIBERA AUTOCOMMIT PARA TABELA EM QUESTAO
			   if($mysqlR->error)
			   {
				$resultado = false;
			   }
		   		
				
			}
			
			 $API->disconnect();
		}
				
				
		
		//VERIFICA SE HOUVE ALGUM ERRO NA CONEXAO DO BANCO LOCAL		
		if($this->conn->error)
		{
			$this->conn->rollback();
			$this->conn->autocommit(true);
			
			return 'ERRO GERAR CREDENCIAIS DE ACESSO1: '.$this->conn->error;
		}else
		{
			if($resultado == true)
			{
				//COMMITA ALTERACOES NO BANCO LOCAL
				$this->conn->commit();
				$this->conn->autocommit(true);
				
				//COMMITA ALTERACOES NO BANCO REMOTO
				$mysqlR->commit();
				$mysqlR->autocommit(true);
				
				return 'OK';
			}else
			{
				$this->conn->rollback();
				$this->conn->autocommit(true);
				
				//DESCARTA ALTERACOES NO BANCO REMOTO
				$mysqlR->rollback();
				$mysqlR->autocommit(true);
			
				return 'ERRO GERAR CREDENCIAIS DE ACESSO2: '.$this->conn->error;
			}
		}
	}	
	public function checarDisponibilidadeDeIp($ip)
	{
		$query = "SELECT * FROM acesso_cliente WHERE ENDERECO_IP LIKE '$ip'";
		$result = $this->conn->query($query);
		$nRow = $result->num_rows;
		if($nRow > 0)
		{
			return 'IP NAUM DISPONIVEL';
		}
	}
	public function checarDisponibilidadeDeUsuario($usuario)
	{
		$query = "SELECT * FROM acesso_cliente WHERE LOGIN LIKE '$usuario'";
		$result = $this->conn->query($query);
		$nRow = $result->num_rows;
		if($nRow > 0)
		{
			return 'USUARIO NAUM DISPONIVEL';
		}
	}
	public function validateIpAddress($ip_addr)
	{
	  //first of all the format of the ip address is matched
	  if(preg_match("/^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/",$ip_addr))
	  {
	    //now all the intger values are separated
	    $parts=explode(".",$ip_addr);
	    //now we need to check each part can range from 0-255
	    foreach($parts as $ip_parts)
	    {
	      if(intval($ip_parts)>255 || intval($ip_parts)<0)
	      return false; //if number is not within range of 0-255
	    }
	    return true;
	  }
	  else
	    return false; //if format of ip address doesn't matches
	}
	public function macaddress_validation($val) 
	{ 
		return (bool)preg_match('/^([0-9a-fA-F][0-9a-fA-F]:){5}([0-9a-fA-F][0-9a-fA-F])$/',	$val); 
	} 
	public function encerrarContrato(AcessoClienteVO $aC)
	{
		//PROCURAR SERVIDOR;
		$queryServidor = "SELECT * FROM servidores WHERE ID = '$aC->ID_SERVIDOR'";
		$resultServidor = $this->conn->query($queryServidor);
		$nRow = $resultServidor->num_rows;
		if($nRow == 0){return 'SERVIDOR NAUM ENCONTRADO';}
		$rowServidor = $resultServidor->fetch_assoc();
		
		$ip = $rowServidor['ENDERECO_IP'];
		$usuario = $rowServidor['USUARIO'];
		$senha = $rowServidor['SENHA'];
		$porta = $rowServidor['PORTA_API'];
		
		//VARIAVEIS ESTANCIADAS API
		$API = new routeros_api();
		$API->port = $porta;					
		$API->debug = true;
				

		
		//-----VERIFICAR TIPO DE AUTENTICA��O E CONTROLE DE BANDA PARA INTERFACE ESCOLHIDA
		$queryAutenBandaInterface = "SELECT * FROM interface WHERE ID = '$aC->ID_INTERFACE'";
		$resultAutenBandaInterface = $this->conn->query($queryAutenBandaInterface);
		$nRowAutenBandaInterface = $resultAutenBandaInterface->num_rows;
		if($nRowAutenBandaInterface == 0){return 'INTERFACE NAUM ENCONTRADA';}
		
		$rowAutenBandaInterface = $resultAutenBandaInterface->fetch_assoc();
		
		//DEFINE TIPO DE AUTENTICACAO
		if($rowAutenBandaInterface['HOSTPOT'] == 1)
		{
			$tipoAutenBanda = 1;
		}
		if($rowAutenBandaInterface['PPOE'] == 1)
		{
			$tipoAutenBanda = 2;
		}
		if($rowAutenBandaInterface['SIMPLES_QUEUE'] == 1)
		{
			$tipoAutenBanda = 3;
		}
		if($rowAutenBandaInterface['HOTSPOT_RADIUS'] == 1)
		{
			$tipoAutenBanda = 4;
		}
		
		if ($API->connect($ip, $usuario, $senha)) {	
			
			
				
			
			
			//SOMENTE HOTSPOT
			if($tipoAutenBanda == 1)
			{					
			  //DESABILITAR USUARIO NO HOTSPOT;
			  $API->write('/ip/hotspot/user/print',false);
			  $API->write('?name=' . $aC->LOGIN);
			  $userArray = $API->read();
			  $userID = $userArray[0]['.id'];
			  //$comment = $userArray[0]['comment'];
			  
			  $API->write('/ip/hotspot/user/disable',false);
			  $API->write('=numbers=' . $userID);
			 // $API->write('=comment=' . $comment. ' ENCERRADO');
			  $API->read();		 
			  						 
			}
			
			//FREERADIUS + HOTSPOT
			if($tipoAutenBanda == 4)
			{					
				//APAGA CREDENCIAS DE ACESSO DO RADIUS
			 	$queryApagarRadCheck = "DELETE FROM radcheck WHERE username='$aC->LOGIN'";
			 	$resultApagarRadCheck = $this->conn->query($queryApagarRadCheck);

			 	//APAGA REFERENCIA DE PLANO PRA USUARIO DO RADIUS
			 	$queryApagarRadUserGroup = "DELETE FROM radusergroup WHERE username='$aC->LOGIN'";
			 	$resultApagarRadUserGroup = $this->conn->query($queryApagarRadUserGroup);
			}
			 
			
				  //PROCURA INTERFACE
				  $API->write('/ip/address/print',false);
				  $API->write('?comment=' . $aC->NOME_RAZAO);
				  $userArray = $API->read();
				  $userID = $userArray[0]['.id'];
				 				  
				  	//DESABILITA INTERFACE
				  $API->write('/ip/address/disable',false);
				  $API->write('=numbers=' . $userID);				
				  $API->read();	
			
			$API->disconnect();		
		}
		
		//MUDAR STATUS PARA ENCERRADO NO MYSQL
		$queryContratosAcessoClientes = "UPDATE acesso_cliente SET STATUS_2='ENCERRADO' WHERE ID='$aC->ID'";
		$resultContratosAcessoClientes = $this->conn->query($queryContratosAcessoClientes);
		
	}
	
	public function ativarContratoAcesso(AcessoClienteVO $aC)
	{
				
		//PROCURAR SERVIDOR PADR�O DA EMPRESA ATUAL;
		$queryServidor = "SELECT * FROM servidores WHERE DEFAULT_2=1";
		$resultServidor = $this->conn->query($queryServidor);
		$nRow = $resultServidor->num_rows;
		if($nRow == 0){return 'SERVIDOR NAUM ENCONTRADO';}
		$rowServidor = $resultServidor->fetch_assoc();
		
		$ip = $rowServidor['ENDERECO_IP'];
		$usuario = $rowServidor['USUARIO'];
		$senha = $rowServidor['SENHA'];
		$porta = $rowServidor['PORTA_API'];
		
		
		
		//VARIAVEIS ESTANCIADAS API
		$API = new routeros_api();
		$API->port = $porta;					
		$API->debug = true;
				

		//MUDAR STATUS PARA ENCERRADO NO MYSQL
		$queryContratosAcessoClientes = "UPDATE acesso_cliente SET INTERFACE_ID='$aC->ID_INTERFACE', PLANOS_ACESSO_ID='$aC->ID_PLANO_ACESSO', 
		CONTRATOS_ACESSO_ID='$aC->ID_CONTRATO', LOGIN='$aC->LOGIN', 
		SENHA='$aC->SENHA', ENDERECO_IP='$aC->ENDERECO_IP', ENDERECO_MAC='$aC->ENDERECO_MAC', STATUS_2='ATIVO' WHERE ID='$aC->ID'";
		if(!$resultContratosAcessoClientes = $this->conn->query($queryContratosAcessoClientes))
		{
			return $this->conn->error;
		}	
		
		//-----PEGA VIGENCIA DO CONTRATO
		$queryVigenciaContrato = "SELECT * FROM contratos_acesso WHERE ID = '$aC->ID_CONTRATO'";
		$resultVigenciaContrato = $this->conn->query($queryVigenciaContrato);
		$nRowVigenciaContrato = $resultVigenciaContrato->num_rows;
		if($nRowVigenciaContrato == 0){return 'NENHUM CONTRATO FOI ENCONTRADO';}
		$rowVigenciaContrato = $resultVigenciaContrato->fetch_assoc();
		$vigenciaContrato = $rowVigenciaContrato['VIGENCIA'];
		$valorAdesao = $rowVigenciaContrato['VALOR_ADESAO'];
		$valorInstalacao = $rowVigenciaContrato['VALOR_INSTALACAO'];
		
				$quantidade = $vigenciaContrato;
   				$_prazo = 0;   
    		  
				
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

					
					$queryUltimoBoleto = "SELECT * FROM contas_receber WHERE CLIENTES_ID = '$aC->CLIENTES_ID' ORDER by ID DESC";
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
			    	
           			$NumeroNovo = $aC->CLIENTES_ID.$ultimoNumero;
					
					
					//$dataPrimeiroBoleto = "10/01/2011";
					$_dia = substr($aC->DATA_BOLETO, 0, 2);
   					$_mes = substr($aC->DATA_BOLETO, 3, 2);
   					$_ano  = substr($aC->DATA_BOLETO, 6, 4);
			
	   				$_ts = mktime(0,0,0,$_mes+$_prazo,$_dia,$_ano);
       				$_data = date('Y-m-d',$_ts);
	   				
	   				
	  				
					$emissao = date('Y-m-d');
					
					
					
					$ndocumento = $aC->CLIENTES_ID.'/'.$ano.'/'.$sequencia2; 
					
					
					
					//Inserindo
					$sql = "INSERT INTO contas_receber (N_DOC,CLIENTES_ID,DATA_VENCIMENTO,VALOR_TITULO, STATUS_2, DATA_EMISSAO, N_NUMERO, 
					CONTROLE, EMPRESA_ID) VALUES
					('$ndocumento', '$aC->CLIENTES_ID', '$_data','$valorPlano', 'ABERTO', '$emissao','$NumeroNovo', '$ndocumento', '$aC->EMPRESA_ID')";
					 
					$resultadoSql = $this->conn->query($sql);
        			
        			//supondo que o vencimento � de 30 em 30 dias 
					
        			$_prazo += 1;
        			
    			
    			}
				        			
    			//------GERAR BOLETO DE ADESAO CASO HOUVER
    			if(strlen($valorAdesao) > 0)
    			{
    				$queryUltimoBoletoAdesao = "SELECT * FROM contas_receber WHERE CLIENTES_ID = '$aC->CLIENTES_ID' ORDER by ID DESC LIMIT 0,1";
					$resultUltimoBoletoAdesao = $this->conn->query($queryUltimoBoletoAdesao);
					$rowUltimoBoletoAdesao = $resultUltimoBoletoAdesao->fetch_assoc();			
			    	$nRowUltimoBoletoAdesao = $resultUltimoBoletoAdesao->num_rows;
			    	if($nRowUltimoBoletoAdesao == 0)
			    	{
			    		$ultimoNumeroAdesao = str_pad("1", 6, "0", STR_PAD_LEFT);
			    	}else
			    	{
			    		$ultimoValorAdesao = ltrim(substr($rowUltimoBoletoAdesao['N_NUMERO'], -6)+1, "0");
			    		
			    		$ultimoNumeroAdesao = str_pad($ultimoValorAdesao, 6, "0", STR_PAD_LEFT);
			    		
			    	}
			    	
           		$NumeroNovoAdesao = $aC->CLIENTES_ID.$ultimoNumeroAdesao;
           			
    			$nDocAdesao  = $aC->CLIENTES_ID.'/ADESAO';
    			$valorAdesao = $rowVigenciaContrato['VALOR_ADESAO'];
    			$queryAdesao = "INSERT INTO contas_receber (N_DOC,CLIENTES_ID,DATA_VENCIMENTO,VALOR_TITULO, STATUS_2, DATA_EMISSAO, N_NUMERO, 
					CONTROLE, EMPRESA_ID) VALUES
					('$nDocAdesao', '$aC->CLIENTES_ID', '$aC->DATA_CADASTRO','$valorAdesao', 'ABERTO','$aC->DATA_CADASTRO','$NumeroNovoAdesao', '$nDocAdesao', '$aC->EMPRESA_ID')";
					 
				 $resultAdesao = $this->conn->query($queryAdesao);
    			}
		
		
		//-----VERIFICAR TIPO DE AUTENTICA��O E CONTROLE DE BANDA PARA INTERFACE ESCOLHIDA
		$queryAutenBandaInterface = "SELECT * FROM interface WHERE ID = '$aC->ID_INTERFACE'";
		$resultAutenBandaInterface = $this->conn->query($queryAutenBandaInterface);
		$nRowAutenBandaInterface = $resultAutenBandaInterface->num_rows;
		if($nRowAutenBandaInterface == 0){return 'INTERFACE NAUM ENCONTRADA';}
		
		$rowAutenBandaInterface = $resultAutenBandaInterface->fetch_assoc();
		$codServidorRadius = $rowAutenBandaInterface['SERVIDOR_RADIUS_ID'];
		
		//DEFINE TIPO DE AUTENTICACAO
		if($rowAutenBandaInterface['HOSTPOT'] == 1)
		{
			$tipoAutenBanda = 1;
		}
		if($rowAutenBandaInterface['PPOE'] == 1)
		{
			$tipoAutenBanda = 2;
		}
		if($rowAutenBandaInterface['SIMPLES_QUEUE'] == 1)
		{
			$tipoAutenBanda = 3;
		}
		if($rowAutenBandaInterface['HOTSPOT_RADIUS'] == 1)
		{
			$tipoAutenBanda = 4;
		}
		
		if ($API->connect($ip, $usuario, $senha)) {	
			
		
			
			
			//SOMENTE HOTSPOT
			if($tipoAutenBanda == 1)
			{					
			  //HABILITA USUARIO NO HOTSPOT;
			  $API->write('/ip/hotspot/user/print',false);
			  $API->write('?name=' . $aC->LOGIN);
			  $userArray = $API->read();
			  $userID = $userArray[0]['.id'];
			  //$comment = $userArray[0]['comment'];
			  
			  $API->write('/ip/hotspot/user/enable',false);
			  $API->write('=numbers=' . $userID);
			 // $API->write('=comment=' . $comment. ' ENCERRADO');
			  $API->read();		 
			  						 
			}
			
			//FREERADIUS + HOTSPOT
			if($tipoAutenBanda == 4)
			{					
				//BUSCAR SERVIDOR RADIUS ASSOCIADO;
			   $queryServidorRadius = "SELECT * FROM servidores_radius WHERE ID=$codServidorRadius";
			   $resultServidorRadius = $this->conn->query($queryServidorRadius);
			   $nRowServidorRadius = $resultServidorRadius->num_rows;
			   if($nRowServidorRadius == 0){return 'ERRO: SERVIDOR RADIUS N�O ENCONTRADO';}
			   $rowServidorRadius = $resultServidorRadius->fetch_assoc();
		   
			   //DEFINE PARAMETROS DO SERVIDOR RADIUS
			   $ipRadius = $rowServidorRadius['IP_FREERADIUS'];
			   $userMysql = $rowServidorRadius['USERNAME_MYSQL'];
			   $senhaMysql = $rowServidorRadius['PASSWORD_MYSQL'];
			   $bancoMysql = $rowServidorRadius['BANCO_DADOS'];
			   
			   

		   //CONECTA AO SERVIDOR MYSQL REMOTO DO RADIUS
		   if($con = mysql_pconnect($ipRadius, $userMysql, $senhaMysql))
		   {
		   	
			   	//CADASTRA USU�RIO,SENHA,IP E MAC NA TABELA MYSQL DO RADIUS			   	
			   	$ipMK1 = substr($aC->ENDERECO_IP, 0, -1);
				$ipMK2 = substr($aC->ENDERECO_IP , -1);
				$ipMK3 = $ipMK2;
			
				$ip2 = $ipMK1.$ipMK3;
			   	
				$queryRadCheck = "INSERT INTO ".$bancoMysql.".radcheck(username,attribute,op,value)VALUES
				('$aC->LOGIN', 'Password','==','$aC->SENHA'),
				('$aC->LOGIN', 'Calling-Station-ID', '==' , '$aC->ENDERECO_MAC'),
				('$aC->LOGIN', 'Framed-IP-Address' , '==' , '$ip2')";			
				
				if(!$resultRadCheck = $this->conn->query($queryRadCheck, $con))
			   	{
			   		return 'ERRO: '.$this->conn->error;
			   	}	
				
				$nomePlanoRadius = $aC->ID_INTERFACE.'_'.$aC->NOME_PLANO_ACESSO;
				
				//CADASTRA USUARIO NO PLANO DO RADIUS
				$queryRadUserGroup = "INSERT INTO ".$bancoMysql.".radusergroup(username,groupname,priority )VALUES
				('$aC->LOGIN','$nomePlanoRadius','1')";				
								
				if(!$resultRadUserGroup = $this->conn->query($queryRadUserGroup, $con))
			   	{
			   		return 'ERRO: '.$this->conn->error;
			   	}		
		   
			   	
		   }else{
		   		return 'ERRO: '.$this->conn->error;
		   }
		   		//FECHA CONEX�O REMOTA
		   		mysql_close($con);
			//-------CONECTA AO SERVIDOR MYSQL REMOTO DO RADIUS

		   		
			}//-------FREERADIUS+HOTSPOT---FIM
			
			 			
				  //PROCURA INTERFACE
				  $API->write('/ip/address/print',false);
				  $API->write('?comment=' . $aC->NOME_RAZAO);
				  $userArray = $API->read();
				  $userID = $userArray[0]['.id'];
				 				  
				  	//DESABILITA INTERFACE
				  $API->write('/ip/address/enable',false);
				  $API->write('=numbers=' . $userID);				
				  $API->read();	
			
			$API->disconnect();	

			
		}
		
			
		
		return 'ok';
	}
	
	
	
	
	public function selecionarCrendencialAcesso($cod)
	{
		
		
		$query = "SELECT acesso_cliente.ID,acesso_cliente.EMPRESA_ID, acesso_cliente.CLIENTES_ID, acesso_cliente.LOGIN, acesso_cliente.SENHA, 
			acesso_cliente.ENDERECO_IP, acesso_cliente.ENDERECO_MAC,  
			clientes.NOME_RAZAO,
			interface.ID as ID_INTERFACE,interface.NOME as NOME_INTERFACE, 
			planos_acesso.ID as ID_PLANO_ACESSO,planos_acesso.NOME as NOME_PLANO_ACESSO,
			servidores.ID as ID_SERVIDOR, servidores.NOME_SERVER, 
			bases.ID as ID_BASE, bases.NOME as NOME_BASE, 
			material_acesso.ID as ID_MATERIAL,material_acesso.NOME as NOME_MATERIAL , 
			contratos_acesso.ID as ID_CONTRATO, contratos_acesso.NOME as NOME_CONTRATO
					FROM acesso_cliente
			    LEFT JOIN clientes ON clientes.ID = acesso_cliente.CLIENTES_ID
			    LEFT JOIN interface ON interface.ID = acesso_cliente.INTERFACE_ID 
			    LEFT JOIN planos_acesso ON planos_acesso.ID = acesso_cliente.PLANOS_ACESSO_ID
			    LEFT JOIN servidores ON servidores.ID = acesso_cliente.SERVIDORES_ID
			    LEFT JOIN bases ON bases.ID = acesso_cliente.BASES_ID    
			    LEFT JOIN material_acesso ON material_acesso.ID = acesso_cliente.MATERIAL_ACESSO_ID
			    LEFT JOIN contratos_acesso ON contratos_acesso.ID = acesso_cliente.CONTRATOS_ACESSO_ID
			    WHERE acesso_cliente.ID = '$cod'";
		
		
		
		
		
		$result = $this->conn->query($query);
		$nrow = $result->num_rows;
		if($nrow == 0){return 'ERRO';}
		
		$row = $result->fetch_assoc();
			
			$acessoCliente = new AcessoClienteVO();
			
			$acessoCliente->ID = $row['ID'];
			$acessoCliente->EMPRESA_ID = $row['EMPRESA_ID'];
			$acessoCliente->CLIENTES_ID = $row['CLIENTES_ID'];
			$acessoCliente->LOGIN = $row['LOGIN'];
			$acessoCliente->SENHA = $row['SENHA'];
			$acessoCliente->ENDERECO_IP = $row['ENDERECO_IP'];
			$acessoCliente->ENDERECO_MAC = $row['ENDERECO_MAC'];
			$acessoCliente->NOME_RAZAO = $row['NOME_RAZAO'];
			$acessoCliente->ID_INTERFACE = $row['ID_INTERFACE'];
			$acessoCliente->NOME_INTERFACE = $row['NOME_INTERFACE'];
			$acessoCliente->ID_PLANO_ACESSO = $row['ID_PLANO_ACESSO'];
			$acessoCliente->NOME_PLANO_ACESSO = $row['NOME_PLANO_ACESSO'];
			$acessoCliente->ID_SERVIDOR = $row['ID_SERVIDOR'];
			$acessoCliente->NOME_SERVER = $row['NOME_SERVER'];
			$acessoCliente->ID_BASE = $row['ID_BASE'];
			$acessoCliente->NOME_BASE = $row['NOME_BASE'];
			$acessoCliente->ID_MATERIAL = $row['ID_MATERIAL'];
			$acessoCliente->NOME_MATERIAL = $row['NOME_MATERIAL'];
			$acessoCliente->ID_CONTRATO = $row['ID_CONTRATO'];
			$acessoCliente->NOME_CONTRATO = $row['NOME_CONTRATO'];
		
		
		return $acessoCliente;
		
	}	
	public function mudarTitulariaContrato($idNovoTitular, $idContrato)
	{
		
		
		//MUDA COMENTARIO DE INTERFACE NO MIKROTIK
		$queryInfoContrato = "SELECT acesso_cliente.ID,acesso_cliente.EMPRESA_ID, acesso_cliente.CLIENTES_ID, acesso_cliente.LOGIN, acesso_cliente.SENHA, 
			acesso_cliente.ENDERECO_IP, acesso_cliente.ENDERECO_MAC,  acesso_cliente.DATA_VENC_CONTRATO, acesso_cliente.DATA_CADASTRO, 
			acesso_cliente.STATUS_2,
			clientes.NOME_RAZAO,
			interface.ID as ID_INTERFACE,interface.NOME as NOME_INTERFACE, 
			planos_acesso.ID as ID_PLANO_ACESSO,planos_acesso.NOME as NOME_PLANO_ACESSO,
			servidores.ID as ID_SERVIDOR, servidores.NOME_SERVER, 
			bases.ID as ID_BASE, bases.NOME as NOME_BASE, 
			material_acesso.ID as ID_MATERIAL,material_acesso.NOME as NOME_MATERIAL , 
			contratos_acesso.ID as ID_CONTRATO, contratos_acesso.NOME as NOME_CONTRATO
					FROM acesso_cliente
			    LEFT JOIN clientes ON clientes.ID = acesso_cliente.CLIENTES_ID
			    LEFT JOIN interface ON interface.ID = acesso_cliente.INTERFACE_ID 
			    LEFT JOIN planos_acesso ON planos_acesso.ID = acesso_cliente.PLANOS_ACESSO_ID
			    LEFT JOIN servidores ON servidores.ID = acesso_cliente.SERVIDORES_ID
			    LEFT JOIN bases ON bases.ID = acesso_cliente.BASES_ID    
			    LEFT JOIN material_acesso ON material_acesso.ID = acesso_cliente.MATERIAL_ACESSO_ID
			    LEFT JOIN contratos_acesso ON contratos_acesso.ID = acesso_cliente.CONTRATOS_ACESSO_ID
			    WHERE acesso_cliente.ID = '$idContrato'";
		$resultInfoContrato = $this->conn->query($queryInfoContrato);
		$nRowInfoContrato = $resultInfoContrato->num_rows;
		if($nRowInfoContrato >0)
		{
			$rowInforContrato = $resultInfoContrato->fetch_assoc();
			
			$acessoCliente = new AcessoClienteVO();
			
			$dataHoje = date('Y-m-d');
			$dataAtual = explode('-', $dataHoje);
			$dataVencimento = explode('-', $row['DATA_VENC_CONTRATO']);
			
			$dataAtual = mktime(0,0,0,$dataAtual[1],$dataAtual[2],$dataAtual[0]);
			$dataBoleto = mktime(0,0,0,$dataVencimento[1],$dataVencimento[2],$dataVencimento[0]);  
			$d3 = ($dataBoleto-$dataAtual);
			$dias = round(($d3/60/60/24));
			
			$acessoCliente->ID = $rowInforContrato['ID'];
			$acessoCliente->EMPRESA_ID = $rowInforContrato['EMPRESA_ID'];
			$acessoCliente->CLIENTES_ID = $rowInforContrato['CLIENTES_ID'];
			$acessoCliente->LOGIN = $rowInforContrato['LOGIN'];
			$acessoCliente->SENHA = $rowInforContrato['SENHA'];
			$acessoCliente->ENDERECO_IP = $rowInforContrato['ENDERECO_IP'];
			$acessoCliente->ENDERECO_MAC = $rowInforContrato['ENDERECO_MAC'];
			$acessoCliente->NOME_RAZAO = $rowInforContrato['NOME_RAZAO'];
			$acessoCliente->ID_INTERFACE = $rowInforContrato['ID_INTERFACE'];
			$acessoCliente->NOME_INTERFACE = $rowInforContrato['NOME_INTERFACE'];
			$acessoCliente->ID_PLANO_ACESSO = $rowInforContrato['ID_PLANO_ACESSO'];
			$acessoCliente->NOME_PLANO_ACESSO = $rowInforContrato['NOME_PLANO_ACESSO'];
			$acessoCliente->ID_SERVIDOR = $rowInforContrato['ID_SERVIDOR'];
			$acessoCliente->NOME_SERVER = $rowInforContrato['NOME_SERVER'];
			$acessoCliente->ID_BASE = $rowInforContrato['ID_BASE'];
			$acessoCliente->NOME_BASE = $rowInforContrato['NOME_BASE'];
			$acessoCliente->ID_MATERIAL = $rowInforContrato['ID_MATERIAL'];
			$acessoCliente->NOME_MATERIAL = $rowInforContrato['NOME_MATERIAL'];
			$acessoCliente->ID_CONTRATO = $rowInforContrato['ID_CONTRATO'];
			$acessoCliente->NOME_CONTRATO = $rowInforContrato['NOME_CONTRATO'];
			$acessoCliente->DATA_VENC_CONTRATO = $rowInforContrato['DATA_VENC_CONTRATO'];
			$acessoCliente->DATA_CADASTRO = $rowInforContrato['DATA_CADASTRO'];
			$acessoCliente->DIAS_CONTRATO = $dias;
			
			//PEGA DADOS SERVIDOR 
			$queryBuscarServidor = "SELECT * FROM servidores WHERE ID = '$acessoCliente->ID_SERVIDOR'";
			$resultBuscarServidor = $this->conn->query($queryBuscarServidor);
			$nRowBuscarServidor = $resultBuscarServidor->num_rows;
			if($nRowBuscarServidor >0)
			{
				$rowBuscarServidor = $resultBuscarServidor->fetch_assoc();
				
				$servidor = new ServidoresVO();
				$servidor->ID = $rowBuscarServidor['ID'];
				$servidor->EMPRESA_ID = $rowBuscarServidor['EMPRESA_ID'];
				$servidor->NOME_SERVER = $rowBuscarServidor['NOME_SERVER'];
				$servidor->ENDERECO_IP = $rowBuscarServidor['ENDERECO_IP'];
				$servidor->USUARIO = $rowBuscarServidor['USUARIO'];
				$servidor->SENHA = $rowBuscarServidor['SENHA'];
				$servidor->PORTA_API = $rowBuscarServidor['PORTA_API'];
				$servidor->DEFAULT_2 = $rowBuscarServidor['DEFAULT_2'];
				
					//VARIAVEIS ESTANCIADAS API
					$API = new routeros_api();
					$API->port = $servidor->PORTA_API;					
					$API->debug = true;
					
					//PEGAR CONFIGURACOES DA INTERFACE
					$querAutenBandaInterface = "SELECT * FROM interface WHERE ID = '$acessoCliente->ID_INTERFACE'";
					$resultAutenBandaInterface = $this->conn->query($querAutenBandaInterface);
					$nRowAutenBandaInterface = $resultAutenBandaInterface->num_rows;
					if($nRowAutenBandaInterface >0){
						$rowAutenBandaInterface = $resultAutenBandaInterface->fetch_assoc();
					}else {
						return 'INTERFACE N ENCONTRADA';
					}
					
					//DEFINE TIPO DE AUTENTICACAO
					if($rowAutenBandaInterface['HOSTPOT'] == 1)
					{
						$tipoAutenBanda = 1;
					}
					if($rowAutenBandaInterface['PPOE'] == 1)
					{
						$tipoAutenBanda = 2;
					}
					if($rowAutenBandaInterface['SIMPLES_QUEUE'] == 1)
					{
						$tipoAutenBanda = 3;
					}
					if($rowAutenBandaInterface['HOTSPOT_RADIUS'] == 1)
					{
						$tipoAutenBanda = 4;
					}
		
		
			
							
			  	
				
				if ($API->connect($servidor->ENDERECO_IP, $servidor->USUARIO, $servidor->SENHA)) {

					//BUSCA NOME DO NOVO TITULAR
					$queryNomeRazao = "SELECT * FROM clientes WHERE ID = '$idNovoTitular'";
					$resultNomeRazao = $this->conn->query($queryNomeRazao);
					$rowNomeRazao = $resultNomeRazao->fetch_assoc();
					
					
					//PROCURA INTERFACE
				    $API->write('/ip/address/print',false);
				    $API->write("?comment=".$acessoCliente->NOME_RAZAO);
				    $userArray = $API->read();
				    $userID = $userArray[0]['.id'];
				    
				  	//MUDA COMENTARIO DA INTERFACE DO CLIENTE
				    $API->write('/ip/address/set',false);
				    $API->write("=comment=".$rowNomeRazao['NOME_RAZAO'],false);
				    $API->write('=numbers='.$userID);				
				    $API->read();	
				    
				   // ip address set comment=MARCOS numbers=*9
				    
					
					//CASO SEJA HOTSPOT
				     if($tipoAutenBanda == 1)
					{
						//PROCURA POR USUARIO NO HOTSPOT
					  $API->write('/ip/hotspot/user/print',false);
					  $API->write('?comment=' . $aC->NOME_RAZAO);
					  $userArray = $API->read();
					  $userID = $userArray[0]['.id'];
					 				  
					  	//MUDA COMENTARIO DA INTERFACE DO CLIENTE
					  $API->write('/ip/hotspot/user/set',false);
					  $API->write('=comment=' . $rowNomeRazao['NOME_RAZAO']);
					  $API->write('=numbers=' . $userID);				
					  $API->read();	
					}
					

					 $API->disconnect(); 
					 
				}else
				{
					return 'N FOI POSSIVEL CONECTAR AO SERVER';
				}	

				//MUDA CADASTRO ACESSO_CLIENTE
				$query = "UPDATE acesso_cliente SET CLIENTES_ID='$idNovoTitular' WHERE ID='$idContrato'";
				$result = $this->conn->query($query);
			
			}else{
				return 'SERVIDOR N ENCONTRADO';
			}
			}else {
				return 'CONTRATO N ENCONTRADO';
			} 		
		
		
		
		
	}
	public function TrocarPlanoAcesso($idInterface, $idPlano, $idContrato,$idServidor, $login, $idCliente)
	{
		//--------TROCAR PLANO NA BASE MYSQL
		$query = "UPDATE acesso_cliente SET INTERFACE_ID='$idInterface', PLANOS_ACESSO_ID='$idPlano' WHERE ID='$idContrato'";
		$result = $this->conn->query($query);
		
		//-----PEGAR NOME DO PLANO
		$queryNovoPlano = "SELECT * FROM planos_acesso WHERE ID = '$idPlano'";
		$resultNovoPlano = $this->conn->query($queryNovoPlano);
		$nRowNovoPlano = $resultNovoPlano->num_rows;
		if($nRowNovoPlano == 0){return 'NENHUM PLANO ENCONTRADO';}
		$rowNovoPlano = $resultNovoPlano->fetch_assoc();
		
		
		$N_DOC = $idCliente."/".date('y');
		$VALOR_TITULO = $rowNovoPlano['VALOR'];
		//-----ALTERAR VALOR DE TITULOS
		$queryAlterarTitulos = "UPDATE contas_receber SET VALOR_TITULO='$VALOR_TITULO'
		WHERE CLIENTES_ID = '$idCliente' AND STATUS_2 = 'ABERTO' AND N_DOC LIKE  '%$N_DOC%'";
		$resultAlterarTitulos = $this->conn->query($queryAlterarTitulos);
		
		
		//---------ALTERAR NO SERVIDOR;
		$queryServidor = "SELECT * FROM servidores WHERE ID = '$idServidor'";
		$resultServidor = $this->conn->query($queryServidor);
		$nRow = $resultServidor->num_rows;
		if($nRow == 0){return 'SERVIDOR NAUM ENCONTRADO';}
		$rowServidor = $resultServidor->fetch_assoc();
		
		$ip = $rowServidor['ENDERECO_IP'];
		$usuario = $rowServidor['USUARIO'];
		$senha = $rowServidor['SENHA'];
		$porta = $rowServidor['PORTA_API'];
		
		//VARIAVEIS ESTANCIADAS API
		$API = new routeros_api();
		$API->port = $porta;					
		$API->debug = true;	
		
		
		//-----VERIFICAR TIPO DE AUTENTICA��O E CONTROLE DE BANDA PARA INTERFACE ESCOLHIDA
		$queryAutenBandaInterface = "SELECT * FROM interface WHERE ID = '$idInterface'";
		$resultAutenBandaInterface = $this->conn->query($queryAutenBandaInterface);
		$nRowAutenBandaInterface = $resultAutenBandaInterface->num_rows;
		if($nRowAutenBandaInterface == 0){return 'INTERFACE NAUM ENCONTRADA';}
		
		$rowAutenBandaInterface = $resultAutenBandaInterface->fetch_assoc();
		
		//DEFINE TIPO DE AUTENTICACAO
		if($rowAutenBandaInterface['HOSTPOT'] == 1)
		{
			$tipoAutenBanda = 1;
		}
		if($rowAutenBandaInterface['PPOE'] == 1)
		{
			$tipoAutenBanda = 2;
		}
		if($rowAutenBandaInterface['SIMPLES_QUEUE'] == 1)
		{
			$tipoAutenBanda = 3;
		}
		if($rowAutenBandaInterface['HOTSPOT_RADIUS'] == 1)
		{
			$tipoAutenBanda = 4;
		}
		
			if ($API->connect($ip, $usuario, $senha)) {					
		
				if($tipoAutenBanda == 1)
				{				
					  //PROCURA USUARIO NO HOTSPOT;
					  $API->write('/ip/hotspot/user/print',false);
					  $API->write('?name=' . $login);
					  $userArray = $API->read();
					  $userID = $userArray[0]['.id'];
					  //$comment = $userArray[0]['comment'];
					  
					  //ALTERA PLANO
					  $API->write('/ip/hotspot/user/set',false);
					  $API->write('=profile=' . $rowNovoPlano['NOME'], false);
					  $API->write('=numbers=' . $userID);
					 
					  $API->read();		  						 
				}	
				if($tipoAutenBanda == 4)
				{
					$nomePlanoRadius = $rowAutenBandaInterface['ID'].'_'.$rowNovoPlano['NOME'];
					//ALTERAR PLANO NA TABELA RADIUS
					$queryRadUserGroup = "UPDATE radusergroup SET groupname = '$nomePlanoRadius' WHERE username = '$login'";
					$resultRadUserGroup = $this->conn->query($queryRadUserGroup);
				}
				
			  $API->disconnect();
		}
		return 'ok';
	}
	public function renovarPlanoAcesso($idInterface, $idPlano, $idContrato, $idCliente, $datBoleto)
	{
		//return $idCliente;
		//BUSCA BOLETOS ABERTOS;
		$queryBoletosAbertos = "SELECT * FROM contas_receber WHERE CLIENTES_ID = '$idCliente' AND STATUS_2 = 'ABERTO'";
		$resultBoletosAbertos = $this->conn->query($queryBoletosAbertos);
		$nRowBoletosAbertos = $resultBoletosAbertos->num_rows;
		//if($nRowBoletosAbertos > 0){return 'EXISTEM BOLETOS EM ABERTO';}
		
		//return 'TESTE';
		
		//LISTAR ACESSO CLIENTE
		$queryAcessoCliente = "SELECT * FROM acesso_cliente WHERE CLIENTES_ID = '$idCliente'";
		$resultAcessoCliente = $this->conn->query($queryAcessoCliente);
		$nRowAcessoCliente = $resultAcessoCliente->num_rows;
		if($nRowAcessoCliente == 0){return 'CONTRATO NAUM FOI ENCONTRADO';}
		
		$rowAcessoCliente = $resultAcessoCliente->fetch_assoc();
			
			$aC = new AcessoClienteVO();
			$aC->ID = $rowAcessoCliente['ID'];
			$aC->EMPRESA_ID = $rowAcessoCliente['EMPRESA_ID'];
			$aC->CLIENTES_ID = $rowAcessoCliente['CLIENTES_ID'];
			$aC->LOGIN = $rowAcessoCliente['LOGIN'];
			$aC->SENHA = $rowAcessoCliente['SENHA'];
			$aC->ENDERECO_IP = $rowAcessoCliente['ENDERECO_IP'];
			$aC->ENDERECO_MAC = $rowAcessoCliente['ENDERECO_MAC'];
			$aC->NOME_RAZAO = $rowAcessoCliente['NOME_RAZAO'];
			$aC->ID_INTERFACE = $rowAcessoCliente['ID_INTERFACE'];
			$aC->NOME_INTERFACE = $rowAcessoCliente['NOME_INTERFACE'];
			$aC->ID_PLANO_ACESSO = $rowAcessoCliente['ID_PLANO_ACESSO'];
			$aC->NOME_PLANO_ACESSO = $rowAcessoCliente['NOME_PLANO_ACESSO'];
			$aC->ID_SERVIDOR = $rowAcessoCliente['ID_SERVIDOR'];
			$aC->NOME_SERVER = $rowAcessoCliente['NOME_SERVER'];
			$aC->ID_BASE = $rowAcessoCliente['ID_BASE'];
			$aC->NOME_BASE = $rowAcessoCliente['NOME_BASE'];
			$aC->ID_MATERIAL = $rowAcessoCliente['ID_MATERIAL'];
			$aC->NOME_MATERIAL = $rowAcessoCliente['NOME_MATERIAL'];
			$aC->ID_CONTRATO = $rowAcessoCliente['ID_CONTRATO'];
			$aC->NOME_CONTRATO = $rowAcessoCliente['NOME_CONTRATO'];
			$aC->DATA_VENC_CONTRATO = $rowAcessoCliente['DATA_VENC_CONTRATO'];
			$aC->DATA_CADASTRO = $rowAcessoCliente['DATA_CADASTRO'];
			//$aC->DIAS_CONTRATO = $dias;
		
		//PROCESSO DE RENOVA��O

		//$aC->DATA_VENC_CONTRATO = date('Y-m-d');

		//-----PEGA VIGENCIA DO CONTRATO
		$queryVigenciaContrato = "SELECT * FROM contratos_acesso WHERE ID = '$idContrato'";
		$resultVigenciaContrato = $this->conn->query($queryVigenciaContrato);
		$nRowVigenciaContrato = $resultVigenciaContrato->num_rows;
		if($nRowVigenciaContrato == 0){return 'NENHUM CONTRATO FOI ENCONTRADO';}
		$rowVigenciaContrato = $resultVigenciaContrato->fetch_assoc();
		$vigenciaContrato = $rowVigenciaContrato['VIGENCIA'];
		$valorAdesao = $rowVigenciaContrato['VALOR_ADESAO'];
		$valorInstalacao = $rowVigenciaContrato['VALOR_INSTALACAO'];
		$nomeContrato = $rowVigenciaContrato['NOME'];
		
		//DEFINIR DATA DE TERMINO DE CONTRATO		

			$data = date('Y-m-d');
			$data = explode("-", $data);
    		$newData = date('Y-m-d', mktime(0, 0, 0, $data[1]+$vigenciaContrato, $data[2], $data[0]));
    		
    		$aC->DATA_VENC_CONTRATO = $newData;
		
			
		
		//-----PEGA NO DO PLANO DE ACESSO
		$query22 = "SELECT * FROM planos_acesso WHERE ID=$idPlano";
		if(!$result22 = $this->conn->query($query22))
		{
			return 'RENOVACAO DE CONTRATO: NAO FOI POSSIVEL REALIZAR CONSULTA DE PLANO DE ACESSO '.$this->conn->error;
		}
		$nRow22 = $result22->num_rows;
		if($nRow22 == 0)
		{
			return 'RENOVACAO DE CONTRATO: NAO FOI POSSIVEL ENCONTRAR PLANO DE ACESSO, RENOVACAO CANCELADA ';
		}
		$row22 = $result22->fetch_assoc();
		$nomePlano = $row22['NOME'];
		
		//-----ALTERA ACESSO_CLIENTE
		$query = "UPDATE acesso_cliente SET INTERFACE_ID='$idInterface', PLANOS_ACESSO_ID='$idPlano', CONTRATOS_ACESSO_ID='$idContrato', 
		DATA_VENC_CONTRATO='$aC->DATA_VENC_CONTRATO' WHERE ID='$aC->ID'";
		$result = $this->conn->query($query);
 			
		//-----VERIFICAR TIPO DE AUTENTICA��O E CONTROLE DE BANDA PARA INTERFACE ESCOLHIDA
		$queryAutenBandaInterface = "SELECT * FROM interface WHERE ID = '$idInterface'";
		$resultAutenBandaInterface = $this->conn->query($queryAutenBandaInterface);
		$nRowAutenBandaInterface = $resultAutenBandaInterface->num_rows;
		if($nRowAutenBandaInterface == 0){return 'INTERFACE NAUM ENCONTRADA';}
		
		$rowAutenBandaInterface = $resultAutenBandaInterface->fetch_assoc();
		
		//DEFINE TIPO DE AUTENTICACAO
		if($rowAutenBandaInterface['HOSTPOT'] == 1)
		{
			$tipoAutenBanda = 1;
		}
		if($rowAutenBandaInterface['PPOE'] == 1)
		{
			$tipoAutenBanda = 2;
		}
		if($rowAutenBandaInterface['SIMPLES_QUEUE'] == 1)
		{
			$tipoAutenBanda = 3;
		}
		if($rowAutenBandaInterface['HOTSPOT_RADIUS'] == 1)
		{
			$tipoAutenBanda = 4;
		}
		
		$idServidor = $rowAutenBandaInterface['SERVIDORES_ID'];
		
		//-----SERVIDOR DEFAULT
		$queryServidorDefault = "SELECT * FROM servidores WHERE ID = '$idServidor'";
		$resultServidorDefault = $this->conn->query($queryServidorDefault);
		$nRowServidorDefault = $resultServidorDefault->num_rows;
		if($nRowServidorDefault == 0){return 'NENHUM SERVIDOR PADRAO FOI ENCONTRADO';}
		
		$rowServidorDefault = $resultServidorDefault->fetch_assoc();
		
		$ip = $rowServidorDefault['ENDERECO_IP'];
		$usuario = $rowServidorDefault['USUARIO'];
		$senha = $rowServidorDefault['SENHA'];
		$porta = $rowServidorDefault['PORTA_API'];
		
		$queryPegarValorPlano = "SELECT * FROM planos_acesso WHERE ID = '$idPlano'";
		$resultPegarValorPlano = $this->conn->query($queryPegarValorPlano);
		$nRowPegarValorPlano = $resultPegarValorPlano->num_rows;
		if($nRowPegarValorPlano == 0){return 'NENHUM PLANO FOI ENCONTRADO';}
		$rowPegarValorPlano = $resultPegarValorPlano->fetch_assoc();
		$valorPlano = $rowPegarValorPlano['VALOR'];
		$nomePlanoHotspot = $rowPegarValorPlano['NOME'];
		
		//VARIAVEIS ESTANCIADAS API
		$API = new routeros_api();
		$API->port = $porta;					
		$API->debug = true;
				

		if ($API->connect($ip, $usuario, $senha)) {
			
			 //ADICIONAR IP INTERFACE PARA O CLIENTE;
//			  $API->write('/ip/address/add',false);
//			  $API->write('=address=' . $aC->ENDERECO_IP.'/30',false);
//			  $API->write('=interface=' .  $aC->NOME_INTERFACE, false);
//			  $API->write('=comment=' . $aC->NOME_RAZAO);
//			  $API->read();
		
			  //CASO UTILIZE HOTSPOT
			  $nomePlanoRadius = $idInterface.'_'.$nomePlano;
			 
			  
			if($tipoAutenBanda == 1)
			{	
				//PROCURA USUARIO NO HOTSPOT;
				$API->write('/ip/hotspot/user/print',false);
				$API->write('?name=' . $aC->LOGIN);
				$userArray = $API->read();
				$userID = $userArray[0]['.id'];
					  						
						
			 	//EDITAR USUARIO NO HOTSPOT
			  	$API->write('/ip/hotspot/user/set',false);
			  	$API->write('=profile=' . $nomePlanoHotspot,false);
			  	$API->write('=numbers=' . $userID);
			 	$API->read();

			 	
			 	
			}		
			
			 //CASO UTILIZE RADIUS + HOTSPOT
			if($tipoAutenBanda == 4)
			{			
				//CADASTRA USUARIO NO PLANO DO RADIUS
				$queryRadUserGroup = "UPDATE radusergroup SET groupname='$nomePlanoRadius' WHERE username = '$aC->LOGIN'";			
				$resultRadUserGroup = $this->conn->query($queryRadUserGroup);
			}
			
			
			
			 $API->disconnect();
		}
		
		
		
		
		
		
		//------------GERAR TITULOS NO FINANCEIRO DA EMPRESA E CLIENTES
	
						
							
				$quantidade = $vigenciaContrato;
   				$_prazo = 0;   
    		  
				
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

					
					$queryUltimoBoleto = "SELECT * FROM contas_receber WHERE CLIENTES_ID = '$idCliente' ORDER by ID DESC";
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
			    	
           			$NumeroNovo = $idCliente.$ultimoNumero;
					
					
					//$dataPrimeiroBoleto = "10/01/2011";
					$_dia = substr($datBoleto, 0, 2);
   					$_mes = substr($datBoleto, 3, 2);
   					$_ano  = substr($datBoleto, 6, 4);
			
	   				$_ts = mktime(0,0,0,$_mes+$_prazo,$_dia,$_ano);
       				$_data = date('Y-m-d',$_ts);
	   				
	   				
	  				
					$emissao = date('Y-m-d');
					
					
					
					$ndocumento = $idCliente.'/'.$ano.'/'.$sequencia2; 
					
					
					
					//Inserindo
					$sql = "INSERT INTO contas_receber (N_DOC,CLIENTES_ID,DATA_VENCIMENTO,VALOR_TITULO, STATUS_2, DATA_EMISSAO, N_NUMERO, 
					CONTROLE, EMPRESA_ID) VALUES
					('$ndocumento', '$idCliente', '$_data','$valorPlano', 'ABERTO', '$emissao','$NumeroNovo', '$sequencia2', '$aC->EMPRESA_ID')";
					 
					$resultadoSql = $this->conn->query($sql);
        			
        			//supondo que o vencimento � de 30 em 30 dias 
					
        			$_prazo += 1;
        			
    			
    			}
			$dataHoje = date('Y-m-d');
			$query0 = "UPDATE acesso_cliente SET DATA_RENOVACAO='$dataHoje' WHERE ID='$aC->ID'";
			$result0 = $this->conn->query($query0);
			
			
				return 'ok';

    			$aC->DATA_CADASTRO = date('Y-m-d');
    			//------GERAR BOLETO DE ADESAO CASO HOUVER
//    			if(strlen($valorAdesao) > 0)
//    			{
//    				$queryUltimoBoletoAdesao = "SELECT * FROM contas_receber WHERE CLIENTES_ID = '$idCliente' ORDER by ID DESC LIMIT 0,1";
//					$resultUltimoBoletoAdesao = $this->conn->query($queryUltimoBoletoAdesao);
//					$rowUltimoBoletoAdesao = $resultUltimoBoletoAdesao->fetch_assoc();			
//			    	$nRowUltimoBoletoAdesao = $resultUltimoBoletoAdesao->num_rows;
//			    	if($nRowUltimoBoletoAdesao == 0)
//			    	{
//			    		$ultimoNumeroAdesao = str_pad("1", 6, "0", STR_PAD_LEFT);
//			    	}else
//			    	{
//			    		$ultimoValorAdesao = ltrim(substr($rowUltimoBoletoAdesao['N_NUMERO'], -6)+1, "0");
//			    		
//			    		$ultimoNumeroAdesao = str_pad($ultimoValorAdesao, 6, "0", STR_PAD_LEFT);
//			    		
//			    	}
//			    	
//           		$NumeroNovoAdesao = $aC->CLIENTES_ID.$ultimoNumeroAdesao;
//           			
//    			$nDocAdesao  = $aC->CLIENTES_ID.'/ADESAO';
//    			$valorAdesao = $rowVigenciaContrato['VALOR_ADESAO'];
//    			$queryAdesao = "INSERT INTO contas_receber (N_DOC,CLIENTES_ID,DATA_VENCIMENTO,VALOR_TITULO, STATUS_2, DATA_EMISSAO, N_NUMERO, 
//					CONTROLE, EMPRESA_ID) VALUES
//					('$nDocAdesao', '$idCliente', '$aC->DATA_CADASTRO','$valorAdesao', 'ABERTO','$aC->DATA_CADASTRO','$NumeroNovoAdesao', '$nDocAdesao', '$aC->EMPRESA_ID')";
//					 
//				 $resultAdesao = $this->conn->query($queryAdesao);
//    			}
    			
				//------GERAR BOLETO DE  INSTALACAO CASO HOUVER
//    			if(strlen($valorAdesao) > 0)
//    			{
//    				$queryUltimoBoletoAdesao = "SELECT * FROM contas_receber WHERE CLIENTES_ID = '$idCliente' ORDER by ID DESC LIMIT 0,1";
//					$resultUltimoBoletoAdesao = $this->conn->query($queryUltimoBoletoAdesao);
//					$rowUltimoBoletoAdesao = $resultUltimoBoletoAdesao->fetch_assoc();			
//			    	$nRowUltimoBoletoAdesao = $resultUltimoBoletoAdesao->num_rows;
//			    	if($nRowUltimoBoletoAdesao == 0)
//			    	{
//			    		$ultimoNumeroAdesao = str_pad("1", 6, "0", STR_PAD_LEFT);
//			    	}else
//			    	{
//			    		$ultimoValorAdesao = ltrim(substr($rowUltimoBoletoAdesao['N_NUMERO'], -6)+1, "0");
//			    		
//			    		$ultimoNumeroAdesao = str_pad($ultimoValorAdesao, 6, "0", STR_PAD_LEFT);
//			    		
//			    	}
//			    	
//           		$NumeroNovoAdesao = $idCliente.$ultimoNumeroAdesao;
//           			
//    			$nDocAdesao  = $idCliente.'/INSTALACAO';
//    			$valorInstalacao = $rowVigenciaContrato['VALOR_INSTALACAO'];
//    			$queryAdesao = "INSERT INTO contas_receber (N_DOC,CLIENTES_ID,DATA_VENCIMENTO,VALOR_TITULO, STATUS_2, DATA_EMISSAO, N_NUMERO, 
//					CONTROLE, EMPRESA_ID) VALUES
//					('$nDocAdesao', '$idCliente', '$aC->DATA_CADASTRO','$valorInstalacao', 'ABERTO','$aC->DATA_CADASTRO','$NumeroNovoAdesao', '$nDocAdesao', '$aC->EMPRESA_ID')";
//					 
//				 $resultAdesao = $this->conn->query($queryAdesao);
//    			}

			

	}
	
	
	public function buscarCredenciaisAcesso($valor, $codEmpresa)
	{
		$query = "SELECT acesso_cliente.ID,acesso_cliente.EMPRESA_ID, acesso_cliente.CLIENTES_ID, acesso_cliente.LOGIN, acesso_cliente.SENHA, 
			acesso_cliente.ENDERECO_IP, acesso_cliente.ENDERECO_MAC,  acesso_cliente.DATA_VENC_CONTRATO, acesso_cliente.DATA_CADASTRO, 
			acesso_cliente.STATUS_2,
			clientes.NOME_RAZAO,
			interface.ID as ID_INTERFACE,interface.NOME as NOME_INTERFACE, 
			planos_acesso.ID as ID_PLANO_ACESSO,planos_acesso.NOME as NOME_PLANO_ACESSO,
			servidores.ID as ID_SERVIDOR, servidores.NOME_SERVER, 
			bases.ID as ID_BASE, bases.NOME as NOME_BASE, 
			material_acesso.ID as ID_MATERIAL,material_acesso.NOME as NOME_MATERIAL , 
			contratos_acesso.ID as ID_CONTRATO, contratos_acesso.NOME as NOME_CONTRATO
					FROM acesso_cliente
			    LEFT JOIN clientes ON clientes.ID = acesso_cliente.CLIENTES_ID
			    LEFT JOIN interface ON interface.ID = acesso_cliente.INTERFACE_ID 
			    LEFT JOIN planos_acesso ON planos_acesso.ID = acesso_cliente.PLANOS_ACESSO_ID
			    LEFT JOIN servidores ON servidores.ID = acesso_cliente.SERVIDORES_ID
			    LEFT JOIN bases ON bases.ID = acesso_cliente.BASES_ID    
			    LEFT JOIN material_acesso ON material_acesso.ID = acesso_cliente.MATERIAL_ACESSO_ID
			    LEFT JOIN contratos_acesso ON contratos_acesso.ID = acesso_cliente.CONTRATOS_ACESSO_ID
			    WHERE acesso_cliente.EMPRESA_ID = '$codEmpresa' AND acesso_cliente.STATUS_2 LIKE 'ATIVO'
			    AND clientes.NOME_RAZAO LIKE '%$valor%' ORDER by ID DESC";
		
		
		
		
		
		$result = $this->conn->query($query);
		$nrow = $result->num_rows;
		if($nrow == 0){return 'ERRO';}
		
		while ($row = $result->fetch_assoc()) {
			
			$acessoCliente = new AcessoClienteVO();
			
			$dataHoje = date('Y-m-d');
			$dataAtual = explode('-', $dataHoje);
			$dataVencimento = explode('-', $row['DATA_VENC_CONTRATO']);
			
			$d1 = mktime(0,0,0,$dataAtual[1],$dataAtual[2],$dataAtual[0]);
			$d2 = mktime(0,0,0,$dataVencimento[1],$dataVencimento[2],$dataVencimento[0]);  
			$d3 = ($d2-$d1);
			$dias = ($d3/2592000);
			$dias = explode('.', $dias);
			$meses = $dias[0];
			
			$acessoCliente->ID = $row['ID'];
			$acessoCliente->EMPRESA_ID = $row['EMPRESA_ID'];
			$acessoCliente->CLIENTES_ID = $row['CLIENTES_ID'];
			$acessoCliente->LOGIN = $row['LOGIN'];
			$acessoCliente->SENHA = $row['SENHA'];
			$acessoCliente->ENDERECO_IP = $row['ENDERECO_IP'];
			$acessoCliente->ENDERECO_MAC = $row['ENDERECO_MAC'];
			$acessoCliente->NOME_RAZAO = $row['NOME_RAZAO'];
			$acessoCliente->ID_INTERFACE = $row['ID_INTERFACE'];
			$acessoCliente->NOME_INTERFACE = $row['NOME_INTERFACE'];
			$acessoCliente->ID_PLANO_ACESSO = $row['ID_PLANO_ACESSO'];
			$acessoCliente->NOME_PLANO_ACESSO = $row['NOME_PLANO_ACESSO'];
			$acessoCliente->ID_SERVIDOR = $row['ID_SERVIDOR'];
			$acessoCliente->NOME_SERVER = $row['NOME_SERVER'];
			$acessoCliente->ID_BASE = $row['ID_BASE'];
			$acessoCliente->NOME_BASE = $row['NOME_BASE'];
			$acessoCliente->ID_MATERIAL = $row['ID_MATERIAL'];
			$acessoCliente->NOME_MATERIAL = $row['NOME_MATERIAL'];
			$acessoCliente->ID_CONTRATO = $row['ID_CONTRATO'];
			$acessoCliente->NOME_CONTRATO = $row['NOME_CONTRATO'];
			$acessoCliente->DATA_VENC_CONTRATO = $row['DATA_VENC_CONTRATO'];
			$acessoCliente->DATA_CADASTRO = $row['DATA_CADASTRO'];
			$acessoCliente->DIAS_CONTRATO = $meses;
			
			$acessoClientes[] = $acessoCliente;
			
		}
		
		return $acessoClientes;
	}
	public function listarMacs()
	{
		$API = new routeros_api();
		$API->port = 8728;					
		$API->debug = true;
		
		if ($API->connect('201.65.162.56', 'rbdigital', 'mkcolorau')) 
		{

		 	$API->write('/interface/wireless/access-list/print',false);
		 	$API->write('?interface=digitalmax.centro.2 (5.8GHz)');
		 	
		}
	}
	public function cadastrarPlanoAcesso(PlanosAcessoVO $planoAcesso)
	{
		//LSITA INTERFACES		
		$queryInterface = "SELECT * FROM interface WHERE ID='$planoAcesso->INTERFACE_ID'";
		$resultInterface = $this->conn->query($queryInterface);
		$nRowInterface = $resultInterface->num_rows;
		if($nRowInterface == 0){return 'ERRO: INTERFACE N�O ENCONTRADA';}
		$rowInterface = $resultInterface->fetch_assoc();
		
		//DEFINE NOME INTERFACE
		$nomeInterface =$rowInterface['NOME']; 
		
		//DEFINE SERVIDOR
		$codServidor = $rowInterface['SERVIDORES_ID'];
		
		//DEFINE COD DO SERVIDOR RADIUS
		$codServidorRadius = $rowInterface['SERVIDOR_RADIUS_ID'];
		
		//DEFINE TIPO DE AUTENTICA��O E CONTROLE
		// 1 - SIM
		// 0 - NAO
		//
		$hotspot =  $rowInterface['HOSTPOT'];
		$ppoe =  $rowInterface['PPOE'];
		$simples_queue =  $rowInterface['SIMPLES_QUEUE'];
		$hotspot_radius =  $rowInterface['HOTSPOT_RADIUS'];
		
		
		//SE UTILIZAR AUTENTICACAO+CONTROLE PELO HOTSPOT
		if($hotspot == 1)
		{
			$query1 = "SELECT * FROM servidores WHERE ID=$codServidor";
			$result1 = $this->conn->query($query1);
			$nRow1 = $result1->num_rows;
			if($nRow1 == 0)
			{
				return 'CADASTRAR PLANO DE ACESSO: SERVIDOR NÃO ENCONTRADO';
			}
			$row1 = $result1->fetch_assoc();
			//DADOS DO SERVIDOR
			$porta = $row1['PORTA_API'];
			$ip = $row1['ENDERECO_IP'];
			$usuario = $row1['USUARIO'];
			$senha = $row1['SENHA'];
			
			//PREPARA A INTERFACE NO SERVIDOR
			//
			//--1-CADASTRA USER-PROFILE NO HOTSPOT DO SERVIDOR
		
					//INSTANCIA A API DO MIKROTIK
					$API = new routeros_api();
					$API->port = $porta;				
					$API->debug = true;
					
					if ($API->connect($ip, $usuario, $senha)) 
					{
						
						
						 //RENOMEIA INTERFACE DO CLIENTE;
						 $API->write('/ip/hotspot/user/profile/add',false);
						 $API->write('=name=' . $planoAcesso->NOME,false);
						 $API->write('=rate-limit=' . $planoAcesso->RATE_LIMIT,false);
						 $API->write('=shared-users=' . $planoAcesso->SHARED_USERS,false);
						if($planoAcesso->SESSION_TIMEOUT != '00:00:00')
						{
						 $API->write('=session-timeout=' . $planoAcesso->SESSION_TIMEOUT,false);
						}
						 $API->write('=keepalive-timeout=' . $planoAcesso->KEEPALIVE_TIMEOUT,false);
						 $API->write('=idle-timeout=' . $planoAcesso->IDLE_TIMEOUT,false);
						 $API->write('=status-autorefresh=' . $planoAcesso->STATUS_AUTOREFRESH);
						
						  $API->read();
						 
						 //add idle-timeout=4h keepalive-timeout=1m name=150k rate-limit=150k/150k shared-users=1 status-autorefresh=3m
						 //CADASTRAR PLANO NA TABELA PLANO_ACESSO
						 
					   $queryPlanoAcesso = "INSERT INTO planos_acesso (INTERFACE_ID, NOME, RATE_LIMIT,SHARED_USERS,SESSION_TIMEOUT,KEEPALIVE_TIMEOUT,IDLE_TIMEOUT,STATUS_AUTOREFRESH,  VALOR) VALUES 
					   ('$planoAcesso->INTERFACE_ID', '$planoAcesso->NOME', '$planoAcesso->RATE_LIMIT', '$planoAcesso->SHARED_USERS', '$planoAcesso->SESSION_TIMEOUT', '$planoAcesso->KEEPALIVE_TIMEOUT','$planoAcesso->IDLE_TIMEOUT', '$planoAcesso->STATUS_AUTOREFRESH', '$planoAcesso->VALOR')";
					   if(!$resultPlanoAcesso = $this->conn->query($queryPlanoAcesso))
					   {
					   		return 'ERRO: '.$this->conn->error; 
					   }
					   
					   	  $API->disconnect();
					}else{
					   return 'ERRO';
					}							  	
			}
			
			
			
		
		
		
		//SE UTILIZAR AUTENTICA��O+CONTROLE NO HOTSPOT
		if($hotspot_radius == 1)
		{
		   //BUSCAR SERVIDOR RADIUS ASSOCIADO;
		   $queryServidorRadius = "SELECT * FROM servidores_radius WHERE ID = $codServidorRadius";
		   $resultServidorRadius = $this->conn->query($queryServidorRadius);
		   $nRowServidorRadius = $resultServidorRadius->num_rows;
		   if($nRowServidorRadius == 0){return 'ERRO: SERVIDOR RADIUS N�O ENCONTRADO';}
		   $rowServidorRadius =$resultServidorRadius->fetch_assoc();
		   
		   //DEFINE PARAMETROS DO SERVIDOR RADIUS
		   $ipRadius = $rowServidorRadius['IP_FREERADIUS'];
		   $userMysql = $rowServidorRadius['USERNAME_MYSQL'];
		   $senhaMysql = $rowServidorRadius['PASSWORD_MYSQL'];
		   $bancoMysql = $rowServidorRadius['BANCO_DADOS'];

		   //TENTA CONECTAR AO SERVIDOR MYSQL
		   if($con = mysql_pconnect($ipRadius, $userMysql, $senhaMysql))
		   {
		   	
			   	//CADASTRA PLANO DE ACESSO NA TABELA radgroupreply DO RADIUS
			   	$groupName = $planoAcesso->INTERFACE_ID."_".$planoAcesso->NOME;
			   	$value = $planoAcesso->RATE_LIMIT;
			   	$querRadGroupRapley = "INSERT INTO ".$bancoMysql.".radgroupreply (groupname, attribute, op, value) VALUES 
			   	('$groupName', 'Mikrotik-Rate-Limit', ':=', '$value')";
			   	
			   	if(!$resultRadGroupRapley = $this->conn->query($querRadGroupRapley, $con))
			   	{
			   		return 'ERRO: '.$this->conn->error;
			   	}		   		
		   
		   }else{
		   		return 'ERRO: '.$this->conn->error;
		   }
		   		//FECHA CONEX�O REMOTA
		   		mysql_close($con);
		   		
		   new BaseClass();
		   //CADASTRAR PLANO NA TABELA PLANO_ACESSO
		   $queryPlanoAcesso = "INSERT INTO planos_acesso (INTERFACE_ID, NOME, RATE_LIMIT, VALOR) VALUES 
		   ('$planoAcesso->INTERFACE_ID', '$planoAcesso->NOME', '$planoAcesso->RATE_LIMIT', '$planoAcesso->VALOR')";
		   if(!$resultPlanoAcesso = $this->conn->query($queryPlanoAcesso))
		   {
		   		return 'ERRO: '.$this->conn->error;
		   }
		}
		
		
		if($simples_queue == 1)
		{
			
		   //CADASTRAR PLANO NA TABELA PLANO_ACESSO
		   $queryPlanoAcesso = "INSERT INTO planos_acesso (INTERFACE_ID, NOME, RATE_LIMIT, VALOR) VALUES 
		   ('$planoAcesso->INTERFACE_ID', '$planoAcesso->NOME', '$planoAcesso->RATE_LIMIT', '$planoAcesso->VALOR')";
		   if(!$resultPlanoAcesso = $this->conn->query($queryPlanoAcesso))
		   {
		   		return 'ERRO: '.$this->conn->error;
		   }
		}
		
		return 'OK';
		
	}
	/**
	 * Função Sicroniza Acesso de Clientes com Servidores
	 * @author Marconi César
	 * @name sicronizaAcessoCliente
	 */
	public function sicronizaAcessoCliente($codEmpresa)
	{
		
		
		//BUSCA SERVIDOR PADRAO DA EMPRESA
		$query0 = "SELECT * FROM  servidores WHERE EMPRESA_ID=$codEmpresa AND DEFAULT_2=1";
		$result0 = $this->conn->query($query0);
		
		//CASO NAO ENCONTRE SERVIDOR PADRAO DA EMPRESA A CONDICAO FALHARA
		if($result0->num_rows == 0)
		{
			return 'NENHUM SERVIDOR PADRAO ENCONTRADO PARA A EMPRESA ATUAL';
		}
		
		//RESULTADO DA BUSCA
		$row0 = $result0->fetch_assoc();
		
		//DEFINE IP DO SERVIDOR
		$ID_SERVIDOR = $row0['ID'];
		$IP_SERVIDOR = $row0['ENDERECO_IP'];
		$USUARIO_SERVIDOR = $row0['USUARIO'];
		$SENHA_SERVIDOR = $row0['SENHA'];
		$PORTA_API_SERVIDOR = $row0['PORTA_API'];
		
		//VERIFICA SE EXISTEM ACESSOS NA EMPRESA ATUAL
		$query1 = "SELECT ac.*,c.NOME_RAZAO,i.NOME FROM acesso_cliente as ac, clientes as c, interface as i
		WHERE ac.EMPRESA_ID =$codEmpresa AND ac.CLIENTES_ID = c.ID AND ac.INTERFACE_ID = i.ID";
		$result1 = $this->conn->query($query1);
		
		
		//CASO NAO ENCONTRE NENHUM ACESSO A CONDICAO FALHARA
		if($result1->num_rows == 0)
		{
			return 'NENHUM ACESSO FOI ENCONTRADO PARA A EMPRESA ATUAL';
		}
		
		
		//TENTA CONECTAR AO SERVIDOR PADRAO
		//VARIAVEIS ESTANCIADAS API
		$API = new routeros_api();
		$API->port = $PORTA_API_SERVIDOR;					
		//$API->debug = true;
				
		//CASO CONSIGA SE CONECTAR AO SERVIDOR
		if ($API->connect($IP_SERVIDOR, 'admin', 'admin'))
		{
			//PROCURA INTERFACES DO SERVIDOR ESCOLHIDO
			$query2 = "SELECT * FROM  interface WHERE SERVIDORES_ID=$ID_SERVIDOR";
			$result2 = $this->conn->query($query2);
			
			if($result2->num_rows > 0)
			{
				//LACO QUE PERCORRE AS INTERFACES DO SERVIDOR INFORMADO
				while($row2 = $result2->fetch_assoc())
				{
					//DEFINE NOME DA INTERFACE
					$NOME_INTERFACE = $row2['NOME'];
					//PROCURA ENDERECOS IP NAS INTERFACES DO SERVIDOR ESCOLHIDO			 
					
					//PROCURA ENDERECOS DE IP NA INTERFACE INFORMADA
					$API->write('/ip/address/print',false);
					$API->write('?actual-interface=' . $NOME_INTERFACE);
					$userArray = $API->read();
					
										
										
					//CASO HAJA IPS NA INTERFACE IRA REMOVE-LOS
					if(count($userArray) > 0)
					{
						
						for($i=0;$i <= count($userArray); $i++)
						{
							//REMOVE O USUARIO DO HOTSPOT		 					  
							$API->write('/ip/address/remove',false);
							$API->write('=numbers=' . $userArray[$i]['.id']);
							$API->read();
						}
					}
				}
				
				
			}
			
			
			  
			  
			
			
			//LACO DE ACESSO DE CLIENTE
			while($row1 = $result1->fetch_assoc())
			{
								
				//DEFINE O IP DA INTERFACE DO SERVIDOR
				$ipMK1 = substr($row1['ENDERECO_IP'], 0, -1);
				$ipMK2 = substr($row1['ENDERECO_IP'] , -1);
				$ipMK3 = $ipMK2 - 1;
				$ip2 = $ipMK1.$ipMK3;
				
				//ADICIONA INTERFACE PARA O CLIENTE NO SERVIDOR
				$API->write('/ip/address/add',false);
				$API->write('=address=' . $ip2.'/30',false);
				$API->write('=interface=' .  $row1['NOME'], false);
				$API->write('=comment=' . $row1['NOME_RAZAO']);
				$API->read();
			}
		}else{
			return 'NAO FOI POSSIVEL CONECTAR-SE AO SERVIDOR PADRAO ATUAL';
		}
		
		
		
		//VERIFICACOES FINAIS		
		if($this->conn->error)
		{
			return 'NAO FOI POSSIVEL CONCLUIR O PROCESSO DE SICRONIZACAO COM SERVIDOR';
		}else{
			return 'ok';
		}
		
		
		
	}
	
	
}
