<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use DB;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct(Request $request=null){
        
		ini_set('display_errors', '1');
		ini_set('display_startup_errors', '1');
		error_reporting(E_ALL);
	}

    public function generate_error($error){
        $json = [];
        
        foreach($error as $key => $val){
            $json["Errors"][$key] = new \stdClass();
            $json["Errors"][$key]->ID = $key;
            $json["Errors"][$key]->Message = $val[0];
            $json["Errors"] = array_values($json["Errors"]);
        }
        $json["Status"] = 1;
        $json["Message"] = "Invalid Input";
        return $json;
    }

    public function generate_response($data){
        $json["Data"] = $data;
        $json["Status"] = 0;
        $json["Message"] = "Success";
        return json_encode($json);
    }

    public function add_errors($error,$id,$message){
        $temp = [];
        $temp[$id] = array(
            $message
        );
        
        $error = array_merge($error,$temp);
        return $error;
    }

    public function upsert($arr,$identifier,$id,$table){

        if($id !== null){
            $row = DB::table($table)
            ->where($identifier,$id)
            ->update($arr);
            $row = $id;
        }else{
            $row = DB::table($table)
            ->insert($arr);
            $row = $this->getLastID();
        }
        return $row;
    }
    public function getLastID(){
        
        $newid = \DB::select( \DB::raw('SELECT lastval() id'))[0]->id;
        return $newid;
    }
}
