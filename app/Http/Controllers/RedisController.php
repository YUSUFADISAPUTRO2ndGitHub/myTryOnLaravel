<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class RedisController extends Controller
{

    public $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function show($id)
    {
        return Redis::get($id);
    }

    public function showAll()
    {
        return Redis::key('*');
    }

    public function create($key, $value)
    {
        return Redis::set($key, $value);
    }

    public function getConsignment()
    {
        return $this->name;
    }
}
