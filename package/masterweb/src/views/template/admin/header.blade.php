@php
    $user = Auth()->user();
    $level = $user->getlevel->level;
    $privilege = \Smt\Masterweb\Models\Privileges::where('id', $user->level)->first();
@endphp
<nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row navbar-info default-layout-navbar">
    <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center"
        style="background:#d2dfdf">
        <a class="navbar-brand brand-logo" href="{{ url('') }}" target="_blank">
            <img src="{{ asset('assets/admin/images/logo/logo-silaboy.png') }}" style="width:180px;" alt="Logo" />
        </a>
        <a class="navbar-brand brand-logo-mini" href="{{ url('') }}" target="_blank">
            <img src="{{ asset('assets/admin/images/logo/logo-silaboy-mini.png') }}" alt="logo" />
        </a>
    </div>
    <div class="navbar-menu-wrapper d-flex align-items-stretch" style="background-color: #3ca8a8">
        <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
            <span class="fas fa-bars"></span>
        </button>
        <ul class="navbar-nav">
            <li class="nav-item nav-search d-none d-md-flex">
                <div class="nav-link" style="width: 600px !important">
                    <select class="form-control smt-select2" id="smt_navigation" style="width: 100%">

                        @php
                            $parent = \Smt\Masterweb\Models\AdminMenu::all()->sortBy('order')->where('upmenu', '0');
                        @endphp

                        @foreach ($parent as $menu)
                            @php
                                $role = \Smt\Masterweb\Models\Role::where('menu_id', $menu->id)
                                    ->where('privilege_id', $privilege->id)
                                    ->first();

                                if ($role != null) {
                                    if ($role->read == 0) {
                                        continue;
                                    }
                                }

                                $child = \Smt\Masterweb\Models\AdminMenu::all()
                                    ->sortBy('order')
                                    ->where('upmenu', $menu->id);
                            @endphp

                            @if (count($child) > 0)
                                <optgroup label="{{ $menu->name }}">
                                    @foreach ($child as $submenu)
                                        @php
                                            $role = \Smt\Masterweb\Models\Role::where('menu_id', $submenu->id)
                                                ->where('privilege_id', $privilege->id)
                                                ->first();

                                            if ($role != null) {
                                                if ($role->read == 0) {
                                                    continue;
                                                }
                                            }
                                        @endphp

                                        <option value="{{ URL::to($submenu->link) }}"
                                            {{ $submenu->link == request()->segment(1) ? 'selected' : '' }}>
                                            {{ $submenu->name }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @else
                                <option value="{{ $menu->link }}"
                                    {{ $menu->link == request()->segment(1) ? 'selected' : '' }}>
                                    {{ $menu->name }}</option>
                            @endif
                        @endforeach
                    </select>

                </div>
            </li>
        </ul>

        <ul class="navbar-nav navbar-nav-right">
            <div><span id="txt"></span></div>


            <li class="nav-item nav-profile dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" id="profileDropdown">
                    <span>{{ $user->name }}</span>
                    {{-- <img
            src="{{ $user->photo == null ? asset('assets/admin/images/logo/favicon.png') : asset('/storage/photo/' . $user->photo) }}"
            alt="profile" /> --}}

                    @if (Storage::disk('public')->exists('photo/' . $user->photo) && $user->photo != null)
                        <img src="{{ Storage::url('photo/' . $user->photo) }}" alt="profile" />
                    @else
                        <img src="{{ asset('assets/admin/images/logo/favicon.png') }}" alt="profile" />
                    @endif
                </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list"
                    aria-labelledby="profileDropdown">

                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item preview-item" href="/biodata">
                        <div class="preview-thumbnail">
                            <div class="preview-icon bg-warning">
                                <i class="fas fa-wrench mx-0"></i>
                            </div>
                        </div>
                        <div class="preview-item-content">
                            <h6 class="preview-subject font-weight-medium">Pengaturan</h6>
                        </div>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item preview-item" href="{{ route('logout') }}"
                        onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                        <div class="preview-thumbnail">
                            <div class="preview-icon bg-info">
                                <i class="fas fa-power-off mx-0"></i>
                            </div>
                        </div>
                        <div class="preview-item-content">
                            <h6 class="preview-subject font-weight-medium">Logout</h6>
                        </div>
                    </a>
                </div>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </li>
        </ul>

        {{-- <ul class="navbar-nav navbar-nav-right">
          <li class="nav-item nav-profile dropdown">
            <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" id="profileDropdown">
              <span>{{$user->name}}</span>
              <img src="{{ ($user->photo == NULL) ? asset('assets/admin/images/logo/favicon.png') : asset('/storage/photo/'.$user->photo)}}" alt="profile"/>
            </a>
            <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
              <a class="dropdown-item" href="/biodata">
                <i class="fas fa-cog text-primary"></i>
                Pengaturan
              </a>
              <div class="dropdown-divider"></div>

              <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                <i class="fas fa-power-off text-primary"></i> Logout
              </a>


            </div>
          </li>
          {{-- <li class="nav-item nav-settings d-none d-lg-block">
            <a class="nav-link" href="#">
              <i class="fas fa-ellipsis-h"></i>
            </a>
          </li> --}}
        {{-- </ul> --}}
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
            data-toggle="offcanvas">
            <span class="fas fa-bars"></span>
        </button>
    </div>
</nav>
