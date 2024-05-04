<?php

namespace App\Http\Controllers;


use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class OCRController extends Controller
{

    public function upload(Request $request)
    {
        $target_file = storage_path("image1.jpg"); // Предполагается, что вы передаете файл через поле с именем 'file'

        // Проверка наличия файла
        if (!$target_file) {
            return response()->json(['error' => 'No file uploaded'], 400);
        }

        $parsedText = $this->uploadToApi($target_file);

        return response()->json(['parsedText' => $parsedText]);
    }

    public function receive(Request $request)
    {
        try {
            // Получаем данные изображения из запроса
            $imageData = $request->input('image');

             //Проверяем наличие данных изображения
            if (empty($imageData)) {
                throw new Exception("Отсутствуют данные изображения в запросе");
            }

            // Декодируем данные изображения из base64
            $imageData = base64_decode($imageData);

            // Записываем изображение в файл
            $imagePath = storage_path('image1.jpg');
            file_put_contents($imagePath, $imageData);

            // Вызываем метод uploadToApi для обработки изображения
            $parsedText = $this->uploadToApi($imagePath);
            //$this->sendTextToFirstService($parsedText);

            // Возвращаем обработанный текст
            return response()->json(['parsedText' => $parsedText]);

//            return response()->json(['success' => true], 200);

            // Обработка изображения (вызов вашего метода или логика обработки)
//            $success = $this->uploadToApi($imagePath);
//
//            if ($success) {
//                return response()->json(['success' => true]);
//            } else {
//                throw new Exception("Ошибка при обработке изображения");
//            }
        } catch (Exception $e) {
            error_log("Ошибка при отправке изображения: " . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
//        try{
//        // Проверяем наличие изображения в запросе
//            if (!$request->hasFile('image')) {
//                return response()->json(['error' => 'No image uploaded'], 400);
//            }
//    //        if (!$request->hasFile('image')) {
//    //            throw new \Exception("Изображение не найдено в запросе");
//    //        }
//            // Получаем объект файла
//            $image = $request->file('image');
//
//            // Генерация уникального имени для изображения
//            $imageName = time() . '_' . $image->getClientOriginalName();
//
//            // Сохраняем изображение временно
//            $image->storeAs('public/images', $imageName);
//
//
//            // Вызываем метод uploadToApi для обработки изображения
//            $parsedText = $this->uploadToApi(storage_path('app/temp/uploaded_image.jpg'));
//
//            $this->sendTextToFirstService($parsedText);
//
//            // Возвращаем обработанный текст
//            // return response()->json(['parsedText' => $parsedText]);
//
//            return response()->json(['success' => true], 200);
//    } catch (\Exception $e) {
//    // Обработка ошибок
//return response()->json(['success' => false, 'error' => $e->getMessage()], 400);
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
                //echo $pareValue['ParsedText'];
            }
            return $parsedText;
        } catch (Exception $err) {
            return $err->getMessage();
        }
    }

//    private function sendTextToFirstService($parsedText)
//    {
//        $client = new Client();
//
//        try {
//            $response = $client->request('POST', 'http://первый_сервис/путь_к_обработчику', [
//                'json' => ['parsedText' => $parsedText],
//            ]);
//
//            // Обработка ответа, если необходимо
//            //$statusCode = $response->getStatusCode();
////            if ($statusCode === 200) {
////                // Успешное выполнение запроса
////            } else {
////                // Обработка ошибки
////            }
//        } catch (Exception $e) {
//            // Обработка ошибки
//        }
//    }
}
