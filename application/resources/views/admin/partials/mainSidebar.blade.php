<!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->
<div class="collapse navbar-collapse navbar-ex1-collapse">
    <ul class="nav navbar-nav side-nav">
        {{do_action_ref_array('admin_menu', [&$AdminSidebarMenu])}}
        @include('admin.partials.mainSidebarMenu', ['items' =>  $AdminSidebarMenu->sortBy('order')->roots()])
    </ul>
</div>