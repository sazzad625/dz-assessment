@isAdmin
        <button class="c-header-toggler c-class-toggler d-lg-none mfe-auto" type="button" data-target="#sidebar"
                data-class="c-sidebar-show">
            <i class="c-icon c-icon-lg cil-menu"></i>
        </button>
@endIsAdmin

@isTeacher
<button class="c-header-toggler c-class-toggler d-lg-none mfe-auto" type="button" data-target="#sidebar"
        data-class="c-sidebar-show">
    <i class="c-icon c-icon-lg cil-menu"></i>
</button>
@endIsTeacher
<a class="c-header-brand d-lg-none c-header-brand-sm-up-center student-logo" href="#">
    <img src="{{asset('images/ops_logo_new_student.png')}}" width="118" alt="OPS Academy">
</a>
@isAdmin
<button class="c-header-toggler c-class-toggler mfs-3 d-md-down-none" type="button" data-target="#sidebar"
        data-class="c-sidebar-lg-show" responsive="true">
    <i class="c-icon c-icon-lg cil-menu"></i>
</button>
@endIsAdmin

@isTeacher
<button class="c-header-toggler c-class-toggler mfs-3 d-md-down-none" type="button" data-target="#sidebar"
        data-class="c-sidebar-lg-show" responsive="true">
    <i class="c-icon c-icon-lg cil-menu"></i>
</button>
@endIsTeacher

<ul class="c-header-nav d-md-down-none">
@isStudent
<li class="c-header-nav-item px-3">
<a class="" href="{{route('home')}}">
    <img src="{{asset('images/ops_logo_new_student.png')}}" width="118" alt="OPS Academy">
</a>
</li>
@endIsStudent
</ul>
<ul class="c-header-nav mfs-auto">
</ul>
<ul class="c-header-nav">
    <li class="c-header-nav-item dropdown">
        <a class="c-header-nav-link" data-toggle="dropdown" href="#" role="button"
           aria-haspopup="true" aria-expanded="false">
            <div class="user-name">{{{ Auth::user()->name }}} <span class="down-arrow"></span></div>

        </a>
        <div class="dropdown-menu dropdown-menu-right pt-0">
            <div class="info-box">
              <div class="info-name d-md-none">{{{ Auth::user()->name }}}</div>
              <div class="info-email">{{{ Auth::user()->email }}}</div>
            </div>
            <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="c-icon mfe-2 cil-account-logout"></i>Logout
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>
    </li>
</ul>

