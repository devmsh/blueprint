<?php

namespace Blueprint\Generators;

use Blueprint\Contracts\Generator as GeneratorContract;
use Blueprint\Tree;

abstract class Generator implements GeneratorContract
{
    const INDENT = '        ';

    /** @var Tree */
    protected $tree;

    /** @var \Illuminate\Contracts\Filesystem\Filesystem */
    protected $files;

    /** @var array */
    protected $imports = [];

    /** @var string */
    protected $new_instance = 'new instance';

    public function __construct($files)
    {
        $this->files = $files;
    }

    protected function outputStub($class, $stub): string
    {
        $path = $this->getPath($class);

        if (!$this->files->exists(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0755, true);
        }

        $this->files->put($path, $this->populateStub($stub, $class));

        return $path;
    }

    protected function buildImports(string $className)
    {
        $imports = array_unique($this->imports[$className]);
        sort($imports);

        return implode(PHP_EOL, array_map(function ($class) {
            return 'use '.$class.';';
        }, $imports));
    }

    protected function addImport(string $className, $fullyQualifiedClassName)
    {
        $this->imports[$className][] = $fullyQualifiedClassName;
    }
}
