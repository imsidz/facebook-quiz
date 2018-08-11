<?php

return array(
    'events' => array(
        [
            'id'            =>  'userSignUp',
            'name'          =>  'On signup',
            'setHandler'    =>  function($callback) {
                User::created(function($user) use($callback) {
                    call_user_func($callback, $user);
                });
            }
        ],
        [
            'id'            =>  'userTookQuiz',
            'name'          =>  'On taking a quiz',
            'setHandler'    =>  function($callback) {
                QuizUserActivity::created(function($activity) use($callback) {
                    if($activity->type == 'attempt') {
                        call_user_func($callback);
                    }
                });
            }
        ],
        [
            'id'            =>  'userSharedQuiz',
            'name'          =>  'On sharing a quiz on Facebook, Twitter, etc',
            'setHandler'    =>  function($callback) {
                QuizUserActivity::created(function($activity) use($callback) {
                    if($activity->type == 'share') {
                        call_user_func($callback);
                    }
                });
            }
        ]
    )
);