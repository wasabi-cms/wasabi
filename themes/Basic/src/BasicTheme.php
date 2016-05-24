<?php

namespace Wasabi\Theme\Basic;

use Wasabi\Cms\View\Theme\Theme;
use Wasabi\Cms\View\Theme\ThemeInterface;

class BasicTheme extends Theme implements ThemeInterface
{
    /**
     * {@inheritdoc}
     */
    public function initialize() {
        $this->name('Basic');
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
