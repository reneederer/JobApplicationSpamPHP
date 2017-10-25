<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    public $id = 0;
    public $name = "";

    public function __construct($id, $name)
    {
        $this->id = $id;
        $this->name = $name;
    }
}
