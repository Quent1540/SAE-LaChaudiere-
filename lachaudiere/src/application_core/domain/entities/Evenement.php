<?php
namespace lachaudiere\application_core\domain\entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evenement extends Model {
    protected $table = 'evenements';
    protected $primaryKey = 'id_evenement';
    public $timestamps = false;

    protected $fillable = [
        'titre',
        'description',
        'tarif',
        'date_debut',
        'date_fin',
        'id_categorie',
        'est_publie',
        'id_utilisateur_creation',
    ];

    public function categorie() {
        return $this->belongsTo(Categorie::class, 'id_categorie');
    }

    public function images() {
        return $this->hasMany(ImagesEvenement::class, 'id_evenement');
    }
}


