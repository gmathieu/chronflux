<?php

class View_Helper_Bubble extends Mg_View_Helper_AbstractHelper
{
    public function bubble($color = '', $filled = false)
    {
        $this->setAttributes(array(
            'class'      => 'bubble',
            'data-color' => $color,
        ));

        if ($filled) {
            $this->addAttribute('style', "background-color: #{$color};");
        }

        return <<<HTML
        <span {$this->renderAttributes()}>&nbsp;</span>
HTML;
    }
}