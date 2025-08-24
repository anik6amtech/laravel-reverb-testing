<?php

namespace App\Http\Controllers;

use App\Events\DemoPing;
use Illuminate\Http\Request;

class PingController extends Controller
{
    public function __invoke()
    {
        $data = [
            'message' => 'Testing reverb',
            'time'    => now()->toIso8601String(),
        ];

        DemoPing::dispatch($data);

        return response()->json(['ok' => true, 'sent' => $data]);
    }
}
