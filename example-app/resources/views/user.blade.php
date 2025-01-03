
<x-header />

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>
    </head>
    <body>
        <h3>File upload data</h3>
        
    <form action="uploadfile" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="doc"><br/>
        <input type="submit">
    </form>


    <br/>
    <br/>
    <br/>


    </body>
</html>  