<?php

namespace Web\Communication;

use Web\Controller;
use Web\ViewFactory;
use Communication\MemberList\MembersProjection;
use Symfony\Component\HttpFoundation\Response;

class MembersController implements Controller
{
    /** @var ViewFactory */
    private $viewFactory;

    /** @var MembersProjection */
    private $membersProjection;

    public function __construct(
        ViewFactory $viewFactory,
        MembersProjection $membersProjection
    ) {
        $this->viewFactory = $viewFactory;
        $this->membersProjection = $membersProjection;
    }

    const ROUTE_MEMBERS = 'members';

    public function getRouteMap(): array
    {
        return [
            self::ROUTE_MEMBERS => ['/members', 'membersAction'],
        ];
    }

    public function membersAction(): Response
    {
        $view = $this->viewFactory->load('members');
        $view->set(
            'members',
            $this->membersProjection->memberList()
        );

        return new Response($view->render(), Response::HTTP_OK);
    }
}
