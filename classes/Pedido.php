<?php

//CLASSE DE CONEXAO
require_once 'classes/Conexao.php';


//OBJETOS VO
require_once 'vo/Assinaturas_CtvVO.php';
require_once 'vo/DavCabecalhoVO.php';
require_once 'vo/EcfVendaCabecalhoVO.php';

class Pedido extends Conexao
{
    /**
    * Função Retorna Cod do Prox. Movimento de Entrada Cabecalho de NF
    * @author Marconi César
    * @name pegaCodMovimentoNF
	 */
    public function procurarPedidos($natureza, $situacao, $formaPgto,$data1,$data2,$codEmpresa)
    {
        //DEFINE A TABELA DE PROCURA DE ACORDO COM A NATUREZA INFORMADA
        if($natureza == 'PREVENDA')
        {
            $tabela = 'ecf_pre_venda_cabecalho';
            $campoData = 'DATA_PV';
        }else if($natureza == 'ORCAMENTO')
        {
            $tabela = 'dav_cabecalho';
            $campoData = 'DATA_EMISSAO';
        }
        
        //DEFINE SITUACAO
        if($situacao == 'ABERTO')
        {
            $situacao = 'A';
        }else if($situacao == 'FECHADO')
        {
            $situacao = 'F';
        }else if($situacao == 'CANCELADO')
        {
            $situacao = 'C';
        }
        
        //VERIFICA SE FOI INFORMADO A FORMA DE PAGAMENTO
        if($formaPgto != '')
        {
            //PROCURA POR FORMA DE PAGAMENTO
            $query0 = "SELECT * FROM  formas_pagamento WHERE NOME LIKE '%$formaPgto%' ";
            $result0 = $this->conn->query($query0);
            
            if($result0->num_rows > 0)
            {
                $row0 = $result0->fetch_assoc();
                $ID_FORMA_PGTO = $row0['ID'];
            }else{
                $ID_FORMA_PGTO = '';
            }
        }else{
                $ID_FORMA_PGTO = '';
        }
        
        //VERIFICA SE EXISTEM DATAS
        if($data1 != '')
        {
            $ArrData1 = explode('/',$data1);
            $ArrData2 = explode('/',$data2);
            
            $data1 = $ArrData1[2].'-'.$ArrData1[1].'-'.$ArrData1[0];
            $data2 = $ArrData2[2].'-'.$ArrData2[1].'-'.$ArrData2[0];
            
            
            //MONTA QUERY COM DATA
            $query = "SELECT * FROM ".$tabela." WHERE SITUACAO LIKE '%$situacao%' AND FORMAS_PAGAMENTO_ID LIKE '%$formaPgto%'
            AND ".$campoData." >=   '$data1' AND ".$campoData." <= '$data2' AND EMPRESA_ID = '$codEmpresa' ";
        }else{
            //MONTA QUERY SEM DATA
            $query = "SELECT * FROM ".$tabela." WHERE SITUACAO LIKE '%$situacao%' AND FORMAS_PAGAMENTO_ID LIKE '%$ID_FORMA_PGTO%'
            AND EMPRESA_ID = '$codEmpresa'";
        }
        
        //CONSULTA NO BANCO
        $result = $this->conn->query($query);
        
        if($result->num_rows == 0)
        {
            return 'ERRO';
        }
        
        while($row = $result->fetch_assoc())
        {
            if($natureza == 'PREVENDA')
            {
                $Object = new EcfVendaCabecalhoVO();               
                
                $Object->ID = $row['ID'];
                $Object->EMPRESA_ID = $row['EMPRESA_ID'];
                $Object->FORMAS_PAGAMENTO_ID = $row['FORMAS_PAGAMENTO_ID'];
                $Object->DATA = $row['DATA_PV'];
                $Object->HORA_PV = $row['HORA_PV'];
                $Object->SITUACAO = $row['SITUACAO'];
                $Object->CCF = $row['CCF'];
                $Object->VALOR = $row['VALOR'];
                        
            }else if($natureza == 'ORCAMENTO')
            {
                $Object = new DavCabecalhoVO();
                
                $Object->ID = $row['ID'];
		$Object->CLIENTES_ID = $row['CLIENTES_ID'];
		$Object->EMPRESA_ID = $row['EMPRESA_ID'];
		$Object->NUMERO_DAV = $row['NUMERO_DAV'];
		$Object->NUMERO_ECF = $row['NUMERO_ECF'];
		$Object->CCF = $row['CCF'];
		$Object->COO = $row['COO'];
		$Object->NOME_DESTINATARIO = $row['NOME_DESTINATARIO'];
		$Object->CPF_CNPJ_DESTINATARIO = $row['CPF_CNPJ_DESTINATARIO'];
		$Object->DATA = $row['DATA_EMISSAO'];
		$Object->HORA_EMISSAO = $row['HORA_EMISSAO'];
		$Object->SITUACAO = $row['SITUACAO'];
		$Object->TAXA_ACRESCIMO = $row['TAXA_ACRESCIMO'];
		$Object->ACRESCIMO = $row['ACRESCIMO'];
		$Object->TAXA_DESCONTO = $row['TAXA_DESCONTO'];
		$Object->DESCONTO = $row['DESCONTO'];
		$Object->SUBTOTAL = $row['SUBTOTAL'];
		$Object->VALOR = $row['VALOR'];
		$Object->IMPRESSO = $row['IMPRESSO'];
		$Object->HASH_TRIPA = $row['HASH_TRIPA'];
		$Object->HASH_INCREMENTO = $row['HASH_INCREMENTO'];
            }
            
                $Objects[] = $Object;           
            
            
        }
        
                return $Objects;
        
    }
}

?>