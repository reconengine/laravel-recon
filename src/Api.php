<?php

namespace LaravelMl;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class Api
{
    const HOST = 'https://staging.laravelml.com/api';

    public function __construct()
    {
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Models
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function showModel(string $name)
    {
        return $this->http()->get(self::HOST . "/models/{$name}");
    }

    public function updateModel(string $name, array $data)
    {
        return $this->http()->put(self::HOST . "/models/{$name}", $data);
    }

    public function storeModel(string $name, array $data)
    {
        return $this->http()->post(self::HOST . "/models", [
            'name' => $name,
            ] + $data);
    }

    public function deleteModel(string $name)
    {
        return $this->http()->delete(self::HOST . "/models/{$name}");
    }

    public function syncModel($model, callable $progress = null)
    {
        $modelName = $model->ml()->name();
        $model::chunk(5000, function (Collection $modelItems) use ($modelName, $progress) {
            $modelJson = $modelItems->map(function ($model) {
                return $model->toMlJson();
            });

            $response = $this->http()->post(self::HOST . "/models/{$modelName}/train", [
                'samples' => $modelJson->toArray(),
            ]);

            $response->throw();

            if ($progress) {
                $progress($modelItems);
            }
        });

        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Model Items
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function createModelItem($model)
    {
        $modelName = $model->ml()->name();

        return $this->http()->post(self::HOST . "/models/{$modelName}/items", $model->toMlJson());
    }

    public function updateModelItem($modelItem)
    {
        $modelName = $modelItem->ml()->name();
        $modelItemIdentifier = $modelItem->ml()->id();

        return $this->http()->put(self::HOST . "/models/{$modelName}/items/{$modelItemIdentifier}", $modelItem->toMlJson());
    }

    public function deleteModelItem($modelName, $modelItemIdentifier)
    {
        return $this->http()->delete(self::HOST . "/models/{$modelName}/items/{$modelItemIdentifier}");
    }

    protected function http()
    {
        return Http::withToken(config('laravel-ml.token'))
            ->withHeaders([
                'Accept' => 'application/json',
                'Content-type' => 'application/json',
            ]);
    }
}
