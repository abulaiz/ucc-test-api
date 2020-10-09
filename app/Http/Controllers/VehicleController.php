<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Vehicle;

class VehicleController extends Controller
{
    private $item_each_pagination = 10;

    private $vehicle;

    function __construct(){
        $this->vehicle = new Vehicle();
    }

    /*
     * Get list of vehicels
     *
     * @request (GET) : 
     * - keyword = Optional, for search plat number, name, or location
     * - engine_displacement = as defined in 
     *   Vehicle()->$accepted_engine_displacement or 'default'
     * - engine_power = as defined in 
     *   Vehicle()->$accepted_engine_power or 'default'
     * - page = number of page
     */
    public function index(Request $request){
        $select = DB::table('vehicels')->select('*')->orderBy('created_at', 'desc');

        if(!empty($request->get('keyword'))){
            $keyword = $request->get('keyword');
            $select->where(function($q) use ($keyword) {
                $q->orWhere('name', 'like', "%$keyword%")
                  ->orWhere('plat_number', 'like', "$keyword")
                  ->orWhere('location', 'like', "$keyword");
            });
        }

        $paginator = $select->paginate($this->item_each_pagination);
        $data = $this->vehicle->mockUp($request, (array)$paginator->items());

        return response()->json($this->paginaitonResponse($paginator, $data));
    }

    /*
	 * Store new vehicel to Database
     */
    public function store(Request $request){
    	$input = $request->all();

        $engine_displacements = $this->vehicle->accepted_engine_displacement;
        $engine_powers = $this->vehicle->accepted_engine_power;

        $validator = Validator::make($input, [
        	'plat_number' => 'required|unique:vehicels,plat_number',
        	'name' => 'required',
        	'engine_displacement' => 'required|numeric',
        	'engine_displacement_unit' => 'required|in:'.implode(',', $engine_displacements),
        	'engine_power' => 'required',
        	'engine_power_unit' => 'required|in:'.implode(',', $engine_powers),
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

    private function paginaitonResponse($paginator, $items){
        return [
           "next_page_url" => $paginator->nextPageUrl(),
           "data" => $items
        ];
    }
}
