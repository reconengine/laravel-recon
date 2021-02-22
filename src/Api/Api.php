<?php

namespace LaravelMl\Api;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use LaravelMl\Exceptions\DatatypeMismatchException;
use LaravelMl\Helpers\InteractionBuilder;
use LaravelMl\LmlDatabaseConfig;
use LaravelMl\LmlItem;
use LaravelMl\LmlRecordConfig;

class Api
{
    const HOST = 'https://staging.laravelml.com/api';

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
    public function showDatabase()
    {
        return $this->http()->get(self::HOST . "/databases/{$this->config['database']}");
    }

    /**
     * @param LmlDatabaseConfig $config
     * @return mixed
     */
//    public function updateDatabase(LmlDatabaseConfig $config)
//    {
//        return $this->http()->put(self::HOST . "/databases/{$config->name()}", $config->toJson());
//    }

    /**
     * @param LmlDatabaseConfig $config
     * @return mixed
     */
    public function storeDatabase(LmlDatabaseConfig $config)
    {
        return $this->http()->post(self::HOST . "/databases", $config->toJson());
    }

    /**
     * @param LmlDatabaseConfig $config
     * @return mixed
     */
    public function deleteDatabase(LmlDatabaseConfig $config)
    {
        return $this->http()->delete(self::HOST . "/databases/{$config->name()}");
    }

    /**
     * @param $model
     * @param callable|null $progress
     * @return bool
     */
    public function syncDatabase($model, callable $progress = null)
    {
        $databaseName = $model->ml()->database()->name();
        $model::chunk(250, function (Collection $records) use ($databaseName, $progress) {
            $samples = $records->map(function ($record) {
                /**
                 * @var LmlItem $record
                 */
                if ($record->isTrainable()) {
                    return $record->ml()->toJson();
                }

                return null;
            })->filter();

            $response = $this->http()->post(self::HOST . "/databases/{$databaseName}/train", [
                'samples' => $samples->toArray(),
            ]);

            $response->throw();

            if ($progress) {
                $progress($records);
            }
        });

        return true;
    }

    /**
     * @param Model $model
     * @return \Illuminate\Http\Client\Response
     */
    public function putUser(Model $model)
    {
        return $this->http()->post(self::HOST . "/databases/{$this->database}/users", [
            'uid' => $model->id,
            'metadata' => $model->toLmlJson(),
        ]);
    }

    /**
     * @param Model $model
     * @return \Illuminate\Http\Client\Response
     */
    public function putItem(Model $model)
    {
        return $this->http()->post(self::HOST . "/databases/{$this->database}/items", [
            'iid' => $model->id,
            'metadata' => $model->toLmlJson(),
        ]);
    }

    /**
     * @param InteractionBuilder $interactionBuilder
     * @return \Illuminate\Http\Client\Response
     */
    public function putInteraction(InteractionBuilder $interactionBuilder)
    {
        return $this->http()->post(self::HOST . "/databases/{$this->database}/interactions", $interactionBuilder->toJson());
    }
//
//    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//    /// Records
//    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//    /**
//     * @param LmlRecordConfig $config
//     * @return mixed
//     * @throws DatatypeMismatchException
//     */
//    public function createRecord(LmlRecordConfig $config)
//    {
//        $config->validate();
//
//        return $this->http()->post(self::HOST . "/databases/{$config->database()->name()}/records", $config->toJson());
//    }
//
//    /**
//     * @param LmlRecordConfig $config
//     * @return mixed
//     * @throws DatatypeMismatchException
//     */
//    public function updateRecord(LmlRecordConfig $config)
//    {
//        $config->validate();
//
//        return $this->http()->put(self::HOST . "/databases/{$config->database()->name()}/records/{$config->networkId()}", $config->toJson());
//    }
//
//    /**
//     * @param LmlRecordConfig $config
//     * @return \Illuminate\Http\Client\Response
//     */
//    public function deleteRecord(LmlRecordConfig $config)
//    {
//        return $this->http()->delete(self::HOST . "/databases/{$config->database()->name()}/records/{$config->networkId()}");
//    }
//
//    /**
//     * @param LmlRecordConfig $config
//     * @return array|mixed
//     * @throws DatatypeMismatchException
//     * @throws \Illuminate\Http\Client\RequestException
//     */
//    public function predict(LmlRecordConfig $config)
//    {
//        $config->validate();
//
//        $response = $this->http()->post(self::HOST . "/databases/{$config->database()->name()}/predict", [
//            'samples' => [$config->features()],
//        ]);
//
//        $response->throw();
//
//        return $response->json();
//    }
//
//    /**
//     * @param LmlRecordConfig $config
//     * @return array|mixed
//     * @throws DatatypeMismatchException
//     * @throws \Illuminate\Http\Client\RequestException
//     */
//    public function associate(LmlRecordConfig $from, LmlRecordConfig $to, float $weight, string $description = null)
//    {
//        $from->validate();
//        $to->validate();
//
//        $response = $this->http()->post(self::HOST . "/databases/{$from->database()->name()}/records/{$from->networkId()}/associate/{$to->networkId()}", [
//            'weight' => $weight,
//            'description' => $description,
//        ]);
//
//        $response->throw();
//
//        return $response->json();
//    }
//
//    /**
//     * @param LmlRecordConfig $config
//     * @return array|mixed
//     * @throws DatatypeMismatchException
//     * @throws \Illuminate\Http\Client\RequestException
//     */
//    public function recommend(LmlRecordConfig $record, RecommendationRequest $request)
//    {
//        $record->validate();
//        // We should NOT validate $to. We only need that to get the class from it.
//
//        $response = $this->http()->post(self::HOST . "/databases/{$record->database()->name()}/records/{$record->networkId()}/recommend", $request->toArray());
//
//        $response->throw();
//
//        return $response->json();
//    }
//
//    /**
//     * @param LmlRecordConfig $config
//     * @return array|mixed
//     * @throws DatatypeMismatchException
//     * @throws \Illuminate\Http\Client\RequestException
//     */
//    public function similar(LmlRecordConfig $record)
//    {
//        // TODO:
//    }

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
