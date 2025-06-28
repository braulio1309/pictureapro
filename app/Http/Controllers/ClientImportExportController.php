<?php

namespace App\Http\Controllers;

use App\Exports\ClientsExport;
use App\Imports\ClientsImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class ClientImportExportController extends Controller
{
    public function export() 
    {
        return Excel::download(new ClientsExport, 'clientes.xlsx');
    }

    public function import(Request $request) 
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls'
        ]);

        Excel::import(new ClientsImport, $request->file('file'));

        return back()->with('success', 'Clientes importados exitosamente!');
    }
}
