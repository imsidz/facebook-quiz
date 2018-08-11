<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 28/03/15
 * Time: 4:28 AM
 */

class AdminCategoriesController extends BaseController{

    public function view() {
        $categories = Category::query();
        $categories = apply_filters('admin_categories_list_query', $categories);
        $grid = DataGrid::source($categories);  //same source types of DataSet
        $grid->add('name','Name', true);
        $grid = apply_filters('admin_categories_list_grid', $grid);

        $grid->edit(route('adminCategoriesAddEdit'), 'Actions','modify|delete');
        return View::make('admin.categories', compact('grid'));
    }

    public function addEdit() {
        $edit = DataEdit::source(new Category());
        if(!Input::get('modify'))
            $edit->label('Create Category');
        else
            $edit->label('Edit Category');
        $edit->add('name','Name', 'text')->rule('required|min:3');
        $edit = apply_filters('admin_category_editor_edit', $edit);
        //$edit->add('slug','Url slug', 'text')->rule('required|min:3');
        $edit->build();
        return $edit->view('admin.createEditCategory', compact('edit'));
    }

}