<?php

class SuperAdminManagePermissionController
{
    public function index()
    {
        AuthMiddleware::check();
        // PermissionMiddleware::page('manage-permissions');

        $_SESSION['active_module'] = 'superadmin';

        $service = new SuperAdminManagePermissionService();

        view('superadmin/manage_permissions', [ 
            'groupedPages' => $service->getGroupedPages(),
            'icons'        => $service->getIcons(),
            'modules'      => $service->getModules()
        ], 'app');
    }

    public function pageAction()
    {
        AuthMiddleware::check();
        // PermissionMiddleware::action('manage-permissions.page');

        (new SuperAdminManagePermissionService())->handlePage($_POST);
        redirect('/superadmin/manage-permissions');
    }

    public function subAction()
    {
        AuthMiddleware::check();
        // PermissionMiddleware::action('manage-permissions.sub');

        (new SuperAdminManagePermissionService())->handleSubPermission($_POST);
        redirect('/superadmin/manage-permissions');
    }
}
