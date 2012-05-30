<html>
<head>
        <script type="text/javascript" src="jquery.js"></script>
        <script type="text/javascript">
        $(document).ready(function(){
                $("select[name='nome']").change(function(){
                        $("input[name='endereco']").val('Carregando...');
                        $("input[name='telefone']").val('Carregando...');

                        $.getJSON(
                                'function.php',
                                {idCliente: $(this).val()},
                                function(data){
                                        $.each(data, function(i, obj){
                                                $("input[name='endereco']").val(obj.endereco);
                                                $("input[name='telefone']").val(obj.telefone);
                                        })
                                });
                });
        });
        </script>
</head>
<body>
        <form action="" method="post">
                <label>Nome: <select name="nome">
                        <option value="" selected>--</option>
<?php
        //http://www.wbruno.com.br/blog/?p=12
        include('function.php');
        echo montaSelect();
?>
                </select></label>
                <label>Endereço: <input type="text" name="endereco" value="" /></label>
                <label>Telefone: <input type="text" name="telefone" value="" /></label>

        </form>
</body>
</html>