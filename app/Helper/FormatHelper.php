<?php

namespace App\Helper;

use App\Models\Mutation;

class FormatHelper
{
    public static function generateMutationCode($productLocationId)
    {
        $today = now()->format('ymd');
        $locationId = str_pad($productLocationId, 3, '0', STR_PAD_LEFT);

        $countToday = Mutation::whereDate('mutation_date', now())
            ->where('product_location_id', $productLocationId)
            ->count() + 1;

        $sequence = str_pad($countToday, 3, '0', STR_PAD_LEFT);

        return "Mutasi-{$today}-{$locationId}-{$sequence}";
    }
}
