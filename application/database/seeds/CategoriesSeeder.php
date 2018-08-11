<?php
use Illuminate\Database\Seeder;

/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 12/12/15
 * Time: 2:33 PM
 */
class CategoriesSeeder extends Seeder{

    public function run()
    {
        Category::create(['name'    =>  'Funny']);
        Category::create(['name'    =>  'Creative']);
        Category::create(['name'    =>  'Cute']);
        Category::create(['name'    =>  'Sports']);
        Category::create(['name'    =>  'Movies']);
    }

}