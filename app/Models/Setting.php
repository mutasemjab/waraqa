<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Exception;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function getValue($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function setValue($key, $value)
    {
        return self::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    public static function adminEmail()
    {
        $email = self::getValue('admin_email');
        if (!$email) {
            throw new Exception('Admin email is not configured in settings');
        }
        return $email;
    }
}
