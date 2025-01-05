<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\TIpeJawaban;
use App\Models\LongAnswer;
use App\Models\MultiAnswer;
use App\Models\AnswerRecord;
use App\Models\QuizAttempt;

class QuizController extends Controller
{
    /**
     * Get quizzes with related data.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getQuizzes(Request $request)
    {
        try {
            // Optional: Filter quizzes based on request (e.g., tipe_kelas_id or dosen_id)
            $query = Quiz::with(['dosen', 'tipeKelas', 'tipeQuiz']);

            if ($request->has('tipe_kelas_id')) {
                $query->where('tipe_kelas_id', $request->input('tipe_kelas_id'));
            }

            if ($request->has('dosen_id')) {
                $query->where('dosen_id', $request->input('dosen_id'));
            }

            // Fetch quizzes with pagination (default: 10 per page)
            $quizzes = $query->paginate($request->input('per_page', 10));

            return response()->json([
                'message' => 'Quizzes retrieved successfully',
                'data' => $quizzes,
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Error fetching quizzes', [
                'exception' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'An error occurred while fetching quizzes',
            ], 500);
        }
    }

    public function storeQuiz(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'dosen_id' => 'required|exists:dosen,id',
            'tipe_kelas_id' => 'required|exists:tipe_kelas,id',
            'tipe_quiz_id' => 'required|exists:tipe_quiz,id',
            'time_end' => 'required|date',
            'questions' => 'required|array',
            'questions.*.question_text' => 'required|string',
            'questions.*.tipe_jawaban_id' => 'required|exists:tipe_jawaban,id',
            'questions.*.long_answer.true_answer' => 'nullable|string',
            'questions.*.multi_answer' => 'nullable|array',
            'questions.*.multi_answer.*.text' => 'required|string',
            'questions.*.multi_answer.*.is_true_answer' => 'required|boolean',
        ]);

        // Simpan quiz
        $quiz = Quiz::create([
            'judul' => $validated['judul'],
            'deskripsi' => $validated['deskripsi'],
            'dosen_id' => $validated['dosen_id'],
            'tipe_kelas_id' => $validated['tipe_kelas_id'],
            'tipe_quiz_id' => $validated['tipe_quiz_id'],
            'time_end' => $validated['time_end'],
        ]);
        
        // Simpan pertanyaan dan jawaban terkait
        foreach ($validated['questions'] as $q) {
            $question = Question::create([
                'quiz_id' => $quiz->id,
                'question_text' => $q['question_text'],
                'tipe_jawaban_id' => $q['tipe_jawaban_id'],
            ]);
            
            // Long Answer
            if ($q['tipe_jawaban_id'] == 1 && isset($q['long_answer']['true_answer'])) {
                LongAnswer::create([
                    'question_id' => $question->id,
                    'true_answer' => $q['long_answer']['true_answer'],
                ]);
            }
            
            // Multi Answer
            if ($q['tipe_jawaban_id'] == 2 && isset($q['multi_answer'])) {
                foreach ($q['multi_answer'] as $answer) {
                    MultiAnswer::create([
                        'question_id' => $question->id,
                        'text' => $answer['text'],
                        'is_true_answer' => $answer['is_true_answer'],
                    ]);
                }
            }
        }

        return response()->json([
            'message' => 'Quiz created successfully',
            'quiz' => $quiz,
        ], 201);
    }

    public function getQuizDetail($quiz_id)
    {
        try {
            // Fetch the quiz data with related questions and creator
            $quiz = Quiz::with(['questions', 'dosen', 'questions.tipeJawaban', 'questions.long_answer', 'questions.multi_answer'])
                        ->findOrFail($quiz_id);

            // Calculate the total number of questions
            $totalQuestions = $quiz->questions->count();

            // Prepare the response
            $response = [
                'success' => true,
                'data' => [
                    'quiz_id' => $quiz->id,
                    'quiz_name' => $quiz->judul,
                    'deskripsi' => $quiz->deskripsi,
                    'total_questions' => $totalQuestions,
                    'waktu_pengerjaan' => $quiz->waktu_pengerjaan,
                    'creator_name' => $quiz->dosen->nama ?? 'Unknown',
                    'questions' => $quiz->questions->map(function ($question) {
                        return [
                            'id' => $question->id,
                            'text' => $question->question_text,
                            'type' => $question->tipeJawaban->nama,
                        ];
                    }),
                ],
            ];

            return response()->json($response, 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Handle case where the quiz ID does not exist
            return response()->json([
                'success' => false,
                'message' => 'Quiz not found.'
            ], 404);

        } catch (\Exception $e) {
            // Handle general errors
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getAnswerDetail($question_id)
    {
        try {
            // Fetch the question with related long_answer and multi_answer
            $question = Question::with(['long_answer', 'multi_answer'])->findOrFail($question_id);
    
            // Prepare the response
            $response = [
                'success' => true,
                'data' => [
                    'question_id' => $question->id,
                    'question_text' => $question->question_text,
                    'long_answer' => $question->long_answer ? [
                        'id' => $question->long_answer->id,
                        'true_answer' => $question->long_answer->true_answer,
                    ] : null,
                    'multi_answers' => $question->multi_answer->map(function ($multiAnswer)  {
                        return [
                            'id' => $multiAnswer->id,
                            'text' => $multiAnswer->text,
                            'is_true_answer' => $multiAnswer->is_true_answer,
                        ];
                    }),
                ],
            ];
    
            return response()->json($response, 200);
    
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Handle case where question_id does not exist
            return response()->json([
                'success' => false,
                'message' => 'Question not found.'
            ], 404);
    
        } catch (\Exception $e) {
            // Handle general errors
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }    

    public function validateAnswer(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'quiz_id' => 'required|exists:quizzes,id',
            'question_id' => 'required|exists:questions,id',
            'answer' => 'required|string',
        ]);

        try {
            // Fetch the question details
            $question = Question::with(['tipeJawaban', 'multi_answer', 'long_answer'])
                ->findOrFail($validatedData['question_id']);

            $isCorrect = '';

            if ($question->tipe_jawaban_id === 2) {
                // Validate using ID
                $isCorrect = $question->multi_answer->where('id', $validatedData['answer'])->where('is_true_answer', true)->isNotEmpty();
            } elseif ($question->tipe_jawaban_id === 1) {
                // Validate for long-answer questions
                // Validate for long-answer questions
                $isCorrect = strtolower(($question->long_answer->true_answer)) === strtolower(($validatedData['answer']));
                \Log::info($question->long_answer->true_answer);
                \Log::info($validatedData['answer']);
            }            

            // Record the result in the `answer_record` table
            AnswerRecord::updateOrCreate(
                [
                    'user_id' => $validatedData['user_id'],
                    'quiz_id' => $validatedData['quiz_id'],
                    'question_id' => $validatedData['question_id'],
                ],
                [
                    'is_correct' => $isCorrect,
                ]
            );

            // Response
            return response()->json([
                'success' => true,
                'data' => [
                    'question_id' => $question->id,
                    'answer' => $validatedData['answer'],
                    'is_correct' => $isCorrect,
                ],
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Question not found.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while validating the answer.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function saveAnswer(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'quiz_id' => 'required|exists:quizzes,id',
            'question_id' => 'required|exists:questions,id',
            'is_correct' => 'required|boolean',
        ]);

        try {
            // Check if an answer already exists for the user, quiz, and question
            $answer = AnswerRecord::where('user_id', $validatedData['user_id'])
                ->where('quiz_id', $validatedData['quiz_id'])
                ->where('question_id', $validatedData['question_id'])
                ->first();

            if ($answer) {
                // Update existing record
                $answer->update([
                    'is_correct' => $validatedData['is_correct'],
                ]);
            } else {
                // Create a new record
                AnswerRecord::create($validatedData);
            }

            return response()->json([
                'success' => true,
                'message' => 'Answer saved successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while saving the answer.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getResumeAnswers($quiz_id, $user_id)
    {
        try {
            // Validate if the quiz and user exist
            $quiz = Quiz::findOrFail($quiz_id);
            $user = User::findOrFail($user_id);

            // Get all answered questions for the quiz by the user
            $answers = AnswerRecord::where('user_id', $user_id)
                ->where('quiz_id', $quiz_id)
                ->with('question')
                ->get();

            // Format the response
            $response = [
                'quiz_id' => $quiz->id,
                'user_id' => $user->id,
                'answered_questions' => $answers->map(function ($answer) {
                    return [
                        'question_id' => $answer->question_id,
                        'question_text' => $answer->question->question_text ?? null,
                        'is_correct' => $answer->is_correct,
                    ];
                }),
            ];

            return response()->json([
                'success' => true,
                'data' => $response,
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Quiz or User not found.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving resume answers.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function storeQuizResult(Request $request)
    {
        $validatedData = $request->validate([
            'quiz_id' => 'required|exists:quizzes,id',
            'user_id' => 'required|exists:users,id',
        ]);

        // Ambil data user dan quiz
        $quizId = $validatedData['quiz_id'];
        $userId = $validatedData['user_id'];

        // Ambil semua jawaban user di kuis ini
        $answers = AnswerRecord::where('quiz_id', $quizId)
            ->where('user_id', $userId)
            ->get();

        // Hitung jumlah soal dan jawaban yang benar
        $totalQuestions = $answers->count();
        $correctAnswers = $answers->where('is_correct', true)->count();

        // Kalkulasi nilai
        $score = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100, 2) : 0;

        // Catat waktu selesai
        $timeFinish = now();

        // Catat data penggunaan power-up dan win streak
        $powerUpsUsed = $answers->sum('power_up_usage');
        $winStreak = $answers->where('is_correct', true)->countBy('streak')->max() ?? 0;

        // Simpan data ke QuizAttempt
        $quizAttempt = QuizAttempt::create([
            'quiz_id' => $quizId,
            'user_id' => $userId,
            'time_finish_record' => $timeFinish,
            'nilai' => $score,
            'how_much_use_power_up' => $powerUpsUsed,
            'how_much_win_streak' => $winStreak,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Quiz result recorded successfully',
            'data' => $quizAttempt,
        ]);
    }

    public function getLeaderboard(Request $request)
    {
        $validated = $request->validate([
            'quiz_id' => 'required|exists:quizzes,id',
        ]);
    
        $leaderboard = QuizAttempt::where('quiz_id', $validated['quiz_id'])
            ->with(['user.dosen', 'user.mahasiswa']) // Tambahkan relasi dosen & mahasiswa
            ->orderByDesc('nilai')
            ->take(10)
            ->get()
            ->map(function ($attempt) {
                // Ambil nama dari Dosen atau Mahasiswa
                $user = $attempt->user;
                $name = $user->dosen ? $user->dosen->nama : ($user->mahasiswa ? $user->mahasiswa->nama : 'Unknown');
    
                return [
                    'user_id' => $user->id,
                    'user_name' => $name,
                    'score' => $attempt->nilai,
                    'power_up_used' => $attempt->how_much_use_power_up,
                    'win_streak' => $attempt->how_much_win_streak,
                ];
            });
    
        return response()->json([
            'success' => true,
            'data' => $leaderboard,
        ]);
    }
    public function getHistory($user_id)
    {
        try {
            $history = QuizAttempt::with('quiz:id,judul') // Ambil hanya kolom yang diperlukan
            ->where('user_id', $user_id)
            ->orderBy('time_finish_record', 'desc')
            ->get()
            ->map(function ($attempt) {
                return [
                    'nilai' => $attempt->nilai,
                    'quiz_title' => $attempt->quiz->judul ?? 'Unknown Quiz',
                ];
            });

            // Return response dalam format JSON
            return response()->json([
                'success' => true,
                'message' => 'History fetched successfully.',
                'data' => $history,
            ]);
        } catch (\Exception $e) {
            // Handle error
            return response()->json([
                'success' => false,
                'message' => 'Error fetching history.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
