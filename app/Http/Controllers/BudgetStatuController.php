<?php

namespace App\Http\Controllers;

use App\Models\Budget_statu;
use App\Http\Requests\StoreBudget_statuRequest;
use App\Http\Requests\UpdateBudget_statuRequest;

class BudgetStatuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $budgetStatus = Budget_statu::all();

        return view('budget-status.index', compact('budgetStatus'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreBudget_statuRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreBudget_statuRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Budget_statu  $budget_statu
     * @return \Illuminate\Http\Response
     */
    public function show(Budget_statu $budget_statu)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Budget_statu  $budget_statu
     * @return \Illuminate\Http\Response
     */
    public function edit(Budget_statu $budget_statu)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateBudget_statuRequest  $request
     * @param  \App\Models\Budget_statu  $budget_statu
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBudget_statuRequest $request, Budget_statu $budget_statu)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Budget_statu  $budget_statu
     * @return \Illuminate\Http\Response
     */
    public function destroy(Budget_statu $budget_statu)
    {
        //
    }
}
