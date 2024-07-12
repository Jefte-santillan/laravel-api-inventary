<?php

namespace App\Kernel\Models;

/**
 * 
 *
 * @author Luis Josafat Heredia Contreras
 */
trait UuidForKey
{
    
    /**
     * Boot the Uuid trait for the model.
     *
     * @return void
     */
    public static function bootUuidForKey()
    {
        static::creating(function($model) {
            $model->incrementing = false;            
            /* required for use seeder */
            if( $model->id) {                
                return true;                
            }            
            $model->{$model->getKeyName()} = app('uuid')->v5();            
        });        
    }
    
    /**
     * necesary laravel 5.6
     */
    public static function boot()
    {
        // fix laravel 5.8
        // https://laracasts.com/discuss/channels/laravel/laravel-57-upgrade-observer-problem
        parent::boot();        
        // fix laravel 5.8
        // https://github.com/JamesHemery/laravel-uuid/
        static::saving(function ($model) {
            $model->keyType = 'string';
            $model->incrementing = false;
            /* required for use seeder */
            if( $model->id) {                
                return true;                
            }
            $model->{$model->getKeyName()} = app('uuid')->v5();
        });
        static::creating(function($model) {
            $model->incrementing = false;            
            /* required for use seeder */
            if( $model->id) {                
                return true;                
            } 
            $model->{$model->getKeyName()} = app('uuid')->v5();            
        });
    }
    
}
