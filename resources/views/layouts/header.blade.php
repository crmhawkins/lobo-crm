    <div class="row header_row" style="align-content: center !important">
        <div class="col-4 col-md-6 header_logo">
            <img src="{{ asset('assets/images/logo_la_fabrica.png') }}" style="max-width: 10%" class="logo" onclick='window.location="/home"'>
        </div>
        <div class="col-4 col-md-4 header_user">
            <div class="row user" style="align-content: center">
                <div class="col-md-6">
                </div>
                <div class="col-6 user_name px-3">
                    <div class="row" >
                        <div class="col-12 text-right">
                            <span class="title_user">
                                @if (strlen(Auth::user()->name) < 5)
                                    {{ Auth::user()->name }}
                                @else
                                    {{ Auth::user()->name . str_repeat(' ', 5) }}
                                @endif
                            </span>
                            <p class="sub_title_user">{{ $roles->where('id', Auth::user()->role)->first()->nombre }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-1 col-md-1 header_user text-left">
            <button class="alert-button" data-toggle="modal" data-target="#modalAlertas" style="position:relative">
                <span class="badge badge-secondary" id="alertasPendientes"
                    style="position: absolute;
                                top: -9px;
                                right: -11px;"></span>
            </button>
            <div class="modal fade" id="modalAlertas">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content rounded">
                        <div class="modal-header">
                            <h3 class="form-title">Alertas de hoy:</h3>
                        </div>
                        <div class="modal-body">
                            <ul id="listaAlertas" class="list-group" style="max-height:400px; overflow-y:scroll;">
                            </ul>
                        </div>
                        <div class="modal-footer">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
