<?php

namespace Karimaouaouda\LaravelRater\Traits;

use Karimaouaouda\LaravelRater\Rater;

/**
 * @mixin Rater
**/
trait SetGet{
    public static function tableName(): string
    {
        return self::$tableName;
    }

    /**
     * @throws \Exception
     */
    public static function setAmountKey(string $newKey = "amount"): void
    {
        if( empty($newKey) ) {
            throw new \Exception("sorry you must provide a valid string");
        }

        self::$amountKey = $newKey;
    }

    /**
     * @throws \Exception
     */
    public static function setCommentKey(string $newKey = "comment"): void
    {
        if( empty($newKey) ) {
            throw new \Exception("sorry you must provide a valid string");
        }

        self::$comment_key = $newKey;
    }


}
