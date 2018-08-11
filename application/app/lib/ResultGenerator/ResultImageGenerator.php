<?php
use Illuminate\Support\Arr;
use Intervention\Image\Image;
use ResultGenerator\Exceptions\ResultGeneratorException;

/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 17/03/17
 * Time: 6:30 PM
 */
class ResultImageGenerator
{
    private $resultsBasePath = 'results';
    private $result;

    /**
     * ResultImageGenerator constructor.
     * @param $result
     */
    public function __construct(QuizUserResults $result)
    {
        $this->result = $result;
    }

    /**
     * @return string
     */
    public function getResultsBasePath()
    {
        return $this->resultsBasePath;
    }

    /**
     * @param string $resultsBasePath
     */
    public function setResultsBasePath($resultsBasePath)
    {
        $this->resultsBasePath = $resultsBasePath;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param mixed $result
     */
    public function setResult($result)
    {
        $this->result = $result;
    }

    public function getResultImagePath()
    {
        $time = $this->result->created_at;
        $path = 'results/' . implode('/', [$time->year, $time->month, $time->day, $time->hour]) . '/' . $this->result->id . '.jpg';
        return $path;
    }

    public function getResultCanvasImagePath()
    {
        $resultId = $this->result->result_id;
        $quiz = $this->result->quiz;
        $result = Arr::where($quiz->results, function($result) use ($resultId) {
            return ($result->id == $resultId);
        });
        $result = current($result);
        $image = $result->image;
        return content_path($image);
    }

    public function getResultCanvasImageObject()
    {
        $path = $this->getResultCanvasImagePath();
        try {
            $image = \Image::make($path);
            try {
                $image->orientate();
                return $image;
            } catch(Exception $e) {
                //Discard exceptions, log just a warning.
                Log::warning("Cannot Auto-orientate image! Exception: " . $e->getMessage());
            }

        } catch(Intervention\Image\Exception\NotReadableException $e) {
            Log::error("Error while reading result image: " . $e->getMessage());
            throw (new ResultGeneratorException("Error while reading result image: " . $e->getMessage()));
        } catch(Intervention\Image\Exception\NotWritableException $e) {
            Log::error("Error while reading result image: " . $e->getMessage());
            throw (new ResultGeneratorException("Error while reading result image: " . $e->getMessage()));
        } catch(ErrorException $e) {
            Log::error("Error while reading result image: " . $e->getMessage());
            throw (new ResultGeneratorException("Error while reading result image: " . $e->getMessage()));
        }
    }

    public function generateUserResultImage()
    {
        $path = $this->getResultImagePath();
        $canvasImageObj = $this->getResultCanvasImageObject();
        do_action_ref_array('prepare_user_result_image_canvas', [&$canvasImageObj, &$this->result, &$this]);

        if($this->shouldOverlayProfilePic() && $this->userHasProfilePic()) {
            $this->overlayProfilePic($canvasImageObj);
        } else {
            return false;
        }
        do_action_ref_array('before_save_user_result_image', [&$canvasImageObj, &$this->result, &$this]);
        $absolutePath = content_path($path);
        \Storage::put($path, $canvasImageObj->stream());
        return true;
    }

    public function overlayProfilePic(Image $canvas)
    {
        $settings = $this->getOverlayConfig();
        $profilePicObj = $this->getUserProfilePic();
        $this->overlayPicOnCanvas($canvas, $profilePicObj, $settings->userPicSize, $settings->userPicXPos, $settings->userPicYPos);
        return $canvas;
    }

    public function overlayPicOnCanvas(Image $canvas, Image $picToOverlay, $size, $x, $y)
    {
        try {
            $picToOverlay->fit($size, $size);
            $canvas->insert($picToOverlay, null, $x, $y);
        } catch (InvalidArgumentException $e) {
            \Log::error("Invalid values for profile pic position or size. Edit the quiz and make sure the position(x and y) values and size are numbers");
        }
        return $canvas;
    }

    public function getOverlayConfig()
    {
        $settings = $this->result->quiz->settings;
        return $settings;
    }

    public function shouldOverlayProfilePic()
    {
        $settings = $this->getOverlayConfig();
        if(isset($settings->addUserPicInResults))
            return ($settings->addUserPicInResults === true || $settings->addUserPicInResults == "true");
        return false;
    }

    /*
     * @returns Intervention\Image\Image
     */
    public function getUserProfilePic()
    {
        try {
            $profilePicUrl = $this->result->user->photo;
            $photoUrl = apply_filters('user_profile_pic', $profilePicUrl, $this->result->user);
            return \Image::make($photoUrl);
        } catch(Intervention\Image\Exception\NotReadableException $e) {
            Log::error("User profile pic is not readable. Error: " . $e->getMessage());
            throw (new ResultGeneratorException("User profile pic is not readable. Error: " . $e->getMessage()));
        } catch(Intervention\Image\Exception\NotWritableException $e) {
            Log::error("User profile pic is not readable. Error: " . $e->getMessage());
            throw (new ResultGeneratorException("User profile pic is not readable. Error: " . $e->getMessage()));
        } catch(ErrorException $e) {
            Log::error("User profile pic is not readable. Error: " . $e->getMessage());
            throw (new ResultGeneratorException("User profile pic is not readable. Error: " . $e->getMessage()));
        }
    }

    public function userHasProfilePic() {
        return !empty($this->result->user->photo);
    }
}