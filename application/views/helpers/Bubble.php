<?php

class View_Helper_Bubble extends Mg_View_Helper_AbstractHelper
{
    public function bubble($options = array())
    {
        $this->setOptions($options);

        $color = $this->getOption('color');

        // defined attributes
        $this->setAttributes(array(
            'class'      => 'bubble',
            'data-color' => $color,
        ));

        // optional attributes
        $this->addAttributes($this->getOption('attr'));

        // automatically fill button when color is set
        if ($color) {
            $this->addAttribute('style', "background-color: #{$color}; color: #{$color}");
        }

        return <<<HTML
        <span {$this->renderAttributes()}>&nbsp;</span>
HTML;
    }
}