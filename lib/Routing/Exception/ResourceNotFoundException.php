<?php
namespace Routing\Exception;
/**
 * The resource was not found.
 *
 * This exception should trigger an HTTP 404 response in your application code.
 * 
 * @api
 */
class ResourceNotFoundException extends \RuntimeException implements ExceptionInterface
{
}
