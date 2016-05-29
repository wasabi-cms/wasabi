<?php

use Cake\Event\Event;
use Cake\Event\EventManager;

EventManager::instance()->dispatch(new Event('Wasabi.Cms.registerTheme', null, [
    'theme' => 'WasabiTheme/Basic.BasicTheme'
]));
