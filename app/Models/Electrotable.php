<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Model;

class Electrotable extends Model
{
    protected $table = 'electrotable';
    public $timestamps = false;
    protected $fillable = [
        'IDColegio', 'CodigoColegio', 'DescripcionColegio', 'TieneCupo', 'CantidadInscritos', 'CantidadReservada', 'IdRecinto', 'CodigoRecinto', 'DescripcionRecinto', 'DireccionRecinto', 'EstatusRecinto', 'IdCircunscripcion', 'DescripcionCircunscripcion', 'IdProvincia', 'DescripcionProvincia', 'Zona', 'codigocircunscripcion', 'IdMunicipio', 'DescripcionMunicipio', 'IdDistritoMunicipal', 'DescripcionDistritoMunicipal', 'IdCiudadSeccion', 'CodigoCiudad', 'DescripcionCiudad', 'IdSectorParaje', 'CodigoSector', 'DescripcionSector', 'EstatusSector'
    ];
}
