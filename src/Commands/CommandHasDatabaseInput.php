<?php


namespace LaravelMl\Commands;


trait CommandHasDatabaseInput
{
    /**
     * @return string|null
     */
    protected function getCurrentDatabase()
    {
        return $this->option('database') ?? config('laravel-ml.database');
    }
}
