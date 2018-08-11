<?php

class BaseController extends Controller {

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout()
	{
		if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
	}

    public static function loadCategories() {
        $categories = new \Illuminate\Database\Eloquent\Collection();
        $categoriesQuery = Category::query();
        $categoriesQuery = apply_filters('categories_list_query', $categoriesQuery);
        try {
            $categories = $categoriesQuery->get();
        } catch(Exception $e) {
            //Discard query exception.. categories table not found.. discard
        }
        $categoriesMap = [];
        if($categories->count()) {
            foreach ($categories as $category) {
                $categoriesMap[$category->id] = $category->name;
            }
        }
        App::bind('categories', function() use($categories) {
            return $categories;
        });
        \Event::fire('categories:loaded', $categories);
        View::share('categories', $categories);
        View::share('categoriesMap', $categoriesMap);
    }

}
