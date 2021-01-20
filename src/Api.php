<?php

namespace LaravelMl;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class Api
{
    const HOST = 'https://test.com/api';

    public function __construct()
    {
    }

    public function showModel(string $name)
    {
        return Http::withToken(config('laravel-ml.token'))->get(self::HOST . "/models/{$name}");
    }

    public function updateModel(string $name, array $data)
    {
        return Http::withToken(config('laravel-ml.token'))->put(self::HOST . "/models/{$name}", $data);
    }

    public function storeModel(string $name, array $data)
    {
        return Http::withToken(config('laravel-ml.token'))->post(self::HOST . "/models/{$name}", $data);
    }

    public function createModelItem($model)
    {
        $modelName = $model->getMlName();

        return Http::withToken(config('laravel-ml.token'))
            ->post(self::HOST . "/models/{$modelName}/items", $model->toMlJson());
    }

    public function updateModelItem($modelItem)
    {
        $modelName = $modelItem->getMlName();
        $modelItemIdentifier = $modelItem->getMlId();

        return Http::withToken(config('laravel-ml.token'))
            ->put(self::HOST . "/models/{$modelName}/items/{$modelItemIdentifier}", $modelItem->toMlJson());
    }

    public function deleteModelItem($modelName, $modelItemIdentifier)
    {
        return Http::withToken(config('laravel-ml.token'))
            ->delete(self::HOST . "/models/{$modelName}/items/{$modelItemIdentifier}");
    }

    public function train($models)
    {
        $models = is_array($models) ? $models : [$models];
        $models = collect($models);

        if ($models->isEmpty()) {
            return null;
        }

        $modelInstance = $models->first();
        $modelName = $modelInstance->getMlName();
        $modelJson = $models->map(function ($model) {
            return $model->toMlJson();
        });

        return Http::withToken(config('laravel-ml.token'))->post(self::HOST . "/models/{$modelName}/train", [
            'samples' => $modelJson->toArray(),
        ]);
    }
}
