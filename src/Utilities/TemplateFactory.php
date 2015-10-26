<?php


namespace LaravelDoctrine\ORM\Utilities;

use Illuminate\Container\Container as Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\FileViewFinder;

class TemplateFactory
{
    /**
     * @param string $templateDir The directory where templates to be used are location
     *
     * @return \Illuminate\View\Factory
     */
    public static function createViewFactory($templateDir)
    {
        $FileViewFinder = new FileViewFinder(
            new Filesystem(),
            [$templateDir]
        );

        $dispatcher = new Dispatcher(new Container());

        $compiler       = new BladeCompiler(new Filesystem(), storage_path() . '/framework/views');
        $bladeEngine    = new CompilerEngine($compiler);
        $engineResolver = new EngineResolver();
        $engineResolver->register('blade', function () use (&$bladeEngine) {
            return $bladeEngine;
        });

        $viewFactory = new \Illuminate\View\Factory($engineResolver, $FileViewFinder, $dispatcher);

        return $viewFactory;
    }
}
