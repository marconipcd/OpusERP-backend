<?php
header("Content-Type: text/html; charset=UTF-8",true);
?>
<?php 

		
	//IMPORTA ARQUIVO DE CONFIGURACAO DE CONEXAO
	require_once '../Conexao.php';
	 
	//FUNCAO UTF8 PARA RECONHECER
	function utf8($string)
	{
		$uf8 = utf8_encode($string);
		return $uft8;
	}

	//DEFINE DATA ATUAL
	$dia = date('d');
	$mes = date('m');
        $ano = date('Y');
	
	//SWITCH PARA TRANSFORMAR DATA NUMERAL PARA NOME
	switch ($mes)
	{
	   case 1: $mes = "JANEIRO"; break;
	   case 2: $mes = "FEVEREIRO"; break;
	   case 3: $mes = "MAR�O"; break;
	   case 4: $mes = "ABRIL"; break;
	   case 5: $mes = "MAIO"; break;
	   case 6: $mes = "JUNHO"; break;
	   case 7: $mes = "JULHO"; break;
	   case 8: $mes = "AGOSTO"; break;
	   case 9: $mes = "SETEMBRO"; break;
	   case 10: $mes = "OUTUBRO"; break;
	   case 11: $mes = "NOVEMBRO"; break;
	   case 12: $mes = "DEZEMBRO"; break;
	}

	//CASO SEJA PASSADO ALGUM PARAMETRO ID
	if (isset($_GET['id']))
	{
  
	   //DEFINE COD DO ACESSO DO CLIENTE
	   $cod_acesso = $_GET['id'];
  
	   //SELECIONA ACESSO DO CLIENTE
	   $queryAcessoCliente = "SELECT acesso_cliente.*,clientes.*,
			planos_acesso.ID as ID_PLANO_ACESSO,planos_acesso.NOME as NOME_PLANO_ACESSO,
			material_acesso.ID as ID_MATERIAL,material_acesso.NOME as NOME_MATERIAL , 
			contratos_acesso.ID as ID_CONTRATO, contratos_acesso.NOME as NOME_CONTRATO,contratos_acesso.VIGENCIA,contratos_acesso.REGIME,contratos_acesso.CLAUSULAS,
			enderecos_principais.* 
			FROM acesso_cliente
			LEFT JOIN clientes ON clientes.ID = acesso_cliente.CLIENTES_ID
			LEFT JOIN planos_acesso ON planos_acesso.ID = acesso_cliente.PLANOS_ACESSO_ID
			LEFT JOIN material_acesso ON material_acesso.ID = acesso_cliente.MATERIAL_ACESSO_ID
			LEFT JOIN contratos_acesso ON contratos_acesso.ID = acesso_cliente.CONTRATOS_ACESSO_ID
			LEFT JOIN enderecos_principais ON enderecos_principais.CLIENTES_ID = clientes.ID
			WHERE acesso_cliente.ID = $cod_acesso";
	   $resultAcessoCliente = $conn->query($queryAcessoCliente);
  
	   //RESULTADO DA BUSCA DO ACESSO CLIENTE
	   $rowAcessoCliente = $resultAcessoCliente->fetch_assoc();
  
 	   //INFORMACOES DO CONTRATO
	   $tituloTermoAdesao = 'TERMO DE ADESÃO DO CONTRATO DE ACESSO A INTERNET À CONTRATO';
	   $textoVigencia = utf8_encode('VIGÊNCIA:');
	   
?>


<style type="text/Css">
<!--

p
{
	padding:0; 
	margin:0;
	font-size: 9px;
}
.endeLoja {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
}
.titulo1 {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	text-align: center;
}
.especificacoes
{
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	text-align: center;
	
}
.tiposContrato
{
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	/**text-align: center;**/
}
.clausulas
{
	font-family: Arial, Helvetica, sans-serif;
	font-size: 9px;
}
.tbnContrato {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 14px;
	font-weight: bold;
}

.tbTexto1 {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
}
.dados {
	font-family: Arial, Helvetica, sans-serif;
}
.dados {
	font-family: Arial, Helvetica, sans-serif;
}
.dados {
	font-size: 12px;
}
.clausulas {
	font-family: Arial, Helvetica, sans-serif;
}
.clausulas {
	font-size: 10px;
}
.data {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
}
#voltar {	background-color: #FFF;
	background-image: url(../imagens/2leftarrow.png);
	height: 24px;
	width: 51px;
	border-top-style: none;
	border-right-style: none;
	border-bottom-style: none;
	border-left-style: none;
	font-size: 10px;
	color: #FFF;
}
.clausulas .clausulas {
	text-align: justify;
}

-->
</style>

