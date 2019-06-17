<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageLog extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'message_log';

    protected $guarded = ['id'];
}
