<?php
namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;

class Option extends AbstractHelper
{
    protected $options;

    public function __construct($options)
    {
        $this->options = $options;
    }

    /**
     * __invoke
     *
     * @param  string $option Module option
     * @return mixed
     */
    public function __invoke($option)
    {
        $method = 'get' . ucfirst($option);

        return call_user_func(array($this->options, $method));
    }
}
