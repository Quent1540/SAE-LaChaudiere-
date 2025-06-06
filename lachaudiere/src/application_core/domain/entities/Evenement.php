<?php
namespace lachaudiere\application_core\domain\entities;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Evenement extends Model
{
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
}