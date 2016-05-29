<?php

namespace WasabiTheme\Basic\View;

use Wasabi\Cms\View\ThemeView;

/**
 * Class BasicThemeView
 */
class BasicThemeView extends ThemeView
{
    public $moduleCount = 0;
    public $modules = [];

    /**
     * Initialization hook method.
     */
    public function initialize()
    {
        parent::initialize();
    }
}
