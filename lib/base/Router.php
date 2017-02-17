<?php

class Router
{

	public function execute($routes)
	{
		// tries to find the route and run the given action on the controller
		try {
			// the controller and action to execute
			$controller = null;
			$action = null;
			
			// tries to find a simple route
			$routeFound = $this->_getSimpleRoute($routes, $controller, $action);
			
			if (!$routeFound) {
				// tries to find the a matching "parameter route"
				$routeFound = $this->_getParameterRoute($routes, $controller, $action);
			}
			
			// no route found, throw an exception to run the error controller
			if (!$routeFound || $controller == null || $action == null) {
				throw new Exception('no route added for ' . $_SERVER['REQUEST_URI']);
			}
			else {
				// executes the action on the controller
				$controller->execute($action);
			}
		}
		catch(Exception $exception) {
			// runs the error controller
			$controller = new ErrorController();
			$controller->setException($exception);
			$controller->execute('error');
		}
	}

    /**
     * @param $route
     * @return int
     */
    public function hasParameters($route)
	{
		return preg_match('/(\/:[a-z]+)/', $route);
	}

    /**
     * @return array|string
     */
    protected function _getUri()
	{
		$uri = explode('?',$_SERVER['REQUEST_URI']);
		$uri = $uri[0];
		$uri = substr($uri, strlen(WEB_ROOT));
		
		return $uri;
	}

    /**
     * @param $routes
     * @param $controller
     * @param $action
     * @return bool
     */
    protected function _getSimpleRoute($routes, &$controller, &$action)
	{
		// fetches the URI
		$uri = $this->_getUri();
		
		// if the route isn't defined, try to add a trailing slash
		if (isset($routes[$uri])) {
			$routeFound = $routes[$uri];
		}
		else if(isset($routes[$uri . '/'])) {
			$routeFound = $routes[$uri . '/'];
		}
		else {
			$uri = substr($uri, 0, -1);
			// fetches the current route
			$routeFound = isset($routes[$uri]) ? $routes[$uri] : false;
		}
		
		// if a matching route was found
		if ($routeFound) {
			list($name, $action) = explode('#', $routeFound);
		
			// initializes the controller
			$controller = $this->_initializeController($name);
			
			return true;
		}
		
		return false;
	}

    /**
     * @param $routes
     * @param $controller
     * @param $action
     * @return bool
     */
    protected function _getParameterRoute($routes, &$controller, &$action)
	{
		// fetches the URI
		$uri = $this->_getUri();
		
		// testing routes with parameters
		foreach ($routes as $route => $path) {
			if ($this->hasParameters($route)) {
				$uriParts = explode('/:', $route);
					
				$pattern = '/^';
				//$pattern .= '\\'.($uriParts[0] == '' ? '/' : $uriParts[0]); 
				if ($uriParts[0] == '') {
					$pattern .= '\\/';
				}
				else {
					$pattern .= str_replace('/', '\\/', $uriParts[0]);
				}
					
				foreach (range(1, count($uriParts)-1) as $index) {
					$pattern .= '\/([a-zA-Z0-9]+)';
				}
				
				// now also handles ending slashes!
				$pattern .= '[\/]{0,1}$/';
					
				$namedParameters = array();
				$match = preg_match($pattern, $uri, $namedParameters);
				// if the route matches
				if ($match) {
					list($name, $action) = explode('#', $path);
		
					// initializes the controller
					$controller = $this->_initializeController($name);
		
					// adds the named parameters to the controller
					foreach (range(1, count($namedParameters)-1) as $index) {
						$controller->addNamedParameter(
								$uriParts[$index],
								$namedParameters[$index]
						);
					}
					
					return true;
				}
			}
		}
		
		return false;
	}

    /**
     * @param $name
     * @return mixed
     */
    protected function _initializeController($name)
	{
		// initializes the controller
		$controller = ucfirst($name) . 'Controller';
		// constructs the controller
		return new $controller();
	}
}
