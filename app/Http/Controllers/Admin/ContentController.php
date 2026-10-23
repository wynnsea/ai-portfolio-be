<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContentRequest;
use App\Http\Requests\ReorderRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;

/**
 * Shared admin CRUD for content resources. Subclasses define the model and
 * resource classes and type-hint their own Form Request in store() and update().
 */
abstract class ContentController extends Controller
{
    /**
     * Default rows per page in the admin tables.
     */
    public const PER_PAGE = 6;

    /**
     * @var class-string<Model>
     */
    protected string $model;

    /**
     * @var class-string<JsonResource>
     */
    protected string $resource;

    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = min(max($request->integer('perPage', self::PER_PAGE), 1), 100);

        $items = $this->model::query()
            ->adminListing($request->query())
            ->paginate($perPage)
            ->withQueryString();

        return $this->resource::collection($items);
    }

    public function show(string $id): JsonResource
    {
        return new $this->resource($this->model::query()->findOrFail($id));
    }

    public function destroy(string $id): Response
    {
        $this->model::query()->findOrFail($id)->delete();

        return response()->noContent();
    }

    public function reorder(ReorderRequest $request): Response
    {
        $this->model::reorder($request->validated('ids'));

        return response()->noContent();
    }

    protected function storeFrom(ContentRequest $request): JsonResponse
    {
        $item = $this->model::query()->create($request->attributesForModel());

        return (new $this->resource($item))->response()->setStatusCode(201);
    }

    protected function updateFrom(ContentRequest $request, string $id): JsonResource
    {
        $item = $this->model::query()->findOrFail($id);
        $item->update($request->attributesForModel());

        return new $this->resource($item);
    }
}
