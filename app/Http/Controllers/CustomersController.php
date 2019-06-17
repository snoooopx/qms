<?php

namespace App\Http\Controllers;

use App\Http\Services\MessageService;
use App\Models\Countries;
use App\Models\Customers;
use Illuminate\Http\Request;

class CustomersController extends Controller
{
    public function index(MessageService $ms)
    {
        $countryList = Countries::all();

        return view('customers', [
            'countryList' => $countryList,
        ]);
    }

    public function filter(Request $request)
    {
        $customers = Customers::whereRaw('1=1');

        if ($request->get('sex')) {
            $customers->where('sex', $request->get('sex'));
        }

        if ($request->get('country')) {
            $customers->where('country_id', $request->get('country'));
        }
        return response()->json(['total' => Customers::count(), 'filtered' => $customers->count()]);
    }
}
