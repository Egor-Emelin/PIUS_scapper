<?php

namespace App\Http\Controllers;


use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class OCRController extends Controller
{


    public function upload(Request $request)
    {
        $target_file = storage_path("testimage.jpg"); // Предполагается, что вы передаете файл через поле с именем 'file'

        // Проверка наличия файла
        if (!$target_file) {
            return response()->json(['error' => 'No file uploaded'], 400);
        }

        $parsedText = $this->uploadToApi($target_file);

        return response()->json(['parsedText' => $parsedText]);
    }
    function uploadToApi($target_file)
    {
        $parsedResult = '';
        require __DIR__ . '/../../../vendor/autoload.php';
        $fileData = fopen($target_file, 'r');
        //dd(@$fileData);
        $client = new Client();
        try {
            $r = $client->request('POST', 'https://api.ocr.space/parse/image', [
                'headers' => ['apiKey' => 'K89404896388957'],
                'multipart' => [
                    [
                        'name' => 'file',
                        'contents' => $fileData
                    ]
                ]
            ]);
            $response = json_decode($r->getBody(), true);
            //dd(@$response);
            $parsedText = "";
            foreach ($response['ParsedResults'] as $pareValue) {
                $parsedText .= $pareValue['ParsedText'];
                echo $pareValue['ParsedText'];
            }
            return $parsedText;
        } catch (Exception $err) {
            return $err->getMessage();
        }
    }
}
