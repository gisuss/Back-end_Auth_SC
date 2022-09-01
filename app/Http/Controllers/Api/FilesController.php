<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UserExport;
use App\Imports\UserImport;
use Illuminate\Support\Facades\App;

class FilesController extends Controller
{
    public function excel_UsersImports(Request $request) {
        Excel::import(new UserImport, $request->file('file')->store('files'));
        return redirect()->back();
    }

    public function excel_UsersExports(Request $request) {
        return Excel::download(new UserExport, 'users.xlsx');
    }

    public function pdf_UsersExports () {
        $dompdf = App::make("dompdf.wrapper");
        return $dompdf->download("Users.pdf");
    }
}
