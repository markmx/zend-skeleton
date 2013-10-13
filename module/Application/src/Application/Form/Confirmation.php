<?php

namespace Application\Form;

use Zend\Form\Element;
use Zend\Form\Form;
use Zend\Validator\Identical;

/*
 * Standalone confirmation form
 * 
 * Secure confirmation form suitable for anonymous user usage.
 * 
 * @todo Add CAPTCHA option
 */
class Confirmation extends Form
{
    /**
     * {@inheritDoc}
     */
    public function init()
    {
        $csrf = new Element\Csrf('security');

        $confirm = new Element\Checkbox('confirm');
        $confirm->setAttributes(array('required' => true));
        $confirm->setLabel('Confirm current DB data will be destroyed and replaced with fixtures data.');

        $send = new Element('submit');
        $send->setValue('Reset DB');
        $send->setAttributes(array(
            'type'  => 'submit'
        ));

        $this->add($confirm);
        $this->add($csrf);
        $this->add($send);

        // Make the check box required
        $identical = new Identical('1');
        $messages = array(
            Identical::NOT_SAME => 'The box must be checked to confirm this action.'
        );

        $identical->setMessages($messages);
        $this->getInputFilter()->get('confirm')->getValidatorChain()->attach($identical);
    }
}
