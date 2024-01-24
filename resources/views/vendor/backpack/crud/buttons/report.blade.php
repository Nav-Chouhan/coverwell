<a href="{{ url($crud->route.'/report') }}" target="_blank" data-button-type="generateReportsBtn" class="btn btn-primary"><span class="ladda-label"><i class="la la-cloud-download-alt"></i> Export Reports</span></a>
@push('after_scripts') @if (request()->ajax()) @endpush @endif
<script>
    $("[data-button-type=generateReportsBtn]").unbind('click');
    // make it so that the function above is run after each DataTable draw event
    // crud.addFunctionToDataTablesDrawEventQueue('deleteEntry');
</script>
@if (!request()->ajax()) @endpush @endif