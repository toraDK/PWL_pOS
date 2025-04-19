<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StokModel extends Model
{
    use HasFactory;
    protected $table = 't_stok';
    protected $primaryKey = 'stok_id';

    protected $fillable = ['supplier_id', 'barang_id', 'user_id', 'stok_tanggal', 'stok_jumlah'];

    public function Supplier(): BelongsTo{
        return $this->BelongsTo(SupplierModel::class, 'supplier_id', 'supplier_id');
    }

    public function Barang(): BelongsTo{
        return $this->BelongsTo(BarangModel::class, 'barang_id', 'barang_id');
    }

    public function User(): BelongsTo{
        return $this->BelongsTo(UserModel::class, 'user_id', 'user_id');
    }
}
