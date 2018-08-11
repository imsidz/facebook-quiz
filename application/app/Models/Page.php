<?php
class Page extends Eloquent {
	protected $table = "pages";
	public static function decodePageJson($page)
    {
		$page->ogData = (array) json_decode($page->ogData);
		if(empty($page->ogData)) {
			$page->ogData = array(
				'ogImage' => '',
				'ogTitle' => '',
				'ogDescription' => ''
			);
		}
		return $page;
    }
}