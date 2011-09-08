<?php

class View_Helper_SideColumnItem extends Mg_View_Helper_AbstractHelper
{
    public $classes;

    public function sideColumnItem($title, $options = array())
    {
        $this->setOptions($options);
        $this->setAttributes(array());

        $this->classes = 'side-column-item';
        $url           = $this->getOption('url');
        $tag           = 'div';

        // URL
        if ($url) {
            $this->addClass('side-column-item-selectable');
            $this->addAttribute('href', $url);
            $tag = 'a';

            if ($this->getOption('selected')) {
                $this->addClass('selected');
            }
        }

        // alt look
        if ($this->getOption('alt', false)) {
            $this->addClass('side-column-item-alt');
        }

        // new look
        if ($this->getOption('new', false)) {
            $this->addClass('side-column-item-new');
        }

        // icons
        $leftIcon  = $this->renderIcon('left');
        $rightIcon = $this->renderIcon('right');

        // attributes
        if (is_array($this->getOption('attr'))) {
            foreach ($this->getOption('attr') as $key => $value) {
                $this->addAttribute($key, $value);
            }
        }
        $this->addAttribute('class', $this->getClasses());

        return <<<HTML
        <{$tag} {$this->renderAttributes()}>
            <article>
                <hrgroup>
                    <h1 class="side-column-item-title">
                        {$leftIcon} {$this->view->escape($title)}
                    </h1>
                    {$this->renderSubtitle()}
                </hrgroup>
                {$rightIcon}
            </article>
        </{$tag}>
HTML;
    }

    public function addClass($name)
    {
        $this->classes .= ' ' . $name;
    }

    public function getClasses()
    {
        return trim($this->classes, ' ');
    }

    public function renderIcon($type)
    {
        $icon = $this->getOption("{$type}-icon");

        if ($icon) {
            $this->addClass("side-column-item-with-{$type}-icon");
    
            if (is_array($icon)) {
                return <<<HTML
                <img src="{$icon['src']}" class="icon" title="{$icon['title']}" />
HTML;
            } else {
                return $icon;
            }
        }
    }

    public function renderSubtitle()
    {
        $subtitle = $this->getOption('subtitle');
        if ($subtitle) {
            return <<<HTML
            <h2 class="side-column-item-subtitle">{$this->view->escape($subtitle)}</h2>
HTML;
        }
    }
}