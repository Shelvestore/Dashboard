<?php

namespace App\Models\Web;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compare extends Model
{
    use HasFactory;
    protected $fillable = ['product_id', 'customer_id'];

    public function products()
    {
        return $this->belongsTo('App\Models\Admin\Product', 'product_id', 'id');
    }

    public function ScopeCustomerId($query, $id)
    {

        $query->where('customer_id', $id);
    }

    public function ScopeCompareId($query, $id)
    {

        $query->where('id', $id);
    }
}
