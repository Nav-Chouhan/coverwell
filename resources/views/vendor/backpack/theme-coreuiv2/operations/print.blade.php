<html>

<head>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0px;
            padding-left:1mm;
        }

        @media print {
            #bg {
                visibility: hidden;
            }
        }

        #qr {
            z-index: 2;
        }
        @if(isset($_GET['l']))

            #bg {
                display:none;
            }

            #content {
                width: 52.5mm;
                position: absolute;
                top: 45mm;
            }

            #photo {
                position: absolute;
                z-index: 2;
                width: 27mm;
                top: 5mm;
                left: 14mm;
                border-radius: 5%;
                object-fit: cover;
            }

        @else

            #bg {
                display:none;
            }

            #content {
                width: 52.5mm;
                position: absolute;
                top: 85mm;
            }

            #photo {
                position: absolute;
                z-index: 2;
                width: 27mm;
                top: 40mm;
                left: 14mm;
                border-radius: 5%;
                object-fit: cover;
            }
        

        @endif

        #name {
            z-index: 2;
            font-family: 'Montserrat', sans-serif;
            font-size: 12pt;
            text-transform: uppercase;
        }

        #company {
            z-index: 2;
            font-family: 'Montserrat', sans-serif;
            font-size: 8pt;
            text-transform: uppercase;
        }

    </style>
</head>

<body>

    <img id="bg" src="{{ url($entry->category->img) }}" />

    @if ($entry->photo)
        <img id="photo" src="{!! $entry->getPhoto64() !!}" />
    @else
        <img id="photo" style="visibility: hidden;" src="{!! $entry->getPhoto64() !!}" />
    @endif
    <div id="content">
        <center>
            <span id="name">{{ $entry->name }}</span>
        </center>
        @if($entry->company)
        <center>
            <span id="company">({{$entry->company->name}})</span>
        </center>
        @endif
        <center>
            @if (strlen($entry->barcode) < 7)
                {!! DNS1D::getBarcodeSVG($entry->barcode, 'C128', 2, 35) !!}
            @else
                {!! DNS1D::getBarcodeSVG($entry->barcode, 'C128', 1.2, 35) !!}
            @endif
        </center>
    </div>

</body>
<script type="text/javascript">
	window.print();
</script>
</html>
