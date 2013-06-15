<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <br />
        <h1>Compruebo Requisitos</h1><br />
        <br />
        <h1>Compruebo Las recomendaciones</h1><br />
        
         <br/><br/>
         <?php
            $router->generate('installer_step_one');
         ?>
              <a href="<?php echo $router->generate('installer_step_one'); ?>">Siguiente</a>
    </body>
</html>
