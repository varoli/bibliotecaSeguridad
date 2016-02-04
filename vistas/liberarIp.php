<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <script src="vistas/js/jquery-1.11.3.min.js"></script>
        <script src="vistas/js/bootstrap.min.js"></script>
        <link rel="stylesheet" href="vistas/css/bootstrap.min.css">
        <link rel="stylesheet" href="vistas/css/index.css">
        <title>Seguridad Informática</title>
    </head>
    <body>
    	<div class="container-fluid header txt">
    		<div class="row">
    			<div class="col-xs-12">
    				<header>
    					<p class="pull-left"><img class="img-responsive tamImg" src="vistas/img/logo_umar.png" /></p>
    					<p class="pull-right"><strong>Fecha: </strong><i><?php echo date("d - m - Y"); ?></i></p>
    				</header>
    			</div>
    		</div>
    		<div class="collapse navbar-collapse pull-right" id="nav">
				<ul class="nav navbar-nav" id="menu">
					<?php
                        echo($menu);
                    ?>
				</ul> <!-- /.navbar-nav -->
			</div> <!-- /#nav -->
    	</div>
    	<div class="container" id="contDinamic">
            <div class="row">
                <div class="col-xs-6">
                    <?php echo($ipBloqueada); ?>
                </div>
                <div class="col-xs-6">
                    <?php echo($listaUser); ?>
                </div>
            </div>
        </div> <!-- /#contDinamic -->
       <div class="container-fluid footer txt">
        	<div class="row">
    			<div class="col-xs-12">
    				<footer class="">  
        				<p><strong>Alumna: </strong><i>Lizeth vásquez Rojas</i></p>
        				<p><strong>Materia: </strong><i>Seguridad Informática</i></p>
        				<p><strong>Semestre: </strong><i>912</i></p>
        			</footer>
    			</div>
    		</div>
    	</div>
    </body>
</html>