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
        //create query advertiser
        $advertisers = DB::table('advertisers')
                        ->select('advertisers.idadvertiser', 'advertisers.iduser', 'advertisers.company_name',
                        'advertisers.business_registration', 'advertisers.business_category', 'advertisers.representative_name',
                        'advertisers.representative_contactno', 'advertisers.company_website', 'advertisers.email',
                        'advertisers.password', 'advertisers.delete' )
                        ->where('advertisers.delete', 0)
                        ->get();

        //the cursor method may be used to greatly reduce your memory usage:
        $cursor = $advertisers;

        if($cursor->count() > 0 ) {                
                return response()->json($cursor);
        } else {
            echo json_encode(
                array("message" => "No advertiser are found.")
            );
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
                    'password' => $new->password,
                ]);                
            }
        } else {
            echo json_encode(
                array("message" => "Advertiser not found.")
            );
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
