<?php

namespace App\Http\Controllers;

use App\Models\Project_priority;
use App\Http\Requests\StoreProject_priorityRequest;
use App\Http\Requests\UpdateProject_priorityRequest;

class ProjectPriorityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  \App\Http\Requests\StoreProject_priorityRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProject_priorityRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Project_priority  $project_priority
     * @return \Illuminate\Http\Response
     */
    public function show(Project_priority $project_priority)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Project_priority  $project_priority
     * @return \Illuminate\Http\Response
     */
    public function edit(Project_priority $project_priority)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateProject_priorityRequest  $request
     * @param  \App\Models\Project_priority  $project_priority
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProject_priorityRequest $request, Project_priority $project_priority)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Project_priority  $project_priority
     * @return \Illuminate\Http\Response
     */
    public function destroy(Project_priority $project_priority)
    {
        //
    }
}
