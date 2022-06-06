<?php

namespace App\Http\Controllers\web\v1;

use Validator;
use DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Divisions extends Controller
{

    public function __construct(Request $request=null){
        parent::__construct();
		
        
	}

    public function get(Request $request){
        $post = $request->input();
        // var_dump($post);
        $validator = Validator::make($post, [
            'Token' => 'required'
        ]);
        $json = [];
        $error = [];

        if ($validator->fails()) {
            $error = $validator->errors()->get('*');
            
            $json = $this->generate_error($error,$json);
            return json_encode($json);
        }
        if(@$post['DivisionID']){
            $result = DB::table('Divisions as d')
            ->leftjoin('Jemaat as j','d.HeadDivisionID','=','j.JemaatID')
            // ->select(DB::raw('"FullName", "Email", "Phone", j."Address", "DivisionsName", "PrivilegeCardNo", "DOB",j."JemaatID" '))
            ->select(DB::raw('d.*, "FullName"'))
            ->where('d.Archived',null)
            ->where('d.DivisionID',$post['DivisionID'])
            ->first();
        }else{
            $result = DB::table('Divisions as d')
            ->leftjoin('Jemaat as j','d.HeadDivisionID','=','j.JemaatID')
            // ->select(DB::raw('"FullName", "Email", "Phone", j."Address", "DivisionsName", "PrivilegeCardNo", "DOB",j."JemaatID" '))
            ->select(DB::raw('d.*, "FullName"'))
            ->where('d.Archived',null)
            ->get();
        }
        
        

        return $this->generate_response($result);
        
        
    }

    public function delete (Request $request){
        $post = $request->input();
        // var_dump($post);
        $validator = Validator::make($post, [
            'Token' => 'required'
        ]);
        $json = [];
        $error = [];
        if ($validator->fails()) {
            $error = $validator->errors()->get('*');
            
            $json = $this->generate_error($error,$json);
            return json_encode($json);
        }

        $arr = array(
            'Archived' => 'Y'
        );
        
        $row = $this->upsert($arr,'DivisionsID',@$post['DivisionsID'],'Divisions');
        $result = array('DivisionsID' => $row);        
        return $this->generate_response($result);
    }
    
    public function save(Request $request){
        $post = $request->input();
        // var_dump($post);
        $validator = Validator::make($post, [
            'Token' => 'required'
        ]);
        $json = [];
        $error = [];

        if ($validator->fails()) {
            $error = $validator->errors()->get('*');
            
            $json = $this->generate_error($error,$json);
            return json_encode($json);
        }
        $arr = array(
            'DivisionsName' => $post['DivisionsName'],
            'Description' => $post['Description'],
            'HeadDivisionID' => $post['HeadDivisionID'],
            'Address' => $post['Address']
        );
        
        $row = $this->upsert($arr,'DivisionsID',@$post['DivisionsID'],'Divisions');
        $result = array('DivisionsID' => $row);        

        return $this->generate_response($result);
    }

    public function login(Request $request){
        $post = $request->input();
        $validator = Validator::make($post, [
            'Username' => 'required',
            'Password' => 'required'
        ]);
        $json = [];
        $error = [];
        if ($validator->fails()) {
            $error = $validator->errors()->get('*');
            print_r($error);die();
            $json = $this->generate_error($error,$json);
            return json_encode($json);
        }

        $result = DB::table('User')
        ->where(function ($query) use($post) {
            $query->where(DB::raw('lower("Username")'),strtolower($post['Username']))
                  ->orWhere(DB::raw('lower("Username")'),strtolower($post['Username']));
        })
        ->where(DB::raw('lower("Password")'),strtolower($post['Password']))
        ->first();
        if(!$result){
            $error = array(
                'Login' => array(
                    'Login Failed'
                )
            );
            $error = $this->add_errors($error,'TEST','NGETEST AJA DULU');
            $json = $this->generate_error($error,$json);
            return json_encode($json);
        }
        if(count($error) > 0){
            $json = $this->generate_error($error,$json);
            return json_encode($json);
        }
        
        $json = $this->generate_response($result);
        return $json;
    }

    


}
