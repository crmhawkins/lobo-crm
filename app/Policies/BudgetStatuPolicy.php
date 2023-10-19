<?php

namespace App\Policies;

use App\Models\Budget_statu;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BudgetStatuPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Budget_statu  $budgetStatu
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Budget_statu $budgetStatu)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Budget_statu  $budgetStatu
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Budget_statu $budgetStatu)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Budget_statu  $budgetStatu
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Budget_statu $budgetStatu)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Budget_statu  $budgetStatu
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Budget_statu $budgetStatu)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Budget_statu  $budgetStatu
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Budget_statu $budgetStatu)
    {
        //
    }
}
