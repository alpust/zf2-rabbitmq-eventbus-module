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
        $this->getEventManager()->getSharedManager()->attach('*', '*', function($data){
            echo "async\n";
            var_dump($data->getName(), $data->getParams());
        });

        $eventBus = $this->getServiceLocator()->get('EventBus');

        $eventBus->subscribe();
    }

    public function testPublishAction()
    {

        $message = $this->params()->fromRoute('message', 'default');

        $this->getEventManager()->getSharedManager()->attach('*', 'inventory.test', function($data){
            echo "sync\n"; var_dump($data->getName(), $data->getParams());
        });

        $this->getEventManager()->trigger('inventory.test', null, [$message]);

    }

}