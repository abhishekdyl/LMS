<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;

class getMonday extends Controller
{

    function getMondaydata(){

        $year = $year ?? date('Y'); // Default to current year if not provided
        $mondays = collect();

        for ($month = 1; $month <= 12; $month++) {
            $firstDay = Carbon::create($year, $month, 1);
            $firstMonday = $firstDay->copy()->next(Carbon::MONDAY);
            if ($firstDay->dayOfWeek == Carbon::MONDAY) {
                $firstMonday = $firstDay; // If the 1st is already Monday, use it
            }
            $mondays->push($firstMonday->toDateString());
        }

        return $mondays;

    }

    function getanyday(){

        $startOfMonth = Carbon::create(2025, 1, 1);
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        $mondays = [];

        $day = $startOfMonth->copy()->next(Carbon::MONDAY);

        while ($day <= $endOfMonth) {
            $mondays[] = $day->format('Y-m-d'); 
            // echo '<pre>';
            // print_r($day);
            $day->addWeek(); 

        }

        return $mondays;

    }


}
