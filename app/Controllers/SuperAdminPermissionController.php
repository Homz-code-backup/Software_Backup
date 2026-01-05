<?php

class SuperAdminPermissionController
{
    public function index()
    {
        AuthMiddleware::check();
        // PermissionMiddleware::page('assign-permissions');

        $_SESSION['active_module'] = 'superadmin';

        $service = new SuperAdminPermissionService();

        view('superadmin/assign_permissions', [
            'employees'      => $service->getEmployees(),
            'pagesByModule'  => $service->getPagesGrouped(),
            'subPermissions' => $service->getSubPermissions(),
            'userPages'      => $service->getUserPageAssignments(),
            'userSubs'       => $service->getUserSubAssignments(),
            'cities'         => $service->getCities(),
            'branches'       => $service->getBranches(),
            'userCities'     => $service->getUserCitiesAssignments(),
            'userBranches'   => $service->getUserBranchesAssignments(),
        ], 'app');
    }

    public function store()
    {
        AuthMiddleware::check();
        // PermissionMiddleware::action('assign-permissions.update');

        (new SuperAdminPermissionService())->update($_POST);

        redirect('/superadmin/assign-permissions');
    }
}
