<?php

namespace Karimaouaouda\LaravelRater\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Karimaouaouda\LaravelRater\Rater;

class RaterController extends BaseController
{
    public function __construct()
    {
        $this->middleware("auth");
    }


    public function rate($model_type_encoded, $model_id, Request $request): void
    {

        $request->validate([
            Rater::$amountKey => ['required', 'integer', 'min:1', 'max:5']
        ]);

        $comment = $request->has(Rater::$comment_key) ? $request->input(Rater::$comment_key) : null;

        $model = Rater::parseModel($model_type_encoded, $model_id);

        Auth::user()->rate($model)
                    ->with( $request->input(Rater::$amountKey) )
                    ->appendComment($comment)
                    ->apply();

    }


    public function unrate($model_type_encoded, $model_id){

        $model = Rater::parseModel($model_type_encoded, $model_id);

        Auth::user()->unrate($model);

    }

    public function rates($rater_type_encoded, $rater_id){

        $user = Rater::parseModel($rater_type_encoded, $rater_id);

        return response()->json($user->rates, 200);

    }

}
