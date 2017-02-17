<?php

class Register
{
    private static $_field = array();
    private static $_user = array();

    /**
     * @param $key
     * @param $value
     */
    public static function setField($key, $value)
    {
        self::$_field[$key] = $value;
    }

    /**
     * @param $key
     * @return mixed|null
     */
    public static function getField($key)
    {
        return isset(self::$_field[$key]) ? self::$_field[$key] : null;
    }

    /**
     * @param $value
     * @return bool
     */
    public static function setUser($value)
    {
        if (!empty(self::$_user))
            return false;

        self::$_user['id'] = $value['id'];
        self::$_user['name'] = $value['name'];
        self::$_user['created_at'] = $value['created_at'];
        return true;
    }

    /**
     * @return array
     */
    public static function getUser()
    {
        if (!self::$_user)
            return false;

        return self::$_user;
    }

    /**
     * @return array
     */
    public static function getUserId()
    {
        if (!isset(self::$_user['id']))
            return false;

        return self::$_user['id'];
    }

    public static function getUserName()
    {
        if (!isset(self::$_user['name']))
            return false;

        return self::$_user['name'];
    }
}