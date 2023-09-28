<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Artisan;

class OtherController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function clear_cache(){
        // \Artisan::call("optimize:clear");
        \Artisan::call("cache:clear");
        \Artisan::call("config:clear");
        \Artisan::call("view:clear");
        \Artisan::call("event:clear");
        \Artisan::call("route:clear");
        echo "cache cleared"; die;
    }

    public function migrate_run(){
        \Artisan::call("migrate");
        echo "new migrations run successful";die;
    }

    public function dbseeding($seedername){
        $command = 'db:seed --class='.$seedername;
        \Artisan::call($command);
        echo "seeder run successfully";die;
    }

    public function migrate_refresh_seed_run(){
        \Artisan::call("migrate:refresh --seed");
        echo "migration refresh seed run successful";die;
    }

    public function gotoadminlogin(){
        return redirect()->route('superadmin.login');
    }
}
