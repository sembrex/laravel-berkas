@extends('berkas::layout')

@section('title', 'Index')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Berkas
                    </div>

                    <div class="panel-body">
                        <button type="button" class="btn btn-primary btn-sm margin-b-10" onclick="Berkas.addFile()"><i class="fa fa-plus margin-r-5"></i> Add File</button>

                        <button type="button" id="btnDeleteMulti" class="btn btn-danger btn-sm hidden margin-b-10"><i class="fa fa-trash margin-r-5"></i> Delete Selected</button>

                        <div id="partialindex">
                            @include('berkas::_index')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
        Berkas.initSelection();
    </script>
@endpush
