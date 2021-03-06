<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UpdateController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $loggeduser_id = Auth::id();        
        //dd($loggeduser_id);
        $voter_row = DB::table('votes')
            ->where('user_id', '=', $loggeduser_id)
            ->get();


        $data = array();

        $reg_to_id_arr = [
            "Banat" => 1,
            "Crisana" => 1,
            "Dobrogea" => 3,
            "Maramures" => 4,
            "Moldova" => 5,
            "Muntenia" => 6,
            "Oltenia" => 7,
            "Transilvania" => 8
        ];


            $countyname_arr = DB::table('counties')
                            ->select('county_name')
                            ->get();
            $data['countyname_arr'] = $countyname_arr;

            $countyid_arr = DB::table('counties')
                            ->select('county_id')
                            ->get();
            $data['countyid_arr'] = $countyid_arr;

            $regid_arr = DB::table('counties')
                            ->select('region')
                            ->get();

            $regionid_arr = array();
            for($i = 0; $i < count($regid_arr); $i++)
                $regionid_arr[$i] = $reg_to_id_arr[$regid_arr[$i]->region]; 
        
            //dd($regionid_arr);

            $data['regionid_arr'] = $regionid_arr;

            $countyreg_arr = DB::table('counties')
                            ->select('region')
                            ->get();
            $data['countyreg_arr'] = $countyreg_arr;

            $partyname_arr = DB::table('parties')
                            ->select('party_name')
                            ->get();
            
            $countygdp_arr = DB::table('counties')
                            ->select('gdp')
                            ->get();

            $data['countygdp_arr'] = $countygdp_arr;

            $countycorr_arr = DB::table('counties')
                            ->select('corruption_level')
                            ->get();

            $data['countycorr_arr'] = $countycorr_arr;

            $data['partyname_arr'] = $partyname_arr;

            $fields = ['candidate_id', 'first_name', 'second_name'];
            $candidate_arr = DB::table('candidates')
                             ->select($fields)
                             ->get();
            $data['candidate_arr'] = $candidate_arr;



        $fields = ['candidates.first_name', 'candidates.second_name', 'candidates.party'];
        $top_arr = DB::table('candidates')
                     ->join('votes', 'candidates.candidate_id', '=', 'votes.candidate_id')
                     ->select($fields);
        $top_arr = $top_arr->addSelect(DB::raw('count(candidates.first_name) as votes_cnt'))
                               ->groupBy('candidates.first_name')
                               ->orderBy('votes_cnt', 'DESC')
                               ->take(10)
                               ->get();
        $data['top_arr'] = $top_arr;

        /*
         SELECT voturi.data_vot, voturi.timp_vot,
                          votant.nume, votant.prenume, candidat.nume, candidat.prenume
                          FROM voturi
                          INNER JOIN votant ON voturi.id_votant=votant.id_votant
                          INNER JOIN candidat ON voturi.id_candidat=candidat.id_candidat
                          ORDER BY voturi.id_votant DESC LIMIT 10
        */
        $fields = ['votes.vote_date', 'votes.vote_time', 
                    'users.first_name', 'users.second_name'];
        $last_arr = DB::table('votes')
                    ->join('users', 'votes.user_id', '=', 'users.user_id')
                    ->join('candidates', 'votes.candidate_id', '=', 'candidates.candidate_id')
                    ->select($fields);
        $last_arr = $last_arr->addSelect(DB::raw('candidates.first_name as cfirst_name'))
                    ->addSelect(DB::raw('candidates.second_name as csecond_name'))
                    ->orderBy('votes.user_id', 'desc')
                    ->take(10)
                    ->get(); 
        $data['last_arr'] = $last_arr;
        
        //dd(compact("data"));

         return view('update', compact("data"));
    }
}
