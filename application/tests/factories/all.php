<?php
use League\FactoryMuffin\Faker\Facade as Faker;

$fm->define('User')->setDefinitions([
    'name'   => Faker::firstName(),
    'email'  => Faker::unique()->email(),
    'photo'  => Faker::optional()->imageUrl(400, 400),
    'password'  =>  'password'
]);

$fm->define('Category')->setDefinitions([
    'name'   => Faker::unique()->word(),
    'slug'  => function ($object, $saved) {
        $slug = \Helpers::slug($object->name);
        return $slug;
    },
    'meta_title'    =>  Faker::optional()->sentence(1),
    'meta_description'    =>  Faker::optional()->paragraph(5),
]);


$fm->define('Post')->setDefinitions([
    'title'   => Faker::sentence(5),
    'description'  => Faker::paragraph(3),
    'content'  => '{}',
    'image'     =>  Faker::imageUrl(400, 400),
    'creator_user_id'   =>  'factory|User',
    'category_id'   =>  'factory|Category',
    'created_from_list_id'  =>  null,
    'original_creator_notified' =>  false,
    'views' =>  Faker::randomNumber(),
    'status'    =>  Faker::optional()->randomElement(array('approved', 'awaiting_approval', 'disapproved', 'not_submitted')),
    'type'  =>  null,
    'settings'  =>  null
]);


$fm->define('PollChoiceVotes')->setDefinitions([
    'post_id'   => Faker::randomNumber(),
    'poll_id'   => Faker::uuid(),
    'choice_id'   => Faker::uuid(),
    'votes'   => Faker::randomNumber()
]);

$fm->define('PollUserAnswers')->setDefinitions([
    'user_id'   => Faker::randomNumber(),
    'post_id'   => Faker::randomNumber(),
    'poll_id'   => Faker::uuid(),
    'choice_id'   => Faker::uuid()
]);

$fm->define('SiteConfig')->setDefinitions([
    'name'   => Faker::randomSlug(),
    'value'   => '{}'
]);


