<?php
class PermissionService
{
    private static bool $loaded = false;
    private static array $pages = [];
    private static array $actions = [];

    private static function load(): void
    {
        if (self::$loaded || !isset($_SESSION['user_employee_id'])) {
            return;
        }

        $repo = new PermissionRepository();

        self::$pages = $repo->getPagePermissions($_SESSION['user_employee_id']);
        self::$actions = $repo->getSubPermissions($_SESSION['user_employee_id']);
        self::$loaded = true;
    }

    public static function hasPage(string $slug): bool
    {
        self::load();
        return in_array($slug, self::$pages, true);
    }

    public static function hasAction(string $slug): bool
    {
        //slug format: estimate.add
        $slug = str_replace('.', '/', $slug);
        $page = explode('/', $slug)[0];
        $action = explode('/', $slug)[1];
        if (in_array($page, self::$pages)) {
            return in_array($action, self::$actions);
        } else {
            return false;
        }
    }

    public static function getCityFilters(string $employeeId): array
    {
        $map = [
            'superadmin' => [1, 2, 3],
            'HZI001' => [1, 2, 3],
            'HZI004' => [1, 2, 3],
            'HZI187' => [1, 3],
            'HZI018' => [2],
            'HZI138' => [1, 2, 3],
            'HZI500' => [1, 2, 3],
            'HZI019' => [1, 2, 3],
        ];

        return $map[$employeeId] ?? [];
    }
}
