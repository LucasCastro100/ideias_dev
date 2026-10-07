<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class IbgeService
{
    protected string $disk = 'public';

    public function getRegions(): array
    {
        return Cache::rememberForever('ibge_regions', function () {
            return json_decode(Storage::disk($this->disk)->get('ibge/regioes.json'), true);
        });
    }

    public function getStates(): array
    {
        return Cache::rememberForever('ibge_states', function () {
            return json_decode(Storage::disk($this->disk)->get('ibge/estados.json'), true);
        });
    }

    public function getCities(): array
    {
        return Cache::rememberForever('ibge_cities', function () {
            return json_decode(Storage::disk($this->disk)->get('ibge/municipios.json'), true);
        });
    }

    public function getStatesByRegion(int $regionId): array
    {
        return collect($this->getStates())
            ->where('regiao.id', $regionId)
            ->values()
            ->all();
    }

    public function getCitiesByState(int $stateId): array
    {
        return collect($this->getCities())
            ->where('microrregiao.mesorregiao.UF.id', $stateId)
            ->values()
            ->all();
    }
}
