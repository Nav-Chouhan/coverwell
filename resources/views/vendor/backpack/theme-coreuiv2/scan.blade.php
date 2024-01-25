@extends(backpack_view('blank'))
@section('content')
<div class="card">
  <div class="card-header">
    <form id="barcodescan">
      {{ csrf_field() }}
      <input id="barcode" class="form-control" placeholder="barcode" required="true" autofocus="true" onfocusout="document.getElementsByName('barcode')[0].focus();" name="barcode" type="text" value="" autocomplete="off">
      <input type='submit' style="display:none;" />
    </form>
  </div>
  <div class="card-body">
    <div class="row">
      <div class="col-md-4 col-sm-12">
        <center><img id="photo" src="data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22200%22%20height%3D%22200%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20200%20200%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_16efe7f6fd4%20text%20%7B%20fill%3Argba(255%2C255%2C255%2C.75)%3Bfont-weight%3Anormal%3Bfont-family%3AHelvetica%2C%20monospace%3Bfont-size%3A10pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_16efe7f6fd4%22%3E%3Crect%20width%3D%22200%22%20height%3D%22200%22%20fill%3D%22%23777%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2274.4296875%22%20y%3D%22104.5609375%22%3E200x200%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E">
        </center>
      </div>
      <div class="col-md-8 col-sm-12">
        <ul class="list-group list-group-flush">
          <li class="list-group-item text-center">
            <h1 id="info">INFORMATION</h1>
          </li>
          <li class="list-group-item text-center">
            <h1 id="barcode_val">BARCODE</h1>
          </li>
          <li class="list-group-item text-center">
            <h1 id="name">Name</h1>
          </li>
          <li class="list-group-item text-center">
            <h1 id="company">Company</h1>
          </li>
          <li class="list-group-item text-center">
            <h1 id="category">Category</h1>
          </li>
          <li class="list-group-item text-center">
            <h1 id="location">Location</h1>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
<audio id="error">
  <source id="soundSrc" src="/error.wav" type="audio/mpeg">
</audio>
<audio id="ok">
  <source id="soundSrc" src="/ok.mp3" type="audio/mpeg">
</audio>
@endsection
@push('after_scripts')
<script type="text/javascript">
  $('#barcodescan').on('submit', function(e) {
    e.preventDefault();
    const barcode = $("#barcode").val();
    $("#barcode").val('');
    $.ajax({
      type: "GET",
      url: "{{backpack_url('visitor')}}/" + barcode + "/scan",
      //data: form.serialize(),
      dataType: 'json',
      success: function(data, status) {
        $('audio#ok')[0].play();
        $("#photo").attr("src", data.photo);
        $("#barcode_val").html(data.barcode);
        $("#name").html(data.name);
        $("#company").html(data.company);
        $("#category").html(data.category);
        $("#location").html(data.location);
        if (data.error) {
          $(".list-group-item").css('color', 'red');
          $("#info").html(data.error);
        } else {
          $("#info").html("INFORMATION");
          $(".list-group-item").css('color', 'blue');
        }
      },
      error: function(request, status, error) {
        $('audio#error')[0].play()
        $(".list-group-item").css('color', 'red');
        switch (request.status) {
          case 0:
            $("#info").html("Check Internet!!");
            break
          case 401:
            window.location.reload();
            break;
          case 404:
            $("#photo").attr("src", "data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22200%22%20height%3D%22200%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20200%20200%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_16efe7f6fd4%20text%20%7B%20fill%3Argba(255%2C255%2C255%2C.75)%3Bfont-weight%3Anormal%3Bfont-family%3AHelvetica%2C%20monospace%3Bfont-size%3A10pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_16efe7f6fd4%22%3E%3Crect%20width%3D%22200%22%20height%3D%22200%22%20fill%3D%22%23777%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2274.4296875%22%20y%3D%22104.5609375%22%3E200x200%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E");
            $("#info").html("NOT FOUND");
            $("#barcode_val").html("");
            $("#name").html("");
            $("#company").html("");
            $("#category").html("");
            $("#location").html("");
            $("#location").html("");
            break;
          default:
            $("#barcode_val").html("");
            $("#name").html("");
            $("#company").html("");
            $("#category").html("");
            $("#location").html("");
            $("#location").html("");
            $("#info").html(error);
        }
      }
    });
  });
</script>
<style>
  #photo {
    width: 100%;
    max-width: 400px;
  }

  @media (max-width: 480px) {
    #photo {
      width: 100%;
    }

    h1 {
      font-size: 1rem;
    }

    .list-group-item {
      padding: 0rem 0rem;
    }
  }
</style>
@endpush