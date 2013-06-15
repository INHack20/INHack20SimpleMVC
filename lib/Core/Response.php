<?php

namespace Core;

/**
 * Description of Response
 *
 * @author adcom23
 */
class Response {
    static function reDirect($url) {
        return header(sprintf('Location: %s',$url));
    }
}

?>
