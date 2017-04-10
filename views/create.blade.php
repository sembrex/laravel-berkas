<style type="text/css">
    .upload-area {
        height: 104px;
        text-align: center;
        border: 2px dashed #aaa;
        position: relative;
        border-radius: 5px;
    }
    .upload-area i {
        line-height: 100px;
        font-size: 30px;
        color: #aaa;
    }
    .upload-area p {
        position: absolute;
        bottom: 10px;
        width: 100%;
        margin: 0;
        color: #aaa;
    }
    .upload-area input {
        width: 100%;
        height: 100px;
        opacity: 0;
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
    }
</style>

<div class="row">
    <div class="col-md-12">
        <form action="{{ route('berkas.store') }}" method="post" id="formUpload" enctype="multipart/form-data">
            {!! csrf_field() !!}

            <div class="form-group">
                <div class="upload-area">
                    <i class="fa fa-plus"></i>
                    <p>Klik atau Seret untuk menambahkan berkas. (jpg, png, bmp, gif, doc, docx, xls, xlsx, csv, pdf, mp4, avi, wmv)</p>
                    <input type="file" name="files[]" multiple>
                </div>
            </div>
        </form>
    </div>
</div>

<div id="preview" class="row hidden">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th width="100px">Preview</th>
                        <th>Nama</th>
                        <th>Deskripsi</th>
                        <th>Tipe</th>
                        <th>Ukuran</th>
                    </tr>
                </thead>
                <tbody id="fileList">
                </tbody>
            </table>
        </div>

        <hr>

        <button type="button" class="btn-clear btn btn-danger btn-sm"><i class="fa fa-times margin-r-5"></i> Bersihkan</button>
        <button type="submit" form="formUpload" class="btn-submit btn btn-success btn-sm pull-right" data-loading-text="<i class='fa fa-spin fa-fw fa-sun-o'></i> Mengunggah berkas..."><i class="fa fa-upload margin-r-5"></i> Unggah</button>
    </div>
</div>

<script type="text/javascript">
    Berkas.uploadedFiles = [];

    $('[name^=files]').change(function() {
        var files = this.files,
            preview = $('#preview'),
            parent = $('#fileList'),
            docs = /^application\/.*(doc|docx|xls|xlsx|csv|pdf|msword|wordprocessingml.document|ms-excel|spreadsheetml.sheet)$/,
            images = /^image\/(jpeg|png|bmp|gif)$/,
            videos = /^video\/(mp4|avi|x-ms-wmv)$/,
            allowed = [];

        if (files.length > 0) {
            $.each(files, function(i, file) {
                if (docs.test(file.type) || images.test(file.type) || videos.test(file.type)) {
                    Berkas.uploadedFiles.push(file);
                    allowed.push(file);
                }
            });
        }

        if (allowed.length > 0) {
            preview.removeClass('hidden');
            $.each(allowed, function(i, file) {
                var reader = new FileReader(),
                    tr = $('<tr>'),
                    img = $('<img>').addClass('img-responsive thumbnail'),
                    size = function() {
                        var kb = file.size/1000,
                            mb = kb/1000;

                        if (mb > 1)
                            return Math.round(mb).formatNumber(0) + ' MB';
                        else
                            return Math.round(kb).formatNumber(0) + ' KB';
                    };

                tr.append($('<td>').append(img));
                tr.append($('<td>').append($('<input>').addClass('form-control input-sm').attr({type: 'text', name: 'filenames[]'}).val(file.name)));
                tr.append($('<td>').append($('<textarea>').addClass('form-control input-sm').attr({name: 'descriptions[]', rows: 3})));
                tr.append('<td>'+ file.type +'</td>');
                tr.append('<td>'+ size() +'</td>');

                reader.onload = function() {
                    if (images.test(file.type))
                        img.attr('src', reader.result);
                    else if(videos.test(file.type))
                        img.attr('src', '/assets/images/video.svg');
                    else if(/^application\/.*(doc|docx|msword|wordprocessingml.document)$/.test(file.type))
                        img.attr('src', '/assets/images/word.svg');
                    else if(/^application\/.*(csv|xls|xlsx|ms-excel|spreadsheetml.sheet)$/.test(file.type))
                        img.attr('src', '/assets/images/excel.svg');
                    else if(/^application\/.*pdf$/.test(file.type))
                        img.attr('src', '/assets/images/pdf.svg');
                    else
                        img.attr('src', '/assets/images/blank.png');
                };

                reader.readAsDataURL(file);

                tr.appendTo(parent);
            });
        }
    });

    function resetUpload() {
        Berkas.uploadedFiles = [];
        $('[name^=files]').val('');
        $('#preview').addClass('hidden');
        $('#fileList').empty();
    }

    $('.btn-clear').click(function() {
        resetUpload();
    });

    $('#formUpload').submit(function(e) {
        e.preventDefault();

        if (Berkas.uploadedFiles.length < 1) {
            swal('Kesalahan!', 'Tidak ada berkas dipilih', 'error').catch(swal.noop);
            return false;
        }

        var form = $(this),
            url = form.prop('action'),
            token = form.find('[name=_token]').val(),
            data = new FormData(),
            btn = $('.btn-submit');

        data.append('_token', token);
        $.each(Berkas.uploadedFiles, function(i, file) {
            data.append('uploads[]', file, file.name);
        });
        $('[name^=filenames]').each(function() {
            data.append('filenames[]', $(this).val());
        });
        $('[name^=descriptions]').each(function() {
            data.append('descriptions[]', $(this).val());
        });

        btn.button('loading');

        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            cache: false,
            contentType: false,
            processData: false,
            success: function(res) {
                console.log(res);
                btn.button('reset');
                swal({
                    title: 'Sukses!',
                    text: 'Berkas berhasil diunggah',
                    type: 'success',
                    showConfirmButton: false,
                    showCloseButton: true,
                    timer: 3000
                }).catch(swal.noop);

                resetUpload();

                $('#partialindex').fadeOut().load('/berkas/partialindex', function() {
                    $(this).fadeIn();
                    Berkas.initSelection();
                });
            },
            error: function(res) {
                btn.button('reset');
                swal({
                    title: 'Kesalahan!',
                    text: 'Berkas gagal diunggah. Silahkan coba lagi atau hubungi admin',
                    type: 'error',
                    showConfirmButton: false,
                    showCloseButton: true,
                    timer: 3000
                }).catch(swal.noop);
            }
        });
    });
</script>
