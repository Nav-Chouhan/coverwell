 
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>
@hasanyrole(['Admin|Operator'])
<x-backpack::menu-item title='Scan' icon='la la-barcode' :link="backpack_url('visitor/scan')" />
<x-backpack::menu-item title="Visitors" icon="la la-id-badge" :link="backpack_url('visitor')" />
<x-backpack::menu-item title="Companies" icon="la la-building" :link="backpack_url('company')" />
<x-backpack::menu-item title="Hotels" icon="la la-hotel" :link="backpack_url('hotel')" />
@can('Invites')
<x-backpack::menu-item title="Invites" icon="la la-envelope-o" :link="backpack_url('invite')" />
@endcan
@endrole

@hasanyrole(['Admin'])
  <x-backpack::menu-item :title="trans('backpack::crud.file_manager')" icon="la la-files-o" :link="backpack_url('elfinder')" />
  <x-backpack::menu-dropdown title="Setup" icon="la la-cogs">
    <x-backpack::menu-dropdown-header title="Setup" />
    <x-backpack::menu-item title="Locations" icon="la la-map-marked" :link="backpack_url('location')" />
    <x-backpack::menu-item title='Visitor categories' icon='la la-user-tag' :link="backpack_url('visitor-category')" />
    <x-backpack::menu-item title="Professions" icon="la la-user-tie" :link="backpack_url('profession')" />
    <x-backpack::menu-item title="Fields" icon="la la-list-alt" :link="backpack_url('field')" />
    <x-backpack::menu-item title="Durations" icon="la la-calendar-week" :link="backpack_url('duration')" />
    <x-backpack::menu-item title="Operations" icon="la la-wrench" :link="backpack_url('operations')" />
    <x-backpack::menu-item title='Settings' icon='la la-cog' :link="backpack_url('setting')" />
  </x-backpack::menu-dropdown>
@endrole

<x-backpack::menu-dropdown title="CMS" icon="la la-newspaper-o">
    <x-backpack::menu-dropdown-header title="CMS" />
    <x-backpack::menu-item title='Articles' icon='la la-newspaper-o' :link="backpack_url('article')" />
    <x-backpack::menu-item title='Categories' icon='la la-list' :link="backpack_url('category')" />
    <x-backpack::menu-item title='Tags' icon='la la-tag' :link="backpack_url('tag')" />
    <x-backpack::menu-item title='Pages' icon='la la-file-o' :link="backpack_url('page')" />
    <x-backpack::menu-item title='Menu' icon='la la-list' :link="backpack_url('menu-item')" />
</x-backpack::menu-dropdown>
<x-backpack::menu-dropdown title="Authentication" icon="la la-users">
    <x-backpack::menu-dropdown-header title="Authentication" />
    <x-backpack::menu-dropdown-item title="Users" icon="la la-user" :link="backpack_url('user')" />
    <x-backpack::menu-dropdown-item title="Roles" icon="la la-group" :link="backpack_url('role')" />
    <x-backpack::menu-dropdown-item title="Permissions" icon="la la-key" :link="backpack_url('permission')" />
</x-backpack::menu-dropdown>
<x-backpack::menu-dropdown title="Reports" icon="la la-bar-chart">
    <x-backpack::menu-dropdown-header title="Reports" />
    <x-backpack::menu-dropdown-item title="Registered" icon="la la-registered" :link="backpack_url('report/registered')" />
    <x-backpack::menu-dropdown-item title="Printed" icon="la la-print" :link="backpack_url('report/printed')" />
    <x-backpack::menu-dropdown-item title="Inside" icon="la la-sign-in" :link="backpack_url('report/inside')" />
    <x-backpack::menu-dropdown-item title="Footfall" icon="la la-area-chart" :link="backpack_url('report/inside')" />
</x-backpack::menu-dropdown>
<x-backpack::menu-item title='Logs' icon='la la-terminal' :link="backpack_url('log')" />
<!--
<x-backpack::menu-item title="Cities" icon="la la-question" :link="backpack_url('city')" />
<x-backpack::menu-item title="States" icon="la la-question" :link="backpack_url('state')" /> -->