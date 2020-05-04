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
     * @param  \Modules\Block  $block
     * @return \Illuminate\Http\Response
     */
    public function show(Survey $survey)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \Modules\Block  $block
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $this->authorize('index', Survey::class);
        return response()->json(['results' => Survey::all()]);
    }
}
