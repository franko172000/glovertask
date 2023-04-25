<?php

namespace App\Traits;

trait UserRulesTrait
{
    public function createRules(){
        return [
            'first_name' => 'string|required',
            'last_name' => 'string|required',
            'phone' => 'string',
            'email' => 'string|email|required|unique:users',
            'password' => 'string'
        ];
    }

    public function updateRules(){
        return [
            'first_name' => 'string',
            'last_name' => 'string',
            'phone' => 'string'
        ];
    }
}
