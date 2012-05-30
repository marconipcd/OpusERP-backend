<?php

require_once 'classes/Conexao.php';

require_once 'vo/osiVO.php';
require_once 'vo/oseVO.php';
require_once 'vo/EndPrincipaisVO.php';
require_once 'vo/tipoProblema_OSI.php';
require_once 'vo/motivoCancelamentoVO.php';
require_once 'vo/tipoProblema_OSE.php';
require_once 'vo/tipoOSe_VO.php';
require_once 'vo/BaseVO.php';
require_once 'vo/TipoServicoOSeVO.php';
require_once 'vo/contasBancariasVO.php';


class Suporte extends Conexao
{
	
	public function gerarCupomOSE($codEmpresa, $codOSE)
	{		
		$query = "SELECT ose.TIPO, ose.CONTATO, ose.BASE, ac.ENDERECO_IP,c.ID as CODIGO_CLIENTE, c.NOME_RAZAO, c.TELEFONE1, 
						c.TELEFONE2, c.CELULAR1, c.CELULAR2, mc.DESCRICAO as MOTIVO , i.NOME as INTERFACE,
						pa.NOME as PLANO, ca.NOME as CONTRATO, ma.NOME as MATERIAL
						FROM ose, acesso_cliente as ac, 
						clientes as c, motivos_cancelamento as mc, interface as i, planos_acesso as pa,
						contratos_acesso as ca, material_acesso as ma WHERE 
						ose.CLIENTES_ID = c.ID AND  ac.CLIENTES_ID = c.ID AND ose.MOTIVO = mc.ID
						AND ac.INTERFACE_ID = i.ID AND ac.PLANOS_ACESSO_ID = pa.ID
						AND ac.CONTRATOS_ACESSO_ID = ca.ID AND ac.MATERIAL_ACESSO_ID = ma.ID
						AND ose.ID=$codOSE";	
								
		$result = $this->conn->query($query);
		$nRow = $result->num_rows;
		if($nRow == 0)
		{
			return 'GERAR CUPOM OSE: NENHUMA OSE ENCONTRADA '.$codOSE;
		}
		$row = $result->fetch_assoc();
		
		
		$dataHoje = date('d/m/Y');
		$hora = date('h:m');
		
		
		//unlink("./util2/os/OS_PRINT.txt");
		$fp = fopen("./util/cupons/ose/OSE".$codOSE.".txt", "w+");
		
		$quebra = chr(13).chr(10);
		
		$linha =  nl2br(str_pad('d i g i t a l', 48, " ", STR_PAD_BOTH));
		$linha .= nl2br(str_pad("Rua Adjar Maciel, 35 Centro Belo Jardim/PE", 48, " ", STR_PAD_BOTH));
		$linha .= nl2br(str_pad("CEP: 55.150-040 Fone: (81)3726.3125", 48, " ", STR_PAD_BOTH));
		$linha .= nl2br(str_pad("CNPJ: 07.578.965/0001-05 IE: 18.3.050.0329", 48, " ", STR_PAD_BOTH));
		$linha .= nl2br(str_pad("www.digitalonline.com.br", 48, " ", STR_PAD_BOTH));
		$linha .= $quebra;
		$linha .= $quebra;
		$linha .= $quebra;
		$linha .= nl2br(str_pad("ABERTURA DE CHAMADO", 48, " ", STR_PAD_BOTH));
		$linha .= nl2br(str_pad("------------------------------------------------", 48, " ", STR_PAD_BOTH));
		$linha .= nl2br(str_pad("EMISSAO: ".$dataHoje." HORA: ".$hora."", 48, " ", STR_PAD_BOTH));
		$linha .= nl2br(str_pad("------------------------------------------------", 48, " ", STR_PAD_BOTH));	
		$linha .= $quebra;
		$linha .= $quebra;
		$linha .= nl2br(str_pad("INFORMACOES DO CHAMADO", 48, " ", STR_PAD_BOTH));
		$linha .= nl2br(str_pad("------------------------------------------------", 48, " ", STR_PAD_BOTH));	
		$linha .= $quebra;
		$linha .= nl2br("TIPO..: ".$row['TIPO']."").$quebra;
		$linha .= nl2br("MOTIVO: ".$row['MOTIVO']."").$quebra;
	    $linha .= $quebra;
		$linha .= nl2br("CONTATO.: ".$row['CONTATO']."").$quebra;
		$linha .= nl2br(str_pad("------------------------------------------------", 48, " ", STR_PAD_BOTH));
		$linha .= $quebra;
		$linha .= nl2br("CLIENTE..: ".$row['NOME_RAZAO']."").$quebra;
		$linha .= $quebra;
		$linha .= nl2br("FONE1.: ".$row['TELEFONE1']."").$quebra;
		$linha .= nl2br("FONE2.: ".$row['TELEFONE2']."").$quebra;
		$linha .= nl2br("CEL.: ".$row['CELULAR1']."").$quebra;
		$linha .= nl2br("CEL.: ".$row['CELULAR2']."").$quebra;
		$linha .= nl2br(str_pad("------------------------------------------------", 48, " ", STR_PAD_BOTH));	
		$linha .= $quebra;
		$linha .= nl2br("TIPO ACESSO.: ".$row['INTERFACE']."").$quebra;
		$linha .= nl2br("PLANO.: ".$row['PLANO']."").$quebra;
		$linha .= nl2br("CONTRATO.: ".$row['CONTRATO']."").$quebra;
		$linha .= nl2br("BASE.: ".$row['BASE']."").$quebra;
		$linha .= nl2br("MATERIAL.: ".$row['MATERIAL']."").$quebra;
		$linha .= nl2br("ENDERECO_IP.: ".$row['ENDERECO_IP']."").$quebra;
		$linha .= $quebra;
		$linha .= nl2br(str_pad("------------------------------------------------", 48, " ", STR_PAD_BOTH));	
		$linha .= $quebra;
		if($row['TIPO'] == 'CANCELAMENTO')
		{
			
			$linha .= nl2br("Venho por meio desta solicitar o cancelamento").$quebra;
			$linha .= nl2br("do Servico de Acesso a Internet de contrato").$quebra;
			$linha .= nl2br("numero ".$row['CODIGO_CLIENTE']."autorizando a  retirada").$quebra;
			$linha .= nl2br("do equipamento instalado, em caso de regime comodato ").$quebra;
			$linha .= nl2br("onde a nao devolucao do material, habilitara a  CONTRATADA").$quebra;
			$linha .= nl2br("a promover o respectivo protesto e execucao ou ").$quebra;
			$linha .= nl2br("inclusao nos Servicos de Protecao ao Credito na forma").$quebra;
			$linha .= nl2br("Item 12 da Clausula SEXTA do respectivo contrato").$quebra;
			          
                 
		}
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
	
		return 'util/cupons/ose/OSE'.$codOSE.'.txt';
	}
	
	
	//------------OSI--------------------------------//
	public function listarOSI($codEmpresa)
	{
		$query = "SELECT osi . * , clientes.NOME_RAZAO FROM osi INNER JOIN clientes 
		ON osi.CLIENTES_ID = clientes.ID WHERE osi.EMPRESA_ID = '$codEmpresa' AND osi.STATUS_2 != 'ENTREGUE' ORDER by osi.DATA_ECAMINHAMENTO ASC";
		$result = $this->conn->query($query);
		$nRow = $result->num_rows;
		if($nRow == 0){return 'ERRO';}
		
		while ($row = $result->fetch_assoc())
		{
			  $osi = new osiVO();
			  $osi->ID = $row['ID'];
			  $osi->EMPRESA_ID = $row['EMPRESA_ID'];
			  $osi->CLIENTES_ID = $row['CLIENTES_ID'];
			  $osi->NOME_RAZAO = $row['NOME_RAZAO'];
			  $osi->DATA_AGENDAMENTO = $row['DATA_AGENDAMENTO'];
			  $osi->DATA_ENTRADA = $row['DATA_ENTRADA'];
			  $osi->DATA_ECAMINHAMENTO = $row['DATA_ECAMINHAMENTO'];
			  $osi->DATA_CONCLUSAO = $row['DATA_CONCLUSAO'];
			  $osi->CONTATO = $row['CONTATO'];
			  $osi->EQUIPAMENTO = $row['EQUIPAMENTO'];
			  $osi->ACESSORIOS = $row['ACESSORIOS'];
			  $osi->OBSERVACAO = $row['OBSERVACAO'];
			  $osi->DIAS_EM_MANUTENCAO = $row['DIAS_EM_MANUTENCAO'];
			  $osi->OPERADOR = $row['OPERADOR'];
			  $osi->TECNICO = $row['TECNICO'];
			  $osi->PROBLEMA = $row['PROBLEMA'];
			  $osi->CONCLUSAO = $row['CONCLUSAO'];
			  $osi->VALOR = $row['VALOR'];
			  $osi->NF_GARANTIA = $row['NF_GARANTIA'];
			  $osi->STATUS_2 = $row['STATUS_2'];
			  
			  $osis[] = $osi;
		}
		
		return $osis;
	}
	public function selecionar_OSI($codOSI)
	{
		$query = "SELECT osi . * , clientes.NOME_RAZAO FROM osi INNER JOIN clientes 
		ON osi.CLIENTES_ID = clientes.ID WHERE osi.STATUS_2 != 'ENTREGUE' AND 
		osi.ID = '$codOSI' ORDER by osi.DATA_ECAMINHAMENTO ASC";
		
		$result = $this->conn->query($query);
		$nRow = $result->num_rows;
		if($nRow == 0){return 'ERRO';}
		
		$row = $result->fetch_assoc();
		
			  $osi = new osiVO();
			  $osi->ID = $row['ID'];
			  $osi->EMPRESA_ID = $row['EMPRESA_ID'];
			  $osi->CLIENTES_ID = $row['CLIENTES_ID'];
			  $osi->NOME_RAZAO = $row['NOME_RAZAO'];
			  $osi->DATA_AGENDAMENTO = $row['DATA_AGENDAMENTO'];
			  $osi->DATA_ENTRADA = $row['DATA_ENTRADA'];
			  $osi->DATA_ECAMINHAMENTO = $row['DATA_ECAMINHAMENTO'];
			  $osi->DATA_CONCLUSAO = $row['DATA_CONCLUSAO'];
			  $osi->CONTATO = $row['CONTATO'];
			  $osi->EQUIPAMENTO = $row['EQUIPAMENTO'];
			  $osi->ACESSORIOS = $row['ACESSORIOS'];
			  $osi->OBSERVACAO = $row['OBSERVACAO'];
			  $osi->DIAS_EM_MANUTENCAO = $row['DIAS_EM_MANUTENCAO'];
			  $osi->OPERADOR = $row['OPERADOR'];
			  $osi->TECNICO = $row['TECNICO'];
			  $osi->PROBLEMA = $row['PROBLEMA'];
			  $osi->CONCLUSAO = $row['CONCLUSAO'];
			  $osi->VALOR = $row['VALOR'];
			  $osi->NF_GARANTIA = $row['NF_GARANTIA'];
			  $osi->STATUS_2 = $row['STATUS_2'];
			  
			
		
		return $osi;
	}
	public function cadastrarOSI(osiVO $osi, EndPrincipaisVO $enderecoPrincipal, ClientesVO $cliente)
	{
		//return;
		
		$dataEntrada = date('Y-m-d');
			$dia1 =  substr($osi->DATA_AGENDAMENTO, 0, 2);
			$mes1 =  substr($osi->DATA_AGENDAMENTO, 3, 2);
			$ano1 =  substr($osi->DATA_AGENDAMENTO, 6, 4);
		
			$dataAgendada= $ano1.'-'.$mes1.'-'.$dia1;
		
		//CADASTRA OSI
		$query = "INSERT INTO osi (
			EMPRESA_ID ,CLIENTES_ID ,DATA_ENTRADA ,DATA_AGENDAMENTO , CONTATO ,EQUIPAMENTO ,ACESSORIOS ,OBSERVACAO ,OPERADOR ,
			STATUS_2)VALUES (
			'$osi->EMPRESA_ID', '$osi->CLIENTES_ID', '$dataEntrada', '$dataAgendada','$osi->CONTATO', '$osi->EQUIPAMENTO', 
			'$osi->ACESSORIOS', '$osi->OBSERVACAO' , '$osi->OPERADOR','ABERTO')";
		$result = $this->conn->query($query);
		
