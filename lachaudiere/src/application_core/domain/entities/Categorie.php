<?php
namespace lachaudiere\application_core\domain\entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categorie extends Model {
    protected $table = 'categories';
    protected $primaryKey = 'id_categorie';
    public $timestamps = false;

    protected $fillable = ['libelle', 'description'];

    public function evenements(): HasMany {
        return $this->hasMany(Evenement::class, 'id_categorie');
    }
}
