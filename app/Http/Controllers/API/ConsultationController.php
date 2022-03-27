<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Consultation;

class ConsultationController extends Controller
{
                    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(){
        $user = auth('api')->user();
        $consults = Consultation::latest()->with('patient');
        $userconsults = $consults->where('doctor_id',$user->id)->paginate(10);
        $consults = $userconsults;
        return response()->json($consults);
    }

    public function list(){
        $patientslist = Consultation::all();
        return PatientResource::collection($patientslist);        
    }

    public function store(Request $request){
        $user = auth('api')->user();

        $this->validate($request,[
            'patient_id' => 'required|integer',
            'symptoms' => 'required|string',
            'status' => 'required|string',
        ]);

        return Consultation::create([
          'patient_id' => $request['patient_id'],
          'doctor_id' => $user->id,      
          'symptoms' => $request['symptoms'],
          'status' => $request['status'],      
          'tests' => $request->input('tests')
        ]);
    }

    public function update(Request $request, $id)
    {
        $consultation = Consultation::findOrFail($id);

        $this->validate($request,[
            'patient_id' => 'required|integer',
            'symptoms' => 'required|string',
            'status' => 'required|string',
        ]);

        $consultation->update($request->all());
    }

    public function destroy(Request $request, $id)
    {
        $consultation = Consultation::findOrFail($id);
        $consultation->delete();
    }

    public function countConsultations(){
        $user = auth('api')->user();
        $consultations = Consultation::all()->where('doctor_id',$user->id)->count();
        return response()->json($consultations);
    }
}
