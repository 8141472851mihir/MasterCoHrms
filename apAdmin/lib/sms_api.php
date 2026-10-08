<?php
class sms_api
{
    function sendWhatsAppMessage($mobile, $template_primary_id, $template_id, $template_params = [], $options = ["document_path" => null, "image_path" => null, "video_path" => null, "dynamic_button_urls" => null, "location_latitude" => null, "location_longitude" => null, "location_name" => null, "location_address" => null], $d, $country_code = '91')
    {   
        $whatsapp_api_data = $d->whatsapp_api_data();
        $whatsapp_api_access_token = $whatsapp_api_data['whatsapp_api_access_token'];
        $company_api_key = $whatsapp_api_data['company_api_key'];
        $whatsapp_api_url = $whatsapp_api_data['whatsapp_api_url'];
        $defaults = ['document_path' => null, 'image_path' => null, 'video_path' => null];
        $config = array_merge($defaults, $options);
        if (empty($mobile) || empty($template_id)) {
            return ['success' => false, 'http_code' => 400, 'response' => 'Mobile number and template ID are required', 'error' => 'Missing required parameters'];
        }
        $data = [
            "id" => $template_primary_id,
            "template_id" => $template_id,
            "userNumber" => $country_code . $mobile,
            "template_body_text_variable" => is_array($template_params) ? json_encode($template_params) : $template_params,
        ];
        if (isset($config['dynamic_button_urls']) && $config['dynamic_button_urls'] != "") {
            $data['dynamic_button_urls'] = json_encode($config['dynamic_button_urls'], JSON_UNESCAPED_SLASHES);
        }
        if (isset($config['location_latitude']) && $config['location_latitude'] != "") {
            $data['location_latitude'] = $config['location_latitude'];
        }
        if (isset($config['location_longitude']) && $config['location_longitude'] != "") {
            $data['location_longitude'] = $config['location_longitude'];
        }
        if (isset($config['location_name']) && $config['location_name'] != "") {
            $data['location_name'] = $config['location_name'];
        }
        if (isset($config['location_address']) && $config['location_address'] != "") {
            $data['location_address'] = $config['location_address'];
        }
        $encryptedData = $d->manage_encryption(1, $data);

        $postFields = ['data' => $encryptedData];
        if (isset($config['document_path']) && $config['document_path'] != "") {
            $postFields['template_header_document'] = new CURLFILE($config['document_path']);
        }
        if (isset($config['image_path']) && $config['image_path'] != "") {
            $postFields['template_header_image'] = new CURLFILE($config['image_path']);
        }
        if (isset($config['video_path']) && $config['video_path'] != "") {
            $postFields['template_header_video'] = new CURLFILE($config['video_path']);
        }
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $whatsapp_api_url . 'send-template',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $postFields,
            CURLOPT_HTTPHEADER => [
                'company_api_key: ' . $company_api_key,
                'platform-key: external',
                'authorization: Bearer ' . $whatsapp_api_access_token
            ],
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);
        curl_close($curl);
        return [
            'success' => ($httpCode >= 200 && $httpCode < 300) && !$error,
            'http_code' => $httpCode,
            'response' => $response,
            'error' => $error ?: null
        ];
    }
}
