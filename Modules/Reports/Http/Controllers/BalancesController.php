<?php
namespace Modules\Reports\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BalancesController extends Controller
{
    public function index()
    {
        return view('reports::Balances.index');
    }
    public function consume()
    {
        return view('reports::Balances.consume_balance');
    }
    public function add()
    {
        return view('reports::Balances.add_balance');
    }
}
