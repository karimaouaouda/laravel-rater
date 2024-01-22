<?php

namespace Karimaouaouda\LaravelRater;


use App\Models\Product;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Karimaouaouda\LaravelRater\Traits\SetGet;
use PHPUnit\Runner\ClassCannotBeFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @mixin Product
 */

class Rater{
    use SetGet;

    /**
     * @var $configs array to hold the config array
    */
    protected static array $configs;

    protected static string $tableName = "rates";

    /**
     * @var $comment_key string the name to be retrieved from request
     */
    public static string $comment_key = "comment";

    /**
     * @var $amountKey string the name to be retrieved from request
     */
    public static string $amountKey = "amount";

    /**
     * test something :)
     * @return string|array dif has a config
     */
    public static function test() : string|array{
        return Config::has("rater") ? "has" : "no";
    }


    /**
     * retrieve all rates from database
     * @return \Illuminate\Support\Collection collection of Ratable models
     *
    */
    public function getAllRates() : \Illuminate\Support\Collection
    {
        return DB::table("rates")->select()->get();
    }

    /**
     * retrieve all rates from database for a specific user
     * @param Authenticatable $user the user to retrieve his rates
     * @return Collection|null collection of Ratable models
     * @throws Exception if no ratable model found i configuration
     */
    public static function getUserRates(Authenticatable $user): ?Collection
    {
        $alias = "";
        $collection = null;

        //check if the user is a rater user

        $alias = array_search( get_class($user), self::$configs['raters'] );

        if( !$alias ){
            throw new Exception("sorry the class does not provided in raters array in rater.php config file : ". get_class($user));
        }

        foreach( self::$configs['matcher'][$alias] as $target ){
            if($collection == null){

                $collection = $user->morphToMany(related: self::$configs['targets'][$target],
                    name: "rater",
                    table: self::$tableName,
                    relatedPivotKey: "target_id",
                                                )->wherePivot("target_type", "=", self::$configs['targets'][$target] )
                                                ->get();



            }else{
               $otherCollection =
                    $user->morphToMany(related: self::$configs['targets'][$target],
                        name: "rater",
                        table: self::$tableName,
                        relatedPivotKey: "target_id",

                                        )->wherePivot("target_type", "=", self::$configs['targets'][$target] )
                                        ->get();



               foreach($otherCollection as $obj){

                    $collection->push($obj);

               }

            }
        }



        return $collection;
    }




    /**
     * retrieve all rates from database for a specific user with a where clause
     * provided by programmer
     * @param Authenticatable $user the user to retrieve his rates
     * @return Collection collection of Ratable models
     */
    public static function getUserRatesWhere(Authenticatable $user, $callback): \Illuminate\Support\Collection
    {
        return DB::table(self::$tableName)->where(function(QueryBuilder $builder) use ($user){
            $builder->where("rater_type", "=", get_class($user))->where("rater_id", "=", $user->id);
        })->where($callback)->get();
    }


    /**
     * check if a specific user rated some ratable model
     * @param Authenticatable $user to check for hem
     * @return Collection|bool collection of Ratable models or false if he didn't rate it
     */
    public static function isUserRated(Authenticatable $user, Ratable $target): bool|\Illuminate\Support\Collection
    {

        $collection = self::getUserRatesWhere($user, function(QueryBuilder $builder) use ($target){
            $builder->where('target_type', "=", get_class($target))->where("target_id", "=", $target->id);
        });

        if( count($collection) > 0 ){
            return $collection;
        }

        return false;
    }


    /**
     * retrieve model raters from database
     * @param Ratable $model the model to retrieve its raters
     * @return Collection|null collection of Ratable models
     * @throws Exception
     */
    public static function getModelRaters(Ratable $model): ?Collection
    {
        $alias = "";
        $collection = null;

        $alias = array_search(get_class($model),  self::$configs['targets']);

        if(!$alias){
            throw new Exception("sorry the class does not provided in raters array in rater.php confg file : ". get_class($model));
        }

        foreach( self::$configs['matcher'] as $user => $targets ){

            if( in_array($alias, $targets) ){

                if($collection == null){

                    $collection = $model->morphToMany(related: self::$configs['raters'][$user],
                        name: "target",
                        table: self::$tableName,
                        relatedPivotKey: "rater_id",
                                                    )->wherePivot("rater_type", "=", self::$configs['raters'][$user] )
                                                    ->get();



                }else{
                   $otherCollection =
                   $model->morphToMany(related: self::$configs['raters'][$user],
                       name: "target",
                       table: self::$tableName,
                       relatedPivotKey: "rater_id",
                                                    )
                                                    ->wherePivot("rater_type", "=", self::$configs['raters'][$user] )
                                                    ->get();

                   foreach($otherCollection as $obj){
                        $collection->push($obj);
                   }
                }
            }
        }
        return $collection;
    }

