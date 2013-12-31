<?php

namespace A3l\Deployer\Auth;

use A3l\Deployer\Auth\Exception\InvalidCredentialsException;

class UserManager
{
    /**
     * Filename to store the data
     */
    protected $filename = 'app/config/auth.bin';

    /**
     *
     */
    protected $data = array('users' => array());

    /**
     * Class Constructor
     * check if the auth file exists, if not creates an empty file based on auth.yml.dist
     */
    public function __construct()
    {
        if (!file_exists($this->filename))
        {
            exec("touch {$this->filename}");
        }else
        {
            $this->data = unserialize(file_get_contents($this->filename));
        }
    }

    /**
     * Create a user
     * @param string user
     * @param string password
     */
    public function create($user, $password)
    {
        if (isset($this->data['users'][$user]))
            throw new \InvalidArgumentException("User ${user} already exists");
        $password = Authentication::calculatePassword($user, $password);
        $this->data['users'][$user] = array('password' => $password, 'username' => $user);
        return $this;
    }

    public function getUser($username)
    {
        if (!isset($this->data['users'][$username]))
            throw new InvalidCredentialsException("User ${user} doesn't exists");
        return $this->data['users'][$username];
    }

    /**
     * Update the user password
     * @param string $user
     * @param string $password
     */
    public function changePassword($user, $password)
    {
        if (!isset($this->data['users'][$user]))
            throw new InvalidCredentialsException("User ${user} doesn't exists");
        $this->data['users'][$user] = array('password' => $password);
        return $this;
    }

    /**
     * Store the data in the persistent file
     */
    public function persist()
    {
        file_put_contents($this->filename, serialize($this->data));
        return $this;
    }
}