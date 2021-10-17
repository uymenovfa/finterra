<?php

namespace Database\Factories;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Transaction::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $sender_id = rand(1,10);
        $recipient_id = $sender_id > 5 ? rand(1, $sender_id - 1) :  rand($sender_id + 1, 10);
        return [
            'sender_id' => $sender_id,
            'recipient_id' => $recipient_id,
            'amount' => rand(1, 1000000),
            'status' => 'completed',
            'status_at' => $this->faker->dateTimeBetween('-2 months', '-1 days'),
        ];
    }
}
