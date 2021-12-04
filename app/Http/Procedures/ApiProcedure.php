<?php

declare(strict_types=1);

namespace App\Http\Procedures;

use App\Models\Data;
use App\Models\User;
use Illuminate\Http\Request;
use Sajya\Server\Exceptions\InvalidParams;
use Sajya\Server\Procedure;

class ApiProcedure extends Procedure
{
    /**
     * The name of the procedure that will be
     * displayed and taken into account in the search
     *
     * @var string
     */
    public static string $name = 'ApiProcedure';

    public function getData(Request $request) : array
    {
        $num = (int) $request->input('num') ?? 0;
        $skip = ($num * 5);
        $data['data'] = Data::whereNotNull('url')->skip($skip)->take(5)->get();

        $data['count'] = Data::count();
        return [
            'success' => true,
            'data' => $data
        ];
    }

    public function setData(Request $request) : array|InvalidParams
    {
        $url = $request->input('url');
        if (!isset($url)) {
            return new InvalidParams(['url' => 'Is required']);
        }

        $result = [
            'success' => false,
        ];

        $exist = Data::where([
                'url' => $url,
                'v_id' => $request->input('v_id')
        ])->first();

        if(!is_null($exist)){
            $exist->count = $exist->count + 1;
            $exist->save();
            if($exist->save()){
                $result = [
                    'success' => true,
                ];
            }
        } else {
            $data = new Data;
            $data->url = (string) $url;
            $data->v_id = (string) $request->input('v_id');
            if($data->save()){
                $result = [
                    'success' => true,
                ];
            }
        }
        return $result;
    }

}
