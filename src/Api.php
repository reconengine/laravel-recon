<?php

namespace LaravelMl;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class Api
{
    const HOST = 'https://staging.laravelml.com/api';

    /**
     * Api constructor.
     */
    public function __construct()
    {
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Models
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * @param string $name
     * @return mixed
     */
    public function showModel(string $name)
    {
        return $this->http()->get(self::HOST . "/models/{$name}");
    }

    /**
     * @param string $name
     * @param array $data
     * @return mixed
     */
    public function updateModel(string $name, array $data)
    {
        return $this->http()->put(self::HOST . "/models/{$name}", $data);
    }

    /**
     * @param string $name
     * @param array $data
     * @return mixed
     */
    public function storeModel(string $name, array $data)
    {
        return $this->http()->post(self::HOST . "/models", [
            'name' => $name,
            ] + $data);
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function deleteModel(string $name)
    {
        return $this->http()->delete(self::HOST . "/models/{$name}");
    }

    /**
     * @param $model
     * @param callable|null $progress
     * @return bool
     */
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
    /**
     * @param $model
     * @return mixed
     */
    public function createModelItem($model)
    {
        $model->ml()->validateItem();
        $model->ml()->validateData($model);
        $modelName = $model->ml()->name();

        return $this->http()->post(self::HOST . "/models/{$modelName}/items", $model->toMlJson());
    }

    /**
     * @param MlModel $modelItem
     * @return mixed
     */
    public function updateModelItem($modelItem)
    {
        $modelItem->ml()->validateItem();
        $modelItem->ml()->validateData($modelItem);
        $modelName = $modelItem->ml()->name();
        $modelItemIdentifier = $modelItem->ml()->id();

        return $this->http()->put(self::HOST . "/models/{$modelName}/items/{$modelItemIdentifier}", $modelItem->toMlJson());
    }

    /**
     * @param string $modelName
     * @param string $modelItemIdentifier
     * @return \Illuminate\Http\Client\Response
     */
    public function deleteModelItem($modelName, $modelItemIdentifier)
    {
        return $this->http()->delete(self::HOST . "/models/{$modelName}/items/{$modelItemIdentifier}");
    }

    /**
     * @param MlModel $modelItem
     * @param array $samples
     * @return array|mixed
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function predict($modelItem)
    {
        $modelItem->ml()->validateItem();
        $modelItem->ml()->validateData($modelItem);

        $modelName = $modelItem->ml()->name();

        $response = $this->http()->post(self::HOST . "/models/{$modelName}/predict", [
            'samples' => [$modelItem->features()],
        ]);

        $response->throw();

        return $response->json();
    }

    /**
     * @return \Illuminate\Http\Client\PendingRequest
     */
    protected function http()
    {
        return Http::withToken(config('laravel-ml.token'))
            ->withHeaders([
                'Accept' => 'application/json',
                'Content-type' => 'application/json',
            ]);
    }
}
