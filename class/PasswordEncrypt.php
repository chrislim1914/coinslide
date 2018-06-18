<?php

class PasswordEncrypt {
    public $hashPassword;    

    public  function safe_b64encode($string) {
        $data = base64_encode($string);
        $data = str_replace(array('+','/','='),array('-','_',''),$data);
        return $data;
    }
    

    public function hash($password) {
        $options = array(
            'salt' => mcrypt_create_iv(32, MCRYPT_DEV_URANDOM),
            'cost' => 12,
          );
        $this->hashPassword = password_hash($password, PASSWORD_BCRYPT, $options);

        return trim($this->safe_b64encode($this->hashPassword));
    }

    public function verifyPassword($password) {
        if(password_verify($password, $this->hashPassword)) {
            echo 'true';
        } else {
            echo 'false';
        }
    }
}


