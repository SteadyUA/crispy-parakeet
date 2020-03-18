<?php
namespace Web;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Throwable;

class App
{
    private $routes;
    private $request;

    public function __construct()
    {
        $session = new Session();
        $session->start();
        $this->request = Request::createFromGlobals();
        $this->request->setSession($session);
    }

    /**
     * @param Controller[] $controllers
     */
    public function registerControllers(iterable $controllers)
    {
        $this->routes = new RouteCollection();
        foreach ($controllers as $controller) {
            foreach ($controller->getRouteMap() as $routeName => list($routePath, $actionName)) {
                if (!method_exists($controller, $actionName)) {
                    throw new \RuntimeException("Method not exists: " . get_class($controller) . "::{$actionName}");
                }
                if (null !== $this->routes->get($routeName)) {
                    throw new \RuntimeException("Route '{$routeName}' already defined.");
                }
                $this->routes->add(
                    $routeName,
                    new Route($routePath, ['_controller' => [$controller, $actionName]])
                );
            }
        }
    }

    private function handleRequest(Request $request): Response
    {
        $context = new RequestContext();
        $context->fromRequest($request);

        $matcher = new UrlMatcher($this->routes, $context);
        try {
            $parameters = $matcher->match($request->getRequestUri());
            $request->attributes->add($parameters);

            return call_user_func($parameters['_controller'], $request);
        } catch (MethodNotAllowedException $ex) {
            return new Response($ex->getMessage(), Response::HTTP_METHOD_NOT_ALLOWED);
        } catch (ResourceNotFoundException $ex) {
            return new Response($ex->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }

    public function urlGenerator(): UrlGenerator
    {
        $context = new RequestContext();
        $context->fromRequest($this->request);

        return new UrlGenerator($this->routes, $context);
    }

    public function main()
    {
        $response = $this->handleRequest($this->request);
        $response->prepare($this->request);
        $response->send();
    }
}
