<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use KhanhArtisan\LaravelBackbone\ModelListener\ObservableModel;

abstract class Pivot extends \Illuminate\Database\Eloquent\Relations\Pivot implements ObservableModel
{
    use HasUlids;
}