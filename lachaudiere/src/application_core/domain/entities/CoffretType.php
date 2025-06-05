<?php
namespace gift\appli\application_core\domain\entities;

use Illuminate\Database\Eloquent\Model;

class CoffretType extends Model{
    protected $table = 'coffret_type';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function theme(){
        return $this->belongsTo(Theme::class, 'theme_id');
    }

    public function prestations(){
        return $this->belongsToMany(
            Prestation::class,
            'coffret2presta',
            'coffret_id',
            'presta_id'
        );
    }
}