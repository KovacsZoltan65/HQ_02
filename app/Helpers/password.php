<?php

if( !function_exists('generatePassword') ) {
    /**
     * 
     * @param type $length
     * @return string
     */
    
    function generatePassword($minLength = 6, $maxLength = 8){
        //$chars =  "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'0123456789`-=~!@#$%^&*()_+,./<>?;:[]{}\|";
        $chars =  "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        
        $str = '';
        $max = strlen($chars) - 1;
        
        for ($i=0; $i < $maxLength; $i++) {
            $str .= $chars[random_int(0, $max)];
        }

        return $str;
    }
    
    
    function generatePassword2($minLength = 6, $maxLength = 8){
        
        //\Log::info( print_r( $minLength , true) );
        //\Log::info( print_r( $maxLength, true) );
        
        $faker = Faker\Factory::create();
        $password = $faker->password($minLength, $maxLength);
        
        $allowedChars = [
            'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
            'a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z',
            '0','1','2','3','4','5','6','7','8','9'];
        $input = str_split($password);
        
        if(in_array($input,$allowedChars)) {
            generatePassword($minLength, $maxLength);
        }else{
            //
        }
        
        return $password;
    }
    
}

