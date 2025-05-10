<h2>IMG</h2>
<form action="/readimg" enctype="multipart/form-data" method="post">
    <!-- @csrf -->
    <meta name="csrf-token" id="tokan" content="{{ csrf_token() }}">
    <input type="file" name="image" id="file" placeholder="select multiple image">
    <button type="submit">Save</button>
    <button type="button" class="upcsvfile">Save ajax</button>
</form>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
    $(document).ready(function(){
      $(".upcsvfile").click(function(){
        var formdata = new FormData();
        var img = $("#file")[0].files[0];
        if(img){
            formdata.append('file',img);
        }
        // Get CSRF token
        // var token = $('meta[name="csrf-token"]').attr('content');

        var token = $('#tokan').attr('content');

        $.ajax({
            url: "{{ route('csvupload')  }}",
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': token
            },
            data: formdata,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log("Success:", response);
                alert("Image uploaded successfully!");
            },
            error: function(xhr) {
                console.error("Error:", xhr.responseText);
                alert("Upload failed.");
            }
        });


      });
    });
</script>
