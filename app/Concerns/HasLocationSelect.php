<?php

namespace App\Concerns;

use App\Services\IbgeService;

/**
 * Select em cascata regiao -> estado -> municipio (dados do IBGE), no mesmo
 * padrao usado em TbrEventDetail. So os campos "estado"/"cidade" (locState/
 * locCity) sao persistidos pelo componente que usa a trait; regiao existe
 * so pra filtrar a lista de estados.
 */
trait HasLocationSelect
{
    public $locRegion = null;
    public $locRegionId = null;
    public $locState = null;
    public $locStateId = null;
    public $locCity = null;
    public $locCityId = null;

    public $locRegions = [];
    public $locFilteredStates = [];
    public $locFilteredCities = [];

    protected function ibge(): IbgeService
    {
        return app(IbgeService::class);
    }

    public function initLocationSelect(?string $stateName = null, ?string $cityName = null): void
    {
        $this->locRegions = $this->ibge()->getRegions();
        $this->locFilteredStates = [];
        $this->locFilteredCities = [];
        $this->locRegion = null;
        $this->locRegionId = null;
        $this->locState = $stateName;
        $this->locStateId = null;
        $this->locCity = $cityName;
        $this->locCityId = null;

        if (!$stateName) {
            return;
        }

        $state = collect($this->ibge()->getStates())->firstWhere('nome', $stateName);
        if (!$state) {
            return;
        }

        $this->locStateId = $state['id'];
        $this->locRegionId = $state['regiao']['id'] ?? null;
        $this->locRegion = $state['regiao']['nome'] ?? null;
        $this->locFilteredStates = $this->locRegionId ? $this->ibge()->getStatesByRegion($this->locRegionId) : [];
        $this->locFilteredCities = $this->ibge()->getCitiesByState($this->locStateId);

        if ($cityName) {
            $city = collect($this->locFilteredCities)->firstWhere('nome', $cityName);
            $this->locCityId = $city['id'] ?? null;
        }
    }

    public function updatedLocRegion($value): void
    {
        $region = collect($this->locRegions)->firstWhere('nome', $value);
        $this->locRegionId = $region['id'] ?? null;

        $this->locState = null;
        $this->locStateId = null;
        $this->locCity = null;
        $this->locCityId = null;
        $this->locFilteredCities = [];

        $this->locFilteredStates = $this->locRegionId
            ? $this->ibge()->getStatesByRegion($this->locRegionId)
            : [];
    }

    public function updatedLocState($value): void
    {
        $state = collect($this->locFilteredStates)->firstWhere('nome', $value);
        $this->locStateId = $state['id'] ?? null;

        $this->locCity = null;
        $this->locCityId = null;

        $this->locFilteredCities = $this->locStateId
            ? $this->ibge()->getCitiesByState($this->locStateId)
            : [];
    }

    public function updatedLocCity($value): void
    {
        $city = collect($this->locFilteredCities)->firstWhere('nome', $value);
        $this->locCityId = $city['id'] ?? null;
    }

    public function resetLocationSelect(): void
    {
        $this->locRegion = null;
        $this->locRegionId = null;
        $this->locState = null;
        $this->locStateId = null;
        $this->locCity = null;
        $this->locCityId = null;
        $this->locFilteredStates = [];
        $this->locFilteredCities = [];
    }
}
