<?php

class EstimateService
{
    private EstimateRepository $repo;

    public function __construct(EstimateRepository $repo)
    {
        $this->repo = $repo;
    }

    public function datatable(array $params)
    {
        // Inject user-specific filters for the "Load" permission (ID 14)
        $params['user_branches'] = $this->repo->getUserBranches(14);
        $params['user_cities'] = $this->repo->getUserCities(14);

        return $this->repo->datatable($params);
    }

    public function getFormDependencies(): array
    {
        return [
            'branches' => $this->repo->getBranches(),
            'cities'   => $this->repo->getCities(),
            'user_branches' => $this->repo->getUserBranches(14),
            'user_cities' => $this->repo->getUserCities(14),
        ];
    }

    public function getClients()
    {
        return $this->repo->getAllCustomers();
    }
}
