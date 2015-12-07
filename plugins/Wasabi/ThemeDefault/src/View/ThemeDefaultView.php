<?php

namespace Wasabi\ThemeDefault\View;

use App\View\AppView;
use Wasabi\Cms\View\Helper\MenuHelper;
use Wasabi\Cms\View\Helper\MetaHelper;
use Wasabi\Core\View\Helper\AssetHelper;

/**
 * Class ThemeDefaultView
 *
 * @property MetaHelper Meta
 * @property AssetHelper Asset
 * @property MenuHelper Menu
 */
class ThemeDefaultView extends AppView
{
    /**
     * Initialization hook method.
     */
    public function initialize()
    {
        parent::initialize();

        $this->loadHelper('Meta', ['className' => 'Wasabi/Cms.Meta']);
        $this->loadHelper('Asset', ['className' => 'Wasabi/Core.Asset']);
        $this->loadHelper('Menu', ['className' => 'Wasabi/Cms.Menu']);
    }

    public function contentArea($contentArea)
    {
        return $this->cell('Wasabi/Cms.ContentArea', [$contentArea]);
    }
}
