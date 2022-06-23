<?php

namespace App\Models;

use CodeIgniter\Model;

class Hora extends Model
{
    protected $table = 'hora_marcado';
    protected $allowedFields = ['id_persona_fecha_marcado', 'id_biometrico', 'hora', 'tipo'];
}
