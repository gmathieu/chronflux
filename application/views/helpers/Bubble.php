<?php

class View_Helper_Bubble extends Mg_View_Helper_AbstractHelper
{
    public function bubble($options = array())
    {
        $this->setOptions($options);

        $classes = 'bubble';
        $color = $this->getOption('color');

        // add # to color
        if ($color) {
            $color = "#{$color}";
        }

        // defined attributes
        $this->setAttributes(array(
            'data-color' => $color,
        ));

        // optional attributes
        $this->addAttributes($this->getOption('attr'));

        // automatically fill button when color is set
        if ($color) {
            $classes .= ' filled';
            $this->addAttribute('style', "background-color: {$color}; color: {$color}");
        }

        // add class attributes
        $this->addAttribute('class', $classes);

        return <<<HTML
        <span {$this->renderAttributes()}><span class="inner-bubble">&nbsp;</span></span>
HTML;
    }
}