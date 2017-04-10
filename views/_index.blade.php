<div class="table-responsive">
    @if($files->count())
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th width="40px">
                        <div class="icheck">
                            <input type="checkbox" class="select-all">
                        </div>
                    </th>
                    <th width="150px"></th>
                    <th>Filename</th>
                    <th>Mime</th>
                    <th>Size</th>
                </tr>
            </thead>
            <tbody>
                @foreach($files as $file)
                    <tr>
                        <td>
                            <div class="icheck">
                                <input type="checkbox" class="select-file" value="{{ $file->id }}">
                            </div>
                        </td>
                        <td>
                            <img src="{{ asset($file->path) }}" class="thumbnail img-responsive">
                        </td>
                        <td>{{ $file->filename }}</td>
                        <td></td>
                        <td></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            No files found. <a role="button" onclick="Berkas.addFile()">Klik here</a> to start uploading files.
        </div>
    @endif
</div>
