<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Services\UploadsManager;// add by jcp
use App\Http\Requests\UploadFileRequest;
use App\Http\Requests\UploadNewFolderRequest;
use Illuminate\Support\Facades\File;
use Dflydev\ApacheMimeTypes\PhpRepository;

class UploadController extends Controller
{
    //
    protected $manager;

    public function __construct(UploadsManager $manager)
    {
      $this->manager = $manager;
    }

    /**
     * Show page of files / subfolders
     */
    public function index(Request $request)
    {
      $folder = $request->get('folder');
      $data = $this->manager->folderInfo($folder);

      return view('admin.upload.index', $data);
    }

    /**
     * Create a new folder
     */
    public function createFolder(UploadNewFolderRequest $request)
    {
      $new_folder = $request->get('new_folder');
      $folder = $request->get('folder').'/'.$new_folder;

      $result = $this->manager->createDirectory($folder);

      if ($result === true) {
        return redirect()
          ->back()
          ->withSuccess("Folder '$new_folder' created.");
        }

      $error = $result ? : "An error occurred creating directory.";
      return redirect()
          ->back()
          ->withErrors([$error]);
    }

    /**
     * Delete a file
     */
    public function deleteFile(Request $request)
    {
      $del_file = $request->get('del_file');
      $path = $request->get('folder').'/'.$del_file;

      $result = $this->manager->deleteFile($path);

      if ($result === true) {
        return redirect()
          ->back()
          ->withSuccess("File '$del_file' deleted.");
      }

      $error = $result ? : "An error occurred deleting file.";
      return redirect()
          ->back()
          ->withErrors([$error]);
      }

    /**
     * Delete a folder
     */
    public function deleteFolder(Request $request)
    {
      $del_folder = $request->get('del_folder');
      $folder = $request->get('folder').'/'.$del_folder;

      $result = $this->manager->deleteDirectory($folder);

      if ($result === true) {
        return redirect()
          ->back()
          ->withSuccess("Folder '$del_folder' deleted.");
      }

      $error = $result ? : "An error occurred deleting directory.";
      return redirect()
        ->back()
        ->withErrors([$error]);
      }

    /**
     * Upload new file
     */
    public function uploadFile(UploadFileRequest $request)
    {
      $extension = '';
      $file = $_FILES['file'];
      $fileName =  $request->get('file_name');
      if (! empty($fileName)) {
        $extension = '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
      }
      $fileName = $fileName ?: $file['name'];
      $path = str_finish($request->get('folder'), '/') . $fileName . $extension;
      $content = File::get($file['tmp_name']);

      $result = $this->manager->saveFile($path, $content);

      if ($result === true) {
        return redirect()
          ->back()
          ->withSuccess("File '$fileName' uploaded.");
      }

      $error = $result ? : "An error occurred uploading file.";
      return redirect()
        ->back()
        ->withErrors([$error]);
    }
}
