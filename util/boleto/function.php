<?php
        $con = mysql_connect('localhost', 'root', '');
        mysql_select_db('radius', $con);


        /**
         * função que retorna o select
         */
        function montaSelect()
        {
                $sql = "SELECT `codigoPessoa`, `textoNome` FROM `pessoa` ";
                $query = mysql_query( $sql );
                
                if( mysql_num_rows( $query ) > 0 )
                {
                        while( $dados = mysql_fetch_assoc( $query ) )
                        {
                                $opt .= '<option value="'.$dados['codigoPessoa'].'">'.$dados['textoNome'].'</option>';
                        }
                }
                else
                        $opt = '<option value="0">Nenhum cliente cadastrado!</option>';
        
                return $opt;
        }
        
        /**
         * função que devolve em formato JSON os dados do pessoa
         */
        function retorna( $id=null )
        {
                $id = (int)$id;
                
                $sql = "SELECT * FROM `pessoa` ";
                if( $id != null )
                        $sql .= "WHERE `codigoPessoa` = {$id} ";
                $query = mysql_query( $sql );
        
                //$json = 'var dados = ';
                $json .= ' [';  
                if( mysql_num_rows( $query ) > 0 )
                {
                        while( $dados = mysql_fetch_assoc( $query ) )
                        {
                                $json .= "{textoEndereco: '{$dados['textoEndereco']}', telefone: '{$dados['telefone']}'}";;
                        }
                }
                else
                        $json = 'textoEndereco: não encontrado';
                        
                $json .= ']';
                //$jston .= ';';
                
                return $json;
        }
        
        
/* só se for enviado o parâmetro, que devolve o combo */
if( isset($_GET['codigoPessoa']) )
{
        echo retorna( $_GET['codigoPessoa'] );
}
?>