<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class PasswordEncrypt extends Controller {

    /**
     * public variable $hashPassword
     */
    public $hashPassword;

        
    /**
     * method to hash password using bcrypt
     * note that bcrypt is design to encrypt but not to retrieved
     * the hashed password
     * 
     * @param $password
     * 
     * @return $hashPassword
     */
    public function hash($password) {
        $options = array(
            'cost' => 12,
          );
        $this->hashPassword = password_hash($password, PASSWORD_BCRYPT, $options);

        return trim($this->hashPassword);
    }

    /**
     * 
     * method to verify password using native php password_verify
     * 
     * @param $password $hashedPassword
     * 
     * return true if verification is success
     * return false if not
     */
    public function verifyPassword($password, $hashedPassword) {
        if(password_verify($password, $hashedPassword)) {
            return true;
        } else {
            return false;
        }
    }    
}
