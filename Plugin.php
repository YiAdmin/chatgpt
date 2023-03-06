<?php

namespace app\chatgpt;

use yi\Menu;

class Plugin
{
    public static function install() 
    {
        Menu::install('chatgpt', 'admin', 'admin');
        Menu::install('chatgpt', 'user', 'index,api');
    }

    public static function uninstall()
    {
        Menu::uninstall('chatgpt', 'admin');
        Menu::uninstall('chatgpt', 'user');
    }

    public static function enable()
    {
        Menu::enable('chatgpt', 'admin');
        Menu::enable('chatgpt', 'user');
    }

    public static function disable()
    {
        Menu::disable('chatgpt', 'admin');
        Menu::disable('chatgpt', 'user');
    }
}