		//ATUALIZA TELEFONES DO CADASTRO PESSOAL DO CLIENTE
		$queryUpdate0 = "UPDATE clientes SET TELEFONE1 = '$cliente->TELEFONE1',TELEFONE2 = '$cliente->TELEFONE2',
					CELULAR1 = '$cliente->CELULAR1',CELULAR2 = '$cliente->CELULAR2' WHERE ID ='$cliente->ID'";
		$resultUpdate0 = $this->conn->query($queryUpdate0);
		
		//ATUALIZA ENDERECO PRINCIPAL DO CLIENTE
		$queryUpdate = "UPDATE enderecos_principais SET CEP = '$enderecoPrincipal->CEP',
			ENDERECO = '$enderecoPrincipal->ENDERECO',NUMERO = '$enderecoPrincipal->NUMERO',
			COMPLEMENTO = '$enderecoPrincipal->COMPLEMENTO',BAIRRO = '$enderecoPrincipal->BAIRRO',
			CIDADE = '$enderecoPrincipal->CIDADE',UF = '$enderecoPrincipal->UF',
			PAIS = '$enderecoPrincipal->PAIS',REFERENCIA = '$enderecoPrincipal->REFERENCIA' WHERE 
			CLIENTES_ID = '$cliente->ID'";
		$resultUpdate = $this->conn->query($queryUpdate);
	}
	public function encaminharOSI(osiVO $osi)
	{
		$query = "UPDATE osi SET TECNICO = '$osi->TECNICO', STATUS_2 = 'ENCAMINHADO' WHERE ID ='$osi->ID'";
		$result = $this->conn->query($query);	
	}
	public function fecharOSI(osiVO $osi)
	{
		$data_conclusao = date('Y-m-d');
		
		$query = "UPDATE osi SET STATUS_2 = 'FECHADO', CONCLUSAO = '$osi->CONCLUSAO', PROBLEMA = '$osi->PROBLEMA', 
		VALOR = '$osi->VALOR', NF_GARANTIA = '$osi->NF_GARANTIA' WHERE ID ='$osi->ID'";
		$result = $this->conn->query($query);
	}
	public function entregarOSI(osiVO $osi)
	{
		$resultado = true;
		$this->conn->autocommit(false);
		
		//SUBSTITUI . POR , NO VALOR 
		$osi->VALOR = str_replace('.', ',', $osi->VALOR);
		
		//MUDAR STATUS DA OSI PARA ENTREGUE;
		$query = "UPDATE osi SET STATUS_2 = 'ENTREGUE' WHERE ID ='$osi->ID'";
		$result = $this->conn->query($query);	
		
		//SE O VALOR FOR DIFERENTE DE 0,00
		if($osi->VALOR != '0,00')
		{
			//SETA QUANTIDADE DE TITULO PARA 1
			$qtd = 1;
			
			//SETA QUANTIDADE				
			$quantidade = $qtd;
   			
			//DEFINE O PRAZO	
			$_prazo = 0;   
   			
			//PROCURA PELO NUMERO DA ULTMA OSI	
   			$queryUltimaOSI = "SELECT * FROM osi ORDER by ID DESC LIMIT 0, 1";
   			$resultUltimaOSI = $this->conn->query($queryUltimaOSI);
			
			if($resultUltimaOSI->num_rows == 0)
			{
				$resultado = false;
			}
			
			//RESULTADO DA CONSULTA POR OSI
   			$rowUltimaOSI = $resultUltimaOSI->fetch_assoc();
   			
			//DEFINE O NOME DO DOCUMENTO DO TITULO	
    		  	$ndocumento = 'OSI_'.$rowUltimaOSI['ID'];
				
			
			//DEFINE SEQUENCIA DO TITULO
			$sequencia = $i;
					
			if($i ==0)
			{
				$sequencia++;
			}else if($i == $sequencia)
			{
				$sequencia++;
			}
					
			$sequencia2 = str_pad($sequencia, 2, "0", STR_PAD_LEFT);
			
			//DEFINE ANO
			$ano = date('y');

			
			//BUSCA POR TITULOS JA CADASTRADOS DO CLIENTE INFORMADAO		
			$queryUltimoBoleto = "SELECT * FROM contas_receber WHERE CLIENTES_ID = '$osi->CLIENTES_ID' ORDER by ID DESC";
			$resultUltimoBoleto = $this->conn->query($queryUltimoBoleto);
			$rowUltimoBoleto = $resultUltimoBoleto->fetch_assoc();			
			    	
			//DEFINE N_NUMERO DO TITULO
			if($resultUltimoBoleto->num_rows == 0)
			{
				$ultimoNumero = str_pad("1", 6, "0", STR_PAD_LEFT);
			}
			else
			{
				$ultimoValor = ltrim(substr($rowUltimoBoleto['N_NUMERO'], -6)+1, "0");
			        $ultimoNumero = str_pad($ultimoValor, 6, "0", STR_PAD_LEFT);
			}
			    	
           		$NumeroNovo = $osi->CLIENTES_ID.$ultimoNumero;
			
			//DEFINE DATA DE EMISSAO  				
			$emissao = date('Y-m-d');
					
			
			//BUSCA POR CLIENTE		
			$queryPegarCodigoEmpresa = "SELECT * FROM clientes WHERE ID = '$osi->CLIENTES_ID'";
			$resultPegarCodigoEmpresa = $this->conn->query($queryPegarCodigoEmpresa);
			
			if($resultPegarCodigoEmpresa->num_rows == 0)
			{
				$resultado = false;
			}
			
			$rowPegarCodigoEmpresa = $resultPegarCodigoEmpresa->fetch_assoc();
			$osi->EMPRESA_ID = $rowPegarCodigoEmpresa['EMPRESA_ID'];
					
			//DEFINE DATA DE VENCIMENTO
			$vencimento = date('Y-m-d');
			
			//CADASTRA TITULO
			$sql = "INSERT INTO contas_receber (N_DOC,CLIENTES_ID, EMPRESA_ID, DATA_VENCIMENTO,VALOR_TITULO, STATUS_2, DATA_EMISSAO, N_NUMERO, 
			CONTROLE) VALUES
			('$ndocumento', '$osi->CLIENTES_ID','$osi->EMPRESA_ID', '$vencimento','$osi->VALOR', 'ABERTO', '$emissao','$NumeroNovo', '$ndocumento')";
					 
			$resultadoSql = $this->conn->query($sql);       		
   			
   		}
		
		
		
		if($this->conn->error)
		{
			$this->conn->rollback();
			$this->conn->autocommit(true);
			
			return 'ERRO ENTREGAR OSI: '.$this->conn->error;
		}else{
			if($resultado)
			{
				$this->conn->commit();
				$this->conn->autocommit(true);
			
				return 'ok';
			}else{
				$this->conn->rollback();
				$this->conn->autocommit(true);
			
				return 'ERRO ENTREGAR OSI: '.$this->conn->error;
			}
		}
	}
	public function excluirOSI($id)
	{
		$query = "DELETE FROM osi WHERE ID='$id'";
		$result = $this->conn->query($query);
	}
	public function gerarOSITxt($os)
	{
		$query = "SELECT osi.*,
			clientes.*,
			enderecos_principais.* 
					FROM osi
			    LEFT JOIN clientes ON clientes.ID = osi.CLIENTES_ID
			    LEFT JOIN enderecos_principais ON enderecos_principais.ID = osi.CLIENTES_ID 
			    WHERE osi.STATUS_2 != 'ENTREGUE' AND osi.ID = '$os' ";
		
		
		$result = $this->conn->query($query);
		$row = $result->fetch_assoc();
		
		
		$dataHoje = date('d/m/Y');
		$hora = date('h:m');
		
		$fp = fopen("./util/cupons/osi/OS".$os.".txt", "w+");
		
		$quebra = chr(13).chr(10);
		
		$linha =  nl2br(str_pad('d i g i t a l', 48, " ", STR_PAD_BOTH));
		$linha .= nl2br(str_pad("Rua Adjar Maciel, 35 Centro Belo Jardim/PE", 48, " ", STR_PAD_BOTH));
		$linha .= nl2br(str_pad("CEP: 55.150-040 Fone: (81)3726.3125", 48, " ", STR_PAD_BOTH));
		$linha .= nl2br(str_pad("CNPJ: 07.578.965/0001-05 IE: 18.3.050.0329", 48, " ", STR_PAD_BOTH));
		$linha .= nl2br(str_pad("www.digitalonline.com.br", 48, " ", STR_PAD_BOTH));
		$linha .= $quebra;
		$linha .= $quebra;
		$linha .= $quebra;
		$linha .= nl2br(str_pad("ORDEM DE SERVICO", 48, " ", STR_PAD_BOTH));
		$linha .= nl2br(str_pad("------------------------------------------------", 48, " ", STR_PAD_BOTH));
		$linha .= nl2br(str_pad("OS n�: ".$os." EMISSAO: ".$dataHoje." HORA: ".$hora."", 48, " ", STR_PAD_BOTH));
		$linha .= nl2br(str_pad("------------------------------------------------", 48, " ", STR_PAD_BOTH));	
		$linha .= $quebra;
		$linha .= $quebra;
		$linha .= nl2br("Cliente..: ".$row['NOME_RAZAO']."");
		$linha .= $quebra;
		$linha .= nl2br("".$row['ENDERECO'].", ".$row['NUMERO']." - ".$row['BAIRRO']." - ".$row['CIDADE']."/".$row['UF']."");
		$linha .= nl2br("Tel......: ".$row['TELEFONE1']." Cel......: ".$row['CELULAR1']."  - Email....: ".$row['EMAIL']."");
		$linha .= $quebra;
		$linha .= nl2br(str_pad("------------------------------------------------", 48, " ", STR_PAD_BOTH));
		$linha .= $quebra;
		$linha .= nl2br("PRODUTO:");
		$linha .= nl2br("".$row['EQUIPAMENTO']."");
		$linha .= $quebra;
		$linha .= nl2br("ACESSORIOS:");
		$linha .= nl2br("".$row['ACESSORIOS']."");
		$linha .= $quebra;
		$linha .= nl2br("OBS:");
		$linha .= nl2br("".$row['OBSERVACAO']."");
		$linha .= $quebra;
		$linha .= nl2br(str_pad("------------------------------------------------", 48, " ", STR_PAD_BOTH));
		$linha .= $quebra;
		$linha .= nl2br("Recebido por:");
		$linha .= nl2br("".$row['OPERADOR']."");
		$linha .= $quebra;
		$linha .= nl2br(str_pad("------------------------------------------------", 48, " ", STR_PAD_BOTH));
		$linha .= $quebra;
		$linha .= $quebra;
		$linha .= nl2br("Atencao:");
		$linha .= $quebra;
		$linha .= nl2br("- O produto so estara coberto pela garantia se apresentar defeito de fabricacao.");
		$linha .= $quebra;
		$linha .= nl2br("- Servico como instacao de sofwares nao serao cobertos pela garantia.");
		$linha .= $quebra;
		$linha .= nl2br("- Dados contidos no equip. estao sujeitos a serem perdidos.");
		$linha .= $quebra;
		$linha .= nl2br("- Caso produto nao seja retirado da assitencia no prazo de 90 dias, sera considerado abandonado pelo cliente e podera ser vendido para custear a manutecao e armazenamento.");
		$linha .= $quebra;
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
		return "util/cupons/osi/OS".$os.".txt";		
	}
	
	//------------TIPOS DE PROBLEMAS OSI--------------//
	public function listarTiposProblemasOSI($codEmpresa)
	{
		$query = "SELECT * FROM tipos_problemas_osi WHERE EMPRESA_ID = '$codEmpresa'";
		$result = $this->conn->query($query);
		$nRow = $result->num_rows;
		if($nRow == 0 ){return 'ERRO';}
		
		while ($row = $result->fetch_assoc())
		{
			$tiposProblema = new tipoProblema_OSI();
			
			$tiposProblema->ID = $row['ID'];	
			$tiposProblema->EMPRESA_ID = $row['EMPRESA_ID'];	
			$tiposProblema->NOME = $row['NOME'];	
			$tiposProblema->VALOR = $row['VALOR'];	
			
			$tiposProblemas[] = $tiposProblema;
			
		}
		
		return $tiposProblemas;
	}
	public function cadastrarTiposProblemasOSI(tipoProblema_OSI $tipoProblema)
	{
		$query = "INSERT INTO tipos_problemas_osi (ID ,EMPRESA_ID ,NOME ,VALOR)VALUES (NULL , '$tipoProblema->EMPRESA_ID', 
		'$tipoProblema->NOME', '$tipoProblema->VALOR')";
		$result = $this->conn->query($query);
	}
	
	//-----------TIPOS SERVICOS OSE-------------------//
	public function listarTiposServicosOSE($empresa) 
	{
		$query = "SELECT * FROM tipos_servicos_ose";
		$result = $this->conn->query($query);
		$nrow = $result->num_rows;
		if($nrow == 0){return 'ERRO';}
		
		while($row = $result->fetch_assoc())
		{
			$tipoServicoOSe = new TipoServicoOSeVO();
			
			$tipoServicoOSe->ID = $row['ID'];
			$tipoServicoOSe->EMPRESA_ID = $row['ID'];
			$tipoServicoOSe->DESCRICAO = $row['DESCRICAO'];
			
			$tipoServicoOSes[] = $tipoServicoOSe;
		}
		
		return $tipoServicoOSes;
	}
	public function selecionarTiposServicosOSE($cod)
	{
		$query = "SELECT * FROM tipos_servicos_ose WHERE ID = '$cod'";
		$result = $this->conn->query($query);
		$nrow = $result->num_rows;
		if($nrow == 0){return 'ERRO';}
		$row = $result->fetch_assoc();
		
			$tipoServicoOSe = new TipoServicoOSeVO();
			
			$tipoServicoOSe->ID = $row['ID'];
			$tipoServicoOSe->EMPRESA_ID = $row['ID'];
			$tipoServicoOSe->DESCRICAO = $row['DESCRICAO'];	
		
		
		return $tipoServicoOSe;
		
	}
	public function cadastrarTiposServicosOSE(TipoServicoOSeVO $tipoServOse) 
	{
		$query = "INSERT INTO  tipos_servicos_ose (
			ID ,EMPRESA_ID ,DESCRICAO)VALUES (NULL ,  '$tipoServOse->EMPRESA_ID',  '$tipoServOse->DESCRICAO'";
		$result = $this->conn->query($query);
	}
	public function cadastrarTipoServOSE(TipoServicoOSeVO $tipoServOse) 
	{
		
		$query = "INSERT INTO  tipos_servicos_ose (
			EMPRESA_ID ,DESCRICAO)VALUES ('$tipoServOse->EMPRESA_ID',  '$tipoServOse->DESCRICAO'";
		$result = $this->conn->query($query);
	}
	public function editarTiposServicosOSE(TipoServicoOSeVO $tipoServOse) 
	{
		$query = "UPDATE  tipos_servicos_ose SET  DESCRICAO =  '$tipoServOse->DESCRICAO' WHERE  ID ='$tipoServOse->ID'";
		$result = $this->conn->query($query);
	}
	public function excluirTiposServicosOSE($empresa) 
	{
		$query = "";
		$result = $this->conn->query($query);
	}
	
	
	//------------OSE--------------------------------//
	public function selecionarAntenaCliente($codCliente)
	{
		$query = "SELECT * FROM acesso_cliente WHERE CLIENTES_ID='$codCliente'";
		$result = $this->conn->query($query);
		$nRow = $result->num_rows;
		if($nRow == 0){
			return 'ERRO';
		}else{
			$row = $result->fetch_assoc();
			$codBase = $row['BASES_ID'];
			return $codBase;
		}
	}
	public function listarChamados($tipo, $status, $codEmpresa)
	{
		
		$tipo1 = explode(" ", $tipo);
		$tipo = $tipo1[0];
		
		$query = "SELECT ose.*, clientes.NOME_RAZAO FROM ose INNER JOIN clientes ON ose.CLIENTES_ID = clientes.ID WHERE ose.TIPO LIKE '%$tipo%'
		AND ose.STATUS_2 LIKE '%$status%'";
		
		$result = $this->conn->query($query);
		$nRow = $result->num_rows;
		if($nRow == 0){return 'ERRO';}
		
		while ($row = $result->fetch_assoc()) {
			
			$ose = new oseVO();
			
			$ose->ID = $row['ID'];
			$ose->EMPRESA_ID = $row['EMPRESA_ID'];
			$ose->CLIENTES_ID = $row['CLIENTES_ID'];
			$ose->NOME_RAZAO = $row['NOME_RAZAO'];
			$ose->TIPO = $row['TIPO'];
			$ose->DATA_EX = substr($row['DATA_EX'], 0, 10);
			$ose->DATA_ENCAMINHAMENTO = $row['DATA_ENCAMINHAMENTO'];
			$ose->DATA_ABERTURA = $row['DATA_ABERTURA'];
			$ose->DATA_FECHAMENTO = $row['DATA_FECHAMENTO'];
			$ose->TURNO = substr($row['TURNO'], 0, 1);
			$ose->BASE = $row['BASE'];
			$ose->CONTATO = $row['CONTATO'];
			$ose->BAIRRO = $row['BAIRRO'];
			$ose->ENDERECO = $row['ENDERECO'];
			$ose->REFERENCIA = $row['REFERENCIA'];
			$ose->STATUS_2 = substr($row['STATUS_2'], 0, 1);
			$ose->TIPO_ENCAMINHAMENTO = $row['TIPO_ENCAMINHAMENTO'];
			$ose->MOTIVO = $row['MOTIVO'];
			$ose->PROBLEMA = $row['PROBLEMA'];
			$ose->CONCLUSAO = $row['CONCLUSAO'];
			$ose->AUSENTE = $row['AUSENTE'];
			$ose->PRIORIDADE = $row['PRIORIDADE'];
			$ose->OBS = $row['OBS'];
			$ose->NOTA_FISCAL = $row['NOTA_FISCAL'];
			$ose->TIPO_SERVICO = $row['TIPO_SERVICO'];
			$ose->TECNICO = $row['TECNICO'];
			$ose->OPERADOR = $row['OPERADOR'];
			
			$oses[] = $ose;
		}
		
		return $oses;
	}
	public function listarMotivosCancelamento($codEmpresa)
	{
		$query = "SELECT * FROM motivos_cancelamento WHERE EMPRESA_ID = '$codEmpresa'";
		$result = $this->conn->query($query);
		$nRow = $result->num_rows;
		if($nRow == '0'){return 'ERRO';}
		while ($row = $result->fetch_assoc()) {
			$motivo = new motivoCancelamentoVO();
			$motivo->ID = $row['ID'];
			$motivo->EMPRESA_ID = $row['EMPRESA_ID'];
			$motivo->DESCRICAO = $row['DESCRICAO'];
			
			$motivos[] = $motivo;
		}
		
		return $motivos;
	}
	public function cadastrarMotivoCancelamento(motivoCancelamentoVO $motivo)
	{
		$query = "INSERT INTO motivos_cancelamento (EMPRESA_ID, DESCRICAO) VALUES ('$motivo->EMPRESA_ID', '$motivo->DESCRICAO')";
		if(!$result = $this->conn->query($query))
		{
			return $this->conn->error;
		}else{
			return 'ok';
		}
		
	}
	public function editarMotivoCancelamento(motivoCancelamentoVO $motivo)
	{
		$query = "UPDATE motivos_cancelamento SET DESCRICAO='$motivo->DESCRICAO' WHERE ID='$motivo->ID'";
		if(!$result = $this->conn->query($query))
		{
			return $this->conn->error;
		}else{
			return 'ok';
		}
		
	}
	public function excluirMotivoCancelamento($id)
	{
		$query = "DELETE FROM motivos_cancelamento WHERE ID='$id'";
		if(!$result = $this->conn->query($query))
		{
			return $this->conn->error;
		}else{
			return 'ok';
		}
	}
	public function listarTiposProblemaOse($codEmpresa)
	{
		$query = "SELECT * FROM tipos_problema_ose WHERE EMPRESA_ID = '$codEmpresa'";
		$result = $this->conn->query($query);
		$nRow = $result->num_rows;
		if($nRow == '0'){return 'ERRO';}
		
		while ($row = $result->fetch_assoc()) {
			$tipoProblema = new tipoProblema_OSE();
			$tipoProblema->ID = $row['ID'];
			$tipoProblema->EMPRESA_ID = $row['EMPRESA_ID'];
			$tipoProblema->DESCRICAO = $row['DESCRICAO'];
			
			$tipoProblemas[] = $tipoProblema;
		}
		
		return $tipoProblemas;
	}
	public function cadastrarOSE(oseVO $ose, EndPrincipaisVO $enderecoPrincipal, ClientesVO $cliente, $valor)
	{
		
		
		$gerarBoleto = false;
		
		//Tipos de Chamados
		if($ose->TIPO == 'CHAMADO INTERNET'){
			$tipo = 'CHAMADO';
			
		}elseif ($ose->TIPO == 'INSTALACAO INTERNET'){
			$tipo = 'INSTALACAO';
			
		}elseif ($ose->TIPO == 'MUDANCA'){
			$tipo = 'MUDANCA';
			
		}elseif ($ose->TIPO == 'CANCELAMENTO INTERNET'){
			$tipo = 'CANCELAMENTO';
			
		}elseif ($ose->TIPO == 'OUTROS'){
			$tipo = 'OUTROS';
			
		}
		
		
		
		//======Pegar valor do Boleto===================//
		$query_gerarBoleto = "SELECT * FROM tipos_ose WHERE NOME LIKE '%$tipo%'";
		$result_gerarBoleto = $this->conn->query($query_gerarBoleto);
		$row_gerarBoleto = $result_gerarBoleto->fetch_assoc();
		$nRow = $result_gerarBoleto->num_rows;
		
			if($row_gerarBoleto['GERAR_TITULO'] == '1')
			{
					$gerarBoleto = true;
					$valorTitulo = $row_gerarBoleto['VALOR_TITULO'];				
			}else
			{
					$gerarBoleto = false;
			}
		
			

			
		$dataHoje = date('Y-m-d h:s:i');
		$diaDataEx = substr($ose->DATA_EX, 0, 2);
		$mesDataEx = substr($ose->DATA_EX, 3, 2);
		$anoDataEx = substr($ose->DATA_EX, 6, 4);
		
		$dataExecucao = $anoDataEx.'-'.$mesDataEx.'-'.$diaDataEx;
			
			
		//-------------Criar Chamado
		$query = "INSERT INTO ose (
			EMPRESA_ID ,CLIENTES_ID ,TIPO ,DATA_EX ,DATA_ABERTURA ,TURNO ,BASE ,
			CONTATO ,BAIRRO ,ENDERECO ,REFERENCIA ,STATUS_2 ,MOTIVO ,PROBLEMA ,
			CONCLUSAO ,PRIORIDADE ,OBS ,NOTA_FISCAL ,TIPO_SERVICO ,OPERADOR
			)VALUES (
			'$ose->EMPRESA_ID', '$ose->CLIENTES_ID', '$tipo', '$dataExecucao', '$dataHoje', '$ose->TURNO', 
			'$ose->BASE', '$cliente->CONTATO', '$enderecoPrincipal->BAIRRO', '$enderecoPrincipal->ENDERECO', '$enderecoPrincipal->REFERENCIA', 
			'ABERTO' , '$ose->MOTIVO', 
			'$ose->PROBLEMA', '$ose->CONCLUSAO' , '$ose->PRIORIDADE', '$ose->OBS' , '$ose->NOTA_FISCAL' , '$ose->TIPO_SERVICO', '$ose->OPERADOR')";
		$result = $this->conn->query($query);
		
		
		//--------------ATUALIZA CONTATO, TELEFONE1, TELEFONE2, CELULAR1, CELULAR2 DO CLIENTE
		$queryContato = "UPDATE clientes SET CONTATO='$cliente->CONTATO',EMAIL='$cliente->EMAIL', TELEFONE1='$cliente->TELEFONE1', TELEFONE2='$cliente->TELEFONE2', 
		CELULAR1='$cliente->CELULAR1', CELULAR2='$cliente->CELULAR2' WHERE ID='$cliente->ID'";
		if(!$resultContato = $this->conn->query($queryContato))
		{
			return 'Atualiza��o de Cadastro Principal: '.$this->conn->error;
		}
		
		//--------------ATUALIZA PONTO DE REFER�NCIA DO ENDERECO PRINCIPAL DO CLIENTE
		$queryUpEndereco = "UPDATE enderecos_principais SET REFERENCIA='$enderecoPrincipal->REFERENCIA', COMPLEMENTO='$enderecoPrincipal->COMPLEMENTO' WHERE CLIENTES_ID='$enderecoPrincipal->CLIENTES_ID'";
		if(!$resultUpEndereco = $this->conn->query($queryUpEndereco))
		{
			return 'Atualiza��o de Endere�o: '.$this->conn->error;
		}
				
		if($gerarBoleto)
		{
				//Gerar Boletos
				///////////////////////

				$qtd = 1;
							
				$quantidade = $qtd;
   				$_prazo = 0;   
   				
   				$queryUltimaOSE = "SELECT * FROM ose ORDER by ID DESC LIMIT 0, 1";
   				$resultUltimaOSE = $this->conn->query($queryUltimaOSE);
   				$rowUltimaOSE = $resultUltimaOSE->fetch_assoc();
   				
   					if($tipo == 'INSTALACAO')
					{
						$ndocumento = $ose->CLIENTES_ID.'/INSTALACAO';
					}else if($tipo == 'MUDANCA')
					{
						$ndocumento = $ose->CLIENTES_ID.'/MUDANCA';
					}else{
						$ndocumento = 'OSE_'.$rowUltimaOSE['ID'];
					} 
					
					
    		  	//$ndocumento = 'OSE_'.$rowUltimaOSE['ID'];
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
//					$_dia = substr($vencimento, 0, 2);
//   					$_mes = substr($vencimento, 3, 2);
//   					$_ano  = substr($vencimento, 6, 4);
//			
//	   				$_ts = mktime(0,0,0,$_mes+$_prazo,$_dia,$_ano);
//       				$_data = date('Y-m-d',$_ts);
	   				
	   				
	  				
					$emissao = date('Y-m-d');
					
					
					if($tipo == 'INSTALACAO')
					{
						$ndocumento = $codigoCliente.'/INSTALACAO';
					}else{
						$ndocumento = $codigoCliente.'/'.$ano.'/'.$sequencia2;
					} 
					
					$queryPegarCodigoEmpresa = "SELECT * FROM clientes WHERE ID = '$codigoCliente'";
					$resultPegarCodigoEmpresa = $this->conn->query($queryPegarCodigoEmpresa);
					$nRowPegarCodigoEmpresa = $resultPegarCodigoEmpresa->num_rows;
					if($nRowPegarCodigoEmpresa != 0)
					{
						$rowPegarCodigoEmpresa = $resultPegarCodigoEmpresa->fetch_assoc();
						$codigoEmpresa = $rowPegarCodigoEmpresa['EMPRESA_ID'];
					}else
					{
						$codigoEmpresa = '1';
					}
					
					$vencimento = date('Y-m-d');
					//Inserindo
					$sql = "INSERT INTO contas_receber (N_DOC,CLIENTES_ID, EMPRESA_ID, DATA_VENCIMENTO,VALOR_TITULO, STATUS_2, DATA_EMISSAO, N_NUMERO, 
					CONTROLE) VALUES
					('$ndocumento', '$codigoCliente','$codigoEmpresa', '$vencimento','$valorTitulo', 'ABERTO', '$emissao','$NumeroNovo', '$ndocumento')";
					 
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

					
       
					$queryUltimoBoleto = "SELECT * FROM contas_receber WHERE CLIENTES_ID = '$ose->CLIENTES_ID' ORDER by ID DESC";
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
			    	
           			$NumeroNovo = $ose->CLIENTES_ID.$ultimoNumero;
					
					//Serie do ultimo boleto gerado
					$ultimoBoleto = $ultimoNumero+1;
					
