@extends('layouts.master')
@section('content')
<div class="card cst-table border-0 shadow-sm">
    <div class="card-header secondory-custom-class fs-5">
        Create Files
    </div>
    <h3 class="text-success" align="center">Upload file</h3>
    <br>
    <div class="container">
        <div class="panel-group">

            <div class="panel panel-primary">
                <div class="panel-heading">File Uploader</div>
                <div class="panel-body">
                    <div class="form-group">
                        <label class="control-label col-sm-2">Select Image:</label>
                        <div class="col-sm-5">
                            <input type="file" id="file" name="files[]" multiple>
                        </div>
                    </div>
                </div>
                <div class="progress">
                    <div id="progress-bar" class="progress-bar" aria-valuenow="" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                        0%
                    </div>
                </div>
                <div id="success" class="row"></div>
            </div>
        </div>
    </div>

</div>
@endsection

<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script>
    $(document).ready(function() {
        $('#file').on('change', function() {
            var formData = new FormData();
            console.log("this.files[0]- ", this.files);
            if (this.files.length) {
                formData.append('file', this.files[0]);
                $('#file-error').text('');
                $('#success-message').hide();
                $('#error-message').hide();

                $.ajax({
                    url: "{{ route('admin.files.store') }}",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    xhr: function() {
                        var xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener('progress', function(e) {
                            console.log("e- ", e);
                            if (e.lengthComputable) {
                                var percentComplete = e.loaded / e.total * 100;
                                $('#progress-bar').css('width', percentComplete + '%');
                                $('#progress-bar').text(percentComplete.toFixed(0) + '%');
                            }
                        }, false);
                        return xhr;
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#success-message').text(response.success).show();
                            $('#success-message').append('<p>Uploaded file: <a href="{{ asset('
                                storage ') }}/' + response.file + '" target="_blank">View File</a></p>');
                        }
                    },
                    error: function(response) {
                        if (response.status === 422) {
                            var errors = response.responseJSON.errors;
                            if (errors.file) {
                                $('#file-error').text(errors.file[0]);
                            }
                        } else {
                            $('#error-message').text('An error occurred while uploading the file.').show();
                        }
                    }
                });
            }
        });
    });
</script>
<script>
    // $(document).ready(function () {
    //     $('#upload-form').on('submit', function (e) {
    //         e.preventDefault();

    //         var formData = new FormData(this);
    //         $('#file-error').text('');
    //         $('#success-message').hide();
    //         $('#error-message').hide();

    //         $.ajax({
    //             url: "{{ route('admin.files.store') }}",
    //             type: "POST",
    //             data: formData,
    //             contentType: false,
    //             processData: false,
    //             xhr: function() {
    //                 var xhr = new window.XMLHttpRequest();
    //                 xhr.upload.addEventListener('progress', function(e) {
    //                     if (e.lengthComputable) {
    //                         var percentComplete = e.loaded / e.total * 100;
    //                         $('#progress-bar').css('width', percentComplete + '%');
    //                         $('#progress-bar').text(percentComplete.toFixed(0) + '%');
    //                     }
    //                 }, false);
    //                 return xhr;
    //             },
    //             headers: {
    //                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //             },
    //             success: function (response) {
    //                 if (response.success) {
    //                     $('#success-message').text(response.success).show();
    //                     response.files.forEach(file => {
    //                         $('#success-message').append('<p>Uploaded file: <a href="{{ asset('storage') }}/' + file + '" target="_blank">View File</a></p>');
    //                     });
    //                 }
    //             },
    //             error: function (response) {
    //                 if (response.status === 422) {
    //                     var errors = response.responseJSON.errors;
    //                     if (errors.files) {
    //                         $('#file-error').text(errors.files[0]);
    //                     }
    //                 } else {
    //                     $('#error-message').text('An error occurred while uploading the files.').show();
    //                 }
    //             }
    //         });
    //     });
    // });
</script>
