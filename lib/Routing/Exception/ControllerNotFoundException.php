<?php
namespace Routing\Exception;
/**
 * Exception thrown when a Controller does not exists
 *
 * @api
 */
class ControllerNotFoundException extends \InvalidArgumentException implements ExceptionInterface
{
}
