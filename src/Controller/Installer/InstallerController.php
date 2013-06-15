<?php

use Core\FileConf;
use Core\Request;
use Routing\Controller;


/**
 * Description of InstallerController
 *
 * @author programacion4
 */
class InstallerController extends Controller {
    
    function indexAction() {
        $tpl = $this->getTemplate();
        return $tpl->renderTplClass('Installer:index.tpl.php');
    }
    
    function stepOneAction() {
        $tpl = $this->getTemplate();
        return $tpl->renderTplClass('Installer:step_one.tpl.php');
    }
    
    function stepTwoAction() {
        $tpl = $this->getTemplate();
        return $tpl->renderTplClass('Installer:step_two.tpl.php');
    }
}

?>
 
