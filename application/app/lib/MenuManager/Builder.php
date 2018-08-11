<?php namespace MenuManager;

class Builder {
	
	/**
	 * The items container
	 *
	 * @var array
	 */
	protected $items;

	/**
	 * The Menu configuration data
	 *
	 * @var array
	 */
	protected $conf;

	/**
	 * The route group attribute stack.
	 *
	 * @var array
	 */
	protected $groupStack = array();
	
	/**
	* The reserved attributes.
	*
	* @var array
	*/
	protected $reserved = array('route', 'action', 'url', 'prefix', 'parent', 'secure', 'raw');

	/**
	* The last inserted item's id
	*
	* @var int
	*/
	protected $last_id;
	


	/**
	 * Get the form action from the options.
	 *
	 * @return string
	 */
	public function dispatch($options)
	{
		// We will also check for a "route" or "action" parameter on the array so that
		// developers can easily specify a route or controller action when creating the
		// menus.
		if (isset($options['url']))
		{
			return $this->getUrl($options);
		}

		elseif (isset($options['route']))
		{
			return $this->getRoute($options['route']);
		}

		// If an action is available, we are attempting to point the link to controller
		// action route. So, we will use the URL generator to get the path to these
		// actions and return them from the method. Otherwise, we'll use current.
		elseif (isset($options['action']))
		{
			return $this->getControllerAction($options['action']);
		}

		return null;
	}

	/**
	 * Get the action for a "url" option.
	 *
	 * @param  array|string  $options
	 * @return string
	 */
	protected function getUrl($options)
	{
		foreach($options as $key => $value) {
			$$key = $value;
		}
		
		$secure = (isset($options['secure']) && $options['secure'] === true) ? true : false;

		if (is_array($url))
		{
			if( self::isAbs($url[0]) ){

				return $url[0];

			}

			return \URL::to($prefix . '/' . $url[0], array_slice($url, 1), $secure);
		}
		
		if( self::isAbs($url) ){

			return $url;

		}
		return \URL::to($prefix . '/' . $url, array(), $secure);
	}

	/**
	 * Check if the given url is an absolute url.
	 *
	 * @param  string  $url
	 * @return boolean
	 */
	public static function isAbs($url)
	{
		return parse_url($url, PHP_URL_SCHEME) or false;		
	}

	/**
	 * Get the action for a "route" option.
	 *
	 * @param  array|string  $options
	 * @return string
	 */
	protected function getRoute($options)
	{
		if (is_array($options))
		{
			return \URL::route($options[0], array_slice($options, 1));
		}

		return \URL::route($options);
	}

	/**
	 * Get the action for an "action" option.
	 *
	 * @param  array|string  $options
	 * @return string
	 */
	protected function getControllerAction($options)
	{
		if (is_array($options))
		{
			return \URL::action($options[0], array_slice($options, 1));
		}

		return \URL::action($options);
	}



}
