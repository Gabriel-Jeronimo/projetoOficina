<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClienteFisico extends Model
{

    public $primaryKey = 'IdFisico';
    public $timestamps = false;
    protected $table = 'clientefisico';

    protected $fillable = ['IdCliente', 'Rg', 'Cpf'];


}
