<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class AddTransactionController extends Controller
{
    private const DATE_FORMAT = 'd/m/Y H';

    public function index() {
            $users = DB::select(
                DB::raw("select
                                u.name as name,
                                u2.name as recipient_name,
                                t.amount as amount,
                                t.status_at as completed
                                from users as u
                            left join transactions t
                                on u.id = t.sender_id
                                and t.status_at=(
                                    select status_at from transactions t2
                                    where t2.status = 'completed' and t2.sender_id=u.id
                                    order by status_at desc limit 1
                                )
                            left join users u2 on u2.id = t.recipient_id
                            order by u.name asc;"));

        $senders =  User::all(['id', 'name'])->toArray();

        return view('createTransaction', [
            'senders' => $senders,
            'users' => $users
        ]);
    }

    public function formSubmit(Request $request) {
        $request->validate([
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
                'date_format:' . self::DATE_FORMAT,
                'after:' . now()->setSecond(59)->setMinute(59),
            ],
        ]);

        Transaction::create([
            'sender_id' => $request->sender,
            'recipient_id' =>  $request->recipient,
            'amount' => $request->amount,
            'status_at' => $this->prepareDate($request->datetime),
        ]);

        return Redirect::back();
    }

    public function selectRecipient(Request $request) {
        if ($request->ajax() && $request->has('id_sender')) {

            $recipients =  User::all(['id', 'name'])->except($request->get('id_sender'))->toArray();
            $data = view('selectRecipient', ['recipients' => $recipients])->render();
            return response()->json(['options' => $data]);
        }
    }

    public function senderMaxAmount(Request $request) {
        if ($request->ajax() && $request->has('id_sender')){
            $max_amount = $this->getMaxAmountByUserId($request->get('id_sender'));
            return response()->json(['max_amount' => $max_amount]);
        }
    }

    private function getMaxAmountByUserId(int $id): int {
        $user = User::findOrFail($id);
        $planned_amount = DB::table('transactions')
            ->select('amount')
            ->where('sender_id', '=', 1)
            ->where('status', '=', 'planned')
            ->sum('amount');

        return $user->balance - $planned_amount;
    }

    private function prepareDate(string $date): string {
        $carbon_date_time = Carbon::createFromFormat(self::DATE_FORMAT, $date);
        return $carbon_date_time->format('Y-m-d H:i:s.u');
    }

}
