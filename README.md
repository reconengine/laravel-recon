# Laravel ML [beta]: Machine Learning predictions brought to Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/exzachlyvv/laravel-ml.svg?style=flat-square)](https://packagist.org/packages/exzachlyvv/laravel-ml)
[![Build Status](https://img.shields.io/travis/exzachlyvv/laravel-ml/master.svg?style=flat-square)](https://travis-ci.org/exzachlyvv/laravel-ml)
[![Quality Score](https://img.shields.io/scrutinizer/g/exzachlyvv/laravel-ml.svg?style=flat-square)](https://scrutinizer-ci.com/g/exzachlyvv/laravel-ml)
[![Total Downloads](https://img.shields.io/packagist/dt/exzachlyvv/laravel-ml.svg?style=flat-square)](https://packagist.org/packages/exzachlyvv/laravel-ml)

Add support for continuous, categorical, and anomaly machine learning model prediction in your Laravel applications using Eloquent models.

## Installation

Install package via composer:

```bash
composer require exzachlyvv/laravel-ml
```

## Usage

1. Add the `MlModel` trait to the Eloquent models you want to get predictions from.
1. Implement the abstract methods: `features()`, `label()`, and `config()`.
1. Models using the `MlModel` trait will automatically synchronize with your machine learning model on
[Laravel ML](https://laravelml.com) when they are created, updated, and deleted.
1. Use the `php artisan ml` command to manage your machine learning model on [Laravel ML](https://laravelml.com).

```php
class YourModel extends Model
{
    use MlModel;

    public function features(): array
    {
        return [
            $this->age,
            $this->experience,
            $this->education,
        ];
    }

    public function label()
    {
        return $this->salary;
    }

    protected function config(MlModelConfig $config)
    {
        return $config
            ->setName('unique_name_for_your_machine_learning_model')
            ->setId($this->id) // identifier for each sample
            ->setType(MlModelConfig::TYPE_CONTINUOUS) // the model type you want.
            ->setDatatype(MlModelConfig::DATATYPE_CONTINUOUS) // the datatype you are importing.
        ;
    }
}
```

To get predictions:
```php
$prediction = $yourModel->predict();
```

## Configuration

1. Machine learning model configuration is managed through the `MlModelConfig` object, via the `config()` method.

| Property      | Required | Notes |
| ----------- | ----------- | ----- |
| `type`      | Yes       | The type of model you want to get predictions from. See [Models](#models) for more. |
| `datatype` | Yes        | The type of data are you returning in the `features()` method. See [Datatypes](#datatypes) for more. |
| `name` | Yes        | This is the name of your machine learning model. Each model on your account must have a unique name. |
| `id` | No        | Identifier for your models. This is used to track them in [Laravel ML](https://laravelml.com). Default: `$this->id` |

## Models

What type of predictions do you want? These should align with the value you are returning from the `label()` method.

| Type      | Values | Notes |
| ----------- | ----------- | ----- |
| `continuous` | 392.123 | |
| `categorical` | "yes", "no" | |
| `anomaly` | 1 = anomaly or 0 = not anomaly | `label()` result is not used. |

## Datatypes

What type of data will your feed your machine learning model? These should align with the values you are returning from the `features()` method.

| Type      | Values | Notes |
| ----------- | ----------- | ----- |
| `continuous` | Numeric | Ex: number of bedrooms, height, test score |
| `categorical` | String | Ex: "green"/"red"/"blue", "Yes"/"No", "Coffee"/"Hot Dog" |
| `mixed` | Numeric + String | Not currently supported. |

**Warning: Anomaly type with Categorical datatype is currently not supported.** 

## Importing Existing Data

Newly create Eloquent records will automatically be imported. However if you want to import existing data into the
machine learning model, please use `php artisan ml` command and select the `Sync` option for your model.

## Roadmap

1. Support for clustering models (unsupervised + categorical labeling), used for grouping.
1. Recommendation systems using Eloquent relations.
1. Automatic model versioning.

### Demo

A live demo can be found at [demo.laravelml.com](https://demo.laravelml.com), source code [here](https://github.com/exzachlyvv/demo.laravelml.com).

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Zachary Vander Velden](https://github.com/exzachlyvv)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

