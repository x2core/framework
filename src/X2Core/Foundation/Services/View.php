<?php

namespace X2Core\Foundation\Services;

use Twig\Environment;
use Twig\Extension\ExtensionInterface;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Class View
 * @package X2Core\Foundation\Service
 */
class View
{
    /**
     * @var Environment
     */
    private $engine;

    /**
     * @var string
     */
    private $suffix;

    /**
     * View constructor.
     * @param $pathViews
     * @param $pathViewsCache
     */
    public function __construct($pathViews, $pathViewsCache)
    {
        $this->loader = new FilesystemLoader($pathViews);
        $this->engine = new Environment($this->loader, [
            'cache' => $pathViewsCache,
        ]);
    }

    /**
     * @param $path
     * @param bool $prepend
     */
    public function registerDirectory($path, $prepend = false){
       $prepend ? $this->loader->prependPath($path) : $this->loader->addPath($path);
    }

    /**
     * @param ExtensionInterface $obj
     */
    public function extend(ExtensionInterface $obj){
        $this->engine->addExtension($obj);
    }

    /**
     * @param array $arr
     */
    public function publishVars(array $arr){
        /** @var mixed $value */
        foreach ($arr as $key => $value){
            $this->engine->addGlobal($key, $value);
        }
    }

    /**
     * @param $name
     * @param callable $pipe
     */
    public function setPipeFilter($name, callable $pipe){
        $this->engine->addFilter(new TwigFilter($name, $pipe));
    }

    /**
     * @param $name
     * @param callable $fn
     */
    public function createFunction($name, callable  $fn){
        $this->engine->addFunction(new TwigFunction($name, $fn));
    }

    /**
     * @return Environment
     */
    public function getEngine()
    {
        return $this->engine;
    }

    /**
     * @param $name
     * @param $data
     * @return string
     */
    public function render($name, $data){
        return $this->engine->render($name . $this->suffix, $data);
    }

    /**
     * @return string
     */
    public function getSuffix(): string
    {
        return $this->suffix;
    }

    /**
     * @param string $suffix
     */
    public function setSuffix(string $suffix)
    {
        $this->suffix = $suffix;
    }
}