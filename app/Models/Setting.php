<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'id_lab'];

    protected static function booted()
    {
        static::addGlobalScope('laboratorium', function ($builder) {
            if (Auth::check() && !Auth::user()->isAdmin()) {
                $builder->where('id_lab', Auth::user()->id_lab);
            }
        });
    }

    public static function get($key, $default = null, $labId = null)
    {
        if ($labId === null && auth()->check() && !auth()->user()->isAdmin()) {
            $labId = auth()->user()->id_lab;
        }

        $setting = self::where('key', $key)->where('id_lab', $labId)->first();
        return $setting ? $setting->value : $default;
    }

    public static function set($key, $value, $labId = null)
    {
        // Jika labId tidak diberikan, gunakan lab dari user login (kecuali admin)
        if ($labId === null && auth()->check() && !auth()->user()->isAdmin()) {
            $labId = auth()->user()->id_lab;
        }

        $setting = self::where('key', $key)->where('id_lab', $labId)->first();
        if ($setting) {
            $setting->update(['value' => $value]);
        } else {
            self::create([
                'key' => $key,
                'value' => $value,
                'id_lab' => $labId
            ]);
        }
    }

    public function laboratorium()
    {
        return $this->belongsTo(Laboratorium::class, 'id_lab');
    }
}
