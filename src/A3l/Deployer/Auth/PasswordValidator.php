<?php

namespace A3l\Deployer\Auth;

class PasswordValidator
{

    protected $password;
    protected $passwordConfirmation;

    public function __construct($password, $passwordConfirmation)
    {
        $this->password = $password;
        $this->passwordConfirmation = $passwordConfirmation;
    }

    /**
     * Check if the password is valid
     */
    public function isValid()
    {
        if ($this->password != $this->passwordConfirmation)
            throw new \InvalidArgumentException('Passwords doesn\'t match');
        if ( trim(strlen($this->password)) < 5 )
            throw new \InvalidArgumentException('Passwords must have 5 characters at least');
        return true;
    }

}