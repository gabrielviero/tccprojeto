<?php 
require 'seguranca.php';
require 'conexao.php';

$query = "Select * from disciplina";
$result = mysql_query($query);

$query2 = "select * from arquivo as a, disciplina as d, disciplina_arquivo as da
		   where a.cod_arquivo = da.arquivo_cod_arquivo and da.disciplina_cod_disciplina = 1";
$arquivo = mysql_query($query2);

$listacurso = mysql_query("Select * from curso_aluno Left Join curso On curso_aluno.curso_cod_curso = curso.cod_curso where curso_aluno.aluno_cod_aluno = ". $_SESSION['codigo']);

if ($_SESSION['ap'] == 1) {
    $ap = "aluno";
}else if ($_SESSION['ap'] == 2) {
    $ap = "professor";
}else if ($_SESSION['ap'] == 3) {
    header("Location: admin.php");
}


?>
<html>
<head>
	<meta charset="utf-8">
	<title>Página inicial</title>
    <link href="css/bootstrap.css" rel="stylesheet" media="screen">
    <link href="css/style.css" rel="stylesheet" media="screen">
    <link href="css/uploadfile.css" rel="stylesheet" media="screen">
    <script src="js/jquery-1.11.0.min.js" type='application/javascript'></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="js/jquery.uploadfile.min.js"></script>

    <script src="js/jquery.slimscroll.min.js"></script>

<style>
    body{
        background: #2b2e37 url('img/bg-index.jpg');
        -webkit-background-size: cover;
        -moz-background-size: cover;
        -o-background-size: cover;
        background-size: cover;
        color: #fff;
    }
    a:hover{
        color: #fff;
    }

    .table > tbody > tr > td{
        border-top: 1px solid rgba(0,0,0,0.2);
    }

    .curso:hover, .semestre_prof:hover{
        color: #F97743;
    }

    .table .semestre{
        transition: opacity 0.2s ease;
    }

    .table:hover tr:not(:hover){
        opacity: 0.5;
    }

    h2{
        text-shadow: 0px 0px 26px rgba(0, 0, 0, 0.3);
    }
  
    tr{
        cursor: pointer;
    }

</style>

<script>

jQuery(document).ready(function ($) {

        $('#menu-disc').slimScroll({
            position: 'right',
            height: '500px',
            railVisible: true,
            alwaysVisible: false
        });



    $(".curso").click(function(){ /*lista os semestres do curso (aluno)*/
        var codigocurso = $(this).attr("data-id");
        jQuery.ajax({
            type: "POST",
            url: "index_semestres.php",
            async: false,
            data: { codigocurso: codigocurso},
            success: function( data ){
                $(".semestre_lista").html(data);
            }
        });      
    });

    $(".semestre_prof").click(function(){ /*lista as disciplinas desse semestre (professor) */
        var cod_semestreprof = $(this).attr("data-id");
        jQuery.ajax({
            type: "POST",
            url: "index_disc.php",
            async: false,
            data: { cod_semestreprof: cod_semestreprof},
            success: function( data2 ){
                $(".semestre_lista").html(data2);
            }
        });      
    });



});


</script>

</head>
<body>
   
   <!-- <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
        <div class="navbar-header">
            <a class="navbar-brand" href="index.php">Upload</a>
        </div>
    <ul class="nav navbar-top-links navbar-right">
        <ul class="nav navbar-nav navbar-right" style= 'margin-right: 0px';>            
            <?php if ($_SESSION['ap'] == 2){ echo '<li><a href="listagem.php">Meus arquivos</a></li>'; }?>
            <li><a href="#">Configurações</a></li>
            <li><a href="#">Ajuda</a></li>
            <li><a href="logout.php">Sair</a></li>
        </ul>
    </ul>
    </nav>-->

   
<div id="wrapper">



<!--principal-->

<div class="container">

    <div class="menumeio">


        <div class="col-lg-12" id='menutopo'>       

            <div class="bemvindo">
                <?php 
                    //echo $_SESSION['codigo'] ."<br />";
                    echo "<h2>Bem-vindo, ". $_SESSION['nome']  ."</h2><br />";
                    //echo "<h4>(Logado como ". $ap .")</h4><br />   ";
                    //echo date("Y");
                ?>
            </div>


            <div class="inicio-menu"><div id="sair">
                <a href='logout.php'><span class="glyphicon glyphicon-off"></span></a></div>
                <div id="config"><a href='configuracoes.php'><span class="glyphicon glyphicon-cog"></span></a></div>
                <div id="meusarquivos"><a href='listagem.php'><span class="glyphicon glyphicon-inbox"></span></a></div>
            </div>


        </div>

        <div class="col-lg-12" id='listagem'>


    <?php if ($_SESSION['ap'] == 1): ?> 

    <script type="text/javascript"></script>

 
            <div class="col-sm-4">

        <input type='button' class='btn btn-primary' value='Cadastrar em um curso' onclick='document.location="selecionarcurso.php"' />

                <div id='menu-disc'>
                    <table class='table table-hover'>
                        <thead>
                            <tr>
                                <th>Curso</th>  
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                while ($cursolist = mysql_fetch_object($listacurso)){
                                    echo "<tr class='curso' data-id='{$cursolist->curso_cod_curso}'>";
                                    echo "<td>{$cursolist->nome}</td>";
                                    echo "</tr>";
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
    <?php elseif ($_SESSION['ap'] == 2): ?>

        <div class="col-sm-4">

            <div id='menu-disc'>
                <table class='table table-hover'>
                    <thead>
                    <tr>
                        <th>Semestre</th>  
                    </tr>
                    </thead>
                    <tbody>
                    <?php

                    $discs_prof = mysql_query("Select * from disciplina Inner Join 
                        disciplina_professor On disciplina.cod_disciplina = disciplina_professor.disciplina_cod_disciplina 
                        where professor_cod_prof = " . $_SESSION['codigo'] . " order by disciplina.periodo"); //seleciona disciplinas que o prof leciona
                    $lista_disc = mysql_fetch_object($discs_prof);
                    


                    for($num=1; $num<=10; $num++){
                        echo "<tr class='semestre_prof' data-id='{$num}'>";
                        echo "<td>$num º semestre</td>";
                        echo "</tr>";
                    }

                    $listasemestres = "";

                    ?>
                    </tbody>
                </table>
            </div>
        </div>

    <?php endif ?>


        <div class="col-sm-8">
            <div class="semestre_lista">
                
            </div>
        </div>

        </div>

    </div>

</div>
        
</div>

</body>
</html>

