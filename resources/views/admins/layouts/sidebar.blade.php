<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-item">
        <a href="{{ route('admin-dashboard') }}" class="nav-link">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>
            Dashboard
            <span class="right badge badge-danger">New</span>
            </p>
        </a>
        </li>
        <li class="nav-item">
        <a href="{{ route('admin.user.index') }}" class="nav-link">
            <i class="nav-icon fas fa-th"></i>
            <p>
            Management User
            <span class="right badge badge-danger">New</span>
            </p>
        </a>
        </li>
    </ul>
</nav>