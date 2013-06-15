<?php
namespace Routing\Exception;
/**
 * Exception thrown when a route does not exists
 *
 * @api
 */
class RouteNotFoundException extends \InvalidArgumentException implements ExceptionInterface
{
    public function __construct($message, $code = 404, $previous=null) {
        parent::__construct($message, $code, $previous);
    }
}
