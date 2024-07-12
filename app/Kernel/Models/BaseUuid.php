<?php

namespace App\Kernel\Models;

/**
 * 
 *
 * @author Luis Josafat Heredia Contreras
 */
class BaseUuid extends Base
{
    
    use UuidForKey;
    public $incrementing = false;
    
}
