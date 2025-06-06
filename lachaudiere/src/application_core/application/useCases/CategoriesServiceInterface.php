<?php
namespace lachaudiere\application_core\application\useCases;

use lachaudiere\application_core\domain\entities\Categorie;

interface CategoriesServiceInterface
{
    public function getCategories(): \Illuminate\Database\Eloquent\Collection;
}