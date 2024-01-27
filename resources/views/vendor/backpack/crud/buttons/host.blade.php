@if ($crud->hasAccess('host'))
@if($entry->hosted_buyer == '0')
<a onclick="$(this).find('span').html('Hosted')" href="{{ url($crud->route.'/'.$entry->getKey().'/host') }}" class="btn btn-sm btn-link"><i class="las la-hotel"></i><span>Host</span></a>
@else
@if (
($entry->printed_on != null && \Carbon\Carbon::parse($entry->printed_on)->diffInMinutes(\Carbon\Carbon::now()) > 10)
&& !backpack_user()->can('PrintDuplicate')
)

@else
<a onclick="$(this).find('span').html('Host')" href="{{ url($crud->route.'/'.$entry->getKey().'/host') }}" class="btn btn-sm btn-link"><i class="las la-hotel"></i> Hosted</a>
@endif
@endif
@endif