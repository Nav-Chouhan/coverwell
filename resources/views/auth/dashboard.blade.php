@extends('layouts.app')
@section('content')
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

<?php $logedinuser = Auth::user(); ?>
<ul class="dashlogout">
    <li><a class="nav-link" href="{{ route('signout') }}">&nbsp;Logout</a></li>
</ul>
<div class="row dashrow justify-content-center">
    <div class="col-md-7">
        <div class="card dashboard">
            <h3 class="card-header text-center dashboardbackground">Visitors</h3>
            <div class="card-body">
                <form method="POST" action="/search" name="search" role="search">
                    @csrf
                    <input type="hidden" name="visitorId" id="visitorId" />
                    <div class=" form-group dashboardform row">
                        <label for="email" class="col-md-6 col-form-label dashbordlabel text-md-right nsgen">Search by Contact No</label>
                        <div class="col-md-4">
                            <input type="text" id="visSearch" class="form-control " name="txtsearch" required autofocus>
                        </div>
                        <div class="col-md-2">
                            <button id="btnSearch" class="form-control" onClick="return false;" name="btnSearch">Search</button>
                        </div>
                    </div>
                </form>
                <table style="width:100%;" id="tableRecs">
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="carInfoModel" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content dashcontent">
            <div class="modal-header dashboardbackground dashcar">
                <button type="button" class="close dashclose" data-dismiss="modal"><i class="bi bi-x-circle-fill"></i></button>
                <h4 class="modal-title">Car Deatils</h4>
            </div>
            <div class="modal-body">
                <div class="d-flex dashdetails">
                    <p class="dashenter">Please enter car details</p>
                    <p class="dashcarno"><input type="text" id="carNoField" name="carno"></p>
                </div>
                <div style="float:right;" class="dashfloat">
                    <button type="button" id="sendCarInfo" class="btn btn-info btn-lg dashsend">Send</button>
                    <button type="button" id="carPopUpClose" class="btn btn-default dashsend" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="roomInfoModel" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content dashcontent">
            <div class="modal-header dashboardbackground dashcar">
                <button type="button" class="close dashclose" data-dismiss="modal"><i class="bi bi-x-circle-fill"></i></button>
                <h4 class="modal-title">Room</h4>
            </div>
            <div class="modal-body">
                <div class="d-flex dashdetails">
                    <p class="dashenter">Please enter room number</p>
                    <p class="dashcarno"><input type="text" id="roomNoField" name="roomno"></p>
                </div>
                <div style="float:right;" class="dashfloat">
                    <button type="button" id="sendRoomInfo" class="btn btn-info btn-lg dashsend">Update</button>
                    <button type="button" id="carPopUpClose" class="btn btn-default dashsend" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
<script>
    function setVisitorId(id, carNo, roomNo) {
        $("#visitorId").val(id);
        $("#carNoField").val(carNo);
        $("#roomNoField").val(roomNo);
    }
    function getRecs() {
        $("#tableRecs").html("")
        if ($("#visSearch").val() != "") {
            $.ajax({
                url: '/search-visitor?searchTerm=' + $("#visSearch").val(),
                type: "GET",
                success: function(data) {
                    let recsHtml = '';
                    if (data.results) {
                        data.results.map((rec, idx) => {
                            if (rec.showBtn == 'updater') {
                                window.location.href = '/internal/' + rec.id
                            }
                            recsHtml = recsHtml + '<tr class="dashname"><td>' + rec.name + '</td><td>' + rec.email + '</td><td>' + rec.contact + '</td>';
                            if (rec.showBtn == 'traveler')
                                recsHtml = recsHtml + '<td><button type="button" class="btn btn-info btn-lg nsregister dashboardbutton" data-toggle="modal" data-target="#carInfoModel" onClick="setVisitorId(\'' + rec.id + '\', \'' + rec.car_number + '\', \'' + rec.room_number + '\')">Departure</button></td>';
                            else if (rec.showBtn == 'hoteler')
                                recsHtml = recsHtml + '<td><button type="button" class="btn btn-info btn-lg nsregister dashboardbutton" data-toggle="modal" data-target="#roomInfoModel" onClick="setVisitorId(\'' + rec.id + '\', \'' + rec.car_number + '\', \'' + rec.room_number + '\')">Recieved</button></td>';
                            recsHtml = recsHtml + '</tr>';
                        })
                        $("#tableRecs").html(recsHtml)
                    }
                }
            });
        }
    }
    $('#visSearch').change(function() {
        getRecs();
    });
    $('#visSearch').keypress(function(event) {
        // Check if the Enter key is pressed (keyCode 13)
        if (event.keyCode === 13) {
            event.preventDefault(); // Prevent form submission or page reload
            getRecs();
        }
    });
    $("#sendCarInfo").click(function() {
        if ($("#carNoField").val() != "") {
            $.ajax({
                url: '/update-car-info',
                type: "POST",
                data: {
                    "carno": $("#carNoField").val(),
                    "visitorId": $("#visitorId").val(),
                    "_token": "{{ csrf_token() }}"
                },
                success: function(data) {
                    $("#carPopUpClose").trigger("click");
                }
            });
        }
    })
    $("#sendRoomInfo").click(function() {
        if ($("#roomNoField").val() != "") {
            $.ajax({
                url: '/update-room-info',
                type: "POST",
                data: {
                    "roomno": $("#roomNoField").val(),
                    "visitorId": $("#visitorId").val(),
                    "_token": "{{ csrf_token() }}"
                },
                success: function(data) {
                    $("#carPopUpClose").trigger("click");
                }
            });
        }
    })
</script>
@endsection