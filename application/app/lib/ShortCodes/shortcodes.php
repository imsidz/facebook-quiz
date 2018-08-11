<?php

if(!function_exists('do_shortcode')) {
    function do_shortcode($content) {
        $shortCodeEngine = App::make('shortCodeEngine');
        return $shortCodeEngine->do_shortcode($content);
    }
}

if(!function_exists('renderQuizzesList')) {
    function renderQuizzesList($stream, $limit = null) {
        $loadQuizOptions = QuizController::getQuizQueryStreamOptions([
            'stream'    =>  $stream
        ]);
        if(!empty($limit)) {
            $loadQuizOptions['limit'] = $limit;
        }
        $quizes = QuizController::_getQuizes($loadQuizOptions);
        return View::make('quizes.quizesList', [
            'quizes' =>  $quizes
        ])->render();
    }
}

return array(
    'latest_quizzes' => [
        'name'          =>  "Latest quizzes",
        'description'   =>  "The list of latest quizzes",
        'attributes'    =>  [
            [
                'attribute'     =>  'limit',
                'description'   =>  'Limit - The number of quizzes to display'
            ]
        ],
        'handler'       =>  function($attrs = []) {
            $limit = !empty($attrs['limit']) ? $attrs['limit'] : null;
            return renderQuizzesList('latest', $limit);
        }
    ],
    'popular_quizzes' => [
        'name'          =>  "Popular quizzes",
        'description'   =>  "The list of popular quizzes",
        'attributes'    =>  [
            [
                'attribute'     =>  'limit',
                'description'   =>  'Limit - The number of quizzes to display'
            ]
        ],
        'handler'       =>  function($attrs = []) {
            $limit = !empty($attrs['limit']) ? $attrs['limit'] : null;
            return renderQuizzesList('popular', $limit);
        }
    ],
    'random_quizzes' => [
        'name'          =>  "Random quizzes",
        'description'   =>  "The list of random quizzes",
        'attributes'    =>  [
            [
                'attribute'     =>  'limit',
                'description'   =>  'Limit - The number of quizzes to display'
            ]
        ],
        'handler'       =>  function($attrs = []) {
            $limit = !empty($attrs['limit']) ? $attrs['limit'] : null;
            return renderQuizzesList('random', $limit);
        }
    ],
    'leaderboard'   => [
        'name'          =>  "Leaderboard",
        'description'   =>  "Displays the leaderboard",
        'attributes'    =>  [

        ],
        'handler'       =>  function($attrs = []) {
            return View::make('leaderboard.code');
        }
    ]
);