<?php


require_once '../../Connections/system.php';
ini_set('max_execution_time','900000000000000000');

mysql_select_db($database_system, $system);


			//VARIAVEIS
			$status = $_GET['status'];
			$data = $_GET['data'];
			$dataIn = $_GET['dataI'];
			$dataFn = $_GET['dataF'];
			$filtro = $_GET['filtro'];
			$vlr_filtro = $_GET['vlr_filtro'];
			$cliente = $_GET['cliente'];
			$empresa = $_GET['empresa'];

			//DATAS	
				if($data == 'EMISSAO')
				{
					$data1 = 'DATA_EMISSAO';
				}else if($data == 'VENCIMENTO')
				{
					$data1 = 'DATA_VENCIMENTO';
				}else if($data == 'PAGAMENTO')
				{
					$data1 = 'DATA_PAGAMENTO';
				}
				
				if($dataIn != " ")
				{
					$diaIn = substr($dataIn, 0, 2);
					$mesIn = substr($dataIn, 3, 2);
					$anoIn = substr($dataIn, 6, 4);
				
					$dataIn = $anoIn.'-'.$mesIn.'-'.$diaIn;
				}else
				{
					$dataIn = "1800-00-00";
				}
				
				$diaFn = substr($dataFn, 0, 2);
				$mesFn = substr($dataFn, 3, 2);
				$anoFn = substr($dataFn, 6, 4);
				
				$dataFn = $anoFn.'-'.$mesFn.'-'.$diaFn;
				
					//FILTROS	
					if($filtro == 'N. DOCUMENTO')
					{
						$filtro1 = 'N_DOC';
					}else if($filtro == 'N. NUMERO')
					{
						$filtro1 = 'N_NUMERO';
					}else if($filtro == 'VALOR DOC')
					{
						$filtro1 = 'VALOR_TITULO';
					}else if($filtro == 'VALOR PAGO')
					{
						$filtro1 = 'VALOR_PAGAMENTO';
					}else if($filtro == 'CONTROLE')
					{
						$filtro1 = 'CONTROLE';
					}else if($filtro == 'FORMA DE PAGAMENTO')
					{
						$filtro1 = 'FORMA_PGTO';
					}
							
			
			if($data == '')
			{
				if($filtro == '')
				{
				

					$query = "
					SELECT contas_receber . * , clientes.NOME_RAZAO
					FROM contas_receber
					LEFT JOIN clientes ON contas_receber.CLIENTES_ID = clientes.ID
					WHERE contas_receber.CLIENTES_ID = clientes.ID
					AND contas_receber.STATUS_2 LIKE '%$status%' 
					AND clientes.NOME_RAZAO LIKE '%$cliente%' 
					AND contas_receber.EMPRESA_ID=$empresa
					ORDER by contas_receber.DATA_VENCIMENTO ASC";
					
					
				}else 
				{
										
					$query = "SELECT contas_receber . * , clientes.NOME_RAZAO
					FROM contas_receber
					LEFT JOIN clientes ON contas_receber.CLIENTES_ID = clientes.ID
					WHERE contas_receber.CLIENTES_ID = clientes.ID
					AND contas_receber.STATUS_2 LIKE '%$status%'
					AND clientes.NOME_RAZAO LIKE '%$cliente%'
					AND contas_receber.".$filtro1." LIKE '%$vlr_filtro%'
					AND contas_receber.EMPRESA_ID=$empresa
					ORDER BY contas_receber.DATA_VENCIMENTO ASC";
				}
				
			}else
			{
				
								
				
				
				if($filtro == '')
				{
					
					
					$query = "SELECT contas_receber . * , clientes.NOME_RAZAO
					FROM contas_receber
					LEFT JOIN clientes ON contas_receber.CLIENTES_ID = clientes.ID
					WHERE contas_receber.CLIENTES_ID = clientes.ID
					AND contas_receber.STATUS_2 LIKE '%$status%'
					AND clientes.NOME_RAZAO LIKE '%$cliente%'
					AND contas_receber.".$data1." >= '$dataIn'
					AND contas_receber.".$data1." <= '$dataFn'
					AND contas_receber.EMPRESA_ID=$empresa
					ORDER BY contas_receber.DATA_VENCIMENTO ASC";	
					
				}else 
				{
					
					$query = "SELECT contas_receber . * , clientes.NOME_RAZAO
					FROM contas_receber
					LEFT JOIN clientes ON contas_receber.CLIENTES_ID = clientes.ID
					WHERE contas_receber.CLIENTES_ID = clientes.ID
					AND contas_receber.STATUS_2 LIKE '%$status%'
					AND clientes.NOME_RAZAO LIKE '%$cliente%'
					AND contas_receber.".$data1." >= '$dataIn'
					AND contas_receber.".$data1." <= '$dataFn'
					AND contas_receber.".$filtro1."  LIKE '%$vlr_filtro%'
					AND contas_receber.EMPRESA_ID=$empresa
					ORDER BY contas_receber.DATA_VENCIMENTO ASC";	
				}
			}
			
	
			
			

		$dadosCliente = mysql_query($query, $system) or die(mysql_error());

		$totalRows_dadosCliente = mysql_num_rows($dadosCliente);
		
		
		function formata($numero)
{
	if(strpos($numero,'.')!='')
	{
		   $var=explode('.',$numero);
		   if(strlen($var[0])==4)
		   {
		     $parte1=substr($var[0],0,1);
		     $parte2=substr($var[0],1,3);
		     if(strlen($var[1])<2)
		     {
		     	$formatado=$parte1.'.'.$parte2.','.$var[1].'0';
		     }else
		     {
		     	$formatado=$parte1.'.'.$parte2.','.$var[1];
		     }
		   }
		   elseif(strlen($var[0])==5)
		   {
		     $parte1=substr($var[0],0,2);
		     $parte2=substr($var[0],2,3);
		     if(strlen($var[1])<2)
		     {
		     	$formatado=$parte1.'.'.$parte2.','.$var[1].'0';
		     }
		     else
		     {
		     	$formatado=$parte1.'.'.$parte2.','.$var[1];
		     }
		   }
		   elseif(strlen($var[0])==6)
		   {
		     $parte1=substr($var[0],0,3);
		     $parte2=substr($var[0],3,3);
		     if(strlen($var[1])<2)
		     {
		     	$formatado=$parte1.'.'.$parte2.','.$var[1].'0';
		     }
		     else
		     {
		     	$formatado=$parte1.'.'.$parte2.','.$var[1];
		     }
		   }
		   elseif(strlen($var[0])==7)
		   {
		     $parte1=substr($var[0],0,1);
		     $parte2=substr($var[0],1,3);
		     $parte3=substr($var[0],4,3);
		     if(strlen($var[1])<2)
		     {
		     	$formatado=$parte1.'.'.$parte2.'.'.$parte3.','.$var[1].'0';
		     }
		     else
		     {
		     $formatado=$parte1.'.'.$parte2.'.'.$parte3.','.$var[1];
		     }
		   }
		   elseif(strlen($var[0])==8)
		   {
		     $parte1=substr($var[0],0,2);
		     $parte2=substr($var[0],2,3);
		     $parte3=substr($var[0],5,3);
		     if(strlen($var[1])<2){
		     $formatado=$parte1.'.'.$parte2.'.'.$parte3.','.$var[1].'0';
		     }else{
		     $formatado=$parte1.'.'.$parte2.'.'.$parte3.','.$var[1];
		     }
		   }
		   elseif(strlen($var[0])==9)
		   {
		     $parte1=substr($var[0],0,3);
		     $parte2=substr($var[0],3,3);
		     $parte3=substr($var[0],6,3);
		     if(strlen($var[1])<2)
		     {
		     	$formatado=$parte1.'.'.$parte2.'.'.$parte3.','.$var[1].'0';
		     }
		     else
		     {
		     	$formatado=$parte1.'.'.$parte2.'.'.$parte3.','.$var[1];
		     }
		   }
		   elseif(strlen($var[0])==10)
		   {
		     $parte1=substr($var[0],0,1);
		     $parte2=substr($var[0],1,3);
		     $parte3=substr($var[0],4,3);
		     $parte4=substr($var[0],7,3);
		     if(strlen($var[1])<2)
		     {
		     	$formatado=$parte1.'.'.$parte2.'.'.$parte3.'.'.$parte4.','.$var[1].'0';
		     }
		     else
		     {
		     	$formatado=$parte1.'.'.$parte2.'.'.$parte3.'.'.$parte4.','.$var[1];
		     }
		   }
		   else
		   {
		     if(strlen($var[1])<2)
		     {
		    	 $formatado=$var[0].','.$var[1].'0';
		     }
		     else
		     {
		    	 $formatado=$var[0].','.$var[1];
		     }
		   }
	  }
	  else
	  {
	     $var=$numero;
	   if(strlen($var)==4)
	   {
	     $parte1=substr($var,0,1);
	     $parte2=substr($var,1,3);
	     $formatado=$parte1.'.'.$parte2.'.'.'00';
	   }
	   elseif(strlen($var)==5)
	   {
	     $parte1=substr($var,0,2);
	     $parte2=substr($var,2,3);
	     $formatado=$parte1.'.'.$parte2.'.'.'00';
	   }
	   elseif(strlen($var)==6)
	   {
	     $parte1=substr($var,0,3);
	     $parte2=substr($var,3,3);
	     $formatado=$parte1.'.'.$parte2.'.'.'00';
	   }
	   elseif(strlen($var)==7)
	   {
	     $parte1=substr($var,0,1);
	     $parte2=substr($var,1,3);
	     $parte3=substr($var,4,3);
	     $formatado=$parte1.'.'.$parte2.'.'.$parte3.'.'.'00';
	   }
	   elseif(strlen($var)==8)
	   {
	     $parte1=substr($var,0,2);
	     $parte2=substr($var,2,3);
	     $parte3=substr($var,5,3);
	     $formatado=$parte1.'.'.$parte2.'.'.$parte3.'.'.'00';
	   }
	   elseif(strlen($var)==9)
	   {
	     $parte1=substr($var,0,3);
	     $parte2=substr($var,3,3);
	     $parte3=substr($var,6,3);
	     $formatado=$parte1.'.'.$parte2.'.'.$parte3.'.'.'00';
	   }
	   elseif(strlen($var)==10)
	   {
	     $parte1=substr($var,0,1);
	     $parte2=substr($var,1,3);
	     $parte3=substr($var,4,3);
	     $parte4=substr($var,7,3);
	     $formatado=$parte1.'.'.$parte2.'.'.$parte3.'.'.$parte4.','.'00';
	   }
	   else
	   {
	     $formatado=$var.'.'.'00';
	   }
	}
	  return $formatado;
}
		
		


