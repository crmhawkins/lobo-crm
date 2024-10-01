<?php

namespace App\Helpers;

use App\Models\GrupoContable;
use App\Models\SubGrupoContable;
use App\Models\CuentasContable;
use App\Models\SubCuentaContable;
use App\Models\SubCuentaHijo;

class GlobalFunctions
{
    public static function findCuentaByNumero($numero)
    {
        $dataSub = [];

        $grupos = GrupoContable::orderBy('numero', 'asc')->get();
        foreach ($grupos as $grupo) {
            $subGrupos = SubGrupoContable::where('grupo_id', $grupo->id)->get();
            foreach ($subGrupos as $subGrupo) {
                $cuentas = CuentasContable::where('sub_grupo_id', $subGrupo->id)->get();
                foreach ($cuentas as $cuenta) {
                    if ($cuenta->numero == $numero) {
                        return [
                            'grupo' => $grupo,
                            'subGrupo' => $subGrupo,
                            'cuenta' => $cuenta
                        ];
                    }

                    $subCuentas = SubCuentaContable::where('cuenta_id', $cuenta->id)->get();
                    foreach ($subCuentas as $subCuenta) {
                        if ($subCuenta->numero == $numero) {
                            return [
                                'grupo' => $grupo,
                                'subGrupo' => $subGrupo,
                                'cuenta' => $cuenta,
                                'subCuenta' => $subCuenta
                            ];
                        }

                        $subCuentasHijas = SubCuentaHijo::where('sub_cuenta_id', $subCuenta->id)->get();
                        foreach ($subCuentasHijas as $subCuentaHija) {
                            if ($subCuentaHija->numero == $numero) {
                                return [
                                    'grupo' => $grupo,
                                    'subGrupo' => $subGrupo,
                                    'cuenta' => $cuenta,
                                    'subCuenta' => $subCuenta,
                                    'subCuentaHija' => $subCuentaHija
                                ];
                            }
                        }
                    }
                }
            }
        }

        return null;
    }

    public static function loadCuentasContables()
    {
        $dataSub = [];
        $indice = 0;

        $grupos = GrupoContable::orderBy('numero', 'asc')->get();
        foreach ($grupos as $grupo) {
            array_push($dataSub, [
                'grupo' => $grupo,
                'subGrupo' => []
            ]);

            $subGrupos = SubGrupoContable::where('grupo_id', $grupo->id)->get();
            $i = 0;
            foreach ($subGrupos as $subGrupo) {
                array_push($dataSub[$indice]['subGrupo'], [
                    'item' => $subGrupo,
                    'cuentas' => []
                ]);

                $cuentas = CuentasContable::where('sub_grupo_id', $subGrupo->id)->get();
                $index = 0;
                foreach ($cuentas as $cuenta) {
                    array_push($dataSub[$indice]['subGrupo'][$i]['cuentas'], [
                        'item' => $cuenta,
                        'subCuentas' => []
                    ]);

                    $subCuentas = SubCuentaContable::where('cuenta_id', $cuenta->id)->get();

                    if (count($subCuentas) > 0) {
                        $indices = 0;
                        foreach ($subCuentas as $subCuenta) {
                            array_push($dataSub[$indice]['subGrupo'][$i]['cuentas'][$index]['subCuentas'], [
                                'item' => $subCuenta,
                                'subCuentasHija' => []
                            ]);

                            $sub_cuenta = SubCuentaHijo::where('sub_cuenta_id', $subCuenta->id)->get();
                            if (count($sub_cuenta) > 0) {
                                foreach ($sub_cuenta as $subCuentaHijo) {
                                    array_push($dataSub[$indice]['subGrupo'][$i]['cuentas'][$index]['subCuentas'][$indices]['subCuentasHija'], $subCuentaHijo);
                                }
                            }
                        }
                    }
                    $index++;
                }
                $i++;
            }
            $indice++;
        }

        return $dataSub;
    }
}
