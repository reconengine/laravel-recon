<?php

namespace LaravelMl\Api;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use LaravelMl\Exceptions\DatatypeMismatchException;
use LaravelMl\Helpers\InteractionBuilder;
use LaravelMl\Helpers\SchemaDefinition;
use LaravelMl\LmlItem;

class Api
{
    const HOST = 'http://localhost:8000/api';//'https://staging.laravelml.com/api';

    protected $database = '';
    protected $config = [];

    /**
     * Api constructor.
     */
    public function __construct()
    {
        $this->config = config('laravel-ml');
        $this->database = config('laravel-ml.database');
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Models
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * @return mixed
     */
    public function getDatabases()
    {
        return $this->http()->get(self::HOST . "/databases")->throw();
    }


    /**
     * @param string $name
     * @param SchemaDefinition $userSchema
     * @param SchemaDefinition $itemSchema
     * @return mixed
     */
    public function storeDatabase(string $name, SchemaDefinition $userSchema, SchemaDefinition $itemSchema)
    {
        return $this->http()->post(self::HOST . "/databases", [
            'name' => $name,
            'user_schema' => $userSchema->toJson(),
            'item_schema' => $itemSchema->toJson(),
        ])->throw();
    }

    /**
     * @param string $name
     * @param callable|null $progress
     * @return \Illuminate\Http\Client\Response
     */
    public function updateDatabase(string $name, array $data)
    {
        return $this->http()->put(self::HOST . "/databases/{$name}", $data)->throw();
    }

    /**
     * @param string $name
     * @param callable|null $progress
     * @return \Illuminate\Http\Client\Response
     */
    public function retrainDatabase(string $name)
    {
        return $this->http()->put(self::HOST . "/databases/{$name}", [
            'retrain' => true,
        ])->throw();
    }

    /**
     * @param Model $model
     * @return \Illuminate\Http\Client\Response
     */
    public function putUsers($model)
    {
        $models = $model instanceof Model ? collect([$model]) : $model;

        $models = $models->filter->isTrainable();
        $modelsRawJson = $models->map(function ($model) {
            return [
                'uid' => $model->id,
                'metadata' => $model->toLmlJson(),
            ];
        });

        return $this->http()->post(self::HOST . "/databases/{$this->database}/users", [
            'users' => $modelsRawJson->toArray()
        ])->throw();
    }

    /**
     * @param Model|collection $model
     * @return \Illuminate\Http\Client\Response
     */
    public function putItems($model)
    {
        $models = $model instanceof Model ? collect([$model]) : $model;

        $models = $models->filter->isTrainable();
        $modelsRawJson = $models->map(function ($model) {
            return [
                'iid' => $model->id,
                'metadata' => $model->toLmlJson(),
            ];
        });

        return $this->http()->post(self::HOST . "/databases/{$this->database}/items", [
            'items' => $modelsRawJson->toArray()
        ])->throw();
    }

    /**
     * @param InteractionBuilder $interactionBuilder
     * @return \Illuminate\Http\Client\Response
     */
    public function putInteractions(InteractionBuilder $interactionBuilder)
    {
        return $this->http()->post(self::HOST . "/databases/{$this->database}/interactions", [
            'interactions' => $interactionBuilder->toJson(),
        ])->throw();
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
