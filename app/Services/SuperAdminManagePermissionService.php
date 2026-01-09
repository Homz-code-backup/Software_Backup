<?php

class SuperAdminManagePermissionService
{
    private PermissionAdminRepository $repo;

    public function __construct()
    {
        $this->repo = new PermissionAdminRepository();
    }

    public function getGroupedPages(): array
    {
        $pages = $this->repo->getAllPages();
        $subs  = $this->repo->getSubPermissionsByPage();

        $grouped = [];
        foreach ($pages as $p) {
            $p['sub_permissions'] = $subs[$p['id']] ?? [];
            $grouped[$p['module']][] = $p;
        }

        ksort($grouped);
        return $grouped;
    }

    public function getIcons(): array
    {
        return require app_path('Config/bootstrap_icons.php');
    }

    public function getModules(): array
    {
        return [
            'HR',
            'Sales',
            'My HR',
            'Presales',
            'Management',
            'Designers',
            'Marketing',
            'CRM',
            'Operations',
            'Accounts',
            'Purchase',
            'IT',
            'Architects',
            'Admin',
            'Manager'
        ];
    }

    public function handlePage(array $d): void
    {
        if (isset($d['add_page'])) {
            $this->repo->addPage($d);
        } elseif (isset($d['edit_page'])) {
            $this->repo->updatePage($d);
        } elseif (isset($d['delete_page'])) {
            $this->repo->deletePage((int)$d['id']);
        }
    }

    public function handleSubPermission(array $d): void
    {
        if (isset($d['add_sub_permission'])) {
            $this->repo->addSubPermission($d);
        } elseif (isset($d['edit_sub_permission'])) {
            $this->repo->updateSubPermission($d);
        } elseif (isset($d['delete_sub_permission'])) {
            $this->repo->deleteSubPermission((int)$d['id']);
        }
    }
}
