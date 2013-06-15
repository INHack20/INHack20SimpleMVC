<?php

use Routing\FileControllerCollection;
use Routing\FileController;

$fileControllerCollection = new FileControllerCollection();

$fileControllerCollection->add(new FileController('Installer','Installer/InstallerController.php'));

return $fileControllerCollection;
?>