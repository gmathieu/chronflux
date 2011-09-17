<?php

class User_View_Helper_InlineEditing extends Mg_View_Helper_AbstractHelper
{
    private $_first = true;
    private $_form;
    private $_dataObj;

    public function inlineEditing($name = null)
    {
        // access setter methods below
        if (is_null($name)) {
            return $this;
        }

        $firstClass     = '';
        $expandedClass  = '';
        $input          = $this->_form->getElement($name);
        $getProperty    = 'get' . ucfirst($name);

        // first element
        if (true === $this->_first) {
            $firstClass   = 'first';
            $this->_first = false;
        }

        // form errors
        if ($input->hasErrors()) {
            $expandedClass = 'expanded';
        }

        return <<<HTML
        <div class="inline-row-editing {$firstClass} {$expandedClass}">
            <a href="javascript:void(0)" class="inline-row-editing-edit-link">
                <span class="inline-row-editing-link-label open">Edit</span>
                <span class="form-row-label">{$input->getLabel()}</span>
                <span class="form-element-wrapper">{$this->_dataObj->$getProperty('--')}</span>
                {$this->view->clear()}
            </a>
            <div class="inline-row-editing-form-row">
                <a href="javascript:void(0)" class="inline-row-editing-link-label inline-row-editing-form-row-cancel">cancel</a>
                {$input->render()}
                <span class="form-row-label">&nbsp;</span>
                <span class="form-element-wrapper">{$this->view->formSubmit('', 'Update')}</span>
            </div>
        </div>
HTML;
    }

    public function setForm(Zend_Form $form)
    {
        $this->_form = $form;

        return $this;
    }

    public function setDataObject(Mg_Data_Object $dataObj)
    {
        $this->_dataObj = $dataObj;

        return $this;
    }
}