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
     * @param LmlDatabaseConfig $config
     * @return mixed
     */
    public function showDatabase(LmlDatabaseConfig $config)
    {
        return $this->http()->get(self::HOST . "/databases/{$config->name()}");
    }

    /**
     * @param LmlDatabaseConfig $config
     * @return mixed
     */
    public function updateDatabase(LmlDatabaseConfig $config)
    {
        return $this->http()->put(self::HOST . "/databases/{$config->name()}", $config->toJson());
    }

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
                 * @var LmlRecord $record
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

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Records
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * @param LmlRecordConfig $config
     * @return mixed
     * @throws Exceptions\DatatypeMismatchException
     */
    public function createRecord(LmlRecordConfig $config)
    {
        $config->validate();

        return $this->http()->post(self::HOST . "/databases/{$config->database()->name()}/records", $config->toJson());
    }

    /**
     * @param LmlRecordConfig $config
     * @return mixed
     * @throws Exceptions\DatatypeMismatchException
     */
    public function updateRecord(LmlRecordConfig $config)
    {
        $config->validate();

        return $this->http()->put(self::HOST . "/databases/{$config->database()->name()}/records/{$config->networkId()}", $config->toJson());
    }

    /**
     * @param LmlRecordConfig $config
     * @return \Illuminate\Http\Client\Response
     */
    public function deleteRecord(LmlRecordConfig $config)
    {
        return $this->http()->delete(self::HOST . "/databases/{$config->database()->name()}/records/{$config->networkId()}");
    }

    /**
     * @param LmlRecordConfig $config
     * @return array|mixed
     * @throws Exceptions\DatatypeMismatchException
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function predict(LmlRecordConfig $config)
    {
        $config->validate();

        $response = $this->http()->post(self::HOST . "/databases/{$config->database()->name()}/predict", [
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
