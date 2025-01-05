<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\TipeJawaban;
use App\Models\TipeKelas;
use App\Models\TipeQuiz;

class TipeController extends Controller
{
    public function tipeKelas(): JsonResponse
    {
        $classTypes = TipeKelas::all(); // Mengambil semua data tipe kelas
        return response()->json($classTypes);
    }
    public function tipeJawaban(): JsonResponse
    {
        $quizTypes = TipeJawaban::all(); // Mengambil semua data tipe kelas
        return response()->json($quizTypes);
    }
}
