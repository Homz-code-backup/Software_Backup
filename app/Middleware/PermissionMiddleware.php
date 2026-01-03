<?php
class PermissionMiddleware
{
    /** 
     * Page-level permission check
     * Example: estimates, projects, dashboard
     */
    public static function page(string $pageSlug): void
    {
        if (!PermissionService::hasPage($pageSlug)) {
            http_response_code(403);
            echo "403 - You don't have permission to access this page";
            exit;
        }
    }

    /** 
     * Action-level permission check 
     * Example: estimate.create, estimate.edit
     */
    public static function action(string $actionSlug): void
    {
        if (!PermissionService::hasAction($actionSlug)) {
            http_response_code(403);
            echo "403 - You don't have permission to perform this action";
            exit;
        }
    }


    public static function hasPage(string $pageSlug): bool
    {
        return PermissionService::hasPage($pageSlug);
    }

    public static function hasAction(string $actionSlug): bool
    {
        return PermissionService::hasAction($actionSlug);
    }
}
