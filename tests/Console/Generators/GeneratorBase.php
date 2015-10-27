<?php

if (!function_exists('storage_path')) {
    function storage_path($path = null)
    {
        return __DIR__ . DIRECTORY_SEPARATOR . '../../../tests/Stubs/storage';
    }
}

abstract class GeneratorBase extends PHPUnit_Framework_TestCase
{
    protected $entityDir;
    protected $mappingDir;

    protected function setUp()
    {
        if (is_dir(__DIR__ . '/../../Stubs/storage/generator/entity')) {
            $this->rrmdir(__DIR__ . '/../../Stubs/storage/generator/entity');
        }
        if (is_dir(__DIR__ . '/../../Stubs/storage/generator/mapping')) {
            $this->rrmdir(__DIR__ . '/../../Stubs/storage/generator/mapping');
        }

        $this->entityDir = realpath(__DIR__ . '/../../Stubs/storage') . DIRECTORY_SEPARATOR . 'generator/entity';
        $this->mappingDir = realpath(__DIR__ . '/../../Stubs/storage') . DIRECTORY_SEPARATOR . 'generator/mapping';
    }

    /**
     * Remove a directory and all contents inside of it
     * @param $dir
     */
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


    /**
     * Get the namespace from a source file
     * @param $src string Source file as a string
     *
     * @return null|string
     */
    protected function getNamespace ($src) {
        $tokens = token_get_all($src);
        $count = count($tokens);
        $i = 0;
        $namespace = '';
        $namespace_ok = false;
        while ($i < $count) {
            $token = $tokens[$i];
            if (is_array($token) && $token[0] === T_NAMESPACE) {
                // Found namespace declaration
                while (++$i < $count) {
                    if ($tokens[$i] === ';') {
                        $namespace_ok = true;
                        $namespace = trim($namespace);
                        break;
                    }
                    $namespace .= is_array($tokens[$i]) ? $tokens[$i][1] : $tokens[$i];
                }
                break;
            }
            $i++;
        }
        if (!$namespace_ok) {
            return null;
        } else {
            return $namespace;
        }
    }
}
