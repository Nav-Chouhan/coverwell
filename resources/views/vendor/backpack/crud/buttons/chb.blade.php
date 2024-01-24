<a href="javascript:void(0)" onclick="chbEntry(this)" data-route="{{ url('open-chb/'.$entry->getKey()) }}" class="btn btn-sm btn-link" data-button-type="chb"><i class="la la-trash"></i>CHB</a>


{{-- Button Javascript --}}
{{-- - used right away in AJAX operations (ex: List) --}}
{{-- - pushed to the end of the page, after jQuery is loaded, for non-AJAX operations (ex: Show) --}}
@push('after_scripts') @if (request()->ajax()) @endpush @endif
<script>
    if (typeof deleteEntry != 'function') {
        $("[data-button-type=chb]").unbind('click');

        function chbEntry(button) {
            // ask for confirmation before deleting an item
            // e.preventDefault();
            var route = $(button).attr('data-route');

            var span = document.createElement("span");
            span.innerHTML = "Testno  sporocilo za objekt <b>test</b>";
            var title = 'Hosted Buyer Details'

            $.ajax({
                url: route,
                type: 'GET',
                success: function(result) {
                    span.innerHTML = result.html
                }
            });

            swal({
                html: true,
                title: title,
                content: span,
                button: {
                    text: "OK",
                    value: true,
                    visible: true,
                    className: "",
                    closeModal: true,
                }
            })

        }
    }

    // make it so that the function above is run after each DataTable draw event
    // crud.addFunctionToDataTablesDrawEventQueue('deleteEntry');
</script>
@if (!request()->ajax()) @endpush @endif