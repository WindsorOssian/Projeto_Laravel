<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use JetBrains\PhpStorm\NoReturn;

class UserScope implements Scope
{

    public function apply(Builder $builder, Model $model): void
    {
        $userId = auth()->user()->parent_user_id ?? auth()->id();
        if ($userId !== null) {
            $builder->where('id_admin', $userId);
        }
    }
}