?>

<page  backtop="0mm" backbottom="0mm" backleft="0mm" backrigth="5mm" orientation="L">
<style type="text/css">
<!--



.titulo td {
	
	font-weight: bold;
	font-size: 10px;
}
.conteudo td
{
	font-size: 9px;
}
.filtros td
{
	font-size: 9px;
	border-bottom: 1px #ccc;
}
-->
</style>

<table>
	<tr>
		<td style="text-align: center;" width="1100"><h2>Filtro Personalizado Financeiro</h2></td>
	</tr>
</table>

<br>
<table width="1000" border="0" cellpadding="0" cellspacing="2">
  <tr class="titulo">
    <td bgcolor="#DADADA" width="450">CLIENTE</td>
    <td bgcolor="#DADADA" >COD.</td>
    <td bgcolor="#DADADA" >N. DOC</td>
    <td bgcolor="#DADADA" >N. NUMERO</td>
    <td bgcolor="#DADADA" >EMISSAO</td>
    <td bgcolor="#DADADA" >VALOR</td>
    <td bgcolor="#DADADA" >VENCIMENTO</td>
    <td bgcolor="#DADADA" >STATUS</td>
    <td bgcolor="#DADADA" >TIPO</td>
    <td bgcolor="#DADADA" >VALOR PAGO</td>
    <td bgcolor="#DADADA" >DATA PGTO.</td>
    <td bgcolor="#DADADA" >FORMA DE PGTO.</td>
  </tr>
  
  <?php $i = 0; $qtd_Dinheiro = 0;?>
  <?php while($row_dadosCliente = mysql_fetch_assoc($dadosCliente)){ 

  	if($i % 2)
  	{
  		$cor = "#C0D9D9";
  	}else
  	{
  		$cor = "#fff";
  	}
  	
  	$valorRecebido;
  	$valorReceber;
  	
  	?>
  <tr class="conteudo" bgcolor="<?php echo $cor;?>" >
	<td><?php echo strtoupper($row_dadosCliente['NOME_RAZAO']);?></td>
    <td><?php echo $row_dadosCliente['ID'];?></td>
    <td><?php echo $row_dadosCliente['N_DOC'];?></td>
    <td><?php echo $row_dadosCliente['N_NUMERO'];?></td>
    <td><?php echo $row_dadosCliente['DATA_EMISSAO'];?></td>
    <td><?php echo str_replace(',', '.', $row_dadosCliente['VALOR_TITULO']);?></td>
    <td><?php echo $row_dadosCliente['DATA_VENCIMENTO'];?> </td>
    <td><?php echo $row_dadosCliente['STATUS_2'];?></td>
    <td><?php echo $row_dadosCliente['TIPO'];?></td>
    <td><?php echo str_replace(',', '.', $row_dadosCliente['VALOR_PAGAMENTO']);?></td>
    <td><?php echo $row_dadosCliente['DATA_PAGAMENTO'];?></td>
    <td><?php echo $row_dadosCliente['FORMA_PGTO'];?></td>
  </tr>
  
  <?php $i ++;?>
  <?php 
	$valorRecebido = str_replace(',', '.', $valorRecebido) + str_replace(',', '.', $row_dadosCliente['VALOR_PAGAMENTO']);
	$valorReceber = str_replace(',', '.', $valorReceber) + str_replace(',', '.', $row_dadosCliente['VALOR_TITULO']);
	
	
	if($row_dadosCliente['FORMA_PGTO'] == 'DINHEIRO')
	{
		$qtd_Dinheiro = $qtd_Dinheiro+1;
		$vr_Dinheiro = str_replace(',', '.',$vr_Dinheiro) + str_replace(',', '.',$row_dadosCliente['VALOR_PAGAMENTO']);
	}
	
 	if($row_dadosCliente['FORMA_PGTO'] == 'CHEQUE')
	{
		$qtd_Cheque = $qtd_Cheque+1;
		$vr_Cheque = str_replace(',', '.',$vr_Cheque) + str_replace(',', '.',$row_dadosCliente['VALOR_PAGAMENTO']);
	}
  	if($row_dadosCliente['FORMA_PGTO'] == 'DEPOSITO')
	{
		$qtd_Deposito = $qtd_Deposito+1;
		$vr_Deposito = str_replace(',', '.',$vr_Deposito) + str_replace(',', '.',$row_dadosCliente['VALOR_PAGAMENTO']);
	}
 	 if($row_dadosCliente['FORMA_PGTO'] == 'CARTAO')
	{
		$qtd_Cartao = $qtd_Cartao+1;
		$vr_Cartao = str_replace(',', '.',$vr_Cartao) + str_replace(',', '.',$row_dadosCliente['VALOR_PAGAMENTO']);
	}
   if($row_dadosCliente['FORMA_PGTO'] == 'BANCO')
	{
		$qtd_Banco = $qtd_Banco+1;
		$vr_Banco = str_replace(',', '.',$vr_Banco) + str_replace(',', '.',$row_dadosCliente['VALOR_PAGAMENTO']);
	}
  }?>
  
  
 <tr class="conteudo" >
	<td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td bgcolor="#C0D9D9"><?php echo formata($valorReceber);?></td>
    <td></td>
    <td></td>
    <td></td>
    <td bgcolor="#C0D9D9"><?php echo formata($valorRecebido);?></td>
    <td></td>
    <td></td>
  </tr>
  
  
