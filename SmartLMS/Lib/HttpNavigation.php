<?php

/**
*@desc Use for redirect browser to other page
*/
class HttpNavigation
{
    /**
    *@desc Use 301 header to redirect, and die()
    */
    public static function OutputRedirectToBrowser($url)
    {
        /* Redirect browser */
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: $url");
        exit(0);
    }
}

?>