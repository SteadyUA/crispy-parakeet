<?php

namespace Web\User;

use User\Auth\Exception\LoginNameExistsException;
use User\Auth\Exception\UnknownLoginNameException;
use Web\Controller;
use Web\ViewFactory;
use Exception;
use User\Auth\AuthService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthController implements Controller
{
    /** @var ViewFactory */
    private $viewFactory;

    /** @var AuthService */
    private $authService;

    public function __construct(
        ViewFactory $viewFactory,
        AuthService $authService
    ) {
        $this->viewFactory = $viewFactory;
        $this->authService = $authService;
    }

    const ROUTE_SIGN_IN = 'signIn';
    const ROUTE_LOG_IN = 'logIn';
    const ROUTE_LOG_OUT = 'logOut';

    public function getRouteMap(): array
    {
        return [
            self::ROUTE_SIGN_IN => ['/sign_in', 'signInAction'],
            self::ROUTE_LOG_IN => ['/log_in', 'logInAction'],
            self::ROUTE_LOG_OUT => ['/log_out', 'logOutAction'],
        ];
    }

    public function signInAction(Request $request): Response
    {
        $view = $this->viewFactory->load('signin');
        $loginName = $request->get('loginName');
        if (isset($loginName)) {
            try {
                $profileId = $this->authService->signIn($loginName);
                $request->getSession()->set('profileId', $profileId);

                return new RedirectResponse($view->url(IndexController::ROUTE_START));
            } catch (LoginNameExistsException $ex) {
                $view->set('error', $ex->getMessage());
            }
        }

        return new Response($view->render(), Response::HTTP_OK);
    }


    public function logInAction(Request $request): Response
    {
        $view = $this->viewFactory->load('login');
        $loginName = $request->get('loginName');
        if (isset($loginName)) {
            try {
                $profileId = $this->authService->logIn($loginName);
                $request->getSession()->set('profileId', $profileId);

                return new RedirectResponse($view->url(IndexController::ROUTE_START));
            } catch (UnknownLoginNameException $ex) {
                $view->set('error', $ex->getMessage());
            }
        }

        return new Response($view->render(), Response::HTTP_OK);
    }

    public function logOutAction(Request $request): Response
    {
        $view = $this->viewFactory->load('error');
        $profileId = $request->getSession()->get('profileId');
        if (isset($profileId)) {
            $this->authService->logOut($profileId);
            $request->getSession()->remove('profileId');
        } else {
            $view->set('error', 'You are not logged in.');
            return new Response($view->render(), Response::HTTP_OK);
        }

        return new RedirectResponse($view->url(IndexController::ROUTE_START));
    }
}
