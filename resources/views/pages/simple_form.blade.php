<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ config('backpack.base.html_direction') }}">
<head>
  @include(backpack_view('inc.head'))
</head>
<body class="app flex-row align-items-center">

  @yield('header')

  <div class="container">
    <form>
      <div class="card">
                  <div class="card-header"><strong>{{$title}} </strong><small>Form</small></div>
        <div class="card-body">
          @if(in_array("hidden_barcode",$page->fields))
          <input class="form-control yes" id="hidden_barcode" name="hidden_barcode" type="hidden" value="{{ Request::get('b') }}">
          @endif
          @if(in_array("arrival_time",$page->fields))
          <div class="form-group">
            <label for="invite_code">Arrival Date & Time</label>
            <input type="datetime-local" name="arrival_time" id="arrival_time" value="{{$page['default_date_time']}}" class="form-control yes">
          </div>
          @endif
          @if(in_array("invite_code",$page->fields))
          <div class="form-group">
            <label for="invite_code">Invite Code</label>
            <input class="form-control yes" id="invite_code" name="invite_code" type="text" placeholder="Enter Invite Code">
          </div>
          @endif
          @if(in_array("name",$page->fields))
          <div class="form-group">
            <label for="name">Name</label>
            <input class="form-control yes" id="name" name="name" type="text" placeholder="Enter your name">
          </div>
          @endif
          @if(in_array("contact",$page->fields))
          <div class="form-group">
            <label for="name">Mobile</label>
            <input class="form-control yes" id="contact" name="contact" type="text" placeholder="Enter your mobile no">
          </div>
          @endif
          @if(in_array("company",$page->fields))
          <div class="form-group">
            <label for="company">Company</label>
            <input class="form-control yes" id="company" name="company" type="text" placeholder="Enter your company name">
          </div>
          @endif
          @if(in_array("membership_no",$page->fields))
          <div class="form-group">
            <label for="member_of">Association Member</label>
            <select class="form-control" onchange="document.getElementById('member_of').value = this.value">
              <option value="" class="option">Select</option>
              <option value="Sarafa" class="option">Sarafa</option>
              <option value="JAS" class="option">JAS</option>
              <option value="GJEPC" class="option">GJEPC</option>
            </select>
            <input type="hidden" class="yes" id="member_of" name="member_of">
          </div>
          <div class="form-group">
            <label for="media_gst_membership_no">Membership No</label>
            <input class="form-control yes" id="media_gst_membership_no" name="media_gst_membership_no" type="text" placeholder="Enter your membership number">
          </div>
          <div class="form-group">
            <label for="media_gst_membership_card">Membership Card</label>
            <input type="file" accept="image/*" class="form-control-file" id="media_gst_membership_card">
            <input type="hidden" class="yes" name="media_gst_membership_card">
          </div>
          @endif
          @if(in_array("address",$page->fields))
          <div class="form-group">
            <label for="address">Address</label>
            <input class="form-control yes" id="address" name="address" type="text" placeholder="Enter your address">
          </div>
          @endif
          @if(in_array("city",$page->fields))
          <div class="form-group">
            <label for="city">City</label>
            <input class="form-control yes" name="city" id="city" type="text" placeholder="Enter your city">
          </div>
          @endif


          @if(in_array("photo",$page->fields))
          <div class="form-group">
            <label for="photo">Photo(Image)</label>
            <input type="file" accept="image/*" class="form-control-file" id="photo">
            <input type="hidden" class="yes" name="photo">
          </div>
          @endif
          @if(in_array("idproof",$page->fields))
          <div class="form-group">
            <label for="idproof">ID Proof(Image)</label>
            <input type="file" accept="image/*" class="form-control-file" id="idproof">
            <input type="hidden" class="yes" name="idproof">
          </div>
          <div class="form-group">
            <label for="idproof_back">ID Proof Back(Image)</label>
            <input type="file" accept="image/*" class="form-control-file" id="idproof_back">
            <input type="hidden" class="yes" name="idproof_back">
          </div>
          @endif
          @if(in_array("vaccine",$page->fields))
          <div class="form-group">
            <label for="vaccine">Vaccine Certificate(II Dose Image**)</label>
            <input type="file" accept="image/*" class="form-control-file" id="vaccine">
            <input type="hidden" class="yes" name="vaccine">
          </div>
          @endif
          <div class="card border-danger text-danger">
            <div class="card-header">Error</div>
            <div id="errors" class="card-body">

            </div>
          </div>
        </div>
        <div class="card-footer">
          <button id="submit" class="btn btn-primary btn-lg btn-block" type="button"><i class="fa fa-dot-circle-o"></i> Submit</button>
        </div>
      </div>
    </form>
  </div>



  @yield('before_scripts')
  @stack('before_scripts')

  @include(backpack_view('inc.scripts'))

  @yield('after_scripts')
  @stack('after_scripts')

  <script src="<?= asset('/js/compress.js') ?>"></script>
  <script type="text/javascript">
    $("#errors").parent().hide();
    $('#submit').click(function() {
      $("#errors").parent().hide();
      form = {
        slug: "{{$page->slug}}",
      };
      $('.yes').each(function() {
        form[this.name] = this.value;
      });

      $.ajax({
        type: "POST",
        url: "{!!$page->api!!}",
        data: form,
        dataType: 'json',
        success: function(data, status) {
          console.log(data);
          alert(data.message);
          location.reload();
        },
        error: function(request, status, error) {
          switch (request.status) {
            case 422:
              $("#errors").parent().show();
              var str = "";
              $.each(request.responseJSON.errors, function(key, value) {
                str += value + "<br/>";
              });
              $("#errors").html(str);
              break;
            default:
              alert(error);
          }
        }
      });
    });

    const compress = new Compress();
    $('input[type="file"]').each(function() {

      this.addEventListener('change', (evt) => {
        //console.log(this.id);
        //const files = [...evt.target.files]
        const files = [evt.target.files[0]];
        compress.compress(files, {
          size: 1,
          quality: 0.90,
          maxWidth: 500,
          maxHeight: 1000,
          resize: true
        }).then((images) => {
          const img = images[0];
          console.log(this.id);
          $('input[name=' + this.id + ']').val(`${img.prefix}${img.data}`);
        });
      }, false);

    });
    window.onerror = function(msg, url, line) {
      var form = {
        msg: msg,
        url: url,
        line: line,
        user_agent: navigator.userAgent
      };
      $.ajax({
        type: "POST",
        url: "{!!url('api/error')!!}",
        data: form,
      });
    };
  </script>
</body>

</html>