//					//$dataPrimeiroBoleto = "10/01/2011";
//					$_dia = substr($vencimento, 0, 2);
//   					$_mes = substr($vencimento, 3, 2);
//   					$_ano  = substr($vencimento, 6, 4);
//			
//	   				$_ts = mktime(0,0,0,$_mes+$_prazo,$_dia,$_ano);
//       				$_data = date('Y-m-d',$_ts);
	   				
	   				
	  				
					$emissao = date('Y-m-d');
					
   					$queryPegarCodigoEmpresa = "SELECT * FROM clientes WHERE ID = '$ose->CLIENTES_ID'";
					$resultPegarCodigoEmpresa = $this->conn->query($queryPegarCodigoEmpresa);
					$nRowPegarCodigoEmpresa = $resultPegarCodigoEmpresa->num_rows;
					if($nRowPegarCodigoEmpresa != 0)
					{
						$rowPegarCodigoEmpresa = $resultPegarCodigoEmpresa->fetch_assoc();
						$codigoEmpresa = $rowPegarCodigoEmpresa['EMPRESA_ID'];
					}else
					{
						$codigoEmpresa = '1';
					}
					
					$vencimento = date('Y-m-d');			
					$sql = "INSERT INTO contas_receber (N_DOC,CLIENTES_ID, EMPRESA_ID, DATA_VENCIMENTO,VALOR_TITULO, STATUS_2, DATA_EMISSAO, N_NUMERO, 
					CONTROLE) VALUES
					('$ndocumento', '$ose->CLIENTES_ID','$codigoEmpresa', '$vencimento','$valorTitulo', 'ABERTO', '$emissao','$NumeroNovo', '$ndocumento')";
					 
					$resultadoSql = $this->conn->query($sql);
        			
        			//supondo que o vencimento � de 30 em 30 dias 
					
        			$_prazo += 1;
        			
    			
    			}
		}	
		}//if
		
		return 'ok';
	}
	public function listarTiposOSE($codEmpresa)
	{
		
		
		$query = "SELECT * FROM tipos_ose WHERE EMPRESA_ID LIKE '$codEmpresa' ORDER by ID ASC" ;
		$result = $this->conn->query($query);
		$nRow = $result->num_rows;
		if($nRow == '0'){return 'ERRO';}
		
		//return $nRow;
		
		while ($row = $result->fetch_assoc()) {
			$tipoOSE = new tipoOSe_VO();
			$tipoOSE->ID = $row['ID'];
			$tipoOSE->EMPRESA_ID = $row['EMPRESA_ID'];
			$tipoOSE->NOME = $row['NOME'];
			$tipoOSE->PONTOS = $row['PONTOS'];
			$tipoOSE->META = $row['META'];
			$tipoOSE->GERAR_TITULO = $row['GERAR_TITULO'];
			$tipoOSE->VALOR_TITULO = $row['VALOR_TITULO'];
			
			$tipoOSEs[] = $tipoOSE;
		}
		
		return $tipoOSEs;
	}
	public function encaminharOSE($codOSE, $tipoEncaminhamento, $tecnico)
	{
		$dataEncaminhamento = date('Y-m-d h:s:i');
		$query = "UPDATE ose SET DATA_ENCAMINHAMENTO = '$dataEncaminhamento',
		STATUS_2 = 'EM ANDAMENTO',TIPO_ENCAMINHAMENTO = '$tipoEncaminhamento',TECNICO = '$tecnico' WHERE ID = '$codOSE'";
		$result = $this->conn->query($query);
		
	}
	public function fecharOSE( $idChamado,$laudo,$idMaterial,$idBase )
	{
		//SELECIONA CHAMADO
		$queryDadosChamados = "SELECT * FROM ose WHERE ID = $idChamado";
		$resultDadosChamados = $this->conn->query($queryDadosChamados);
		$rowDadosChamados = $resultDadosChamados->fetch_assoc();
		$nRowDadosChamados = $resultDadosChamados->num_rows;
		if($nRowDadosChamados == 0)
		{
			return 'N�O FOI LOCALIZADO NENHUM OSE!';
		}
		$idCliente = $rowDadosChamados['CLIENTES_ID'];				
		$data_fechamento = date('Y-m-d h:i:s');
		
		
		
		//ATUALIZAR MATERIAL E BASE NO ACESSO DO CLIENTE
		$queryAcesso = "UPDATE acesso_cliente SET BASES_ID='$idBase', MATERIAL_ACESSO_ID='$idMaterial' 
		WHERE CLIENTES_ID='$idCliente'";
		if(!$resultAcesso = $this->conn->query($queryAcesso))
		{
			return $this->conn->error;
		}
		
		//ATUALIZA STATUS DO CHAMADO INCLUI DATA DE FECHAMENTO E CONCLUS�O	
		$queryChamado = "UPDATE ose SET STATUS_2 = 'FECHADO', DATA_FECHAMENTO='$data_fechamento',  
		CONCLUSAO='$laudo' WHERE ID = '$idChamado'";
		if(!$result = $this->conn->query($queryChamado))
		{
			return $this->conn->error;
		}
		
		return 'ok';
					
	}
	public function excluirOSE($id)
	{
		$query = "DELETE FROM ose WHERE ID = '$id'";
		$result = $this->conn->query($query);		
	}
	public function reagendarChamado($id, $data, $obs, $prioridade, $turno)
	{
		
		//return;
		$diaDataEx = substr($data, 0, 2);
		$mesDataEx = substr($data, 3, 2);
		$anoDataEx = substr($data, 6, 4);
		
		$dataExecucao = $anoDataEx.'-'.$mesDataEx.'-'.$diaDataEx;
		
		$query = "UPDATE ose SET DATA_EX = '$dataExecucao', STATUS_2 = 'ABERTO', 
		OBS = '$obs', PRIORIDADE = '$prioridade', TURNO= '$turno' WHERE ID = '$id'";
		$result = $this->conn->query($query) or  die($this->conn->error); 
		
		return $result;
	}
	public function editarOutrosServ($id, $codigoCliente, $tipoServ, $obs, $valor)
	{
		$query = "UPDATE ose SET TIPO='OUTROS', TIPO_SERVICO='$tipoServ', OBS='$obs' WHERE ID='$id'";
		$result = $this->conn->query($query);
		
		if($valor != '0,00')
		{        			
				//Gerar Boletos
				///////////////////////

				$qtd = 1;
							
				$quantidade = $qtd;
   				$_prazo = 0;   
   				
    		  	$ndocumento = 'OSE_'.$id;
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
//					$_dia = substr($vencimento, 0, 2);
//   					$_mes = substr($vencimento, 3, 2);
//   					$_ano  = substr($vencimento, 6, 4);
//			
//	   				$_ts = mktime(0,0,0,$_mes+$_prazo,$_dia,$_ano);
//       				$_data = date('Y-m-d',$_ts);
	   				
	   				
	  				
					$emissao = date('Y-m-d');
					
					
					
					$ndocumento = $codigoCliente.'/'.$ano.'/'.$sequencia2; 
					
					$queryPegarCodigoEmpresa = "SELECT * FROM clientes WHERE ID = '$codigoCliente'";
					$resultPegarCodigoEmpresa = $this->conn->query($queryPegarCodigoEmpresa);
					$nRowPegarCodigoEmpresa = $resultPegarCodigoEmpresa->num_rows;
					if($nRowPegarCodigoEmpresa != 0)
					{
						$rowPegarCodigoEmpresa = $resultPegarCodigoEmpresa->fetch_assoc();
						$codigoEmpresa = $rowPegarCodigoEmpresa['EMPRESA_ID'];
					}else
					{
						$codigoEmpresa = '1';
					}
					
					$vencimento = date('Y-m-d');
					//Inserindo
					$sql = "INSERT INTO contas_receber (N_DOC,CLIENTES_ID, EMPRESA_ID, DATA_VENCIMENTO,VALOR_TITULO, STATUS_2, DATA_EMISSAO, N_NUMERO, 
					CONTROLE) VALUES
					('$ndocumento', '$codigoCliente','$codigoEmpresa', '$vencimento','$valor', 'ABERTO', '$emissao','$NumeroNovo', '$ndocumento')";
					 
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
					
//					//$dataPrimeiroBoleto = "10/01/2011";
//					$_dia = substr($vencimento, 0, 2);
//   					$_mes = substr($vencimento, 3, 2);
//   					$_ano  = substr($vencimento, 6, 4);
//			
//	   				$_ts = mktime(0,0,0,$_mes+$_prazo,$_dia,$_ano);
//       				$_data = date('Y-m-d',$_ts);
	   				
	   				
	  				
					$emissao = date('Y-m-d');
					
   					$queryPegarCodigoEmpresa = "SELECT * FROM clientes WHERE ID = '$codigoCliente'";
					$resultPegarCodigoEmpresa = $this->conn->query($queryPegarCodigoEmpresa);
					$nRowPegarCodigoEmpresa = $resultPegarCodigoEmpresa->num_rows;
					if($nRowPegarCodigoEmpresa != 0)
					{
						$rowPegarCodigoEmpresa = $resultPegarCodigoEmpresa->fetch_assoc();
						$codigoEmpresa = $rowPegarCodigoEmpresa['EMPRESA_ID'];
					}else
					{
						$codigoEmpresa = '1';
					}
					
					$vencimento = date('Y-m-d');			
					$sql = "INSERT INTO contas_receber (N_DOC,CLIENTES_ID, EMPRESA_ID, DATA_VENCIMENTO,VALOR_TITULO, STATUS_2, DATA_EMISSAO, N_NUMERO, 
					CONTROLE) VALUES
					('$ndocumento', '$codigoCliente','$codigoEmpresa', '$vencimento','$valor', 'ABERTO', '$emissao','$NumeroNovo', '$ndocumento')";
					 
					$resultadoSql = $this->conn->query($sql);
        			
        			//supondo que o vencimento � de 30 em 30 dias 
					
        			$_prazo += 1;
        			
    			
    			}
		}	
		}

	}
	public function fecharOSeOutros(oseVO $ose)
	{
		$dataFechamento = date('Y-m-d');
		$query = "UPDATE ose SET DATA_FECHAMENTO='$dataFechamento', CONCLUSAO='$ose->CONCLUSAO',
		 STATUS_2='FECHADO' WHERE ID='$ose->ID'";
		$result = $this->conn->query($query);
		
	}
	
	
	
	//------------ANTENAS---------------------------//
	public function listarBases($codEmpresa)
	{
		$query = "SELECT * FROM bases WHERE EMPRESA_ID = '$codEmpresa'";	
		$result = $this->conn->query($query);
		$nRow = $result->num_rows;
		if($nRow == '0'){return 'ERRO';}
		
		while ($row = $result->fetch_assoc())
		{
			$base = new BaseVO();
			$base->ID = $row['ID']; 	
			$base->EMPRESA_ID = $row['EMPRESA_ID']; 	
			$base->NOME = $row['NOME']; 	
			$bases[] = $base;
		}
		
		return $bases;
	}
	
	//-----------PRODUTOS-----------------------------//
	public function listarProdutos($codEmpresa)
	{
		$query = "SELECT * FROM produtos WHERE EMPRESA_ID = '$codEmpresa'";
		$result = $this->conn->query($query);
		$nRow = $result->num_rows;
		if($nRow == '0'){return 'ERRO';}
		
		while ($row = $result->fetch_assoc())
		{
			$produto = new ProdutosVO();
			$produto->ID = $row['ID'];
			$produto->EMPRESA_ID = $row['EMPRESA_ID'];
			$produto->DESCRICAO = $row['DESCRICAO'];
			$produto->VALOR = $row['VALOR'];
			$produto->QTD_ESTOQUE = $row['QTD_ESTOQUE'];
			$produto->QTD_MINIMA = $row['QTD_MINIMA'];
			$produto->TOTAL_VALOR = $row['TOTAL_VALOR'];
			
			$produtos[] = $produto;
		}
		
		

		return $produtos;
	}
	public function listarProdutosCliente($codCliente)
	{
		$query = "SELECT acesso_cliente. *, material_acesso. *  FROM acesso_cliente INNER JOIN material_acesso ON 
		acesso_cliente.MATERIAL_ACESSO_ID = material_acesso.ID WHERE acesso_cliente.CLIENTES_ID = '$codCliente'";
		$result = $this->conn->query($query);
		
		$qtd = $result->num_rows;
		
			if($qtd > 0)
			{
					while ($row = $result->fetch_assoc())
					{
					$produto = new ProdutosVO();
					
					$produto->ID = $row['ID'];
					$produto->EMPRESA_ID = $row['EMPRESA_ID']; 	
					$produto->NOME = $row['NOME'];
					$produto->VALOR = $row['VALOR'];
					$produto->QTD_ESTOQUE = $row['QTD_ESTOQUE'];
					$produto->QTD_MINIMA = $row['QTD_MINIMA'];
					$produto->TOTAL_VALOR = $row['TOTAL_VALOR'];
					
					$produtos[] = $produto;
					}
			}
			else 
			{
					$produto = new ProdutosVO();
					
					$produto->ID = '0';
					$produto->NOME = 'Cliente n�o Possui Material de Acesso';
					
					$produtos[] = $produto;
			}
		
		
		return $produtos;
	}
	public function atualizarQtdProduto($id,$op,$qtdAtual)
	{
		//INCLUIR ID CLIENTE E VERIFICAR SE ELE � COMODATO OU N�O
		
		$queryProdutos = "SELECT * FROM material_acesso WHERE ID = '$id'";
		$resultProdutos = $this->conn->query($queryProdutos);
		$rowProdutos = $resultProdutos->fetch_assoc();
		
		$qtdAtual = $rowProdutos['QTD_ESTOQUE'];
		
		$antiga_qtd = $qtdAtual;
			
		if($op == 'somar')
			{
				$nova_qtd = $antiga_qtd+1;
				
			}
		else if($op == 'subtrair')
			{
				$nova_qtd = $antiga_qtd-1;
			}
		
		$query = "UPDATE material_acesso SET QTD_ESTOQUE = '$nova_qtd' WHERE ID = '$id'";
		$result = $this->conn->query($query);
	}
	public function listarProdutosComEstoque($codEmpresa)
	{
		$query = "SELECT * FROM material_acesso WHERE QTD_ESTOQUE >= '1' 
		AND EMPRESA_ID = '$codEmpresa' ORDER by NOME ASC";
		$result = $this->conn->query($query);
		
		while ($row = $result->fetch_assoc())
		{
			
			$produto = new ProdutosVO();
			$produto->ID = $row['ID'];
			$produto->NOME = $row['NOME'];
			$produto->DESCRICAO = $row['DESCRICAO'];
			$produto->VALOR = $row['VALOR'];
			$produto->QTD_ESTOQUE = $row['QTD_ESTOQUE'];
			$produto->QTD_MINIMA = $row['QTD_MINIMA'];
			$produto->TOTAL_VALOR = $row['TOTAL_VALOR'];
			
			$produtos[] = $produto;
		}
		
		

		return $produtos;
	}
}