<?php namespace Vdomah\Roles\Classes;

use Auth;
use RainLab\Builder\Classes\PermissionsModel;
use Vdomah\Roles\Models\Permission as PermissionModel;

/**
 * Model
 */
class Helper
{

    public static function able($perm_code, $user = null)
    {
        $perm = PermissionModel::where('code', $perm_code)->first();

        if (Auth::check() && $user == null) {
            $user = Auth::getUser();
        }

        return $user && $perm ? $user->role->gotPermission($perm) : false;
    }

    public static function isRole($code, $user = null)
    {
        $out = false;

        //$role_ids = [];
        $first = self::where('code', $code)->first();

        $role_ids = $first->ancestors->lists('id');
        $role_ids[] = $first->id;

        if (Auth::check() && $user == null) {
            $user = Auth::getUser();
        }

        if (in_array($user->role_id, $role_ids)) {
            $out = true;
        }

        return $out;
    }

    public static function iterateChildren($children, $perm)
    {
        $out = false;

        foreach ($children as $child) {
            if ($child->id == $perm->role_id) {
                $out = true;
            } elseif ($child->children != null) {
                $out = self::iterateChildren($child->children, $perm);
            }
        }

        return $out;
    }
}