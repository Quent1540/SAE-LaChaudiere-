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

/* prestationsParCategorie.twig */
class __TwigTemplate_8c9f03573df9b49e2a4c459d002286d2 extends Template
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
    <title>Prestations de la catégorie</title>
</head>
<body>
    <h1>";
        // line 8
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["categorie"] ?? null), "libelle", [], "any", false, false, false, 8), "html", null, true);
        yield " — Prestations</h1>

    ";
        // line 10
        if ((($tmp =  !Twig\Extension\CoreExtension::testEmpty(CoreExtension::getAttribute($this->env, $this->source, ($context["categorie"] ?? null), "prestations", [], "any", false, false, false, 10))) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 11
            yield "        <ul>
            ";
            // line 12
            $context['_parent'] = $context;
            $context['_seq'] = CoreExtension::ensureTraversable(CoreExtension::getAttribute($this->env, $this->source, ($context["categorie"] ?? null), "prestations", [], "any", false, false, false, 12));
            foreach ($context['_seq'] as $context["_key"] => $context["prestation"]) {
                // line 13
                yield "                <li>
                    <strong>";
                // line 14
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["prestation"], "libelle", [], "any", false, false, false, 14), "html", null, true);
                yield "</strong><br>
                    ";
                // line 15
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["prestation"], "description", [], "any", false, false, false, 15), "html", null, true);
                yield "<br>
                    ";
                // line 16
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["prestation"], "tarif", [], "any", false, false, false, 16), "html", null, true);
                yield " € / ";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["prestation"], "unite", [], "any", false, false, false, 16), "html", null, true);
                yield "
                    <a href=\"/prestation?id=";
                // line 17
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["prestation"], "id", [], "any", false, false, false, 17), "html", null, true);
                yield "\">Détails</a>
                </li>
            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_key'], $context['prestation'], $context['_parent']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 20
            yield "            <br>
            <a href=\"/categorie/";
            // line 21
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["categorie"] ?? null), "id", [], "any", false, false, false, 21), "html", null, true);
            yield "\">Retour à la catégorie</a>
        </ul>
    ";
        } else {
            // line 24
            yield "        <p>Aucune prestation trouvée pour cette catégorie.</p>
    ";
        }
        // line 26
        yield "</body>
</html>";
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "prestationsParCategorie.twig";
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
        return array (  104 => 26,  100 => 24,  94 => 21,  91 => 20,  82 => 17,  76 => 16,  72 => 15,  68 => 14,  65 => 13,  61 => 12,  58 => 11,  56 => 10,  51 => 8,  42 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "prestationsParCategorie.twig", "C:\\xampp\\htdocs\\Projet_Giftbox\\giftAppli\\src\\views\\prestationsParCategorie.twig");
    }
}
