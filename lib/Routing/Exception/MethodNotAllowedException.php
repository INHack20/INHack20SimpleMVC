<?php
namespace Routing\Exception;
/**
 * The resource was found but the request method is not allowed.
 *
 * This exception should trigger an HTTP 405 response in your application code.
 * @api
 */
class MethodNotAllowedException extends \RuntimeException implements ExceptionInterface
{
    public function __construct($allowedMethods,$message = null, $code = 0, \Exception $previous = null)
    {
        parent::__construct($message.', Available methods are: '.  implode(',', $allowedMethods), $code, $previous);
    }
}
