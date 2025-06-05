<?php
namespace gift\appli\application_core\domain\entities;

use Illuminate\Database\Eloquent\Model;

class Theme extends Model{
    protected $table = 'theme';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function coffrets(){
        return $this->hasMany(CoffretType::class, 'theme_id');
    }
}