<?php
	/**
	* Salva o arquivo do upload
	*
	* @author Gregui Shigunov
	* @since 12/08/2007
	*/
	
	//verificando se exite um upload de arquivo
	if (isset($_FILES['nome_do_campo']['name'])
	&& strlen($_FILES['nome_do_campo']['name']) > 1) {
	
	//salvando arquivo de upload
	$strOrigem = $_FILES['nome_do_campo'] ['tmp_name'];
	$strDestino = "./arquivosRet/".$_FILES['nome_do_campo']['name'];
	
	$bolOk = move_uploaded_file ($strOrigem, $strDestino);
	
	
	}
?>