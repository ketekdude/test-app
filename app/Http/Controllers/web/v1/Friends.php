<?php

namespace App\Http\Controllers\web\v1;

use Validator;
use DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Friends extends Controller
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
        if(@$post['FriendsID']){
            $result = DB::table('Friends as f')
            ->leftjoin('Jemaat as j','f.FriendsLeaderID','=','j.JemaatID')
            // ->select(DB::raw('"FullName", "Email", "Phone", j."Address", "FriendsName", "PrivilegeCardNo", "DOB",j."JemaatID" '))
            ->select(DB::raw('f.*, "FullName"'))
            ->where('f.Archived',null)
            ->where('f.FriendsID',$post['FriendsID'])
            ->first();
        }else{
            $result = DB::table('Friends as f')
            ->leftjoin('Jemaat as j','f.FriendsLeaderID','=','j.JemaatID')
            // ->select(DB::raw('"FullName", "Email", "Phone", j."Address", "FriendsName", "PrivilegeCardNo", "DOB",j."JemaatID" '))
            ->select(DB::raw('f.*, "FullName"'))
            ->where('f.Archived',null)
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
        
        $row = $this->upsert($arr,'FriendsID',@$post['FriendsID'],'Friends');
        $result = array('FriendsID' => $row);        
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
            'FriendsName' => $post['FriendsName'],
            'Address' => $post['Address'],
            'FriendsLeaderID' => $post['FriendsLeaderID'],
            'Address' => $post['Address']
        );
        
        $row = $this->upsert($arr,'FriendsID',@$post['FriendsID'],'Friends');
        $result = array('FriendsID' => $row);        

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
