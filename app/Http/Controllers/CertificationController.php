<?php

namespace App\Http\Controllers;

use App\Http\Resources\CertificationResource;
use App\Models\Certification;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CertificationController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return CertificationResource::collection(Certification::query()->ordered()->get());
    }
}
