<?php

namespace Web\User;

use Web\Controller;
use Web\ViewFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class IndexController implements Controller
{
    /** @var ViewFactory */
    private $viewFactory;

    public function __construct(ViewFactory $viewFactory)
    {
        $this->viewFactory = $viewFactory;
    }

    const ROUTE_START = 'start';

    public function getRouteMap(): array
    {
        return [
            self::ROUTE_START => ['/', 'indexAction'],
        ];
    }

    public function indexAction(Request $request): Response
    {
        $view = $this->viewFactory->load('index');
        $profileId = $request->getSession()->get('profileId', null);
        $view->set('profileId', $profileId);

        return new Response($view->render(), Response::HTTP_OK);
    }
}
