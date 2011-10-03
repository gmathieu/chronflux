<?php

class View_Helper_Button extends Mg_View_Helper_AbstractHelper
{
    public function button($name, $url = '#', $options = array())
    {
        $this->setOptions($options);

        // URL
        if ('#' === $url) {
            $url = 'javascript:void(0);';
        }

        // icon
        $icon = $this->renderIcon();

        // attributes
        $attr = $this->getOption('attr', array());
        $this->setAttributes($attr);

        return <<<HTML
        <a href="{$url}" class="button {$this->getOption('class', '')}" {$this->renderAttributes()}>
            {$icon}
            <label>{$this->view->escape($name)}</label>
        </a>
HTML;
    }

    public function renderIcon()
    {
        $icon = $this->getOption('icon');

        if ($icon) {
            return <<<HTML
            <span class="icon">{$icon}</span>
HTML;
        } else {
            return '';
        }
    }
}