<?php

namespace Norotaro\FirebaseUsers;

use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public $require = [
        'Winter.User',
        'Norotaro.Firebase',
    ];

    public function register()
    {
        $this->registerConsoleCommand('firebaseusers:sync', Console\SyncUsers::class);
    }

    public function registerSettings()
    {
        return [
            'settings' => [
                'label'       => 'norotaro.firebaseusers::lang.settings.menu',
                'description' => 'norotaro.firebaseusers::lang.settings.menu_description',
                'category'    => 'norotaro.firebase::lang.plugin.name',
                'icon'        => 'icon-address-card',
                'class'       => Models\Settings::class,
                'keywords'    => 'firebase',
                'permissions' => ['norotaro.firebaseusers.access_settings'],
            ]
        ];
    }

    public function registerSchedule($schedule)
    {
        if (Models\Settings::get('sync_auto_enabled', false)) {
            $frequency = Models\Settings::get('sync_auto_frequency', 'everyTenMinutes');
            $schedule->command('firebaseusers:sync')
                ->{$frequency}();
        }
    }
}
