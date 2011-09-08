<?php

class App_Form extends Zend_Form
{
    public $hiddenDecorators = array(
        'ViewHelper'
    );

    public $submitDecorators = array(
        'ViewHelper',
        array(array('form-submit-element' => 'HtmlTag'), array('tag' => 'div', 'class' => 'form-submit-element')),
        array(array('form-submit-wrapper' => 'HtmlTag'), array('tag' => 'div', 'class' => 'form-submit-wrapper')),
    );

    public function loadDefaultDecorators()
    {
        $this->setDecorators(array(
            'FormElements',
            'Form'
        ));
    }

    public function addHiddenElement($name, $params = array())
    {
        $this->addElement('hidden', $name, $params);
        $this->getElement($name)->setDecorators($this->hiddenDecorators);
    }

    public function addTextElement($name, $params = array())
    {
        $this->addElement('text', $name, $params);
        $this->getElement($name)->setDecorators($this->_rowDecorator('form-text-row'));
        $this->getElement($name)->addFilter('StringTrim');
    }

    public function addTextareaElement($name, $params = array())
    {
        $this->addElement('textarea', $name, $params);
        $this->getElement($name)->setDecorators($this->_rowDecorator('form-text-row'));
        $this->getElement($name)->addFilter('StringTrim');
    }

	public function addSubmitElement($value = 'submit')
	{
		$this->addElement('submit', 'submit', array(
			'label'      => $value,
			'decorators' => $this->submitDecorators
		));
	}

    private function _rowDecorator($rowWrapperClass = '')
    {
        return array(
            'ViewHelper',
            'Errors',
            array('Description', array('class' => 'description')),
            array(array('form-element-wrapper' => 'HtmlTag'), array('tag' => 'div', 'class' => 'form-element-wrapper')),
            array('label'),
            array(array('form-row' => 'HtmlTag'), array('tag' => 'div', 'class' => "form-row {$rowWrapperClass}")),
        );
    }
}