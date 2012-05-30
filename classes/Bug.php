<?php

require_once 'classes/Conexao.php';

class Bug extends Conexao
{
    public function listarBugs()
    {
        $query0 = "SELECT * FROM  bugs ORDER by ID DESC";
        $result0 = $this->conn->query($query0);
        
        if($result0->num_rows == 0)
        {
            return 'ERRO';
        }
        
        while($row0 = $result0->fetch_assoc())
        {
            $Bug = new BugVO();
            
            $Bug->ID = $row0['ID'];
            $Bug->DESCRICAO_REPRODUZIR_BUG = $row0['DESCRICAO_REPRODUZIR_BUG'];
            $Bug->COMPORTAMENTO_ESPERADO = $row0['COMPORTAMENTO_ESPERADO'];
            $Bug->COMPORTAMENTO_OBSERVADO = $row0['COMPORTAMENTO_OBSERVADO'];
            $Bug->QUEM_DESTINADO = $row0['QUEM_DESTINADO'];
            $Bug->CONSERTADO = $row0['CONSERTADO'];
            $Bug->DESCRICAO_RESOLUCAO = $row0['DESCRICAO_RESOLUCAO'];
            $Bug->DATA_HORA = $row0['DATA_HORA'];
            
            $Bugs[] = $Bug;
        }
        
        return $Bugs;        
    }
    public function cadastrarBugs(BugVO $bug)
    {
        $this->conn->autocommit(false);
        
        $query = "INSERT INTO bugs (ID, DESCRICAO_REPRODUZIR_BUG, COMPORTAMENTO_ESPERADO, COMPORTAMENTO_OBSERVADO, QUEM_DESTINADO, CONSERTADO)
        VALUES (NULL, '$bug->DESCRICAO_REPRODUZIR_BUG', '$bug->COMPORTAMENTO_ESPERADO', '$bug->COMPORTAMENTO_OBSERVADO',
        'MARCONI', 'N')";
        $this->conn->query($query);
        
        if($this->conn->error)
        {
            $this->conn->rollback();
            $this->conn->autocommit(true);
        }else{
            $this->conn->commit();
            $this->conn->autocommit(true);
        }
    }
}
?>