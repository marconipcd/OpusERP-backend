<?php

require_once 'classes/Conexao.php';

require_once 'vo/Assinaturas_CtvVO.php';

class ClaroTv extends Conexao
{
	public function listarAssinaturasCtv($codEmpresa,$tipo)
	{
		
		
		
		if($tipo == 'APROVACAO')
		{
				$query = "SELECT c.*,actv.*,ep.* FROM
							clientes as c, assinaturas_ctv as actv, enderecos_principais as ep
							WHERE c.ID = actv.CLIENTE_ID AND c.ID = ep.CLIENTES_ID
							AND actv.ANDAMENTO LIKE '%PENDENTE%' 
							AND actv.APROVACAO_CREDITO = ''
							AND actv.EMPRESA_ID = '$codEmpresa'
							ORDER by actv.ID DESC";
		
		}else if($tipo == 'VSALES')
		{			
				$query = "SELECT c.*,actv.*,ep.* FROM
							clientes as c, assinaturas_ctv as actv, enderecos_principais as ep
							WHERE c.ID = actv.CLIENTE_ID AND c.ID = ep.CLIENTES_ID
							AND actv.ANDAMENTO LIKE '%PENDENTE%' 
							AND actv.APROVACAO_CREDITO != ''
							AND actv.V_SALES = ''
							AND actv.EMPRESA_ID = '$codEmpresa'
							ORDER by actv.ID DESC";
		
		}else if($tipo == 'DOCUMENTACAO')
		{
			$query = "SELECT c.*,actv.*,ep.* FROM
							clientes as c, assinaturas_ctv as actv, enderecos_principais as ep
							WHERE c.ID = actv.CLIENTE_ID AND c.ID = ep.CLIENTES_ID
							AND actv.ANDAMENTO LIKE '%PENDENTE%' 
							AND actv.APROVACAO_CREDITO != ''
							AND actv.V_SALES != ''
							AND actv.DOCUMENTACAO = ''
							AND actv.EMPRESA_ID = '$codEmpresa'
							ORDER by actv.ID DESC";
		}
		else if($tipo == 'CONFIRMACAO')
		{
			
			$query = "SELECT c.*,actv.*,ep.* FROM
					clientes as c, assinaturas_ctv as actv, enderecos_principais as ep
					WHERE c.ID = actv.CLIENTE_ID AND c.ID = ep.CLIENTES_ID
					AND actv.ANDAMENTO LIKE '%PENDENTE%' OR actv.ANDAMENTO LIKE '%AGENDADO%'
					AND actv.APROVACAO_CREDITO != ''
					AND actv.V_SALES != ''
					AND actv.DOCUMENTACAO != ''
					AND actv.COFNIRMACAO_CLIENTE = ''
					AND actv.EMPRESA_ID = '$codEmpresa'
					ORDER by actv.ID DESC";
								
		}else if($tipo == 'APROVADOS')
		{
			$query = "SELECT c.*,actv.*,ep.* FROM
								clientes as c, assinaturas_ctv as actv, enderecos_principais as ep
								WHERE c.ID = actv.CLIENTE_ID AND c.ID = ep.CLIENTES_ID
								AND (actv.ANDAMENTO  LIKE 'PENDENTE' OR actv.ANDAMENTO LIKE 'AGENDADO')								
								AND actv.APROVACAO_CREDITO != ''
								AND actv.V_SALES != ''
								AND actv.DOCUMENTACAO != ''
								AND actv.EMPRESA_ID = '$codEmpresa'
								ORDER by actv.ID DESC";
		}
		
		
						
		$result = $this->conn->query($query);
		$nRow = $result->num_rows;
		if($nRow ==0){return 'ERRO';}
		
		while($row = $result->fetch_assoc())
		{
			$assinaturasCtv = new Assinaturas_CtvVO();
			
			$assinaturasCtv->ID = $row['ID'];
			$assinaturasCtv->EMPRESA_ID = $row['EMPRESA_ID'];
			$assinaturasCtv->CLIENTE_ID = $row['CLIENTE_ID'];
			$assinaturasCtv->NOME_RAZAO = $row['NOME_RAZAO'];
			$assinaturasCtv->DOC_CPF_CNPJ = $row['DOC_CPF_CNPJ'];
			$assinaturasCtv->TELEFONES = $row['ANDAMENTO'].'-'.$row['TELEFONE2'];
			$assinaturasCtv->CIDADE = $row['CIDADE'];
			$assinaturasCtv->PROPOSTA = $row['PROPOSTA'];
			$assinaturasCtv->CONTRATO = $row['CONTRATO'];
			$assinaturasCtv->TV_POR_ASSINATURA = $row['TV_POR_ASSINATURA']; 
			$assinaturasCtv->PACOTE_ADD_FUT_CLUBE_AZUL = $row['PACOTE_ADD_FUT_CLUBE_AZUL']; 
			$assinaturasCtv->PACOTE_ADD_FUT_CLUBE_VERDE = $row['PACOTE_ADD_FUT_CLUBE_VERDE'];
			$assinaturasCtv->PACOTE_ADD_FUT_CLUBE_AMARELO = $row['PACOTE_ADD_FUT_CLUBE_AMARELO'];
			$assinaturasCtv->PACOTE_ADD_FUT_CLUBE_BRANCO = $row['PACOTE_ADD_FUT_CLUBE_BRANCO'];
			$assinaturasCtv->PACOTE_ADD_FUT_CLUBE_PRETO = $row['PACOTE_ADD_FUT_CLUBE_PRETO'];
			$assinaturasCtv->PACOTE_ADD_FUT_CLUBE_VERMELHO = $row['PACOTE_ADD_FUT_CLUBE_VERMELHO'];
			$assinaturasCtv->PACOTE_ADULT_SEXYHOT = $row['PACOTE_ADULT_SEXYHOT'];
			$assinaturasCtv->PACOTE_ADULT_PLAYBOY = $row['PACOTE_ADULT_PLAYBOY'];
			$assinaturasCtv->PACOTE_ADULT_FORMAN = $row['PACOTE_ADULT_FORMAN'];
			$assinaturasCtv->PACOTE_ADULT_COMBATE = $row['PACOTE_ADULT_COMBATE'];
			$assinaturasCtv->OUTROS_PRODUTOS = $row['OUTROS_PRODUTOS'];
			$assinaturasCtv->PLANO_ESCOLHIDO = $row['PLANO_ESCOLHIDO'];
			$assinaturasCtv->PONTO_EXTRA = $row['PONTO_EXTRA'];
			$assinaturasCtv->DIA_PAGAMENTO = $row['DIA_PAGAMENTO'];
			$assinaturasCtv->DEBITO_EM_CONTA = $row['DEBITO_EM_CONTA'];
			$assinaturasCtv->BANCO = $row['BANCO'];
			$assinaturasCtv->AGENCIA = $row['AGENCIA'];
			$assinaturasCtv->CONTA_CORRENTE = $row['CONTA_CORRENTE'];
			$assinaturasCtv->TITULAR = $row['TITULAR'];
			$assinaturasCtv->CPF_TITULAR = $row['CPF_TITULAR'];
			$assinaturasCtv->CONFIRMACAO_TELEFONE = $row['CONFIRMACAO_TELEFONE']; 
			$assinaturasCtv->AUTORIZACAO_CONFIRMACAO = $row['AUTORIZACAO_CONFIRMACAO'];
			$assinaturasCtv->ESTA_CIENTE = $row['ESTA_CIENTE'];
			$assinaturasCtv->NOME_AMIGO = $row['NOME_AMIGO'];
			$assinaturasCtv->TELEFONE_AMIGO = $row['TELEFONE_AMIGO'];
			$assinaturasCtv->APROVACAO_CREDITO = $row['APROVACAO_CREDITO'];
			$assinaturasCtv->COFNIRMACAO_CLIENTE = $row['COFNIRMACAO_CLIENTE'];
			$assinaturasCtv->DOCUMENTACAO = $row['DOCUMENTACAO'];
			$assinaturasCtv->V_SALES = $row['V_SALES'];
			$assinaturasCtv->CONSULTOR_PARCEIRO = $row['CONSULTOR_PARCEIRO']; 
			$assinaturasCtv->EMPRESA = $row['EMPRESA'];
			$assinaturasCtv->ANDAMENTO = $row['ANDAMENTO'];
			$assinaturasCtv->ORIGEM = $row['ORIGEM'];
			
			$assinaturasCtvs[] = $assinaturasCtv;
			
		}
		
		return $assinaturasCtvs;
	}
}