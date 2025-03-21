<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Mail;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class mailContoller extends Controller
{

    public function txt_mail()
    {
        $info = [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'mess' => 'Welcome to our platform!',
        ];
        Mail::send(['html' => 'mail'], $info, function ($message)
        { 
            $message->to('test21@yopmail.com', 'W3SCHOOLS')
                ->subject('Basic test eMail from W3schools.');
            $message->from('tdemo775@gmail.com', 'abhishek');
        });
        echo "Successfully sent the email";
    }

    public function html_mail()
    {
        $info = array(            
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'mess' => 'Welcome to our platform!',
        );
        Mail::send('mail', $info, function ($message)
        {
            $message->to('test21@yopmail.com', 'w3schools')
                ->subject('HTML test eMail from W3schools.');
            $message->from('tdemo775@gmail.com', 'abhishek html');
        });
        echo "Successfully sent the email";
    }

    public function attached_mail()
    {
        $info = array(
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'mess' => 'Welcome to our platform!',
        );
        Mail::send('mail', $info, function ($message)
        {
            $message->to('test21@yopmail.com', 'w3schools')
                ->subject('Test eMail with an attachment from W3schools.');
            $message->attach('C:\wamp64\www\example-app\storage\app\img\aaa.jpg');
            // $message->attach('D:\laravel_main\laravel\public\uploads\message_mail.txt');
            $message->from('tdemo775@gmail.com', 'abhishek attach');
        });
        echo "Successfully sent the email";
    }

}
