<?php

//Regsiter shutdown hook

register_shutdown_function(function() {
    do_action('shutdown');
});

\Event::listen('config:loaded', function() {
    do_action('config_loaded');
    \Event::listen('plugins:loaded', function() {
        dd(MyConfig::all());
        do_action('plugins_loaded', [\Plugins::enabled()]);
    });
});

\User::created(function($user) {
    do_action_ref_array('user_register', [&$user]);
});

//User confirmed hook
User::updated(function($user)
{
    $original = $user->getOriginal();
    if (!$original['confirmed'] && $user->confirmed) {
        do_action_ref_array('user_confirmed', [&$user]);
    }
});
\Event::listen('auth.login', function($user) {
    do_action_ref_array('user_login', [&$user]);
});

//Categories
\Category::created(function($category) {
    do_action_ref_array('create_category', [&$category]);
});
\Category::deleting(function($category) {
    do_action_ref_array('delete_category', [&$category]);
});
\Category::deleted(function($category) {
    do_action_ref_array('deleted_category', [&$category]);
});


//Quizzes
\Quiz::created(function($post) {
    do_action_ref_array('create_quiz', [&$post]);
});
\Quiz::deleting(function($post) {
    do_action_ref_array('delete_quiz', [&$post]);
});
\Quiz::deleted(function($post) {
    do_action_ref_array('deleted_quiz', [&$post]);
});

//Pages
\Page::created(function($page) {
    do_action_ref_array('create_page', [&$page]);
});
\Page::deleting(function($page) {
    do_action_ref_array('delete_page', [&$page]);
});
\Page::deleted(function($page) {
    do_action_ref_array('deleted_page', [&$page]);
});

add_action('create_post_below_editor', function($val) {
    echo ' wwwww test';
});
