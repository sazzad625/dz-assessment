<li class="c-sidebar-nav-item">
    <a class="c-sidebar-nav-link c-active" href="{{ route('home') }}">
        <i class="c-sidebar-nav-icon cil-home"></i>Dashboard
    </a>
</li>

@hasPermissionAny([
App\Models\Permission::VIEW_ROLE_PERMISSION,
App\Models\Permission::CREATE_ROLE_PERMISSION,
])
<li class="c-sidebar-nav-dropdown"><a class="c-sidebar-nav-dropdown-toggle" href="javascript:void(0)">
        <i class="c-sidebar-nav-icon cil-lock-locked"></i> Roles</a>
    <ul class="c-sidebar-nav-dropdown-items">
        @hasPermission(App\Models\Permission::CREATE_ROLE_PERMISSION)
            <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{route('role.create')}}"> Add Role</a></li>
        @endHasPermission
        @hasPermission(App\Models\Permission::VIEW_ROLE_PERMISSION)
            <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{route('role.view')}}"> View Role</a></li>
        @endHasPermission
    </ul>
</li>
@endHasPermissionAny

@hasPermissionAny([
App\Models\Permission::VIEW_COURSE_PERMISSION,
App\Models\Permission::CREATE_COURSE_PERMISSION,
])
<li class="c-sidebar-nav-dropdown"><a class="c-sidebar-nav-dropdown-toggle" href="javascript:void(0)">
        <i class="c-sidebar-nav-icon cil-video"></i> Courses</a>
    <ul class="c-sidebar-nav-dropdown-items">
        @hasPermission(App\Models\Permission::CREATE_COURSE_PERMISSION)
            <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{route('course.add')}}"> Add Course</a></li>
        @endHasPermission
        @hasPermission(App\Models\Permission::SEARCH_COURSE_PERMISSION)
            <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{route('course.search')}}"> View Courses</a></li>
        @endHasPermission
    </ul>
</li>
@endHasPermissionAny

@hasPermissionAny([
App\Models\Permission::CREATE_COURSE_CATEGORY_PERMISSION,
App\Models\Permission::VIEW_COURSE_CATEGORY_PERMISSION,
])
<li class="c-sidebar-nav-dropdown"><a class="c-sidebar-nav-dropdown-toggle" href="javascript:void(0)">
        <i class="c-sidebar-nav-icon cil-list-rich"></i> Course Categories</a>
    <ul class="c-sidebar-nav-dropdown-items">
        @hasPermission(App\Models\Permission::CREATE_COURSE_CATEGORY_PERMISSION)
            <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{route('category.add')}}"> Add Category</a></li>
        @endHasPermission
        @hasPermission(App\Models\Permission::VIEW_COURSE_CATEGORY_PERMISSION)
            <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{route('category.view')}}"> View Categories</a></li>
        @endHasPermission
    </ul>
</li>
@endHasPermissionAny


@hasPermissionAny([
App\Models\Permission::CREATE_DEPARTMENT_PERMISSION,
App\Models\Permission::SEARCH_DEPARTMENT_PERMISSION,
])
<li class="c-sidebar-nav-dropdown"><a class="c-sidebar-nav-dropdown-toggle" href="javascript:void(0)">
    <i class="c-sidebar-nav-icon cil-building"></i> Department</a>
    <ul class="c-sidebar-nav-dropdown-items">
        @hasPermission(App\Models\Permission::CREATE_DEPARTMENT_PERMISSION)
        <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{route('department.add')}}"> Add Department</a></li>
        @endHasPermission
        @hasPermission(App\Models\Permission::SEARCH_DEPARTMENT_PERMISSION)
        <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{route('department.search')}}"> View Department</a></li>
        @endHasPermission
    </ul>
</li>
@endHasPermissionAny

@hasPermissionAny([
App\Models\Permission::CREATE_STUDENT_PERMISSION,
App\Models\Permission::VIEW_STUDENT_PERMISSION,
App\Models\Permission::BULK_UPLOAD_STUDENT_PERMISSION,
])
<li class="c-sidebar-nav-dropdown"><a class="c-sidebar-nav-dropdown-toggle" href="javascript:void(0)">
        <i class="c-sidebar-nav-icon cil-education"></i> Students</a>
    <ul class="c-sidebar-nav-dropdown-items">
        @hasPermission(App\Models\Permission::CREATE_STUDENT_PERMISSION)
        <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{route('user.add', 'student')}}"> Add
                Student</a></li>
        @endHasPermission
        @hasPermission(App\Models\Permission::VIEW_STUDENT_PERMISSION)
        <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{route('user.search', strtolower(App\Models\User::USER_TYPE_STUDENT))}}"> View Students</a></li>
        @endHasPermission
        @hasPermission(App\Models\Permission::BULK_UPLOAD_STUDENT_PERMISSION)
        <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{route('user.upload', strtolower(\App\Models\User::USER_TYPE_STUDENT))}}"> Upload Students</a></li>
        @endHasPermission
    </ul>
</li>
@endHasPermissionAny

@hasPermissionAny([
App\Models\Permission::CREATE_TEACHER_PERMISSION,
App\Models\Permission::VIEW_TEACHER_PERMISSION,
App\Models\Permission::BULK_UPLOAD_TEACHER_PERMISSION,
])
<li class="c-sidebar-nav-dropdown"><a class="c-sidebar-nav-dropdown-toggle" href="javascript:void(0)">
        <i class="c-sidebar-nav-icon cil-user-female"></i> Teachers</a>
    <ul class="c-sidebar-nav-dropdown-items">
        @hasPermission(App\Models\Permission::CREATE_TEACHER_PERMISSION)
        <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{route('user.add', 'teacher')}}"> Add Teacher</a></li>
        @endHasPermission

        @hasPermission(App\Models\Permission::SEARCH_TEACHER_PERMISSION)
        <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{route('user.search', 'teacher')}}"> View Teachers</a></li>
        @endHasPermission

        @hasPermission(App\Models\Permission::BULK_UPLOAD_TEACHER_PERMISSION)
        <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{route('user.upload', strtolower(\App\Models\User::USER_TYPE_TEACHER))}}"> Upload Teachers</a></li>
        @endHasPermission
    </ul>
</li>
@endHasPermissionAny

@hasPermissionAny([
App\Models\Permission::INDIVIDUAL_PERFORMANCE_REPORT_PERMISSION,
App\Models\Permission::COURSE_PERFORMANCE_REPORT_PERMISSION
])
<li class="c-sidebar-nav-dropdown"><a class="c-sidebar-nav-dropdown-toggle" href="javascript:void(0)">
        <i class="c-sidebar-nav-icon cil-spreadsheet"></i> Report </a>
    <ul class="c-sidebar-nav-dropdown-items">
        @hasPermission(App\Models\Permission::INDIVIDUAL_PERFORMANCE_REPORT_PERMISSION)
        <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{route('report.individual-performance')}}">
                Individual Performance Report</a></li>
        @endHasPermission

    </ul>
</li>
@endHasPermissionAny
