<?php

class Validator
{
    static function Login()
    {
        $config = [
            [
                'field' => 'email',
                'label' => 'Email',
                'rules' => 'required|max_length[50]|valid_email',
            ],
            [
                'field' => 'password',
                'label' => 'Password',
                'rules' => 'required|min_length[5]'
            ]
        ];


        return $config;
    }
}
