<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Zend\ServiceManager\ServiceManagerAwareInterface;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        return new ViewModel();
    }

    public function resetDbAction()
    {
        $sl = $this->getServiceLocator();
        $form = $sl->get('FormElementManager')->get('\Application\Form\Confirmation');
        $request = $this->getRequest();

        if ($request->isPost()) {
            $form->setData($request->getPost());
            
            if ($form->isValid()) {
                $this->resetDb();
                $this->flashMessenger()->addMessage('DB reset!');
                return $this->redirect()->toRoute('home');
            }
        }

        return array('form' => $form);
    }

    protected function resetDb()
    {
        $sm = $this->getServiceLocator();
        $em = $sm->get('doctrine.entitymanager.orm_default');
        $paths = $sm->get('doctrine.configuration.fixtures');

        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($em, $purger);

        foreach($paths as $key => $value) {
            $loader->loadFromDirectory($value);
        }

        foreach ($loader->getFixtures() as $fixture) {
            if ($fixture instanceof ServiceManagerAwareInterface) {
                $fixture->setServiceManager($sm);
            }
        }

        $executor->execute($loader->getFixtures());
    }
}
