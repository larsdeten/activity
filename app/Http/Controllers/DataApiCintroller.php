<?php

namespace App\Http\Controllers;

use App\Models\Data;
use Illuminate\Http\Request;

class DataApiCintroller extends Controller
{
    public function getData() : array
    {
        $data = Data::all();
        return [
            'success' => true,
            'data' => $data
        ];
    }
    public function setData(Request $request) : array
    {
        $data = new Data;
        $data->url = $request->input('url');
        if($data->save()){
            return [
                'success' => true,
                'data' => $request
            ];
        }
        return [
            'success' => false,
            'data' => 'error'
        ];
    }

}
