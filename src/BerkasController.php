<?php

namespace Karogis\Berkas;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BerkasController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
        $this->middleware(config('berkas.middleware'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $files = BerkasModel::paginate(15);
        return view('berkas::index', compact('files'));
    }

    public function partialindex()
    {
        $files = BerkasModel::paginate(15);
        return view('berkas::_index', compact('files'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('berkas::create');
    }

    private function renameIfExist($path) {
        if (file_exists($path)) {
            $dir = pathinfo($path, PATHINFO_DIRNAME);
            $filename = pathinfo($path, PATHINFO_FILENAME);
            $extension = pathinfo($path, PATHINFO_EXTENSION);
            $i = 1;

            while (file_exists($path)) {
                $new_filename = $filename.'-'.$i.'.'.$extension;
                $path = $dir.'/'.$new_filename;
                $i++;
            }

            return $new_filename;
        } else {
            return pathinfo($path, PATHINFO_BASENAME);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, ['uploads' => 'required|array']);

        $rules = [];

        if ($request->uploads) {
            foreach ($request->files as $key => $file) {
                $rules['uploads.'.$key] = 'mimes:jpeg,png,bmp,doc,docx,xls,xlsx,csv,pdf,mp4,avi,wmv';
            }
        }

        $this->validate($request, $rules);

        DB::beginTransaction();
        try {
            foreach ($request->uploads as $i => $file) {
                $name = $file->getClientOriginalName();
                $type = $file->getClientMimeType();
                $extension = $file->getClientOriginalExtension();
                $size = $file->getClientSize();

                $year = date('Y');
                $month = date('m');
                $save_path = "storage/berkas/$year/$month";
                $storage_path = "public/berkas/$year/$month";
                if (!Storage::exists($storage_path))
                    Storage::makeDirectory($storage_path);

                $save_name = $this->renameIfExist($save_path.'/'.$name);

                $images = ['image/jpeg', 'image/png', 'image/bmp'];
                if (in_array($type, $images)) {
                    $img = Image::make($file);
                    if ($img->width() >= $img->height()) {
                        $img->resize(1024, null, function($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        });
                    } else {
                        $img->resize(null, 1024, function($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        });
                    }

                    $stream = $img->stream($extension, 90);
                    $store = Storage::put($storage_path.'/'.$save_name, $stream);
                    $size = Storage::size($storage_path.'/'.$save_name);
                } else {
                    $store = Storage::putFileAs($storage_path, $file, $save_name);
                }

                $path = $save_path.'/'.$save_name;

                $files[] = BerkasModel::create([
                    'filename' => $request->filenames[$i] ?: $save_name,
                    'description' => $request->descriptions[$i],
                    'mime' => $type,
                    'size' => $size,
                    'path' => $path,
                ]);
            }
        } catch (Exception $e) {
            DB::rollback();
            logger('[Karogis\Berkas\BerkasController@store]: '.$e->getMessage());
            return response('Terjadi kesalahan sistem.', 500);
        }

        DB::commit();

        return response([
          'status' => 'success',
          'data' => $files
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
