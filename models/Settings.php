<?php

namespace Norotaro\FirebaseUsers\Models;

use Model;

class Settings extends Model
{
    public $implement = ['System.Behaviors.SettingsModel'];

    // A unique code
    public $settingsCode = 'norotaro_firebaseusers_settings';

    // Reference to field configuration
    public $settingsFields = 'fields.yaml';
}
