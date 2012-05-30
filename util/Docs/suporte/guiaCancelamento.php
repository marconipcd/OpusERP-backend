<?php

require_once '../../Connections/system.php';




?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<style type="text/css">
<!--
body {
	margin-top: 0px;
}
.guia {
	font-family: Arial, Helvetica, sans-serif;
}
.guia {
	font-size: 12px;
}
.guia {
	font-family: "Courier New", Courier, monospace;
}
.guiaNegrito {
	font-family: "Courier New", Courier, monospace;
	font-size: 12px;
	font-weight: bold;
}
-->
</style></head>

<body>
<table width="750" border="0" align="center" cellpadding="0" cellspacing="6">
  <tr>
    <td valign="top"><table border="0" align="center" cellpadding="0">
      <tr>
        <td align="left"><a href="#" onclick="window.print(); return false;"><img src="imagens/imprimir.png" width="18" height="18" border="0" /></a></td>
      </tr>
      <tr>
        <td align="center"><p><span class="guia">d i g i t a l</span></p>
          <p><span class="guia"> Rua Adjar Maciel, 35 Centro Belo Jardim/PE.<br />
            CEP: 55.150-040 Fone: (81)3726.3125<br />
            CNPJ: 07.578.965/0001-05 IE: 18.3.050.0329846-2<br />
            www.digitalonline.com.br</span></p></td>
      </tr>
      <tr>
        <td align="left"><span class="guia">°°°°°°°  A B E R T U R A   D E   C H A M A D O   °°°°°°°°°<br />
          ---------------------------------------------------<br />
          CHAMADO: </span><span class="guiaNegrito"><?php echo $row_chamadoCliente['id_chamado']; ?></span><span class="guia"> DATA:
          <?php $data = date('d/m/Y'); echo $data;?>
          <br />
          ---------------------------------------------------</span></td>
      </tr>
      <tr>
        <td align="left"><p><span class="guia">TIPO....: CANCELAMENTO INTERNET<br />
          MOTIVO..: </span><span class="guiaNegrito"><?php echo $row_chamadoCliente['motivo']; ?></span></p>
          <p><span class="guia">CONTATO.: </span><span class="guiaNegrito"><?php echo $row_cliente['contato']; ?></span><span class="guia"><br />
            ---------------------------------------------------</span></p></td>
      </tr>
      <tr>
        <td align="left"><span class="guia">Cliente.: </span><span class="guiaNegrito"><?php echo $row_chamadoCliente['textoNome']; ?></span><span class="guia"><br />
          </span><span class="guiaNegrito"><?php echo $row_cliente['textoEndereco']; ?></span> <span class="guiaNegrito"><?php echo $row_cliente['bairro']; ?></span><span class="guia"><br />
          </span><span class="guiaNegrito"><?php echo $row_cliente['codigoCep']; ?></span> <span class="guiaNegrito"><?php echo $row_cliente['textoCidade']; ?></span><span class="guia"><br />
          Ref.: </span><span class="guiaNegrito"><?php echo $row_cliente['referencia']; ?></span><span class="guia"><br />
          Fone1...: </span><span class="guiaNegrito"><?php echo $row_cliente['telefone']; ?></span><span class="guia">Cel......: </span><span class="guiaNegrito"><?php echo $row_cliente['celular1']; ?></span><span class="guia"><br />
          Fone2...: </span><span class="guiaNegrito"><?php echo $row_cliente['telefone2']; ?></span></td>
      </tr>
      <tr>
        <td align="left"><span class="guia">---------------------------------------------------</span></td>
      </tr>
      <tr>
        <td align="left"><span class="guia">TipoAce.: </span><span class="guiaNegrito"><?php echo $row_cliente['acesso']; ?></span><span class="guia"> Plano....: </span><span class="guiaNegrito"><?php echo $row_cliente['plano']; ?></span><span class="guia"><br />
          Regime..: </span><span class="guiaNegrito"><?php echo $row_cliente['regime']; ?></span><span class="guia"> Antena...: </span><span class="guiaNegrito"><?php echo $row_cliente['antena']; ?></span><span class="guia"><br />
          Material: </span><span class="guiaNegrito"><?php echo $row_produto_desc['desc_produto']; ?></span><span class="guia"><br />
          IP Radio:    .   .   . <br />
          IP......: </span></td>
      </tr>
      <tr>
        <td align="left"><span class="guia">---------------------------------------------------</span></td>
      </tr>
      <tr>
        <td align="left"><p><span class="guia">Obs:<br />
          Venho por meio desta solicitar o cancelamento do Servico de          Acesso a Internet de contrato numero </span><span class="guiaNegrito"><?php echo $row_cliente['codigoPessoa']; ?></span><span class="guia">, autorizando a 
          retirada do equipamento instalado, em caso de regime comodato, onde a nao devolucao do material, habilitara a 
          CONTRATADA, a promover o respectivo protesto e execucao ou          inclusao nos Servicos de Protecao ao Credito na forma do<br />
          Item 12 da Clausula SEXTA do respectivo contrato.<br />
          </span></p>
          <p><span class="guia">Sem mais para o momento,<br />
          </span></p>
          <p>&nbsp;</p>
          <p><span class="guia">---------------------------------------------------<br />
            Assinatura CONTRATANTE</span></p></td>
      </tr>
    </table></td>
    <td align="center"><table border="0" align="center" cellpadding="0">
      <tr>
        <td align="left">&nbsp;</td>
      </tr>
      <tr>
        <td align="center"><p><span class="guia">d i g i t a l</span></p>
          <p><span class="guia"> Rua Adjar Maciel, 35 Centro Belo Jardim/PE.<br />
            CEP: 55.150-040 Fone: (81)3726.3125<br />
            CNPJ: 07.578.965/0001-05 IE: 18.3.050.0329846-2<br />
            www.digitalonline.com.br</span></p></td>
      </tr>
      <tr>
        <td align="left"><span class="guia">°°°°°°°  A B E R T U R A   D E   C H A M A D O   °°°°°°°°°<br />
          ---------------------------------------------------<br />
          CHAMADO: </span><span class="guiaNegrito"><?php echo $row_chamadoCliente['id_chamado']; ?></span><span class="guia"> DATA:
            <?php $data = date('d/m/Y'); echo $data;?>
            <br />
            ---------------------------------------------------</span></td>
      </tr>
      <tr>
        <td align="left"><p><span class="guia">TIPO....: CANCELAMENTO INTERNET<br />
          MOTIVO..: </span><span class="guiaNegrito"><?php echo $row_chamadoCliente['motivo']; ?></span></p>
          <p><span class="guia">CONTATO.: </span><span class="guiaNegrito"><?php echo $row_cliente['contato']; ?></span><span class="guia"><br />
            ---------------------------------------------------</span></p></td>
      </tr>
      <tr>
        <td align="left"><span class="guia">Cliente.: </span><span class="guiaNegrito"><?php echo $row_chamadoCliente['textoNome']; ?></span><span class="guia"><br />
          </span><span class="guiaNegrito"><?php echo $row_cliente['textoEndereco']; ?></span> <span class="guiaNegrito"><?php echo $row_cliente['bairro']; ?></span><span class="guia"><br />
            </span><span class="guiaNegrito"><?php echo $row_cliente['codigoCep']; ?></span> <span class="guiaNegrito"><?php echo $row_cliente['textoCidade']; ?></span><span class="guia"><br />
              Ref.: </span><span class="guiaNegrito"><?php echo $row_cliente['referencia']; ?></span><span class="guia"><br />
                Fone1...: </span><span class="guiaNegrito"><?php echo $row_cliente['telefone']; ?></span><span class="guia">Cel......: </span><span class="guiaNegrito"><?php echo $row_cliente['celular1']; ?></span><span class="guia"><br />
                  Fone2...: </span><span class="guiaNegrito"><?php echo $row_cliente['telefone2']; ?></span></td>
      </tr>
      <tr>
        <td align="left"><span class="guia">---------------------------------------------------</span></td>
      </tr>
      <tr>
        <td align="left"><span class="guia">TipoAce.: </span><span class="guiaNegrito"><?php echo $row_cliente['acesso']; ?></span><span class="guia"> Plano....: </span><span class="guiaNegrito"><?php echo $row_cliente['plano']; ?></span><span class="guia"><br />
          Regime..: </span><span class="guiaNegrito"><?php echo $row_cliente['regime']; ?></span><span class="guia"> Antena...: </span><span class="guiaNegrito"><?php echo $row_cliente['antena']; ?></span><span class="guia"><br />
            Material: </span><span class="guiaNegrito"><?php echo $row_produto_desc['desc_produto']; ?></span><span class="guia"><br />
              IP Radio:    .   .   . <br />
              IP......: </span></td>
      </tr>
      <tr>
        <td align="left"><span class="guia">---------------------------------------------------</span></td>
      </tr>
      <tr>
        <td align="left"><p><span class="guia">Obs:<br />
          Venho por meio desta solicitar o cancelamento do Servico de          Acesso a Internet de contrato numero </span><span class="guiaNegrito"><?php echo $row_cliente['codigoPessoa']; ?></span><span class="guia">, autorizando a 
            retirada do equipamento instalado, em caso de regime comodato, onde a nao devolucao do material, habilitara a 
            CONTRATADA, a promover o respectivo protesto e execucao ou          inclusao nos Servicos de Protecao ao Credito na forma do<br />
            Item 12 da Clausula SEXTA do respectivo contrato.<br />
          </span></p>
          <p><span class="guia">Sem mais para o momento,<br />
          </span></p>
          <p>&nbsp;</p>
          <p><span class="guia">---------------------------------------------------<br />
            Assinatura CONTRATANTE</span></p></td>
      </tr>
    </table></td>
  </tr>
</table>
<p><br />
</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p><br />
  <br />
</p>
<p>&nbsp;</p>
</body>
</html>
<?php
mysql_free_result($chamadoCliente);

mysql_free_result($cliente);
?>
