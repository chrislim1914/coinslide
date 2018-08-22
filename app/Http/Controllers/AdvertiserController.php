<?php

namespace App\Http\Controllers;

use App\Advertiser;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use App\Http\Controllers\PasswordEncrypt;

class AdvertiserController extends Controller {

    /**
     * method to display list of advertiser
     * 
     * @return response
     */
    public function advertiserList(){
        /**
         * create union create to merge query without subscriber by using NOT IN condition and insert value of 0
         * 
         * the second query is to find unsubscribe count and subscribe count is equal then the subscription value = 0
         * 
         * the third query is advertiser with link on subscribe table and count the subscription but filter the enddate
         * to avoid any miscount value base on second query
         * 
         */
        $advertisers = DB::table('advertisers')
                        ->select('advertisers.idadvertiser', 
                                'advertisers.iduser', 
                                'advertisers.company_name',
                                'advertisers.business_registration', 
                                'advertisers.business_category', 
                                'advertisers.representative_name',
                                'advertisers.representative_contactno', 
                                'advertisers.company_website', 
                                'advertisers.email',
                                DB::raw("(0) as subscriber"))
                                ->where('advertisers.delete', 0)
                                ->whereNotIn('advertisers.idadvertiser', DB::table('subscriptions')
                                                                        ->select('subscriptions.idadvertiser')
                                );
        $advertiserAllEnded = DB::table('advertisers')
                                ->join('subscriptions', 'advertisers.idadvertiser', '=', 'subscriptions.idadvertiser')
                                ->select('advertisers.idadvertiser', 
                                        'advertisers.iduser', 
                                        'advertisers.company_name',
                                        'advertisers.business_registration', 
                                        'advertisers.business_category', 
                                        'advertisers.representative_name',
                                        'advertisers.representative_contactno', 
                                        'advertisers.company_website', 
                                        'advertisers.email',
                                        DB::raw("(0) as subscriber"))
                                ->where('advertisers.delete', 0)
                                ->groupBy('advertisers.idadvertiser')
                                ->having(DB::raw("Count(subscriptions.startdate)"), '=', DB::raw("Count(subscriptions.enddate)"));

        $advertiserswithSub = DB::table('advertisers')
                        ->join('subscriptions', 'advertisers.idadvertiser', '=', 'subscriptions.idadvertiser')
                        ->select('advertisers.idadvertiser', 
                                'advertisers.iduser', 
                                'advertisers.company_name',
                                'advertisers.business_registration', 
                                'advertisers.business_category', 
                                'advertisers.representative_name',
                                'advertisers.representative_contactno', 
                                'advertisers.company_website', 
                                'advertisers.email',
                                DB::raw('count(subscriptions.idsubscription) as subscriber'))
                        ->where('advertisers.delete', 0)
                        ->whereNull('subscriptions.enddate')                        
                        ->union($advertisers)
                        ->union($advertiserAllEnded)
                        ->groupBy('advertisers.idadvertiser')
                        ->orderBy('subscriber', 'DESC')
                        ->get();

        //the cursor method may be used to greatly reduce your memory usage:
        $cursor = $advertiserswithSub;

        if($cursor->count() > 0 ) {                
            return response()->json($cursor);
        } else {
            return response()->json([
                "message" => "No advertiser are found."
            ]);
        }
    }

    /**
     * method to read single advertiser
     * 
     * @param $id
     * @return response
     */
    public function readAdvertiser($id){
        //find Advertiser info
        $advertiser = Advertiser::where('idadvertiser', $id)
                                ->where('delete', 0)
                                ->get();

        //the cursor method may be used to greatly reduce your memory usage:
        $cursor = $advertiser;

        if($cursor->count() > 0 ) {                   
            foreach($cursor as $new) {
                return response()->json([
                    'idadvertiser' => $new->idadvertiser,
                    'iduser' => $new->iduser,
                    'company_name' => $new->company_name,
                    'business_registration' => $new->business_registration,
                    'business_category' => $new->business_category,
                    'representative_name' => $new->representative_name,
                    'representative_contactno' => $new->representative_contactno,
                    'company_website' => $new->company_website,
                    'email' => $new->email,
                ]);                
            }
        } else {
            return response()->json([
                "message" => "No advertiser are found."
            ]);
        }
    }

    /**
     * method to create Advertiser
     * 
     * @param Request $request
     * 
     * @return response
     */
    public function createAdvertiser(Request $request){
        
        $advertiser = new Advertiser();

        $advertiser->idadvertiser               = $request->idadvertiser;
        $advertiser->company_name               = $request->company_name;
        $advertiser->business_registration      = $request->business_registration;
        $advertiser->business_category          = $request->business_category;
        $advertiser->representative_name        = $request->representative_name;
        $advertiser->representative_contactno   = $request->representative_contactno;
        $advertiser->company_website            = $request->company_website;
        $advertiser->email                      = $request->email;

        /**
         * email sender here to verify the registration
         * 
         * also create new table to log all created verification code
         * to match the advertiser code entry
         */

        if($advertiser->save()) {
            echo json_encode(
                array("message" => "Email has been sent.")
            );
        } else {
            echo json_encode(
                array("message" => "Error encountered.")
            );
        }

    }

    /**
     * method to save the newly registered advertiser password
     * 
     * @param $id
     * 
     * @return response
     */
    public function insertPassword(Request $request, $id){

        //first retrieved the advertiser
        $advertiser = Advertiser::where('idadvertiser', $id)->get();

        if($advertiser->count() > 0){
            $hash = new PasswordEncrypt();
            $insertPass = Advertiser::where('idadvertiser', $id);
            if($insertPass->update([
                'password' => $hash->hash($request->password)//password hash
            ])){
                echo json_encode(
                    array("message" => "Password is set.")
                );
            } else {
                echo json_encode(
                    array("message" => "Failed to set password.")
                );
            }
        } else {
            echo json_encode(
                array("message" => "advertiser not found.")
            );
        }

    }

    /**
     * method to update advertiser info
     * 
     * @param $id
     * 
     * @return response
     */
    public function updateAdvertiser(Request $request, $id){

        //first retrieved the advertiser
        $advertiser = Advertiser::where('idadvertiser', $id)->get();

        if($advertiser->count() > 0){
            $insertPass = Advertiser::where('idadvertiser', $id);
            if($insertPass->update([
                'company_name'              => $request->company_name,
                'business_registration'     => $request->business_registration,
                'business_category'         => $request->business_category,
                'representative_name'       => $request->representative_name,
                'representative_contactno'  => $request->representative_contactno,
                'company_website'           => $request->company_website,
            ])){
                echo json_encode(
                    array("message" => "Advertiser info Updated.")
                );
            } else {
                echo json_encode(
                    array("message" => "There is nothing to update.")
                );
            }
        } else {
            echo json_encode(
                array("message" => "advertiser not found.")
            );
        }
    }
}
