<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* first.html.twig */
class __TwigTemplate_5abd47d96aee629f6c9efdb2b3210a4a410538b9c6ef7aadfa005072a23a2f40 extends \Twig\Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        echo "
Hello and number is ";
        // line 2
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('square')->getCallable(), [($context["num"] ?? null)]), "html", null, true);
    }

    public function getTemplateName()
    {
        return "first.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  40 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "first.html.twig", "C:\\ai021\\www\\framework\\tests\\views\\first.html.twig");
    }
}
