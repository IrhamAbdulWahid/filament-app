<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangModel extends Model
{
    use HasFactory;

    protected $table = 'barang';  // Jika nama tabel berbeda, sesuaikan.

    protected $guarded = [];  // Sesuaikan dengan kebutuhan.

    public function fakturDetails()
    {
        return $this->hasMany(DetailFakturModel::class, 'barang_id');
    }
}
