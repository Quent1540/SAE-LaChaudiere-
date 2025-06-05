<?php
namespace gift\appli\application_core\domain\entities;
use Illuminate\Database\Eloquent\Model;

class User extends Model {
    protected $table = 'user';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['id', 'user_id', 'password', 'role'];
}