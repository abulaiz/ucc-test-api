<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class VehicleController extends Controller
{
	private $accepted_engine_displacement_unit = ['liters', 'centimeters', 'inches'];

	private $accepted_engine_power_unit = ['hoursepower', 'kilowatts'];

	private $engine_displacement_unit_alias = [
		'liters' => 'L', 'centimeters' => 'cm', 'inches' => 'inches'
	];

	private $engine_power_unit_alias = [
		'hoursepower' => 'HP', 'kilowatts' => 'kW'
	];

    public function index(Request $request){

    }

    /*
	 * Store new vehicel to Database
     */
    public function store(Request $request){
    	$input = $request->all();

        $validator = Validator::make($input, [
        	'plat_number' => 'required|unique:vehicels,plat_number',
        	'name' => 'required',
        	'engine_displacement' => 'required|numeric',
        	'engine_displacement_unit' => 'required|in:'.implode(',', $this->accepted_engine_displacement_unit),
        	'engine_power' => 'required',
        	'engine_power_unit' => 'required|in:'.implode(',', $this->accepted_engine_power_unit),
        	'price' => 'required|numeric',
        	'location' => 'required'
        ]);   

        if ($validator->fails())
            return response()->json([
            	'errors' => $validator->errors()->getMessages(),
            	'success' => false
            ]);  

        DB::beginTransaction();

        try {
        	$input['created_at'] = date('Y-m-d H:i:s');
        	DB::table('vehicels')->insert($input);
        	DB::commit();
		} catch (\Exception $e) {
			DB::rollback();
			return response()->json(['success' => false]);  
		}      

		return response()->json(['success' => true]); 
    }
}
