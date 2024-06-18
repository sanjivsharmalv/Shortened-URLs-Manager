<?php

declare(strict_types=1);

namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    private $entityManager = null;
    private $userService = null;

    public function __constructor($entityManager, $userService)
    {
        $this->entityManager = $entityManager;
        $this->userService = $userService;
    }
    public function indexAction()
    {
        $vm = new ViewModel();
        $request = $this->getRequest();

        //get Query Parameters
        $queryParameters = $request->getQuery();

        $messageType = $queryParameters->get('messageType', '');

        $vm->setVariable('messageType', $messageType);
        $message = $queryParameters->get('message', '');

        $vm->setVariable('message', $message);
        $vm->setTemplate('./application/index/index');
        return $vm;
    }
}
