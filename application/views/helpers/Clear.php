<?php

class View_Helper_Clear extends Mg_View_Helper_AbstractHelper
{
    public function clear()
    {
        return <<<HTML
        <span class="clear">&nbsp;</span>
HTML;
    }
}