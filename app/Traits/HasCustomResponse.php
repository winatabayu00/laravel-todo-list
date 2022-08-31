<?php

namespace App\Traits;

trait HasCustomResponse
{
    /* set default customize alert to display on sweet alert */
    public function success_create(string $model_name): array
    {
        return [
            'icon' => 'success',
            'title' => 'Success',
            'message' => 'New ' . $model_name . ' successfully created!.',
        ];
    }
    public function success_update(string $model_name): array
    {
        return [
            'icon' => 'success',
            'title' => 'Success',
            'message' => $model_name . ' has been updated!.',
        ];
    }
    public function success_delete(string $model_name): array
    {
        return [
            'icon' => 'success',
            'title' => 'Success',
            'message' => $model_name . ' has been deleted!.',
        ];
    }

    public function success_custom(string $model_name, string $message): array
    {
        return [
            'icon' => 'success',
            'title' => 'Success',
            'message' => $model_name . ' ' . $message,
        ];
    }
}
