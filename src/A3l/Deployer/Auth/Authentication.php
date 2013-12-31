<?php

namespace A3l\Deployer\Auth;

use A3l\Deployer\Auth\Exception\InvalidCredentialsException;

class Authentication
{
    const SALT = "ZTKn@~6][5BXbHRWLxp[S>3;RNd[vJkzF|kqrv]}d1d)^R1GQ3K&1=Be.Rzb|>Ex9";

    protected $userManager;

    public function __construct($userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * Create a hash for a password
     * @param string $username
     * @param string $password
     */
    public static function calculatePassword($username, $password)
    {
        return hash("sha512", Authentication::SALT.$username.$password);
    }

    /**
     *
     */
    public function login($username, $password)
    {
        $user = $this->userManager->getUser($username);
        if ($user['password'] != static::calculatePassword($username, $password))
            throw new InvalidCredentialsException('Invalid Credentials');
        return $user;
    }

}