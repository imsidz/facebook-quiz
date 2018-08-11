<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 28/03/15
 * Time: 4:29 AM
 */

use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;


class Category extends Eloquent{
    use Sluggable;
    use SluggableScopeHelpers;
    protected $table = "categories";
    public $timestamps = false;

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }
}
