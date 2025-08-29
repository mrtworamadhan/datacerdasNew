<?php

namespace App\Filters;

use JeroenNoten\LaravelAdminLte\Menu\Filters\FilterInterface;

class ContextFilter implements FilterInterface
{
    public function transform($item)
    {
        // Cek apakah user sedang berada di route superadmin
        $isSuperAdminContext = request()->routeIs('superadmin.*', 'desas.*', 'admin.users.*', 'company-settings.*');

        // Jika item menu punya 'context'
        if (isset($item['context'])) {
            // Jika kita di konteks superadmin, TAPI menu ini untuk tenant, sembunyikan.
            if ($isSuperAdminContext && $item['context'] === 'tenant') {
                $item['restricted'] = true;
            }

            // Jika kita di konteks tenant (bukan superadmin), TAPI menu ini untuk superadmin, sembunyikan.
            if (!$isSuperAdminContext && $item['context'] === 'superadmin') {
                $item['restricted'] = true;
            }
        }

        return $item;
    }
}