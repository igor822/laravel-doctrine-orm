<?php

if (!function_exists('storage_path')) {
    function storage_path($path = null)
    {
        return __DIR__ . DIRECTORY_SEPARATOR . '../../../tests/Stubs/storage';
    }
}
if (!function_exists('env')) {
    function env($var, $default = null)
    {
        return $default;
    }
}
if (!function_exists('config')) {
    function config($var)
    {
        return $var;
    }
}
if (!function_exists('app_path')) {
    function app_path($path = null)
    {
        return __DIR__ . $path;
    }
}

abstract class GeneratorBase extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (is_dir(__DIR__ . '/../../Stubs/storage/generator')) {
            $this->rrmdir(__DIR__ . '/../../Stubs/storage/generator');
        }
    }

    protected function rrmdir($dir)
    {
        foreach (glob($dir . '/*') as $file) {
            if (is_dir($file)) {
                $this->rrmdir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dir);
    }
}
