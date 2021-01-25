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
     * @param MlModelConfig $config
     * @return mixed
     */
    public function showModel(MlModelConfig $config)
    {
        return $this->http()->get(self::HOST . "/models/{$config->name()}");
    }

    /**
     * @param string $name
     * @param array $data
     * @return mixed
     */
    public function updateModel(MlModelConfig $config)
    {
        return $this->http()->put(self::HOST . "/models/{$config->name()}", $config->toArray());
    }

    /**
     * @param MlModelConfig $config
     * @return mixed
     */
    public function storeModel(MlModelConfig $config)
    {
        return $this->http()->post(self::HOST . "/models", $config->toArray());
    }

    /**
     * @param MlModelConfig $config
     * @return mixed
     */
    public function deleteModel(MlModelConfig $config)
    {
        return $this->http()->delete(self::HOST . "/models/{$config->name()}");
    }

    /**
     * @param $model
     * @param callable|null $progress
     * @return bool
     */
    public function syncModel($model, callable $progress = null)
    {
        $modelName = $model->ml()->name();
        $model::chunk(250, function (Collection $modelItems) use ($modelName, $progress) {
            $modelJson = $modelItems->map(function ($model) {
                /**
                 * @var MlModel $model
                 */
                if ($model->isTrainable()) {
                    return $model->ml()->toMlJson();
                }

                return null;
            })->filter();

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
    public function createModelItem(MlModelConfig $config)
    {
        $config->validateItem();
        $config->validateData();

        return $this->http()->post(self::HOST . "/models/{$config->name()}/items", $config->toMlJson());
    }

    /**
     * @param MlModel $modelItem
     * @return mixed
     */
    public function updateModelItem(MlModelConfig $config)
    {
        $config->validateItem();
        $config->validateData();

        return $this->http()->put(self::HOST . "/models/{$config->name()}/items/{$config->id()}", $config->toMlJson());
    }

    /**
     * @param string $modelName
     * @param string $modelItemIdentifier
     * @return \Illuminate\Http\Client\Response
     */
    public function deleteModelItem(MlModelConfig $config)
    {
        return $this->http()->delete(self::HOST . "/models/{$config->name()}/items/{$config->id()}");
    }

    /**
     * @param MlModel $modelItem
     * @param array $samples
     * @return array|mixed
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function predict(MlModelConfig $config)
    {
        $config->validateItem();
        $config->validateData();

        $response = $this->http()->post(self::HOST . "/models/{$config->name()}/predict", [
            'samples' => [$config->features()],
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
