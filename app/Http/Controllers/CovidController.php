<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use function MongoDB\BSON\toJSON;

class CovidController extends Controller
{
    private function returnApiAsJSON($link){
        $response = Http::get($link);
        if($response->ok())
            return $response->json();
        return "Oops! There seems to be a problem with the api endpoint. Try again later.";
    }
    public function all(){
        return $this->returnApiAsJSON('https://corona.lmao.ninja/v2/all');
    }
    public function historyAll(){
        return $this->returnApiAsJSON('https://corona.lmao.ninja/v2/historical/all');
    }
    public function country($country){
        return $this->returnApiAsJSON('https://corona.lmao.ninja/v2/countries/' . $country);
    }
    public function historyCountry($country){
        return $this->returnApiAsJSON('https://corona.lmao.ninja/v2/historical/' . $country);
    }
    public function trendCountry($country){
        $countryStats = $this->historyCountry($country);
        $countryStats = $countryStats['timeline'];
        $deathStats = $this->getStats('deaths', $countryStats);
        $casesStats = $this->getStats('cases', $countryStats);
        $recoveredStats = $this->getStats('recovered', $countryStats);
        $allStats = json_encode(['deaths' => $deathStats, 'cases' => $casesStats, 'recovered' => $recoveredStats]);
        dd($allStats);
    }
    private function getStats($statName, $countryStats){
        $lastStatCount = end($countryStats[$statName]);
        $secondToLastStatCount = prev($countryStats[$statName]);
        $statCountDelta =  $lastStatCount - $secondToLastStatCount;
        $statCountIncreased = $statCountDelta > 0;
        return ['delta' => abs($statCountDelta), 'increased' => $statCountIncreased];
    }
}
