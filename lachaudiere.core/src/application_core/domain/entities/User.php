<?php
namespace lachaudiere\application_core\domain\entities;

use Illuminate\Database\Eloquent\Model;

class User extends Model {
    protected $table = 'utilisateurs';
    protected $primaryKey = 'id_utilisateur'; 

    public $incrementing = true;

    public $timestamps = false;

    protected $fillable = [
        'email',
        'mot_de_passe_hash',
        'role'
    ];

    protected $hidden = [
        'mot_de_passe_hash',
    ];
}