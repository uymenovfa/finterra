<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class UsersController extends Controller
{
    public function index()
    {
        $items = DB::table('users')
            ->selectRaw('
                any_value(users.name) as name,
                any_value(ur.name) as recipient_name,
                any_value(transactions.amount) as amount,
                max(transactions.completed_at) as completed
            ')
            ->leftJoin('transactions', 'users.id', '=', 'transactions.sender_id')
            ->leftJoin('users as ur', 'ur.id', '=', 'transactions.recipient_id')
            ->groupBy('users.id')
            ->get();

        return view('index', compact('items'));
    }
}
