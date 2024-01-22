<?php

namespace Karimaouaouda\LaravelRater\Traits;

use App\Models\User;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Karimaouaouda\LaravelRater\Rater;

trait CanBeRated{

    public function getRatersAttribute(){
        return Rater::getModelRaters($this);
    }

    public function ratedBy(Authenticatable $user, int $mark){
        dd("user : ". $user->name . " rate : ". $this->name . " with : ". $mark);
    }

    public function isRatedBy(User $user){
        return Rater::isUserRated($user, $this);
    }

}