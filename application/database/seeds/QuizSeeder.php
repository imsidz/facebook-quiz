<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class QuizSeeder extends Seeder
{
    public function run()
    {
        Eloquent::unguard();
        $quizFilesPattern = __DIR__ . "/quizzes/*";
        $quizSeeds = \File::glob($quizFilesPattern);
        foreach ($quizSeeds as $quizSeedFile) {
            $this->seedQuizFromFile($quizSeedFile);
        }

    }

    public function seedQuizFromFile($file)
    {
        $quiz = new Quiz();
        $quizData = json_decode($this->getQuizSeedContent($file), true);
        //$excludeKeys = ['created_at', 'updated_at', 'id', 'views', 'status', 'created_by', 'active', 'attempts', 'completions'];
        $excludeKeys = ['attempts', 'completions', 'viewQuizUrl'];
        foreach ($quizData as $key => $val) {
            if (in_array($key, $excludeKeys))
                continue;
            $quiz->$key = is_array($quizData[$key]) ? json_encode($quizData[$key]) : $quizData[$key];
        }
        $quiz->save();
    }

    public function getQuizSeedContent($file)
    {
        return \File::get($file);
    }
}
