<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class checkSubs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subs:control';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $subs = Subscription::isNotEnded()->limit(10)->get();

        foreach ($subs as $sub){
            $req = Request::create('/api/v1/checkSubs/','POST');
            $res = Route::dispatch($req);
            $result = json_decode($res->getContent());
            if($result->status){
                $sub->expire_date = Carbon::parse($sub->expire_date)->add(1,'month')->format('Y-m-d H:i:s');
            } else {
                $sub->is_ended = 1;
            }

            $sub->save();
        }
    }
}
