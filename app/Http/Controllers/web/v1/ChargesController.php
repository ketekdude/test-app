<?php

namespace App\Http\Controllers\web\v1;

use Validator;
use DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ChargesController extends Controller
{

    private $isCharged = [100,150,400];

    private $test = 'test';

    public function __construct(Request $request=null){
        
		ini_set('display_errors', '1');
		ini_set('display_startup_errors', '1');
		error_reporting(E_ALL);
        
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

    public function get_branch(){
        $post = $request->input();
        // $validator = Validator::make($post, [
        //     'latitude' => 'required',
        //     'longitude' => 'required'
        // ]);

        $result = DB::table('Jemaat')->get();
        
        if ($validator->fails()) {
            return "Not a Valid ID";
        }

        return json_encode($result);
        
    }


}
