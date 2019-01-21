<?php
namespace OxidEsales\EshopCommunity\Tests\Helper;

class Context
{
    protected static $activeUser;

    public static function isUserLoggedIn()
    {
        return isset(self::$activeUser);
    }

    public static function setActiveUser($activeUser)
    {
        self::$activeUser = $activeUser;
    }
}
