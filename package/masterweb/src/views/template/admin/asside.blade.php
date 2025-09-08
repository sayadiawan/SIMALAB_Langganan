@php
$user = Auth()->user();
$level = $user->getlevel->level;
$privilege = \Smt\Masterweb\Models\Privileges::where('id', $user->level)->first();
@endphp
<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
    <li class="nav-item nav-profile">
      <div class="nav-link">
        <div class="profile-image">
          {{-- <img src="{{ ($user->photo == NULL) ? asset('assets/admin/images/logo/favicon.png') : asset('/storage/photo/'.$user->photo)}}" alt="image"/> --}}

          @if (Storage::disk('public')->exists('photo/' . $user->photo) && $user->photo != null)
            <img src="{{ Storage::url('photo/' . $user->photo) }}" alt="profile" />
          @else
            <img src="{{ asset('assets/admin/images/logo/favicon.png') }}" alt="profile" />
          @endif
        </div>
        <div class="profile-name">
          <p class="name">
            Hai, {{ explode(' ', $user->name)[0] }}
          </p>
          <p class="designation">
            {{-- {{$privilege->name}} --}}
          </p>
        </div>
      </div>
    </li>

    {{-- LIST MENU --}}
    @php
      $parent = \Smt\Masterweb\Models\AdminMenu::all()
          ->sortBy('order')
          ->where('upmenu', '0');

          // dd($parent);
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
      @if ( SmtHelp::create_link($menu->name) =="klinik")
        @if (!isset($user->laboratorium))
            <li class="nav-item">
              @if (count($child) > 0)
              <a class="nav-link" data-toggle="collapse" href="#menu-{{ SmtHelp::create_link($menu->name) }}" aria-expanded="false"
                aria-controls="page-layouts">
                <i class="{{ $menu->icon }} menu-icon"></i><span class="menu-title">{{ $menu->name }}</span>
                <i class="menu-arrow"></i>
              </a>

              <div class="collapse" id="menu-{{ SmtHelp::create_link($menu->name) }}">
                <ul class="nav flex-column sub-menu">
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
                  <li class="nav-item"> <a class="nav-link" href="{{ URL::to($submenu->link) }}">{{ $submenu->name }}</a></li>
                  @endforeach
                </ul>
              </div>
              @else
              @if ($menu->link!="elits-analys")
              <a class="nav-link" href="{{ $menu->link }}">
                <i class="{{ $menu->icon }} menu-icon"></i><span class="menu-title">{{ $menu->name }}</span>
              </a>
              @endif
              {{-- @if ($user->laboratorium->nama_laboratorium=="Klinik" && $menu->link != '/elits-analys')
                <a class="nav-link" href="{{ $menu->link }}">
                  <i class="{{ $menu->icon }} menu-icon"></i><span class="menu-title">{{ $menu->name }}</span>
                </a>
              @else
                <a class="nav-link" href="{{ $menu->link }}">
                  <i class="{{ $menu->icon }} menu-icon"></i><span class="menu-title">{{ $menu->name }}</span>
                </a>
              @endif --}}
              @endif
            </li>
        @elseif ($user->laboratorium->nama_laboratorium=="Klinik" )
          <li class="nav-item">
            @if (count($child) > 0)
              <a class="nav-link" data-toggle="collapse" href="#menu-{{ SmtHelp::create_link($menu->name) }}" aria-expanded="false"
                aria-controls="page-layouts">
                <i class="{{ $menu->icon }} menu-icon"></i><span class="menu-title">{{ $menu->name }}</span>
                <i class="menu-arrow"></i>
              </a>

              <div class="collapse" id="menu-{{ SmtHelp::create_link($menu->name) }}">
                <ul class="nav flex-column sub-menu">
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
                  <li class="nav-item"> <a class="nav-link" href="{{ URL::to($submenu->link) }}">{{ $submenu->name }}</a></li>
                  @endforeach
                </ul>
              </div>
            @else
              @if ($menu->link!="elits-analys")
                <a class="nav-link" href="{{ $menu->link }}">
                  <i class="{{ $menu->icon }} menu-icon"></i><span class="menu-title">{{ $menu->name }}</span>
                </a>
              @endif
            @endif
          </li>
        @endif
      @else
      <li class="nav-item">
        @if (count($child) > 0)
          <a class="nav-link" data-toggle="collapse" href="#menu-{{ SmtHelp::create_link($menu->name) }}"
            aria-expanded="false" aria-controls="page-layouts">
            <i class="{{ $menu->icon }} menu-icon"></i><span class="menu-title">{{ $menu->name }}</span>
            <i class="menu-arrow"></i>
          </a>

          <div class="collapse" id="menu-{{ SmtHelp::create_link($menu->name) }}">
            <ul class="nav flex-column sub-menu">
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
                <li class="nav-item"> <a class="nav-link"
                    href="{{ URL::to($submenu->link) }}">{{ $submenu->name }}</a></li>
              @endforeach
            </ul>
          </div>
        @else
         @if (isset($user->laboratorium))
            @if ($user->laboratorium->nama_laboratorium=="Klinik" && $menu->link != '/elits-analys')
              <a class="nav-link" href="{{ $menu->link }}">
                <i class="{{ $menu->icon }} menu-icon"></i><span class="menu-title">{{ $menu->name }}</span>
              </a>
            @elseif ($user->laboratorium->nama_laboratorium !="Klinik" && $menu->link != '/elits-analys/klinik')
              <a class="nav-link" href="{{ $menu->link }}">
                <i class="{{ $menu->icon }} menu-icon"></i><span class="menu-title">{{ $menu->name }}</span>
              </a>
            @endif
         @else
            <a class="nav-link" href="{{ $menu->link }}">
              <i class="{{ $menu->icon }} menu-icon"></i><span class="menu-title">{{ $menu->name }}</span>
            </a>
          @endif
        @endif
      </li>
      @endif
    @endforeach
    <li class="nav-item">
      <a class="nav-link" href="{{ route('logout') }}"
        onclick="event.preventDefault();document.getElementById('logout-form').submit();">
        <i class="fas fa-power-off menu-icon"></i><span class="menu-title">Logout</span>
      </a>

      <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
      </form>
    </li>
  </ul>
</nav>
