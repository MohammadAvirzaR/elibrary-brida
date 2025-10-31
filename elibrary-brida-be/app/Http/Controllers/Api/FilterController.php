<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Type;
use App\Models\Unit;
use App\Models\License;
use Illuminate\Http\Request;

class FilterController extends Controller
{
    public function index()
    {
        return response()->json([
            'subjects' => Subject::select('id', 'subject_name')->get(),
            'types' => Type::select('id', 'type_name')->get(),
            'units' => Unit::select('id', 'unit_name')->get(),
            'licenses' => License::select('id', 'license_name')->get(),
            'years' => [5, 7, 10, 15, 20, 25], // year filters range
            'access_rights' => ['public', 'private']
        ]);
    }
}
