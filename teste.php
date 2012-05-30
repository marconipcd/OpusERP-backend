<?php

require_once 'classes/Estoque.php';
require_once 'vo/MovimentoEntCabecalhoVO.php';

$teste = new Estoque();
$mecVO = new MovimentoEntCabecalhoVO();

$mecVO->ID = 1;
$mecVO->EMPRESA_ID = 2;
$mecVO->COD_NF = 342324324;
$mecVO->FORNECEDOR_ID = 123;
$mecVO->QTD_ITENS = 1;
$mecVO->VALOR_TOTAL = 2;




print_r($teste->cadastrarCabeEntMovNF($mecVO));






			
    		
    	
    		

