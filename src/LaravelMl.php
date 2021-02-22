<?php

namespace LaravelMl;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class LaravelMl
{
    public function __construct()
    {
    }

    /**
     * @return \Illuminate\Support\Collection
     * @throws \ReflectionException
     */
    public function detectDatabases()
    {
        $path = $this->detectDatabasePath();
        $namespace = $this->detectDatabaseNamespace();

        $files = File::allFiles($path);
        $foundClasses = collect();
        foreach ($files as $file) {
            if (File::isFile($file)) {
                $modelClass = $namespace . '\\' . $file->getFilenameWithoutExtension();

                try {
                    $traits = class_uses($modelClass);

                    if (! in_array(LmlItem::class, $traits)) {
                        continue;
                    }

                    $reflectedClass = new \ReflectionClass($modelClass);

                    if ($reflectedClass->isAbstract()) {
                        continue;
                    }

                    $foundClasses->push($modelClass);
                } catch (\ErrorException $errorException) {
                    // often times, a file is detected, but isn't a model.
                }
            }
        }

        return $foundClasses;
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function detectDatabasePath()
    {
        $path = app_path('Models');
        if (! File::isDirectory($path)) {
            $path = app_path();
        }

        if (! File::isDirectory($path)) {
            throw new \Exception('Failed to find models.');
        }

        return $path;
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function detectDatabaseNamespace()
    {
        if (File::isDirectory(app_path('Models'))) {
            return 'App\\Models';
        }

        return 'App';
    }
}
