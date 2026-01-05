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
        return $this->repo->datatable($params);
    }

    public function getFormDependencies(): array
    {
        return [
            'branches' => $this->repo->getBranches(),
            'cities'   => $this->repo->getCities(),
        ];
    }

    public function getClients()
    {
        return $this->repo->getAllCustomers();
    }
}
