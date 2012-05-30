<?php
header("Content-Type: text/html; charset=ISO-8859-1",true);
?>
<?php
    require_once '../Conexao.php';
    
    if(isset($_GET['nDAV']))
    {
        $nDAV = $_GET['nDAV'];
        
        
        //PROCURA ITENS DO DAV
        
	$query0 = "SELECT * 
                FROM  dav_detalhe WHERE NUMERO_DAV LIKE '$nDAV'";
	$result0 = $conn->query($query0);
        
        
        
        
    }
?>
<html>
    <head>
       <meta http-equiv="Content-Type" content="text/html;charset=utf-8" >
       <style>
            body{
                font-size: 8px;
            }
            p {font-size: 11px; padding: 0px; margin: 0px; text-align: center;}
            p .TITULO{font-size: 9px;}
            hr {padding: 0px; margin: 0px;}
       </style> 
    </head>
    <body>
        
      <p class="TITULO">DOCUMENTO AUXILIAR DE VENDA - PEDIDO</p>
      <p>NAO E DOCUMENTO FISCAL - NAO E VALIDO COMO RECIBO E COMO GARANTIA DE MERCADORIA - NAO COMPROVA PAGAMENTO</p>
      <br/>
      <p>Identificacao do Estabelecimento Emitente :</p>
      <p>OK MICRO LTDA</p>
      <p>RUA DEMOCRITO DE SOUZA FILHO 503  - MADALENA - RECIFE - PE  FONE : (81)3222-9197    (81)3031-7260</p>
      <p>CNPJ : 05.044.579/0001-08</p>
      <br/>
      <p>Identificação do Destinatário :</p>
      <p>ADEMIR DE SOUZA PINTO FILHO</p>
      <p>CNPJ : 07.578.965/0001-05</p>
      <br/>
      <br/>
      <br/>
      <br/>
      
      
      
      <table width="100%">
        <tr>
            <td width="300">Nº do Documento : 0000007908</td>
            <td>Nº do Documento Fiscal : _____________________</td>
        </tr>        
      </table>
      <br/>
      <br/>
       <table >
      
        <tr>
            <td width="80">Codigo</td>
            <td width="300">Produto</td>
            <td width="60">Quant.</td>
            <td width="60">Preco</td>
            <td width="60">SubTotal</td>
            <td width="60">Desconto</td>
            <td width="60">Total</td>
        </tr>
         <tr>
            <td colspan="7" width="680">
                <hr width="680" />                
            </td>
         </tr>
       
       <?php
            if($result0->num_rows > 0)
             {
           
                while($row0 = $result0->fetch_assoc())
                {
        ?>
      
        <tr>
            <td width="80"><?php echo($row0['ID_PRODUTO'])?></td>
            <td width="300"><?php echo($row0['NOME_PRODUTO'])?></td>
            <td width="60"><?php echo($row0['QUANTIDADE'])?></td>
            <td width="60"><?php echo($row0['VALOR_UNITARIO'])?></td>
            <td width="60"><?php echo($row0['VALOR_TOTAL'])?></td>
            <td width="60">0</td>
            <td width="60"><?php echo($row0['VALOR_TOTAL'])?></td>
        </tr>
        
        <?php
                }
             }
        ?>
        
        <tr>
            <td colspan="7" width="680">
                <hr width="680" />                
            </td>
         </tr>
       
       
      </table>
      
      
    </body>
</html>