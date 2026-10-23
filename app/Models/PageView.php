<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * One unique visitor per path per day. visited_on is kept as a plain Y-m-d string.
 */
#[Fillable(['path', 'visited_on', 'visitor_hash'])]
class PageView extends Model
{
    //
}
