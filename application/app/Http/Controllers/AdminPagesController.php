<?php

class AdminPagesController extends BaseController{
	
	public function listPages(){
		$pages = Page::all();
		return View::make('admin/pages/view')->with(array(
			'pages' => $pages
		));
	}
	
	public function createEdit(){
		
		$pageId = Input::get('pageId', null);
		$pageData = Input::get('page', array());
		
		try {
			if($pageId || !empty($pageData['id'])) {
				//die(var_dump($pageId));
				$page = Page::findOrFail($pageId ? $pageId : $pageData['id']);
			} else {
				$page = new Page;
			}
		} catch(ModelNotFoundException $e) {
			return Response::json(array(
				'error' => 1,
				'message' => $e->getMessage()
			));
		}
		
		if(Request::ajax() && Request::isMethod('post')) {
			//Form submitted- Create the page
			
			//$keys = ['topic', 'description', 'pageContent', 'image', 'questions', 'results', 'ogImages'];
			foreach($pageData as $key => $val) {
				$page->$key = is_array($pageData[$key]) ? json_encode($pageData[$key]) : $pageData[$key];
			}
			//var_dump($page);
			$page->save();
			return Response::json(array(
				'success' => 1,
				'page' => $page
			));
		} else {
			$pageSchema = new \Schemas\PageSchema();
			
			return View::make('admin/pages/create')->with(array(
				'pageSchema' => $pageSchema->getSchema(),
				'pageData' => Page::decodePageJson($page),
				'editingMode' => $pageId ? true : false,
				'creationMode' => $pageId ? false : true
			));
		}
	}
	
	public function delete(){
		$pageId = Input::get('pageId', null);
		if(!$pageId) {
			return('Invalid url! Page Id not passed!');
		}
		try{
			$page = Page::findOrFail($pageId ? $pageId : $pageData['id']);
		} catch(Illuminate\Database\Eloquent\ModelNotFoundException $e) {
			return('Invalid page! The page you are trying to delete doesn\'t exist.');
		}
		if(Request::isMethod('post')) {
			//POST method - confirmed - Delete now!
			if($page->delete()) {
				$deleteSuccess = true;
			}else {
				$deleteSuccess = false;
			}
			return View::make('admin/pages/delete')->with(array(
				'page' => Page::decodePageJson($page),
				'deleteSuccess' => $deleteSuccess
			));
		} else if(Request::isMethod('get')) {
			//Ask for confirmation
			return View::make('admin/pages/delete')->with(array(
				'page' => Page::decodePageJson($page),
				'getConfirmation' => true
			));
		}
	}
}