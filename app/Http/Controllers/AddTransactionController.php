<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddTransactionController extends Controller
{
    public function index() {
        $users = DB::table('users')
            ->selectRaw('
                any_value(users.name) as name,
                any_value(ur.name) as recipient_name,
                any_value(transactions.amount) as amount,
                max(transactions.completed_at) as completed
            ')
            ->leftJoin('transactions', 'users.id', '=', 'transactions.sender_id')
            ->leftJoin('users as ur', 'ur.id', '=', 'transactions.recipient_id')
            ->groupBy('users.id')
            ->get()
            ->toArray();

        $senders =  User::all(['id', 'name', 'balance'])->toArray();

        return view('createTransaction', [
            'senders' => $senders,
            'users' => $users
        ]);
    }

    public function formSubmit(Request $request) {
        $validated = $request->validate([
            'sender' =>[
                'required',
                'exists:App\Models\User,id',
            ],
            'recipient' => [
                'required',
                'exists:App\Models\User,id',
            ],
            'amount' => [
                'required',
                'numeric',
                'max:' . $this->getMaxAmountByUserId($request->sender),
                'not_in:0'
            ],
            'datetime' => [
                'required',
                'date_format:d/m/Y H',
                'after:' . now()->setSecond(59)->setMinute(59),
            ],
        ]);
        dd($request->all());
    }

    public function selectRecipient(Request $request) {
        if ($request->ajax() && $request->has('id_sender')){

            $recipients =  User::all(['id', 'name'])->except($request->get('id_sender'))->toArray();
            $data = view('selectRecipient', ['recipients' => $recipients])->render();
            return response()->json(['options' => $data]);
        }
    }

    public function senderMaxAmount(Request $request) {
        if ($request->ajax() && $request->has('id_sender')){
            $max_amount = $this->getUserMaxAmount($request->get('id_sender'));
            return response()->json(['max_amount' => $max_amount]);
        }
    }

    private function getMaxAmountByUserId(int $id) {
        $user = User::findOrFail($id);
        $planned_amount = DB::table('transactions')
            ->select('amount')
            ->where('sender_id', '=', 1)
            ->where('status', '=', 'planned')
            ->sum('amount');

        return $user->balance - $planned_amount;
    }

}
