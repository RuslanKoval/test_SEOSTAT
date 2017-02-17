<?php

class Controller
{
	// defines the view
	public $view = null;
	// defines the request
	protected $_request = null;
	// the current action
	protected $_action = null;
	
	protected $_namedParameters = array();

    public function init()
	{
		$this->view = new View();
		
		$this->view->settings->action = $this->_action;
		$this->view->settings->controller = strtolower(str_replace('Controller', '', get_class($this)));
	}

	public function beforeFilters()
	{
		// no standard filers
	}

	public function afterFilters()
	{
		// no standard filers
	}


    /**
     * @param string $action
     */
    public function execute($action = 'index')
	{
		// stores the current action
		$this->_action = $action;
		
		// initializes the controller
		$this->init();
		
		// executes the before filters
		$this->beforeFilters();
		
		// adds the action suffix to the function to call
		$actionToCall = $action.'Action';
		
		// executes the action
		$this->$actionToCall();
		
		// executes the after filterss
		$this->afterFilters();
		
		// renders the view
		$this->view->render($this->_getViewScript($action));
	}

    /**
     * @param $action
     * @return string
     */
    protected function _getViewScript($action)
	{
		// fetches the current controller executed
		$controller = get_class($this);
		// removes the "Controller" part and adds the action name to the path
		$script = strtolower(substr($controller, 0, -10) . '/' . $action . '.phtml');
		// returns the script to render
		return $script;
	}

    /**
     * @return string
     */
    protected function _baseUrl()
	{
		return WEB_ROOT;
	}

    /**
     * @return null|Request
     */
    public function getRequest()
	{
		if ($this->_request == null) {
			$this->_request = new Request();
		}
		
		return $this->_request;
	}

    /**
     * @param $key
     * @param null $default
     * @return mixed|null
     */
    protected function _getParam($key, $default = null)
	{
		// tests against the named parameters first
		if (isset($this->_namedParameters[$key])) {
			return $this->_namedParameters[$key];
		}
		
		// tests against the GET/POST parameters
		return $this->getRequest()->getParam($key, $default);
	}

    /**
     * @return array
     */
    protected function _getAllParams()
	{
		return array_merge($this->getRequest()->getAllParams(), $this->_namedParameters);
	}
	
	public function addNamedParameter($key, $value)
	{
		$this->_namedParameters[$key] = $value;
	}

    /**
     * @return bool
     */
    public function loadData()
    {
        $data = $this->_getAllParams();
        if(!$data)
            return false;

        foreach ($data as $key => $item) {
            Register::setField($key, $this->hackpro($item));
        }
        return true;
    }


    /**
     * @param $string
     * @return mixed|null
     */
    private function hackpro($string) {
        if (!isset($string)) {
            return NULL;
        }
        $string = preg_replace("/[^A-Za-z0-9?!.,'@$ _-]/", '', $string);
        $string = preg_replace("/\?/", "&#63;", $string);
        $string = preg_replace("/\!/", "&#33;", $string);
        $string = preg_replace("/\'/", "&#39;", $string);
        //$string = preg_replace("/\,/", "&#44;", $string);
        $string = preg_replace("/\\\$/", "&#36;", $string);

        return $string;
    }
}
