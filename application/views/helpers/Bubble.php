<?php

class View_Helper_Bubble extends Mg_View_Helper_AbstractHelper
{
    public function bubble($color = '', $selected = false)
    {
        $this->setAttributes(array(
            'class'      => 'bubble',
            'data-color' => $color,
        ));

        if ($selected) {
            $this->addAttribute('style', "background-color: #{$color};");
        }

        return <<<HTML
        <span {$this->renderAttributes()}>&nbsp;</span>
HTML;
    }
}