<?php

namespace App\Http\Controllers\Admin\Charts;


use Backpack\CRUD\app\Http\Controllers\ChartController;
use ConsoleTVs\Charts\Classes\Chartjs\Chart;
use App\Models\Visitor;
use App\Models\Location;
use App\Models\VisitorCategory;

class InsideVisitorsChartController extends ChartController
{
    public function setup()
    {
        $this->chart = new Chart();

        // MANDATORY. Set the labels for the dataset points
        $labels = [];
        for ($days_backwards = 0; $days_backwards >= 0; $days_backwards--) {
            if ($days_backwards == 1) {
            }
            $labels[] = $days_backwards . ' days ago';
        }
        $this->chart->labels($labels);

        // RECOMMENDED. Set URL that the ChartJS library should call, to get its data using AJAX.
        $this->chart->load(backpack_url('charts/inside'));

        // OPTIONAL
        $this->chart->minimalist(false);
        $this->chart->displayLegend(true);
        $this->chart->options([
            'tooltips' => [ //working
                'mode' => 'index',
                'intersect' => false,
                'position'=>'nearest'
            ],
            'responsive' => true, //working
            'scales' => [
                'xAxes' => [['stacked' => true]],
                'yAxes' => [['stacked' => true]],
            ],
            "maintainAspectRatio" => false,
        ], true);
    }

    /**
     * Respond to AJAX calls with all the chart data points.
     *
     * @return json
     */
    public function data()
    {
        $locations = Location::orderBy('lft')->get()->pluck('id')->toArray();
        $categories = VisitorCategory::all();
        foreach ($categories as $category) {
            for ($days_backwards = 0; $days_backwards >= 0; $days_backwards--) {
                $visitors[$category->id][] = Visitor::where('category_id',$category->id)
                ->whereIn('current_location',$locations)
                ->whereDate('last_movement', today()->subDays($days_backwards))->count();
            }
            $color = 'rgb(' . rand(0, 255) . ',' . rand(0, 255) . ',' . rand(0, 255) . ')';
            $color_trans = rtrim($color, ")") . ', 0.4)';
            $this->chart->dataset($category->name, 'bar', $visitors[$category->id])
                ->color($color)
                ->backgroundColor($color_trans);
        }

        
        
    }
}
