<?php

class Request
{
    /**
     * @return bool
     */
    public function isPost()
	{
		return ($_SERVER['REQUEST_METHOD'] == 'POST' ? true : false);
	}

    /**
     * @return bool
     */
    protected function _isGet()
	{
		return ($_SERVER['REQUEST_METHOD'] == 'GET' ? true : false);
	}

    /**
     * @param $key
     * @param null $default
     * @return null
     */
    public function getParam($key, $default = null)
	{
		if ($this->isPost()) {
			if(isset($_POST[$key])) {
				return $_POST[$key];
			}
		}
		else if ($this->_isGet()) {
			if(isset($_GET[$key])) {
				return $_GET[$key];
			}
		}
			
		return $default;
	}

    /**
     * @return mixed
     */
    public function getAllParams()
	{
		if ($this->isPost()) {
			return $_POST;
		}
		else if ($this->_isGet()) {
			return $_GET;
		}
	}
}
