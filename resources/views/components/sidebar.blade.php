<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="index.html">Stisla</a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="index.html">St</a>
        </div>
        <ul class="sidebar-menu">
            <li class="menu-header">Dashboard</li>
            <li>
                <a class="nav-link" href="{{ route('dashboard.index') }}">
                    <i class="fas fa-fire"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="menu-header">TODO</li>
            <li>
                <a class="nav-link" href="{{ route('task.index') }}">
                    <i class="fas fa-fire"></i>
                    <span>Task</span>
                </a>
            </li>
            @if ($user->hasAnyRole(['Boss', 'Manager']))
                <li>
                    <a class="nav-link" href="{{ route('additional-task.index') }}">
                        <i class="fas fa-fire"></i>
                        <span>Additional Task</span>
                    </a>
                </li>
            @endif
        </ul>

    </aside>
</div>
