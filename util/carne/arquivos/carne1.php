<?php    
    require_once('../Conexao.php');
?>





<html>
    <head>
            <style type="text/css">
                .titulo {
                  font-size: 18px;
                  font-weight: bold;
                  }
                .table{
                    border-bottom: 1px dotted #333;
                }
                .subTitulo{
                    font-size: 12px;
                    font-weight: bold;
                }
                .dados{
                    font-size: 12px;
                    font-weight: lighter;
                }
                .menssagem_rodape{
                    font-size: 10px;
                }
                .autenticao{
                    font-size:9px;
                }
                .recibo{
                    border-right: 1px dotted #333;
                }
            </style>
    </head>
    <body>
    
<?php
    if (isset($_GET['ids_boletos']))
    {	 		
	        
?>
    
    
        <table class="table">
            <tr>
                <td colspan="4" width="740"  align="center"><span class="titulo">CARNE DE PAGAMENTO<br/><br/></span></td>
            </tr>
            
            <tr >
                <td width="60" align="right"><span class="subTitulo">Cliente:</span></td>
                <td ><span class="dados">MARCONI CESAR PEREIRA DA SILVA</span></td>                
            </tr>
            <tr >
                <td width="60" align="right"><span class="subTitulo">CPF:</span></td>
                <td ><span class="dados">058.721.094-07</span></td>
            </tr>
            <tr >
                <td width="60" align="right"><span class="subTitulo">RG:</span></td>
                <td ><span class="dados">714503434</span></td>
            </tr>
            <tr >
                <td width="60" align="right"><span class="subTitulo">ENDERECO:</span></td>
                <td >
                    <span class="dados">VILA DR. FERNANDO DE ABREU QD. C</span>
                    <span class="subTitulo">BAIRRO:</span>
                    <span class="dados">SAO PEDRO</span>
                </td>
            </tr>
            <tr >
                <td width="60" align="right"><span class="subTitulo">CIDADE:</span></td>
                <td >
                    <span class="dados">BELO JARDIM</span>                   
                </td>
            </tr>
            <tr >
                <td width="60" align="right"><span class="subTitulo">REFERENCIA:</span></td>
                <td >
                    <span class="dados">PROX. A PRACA DOS EVENTOS</span>                   
                </td>
            </tr>
            
            <tr>
                <td colspan="4" width="740"  align="center">
                <br/><br/>
                    <span class="menssagem_rodape">Garanta Seu Atendimento, Pague em Dia!</span>
                </td>
            </tr>
           
        </table>
        <br/>
        
<?php
        $ids_get = explode(",", $_GET['ids_boletos']);
	$i = 0;
	foreach ($ids_get as $id)
	{
            $i++;
	    $ids_boletos[$i] = $id;
	}

        foreach ($ids_boletos as $id)
	{
            
        
                $query0 = "SELECT contas_receber . * , clientes.NOME_RAZAO, enderecos_cobranca. *
		    FROM contas_receber 
		    LEFT JOIN clientes ON contas_receber.CLIENTES_ID = clientes.ID
		    LEFT JOIN enderecos_cobranca ON  enderecos_cobranca.CLIENTES_ID = clientes.ID
		    WHERE contas_receber.ID = '$id'";
                    
                $result0 = $conn->query($query0);
                
                $row0 = $result0->fetch_assoc();
    
?>
        
        <table class="table">
            <tr>
                <td class="recibo" colspan="1" width="260"  align="left">
                   <span class="titulo">RECIBO</span><br/><br/>
                    <span class="subTitulo">Cliente</span><br/>
                    <span class="dados">MARCONI CESAR PEREIRA DA SILVA</span><br/>
                    <span class="subTitulo">Contrato</span>
                    <span class="dados">12344</span><br/>
                    <span class="subTitulo">Parc</span>
                    <span class="dados">1/10</span><br/>
                    <span class="subTitulo">Valor a Pagar</span>
                    <span class="dados">62,90</span><br/><br/>
                    <span class="subTitulo">Valor a Pago</span>
                    <span class="dados">____________</span><br/><br/><br/>
                    <span class="autenticao">Autenticacao</span>
                    <br/><br/><br/>
                </td>
                <td  colspan="1" width="470"  >                   
                    <span class="subTitulo">Cliente:</span><span class="dados">MARCONI CESAR PEREIRA DA SILVA</span><br/>
                    <span class="subTitulo">Contrato:</span>
                    <span class="dados">12344</span><br/>
                     <span class="subTitulo">Vencimento:</span>
                    <span class="dados">12/10/2012</span><br/>
                    <span class="subTitulo">Parc:</span>
                    <span class="dados">1/10</span><br/>
                    <span class="subTitulo">Valor a Pagar:</span>
                    <span class="dados">62,90</span><br/><br/><br/>
                    <span class="subTitulo">Valor a Pago:</span>
                    <span class="dados">____________</span><br/><br/><br/>
                    <span class="menssagem_rodape">Cobrar após vencimento:</span><br/>
                    <span class="menssagem_rodape">multa de R$ 1,25 e juros de R$ 0,43 por dia de atraso.</span>
                    <br/><br/><br/>
                </td>
            </tr>
            
         
            
           
        </table>
<?php               
                
        }
    }
?>

    </body>
</html>
