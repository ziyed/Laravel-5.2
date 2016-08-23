<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use App\models\Common;
use App\models\Job;
use App\models\Job_gig;
use App\models\Geomap_model;
use DB;
use Config;
use Session;

class JobController extends Controller {

    public $common;
    public $job;

    public function __construct() {
        $this->middleware('auth', ['except' => 'show']);
        $this->common = new Common;
        $this->job = new Job;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        //   
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {


        //  
        $data = array();
        $data['tags'] = DB::table('tags')->get();
        $data['categories'] = $this->common->allCategories();
        return view('jobs.create', ['data' => $data]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        $this->validate($request, [
            'job_title' => 'required',
            'job_price' => 'required',
        ]);

        // process receive data 
        if ($request->edit_id) {
            $whose_job = DB::table('jobs')->where('job_row_id', $request->edit_id)->first()->user_id;
            if ($whose_job != Auth::user()->id) {
                abort(403);
            }

            $this->job = $this->job->find($request->edit_id);
        }

        if ($uplaoded_image_name = $this->common->uploadImage('task_image', 'uploads/tasks')) {
            $this->job->image = $uplaoded_image_name;
        }

        $this->job->job_title = $request->job_title;
        $this->job->category_row_id = $request->category_row_id;
        $this->job->user_id = Auth::user()->id;
        $this->job->job_description = $request->job_description;
        $this->job->job_price = $request->job_price;
        $this->job->instruction = $request->instruction;

        $this->job->completion_day = $request->completion_day;
        $this->job->completion_unit = $request->completion_unit;
        $this->job->max_order_accept = $request->max_order_accept;

        if ($request->geo_address) {
            $this->job->geo_address = trim($request->geo_address);
            $Geomap_model = new Geomap_model();
            $latLang = $Geomap_model->Get_LatLng_From_Google_Maps(trim($request->geo_address));
            if ($latLang) {
                $this->job->lat = $latLang['lat'];
                $this->job->lng = $latLang['lng'];
            }
        }


        /* $this->job->has_shipping = $request->has_shipping == 'on'; 
          $this->job->shipping_amount = $request->shipping_amount;
          $this->job->shipping_country = $request->shipping_country;
          $this->job->shipping_amount_anywhere = $request->shipping_amount_anywhere;
         */

        $this->job->save();
        $job_row_id = $this->job->job_row_id;


        // tags           
        DB::table('job_tags')->where('job_row_id', $job_row_id)->delete();
        $tags = trim($request->tags);
        $tagsArray = explode(',', $tags);
        for ($i = 0; $i < count($tagsArray); $i++) {
            $tagName = $tagsArray[$i];
            $tagInfo = DB::table('tags')->where('tag_name', $tagName)->first();
            if ($tagInfo) {
                $tag_row_id = $tagInfo->tag_row_id;
            } else {
                $tag_row_id = DB::table('tags')->insertGetId(
                        [ 'tag_name' => $tagName]
                );
            }

            DB::table('job_tags')->insert(
                    [ 'tag_row_id' => $tag_row_id, 'job_row_id' => $job_row_id]
            );
        }

        // Gig
        //dd($request->gig_prices);
        $totalGig = count($request->gig_prices) - 1; // do not count the last gig which is kept as hidden.

        if ($totalGig) {
            DB::table('job_gigs')->where('job_row_id', $job_row_id)->delete();
        }

        //dd($request->gig_titles);
        for ($i = 0; $i < $totalGig; $i++) {
            DB::table('job_gigs')->insert(
                    [ 'gig_title' => $request->gig_titles[$i], 'job_row_id' => $job_row_id, 'gig_price' => $request->gig_prices[$i]]
            );
        }


        // update category count        
        DB::table('categories')
                ->where('category_row_id', $request->category_row_id)
                ->increment('product_count', 1);
        Session::flash('success-message', 'Successfully Done !');
        return Redirect::to('myJobs');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        //     
        $matchThese = ['j.job_row_id' => $id];
        $data['single_info'] = DB::table('jobs As j')
                ->leftJoin('users As u', 'j.user_id', '=', 'u.id')
                ->leftJoin('categories As c', 'c.category_row_id', '=', 'j.category_row_id')
                ->select('j.*', 'j.updated_at As job_updated_at', 'u.*', 'c.category_name')
                ->where($matchThese)
                ->first();


        $matchThese = ['job_reviews.job_row_id' => $id];
        $data['review'] = DB::table('job_reviews')
                ->where($matchThese)
                ->first();

        $data['gigs'] = DB::table('job_gigs')->where('job_row_id', $id)->get();

        $matchThese = ['jt.job_row_id' => $id];
        $data['tags'] = DB::table('job_tags As jt')
                ->Join('tags As t', 'jt.tag_row_id', '=', 't.tag_row_id')
                ->select('jt.*', 't.tag_name')
                ->where($matchThese)
                ->first();

        if (Auth::check()) {
            $data['is_my_job'] = DB::table('jobs')->where('job_row_id', $id)->where('user_id', Auth::user()->id)->count();
        } else {
            $data['is_my_job'] = 0;
        }


        return view('jobs.show', ['data' => $data]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {

        $data['categories'] = $this->common->allCategories();
        $data['single_info'] = DB::table('jobs')->where('job_row_id', $id)->first();

        //gigs              
        $data['job_gigs'] = DB::table('job_gigs')->where('job_row_id', $id)->get();
        //dd($data['job_gigs']);
        //tags
        $data['tags'] = DB::table('tags')->get();
        $matchThese = ['jt.job_row_id' => $id];
        $data['job_tags'] = DB::table('job_tags As jt')
                ->Join('tags As t', 'jt.tag_row_id', '=', 't.tag_row_id')
                ->select('jt.*', 't.tag_name')
                ->where($matchThese)
                ->get();

        $tag_name = '';
        if ($data['job_tags']) {

            foreach ($data['job_tags'] as $tag) {
                $tag_name = $tag_name . ',' . $tag->tag_name;
            }
        }

        $data['assigned_tags'] = trim($tag_name, ',');

        return view('jobs.create', ['data' => $data]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        //
    }

    function myJobs() {
        $data['items'] = $this->job->where('user_id', auth::user()->id)->orderBy('job_row_id', 'DESC')->get();
        return view('jobs.my_jobs', ['data' => $data]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        //
    }

    public function imageDelete(Request $request) {

        $job_id = $request->id;
        $file_name = $request->file_name;

        if (File::exists(public_path() . '/uploads/tasks/' . $file_name)) {
            File::delete(public_path() . '/uploads/tasks/' . $file_name);
        }

        if ($job_id) {
            $this->job = $this->job->find($job_id);
            $this->job->image = '';
            $this->job->save();
        }

        return 'ok';
    }

    //Export data

    function filterData(&$str) {
        $str = preg_replace("/\t/", "\\t", $str);
        $str = preg_replace("/\r?\n/", "\\n", $str);
        if (strstr($str, '"'))
            $str = '"' . str_replace('"', '""', $str) . '"';
    }

    function exportToCSV() {


        $data = array(
            array("First Name" => "Nitya12", ""),
            array("First Name" => "Codex12"),
            array("First Name" => "John"),
            array("First Name" => "Michael"),
            array("First Name" => "Sarah")
        );

        $data = array();
        $items = $this->job->where('user_id', auth::user()->id)->get();

        foreach ($items as $item) {
            $tempData []["Job Title"] = $item->job_title;
            $tempData []["Job Description"] = $item->job_description;
        }


        $data = $tempData;



        /*   Works..
          $i = 0;
          foreach($items  as $item)
          {

          $data [$i]["Job Description"] =  $item->job_description;
          $data [$i]["Job Title22"] =  $item->job_title;
          $i++;
          }
         */
        $fileName = "codexworld_export_data" . date('Ymd') . ".xls";

        // headers for download
        header("Content-Disposition: attachment; filename=\"$fileName\"");
        header("Content-Type: application/vnd.ms-excel");

        $flag = false;
        foreach ($data as $row) {
            if (!$flag) {
                // display column names as first row
                echo implode("\t", array_keys($row)) . "\n";
                $flag = true;
            }
            // filter data
            //array_walk($row, 'filterData');
            echo implode("\t", array_values($row)) . "\n";
        }

        exit;
    }

}
