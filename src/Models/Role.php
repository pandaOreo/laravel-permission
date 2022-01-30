<?php

namespace PandaOreo\Permission\Models;

use PandaOreo\Permission\Contracts\Role as ContractRole;
use PandaOreo\Permission\Traits\HasMenus;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Models\Role as SpatieRole;

/**
 * Class Role
 *
 * @property int $id
 * @property string $name
 * @property string $guard_name
 * @package Hedeqiang\Permission\Models
 */
class Role extends SpatieRole implements ContractRole
{
    use HasMenus;

    public function menus(): BelongsToMany
    {
        return self::belongsToMany(
            config('permission.models.menu'),
            config('permission.table_names.role_has_menus'),
            'role_id',
            'menu_id'
        );
    }

    public function getMenuTree($parentId = null, $showButton = false)
    {
        $allMenuIds = $this->menus
            ->map(function ($menu) {
                return array_merge(array_filter(explode('-', trim($menu->parent_path, '-'))), [$menu->id]);
            })
            ->collapse()
            ->unique();

        $allMenus = Menu::all()->whereIn('id', $allMenuIds);
        return Menu::getMenuTree($parentId, $allMenus, $showButton);
    }
}
