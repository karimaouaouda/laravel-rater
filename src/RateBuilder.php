<?php

namespace Karimaouaouda\LaravelRater;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;
use App\Exceptions\TestException;

/**
 * @mixin \App\Models\User
*/
class RateBuilder{

    protected Authenticatable $rater;

    protected Ratable $target;

    protected int $amount = -1;

    protected string $comment = "";

    protected bool $hasComment = false;

    protected string $tableName = "rates";

    /**
     * @throws TestException
     * @throws \Exception
     */
    public function __construct(Authenticatable $user, Ratable $model, int $amount = -1, string $comment = null)
    {
        $this->rater = $user;
        $this->target = $model;

        if($user->rated($model)){
            throw new TestException("sorry you rate this model");
        }

        if($amount != -1){
            $this->with($amount);
        }

        if( $comment != null ){
            $this->appendComment($comment);
        }
    }

    /**
     * @throws \Exception
     */
    public function with(int $amount): static
    {
        if( $amount < 1 || $amount > 5 ){
            throw new \Exception("the amount must be a valid integer in range 1-5 given : $amount");
        }

        $this->amount = $amount;


        return $this;
    }

    /**
     * @throws \Exception
     */
    public function appendComment(string $comment): static
    {
        if( empty( trim($comment) ) ){
            //must be validated
            throw new \Exception("must provide a comment");
        }

        $comment = htmlspecialchars($comment);

        $this->hasComment = true;

        $this->comment = $comment;

        return $this;
    }


    public function apply(){
        DB::table($this->tableName)->insert([
            "rater_type" => get_class($this->rater),
            "rater_id" => $this->rater->id,
            "target_type" => get_class($this->target),
            "target_id" => $this->target->id,
            "comment" => $this->hasComment ? $this->comment : null,
            "amount" => $this->amount,
            "created_at" => now(),
            "updated_at" => now()
        ]);
    }


}
