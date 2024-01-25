@if ($crud->hasAccess('print'))
	@if($entry->printed_on == null)
	<a onclick="$(this).find('span').html(' RePrint')" href="{{ url($crud->route.'/'.$entry->getKey().'/print') }}" target="print_frame" class="btn btn-sm btn-link"><i class="las la-print"></i><span>Print</span></a>
	@else
		@if (($entry->printed_on != null &&  \Carbon\Carbon::parse($entry->printed_on)->diffInMinutes(\Carbon\Carbon::now()) > 10) && !backpack_user()->can('PrintDuplicate'))	
		@else
			<a href="{{ url($crud->route.'/'.$entry->getKey().'/print') }}" target="print_frame" class="btn btn-sm btn-link"><i class="las la-print"></i> RePrint</a>
		@endif
	@endif
@endif
