<?php

namespace Blueprint\Generators;

use Blueprint\Tree;

class SeederGenerator extends Generator
{
    public function output(Tree $tree): array
    {
        $this->tree = $tree;

        if (empty($tree->seeders())) {
            return [];
        }

        $output = [];

        $stub = $this->files->stub('seeder.stub');

        foreach ($tree->seeders() as $model) {
            $path = $this->getPath($model);
            $this->files->put($path, $this->populateStub($stub, $model));

            $output['created'][] = $path;
        }

        return $output;
    }

    public function types(): array
    {
        return ['seeders'];
    }

    protected function populateStub(string $stub, string $model)
    {
        $stub = str_replace('{{ class }}', $this->getClassName($model), $stub);
        $stub = str_replace('{{ body }}', $this->build($model), $stub);

        return $stub;
    }

    protected function getClassName(string $model)
    {
        return $model.'Seeder';
    }

    protected function build(string $model)
    {
        return sprintf('factory(\\%s::class, 5)->create();', $this->tree->fqcnForContext($model));
    }

    private function getPath($model)
    {
        return 'database/seeds/'.$model.'Seeder.php';
    }
}
