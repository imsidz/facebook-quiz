<?php
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Image;

/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 17/03/17
 * Time: 6:56 PM
 */
class ResultImageGeneratorTest extends TestCase
{
    public static $testResultId = 'dad64174-a4f9-496d-bfdc-3d97bc591ea0'; //Result 'Sexiest-  Wow! Really.. You are one among the top 10 sexiest people in the world'
    public static function makeResultImageGenerator()
    {
        $user = static::$fm->create('User');
        $quiz = Quiz::where('topic', 'How dirty is your mind?')->first();
        $result = new QuizUserResults();
        $result->user = $user;
        $result->quiz = $quiz;
        $result->result_id = self::$testResultId;

        //Setting custom profile pic (a local file for faster testing)
        $result->user->photo = base_path('tests/assets/sample-profile-pic.jpg');
        $settings = (array) $result->quiz->settings;
        $settings['addUserPicInResults'] = true;
        $settings['userPicSize'] = 50;
        $settings['userPicXPos'] = 100;
        $settings['userPicYPos'] = 100;
        $result->quiz->settings = json_encode($settings);

        $resultImageGenerator = new ResultImageGenerator($result);
        return $resultImageGenerator;
    }
    public function testGetResultCanvasImagePath()
    {
        $resultImageGenerator = self::makeResultImageGenerator();
        $canvasPath = $resultImageGenerator->getResultCanvasImagePath();

        $this->assertEquals($canvasPath, content_path('/media/dirty-mind/dirty-exciting.jpg'));
    }

    public function testGetResultCanvasImageObject()
    {
        $resultImageGenerator = self::makeResultImageGenerator();
        $imageObj = $resultImageGenerator->getResultCanvasImageObject();
        $this->assertInstanceOf(Image::class, $imageObj);
    }

    public function testGetResultImagePath()
    {
        $resultImageGenerator = static::makeResultImageGenerator();
        $time = Carbon::create(2017, 8, 15, 5);
        //Setting result created time as custom to verify generated path
        $result = $resultImageGenerator->getResult();
        $result->id = 100;//Setting custom ID
        $result->created_at = $time;
        $this->assertEquals($resultImageGenerator->getResultsBasePath() . '/2017/8/15/5/'. $result->id . '.jpg', $resultImageGenerator->getResultImagePath());
    }

    public function testGenerateUserResultImage()
    {
        $resultImageGenerator = static::makeResultImageGenerator();
        $time = Carbon::create(2017, 8, 15, 5);
        //Setting result created time as custom to verify generated path
        $result = $resultImageGenerator->getResult();
        $result->id = 100;//Setting custom ID
        $result->created_at = $time;

        $resultImagePath = $resultImageGenerator->getResultImagePath();
        if(\Storage::exists($resultImagePath))
            \Storage::delete($resultImagePath);
        $resultImageGenerator->generateUserResultImage();
        $this->assertTrue(\Storage::exists($resultImagePath));
    }
}
