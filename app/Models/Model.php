<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use KhanhArtisan\LaravelBackbone\ModelListener\ObservableModel;

abstract class Model extends \Illuminate\Database\Eloquent\Model implements ObservableModel
{
    use HasUlids;
}