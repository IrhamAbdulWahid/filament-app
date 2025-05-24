<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FakturModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'faktur';

    protected $fillable = [
        'kode_faktur',
        'tanggal_faktur',
        'kode_customer',
        'customer_id',
        'ket_faktur',
        'total',
        'nominal_charge',
        'charge',
        'total_final',
    ];

    // Relasi ke CustomerModel
    public function customer()
    {
        return $this->belongsTo(CustomerModel::class);
    }

    // Relasi ke DetailFakturModel
    public function detail()
    {
        return $this->hasMany(DetailFakturModel::class, 'faktur_id');
    }

    // Method biasa untuk qualifications
    public function qualifications()
    {
        // Misalnya, mengembalikan data khusus terkait qualifications dari suatu sumber atau menghitung sesuatu
        return 'This is a placeholder for qualifications logic';
    }
}
