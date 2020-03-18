<?php

namespace Web\Communication;

use Web\Controller;
use Web\ViewFactory;
use Exception;
use Communication\Messages\SendMessageAware;
use Communication\Messages\MessagesProjection;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MessagesController implements Controller
{
    /** @var ViewFactory */
    private $viewFactory;

    /** @var SendMessageAware */
    private $chatService;

    /** @var MessagesProjection */
    private $messagesProjection;

    public function __construct(
        ViewFactory $viewFactory,
        SendMessageAware $chatService,
        MessagesProjection $messagesProjection
    ) {
        $this->viewFactory = $viewFactory;
        $this->chatService = $chatService;
        $this->messagesProjection = $messagesProjection;
    }

    const ROUTE_MESSAGES = 'messages';
    const ROUTE_SEND_MESSAGE = 'sendMessage';

    public function getRouteMap(): array
    {
        return [
            self::ROUTE_MESSAGES => ['/messages', 'messagesAction'],
            self::ROUTE_SEND_MESSAGE => ['/send_message', 'sendMessageAction'],
        ];
    }

    public function messagesAction(): Response
    {
        $view = $this->viewFactory->load('messages');

        $list = [];
        foreach ($this->messagesProjection->latestMessages(100) as $message) {
            array_unshift($list, $message);
        }
        $view->set('messages', $list);

        return new Response($view->render(), Response::HTTP_OK);
    }

    public function sendMessageAction(Request $request): Response
    {
        $messageText = $request->get('messageText', '');
        $view = $this->viewFactory->load('error');
        if (!empty(trim($messageText))) {
            try {
                $profileId = $request->getSession()->get('profileId');
                $this->chatService->sendMessage($profileId, $messageText);
            } catch (Exception $ex) {
                $view->set('error', $ex->getMessage());

                return new Response($view->render(), Response::HTTP_OK);
            }
        }

        return new RedirectResponse($view->url(self::ROUTE_MESSAGES));
    }
}
