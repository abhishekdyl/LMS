<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;
use Alimranahmed\LaraOCR\Services\OcrAbstract;
use Imagick;

class fileupload extends Controller
{

    public function readImage2(Request $req){
        // Get the uploaded PDF file
        $pdf = $req->file('image'); // Laravel form request 'image'
        
        if ($pdf && $pdf->isValid()) {
            $pdfFile = $pdf->getPathName();
            $outputDir = storage_path('app/public/output_images/');
            
            if (!is_dir($outputDir)) {
                mkdir($outputDir, 0777, true);  // Create the output directory if it doesn't exist
            }

            $gsPath = 'gswin64c';  // Or use the full path to Ghostscript executable
            $resolution = 600;
            $command = "$gsPath -sDEVICE=pngalpha -o {$outputDir}output-%d.png -r$resolution $pdfFile";
            $output = shell_exec($command);

            // If the command executed successfully, process the generated images
            if ($output !== null) {
                $imageFiles = glob($outputDir . 'output-*.png');

                foreach ($imageFiles as $imageFile) {
                    if (file_exists($imageFile)) {
                        // Process each image using OCR
                        $ocr = app()->make(OcrAbstract::class);

                        // Detect language or specify a language code (for multilingual support)
                        // For Tesseract, you can specify languages like "eng" for English, "spa" for Spanish, etc.
                        // Use 'eng+spa' for English and Spanish together, etc.
                        $languages = 'eng+fra';  // Default to English
                        $parsedText = $ocr->scan($imageFile, $languages); // Specify the language for OCR scan
                        
                        // Debug output for the OCR result
                        echo "<pre>-----";
                        print_r($parsedText);
                        echo "</pre>";

                        // Optionally, you can store or return the parsed text
                        // return view('lara_ocr.parsed_text', compact('parsedText'));
                    }
                }
            } else {
                echo "Error executing Ghostscript command: $output";
            }
        } else {
            echo "No valid PDF file uploaded.";
        }
    }


    function uploadimg(Request $req){
        return $req->file('doc')->store('img');
        // return $req->file('doc')->storeAs('img',getClientOriginalName('aaa'));
    }

    function csvupload(Request $req){
        
        $req->validate([
            'file'=>'required|mimes:csv|max:2048',
        ]);

        $file = $req->file("file");
        $handle = fopen($file->getRealPath(),'r');
        $header = fgetcsv($handle);
        $csvData = [];

        while (($raw = fgetcsv($handle)) !== false) {
            $data = array_combine($header, $raw);

            // $validator = Validator::make($data,[
            //     'fullname' => 'required|string|max:255',
            //     'email' => 'required|email|unique:email',
            // ]);

            // if($validator->fails()){
            //     continue;
            // }

            $csvData[] = $data; 
        }

        fclose($handle);

        echo '<pre>';
        print_r($csvData);
        die;


    }
 

    public function showPdf(Request $req){
        // $filename = 'ejlXYRCDQWZfYuoR4HcImEJyY1mb6sdFKPKBC30i.pdf';
        // $url = asset('storage/app/img/' . $filename);
        // return '<a href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '" target="_blank">View PDF</a>';

        return view('/products/ejlXYRCDQWZfYuoR4HcImEJyY1mb6sdFKPKBC30i.pdf');
        // return 'storage/app/img/ejlXYRCDQWZfYuoR4HcImEJyY1mb6sdFKPKBC30i.pdf';
    }


}
//  storage/app/img/ejlXYRCDQWZfYuoR4HcImEJyY1mb6sdFKPKBC30i.pdf



<?php

// namespace App\Http\Controllers\User;

// use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
// use thiagoalessio\TesseractOCR\TesseractOCR;
// use \Imagick;
// class DashboardController extends Controller
// {
//     public function index() {
//         $user = auth()->user();
//         // dd($user->currentmemberships->role->hasPermissionTo('plus_viewteachers'));
//         return view('user.index');
//     }
//     public function testing()
//     {
//         // echo phpinfo();
//         // die;
//         $pdfPath = 'testpdf/pdf1.1.pdf';  // Your PDF file
//         $outputDir = 'testpdf/images';  // Directory to store images
//         if (!is_dir($outputDir)) {
//             mkdir($outputDir, 0777, true);
//         }

//         // $imagePaths = $this->pdfToImages($pdfPath, $outputDir);
//         $imagePaths = array("testpdf/images/page_0.jpg", "testpdf/images/page_1.jpg","testpdf/images/page_2.jpg","testpdf/images/page_3.jpg");
//         // $imagePaths = array("testpdf/images/page_0.jpg");
//         $extractedText = $this->extractTextFromImages($imagePaths);
//         // dd($extractedText);
//         $questions = $this->extractQuestions($extractedText);
//         echo "<pre>";
//         print_r($questions);
//         echo "</pre>";
//         // echo nl2br($extractedText);
//         die;

//     }
//     public function pdfToImages($pdfPath, $outputDir) {
//         $pdfPath = realpath($pdfPath);
//         $imagick = new Imagick();
//         $imagick->setResolution(300, 300); // High resolution for better OCR
//         $imagick->readImage($pdfPath);
        
//         $imagePaths = [];
//         foreach ($imagick as $i => $image) {
//             $imagePath = "{$outputDir}/page_{$i}.jpg";
//             $image->setImageFormat('jpg');
//             $image->writeImage($imagePath);
//             $imagePaths[] = $imagePath;
//         }
        
//         return $imagePaths;
//     }
//     // Extract text using OCR
//     public function extractTextFromImages($imagePaths) {
//         $fullText = "";
//         foreach ($imagePaths as $image) {
//             $text = (new TesseractOCR($image))
//                 ->run();
//             $fullText .= "\n" . $text;
//         }
//         return $fullText;
//     }
//     public function extractQuestions($text) {
//         // dd($text);
//         // $pattern = '/(\d+)-\s(.*?)(?:\n(?:O|\@|\CO|O\)|@|®).*?)+/s';
//         $pattern = '/(\d+-\s.*?)(?=\n\d+-|\Z)/s';

//         // Match all questions
//         preg_match_all($pattern, $text, $matches, PREG_SET_ORDER);

//         $questions = [];

//         foreach ($matches as $match) {
//             $question = $match[0];
//             $questionNo = null;
//             $questionText = null;
//             preg_match_all('/^(\d+)-\s(.+?)(?=\n[O@]|\n\d+-|\Z)/s', $question, $questionMatches, PREG_SET_ORDER);
//             if(isset($questionMatches[0][1])){
//                 $questionNo = $questionMatches[0][1];
//             }
//             if(isset($questionMatches[0][2])){
//                 $questionText = $questionMatches[0][2];
//             }

//             preg_match_all('/(?:O|\@|\CO|O\)|0\)|@|®)\s(.*)/', $question, $optionMatches);

//             $options = array();
//             $selectedAnswer = null;
//             if(isset($optionMatches[1])){
//                 $options = array_map('trim', $optionMatches[1]);
//             }
//             // Identify selected answer (marked with '@')
//             if($optionMatches[0]){
//                 foreach ($optionMatches[0] as $index => $optionLine) {
//                     if (strpos($optionLine, '@') !== false) {
//                         $selectedAnswer = $options[$index];
//                     }
//                 }        
//             }
//             $questions[] = [
//                 'question' => $question,
//                 'questionNo' => $questionNo,
//                 'questiontext' => $questionText,
//                 'options' => $options,
//                 'selected_answer' => $selectedAnswer
//             ];
//         }

//         return $questions;
//     }

// }

?>
