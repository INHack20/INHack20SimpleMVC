<?php
namespace Routing\Generator;

use InvalidArgumentException;
use Routing\Exception\MissingMandatoryParametersException;
use Routing\Exception\RouteNotFoundException;
use Routing\RequestContext;
use Routing\RouteCollection;
/**
 * UrlGenerator generates a URL based on a set of routes.
 *
 * @api
 */
class UrlGenerator
{
    protected $context;
        
    const ROUTE = 'route';


    /**
     * This array defines the characters (besides alphanumeric ones) that will not be percent-encoded in the path segment of the generated URL.
     *
     * PHP's rawurlencode() encodes all chars except "a-zA-Z0-9-._~" according to RFC 3986. But we want to allow some chars
     * to be used in their literal form (reasons below). Other chars inside the path must of course be encoded, e.g.
     * "?" and "#" (would be interpreted wrongly as query and fragment identifier),
     * "'" and """ (are used as delimiters in HTML).
     */
    protected $decodedChars = array(
        // the slash can be used to designate a hierarchical structure and we want allow using it with this meaning
        // some webservers don't allow the slash in encoded form in the path for security reasons anyway
        // see http://stackoverflow.com/questions/4069002/http-400-if-2f-part-of-get-url-in-jboss
        '%2F' => '/',
        // the following chars are general delimiters in the URI specification but have only special meaning in the authority component
        // so they can safely be used in the path in unencoded form
        '%40' => '@',
        '%3A' => ':',
        // these chars are only sub-delimiters that have no predefined meaning and can therefore be used literally
        // so URI producing applications can use these chars to delimit subcomponents in a path segment without being encoded for better readability
        '%3B' => ';',
        '%2C' => ',',
        '%3D' => '=',
        '%2B' => '+',
        '%21' => '!',
        '%2A' => '*',
        '%7C' => '|',
    );

    protected $routes;

    /**
     * Constructor.
     *
     * @param RouteCollection $routes  A RouteCollection instance
     * @param RequestContext  $context The context
     *
     * @api
     */
    public function __construct(RouteCollection $routes, RequestContext $context)
    {
        $this->routes = $routes;
        $this->context = $context;
    }

    /**
     * {@inheritdoc}
     */
    public function setContext(RequestContext $context)
    {
        $this->context = $context;
    }

    /**
     * {@inheritdoc}
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * {@inheritDoc}
     */
    public function generate($name, $parameters = array(), $absolute = false)
    {
        if (null === $route = $this->routes->get($name)) {
            throw new RouteNotFoundException(sprintf('Route "%s" does not exist.', $name));
        }

        // the Route has a cache of its own and is not recompiled as long as it does not get modified
        $requirements = $route->getRequirements();
                
        // all params must be given
            if ($diff = array_diff_key($requirements,$parameters)) {
            throw new MissingMandatoryParametersException(sprintf('The "%s" route has some missing mandatory parameters ("%s").', $name, implode('", "', array_keys($diff))));
        }
        $url = array();
        $url[self::ROUTE] = $name;
        foreach ($parameters as $key => $value) {
            if(empty($value) && in_array($key, $requirements)){
                throw new InvalidArgumentException(sprintf('mandatory parameter "%s" can not be empty.', $key));
            }
            if((empty($key) || empty($value))){
                continue;
            }
            $url[$key] = $value;
        }
        $url = http_build_query($url);
        $url =$_SERVER['PHP_SELF'].'?'.$url;
        
        if ('' === $url) {
            $url = '/';
        }
        
        $url = $this->context->getBaseUrl().strtr($url, $this->decodedChars);
        
        return $url;
    }
}
