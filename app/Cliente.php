<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Cliente extends Model
{
    public $primaryKey = 'IdCliente';
    public $timestamps = false;

    protected $fillable = ['IdCliente', 'Nome', 'IdEndereco'];

    public function Carros(){
        return $this->hasMany(Carro::class, 'Proprietario', 'IdCliente');
    }

    public function Endereco(){
        return $this->HasOne(Endereco::class, 'IdEndereco', 'IdEndereco');
    }

    public function Telefones(){
        return $this->hasMany(Telefone::class, 'Cliente', 'IdCliente');
    }

    public function Juridico(){
        return $this->belongsTo(ClienteJuridico::class, 'IdCliente', 'IdCliente');
    }

    public function Fisico(){
        return $this->belongsTo(ClienteFisico::class, 'IdCliente', 'IdCliente');
    }
}
