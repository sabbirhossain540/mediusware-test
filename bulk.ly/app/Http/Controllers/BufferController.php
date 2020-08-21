<?php

namespace Bulkly\Http\Controllers;

use Illuminate\Http\Request;

use Bulkly\BufferPosting;

use Bulkly\SocialPostGroups;

class BufferController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    { 
        $search = request()->query('search');
        $groupSearch = request()->query('groupSearch');
        
        if($search){
            $allBufferPost = BufferPosting::where('post_text','LIKE', "%$search%")->paginate(10);
        }else{
            $allBufferPost = BufferPosting::paginate(10);
        }
        
        return view('bufferpost.index')->with('bufferPosts', $allBufferPost)->with('socialGroups', SocialPostGroups::all());
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
