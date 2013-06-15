 <br />
        <h1>Base de datos</h1><br />
<form method="POST" action="<?php echo $router->generate('installer_step_two'); ?>">
    <table border="0">
        <tr>
            <td>Nombre del Host *</td>
            <td><input type="text" name="siempre[SIEMPRE_main_db_host]" required="required" value="127.0.0.1" /></td>
        </tr>
        <tr>
            <td>Nombre de la base de datos *</td>
            <td><input type="text" name="siempre[SIEMPRE_main_db_name]" placeholder="'siempre' por defecto" value="" /></td>
        </tr>
        <tr>
            <td>Puerto de la base de datos *</td>
            <td><input type="text" name="siempre[SIEMPRE_main_db_port]" placeholder="'5432' por defecto" value="5432" /></td>
        </tr>
        <tr>
            <td>Usuario *</td>
            <td><input type="text" name="siempre[SIEMPRE_main_db_user]" required="required" value="" /></td>
        </tr>
        <tr>
            <td>Contrase&ntilde;a *</td>
            <td><input type="text" name="siempre[SIEMPRE_main_db_pass]" required="required" value="" /></td>
        </tr>
    </table>
    <input type="submit" value="Siguiente" />
</form>


<a href="<?php echo $router->generate('installer_index'); ?>">Atras</a>