</table>


<?php if($status == 'FECHADO'){?>
<br />
<br />
<table>
	<tr>
		<td><h3>Resumo Geral de Boletos Fechados</h3></td>
	</tr>
</table>
<table>
		
	<tr class="titulo">
		<td width="150" bgcolor="#DADADA">TIPO</td>
		<td width="30" bgcolor="#DADADA">QTD</td>
		<td bgcolor="#DADADA">VALOR TOTAL</td>
	</tr>
	<tr class="conteudo">
		<td>DINHEIRO</td>
		<td><?php echo $qtd_Dinheiro;?></td>
		<td><?php if(formata($vr_Dinheiro) != ',00') echo formata($vr_Dinheiro);?></td>
	</tr>
	<tr class="conteudo" bgcolor="#C0D9D9">
		<td>BANCO</td>
		<td><?php echo $qtd_Banco;?></td>
		<td><?php if(formata($vr_Banco) != ',00') echo formata($vr_Banco);?></td>
	</tr>	
	<tr class="conteudo" >
		<td>DEPOSITO</td>
		<td><?php echo $qtd_Deposito;?></td>
		<td><?php if(formata($vr_Deposito) != ',00') echo formata($vr_Deposito);?></td>
	</tr>
	<tr class="conteudo" bgcolor="#C0D9D9">
		<td>CHEQUE</td>
		<td><?php echo $qtd_Cheque;?></td>
		<td><?php if(formata($vr_Cheque) != ',00') echo formata($vr_Cheque);?></td>
	</tr>
	<tr class="conteudo" >
		<td>CARTAO</td>
		<td><?php echo $qtd_Cartao;?></td>
		<td><?php if(formata($vr_Cartao) != ',00') echo formata($vr_Cartao);?></td>
	</tr>
	<tr class="titulo" bgcolor="#DADADA" >
		<td>TOTAL GERAL</td>
		<td><?php echo $qtd_Dinheiro+$qtd_Banco+$qtd_Deposito+$qtd_Cheque+$qtd_Cartao;?></td>
		<td><?php if(formata($valorRecebido) != ',00') echo formata($valorRecebido);?></td>
	</tr>
	
</table>


<?php }?>

</page>








