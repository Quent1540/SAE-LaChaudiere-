<?php
namespace lachaudiere\application_core\application\useCases;

use lachaudiere\application_core\domain\entities\Categorie;

class CategoriesService implements CategoriesServiceInterface
{
    public function getCategories(): \Illuminate\Database\Eloquent\Collection
    {
        return Categorie::all();
    }
}