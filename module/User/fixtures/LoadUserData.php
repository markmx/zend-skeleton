<?php

namespace User;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\FixtureInterface;
use ZfcUser\Entity\User;

use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\Crypt\Password\Bcrypt;

class LoadUserData implements FixtureInterface, ServiceManagerAwareInterface
{
    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * Set service manager instance
     *
     * @param ServiceManager $serviceManager
     * @return User
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;

        return $this;
    }

    public function load(ObjectManager $manager)
    {
        $zfcUserService = $this->serviceManager->get('zfcuser_user_service');

        $users = array(
            array(
                'email'    => 'adam@example.com',
                'password' => 'password',
            ),
            array(
                'email'    => 'barbara@example.com',
                'password' => 'password',
            ),
            array(
                'email'    => 'charlie@example.com',
                'password' => 'password',
            ),
        );

        foreach ($users as $user) {
            $user['passwordVerify'] = $user['password'];

            if (false === $zfcUserService->register($user)) {
                $this->reportError();
            }
        }
    }

    protected function reportError()
    {
        $form = $this->serviceManager->get('zfcuser_register_form');
        $allErrors = $form->getMessages();

        $errorsAsString = '';
        $commaSeparator = '';

        foreach ($allErrors as $fieldName => $fieldErrors) {
            $errorsAsString .= $commaSeparator . $fieldName . ' > ';
            $commaSeparator = ', ';
            $colonSeparator = '';

            foreach ($fieldErrors as $errorType => $errorMessage) {
                $errorsAsString .= $colonSeparator . $errorType .'=' . $errorMessage;
                $colonSeparator = ' : ';
            }
        }

        throw new \Exception('Validation errors found in data fixtures: ' . $errorsAsString);
    }
}
