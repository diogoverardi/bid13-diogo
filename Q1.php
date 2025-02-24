<?php

function isValidPhoneNumber($phone_number, $customer_id, $api_key) {
	
	# I have add this data validation for the number
	if (empty($phone_number)) {
		return false;
	}
    
    $api_url = "https://rest-ww.telesign.com/v1/phoneid/$phone_number";
    
    $ch = curl_init();

    #implemented curl_setopt_array()
    curl_setopt_array($ch, [
        CURLOPT_URL => $api_url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode(["consent" => ["method" => 1]]), # added this as requested in the documentation
        CURLOPT_HTTPHEADER => [
            'Accept: application/json',
            'Authorization: Basic ' . base64_encode("$customer_id:$api_key"),
            'Content-Type: application/json',
        ],
    ]);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code !== 200) {
        return false; // API request failed
    }
    
    $data = json_decode($response, true);
    
    # updated the correct data location inside the API response
    if (!isset($data['phone_type']['description'])) { 
        return false; // Unexpected API response
    }
    
    $valid_types = ["FIXED_LINE", "MOBILE", "VALID"]; 
    return in_array(strtoupper($data['phone_type']['description']), $valid_types); # updated the correct data location inside the API response
}

// Usage example
$phone_number = "18193181649"; // Replace with actual phone number
$customer_id = "E927AD0D-B507-4D17-A139-660BE421A77E";
$api_key = "0vcTgssWZ7E/Z+pZ4EIio/jZ1OwXkhwlLFblHgZf1Gxvta/sPZBIvsBBDbR8tTJkuJUZgGh2sy1xG192tNeNXQ==";
$result = isValidPhoneNumber($phone_number, $customer_id, $api_key);
var_dump($result);

#Diogo Verardi - 24FEB2025 - Quebec,Canada

/**

Analysis and modifications 


- This is the response the you'll get if you try and debug the code from chatgpt:
string(218) "{"errors": [{"code": -40005, "description": "Method Not Allowed"}], "reference_id": null, "status": {"code": 500, "description": "Transaction not attempted", "updated_on": "2025-02-18T11:41:39Z"}, "sub_resource": null}" 

and this is because the API Request is missing data, such as the actual Request body (CURLOPT_POSTFIELDS), after reviewing their documentation (https://developer.telesign.com/enterprise/reference/submitphonenumberforidentity) if you simply add this line it works:    curl_setopt($ch, CURLOPT_POSTFIELDS, '{"consent":{"method":1}}');

    
- I have also added validation for the parameter $phone_number.


- I strongly suggest moving the variables $api_url and $valid_types to Constants. I didn't do it because this code example doesn't use classes, but in real-world I'd 100% have done it. 


- Instead of repeating lines of code with curl_setopt() I have used curl_setopt_array(), readability is improved.

- Added the JSON parameter in the CURL HTTPHEADER, as their API requests in the PHP example here: https://developer.telesign.com/enterprise/reference/submitphonenumberforidentity

- The final error was array key "$data['numbering']['phone_type']" which is just completely wrong, a simple look in the API documentation or even a var_dump($data) will give you the right location for this data, which is $data['phone_type']['description']

 **/ 