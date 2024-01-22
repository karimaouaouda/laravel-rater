<?php
namespace Karimaouaouda\LaravelRater;

use Illuminate\Foundation\Auth\User as Authenticatable;


interface Ratable{
    public function ratedBy(Authenticatable $user, int $mark);
}