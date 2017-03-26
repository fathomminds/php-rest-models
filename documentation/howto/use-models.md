## How to use Models? ##

An example usage of basic Model usage with the [Lumen PHP framework](https://lumen.laravel.com/).

### FooController ###

```
<?php
namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use YourApp\Models\FooModel;
use Fathomminds\Rest\Exceptions\RestException;

class FooController extends Controller
{

    public function getList(FooModel $model)
    {
        try {
            return new JsonResponse($model->all());
        } catch (\Exception $ex) {
            return new JsonResponse(['error' => $ex->getMessage()]);
        }
    }

    public function update(FooModel $model, Request $request, $id)
    {
        try {
            $input = json_decode($request->getContent());
            $model->use($input);
            $model->resource()->_id = $id;
            $model->save();
            return new JsonResponse($model->resource());
        } catch (\Exception $ex) {
            return new JsonResponse(['error' => $ex->getMessage()]);
        }
    }

    public function delete(FooModel $model, $id)
    {
        try {
            $model->one($id)->delete();
            return new JsonResponse($id);
        } catch (\Exception $ex) {
            return new JsonResponse(['error' => $ex->getMessage()]);
        }
    }

    public function create(FooModel $model, Request $request)
    {
        try {
            $input = json_decode($request->getContent());
            $model->use($input);
            $model->save();
            return new JsonResponse($model->resource());
        } catch (\Exception $ex) {
            return new JsonResponse(['error' => $ex->getMessage()]);
        }
    }

    public function getItem(FooModel $model, $id)
    {
        try {
            return new JsonResponse($model->one($id)->resource());
        } catch (\Exception $ex) {
            return new JsonResponse(['error' => $ex->getMessage()]);
        }
    }
}

```
