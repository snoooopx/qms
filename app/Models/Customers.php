<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customers extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'customers';

    /**
     * Get the country record associated with the user.
     */
    public function country()
    {
        return $this->hasOne('Models\Countries', 'id', 'country_id');
    }
}
