@extends(backpack_view('blank'))

@php
	// ---------------------
	// JUMBOTRON widget demo
	// ---------------------
	// Widget::add([
 //        'type'        => 'jumbotron',
 //        'name' 		  => 'jumbotron',
 //        'wrapperClass'=> 'shadow-xs',
 //        'heading'     => trans('backpack::base.welcome'),
 //        'content'     => trans('backpack::base.use_sidebar'),
 //        'button_link' => backpack_url('logout'),
 //        'button_text' => trans('backpack::base.logout'),
 //    ])->to('before_content')->makeFirst();

	// -------------------------
	// FLUENT SYNTAX for widgets
	// -------------------------
	// Using the progress_white widget
	// 
	// Obviously, you should NOT do any big queries directly in the view.
	// In fact, it can be argued that you shouldn't add Widgets from blade files when you
	// need them to show information from the DB.
	// 
	// But you do whatever you think it's best. Who am I, your mom?
	$categoryCount = App\Models\VisitorCategory::count();
	$visitorCount = App\Models\Visitor::count();
	$companyCount = App\Models\Company::count();
	$lastRegistration = App\Models\Visitor::orderBy('registered_on', 'DESC')->first();
	$lastRegistration = $lastRegistration?$lastRegistration->registered_on:null;
 
 
 
	//$lastArticleDaysAgo = \Carbon\Carbon::now()->day;
 
 	// notice we use Widget::add() to add widgets to a certain group
	Widget::add()->to('before_content')->type('div')->class('row')->content([
		// notice we use Widget::make() to add widgets as content (not in a group)
		Widget::make()
			->type('progress')
			->class('card border-0 text-white bg-primary')
			->progressClass('progress-bar')
			->value($visitorCount)
			->description('Registered Visitors.')
			->progress(100*(int)$visitorCount/1000)
			->hint(15000-$visitorCount.' more until next milestone.'),
		// alternatively, to use widgets as content, we can use the same add() method,
		// but we need to use onlyHere() or remove() at the end
		Widget::add()
		    ->type('progress')
		    ->class('card border-0 text-white bg-success')
		    ->progressClass('progress-bar')
		    ->value($companyCount)
		    ->description('Companies.')
		    ->progress(80)
		    ->hint('Great! Don\'t stop.')
		    ->onlyHere(), 
		// alternatively, you can just push the widget to a "hidden" group
		Widget::make()
			->group('hidden')
		    ->type('progress')
		    ->class('card border-0 text-white bg-warning')
		    ->value($lastRegistration?\Carbon\Carbon::parse($lastRegistration)->diffForHumans():"0")
		    ->progressClass('progress-bar')
		    ->description('Last Registration.')
		    ->progress(30)//$lastRegistration->diffInHours())
		    ->hint('Post an article every 3-4 days.'),
		// both Widget::make() and Widget::add() accept an array as a parameter
		// if you prefer defining your widgets as arrays
	    Widget::make([
			'type' => 'progress',
			'class'=> 'card border-0 text-white bg-dark',
			'progressClass' => 'progress-bar',
			'value' => $categoryCount,
			'description' => 'Categories.',
			'progress' => (int)$categoryCount/20*100,
			'hint' => $categoryCount>20?'Try to stay under 75 products.':'Good. Good.',
		]),
	]);

    $widgets['after_content'][] = [
	  'type' => 'div',
	  'class' => 'row',
	  'content' => [ // widgets 
	       [
			  'type' => 'card',
			  // 'wrapperClass' => 'col-sm-6 col-md-4', // optional
			  // 'class' => 'card bg-dark text-white', // optional
			  'content' => [
			      'header' => 'Some card title', // optional
			      'body' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis non mi nec orci euismod venenatis. Integer quis sapien et diam facilisis facilisis ultricies quis justo. Phasellus sem <b>turpis</b>, ornare quis aliquet ut, volutpat et lectus. Aliquam a egestas elit. <i>Nulla posuere</i>, sem et porttitor mollis, massa nibh sagittis nibh, id porttitor nibh turpis sed arcu.',
			  ]
			],
			[
			  'type' => 'card',
			  // 'wrapperClass' => 'col-sm-6 col-md-4', // optional
			  // 'class' => 'card bg-dark text-white', // optional
			  'content' => [
			      'header' => 'Another card title', // optional
			      'body' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis non mi nec orci euismod venenatis. Integer quis sapien et diam facilisis facilisis ultricies quis justo. Phasellus sem <b>turpis</b>, ornare quis aliquet ut, volutpat et lectus. Aliquam a egestas elit. <i>Nulla posuere</i>, sem et porttitor mollis, massa nibh sagittis nibh, id porttitor nibh turpis sed arcu.',
			  ]
			],
			[
			  'type' => 'card',
			  // 'wrapperClass' => 'col-sm-6 col-md-4', // optional
			  // 'class' => 'card bg-dark text-white', // optional
			  'content' => [
			      'header' => 'Yet another card title', // optional
			      'body' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis non mi nec orci euismod venenatis. Integer quis sapien et diam facilisis facilisis ultricies quis justo. Phasellus sem <b>turpis</b>, ornare quis aliquet ut, volutpat et lectus. Aliquam a egestas elit. <i>Nulla posuere</i>, sem et porttitor mollis, massa nibh sagittis nibh, id porttitor nibh turpis sed arcu.',
			  ]
			],
	  ]
	];
    $widgets['after_content'][] = [
	  'type'         => 'alert',
	  'class'        => 'alert alert-warning bg-dark border-0 mb-4',
	  'heading'      => 'Yet another card title',
	  'content'      => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis non mi nec orci euismod venenatis. Integer quis sapien et diam facilisis facilisis ultricies quis justo. Phasellus sem <b>turpis</b>, ornare quis aliquet ut, volutpat et lectus. Aliquam a egestas elit. <i>Nulla posuere</i>, sem et porttitor mollis, massa nibh sagittis nibh, id porttitor nibh turpis sed arcu.',
	  'close_button' => true, // show close button or not
	];

    $widgets['before_content'][] = [
	  'type' => 'div',
	  'class' => 'row',
	  'content' => [ // widgets 
		  	[ 
		        'type' => 'chart',
		        'wrapperClass' => 'col-md-6',
		        // 'class' => 'col-md-6',
		        'controller' => \App\Http\Controllers\Admin\Charts\LatestUsersChartController::class,
				'content' => [
				    'header' => 'New Users Past 7 Days', // optional
				    // 'body' => 'This chart should make it obvious how many new users have signed up in the past 7 days.<br><br>', // optional
					
		    	]
	    	],
	    	[ 
		        'type' => 'chart',
		        'wrapperClass' => 'col-md-6',
		        // 'class' => 'col-md-6',
		        'controller' => \App\Http\Controllers\Admin\Charts\NewEntriesChartController::class,
				'content' => [
				    'header' => 'New Entries', // optional
				    'body' => 'This chart should make it obvious how many new users have signed up in the past 7 days.<br><br>', // optional
		    	]
	    	],
    	]
	];

    $widgets['after_content'][] = [
	  'type' => 'div',
	  'class' => 'row',
	  'content' => [ // widgets 

	    	[ 
		        'type' => 'chart',
		        'wrapperClass' => 'col-md-4',
		        // 'class' => 'col-md-6',
		        'controller' => \App\Http\Controllers\Admin\Charts\Pies\ChartjsPieController::class,
				'content' => [
				    'header' => 'Pie Chart - Chartjs', // optional
				    // 'body' => 'This chart should make it obvious how many new users have signed up in the past 7 days.<br><br>', // optional
		    	]
	    	],
	    	[ 
		        'type' => 'chart',
		        'wrapperClass' => 'col-md-4',
		        // 'class' => 'col-md-6',
		        'controller' => \App\Http\Controllers\Admin\Charts\Pies\EchartsPieController::class,
				'content' => [
				    'header' => 'Pie Chart - Echarts', // optional
				    // 'body' => 'This chart should make it obvious how many new users have signed up in the past 7 days.<br><br>', // optional
		    	]
	    	],
	    	[ 
		        'type' => 'chart',
		        'wrapperClass' => 'col-md-4',
		        // 'class' => 'col-md-6',
				'controller' => \App\Http\Controllers\Admin\Charts\Pies\HighchartsPieController::class,
				'content' => [
				    'header' => 'Pie Chart - Highcharts', // optional
				    // 'body' => 'This chart should make it obvious how many new users have signed up in the past 7 days.<br><br>', // optional
		    	]
	    	],

	  ]
	];


    $widgets['after_content'][] = [
	  'type' => 'div',
	  'class' => 'row',
	  'content' => [ // widgets 

	    	[ 
		        'type' => 'chart',
		        'wrapperClass' => 'col-md-6',
		        // 'class' => 'col-md-6',
		        'controller' => \App\Http\Controllers\Admin\Charts\Lines\ChartjsLineChartController::class,
				'content' => [
				    'header' => 'Line Chart - Chartjs', // optional
				    // 'body' => 'This chart should make it obvious how many new users have signed up in the past 7 days.<br><br>', // optional
		    	]
	    	],
	    	[ 
		        'type' => 'chart',
		        'wrapperClass' => 'col-md-6',
		        // 'class' => 'col-md-6',
		        'controller' => \App\Http\Controllers\Admin\Charts\Lines\EchartsLineChartController::class,
				'content' => [
				    'header' => 'Line Chart - Echarts', // optional
				    // 'body' => 'This chart should make it obvious how many new users have signed up in the past 7 days.<br><br>', // optional
		    	]
	    	],
	    	[ 
		        'type' => 'chart',
		        'wrapperClass' => 'col-md-6',
		        // 'class' => 'col-md-6',
		        'controller' => \App\Http\Controllers\Admin\Charts\Lines\HighchartsLineChartController::class,
				'content' => [
				    'header' => 'Line Chart - Highcharts', // optional
				    // 'body' => 'This chart should make it obvious how many new users have signed up in the past 7 days.<br><br>', // optional
		    	]
	    	],
	    	[ 
		        'type' => 'chart',
		        'wrapperClass' => 'col-md-6',
		        // 'class' => 'col-md-6',
		        'controller' => \App\Http\Controllers\Admin\Charts\Lines\FrappeLineChartController::class,
				'content' => [
				    'header' => 'Line Chart - Frappe', // optional
				    // 'body' => 'This chart should make it obvious how many new users have signed up in the past 7 days.<br><br>', // optional
		    	]
	    	],
	    	

    	]
	];
@endphp

@section('content')
	{{-- In case widgets have been added to a 'content' group, show those widgets. --}}
	@include(backpack_view('inc.widgets'), [ 'widgets' => app('widgets')->where('group', 'content')->toArray() ])
@endsection
