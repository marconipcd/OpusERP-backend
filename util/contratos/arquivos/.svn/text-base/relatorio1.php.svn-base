<?php


require_once '../../Connections/system.php';


mysql_select_db($database_system, $system);

	if(isset($_GET['de']))
	{
		if(isset($_GET['ate']))
		{
			
			//Variaveis
			$de = $_GET['de'];
			$ate = $_GET['ate'];
			
			$diaI = substr($de, 0, 2);
			$mesI = substr($de, 3, 2);
			$anoI = substr($de, 6, 4);
			
			$dataI = $anoI.'-'.$mesI.'-'.$diaI;
			
			$diaF = substr($ate, 0, 2);
			$mesF = substr($ate, 3, 2);
			$anoF = substr($ate, 6, 4);
			
			$dataF = $anoF.'-'.$mesF.'-'.$diaF;
			
			$pacote_escolhido = $_GET['pacote'];
			$empresa = $_GET['empresa'];
			$vendedor = $_GET['vendedor'];;
			$cidade = $_GET['cidade'];
			$plano_escolhido = $_GET['plano'];
			$instalador = $_GET['instalador'];
			
			
			
			
			$query_dadosCliente = "SELECT c.*, c.cidade, a.* FROM clientes c INNER JOIN agenda a ON c.cod_cliente = a.id_cliente WHERE c.pacote_escolhido LIKE '%$pacote_escolhido%' AND c.empresa LIKE '%$empresa%' AND c.consultor_parceiro LIKE '%$vendedor%' AND c.cidade LIKE '%$cidade%' AND c.plano_escolhido LIKE '%$plano_escolhido%' AND a.tecnico LIKE '%$instalador%' AND a.data_conclusao >= '$dataI' AND a.data_conclusao <= '$dataF' ORDER by a.data_conclusao ASC ";			
		
			
//			SELECT p.codigoPessoa, r. * FROM pessoa p INNER JOIN radcheck r ON p.codigoPessoa = r.cliente WHERE p.antena LIKE '%DIGITALONLINE01%'
			
				
		
		}else 
		{
			
		}
	}else 
	{
		$pacote_escolhido = $_GET['pacote'];
		$empresa = $_GET['empresa'];
		$vendedor = $_GET['vendedor'];;
		$cidade = $_GET['cidade'];
		$plano_escolhido = $_GET['plano'];
		
		
		$query_dadosCliente = "SELECT c.*, c.cidade, a.* FROM clientes c INNER JOIN agenda a ON c.cod_cliente = a.id_cliente WHERE c.pacote_escolhido LIKE '%$pacote_escolhido%' AND c.empresa LIKE '%$empresa%' AND c.consultor_parceiro LIKE '%$vendedor%' AND c.cidade LIKE '%$cidade%' AND c.plano_escolhido LIKE '%$plano_escolhido%' AND a.tecnico LIKE '%$instalador%' ORDER by a.data_conclusao ASC";
	}

//Aplicar Descri��o do Filtro


//Consulta
$dadosCliente = mysql_query($query_dadosCliente, $system) or die(mysql_error());

$totalRows_dadosCliente = mysql_num_rows($dadosCliente);


?>

<page  backtop="0mm" backbottom="0mm" backleft="0mm" backrigth="5mm">
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
		<td style="text-align: center;" width="720"><h2>Relat�rio de Assinantes</h2></td>
	</tr>
</table>
<table>
	<tr class="filtros">
		<td><span style="font-weight: bold;">Filtros:</span><br>
			<?php 
				
				echo 'Pacote:';
				if($_GET['pacote'] != ''){echo $_GET['pacote'].'<br>';}else{echo 'TODOS'.'<br>';}
				
				echo 'Empresa:';
				if($_GET['empresa'] != ''){echo $_GET['empresa'].'<br>';}else{echo 'TODAS'.'<br>';}
				
				echo 'Vendedor:';
				if($_GET['vendedor'] != ''){echo $_GET['vendedor'].'<br>';}else{echo 'TODOS'.'<br>';}
				
				echo 'Cidade:';
				if($_GET['cidade'] != ''){echo $_GET['cidade'].'<br>';}else{echo 'TODAS'.'<br>';}
				
				echo 'Plano:';
				if($_GET['plano'] != ''){echo $_GET['plano'].'<br>';}else{echo 'TODOS'.'<br>';}
				
				echo 'Instalador:';
				if($_GET['instalador'] != ''){echo $_GET['instalador'].'<br>';}else{echo 'TODOS'.'<br>';}
				
				
				
				

		
			?>	
			
			
			
		</td>
		<td valign="top" style="border-left: 1px #ccc; padding-left: 8px; margin-left: 8px;">
		<?php 
				if(isset($_GET['de']))
				{
					if(isset($_GET['ate']))
					{
						?>
						<span style="font-weight: bold;">Per�odo:</span><br>	
						<?php
						echo 'De:'.$_GET['de'].'<br>';
						echo 'At�:'.$_GET['ate'].'<br>';
						
					}
				}
			?>	
		</td>
	</tr>
	
</table>
<br>
<table width="1000" border="0" cellpadding="0" cellspacing="2">
  <tr class="titulo">
    <td bgcolor="#DADADA">DATA INST.</td>
    <td bgcolor="#DADADA">INSTALADOR</td>
    <td bgcolor="#DADADA" width="190">ASSINANTE</td>
    <td bgcolor="#DADADA">CIDADE</td>
    <td bgcolor="#DADADA" width="50">PACOTE</td>
    <td bgcolor="#DADADA" width="80">PAGAMENTO</td>
    <td bgcolor="#DADADA">ORIGEM</td>
    <td bgcolor="#DADADA">VENDEDOR</td>
    <td bgcolor="#DADADA">EMPRESA</td>
  </tr>
  
  <?php $i = 0; ?>
  <?php while($row_dadosCliente = mysql_fetch_assoc($dadosCliente)){ 

  	if($i % 2)
  	{
  		$cor = "#C0D9D9";
  	}else
  	{
  		$cor = "#fff";
  	}
  	
  	?>
  <tr class="conteudo" bgcolor="<?php echo $cor;?>" >
	<td><?php echo $row_dadosCliente['data_conclusao'];?></td>
    <td><?php echo mb_strtoupper($row_dadosCliente['tecnico'], 'UTF-8'); ?></td>
    <td><?php echo mb_strtoupper($row_dadosCliente['nome_razao'], 'UTF-8');?></td>
    <td><?php echo mb_strtoupper($row_dadosCliente['cidade'], 'UTF-8');?></td>
    <td><?php echo mb_strtoupper($row_dadosCliente['pacote_escolhido'], 'UTF-8');?></td>
    <td><?php echo mb_strtoupper($row_dadosCliente['plano_escolhido'], 'UTF-8');?></td>
    <td><?php echo mb_strtoupper($row_dadosCliente['origem'], 'UTF-8');?></td>
    <td><?php echo mb_strtoupper($row_dadosCliente['consultor_parceiro'], 'UTF-8');?></td>
    <td><?php echo mb_strtoupper($row_dadosCliente['empresa'], 'UTF-8');?></td>
  </tr>
  <?php $i ++;?>
  <?php }?>
  
</table>

<span style="font-size: 9px;">
	
  Total de <strong><?php echo $totalRows_dadosCliente;?></strong> Registros Encontrados.
</span>
</page>










