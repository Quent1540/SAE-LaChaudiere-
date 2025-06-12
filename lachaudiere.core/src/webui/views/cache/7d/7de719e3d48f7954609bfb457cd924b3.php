<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\CoreExtension;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;
use Twig\TemplateWrapper;

/* categorieParId.twig */
class __TwigTemplate_81ee2b9103c87dfde98f5ecae39dfcff extends Template
{
    private Source $source;
    /**
     * @var array<string, Template>
     */
    private array $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 1
        yield "<!DOCTYPE html>
<html lang=\"fr\">
<head>
    <meta charset=\"UTF-8\">
    <title>Détail Catégorie</title>
</head>
<body>
    <h1>Détail de la catégorie</h1>
    <p><strong>ID :</strong> ";
        // line 9
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["categorie"] ?? null), "id", [], "any", false, false, false, 9), "html", null, true);
        yield "</p>
    <p><strong>Libellé :</strong> ";
        // line 10
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["categorie"] ?? null), "libelle", [], "any", false, false, false, 10), "html", null, true);
        yield "</p>
    <p><strong>Description :</strong> ";
        // line 11
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["categorie"] ?? null), "description", [], "any", false, false, false, 11), "html", null, true);
        yield "</p>
    <a href=\"/categories/";
        // line 12
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["categorie"] ?? null), "id", [], "any", false, false, false, 12), "html", null, true);
        yield "/prestations\">Voir les prestations</a>
    <br><br>
    <a href=\"/categories\">Retour à la liste des catégories</a>
</body>
</html>";
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "categorieParId.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable(): bool
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo(): array
    {
        return array (  64 => 12,  60 => 11,  56 => 10,  52 => 9,  42 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "categorieParId.twig", "C:\\xampp\\htdocs\\Projet_Giftbox\\giftAppli\\src\\views\\categorieParId.twig");
    }
}
