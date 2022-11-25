<?php

namespace App\Auth;

class AuthenticationCheck
{
    private string $username;
    private string $password;

    public function __construct()
    {
        $this->username = config('kamar.username');
        $this->password = config('kamar.password');
    }

    public function fails()
    {
        return request()->server('HTTP_AUTHORIZATION') !== ("Basic " . base64_encode($this->username . ':' . $this->password));
    }
}
