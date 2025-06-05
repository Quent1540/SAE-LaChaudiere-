<?php
namespace lachaudiere\application_core\domain\entities;

use Illuminate\Database\Eloquent\Model;

class User extends Model {
    protected $table = 'utilisateurs';
    protected $primaryKey = 'id_utilisateur';

    public $incrementing = false;
    protected $keyType = 'string'; 

    public $timestamps = false;

}