<?php
header('Content-Type: text/html; charset=ISO-8859-1');
?>
<?php

require_once('../Connections/system.php');
//require_once("dompdf/dompdf_config.inc.php");

	
	if (!function_exists('GetSQLValueString')) 
	{
		function GetSQLValueString($theValue, $theType, $theDefinedValue = '', $theNotDefinedValue = '') 
		{
		    return $theValue;
		}
	}

	

	

function monta_linha_digitavel($linha) {
    // Posi��o 	Conte�do
    // 1 a 3    N�mero do banco
    // 4        C�digo da Moeda - 9 para Real
    // 5        Digito verificador do C�digo de Barras
    // 6 a 19   Valor (12 inteiros e 2 decimais)
    // 20 a 44  Campo Livre definido por cada banco

    // 1. Campo - composto pelo c�digo do banco, c�digo da mo�da, as cinco primeiras posi��es
    // do campo livre e DV (modulo10) deste campo
    $p1 = substr($linha, 0, 4);
    $p2 = substr($linha, 19, 5);
    $p3 = modulo_10("$p1$p2");
    $p4 = "$p1$p2$p3";
    $p5 = substr($p4, 0, 5);
    $p6 = substr($p4, 5);
    $campo1 = "$p5.$p6";

    // 2. Campo - composto pelas posi�oes 6 a 15 do campo livre
    // e livre e DV (modulo10) deste campo
    $p1 = substr($linha, 24, 10);
    $p2 = modulo_10($p1);
    $p3 = "$p1$p2";
    $p4 = substr($p3, 0, 5);
    $p5 = substr($p3, 5);
    $campo2 = "$p4.$p5";

    // 3. Campo composto pelas posicoes 16 a 25 do campo livre
    // e livre e DV (modulo10) deste campo
    $p1 = substr($linha, 34, 10);
    $p2 = modulo_10($p1);
    $p3 = "$p1$p2";
    $p4 = substr($p3, 0, 5);
    $p5 = substr($p3, 5);
    $campo3 = "$p4.$p5";

    // 4. Campo - digito verificador do codigo de barras
    $campo4 = substr($linha, 4, 1);

    // 5. Campo composto pelo valor nominal pelo valor nominal do documento, sem
    // indicacao de zeros a esquerda e sem edicao (sem ponto e virgula). Quando se
    // tratar de valor zerado, a representacao deve ser 000 (tres zeros).
    $campo5 = substr($linha, 5, 14);

    return "$campo1 $campo2 $campo3 $campo4 $campo5"; 
}


	



	
	 
	
	
	
	
	
$content = "<style type='text/css'>
.table1 {
	width: 800px;
	border-bottom-width: 1px;
	border-bottom-style: dashed;
	border-bottom-color: #000;
}
.cabecalho {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 14px;
	font-weight: bold;
	text-align: right;
}
.linhas_formatacao {
	font-family: Courier;
	font-size: 8px;
	color: #000;
	border-bottom-width: 1px;
	border-bottom-style: solid;
	border-bottom-color: #000;
	text-align: left;
	vertical-align: bottom;
	border-left-color: #000;
	padding-left: 0px;
}
.formatacao_valores
{
	font-family: Courier;
	font-size: 8px;
	color: #666;
	padding: 0;
	margin: 0;
}
.titulo_linhas {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 8px;
	color: #000;
	text-align: center;
}
.div_vertical {
	background-image: url(imagens/dic_boleto.gif);
}
.bg_div {
	background-image: url(imagens/bg_div.gif);
	height: 4px;
}
.linhaDigitavel {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 14px;
	text-align: right;
	color: #000;
}
</style>
<page backtop='0mm' backbottom='0mm' backleft='10mm' backright='10mm' >";

		if (isset($_GET['ids_boletos'])) {
	 		
			$ids_get = explode(",", $_GET['ids_boletos']);
			
			
			$i = 0;
			foreach ($ids_get as $id)
			{
				$i++;
				$ids_boletos[$i] = $id;
			}
			
		}

	foreach ($ids_boletos as $id)
	{
		
		
	
	
	
	mysql_select_db($database_system, $system);
	$query_boletos = "SELECT contas_receber . * , clientes.NOME_RAZAO, enderecos_principais. *
				FROM contas_receber 
				LEFT JOIN clientes ON contas_receber.CLIENTES_ID = clientes.ID
				LEFT JOIN enderecos_principais ON  enderecos_principais.CLIENTES_ID = clientes.ID
				WHERE contas_receber.ID = '$id'"; 
	 
	$boletos = mysql_query($query_boletos, $system) or die(mysql_error());
	$row_boletos = mysql_fetch_assoc($boletos);
	$totalRows_boletos = mysql_num_rows($boletos);
	
	
	$nNumero = $row_boletos['N_NUMERO'];
	$nome = $row_boletos['NOME_RAZAO'];
	$empresaID = $_GET['EMPRESA_ID'];
	
	$query_configuracoesBoleto = "SELECT * FROM conta_bancaria WHERE EMPRESA_ID = '$empresaID'";
	$configuracoesBoleto = mysql_query($query_configuracoesBoleto, $system) or die(mysql_error());
	$row_configuracoesBoleto = mysql_fetch_assoc($configuracoesBoleto);
	$totalRows_configuracoesBoleto = mysql_num_rows($configuracoesBoleto);
	
	// DADOS DO BOLETO PARA O SEU CLIENTE
	$dias_de_prazo_para_pagamento = 5;
	$taxa_boleto = $row_configuracoesBoleto['TAXA_BOLETO'];

	$_ano = substr($row_boletos['DATA_VENCIMENTO'], 0, 4);
	$_mes = substr($row_boletos['DATA_VENCIMENTO'], 5, 2);
	$_dia = substr($row_boletos['DATA_VENCIMENTO'], 8, 2);

	$_dataVencimento = $_dia.'/'.$_mes.'/'.$_ano;
	
	$data_venc = $_dataVencimento;  // Prazo de X dias OU informe data: '13/04/2006';
	
	$valor_cobrado =  $row_boletos['VALOR_TITULO'];// Valor - REGRA: Sem pontos na milhar e tanto faz com '.' ou ',' ou com 1 ou 2 ou sem casa decimal
	$valor_cobrado = str_replace(',', '.',$valor_cobrado);
	$valor_boleto=number_format($valor_cobrado+$taxa_boleto, 2, ',', '');
	
	$dadosboleto['nosso_numero'] = $row_boletos['N_NUMERO'];
	$dadosboleto['numero_documento'] = $row_boletos['N_DOC'];	// Num do pedido ou do documento
	$dadosboleto['data_vencimento'] = $_dataVencimento; // Data de Vencimento do Boleto - REGRA: Formato DD/MM/AAAA
	$dadosboleto['data_documento'] = date('d/m/Y'); // Data de emiss�o do Boleto
	$dadosboleto['data_processamento'] = date('d/m/Y'); // Data de processamento do boleto (opcional)
	$dadosboleto['valor_boleto'] = $valor_boleto; 	// Valor do Boleto - REGRA: Com v�rgula e sempre com duas casas depois da virgula
	
	
	
	
	// DADOS DO SEU CLIENTE
	$dadosboleto['sacado'] = $row_boletos['NOME_RAZAO'];
	$dadosboleto['endereco1'] = $row_boletos['ENDERECO'].','.$row_boletos['NUMERO'].' - '.$row_boletos['BAIRRO'].' - '.$row_boletos['REFERENCIA'];
	$dadosboleto['endereco2'] = $row_boletos['CEP'].' - '.$row_boletos['CIDADE'];
	
	// INFORMACOES PARA O CLIENTE
	$dadosboleto['demonstrativo1'] = '';
	$dadosboleto['demonstrativo2'] = '';
	$dadosboleto['demonstrativo3'] = '';
	
	// INSTRU��ES PARA O CAIXA
	//$instrucoes = $row_configuracoesBoleto['instrucoes'];
	$dadosboleto['instrucoes1'] = nl2br($row_configuracoesBoleto['INSTRUCOES1']);
	$dadosboleto['instrucoes2'] = nl2br($row_configuracoesBoleto['INSTRUCOES2']);
	$dadosboleto['instrucoes3'] = nl2br($row_configuracoesBoleto['INSTRUCOES3']);
	
	$dadosboleto['instrucoes4'] = '';
	
	// DADOS OPCIONAIS DE ACORDO COM O BANCO OU CLIENTE
	$dadosboleto['quantidade'] = $row_boletos['CONTROLE'] .' de '.$totalRows_boletos;
	$dadosboleto['valor_unitario'] = '';
	$dadosboleto['aceite'] = 'N';		
	$dadosboleto['especie'] = 'R$';
	$dadosboleto['especie_doc'] = 'DM';



	// ---------------------- DADOS FIXOS DE CONFIGURA��O DO SEU BOLETO --------------- //
	
		$ag = $row_configuracoesBoleto['AGENCIA_BANCO'];
		$cc = $row_configuracoesBoleto['N_CONTA'];
		$cv = $row_configuracoesBoleto['CONVENIO'];
		$ct = $row_configuracoesBoleto['CONTRATO'];
		$cat = $row_configuracoesBoleto['CARTEIRA'];
		$vc = $row_configuracoesBoleto['VARIACAO_CARTEIRA'];
		$fc = '7';
		$fnn = '2';
		$ide = $row_configuracoesBoleto['IDENTIFICACAO'];
		$cnpj = $row_configuracoesBoleto['CPF_CNPJ'];
		$end = $row_configuracoesBoleto['ENDERECO'];
		$uf = $row_configuracoesBoleto['CIDADE_UF'];
		$cedente = $row_configuracoesBoleto['CEDENTE'];
	
	
	
	// DADOS DA SUA CONTA - BANCO DO BRASIL
	$dadosboleto['agencia'] = $ag; // Num da agencia, sem digito
	$dadosboleto['conta'] = $cc; 	// Num da conta, sem digito
	
	// DADOS PERSONALIZADOS - BANCO DO BRASIL
	$dadosboleto['convenio'] = $cv;  // Num do conv�nio - REGRA: 6 ou 7 ou 8 d�gitos
	$dadosboleto['contrato'] = $ct; // Num do seu contrato
	$dadosboleto['carteira'] = $cat;
	$dadosboleto['variacao_carteira'] = $vc;  // Varia��o da Carteira, com tra�o (opcional)
	
	// TIPO DO BOLETO
	$dadosboleto['formatacao_convenio'] = $fc; // REGRA: 8 p/ Conv�nio c/ 8 d�gitos, 7 p/ Conv�nio c/ 7 d�gitos, ou 6 se Conv�nio c/ 6 d�gitos
	$dadosboleto['formatacao_nosso_numero'] = $fnn; // REGRA: Usado apenas p/ Conv�nio c/ 6 d�gitos: informe 1 se for NossoN�mero de at� 5 d�gitos ou 2 para op��o de at� 17 d�gitos
	
	
	// SEUS DADOS
	$dadosboleto['identificacao'] = $ide;
	$dadosboleto['cpf_cnpj'] = $cnpj;
	$dadosboleto['endereco'] = $end;
	$dadosboleto['cidade_uf'] = $uf;
	$dadosboleto['cedente'] = $cedente;
	
	
	
	require_once ('include/funcoes_bb4.php');
	
	$codigobanco = "001";
$codigo_banco_com_dv = geraCodigoBanco($codigobanco);
$nummoeda = "9";
$fator_vencimento = fator_vencimento($dadosboleto["data_vencimento"]);

//valor tem 10 digitos, sem virgula6
$valor = formata_numero($dadosboleto["valor_boleto"],10,0,"valor");
//agencia � sempre 4 digitos
$agencia = formata_numero($dadosboleto["agencia"],4,0);
//conta � sempre 8 digitos
$conta = formata_numero($dadosboleto["conta"],8,0);
//carteira 18
$carteira = $dadosboleto["carteira"];
//agencia e conta
$agencia_codigo = $agencia."-". modulo_11($agencia) ." / ". $conta ."-". modulo_11($conta);
//Zeros: usado quando convenio de 7 digitos
$livre_zeros='000000';

// Carteira 18 com Conv�nio de 8 d�gitos
if ($dadosboleto["formatacao_convenio"] == "8") {
	$convenio = formata_numero($dadosboleto["convenio"],8,0,"convenio");
	// Nosso n�mero de at� 9 d�gitos
	$nossonumero = formata_numero($dadosboleto["nosso_numero"],9,0);
	$dv=modulo_11("$codigobanco$nummoeda$fator_vencimento$valor$livre_zeros$convenio$nossonumero$carteira");
	$linha="$codigobanco$nummoeda$dv$fator_vencimento$valor$livre_zeros$convenio$nossonumero$carteira";
	//montando o nosso numero que aparecer� no boleto
	$nossonumero = $convenio . $nossonumero ."-". modulo_11($convenio.$nossonumero);
}

// Carteira 18 com Conv�nio de 7 d�gitos
if ($dadosboleto["formatacao_convenio"] == "7") {
	$convenio = formata_numero($dadosboleto["convenio"],7,0,"convenio");
	// Nosso n�mero de at� 10 d�gitos
	$nossonumero = formata_numero($dadosboleto["nosso_numero"],10,0);
	$dv=modulo_11("$codigobanco$nummoeda$fator_vencimento$valor$livre_zeros$convenio$nossonumero$carteira");
	$linha="$codigobanco$nummoeda$dv$fator_vencimento$valor$livre_zeros$convenio$nossonumero$carteira";
    $nossonumero = $convenio.$nossonumero;
	//N�o existe DV na composi��o do nosso-n�mero para conv�nios de sete posi��es
}

// Carteira 18 com Conv�nio de 6 d�gitos
if ($dadosboleto["formatacao_convenio"] == "6") {
	$convenio = formata_numero($dadosboleto["convenio"],6,0,"convenio");
	
	if ($dadosboleto["formatacao_nosso_numero"] == "1") {
		
		// Nosso n�mero de at� 5 d�gitos
		$nossonumero = formata_numero($dadosboleto["nosso_numero"],5,0);
		$dv = modulo_11("$codigobanco$nummoeda$fator_vencimento$valor$convenio$nossonumero$agencia$conta$carteira");
		$linha = "$codigobanco$nummoeda$dv$fator_vencimento$valor$convenio$nossonumero$agencia$conta$carteira";
		//montando o nosso numero que aparecer� no boleto
		$nossonumero = $convenio . $nossonumero ."-". modulo_11($convenio.$nossonumero);
	}
	
	if ($dadosboleto["formatacao_nosso_numero"] == "2") {
		
		// Nosso n�mero de at� 17 d�gitos
		$nservico = "21";
		$nossonumero = formata_numero($dadosboleto["nosso_numero"],17,0);
		$dv = modulo_11("$codigobanco$nummoeda$fator_vencimento$valor$convenio$nossonumero$nservico");
		$linha = "$codigobanco$nummoeda$dv$fator_vencimento$valor$convenio$nossonumero$nservico";
	}
}
	
	$dadosboleto["linha_digitavel"] = monta_linha_digitavel($linha);
	$dadosboleto["codigo_barras"] = $linha;
//$dadosboleto["linha_digitavel"] = monta_linha_digitavel($linha);
$dadosboleto["agencia_codigo"] = $agencia_codigo;
$dadosboleto["nosso_numero"] = $nossonumero;
$dadosboleto["codigo_banco_com_dv"] = $codigo_banco_com_dv;
	
$content .= "

	<br/>
		<table border='0' cellpadding='0' cellspacing='0' class='table1'>
  <tr >
    <td width='155' style='border-bottom: 1px solid #000;' ><img src='imagens/bb_logo.png' width='155' height='25' /></td>
    <td width='45' style='border-bottom: 1px solid #000;' class='cabecalho' >|001-9|</td>
    <td width='140' style='border-bottom: 1px solid #000;' class='cabecalho' >Recibo Sacado</td>
    <td width='4' rowspan='7' class='div_vertical' style='border-bottom: 1px solid #000;' >&nbsp;</td>
    <td width='165' style='border-bottom: 1px solid #000;' ><img src='imagens/bb_logo.png'  width='155' height='25' /></td>
    <td width='44' style='border-bottom: 1px solid #000;' class='cabecalho' >|001-9|</td>
    <td width='140' style='border-bottom: 1px solid #000;' class='cabecalho' >Recibo de Entrega</td>
  </tr>
  <tr >
    <td colspan='3' style='border-bottom: 1px solid #000;' >
    
    <table width='100%' border='0' cellpadding='0' cellspacing='0' class='linhas_formatacao'>
      <tr>
        <td width='80'><span class='titulo_linhas'>Vencimento</span><br />
".$dadosboleto["data_vencimento"]."</td>
        <td width='2%'><img src='imagens/div_txt.gif' width='6' height='18' /></td>
        <td width='100'><span class='titulo_linhas'>Ag�ncia</span><br />
".$dadosboleto["agencia_codigo"]."</td>
        <td width='2%'><img src='imagens/div_txt.gif'  width='6' height='18' /></td>
        <td width='60'><span class='titulo_linhas'>Moeda</span><br />
R$</td>
        <td width='2%'><img src='imagens/div_txt.gif'  width='6' height='18' /></td>
        <td width='20%'><span class='titulo_linhas'>Quantidade</span><br />
         ".$dadosboleto['quantidade']."</td>
        </tr>
    </table>
    
    </td>
    <td colspan='3' style='border-bottom: 1px solid #000;' >
    
    <table width='100%' border='0' cellpadding='0' cellspacing='0' class='linhas_formatacao'>
      <tr>
        <td width='80'><span class='titulo_linhas'>Vencimento</span><br />
        ".$dadosboleto["data_vencimento"]."</td>
        <td width='2%'><img src='imagens/div_txt.gif'  width='6' height='18' /></td>
        <td width='100'><span class='titulo_linhas'>Ag�ncia</span><br />
       ".$dadosboleto["agencia_codigo"]."</td>
        <td width='2%'><img src='imagens/div_txt.gif'  width='6' height='18' /></td>
        <td width='60'><span class='titulo_linhas'>Moeda</span><br />
         R$</td>
        <td width='2%'><img src='imagens/div_txt.gif'  width='6' height='18' /></td>
        <td width='20%'><span class='titulo_linhas'>Quantidade</span><br />
           ".$dadosboleto['quantidade']."</td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td colspan='3' style='border-bottom: 1px solid #000;' >
    
    <table width='100%' border='0' cellpadding='0' cellspacing='0' class='linhas_formatacao'>
      <tr>
        <td width='120'><span class='titulo_linhas'>(=) Valor do Documento</span><br />
          ".$dadosboleto["valor_boleto"]."</td>
        <td width='2%'><img src='imagens/div_txt.gif'  width='6' height='18' /></td>
        <td width='120'><span class='titulo_linhas'>Nosso N�mero</span><br />
         ".$dadosboleto["nosso_numero"]." </td>
        <td width='1%'><img src='imagens/div_txt.gif'  width='6' height='18' /></td>
        <td width='28%'><span class='titulo_linhas'>N� do Documento</span><br />
         ".$dadosboleto["numero_documento"]."</td>
        </tr>
    </table></td>
    <td colspan='3' style='border-bottom: 1px solid #000;' >
    
    <table width='100%' border='0' cellpadding='0' cellspacing='0' class='linhas_formatacao'>
      <tr>
        <td width='120'><span class='titulo_linhas'>(=) Valor do Documento</span><br />
          ".$dadosboleto["valor_boleto"]."</td>
        <td width='2%'><img src='imagens/div_txt.gif'  width='6' height='18' /></td>
        <td width='120'><span class='titulo_linhas'>Nosso N�mero</span><br />
          ".$dadosboleto["nosso_numero"]."</td>
        <td width='2%'><img src='imagens/div_txt.gif'  width='6' height='18' /></td>
        <td width='28%'><span class='titulo_linhas'>N� do Documento</span><br />
          ".$dadosboleto["numero_documento"]."</td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td colspan='3' style='border-bottom: 1px solid #000;' >
    
    <table width='100%' border='0' cellpadding='0' cellspacing='0' class='linhas_formatacao'>
      <tr>
        <td width='100'><span class='titulo_linhas'>(-) Desconto</span><br />
          0,00</td>
        <td width='2%'><img src='imagens/div_txt.gif'  width='6' height='18' /></td>
        <td width='100'><span class='titulo_linhas'>(+) Juros / Multa</span><br />
          0,00 </td>
        <td width='2%'><img src='imagens/div_txt.gif'  width='6' height='18' /></td>
        <td width='28%'><span class='titulo_linhas'>(=) Valor Cobrado</span><br />
          ".$dadosboleto["valor_boleto"]."</td>
      </tr>
    </table></td>
    <td colspan='3' style='border-bottom: 1px solid #000;' >
    
    <table width='100%' border='0' cellpadding='0' cellspacing='0' class='linhas_formatacao'>
      <tr>
        <td><span class='titulo_linhas'>Sacado</span><br />
          <span style='text-transform:uppercase;'>".$dadosboleto["sacado"]."</span> </td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td colspan='3' style='border-bottom: 1px solid #000;'>
    
    <table width='100%' border='0' cellpadding='0' cellspacing='0' class='linhas_formatacao'>
      <tr>
        <td><span class='titulo_linhas'>Sacado</span><br />
           <span style='text-transform:uppercase;'>".$dadosboleto["sacado"]."</span></td>
        </tr>
    </table></td>
    <td colspan='3' style='border-bottom: 1px solid #000;' ><table width='100%' border='0' cellpadding='0' cellspacing='0' class='linhas_formatacao'>
      <tr>
        <td width='280'><span class='titulo_linhas'>Assinatura do Recebedor</span><br />
          <br /></td>
        <td width='1%'><img src='imagens/div_txt.gif'  width='6' height='18' /></td>
        <td width='29%'><span class='titulo_linhas'>Data de Entrega<br />
          ___/____/_____
        </span></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td colspan='3' style='border-bottom: 1px dotted #000;' ><table width='100%' border='0' cellpadding='0' cellspacing='0' class='linhas_formatacao'>
      <tr>
        <td><span class='titulo_linhas'>Cedente</span><br />
          ".$dadosboleto["cedente"]."<br />
          ".$dadosboleto["linha_digitavel"]."</td>
      </tr>
    </table></td>
    <td colspan='3' style='border-bottom: 1px dotted #000;' >
    <table width='100%' border='0' cellpadding='0' cellspacing='0' class='linhas_formatacao'>
      <tr>
        <td><span class='titulo_linhas'>Cedente</span><br />
          ".$dadosboleto["cedente"]."<br />
          ".$dadosboleto["linha_digitavel"]."
         </td>
      </tr>
    </table>
    </td>
  </tr>
  
</table>

<table border='0' cellpadding='0' cellspacing='0' class='table1'>
  <tr>
    <td colspan='3'>
    <table width='100%' border='0' cellpadding='0' cellspacing='0'>
      <tr>
        <td style='border-bottom: 1px solid #000;'>
        <img src='imagens/bb_logo.png'  width='155' height='25' />
        </td>
        <td width='80' style='border-bottom: 1px solid #000;'>
        <span class='cabecalho'>|001-9|</span>
        </td>
        <td width='480' class='linhaDigitavel' style='border-bottom: 1px solid #000;'>".$dadosboleto["linha_digitavel"]."</td>
        </tr>
    </table>
   </td>
  </tr>
  <tr>
    <td colspan='3'><table width='100%' border='0' cellpadding='0' cellspacing='0'>
      <tr>
        <td width='429' style='border-bottom: 1px solid #000;' class='linhas_formatacao'>
        	<span class='titulo_linhas'>Local de Pagamento</span><br />
          	Pagável em qualquer banco até o vencimento.
       </td>
        <td width='1%'><img src='imagens/div_txt.gif'  width='6' height='18' /></td>
        <td width='80' style='border-bottom: 1px solid #000; background-color: #CCC;' class='linhas_formatacao'>
	         <span class='titulo_linhas'>Uso do Banco</span><br /><br />
	        
	    </td>
        <td width='1%'><img src='imagens/div_txt.gif'  width='6' height='18' /></td>
        <td width='194' style='border-bottom: 1px solid #000; background-color: #CCC;' class='linhas_formatacao'>
        	<span class='titulo_linhas'>Vencimento</span><br />
          	".$dadosboleto["data_vencimento"]."
         </td>
      </tr>
    </table>
    </td>
  </tr>
  <tr>
    <td colspan='3'><table width='100%' border='0' cellpadding='0' cellspacing='0' class='linhas_formatacao'>
      <tr>
        <td width='517' style='border-bottom: 1px solid #000;'><span class='titulo_linhas'>Cedente</span><br />
          ".$dadosboleto["cedente"]."
          </td>
        <td width='1%' ><img src='imagens/div_txt.gif'  width='6' height='18' /></td>
        <td width='191' style='border-bottom: 1px solid #000;'  ><span class='titulo_linhas'>Agência / Código Cedente</span><br />
          ".$dadosboleto["agencia_codigo"]."</td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td colspan='3'><table width='100%' border='0' cellpadding='0' cellspacing='0' class='linhas_formatacao'>
      <tr>
        <td width='517' style='border-bottom: 1px solid #000;' ><span class='titulo_linhas'>Endereço do Cedente</span><br />
          RUA ADJAR MACIEL, 35 - CENTRO - BELO JARDIM - PE</td>
        <td width='1%'><img src='imagens/div_txt.gif'  width='6' height='18' /></td>
        <td width='191' style='border-bottom: 1px solid #000;'><span class='titulo_linhas'>Nosso Número</span><br />
         ".$dadosboleto["nosso_numero"]."</td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td colspan='3'><table width='100%' border='0' cellpadding='0' cellspacing='0' class='linhas_formatacao'>
      <tr>
        <td width='60' style='border-bottom: 1px solid #000;'><span class='titulo_linhas'>Data Doc.</span><br />
          ".$data = date('d/m/Y')." </td>
        <td width='1%'><img src='imagens/div_txt.gif'  width='6' height='18' /></td>
        <td width='60' style='border-bottom: 1px solid #000;'><span class='titulo_linhas'>Nº Documento</span><br /> 
          ".$dadosboleto["numero_documento"]."
</td>
        <td width='1%'><img src='imagens/div_txt.gif'  width='6' height='18' /></td>
        <td width='50' style='border-bottom: 1px solid #000;'><span class='titulo_linhas'>Espécie.</span><br /> 
          DM
</td>
        <td width='1%'><img src='imagens/div_txt.gif'  width='6' height='18' /></td>
        <td width='30' style='border-bottom: 1px solid #000;'><span class='titulo_linhas'>Aceite.</span><br /> 
          S
</td>
        <td width='1%'><img src='imagens/div_txt.gif'  width='6' height='18' /></td>
        <td width='60' style='border-bottom: 1px solid #000;'><span class='titulo_linhas'>Data Proc.</span><br />
".$data = date('d/m/Y')."</td>
        <td width='1%'><img src='imagens/div_txt.gif'  width='6' height='18' /></td>
        <td width='50' style='border-bottom: 1px solid #000;'><span class='titulo_linhas'>Carteira</span><br /> 
          18/19
</td>
        <td width='1%'><img src='imagens/div_txt.gif'  width='6' height='18' /></td>
        <td width='40' style='border-bottom: 1px solid #000;'><span class='titulo_linhas'>Moeda</span><br /> 
          R$
</td>
        <td width='1%'><img src='imagens/div_txt.gif'  width='6' height='18' /></td>
        <td width='83' style='border-bottom: 1px solid #000;'><span class='titulo_linhas'>Quantidade</span><br />
          ".$dadosboleto['quantidade']."</td>
        <td width='1%'><img src='imagens/div_txt.gif'  width='6' height='18' /></td>
        <td width='191'style='border-bottom: 1px solid #000; background-color: #CCC;'><span class='titulo_linhas'>(=)Valor do Documento</span><br />
        <strong> ".$dadosboleto['valor_boleto']."</strong></td>
        </tr>
    </table></td>
  </tr>
  <tr>
    <td width='370' border='0' valign='top' class='linhas_formatacao' style='border-bottom: 1px solid #000;'>
    
     <span class='titulo_linhas'>Instruções de responsabilidade do cedente</span><br />
         
                    
          ".$dadosboleto["demonstrativo1"]."<br />	
		  ".$dadosboleto["demonstrativo2"]."<br />
		  ".$dadosboleto["demonstrativo3"]."<br />
		  ".$dadosboleto["instrucoes1"]."<br />
		  ".$dadosboleto["instrucoes2"]."<br />
		  ".$dadosboleto["instrucoes3"]."<br />
		  ".$dadosboleto["instrucoes4"]."
    </td>
    <td width='140' border='0' valign='top' class='linhas_formatacao' style='border-bottom: 1px solid #000;'>
    
    
    <br /><br />
			<barcode type='I25' value='".$nNumero."' label='true' style='width: 140px; height: 30px'></barcode>
			
    
    </td>
    <td width='180' valign='top' class='linhas_formatacao' border='0'>
    
    <table width='100%' border='0' cellpadding='0' cellspacing='0' class='linhas_formatacao'>
      <tr>
        <td width='4%'><img src='imagens/div_txt.gif'  width='6' height='18' /></td>
        <td width='191' style='border-bottom: 1px solid #000;'><span class='titulo_linhas'>(-) Desconto / Abatimento</span><br />
          0,00</td>
        </tr>
      </table>
      <table width='100%' border='0' cellpadding='0' cellspacing='0' class='linhas_formatacao'>
        <tr>
          <td width='4%'><img src='imagens/div_txt.gif'  width='6' height='18' /></td>
          <td width='191' style='border-bottom: 1px solid #000;'><span class='titulo_linhas'>(-) Outras Deduções</span><br />
            <br/></td>
        </tr>
      </table>
      <table width='100%' border='0' cellpadding='0' cellspacing='0' class='linhas_formatacao'>
        <tr>
          <td width='4%'><img src='imagens/div_txt.gif'  width='6' height='18' /></td>
          <td width='191' style='border-bottom: 1px solid #000;'><span class='titulo_linhas'>(+) Juros / Multa</span><br />
            <br/></td>
        </tr>
      </table>
      <table width='100%' border='0' cellpadding='0' cellspacing='0' class='linhas_formatacao'>
        <tr>
          <td width='4%'><img src='imagens/div_txt.gif'  width='6' height='18' /></td>
          <td width='191' style='border-bottom: 1px solid #000;'><span class='titulo_linhas'>(+) Outros Acréscimos</span><br />
            <br/></td>
        </tr>
      </table>
     
      <table width='100%' border='0' cellpadding='0' cellspacing='0' class='linhas_formatacao'>
        <tr>
          <td width='4%'><img src='imagens/div_txt.gif'  width='6' height='18' /></td>
          <td width='191' style='border-bottom: 1px solid #000;'><span class='titulo_linhas'>(=) Valor Cobrado</span><br />
          <br/></td>
        </tr>
    </table></td>
  </tr>
  <tr>
    <td colspan='3' >
    <table width='100%' border='0' cellpadding='0' cellspacing='0' class='linhas_formatacao'>
      <tr>
       
        <td width='720' style='border-bottom: 1px solid #000;'><span class='titulo_linhas'>Sacado:</span><br />
         
          ".$dadosboleto["sacado"]."<br />
          ".$dadosboleto['endereco1']."<br />
		  ".$dadosboleto['endereco2']."</td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td colspan='3' ><table width='100%' border='0' cellpadding='0' cellspacing='0' class='linhas_formatacao'>
      <tr>
       
        <td width='490' style='border-bottom: 1px dotted #000;' ><br />

		<barcode type='I25' value='".$dadosboleto["codigo_barras"]."' label='none' style='width: 424px; height: 52px' ></barcode>
       <br /><br /> <br /><br />
        </td>
       <td valign='top' width='230' style='border-bottom: 1px dotted #000;' ><span class='titulo_linhas'>Autenticação Mecanica</span><br />

		
       <br /><br /> <br /><br />
        </td>
      </tr>
      <tr>
      	<td>
      	
      	</td>
      </tr>
    </table></td>
  </tr>
</table>
	<br/><br/>
";

	}


$content .= "</page>";


    require_once('../libs/html2pdf/html2pdf.class.php');
    $html2pdf = new HTML2PDF('P','A4','pt', true, 'ISO-8859-1', array(0, 0, 0, 0));
	$html2pdf->writeHTML($content);
	$html2pdf->Output('boleto.pdf');
	
  	
?>







	
  
