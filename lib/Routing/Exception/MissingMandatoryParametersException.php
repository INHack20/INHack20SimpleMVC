<?php
namespace Routing\Exception;
/**
 * Exception thrown when a route cannot be generated because of missing
 * mandatory parameters.
 *
 * @api
 */
class MissingMandatoryParametersException extends \InvalidArgumentException implements ExceptionInterface
{
}
