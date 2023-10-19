<?php

use Carbon\Carbon;
use App\Models\Budget_reference_autopincrement;


if (! function_exists('referent_budget')) {
    function referent_budget()
    {
        $now = Carbon::now();
        $year = $now->isoFormat('YYYY');
        $mouth = $now->isoFormat('MM');
        $day = $now->isoFormat('DD');

        $numeracion = Budget_reference_autopincrement::orderBy('id', 'desc')->first();
        $finalReferencia;
        if ($numeracion != null) {
            $finalReferencia = intval($numeracion->reference_autoincrement) + 1;
        } else {
            $finalReferencia = '0001';
            return $year . $mouth . $day . $finalReferencia;
        }
        return $finalReferencia;

    }
}
