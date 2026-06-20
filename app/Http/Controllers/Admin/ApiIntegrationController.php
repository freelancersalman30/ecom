<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentGateway;
use App\Models\SmsGateway;
use App\Models\Courierapi;
use Toastr;
use File;
use Str;
use Image;
use DB;

class ApiIntegrationController extends Controller
{
    
     
    public function pay_manage ()
    {
        $bkash = PaymentGateway::where('type','=','bkash')->first();
        $shurjopay = PaymentGateway::where('type','=','shurjopay')->first();
$uddoktapay = PaymentGateway::where('type', 'uddoktapay')->first();
return view('backEnd.apiintegration.pay_manage', compact('bkash', 'shurjopay', 'uddoktapay'));

    }
    
   public function pay_update(Request $request)
{
    $update_data = \App\Models\PaymentGateway::find($request->id);
    $input = $request->all();
    $input['status'] = $request->status ? 1 : 0;
    $update_data->update($input);

    // ✅ যদি গেটওয়ে টাইপ হয় UddoktaPay
    if ($update_data->type === 'uddoktapay') {
        $this->updateEnvFile('UDDOKTAPAY_API_KEY', $request->app_key);
        $this->updateEnvFile('UDDOKTAPAY_API_URL', $request->base_url);
    }

    \Toastr::success('Success', ucfirst($update_data->type) . ' settings updated successfully');
    return redirect()->back();
}

/**
 * 🔧 Helper function: Update or add key in .env file
 */
private function updateEnvFile($key, $value)
{
    $path = base_path('.env');

    if (file_exists($path)) {
        $oldValue = env($key);

        if (strpos(file_get_contents($path), $key) !== false) {
            // Replace old value
            file_put_contents($path, str_replace(
                $key . '=' . $oldValue,
                $key . '=' . $value,
                file_get_contents($path)
            ));
        } else {
            // Add new line if not exists
            file_put_contents($path, PHP_EOL . $key . '=' . $value, FILE_APPEND);
        }
    }
}

    
    public function sms_manage ()
    {  
        $sms = SmsGateway::first();
        return view('backEnd.apiintegration.sms_manage',compact('sms'));
    }
    
public function sms_update(Request $request)
{
    $update_data = SmsGateway::find($request->id);
    $input = $request->all();
    $input['status'] = $request->status?1:0;
    $input['order'] = $request->order?1:0;
    $input['forget_pass'] = $request->forget_pass?1:0;
    $input['password_g'] = $request->password_g?1:0;

    // DB Update
    $update_data->update($input);

    // ============================
    //  🔥 HERE: Save to .env file
    // ============================
    if ($request->filled('admin_phone_list')) {
        $this->updateEnvFile('ADMIN_PHONE_LIST', $request->admin_phone_list);
    }

    Toastr::success('Success','Data update successfully');
    return redirect()->back();
}

    
    public function courier_manage ()
    {
        $steadfast = Courierapi::where('type','=','steadfast')->first();
        $pathao = Courierapi::where('type','=','pathao')->first();
        return view('backEnd.apiintegration.courier_manage',compact('steadfast','pathao'));
    }
    
    public function courier_update (Request $request)
    {
      
        $update_data = Courierapi::find($request->id);
        $input = $request->all();
        $input['status'] = $request->status?1:0;
        $update_data->update($input);
        
        Toastr::success('Success','Data update successfully');
        return redirect()->back();
    }
    public function sms_custom_send_page()
{
    return view('backEnd.apiintegration.sms_custom_send');
}

public function sms_custom_send(Request $request)
{
    $request->validate([
        'phone' => 'required|string',
        'message' => 'required|string|max:500',
    ]);

    try {
        // ✅ তোমার গেটওয়ে ইনফো নিচ্ছি
        $sms_gateway = \App\Models\SmsGateway::where('status', 1)->first();
        if (!$sms_gateway) {
            Toastr::error('Failed', 'SMS Gateway not configured.');
            return back();
        }

        // ✅ ফোন নাম্বার পরিষ্কার
        $number = preg_replace('/[^0-9]/', '', $request->phone);
        $message = $request->message;

        // ✅ API Data প্রস্তুত করা
        $api_key = $sms_gateway->api_key;
        $senderid = $sms_gateway->senderid ?? $sms_gateway->serderid ?? '';
        $url = $sms_gateway->url;

        // ✅ Curl দিয়ে Send করা (official working method)
        $postData = [
            'api_key' => $api_key,
            'type' => 'text',
            'number' => $number,
            'senderid' => $senderid,
            'message' => $message,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        // ✅ লগে রেসপন্স দেখা
        \Log::info("BulkSMSBD Manual Response: " . $response);

        // ✅ যদি কোনো Error থাকে
        if ($err) {
            Toastr::error('Error', 'cURL Error: ' . $err);
            return back();
        }

        // ✅ Response ডিকোড করে চেক করা
        if (stripos($response, 'SMS sent successfully') !== false || stripos($response, '202') !== false) {
            Toastr::success('Success', 'SMS sent successfully!');
        } else {
            Toastr::warning('Failed', 'API Response: ' . $response);
        }

        return back();

    } catch (\Exception $e) {
        \Log::error("Manual SMS Send Failed: " . $e->getMessage());
        Toastr::error('Failed', 'SMS sending failed: ' . $e->getMessage());
        return back();
    }
}


}