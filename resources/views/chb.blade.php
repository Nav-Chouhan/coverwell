@if ($exhibitorName)
<div class="chbtable">
    <div class="chbrow header">
        <div class="chbcell">Exhibitor Code</div>
        <div class="chbcell">Exhibitor Name</div>
        <div class="chbcell">Company Name</div>
    </div>
    <div class="chbrow">
        <div class="chbcell">{{ $exhibitorName['barcode'] }}</div>
        <div class="chbcell">{{ $exhibitorName['name'] }}</div>
        <div class="chbcell">{{ $exhibitorName['company']['name'] }}</div>
    </div>
</div>
@elseif ($visitors)
<div class="chbtable">
    <div class="chbrow header">
        <div class="chbcell">Buyer Code</div>
        <div class="chbcell">Buyer Name</div>
        <div class="chbcell">Buyer Company</div>
    </div>
    @foreach($visitors as $visitor)
    <div class="chbrow">
        <div class="chbcell">{{ $visitor['barcode'] }}</div>
        <div class="chbcell">{{ $visitor['name'] }}</div>
        <div class="chbcell">{{ $visitor['company']['name'] }}</div>
    </div>
    @endforeach
</div>
@else
<div class="chbtable">
    <div class="chbrow header">
        <div class="chbcell">Hosted By JAS</div>
    </div>
</div>
@endif
<style>
    .chbtable {
        display: table;
        width: 100%;
    }

    .chbrow {
        display: table-row;
    }

    .chbcell {
        display: table-cell;
        padding: 5px 10px;
        border: 1px solid #ccc;
    }
</style>