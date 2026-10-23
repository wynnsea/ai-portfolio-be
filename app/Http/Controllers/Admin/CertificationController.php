<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CertificationRequest;
use App\Http\Resources\CertificationResource;
use App\Models\Certification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

class CertificationController extends ContentController
{
    protected string $model = Certification::class;

    protected string $resource = CertificationResource::class;

    public function store(CertificationRequest $request): JsonResponse
    {
        return $this->storeFrom($request);
    }

    public function update(CertificationRequest $request, string $certification): JsonResource
    {
        return $this->updateFrom($request, $certification);
    }
}
