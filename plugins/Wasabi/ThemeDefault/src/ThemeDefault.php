<?php

namespace Wasabi\ThemeDefault;

use Wasabi\Cms\View\Theme\Theme;
use Wasabi\Cms\View\Theme\ThemeInterface;

class ThemeDefault extends Theme implements ThemeInterface
{
    /**
     * {@inheritdoc}
     */
    public function initialize() {
        $this->name(__d('wasabi_theme_default', 'Default'));
    }

    /**
     * {@inheritdoc}
     */
    public function registerLayouts()
    {
        return [
            'Default'
        ];
    }
}
