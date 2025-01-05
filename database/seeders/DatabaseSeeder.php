<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Seeder for Role
        DB::table('roles')->insert([
            ['nama' => 'mahasiswa'],
            ['nama' => 'dosen'],
        ]);

        DB::table('users')->insert([
            [
                'email' => 'mahasiswa1@example.com',
                'password' => Hash::make('password'),
                'role_id' => 1, // Role mahasiswa
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'email' => 'dosen2@example.com',
                'password' => Hash::make('password'),
                'role_id' => 2, // Role dosen
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Seeder for TipeKelas
        DB::table('tipe_kelas')->insert([
            ['nama' => 'RPA'],
            ['nama' => 'RPB'],
            ['nama' => 'RMA'],
            ['nama' => 'RMB'],
        ]);

        // Seeder for TipeQuiz
        DB::table('tipe_quiz')->insert([
            ['nama' => 'manual_make'],
            ['nama' => 'AI_make'],
        ]);

        // Seeder for TipeJawaban
        DB::table('tipe_jawaban')->insert([
            ['nama' => 'long_answer_text'],
            ['nama' => 'multiple_choice'],
        ]);

        // Seeder for PowerUp
        DB::table('power_ups')->insert([
            [
                'name' => 'Supersonic',
                'deskripsi' => 'Players can get 1.5x the score for 20 seconds when they play at a faster speed.',
                'effect' => '1.5x score for 20 seconds',
                'duration' => 20,
            ],
            [
                'name' => 'Streak Booster',
                'deskripsi' => 'Boosts the number in the player\'s streak counter.',
                'effect' => 'Boost streak counter',
                'duration' => 0,
            ],
            [
                'name' => 'Gift',
                'deskripsi' => 'Players can send another player an extra score of 800.',
                'effect' => 'Send 800 extra score',
                'duration' => 0,
            ],
            [
                'name' => 'Double Jeopardy',
                'deskripsi' => 'Players get double the score if they choose the correct answer but lose it all if they choose the wrong answer.',
                'effect' => 'Double score or lose all',
                'duration' => 0,
            ],
            [
                'name' => '2X',
                'deskripsi' => 'Players get twice the score for answering a question correctly.',
                'effect' => '2x score for correct answer',
                'duration' => 0,
            ],
            [
                'name' => '50-50',
                'deskripsi' => 'Eliminates half of the incorrect answer options.',
                'effect' => 'Eliminate half of incorrect options',
                'duration' => 0,
            ],
            [
                'name' => 'Eraser',
                'deskripsi' => 'Eliminates one wrong option.',
                'effect' => 'Eliminate one wrong option',
                'duration' => 0,
            ],
            [
                'name' => 'Immunity',
                'deskripsi' => 'A player can attempt the same question twice in case they answered it incorrectly the first time.',
                'effect' => 'Attempt question twice',
                'duration' => 0,
            ],
            [
                'name' => 'Time Freeze',
                'deskripsi' => 'The timer is frozen to allow players to answer 1 question.',
                'effect' => 'Freeze timer for 1 question',
                'duration' => 0,
            ],
            [
                'name' => 'Power Play',
                'deskripsi' => 'All players in the session get 50% more score in 20 seconds.',
                'effect' => '50% more score for all players',
                'duration' => 20,
            ],
            [
                'name' => 'Streak Saver',
                'deskripsi' => 'Protects a player’s streak against an incorrect answer.',
                'effect' => 'Protect streak',
                'duration' => 0,
            ],
            [
                'name' => 'Glitch',
                'deskripsi' => 'All players\' screens glitch for 10 seconds (does not affect scores).',
                'effect' => 'Glitch screens for 10 seconds',
                'duration' => 10,
            ],
        ]);

        // Seeder for other tables (you can customize the data as needed)
        DB::table('mahasiswa')->insert([
            [
                'user_id' => 1,
                'nim' => '123456789',
                'nama' => 'John Doe',
                'tipe_kelas_id' => 1,
                'prodi' => 'Teknik Informatika',
                'jurusan' => 'Sistem Informasi',
            ],
        ]);

        DB::table('dosen')->insert([
            [
                'user_id' => 2,
                'nidn' => '1122334455',
                'nama' => 'Dr. Jane Smith',
                'prodi' => 'Teknik Informatika',
                'jurusan' => 'Ilmu Komputer',
                'mata_kuliah' => 'Pemrograman Lanjut',
            ],
        ]);
    }
}