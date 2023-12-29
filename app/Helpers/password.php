<?php

if( !function_exists('generate_password') ) {
    /**
     * 
     * @param type $length
     * @return string
     */
    function generate_password($length = 20){
        $chars =  'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'.
            '0123456789`-=~!@#$%^&*()_+,./<>?;:[]{}\|';
        
        $str = '';
        $max = strlen($chars) - 1;
        
        for ($i=0; $i < $length; $i++) {
            $str .= $chars[random_int(0, $max)];
        }

        return $str;
    }
}