    /**
     * @throws Exception
     */
    public static function unrate(Authenticatable $user, Ratable $model){
        if(!self::isUserRated($user, $model)){
            throw new Exception("you didn't rate this model");
        }

        DB::table(self::$tableName)->where(function(QueryBuilder $builder) use ($user){
            $builder->where("rater_type", "=", get_class($user))->where("rater_id", "=", $user->id);
        })->where(function(QueryBuilder $builder) use ($model){
            $builder->where("target_type", "=", get_class($model))->where("target_id", "=", $model->id);
        })->delete();
    }

    public static function parseModel($encoded_type, $id){
        $class = base64_decode($encoded_type);

        if( !class_exists($class) ){
            throw new ClassCannotBeFoundException($class, __FILE__);
        }

        $model = $class::find($id);

        if($model == null){
            throw new NotFoundHttpException("can not found the target model");
        }

        return $model;
    }


    public static function raterSummary(bool $json = false) : string
    {
        $collection = new \Illuminate\Support\Collection();

        $rates = DB::table("rates")->select()->get();

        foreach( $rates as $rate ){
            $collection->push([
                'rater_type'     => $rate->rater_type,
                "rater_id"       => $rate->rater_id,
                'target_type'    => $rate->target_type,
                'target_id'      => $rate->target_id,
                self::$amountKey => $rate->{ self::$amountKey },
                self::$comment_key => $rate->{ self::$comment_key }
            ]);
        }

        return $json ? $collection->toJson() : $collection;
    }

    /**
     * @throws Exception
     */
    public static function assertRaters(): void
    {
        if( !Config::has("rater.raters") ){
            throw new Exception("the config file rater.php must have raters kay whith array value");
        }

        foreach( Config::get('rater.raters') as $alias => $class ){
            if( !class_exists($class) ){
                throw new Exception("no class founded whith that name : ". $class);
            }

            if(! ((new $class) instanceof Authenticatable) ){
                throw new Exception("a rater class must be an authentiactable class : " . $class . "in son an authenticatable");
            }

            if( !key_exists($alias, Config::get('auth.guards')) ){
                throw new Exception("the alias must be one of guards that you register in auth.php config file ". $class);
            }
        }
    }

    /**
     * @throws Exception
     */
    public static function assertTargets(): void
    {
        if( !Config::has("rater.targets") ){
            throw new Exception("the config file rater.php must have targets kay with array value");
        }

        foreach( Config::get('rater.targets') as $alias => $class ){
            if( !class_exists($class) ){
                throw new Exception("no class founded whith that name : ". $class);
            }

            if(! ((new $class) instanceof Model) ){
                throw new Exception("a target class must be a model subclass (eloquent) : " . $class . "is not an Eloquent");
            }
        }
    }

    /**
     * @throws Exception
     */
    public static function assertMatcher(): void
    {
        if( !Config::has("rater.matcher") ){
            throw new Exception("the config file rater.php must have matcher kay with array value");
        }

        foreach( Config::get('rater.matcher') as $user => $targets ){
            if( !key_exists($user , Config::get("rater.raters")) ){
                throw new Exception("the rater from matcher array must in raters array : ". $user);
            }

            foreach($targets as $target){
                if( !key_exists($target , Config::get("rater.targets")) ){
                    throw new Exception("the target from matcher array must in tarets array : ". $target);
                }
            }
        }
    }

    /**
     * @throws Exception
     */
    public static function assertConfiguration(): void
    {

        self::assertRaters();
        self::assertTargets();
        self::assertMatcher();

        self::$configs = Config::get('rater');
    }
}
