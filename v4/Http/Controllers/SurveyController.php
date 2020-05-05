<?php

namespace v4\Http\Controllers;
use v4\Models\Survey;
use Illuminate\Http\Request;
use Illuminate\Validation\Validator;
use Illuminate\Support\Facades\Auth;


class SurveyController extends V4Controller
{
    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(int $id)
    {
        $survey = Survey::find($id);
        $this->authorize('show', $survey);
        return response()->json(['survey' => $survey]);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index()
    {
        $this->authorize('index', Survey::class);
        return response()->json(['results' => Survey::all()]);
    }
}
