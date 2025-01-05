<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MaterialController extends Controller
{
    public function uploadMaterial(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx|max:10240', // Maks 10 MB
            'dosen_id' => 'required|exists:dosen,id',
            'tipe_kelas_id' => 'required|exists:tipe_kelas,id',
        ]);
    
        \Log::info($request);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'gila'
            ], 422);
        }
    
        try {
            $file = $request->file('file');
            $originalExtension = $file->getClientOriginalExtension();
    
            // Buat nama file unik
            $fileName = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9_]/', '_', $request->title) . '.pdf';
            $convertedFilePath = 'materials/' . $fileName;
    
            // Simpan file asli sementara
            $tempFilePath = $file->store('materials/temp');
    
            // Konversi file ke PDF jika bukan PDF
            if ($originalExtension !== 'pdf') {
                $converter = new PdfConverter();
                $converter->convert(storage_path('app/' . $tempFilePath), storage_path('app/' . $convertedFilePath));
                Storage::delete($tempFilePath);
            } else {
                Storage::move($tempFilePath, $convertedFilePath);
            }
    
            // Simpan ke database
            $material = Material::create([
                'title' => $request->title,
                'description' => $request->description,
                'file_path' => $convertedFilePath,
                'dosen_id' => $request->dosen_id,
                'tipe_kelas_id' => $request->tipe_kelas_id,
            ]);
    
            return response()->json([
                'success' => true,
                'message' => 'Material uploaded successfully',
                'data' => $material,
            ], 201);
        } catch (\Exception $e) {
            // Log error untuk debugging
            \Log::error('Upload Material Error: ' . $e->getMessage());
    
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload material',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function getMaterialsByKelas($tipeKelasId)
    {
        // Mengambil materials dengan nama dosen dan tipe kelas
        $materials = Material::with(['dosen', 'tipeKelas'])
            ->where('tipe_kelas_id', $tipeKelasId)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $materials->map(function ($material) {
                return [
                    'id' => $material->id,
                    'title' => $material->title,
                    'description' => $material->description,
                    'file_path' => $material->file_path,
                    'dosen_name' => $material->dosen->nama ?? 'N/A',
                    'tipe_kelas_name' => $material->tipeKelas->nama ?? 'N/A',
                ];
            }),
        ], 200);
    }
}
