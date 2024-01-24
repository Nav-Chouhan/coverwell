@if ($crud->hasAccess('export'))
<a href="{{ url($crud->route.'/export') }}" target="_blank" data-a-href="{{ url($crud->route.'/export') }}" onclick="exportRecs()" data-button-type="exportBtn" class="btn btn-primary" data-style="zoom-in"><span class="ladda-label"><i class="la la-cloud-download-alt"></i> Export {{ $crud->entity_name_plural }}</span></a>
@endif
@push('after_scripts') @if (request()->ajax()) @endpush @endif
<script>
	$("[data-button-type=exportBtn]").unbind('click');

	function exportRecs() {
		const queryString = window.location.search;
		$("[data-button-type=exportBtn]").attr("href", $("[data-button-type=exportBtn]").attr("data-a-href") + queryString)
	}
	// make it so that the function above is run after each DataTable draw event
	// crud.addFunctionToDataTablesDrawEventQueue('deleteEntry');
</script>
@if (!request()->ajax()) @endpush @endif