<?php

namespace App\Http\Controllers\web\v1;

use Validator;
use DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Jemaat extends Controller
{

    public function __construct(Request $request=null){
        parent::__construct();
		
        
	}

    public function clearSession(Request $request){
        $request->session()->flush();
    }

    public function test(Request $request){
        $post = $request->input();
        $validator = Validator::make($post, [
            'transId' => 'required|integer|max:599|min:100',
        ]);
        
        $json = [];
        if ($validator->fails()) {
            return "Not a Valid ID";
        }
        if(in_array($post['transId'],$this->isCharged))
        {
            $session = $request->session()->get('test');
            $data =
                $post['transId']
            ;
            if(count((array)$session) == 0)
                $session = $request->session()->push('test',$data);
            else{
                if(in_array($post['transId'],$session)){

                }else{
                    $session = $request->session()->push('test',$data);
                }
            }
                
            
            
            $json['Message'] = 'transId '.$post['transId'].' has charged';
            $json['Session'] = $request->session()->get('test');
            
        }else{
            $json['Message'] = 'transId '.$post['transId'].' hasn\'t charged yet';
        }
        
        
        return json_encode($json);
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
        if(@$post['JemaatID']){
            $result = DB::table('Jemaat as j')
            ->leftjoin('Friends as f','f.FriendsID','=','j.FriendsID')
            ->select(DB::raw('"FullName", "Email", "Phone", j."Address", "FriendsName", "PrivilegeCardNo", "DOB",j."JemaatID" '))
            ->where('Archived',null)
            ->where('JemaatID',$post['JemaatID'])
            ->first();
        }else{
            $result = DB::table('Jemaat as j')
            ->leftjoin('Friends as f','f.FriendsID','=','j.FriendsID')
            ->select(DB::raw('"FullName", "Email", "Phone", j."Address", "FriendsName", "PrivilegeCardNo", "DOB",j."JemaatID" '))
            ->where('Archived',null)
            ->get();
        }
        
        

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
