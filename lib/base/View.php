<?php

class View
{
	// used for holding the content of the view script
	protected $_content = "";
	// the standard layout
	protected $_layout = 'layout';
	
	protected $_viewEnabled = true;
	protected $_layoutEnabled = true;
	
	// initializes the data array
	protected $_data = array();
	// intializes the additional javascripts to add to the header.
	protected $_javascripts = '';
	
	public $settings = null;
	
	public function __construct()
	{
	  $this->settings = new stdClass();
	}


    /**
     * @param $viewScript
     */
    protected function _renderViewScript($viewScript)
	{
		// starts the output buffer
		ob_start();
		
		// includes the view script
		include(ROOT_PATH . '/app/views/scripts/' . $viewScript);
		
		// returns the content of the output buffer
		$this->_content = ob_get_clean();
	}

    /**
     * @return string
     */
    public function content()
	{
		return $this->_content;
	}

    /**
     * @param $viewScript
     */
    public function render($viewScript)
	{
	  if ($viewScript && $this->_viewEnabled) {
  		// renders the view script
  		$this->_renderViewScript($viewScript);
	  }
		
	  if ($this->_isLayoutDisabled()) {
	    echo $this->_content;
	  }
	  else {
  		// includes the current view, which uses the "$this->content()" to output the 
  		// view script that was just rendered
  		include(ROOT_PATH . '/app/views/layouts/' . $this->_getLayout() . '.phtml');
	  }
	}
	
	/**
	 * Renders the given data as json
	 * @param mixed $data
	 */
	public function renderJson($data)
	{
	  $this->disableView();
	  $this->disableLayout();
	  
	  // sets the json headers
	  header('Cache-Control: no-cache, must-revalidate');
	  header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	  header('Content-type: application/json');
	  
	  echo json_encode($data);
	}
	
	protected function _getLayout()
	{
		return $this->_layout;
	}
	
	public function setLayout($layout)
	{
		$this->_layout = $layout;
		
		if ($layout) {
		  $this->_enableLayout();
		}
	}
	
	public function disableLayout()
	{
	  $this->_layoutEnabled = false;
	}
	
	public function disableView()
	{
	  $this->_viewEnabled = false;
	}

    /**
     * @param $key
     * @param $value
     */
    public function __set($key, $value)
	{
		// stores the data
		$this->_data[$key] = $value;
	}

    /**
     * @param $key
     * @return mixed|null
     */
    public function __get($key)
	{
		if (array_key_exists($key, $this->_data)) {
			return $this->_data[$key];
		}
		
		return null;
	}

    /**
     * @return string
     */
    public function baseUrl()
	{
		return WEB_ROOT;
	}

    /**
     * @param $script
     */
    public function appendScript($script)
	{
		$this->_javascripts .= '<script type="text/javascript" src="'.$script.'"></script>' ."\n";
	}

    public function printScripts()
	{
		echo $this->_javascripts;
	}

    protected function _enableLayout()
	{
	  $this->_layoutEnabled = true;
	}

    /**
     * @return bool
     */
    protected function _isLayoutDisabled()
	{
	  return !$this->_layoutEnabled;
	}
}
