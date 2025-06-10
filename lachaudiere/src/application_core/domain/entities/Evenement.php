<?php
namespace lachaudiere\application_core\domain\entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evenement extends Model {
    protected $table = 'evenements';
    protected $primaryKey = 'id_evenement';
    public $timestamps = false;

    public function categorie() {
        return $this->belongsTo(Categorie::class, 'id_categorie');
    }

    public function images() {
        return $this->hasOne(ImagesEvenement::class, 'id_evenement');
    }
}


