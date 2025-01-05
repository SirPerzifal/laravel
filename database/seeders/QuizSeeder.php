<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuizSeeder extends Seeder
{
    public function run()
    {
        // Seeder for Quiz
        DB::table('quizzes')->insert([
            [
                'judul' => 'Basic Programming',
                'deskripsi' => 'Quiz about programming basics',
                'dosen_id' => 1,
                'tipe_kelas_id' => 1,
                'tipe_quiz_id' => 1,
                'time_end' => now()->addDays(7),
            ],
        ]);

        // Seeder for Question
        DB::table('questions')->insert([
            [
                'quiz_id' => 1,
                'question_text' => 'What is the output of 1 + 1?',
                'tipe_jawaban_id' => 2,
            ],
            [
                'quiz_id' => 1,
                'question_text' => 'Explain polymorphism in object-oriented programming.',
                'tipe_jawaban_id' => 1,
            ],
        ]);

        // Seeder for LongAnswer
        DB::table('long_answers')->insert([
            [
                'question_id' => 2,
                'true_answer' => 'Polymorphism allows objects to be treated as instances of their parent class.',
            ],
        ]);

        // Seeder for MultiAnswer
        DB::table('multi_answers')->insert([
            [
                'question_id' => 1,
                'text' => '1',
                'is_true_answer' => false,
            ],
            [
                'question_id' => 1,
                'text' => '2',
                'is_true_answer' => true,
            ],
            [
                'question_id' => 1,
                'text' => '3',
                'is_true_answer' => false,
            ],
            [
                'question_id' => 1,
                'text' => '4',
                'is_true_answer' => false,
            ],
        ]);

        // Seeder for QuizAttempt
        DB::table('quiz_attempts')->insert([
            [
                'quiz_id' => 1,
                'user_id' => 1,
                'time_finish_record' => now(),
                'nilai' => 85,
                'how_much_use_power_up' => 2,
                'how_much_win_streak' => 5,
            ],
        ]);

        // Seeder for PowerUpUsage
        DB::table('power_up_usages')->insert([
            [
                'user_id' => 1,
                'power_up_id' => 1,
                'quiz_id' => 1,
                'used_at' => now()->subMinutes(10),
            ],
            [
                'user_id' => 1,
                'power_up_id' => 3,
                'quiz_id' => 1,
                'used_at' => now()->subMinutes(5),
            ],
        ]);

        // Seeder for HumanRobotPercentage
        DB::table('human_robot_percentages')->insert([
            [
                'user_id' => 1,
                'quiz_id' => 1,
                'human_percentage' => 70,
                'robot_percentage' => 30,
            ],
        ]);
    }
}