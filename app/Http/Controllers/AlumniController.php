<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Alumni;
use App\Models\User;  // Add this import

class AlumniController extends Controller
{
    public function index()
    {
        $alumnis = Alumni::all();
        return view('Alumni.index' ,['Alumni' => $alumnis ] );

    }

    public function create()
    {
        return view('Alumni.create');
        
    }   

    public function store(Request $request){
       $data = $request->validate([

    'StudentID' => 'required',
            'Fullname' => 'required',
            'Age' => 'required',
        'Gender'=> 'required',
            'Course'=> 'required',
                'Section'=> 'required',
                'Batch'=> 'required',
    'Contact'=> 'required',
    'Address'=> 'required',
    'Emailaddress'=> 'required',
    'Occupation'=> 'required',

       ]);

       $newAlumni = Alumni::create($data);
       return redirect()->route('Alumni.index')->with('success', 'Record created successfully!');
       
    }



    public function edit($id)
    {
        $alumni = Alumni::findOrFail($id);
       return view('Alumni.edit', ['alumni' => $alumni]);
    }



    
    public function update(Request $request, Alumni $Alumni)
    {
        $request->validate([
            'StudentID' => 'required',
            'Fullname' => 'required',
            'Age' => 'required',
            'Gender' => 'required',
            'Course' => 'required',
            'Section' => 'required',
            'Batch' => 'required',
            'Contact' => 'required',
            'Address' => 'required',
            'Emailaddress' => 'required|email',
            'Occupation' => 'required',
        ]);
    
        // Update Alumni record
        $Alumni->update($request->all());
    
        // Update corresponding user profile
        User::where('student_id', $request->StudentID)->update([
            'student_id' => $request->StudentID,
            'fullname' => $request->Fullname,
            'age' => $request->Age,
            'gender' => $request->Gender,
            'course' => $request->Course,
            'section' => $request->Section,
            'batch' => $request->Batch,
            'contact' => $request->Contact,
            'address' => $request->Address,
            'email' => $request->Emailaddress,
            'occupation' => $request->Occupation,
        ]);
    
        return redirect()->route('Alumni.index')
            ->with('success', 'Alumni Record Updated Successfully');
    }



    public function destroy($id)
    {
        $alumni = Alumni::findOrFail($id);
        $alumni->delete();
        return redirect()->route('Alumni.index')->with('success', 'Record deleted successfully');
    }


    
}
