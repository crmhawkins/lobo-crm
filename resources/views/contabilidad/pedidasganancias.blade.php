@extends('layouts.app')

@section('title', 'Contabilidad')

@section('content-principal')
<div class="container">
    <h1 class="my-4">Perdidas y Ganancias</h1>

    <!-- Formulario de filtros -->
    <form method="GET" action="{{ route('contabilidad.perdidasYGanancias') }}" class="mb-4">
        <table class="table table-bordered">
            <tr>
                <td>
                   {{$totalNegocios}}
                    
                </td>
                <td>
                    Importe netro de la cifra de negocios
                </td>
            </tr>

        </table>
    </form>
</div>
@endsection
