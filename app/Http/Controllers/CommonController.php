<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\Town;
use App\Models\MunicipalDistrict;
use App\Models\Place;
use App\Models\Neighbourhood;
use App\Models\Electrotable;
use Maatwebsite\Excel\Facades\Excel;
use Helpers;
use DB;
use Log;
use \Config;

class CommonController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function get_states(Request $request){
        $countryId = $request->id;
        $rows = State::where('country_id',$countryId)->get();
        if(is_array($countryId)){
            $rows = State::whereIn('country_id',$countryId)->get();
        }
        $html = "<option value=''>Select State</option>"; 
        foreach ($rows as $key => $row) {
            $html .= '<option value='.$row->id.'>'.$row->name.'</option>';
        }
        return $html;
    }

    public function get_cities(Request $request){
        $stateId = $request->id;
        $rows = City::where('state_id',$stateId)->get();
        if(is_array($stateId)){
            $rows = City::whereIn('state_id',$stateId)->get();
        }
        $html = "<option value=''>Select City</option>";
        foreach ($rows as $key => $row) {
            $html .= '<option value='.$row->id.'>'.$row->name.'</option>';
        }
        return $html;
    }

    public function get_municipal_districts(Request $request){
        $cityId = $request->id;
        $rows = MunicipalDistrict::where('city_id',$cityId)->get();
        if(is_array($cityId)){
            $rows = MunicipalDistrict::whereIn('city_id',$cityId)->get();
        }
        $html = "<option value=''>Select Municipal District</option>"; 
        foreach ($rows as $key => $row) {
            $html .= '<option value='.$row->id.'>'.$row->name.'</option>';
        }
        return $html;
    }

    public function get_towns(Request $request){
        $municipalDistrictId = $request->id;
        $rows = Town::where('municipal_district_id',$municipalDistrictId)->get();
        if(is_array($municipalDistrictId)){
            $rows = Town::whereIn('municipal_district_id',$municipalDistrictId)->get();
        }
        $html = "<option value=''>Select Town</option>"; 
        foreach ($rows as $key => $row) {
            $html .= '<option value='.$row->id.'>'.$row->name.'</option>';
        }
        return $html;
    }

    public function get_places(Request $request){
        $townId = $request->id;
        $rows = Place::where('town_id',$townId)->get();
        if(is_array($townId)){
            $rows = Place::whereIn('town_id',$townId)->get();
        }
        $html = "<option value=''>Select Place</option>"; 
        foreach ($rows as $key => $row) {
            $html .= '<option value='.$row->id.'>'.$row->name.'</option>';
        }
        return $html;
    }

    public function get_neighbourhoods(Request $request){
        $placeId = $request->id;
        $rows = Neighbourhood::where('place_id',$placeId)->get();
        if(is_array($placeId)){
            $rows = Neighbourhood::whereIn('place_id',$placeId)->get();
        }
        $html = "<option value=''>Select Neighbourhood</option>"; 
        foreach ($rows as $key => $row) {
            $html .= '<option value='.$row->id.'>'.$row->name.'</option>';
        }
        return $html;
    }

    public function get_cities_n_districts(Request $request){
        $stateId = $request->id;

        // Fetch City
        $rows = City::where('state_id',$stateId)->get();
        if(is_array($stateId)){
            $rows = City::whereIn('state_id',$stateId)->get();
        }
        $cityHtml = "<option value=''>Select City</option>";
        foreach ($rows as $key => $row) {
            $cityHtml .= '<option value='.$row->id.'>'.$row->name.'</option>';
        }

        // Fetch Districts (Circunscripcion)
        $rows = Electrotable::select('IdCircunscripcion', 'DescripcionCircunscripcion')->where('IdProvincia',$stateId)->distinct('IdCircunscripcion')->get();
        if(is_array($stateId)){
            $rows = Electrotable::select('IdCircunscripcion', 'DescripcionCircunscripcion')->whereIn('IdProvincia',$stateId)->distinct('IdCircunscripcion')->get();
        }
        $districtHtml = "<option value=''>Select District</option>";
        foreach ($rows as $key => $row) {
            $districtHtml .= '<option value='.$row->IdCircunscripcion.'>'.$row->DescripcionCircunscripcion.'</option>';
        }

        $response = [
            "city" => $cityHtml,
            "district" => $districtHtml,
        ];

        return $response;
    }

    public function get_municipal_districts_n_recintos(Request $request){
        $cityId = $request->id;

        // Fetch Municipal Districts
        $rows = MunicipalDistrict::where('city_id',$cityId)->get();
        if(is_array($cityId)){
            $rows = MunicipalDistrict::whereIn('city_id',$cityId)->get();
        }
        $municipalDistHtml = "<option value=''>Select Municipal District</option>"; 
        foreach ($rows as $key => $row) {
            $municipalDistHtml .= '<option value='.$row->id.'>'.$row->name.'</option>';
        }

        // Fetch Recintos
        $rows = Electrotable::select('IdRecinto', 'DescripcionRecinto')->where('IdMunicipio',$cityId)->distinct('IdRecinto')->get();
        if(is_array($cityId)){
            $rows = Electrotable::select('IdRecinto', 'DescripcionRecinto')->whereIn('IdMunicipio',$cityId)->distinct('IdRecinto')->get();
        }
        $recintoHtml = "<option value=''>Select Recintos</option>";
        foreach ($rows as $key => $row) {
            $recintoHtml .= '<option value='.$row->IdRecinto.'>'.$row->DescripcionRecinto.'</option>';
        }

        $response = [
            "municipalDist" => $municipalDistHtml,
            "recintos" => $recintoHtml,
        ];

        return $response;
    }

    public function get_recintos(Request $request){
        $municipalDistrictId = $request->id;

        // Fetch Recintos (Circunscripcion)
        $rows = Electrotable::select('IdRecinto', 'DescripcionRecinto')->where('IdDistritoMunicipal',$municipalDistrictId)->distinct('IdRecinto')->get();
        if(is_array($municipalDistrictId)){
            $rows = Electrotable::select('IdRecinto', 'DescripcionRecinto')->whereIn('IdDistritoMunicipal',$municipalDistrictId)->distinct('IdRecinto')->get();
        }
        $recintoHtml = "<option value=''>Select Recintos</option>";
        foreach ($rows as $key => $row) {
            $recintoHtml .= '<option value='.$row->IdRecinto.'>'.$row->DescripcionRecinto.'</option>';
        }

        return $recintoHtml;
    }

    public function get_colleges(Request $request){
        $recintosId = $request->id;

        // Fetch Recintos (Circunscripcion)
        $rows = Electrotable::select('IDColegio', 'CodigoColegio', 'DescripcionColegio')->where('IdRecinto',$recintosId)->distinct('IDColegio')->get();
        if(is_array($recintosId)){
            $rows = Electrotable::select('IDColegio', 'CodigoColegio', 'DescripcionColegio')->whereIn('IdRecinto',$recintosId)->distinct('IDColegio')->get();
        }

        $collegeHtml = "<option value=''>Select College</option>";
        foreach ($rows as $key => $row) {
            $collegeHtml .= '<option value='.$row->IDColegio.'>'.$row->DescripcionColegio.' ('.$row->CodigoColegio.')</option>';
        }

        return $collegeHtml;
    }

    public function import_neighbourhoods(Request $request)
    {
        $file = $request->file('excel_file');

        // Validate the uploaded file
        $request->validate([
            'excel_file' => 'required|mimes:xls,xlsx'
        ]);

        // Read the file into a collection
        $data = Excel::toCollection(null, $file)[0];

        // Insert the data into the table
        $i = 0;
        foreach ($data as $row) {
            if($i > 0){
                Neighbourhood::create([
                    'name' => $row[0], // Replace column1 with your actual column names
                    // Add more columns as needed
                ]);
            }
            $i++;
        }

        $succResponse = sendSuccessResponse(200, '');
        return response()->json($succResponse, 200);
    }
}
