<?php
namespace lachaudiere\application_core\domain\entities;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ImagesEvenement extends Model
{
    protected $table = 'images_evenements';
    protected $primaryKey = 'id_image';
    public $timestamps = false;
    protected $fillable = [
        'id_image',
        'id_evenement',
        'url_image',
        'legende',
        'ordre_affichage',
    ];
}