<page  backtop="0mm" backbottom="0mm" backleft="0mm" backrigth="5mm">
    <table border="1" width="700" border="0" align="center" cellpadding="2" cellspacing="0">

   <!--LOGOMARCA-->
   <tr>
   		<td align="center"><img src="imagens/logocontrato.jpg" width="255" height="57" /></td>
   </tr>
   
   <!--ENDERECO LOJA-->
   <tr>
    <td align="center" class="endeLoja">
    CNPJ: 07.578.965/0001-05  -  RUA CORONEL ADJAR MACIEL, 35  CENTRO   BELO JARDIM-PE  CEP: 55150-040<br />
    COMERCIAL: (81) 3726-3125  -  SUPORTE: (81) 3726-3125 - suporte@digitalonline.com.br - www.digitalonline.com.br</td>
   </tr>
  
     <tr><td align="left">&nbsp;</td></tr>
   
   <!--TITULO TERMO-->
   <tr>
    <td class="titulo1">
    	<?php echo utf8_encode($tituloTermoAdesao);?> <strong><?php echo $rowAcessoCliente['NOME_CONTRATO'];?></strong>
    	<br><br>
    </td>
   </tr>
 	 
	 <!--NUMERO CONTRATO, VEGENCIA-->
 	 <tr>
        <td bgcolor="#CCCCCC">
        
        <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
	       <tr>
		      <td width="250">N�. CONTRATO: <strong><?php echo  $rowAcessoCliente['CLIENTES_ID'];?></strong></td>
		      <td width="250">Belo Jardim, <?php echo $dia;?> de <?php echo $mes;?> de <?php echo $ano;?></td>
		      <td><?php echo $textoVigencia;?> <strong><?php echo $rowAcessoCliente['VIGENCIA'];?></strong></td>  
	       </tr>
   		</table>
   		
        </td>
 	 </tr>
  
	<!--ESPECIFICA��ES CONTRATANTE, CONTRATADO-->
  <tr>
    <td align="center" class="especificacoes">
    <br>
    Pelo presente instrumento, na qualidade de <strong>CONTRATANTE</strong>, abaixo qualificado, declaramos para os devidos fins de direito que <br/>
    temos pleno conhecimento e concordamos com todos os termos e <?php echo utf8_encode('condi��es');?> do CONTRATO DE ACESSO A INTERNET da<br>
    Digitalonline aderindo neste ato ao contrato ao qual este Termo aditivo se refere.
    <br>
    <span class="data">
     
    </span>
    </td>
  </tr>
  
	<!--  DADOS DO CONTRATANTE-->
	<tr>
		<td>
		
		<table class='tiposContrato' width="730" border="0.5" cellpadding="0" cellspacing="0">
			<tr>
				<td width="500"><strong>1. DADOS DO CONTRATANTE:</strong></td>
				<td><br></td>
			</tr>
			<tr>
				<td>NOME/<?php echo utf8_encode('RAZ�O');?> SOCIAL: <strong><?php echo $rowAcessoCliente['NOME_RAZAO'];?></strong></td>
				<td width="240">CNPJ/CPF: <?php echo $rowAcessoCliente['DOC_CPF_CNPJ'];?></td>
			</tr>
			<tr>
				<td colspan="2"><?php echo $rowAcessoCliente['ENDERECO'];?> - <?php echo $rowAcessoCliente['NUMERO'];?> - <?php echo $rowAcessoCliente['BAIRRO'];?> - BELO JARDIM-PE - <?php echo $rowAcessoCliente['CEP'];?></td>
				
			</tr>
			<tr>
				<td colspan="2">
					<table cellpadding="0" cellspacing="0">
						<tr>
							<td width="400"><?php echo $rowAcessoCliente['REFERENCIA'];?></td>
							<td style='border-left: 1px solid #000;'>TELFONES: <?php echo $rowAcessoCliente['TELEFONE1'];?> <?php echo $rowAcessoCliente['TELEFONE2'];?> <?php echo $rowAcessoCliente['CELULAR1'];?> <?php echo $rowAcessoCliente['CELULAR2'];?></td>
						</tr>
					</table>
					
				</td>
				
			</tr>
			<tr>
				<td colspan="2"><br></td>
			</tr>
			<tr>
				<td colspan="2"><strong><?php echo utf8_encode('2. INFORMAÇÕES DO SERVI�O CONTRATADO:');?></strong></td>
			</tr>
			<tr>
				<td colspan="2">
					<table cellpadding="0" cellspacing="0">
						<tr>
							<td width="200" >REGIME:  <?php echo $rowAcessoCliente['REGIME'];?></td>
							<td width="200" style='border-left: 1px solid #000;'>PLANO:  <?php echo $rowAcessoCliente['NOME_PLANO_ACESSO'];?></td>
							<td width="150"  style='border-left: 1px solid #000;'>IP:  <?php echo $rowAcessoCliente['ENDERECO_IP'];?></td>
							<td style='border-left: 1px solid #000;'><?php echo utf8_encode('USUÁRIO');?>:  <?php echo $rowAcessoCliente['LOGIN'];?></td>
						</tr>
					</table>
					
				</td>
				
			</tr>
		</table>
				
		</td>
	</tr>
 	
 	<!--TIPOS DO CONTRATO--> 
  	<tr class='tiposContrato'>
  		<td>
  		<br>
  		<strong>3. TIPOS DE CONTRATO</strong><br>
		3.1 - Contrato LIVRE � Trata-se de um acordo entre as partes informadas  neste contrato, em que o CONTRATANTE podera optar<br>
		pelo cancelamento do <?php echo utf8_encode('serviço');?> acordado em qualquer tempo sem <?php echo utf8_encode('cobrança');?> de <?php echo utf8_encode('�nus');?> para qualquer das partes.<br>
		3.2 - Contrato FIDELIDADE 12 MESES - Trata-se de um acordo entre as partes informadas  neste contrato, em que<br>
		o CONTRATANTE <?php echo utf8_encode('estará');?> isento da Taxa de <?php echo utf8_encode('Adesão');?> praticada pela CONTRATADA, onde em troca, a CONTRATANTE permanecera<br>
		com o <?php echo utf8_encode('servç�o');?> ativo por no m�nimo 12 meses, segundo <?php echo utf8_encode('Cláusula de Fideliza��o.');?>
  		<br><br>
  		
  		</td>  	
  	</tr>
  
	<!--ASSINATURAS-->
	<tr>
		<td>
			<table border="0.5" cellpadding="0" cellspacing="0">
				<tr>
					<td width="745" colspan="2">
						<strong>4. ASSINATURAS</strong> <br>
						LI E ESTOU DE ACORDO COM AS <?php echo utf8_encode('CONDIÇÕES');?> GERAIS DESTE CONTRATO EM FRENTE E VERSO E CONFIRMO A <br>
 						<?php echo utf8_encode('ADES�O');?> AO CONTRATO DENOMINADO <strong>"<?php echo $rowAcessoCliente['NOME_CONTRATO'];?>".</strong>
						
					</td>
				</tr>
				<tr>
					<td>
					PELA CONTRATADA:<br>
					<img src="imagens/clip_image002_0000.jpg"><br>
					REPRESENTANTE LEGAL<br>
					Nome: ADEMIR DE SOUZA PINTO FILHO
					
					
					</td>
					<td>
					
					CONTRATANTE:<br><br>

					__________________________________________<br>
					REPRESENTANTE LEGAL<br> 
					Nome: <?php echo $rowAcessoCliente['NOME_RAZAO'];?><br><br>

					__________________________________________<br>
					AVALISTA/TESTEMUNHA<br>
					
					
					</td>
				</tr>
			</table>
		</td>
	</tr>
	 
	<!--CLAUSULAS-->
	
	<tr>
		<td align='center'><strong><br>CONTRATO DE ACESSO A INTERNET VIA <?php echo utf8_encode('R�DIO');?>/CABO DIGITALONLINE  - DAS <?php echo utf8_encode('DISPOSI��ES');?> GERAIS:</strong><br><br></td>
	
	</tr>
	<tr>
		<td >
		<p>
		1. O presente termo de <?php echo utf8_encode('adesão');?>, sem qualquer <?php echo utf8_encode('excessão');?>, as <?php echo utf8_encode('condições');?> fixadas noutro anterior, por ventura existentes, desde que vinculado quanto a <?php echo utf8_encode('vig�ncia');?> que interrompe-se e passa a corresponder a que ora<br>
		informada, reiniciando-se a contagem. 
		<br><br>
		Contrato de <?php echo utf8_encode('prestação');?> de <?php echo utf8_encode('serviços');?>, que entre si celebram, de um lado, DIGITAL, empresa estabelecida � Rua Coronel Adjar Maciel, n� 35 bairro Centro em Belo Jardim-PE, inscrita no CNPJ sob o n.� 07.578.965/0001-05,<br>
		que <?php echo utf8_encode('responder�');?> por todos os efeitos deste contrato, doravante denominada CONTRATADA e a pessoa <?php echo utf8_encode('f�sica');?>/<?php echo utf8_encode('jur�dica');?> identificada no termo de <?php echo utf8_encode('Ades�o');?> anexo, doravante denominado<br>
		<strong>CONTRATANTE</strong>, que se <?php echo utf8_encode('regerá');?> pelas <?php echo utf8_encode('cláusulas');?> e <?php echo utf8_encode('condições');?> seguintes:
		</p>
		<br><br>
		</td>
	
	</tr> 
	 
<!--	 DISPOSI��ES GERAIS-->
	 
    </table>
    
    	    
    <span class='clausulas'>
     <?php echo str_replace('TEXTFORMAT', 'P', $rowAcessoCliente['CLAUSULAS']);?>
     </span>
     <br><br>
     <span align='right'>
	     <p>
	     ----------------------------------------------------------------------------------------------<br>
		Assinatura do Contratante e/ou Representante Legal
		</p>
     </span>
</page>

<?php } ?>