<?php
namespace EventBus\PortAdapter\ZF2\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class ConsoleController extends AbstractActionController
{

    public function consumeAction()
    {
        $eventBus = $this->getServiceLocator()->get('EventBus');
        $eventBus->subscribe();
    }

    public function testConsumeAction()
    {
        $this->getEventManager()->getSharedManager()->attach('*', 'test', function($data){
            var_dump($data);
        });

        $eventBus = $this->getServiceLocator()->get('EventBus');

        $eventBus->subscribe();
    }

    public function testPublishAction()
    {

        $message = $this->params()->fromRoute('message', 'default');

        $eventBus = $this->getServiceLocator()->get('EventBus');

        $eventBus->publish(['name' => 'test', 'params' => [$message]]);

    }

}