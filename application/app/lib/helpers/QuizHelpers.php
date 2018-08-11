<?php
//taken from wordpress
function utf8_uri_encode( $utf8_string, $length = 0 ) {
	$unicode = '';
	$values = array();
	$num_octets = 1;
	$unicode_length = 0;

	$string_length = strlen( $utf8_string );
	for ($i = 0; $i < $string_length; $i++ ) {

		$value = ord( $utf8_string[ $i ] );

		if ( $value < 128 ) {
			if ( $length && ( $unicode_length >= $length ) )
				break;
			$unicode .= chr($value);
			$unicode_length++;
		} else {
			if ( count( $values ) == 0 ) $num_octets = ( $value < 224 ) ? 2 : 3;

			$values[] = $value;

			if ( $length && ( $unicode_length + ($num_octets * 3) ) > $length )
				break;
			if ( count( $values ) == $num_octets ) {
				if ($num_octets == 3) {
					$unicode .= '%' . dechex($values[0]) . '%' . dechex($values[1]) . '%' . dechex($values[2]);
					$unicode_length += 9;
				} else {
					$unicode .= '%' . dechex($values[0]) . '%' . dechex($values[1]);
					$unicode_length += 6;
				}

				$values = array();
				$num_octets = 1;
			}
		}
	}

	return $unicode;
}

//taken from wordpress
function seems_utf8($str) {
	$length = strlen($str);
	for ($i=0; $i < $length; $i++) {
		$c = ord($str[$i]);
		if ($c < 0x80) $n = 0; # 0bbbbbbb
		elseif (($c & 0xE0) == 0xC0) $n=1; # 110bbbbb
		elseif (($c & 0xF0) == 0xE0) $n=2; # 1110bbbb
		elseif (($c & 0xF8) == 0xF0) $n=3; # 11110bbb
		elseif (($c & 0xFC) == 0xF8) $n=4; # 111110bb
		elseif (($c & 0xFE) == 0xFC) $n=5; # 1111110b
		else return false; # Does not match any model
		for ($j=0; $j<$n; $j++) { # n bytes matching 10bbbbbb follow ?
			if ((++$i == $length) || ((ord($str[$i]) & 0xC0) != 0x80))
				return false;
		}
	}
	return true;
}

class QuizHelpers {
	public static function getUrlString($title) {

        return Helpers::slug($title);

	}
	public static function viewQuizUrlParams($quiz){
		return array('nameString' => self::getUrlString($quiz->topic), 'quizId' => $quiz->id);
	}
	
	public static function viewQuizUrl($quiz, $result = null){
		$viewQuizUrlParams = self::viewQuizUrlParams($quiz);
		if($result) {
		    if(is_string($result))
		        $resultId = $result;
            else
                $resultId = $result->id;
			$url = route('viewQuizResultLandingPage', array_merge($viewQuizUrlParams, array('resultId' => $resultId)));
		} else {
			$url = route('viewQuiz', $viewQuizUrlParams);
		}
        $url = apply_filters('view_quiz_url', $url, $quiz);
		return $url;
	}

    public static function getOgImage($quiz) {
        $ogImageUrl = @content_url($quiz->ogImages->main);
        $ogImageUrl = apply_filters('quiz_og_image_url', $ogImageUrl, $quiz);
        return $ogImageUrl;
    }

    public static function getThumbnail($quiz) {
        $thumbnailUrl = @content_url(!empty($quiz->ogImages->main) ? $quiz->ogImages->main.'_thumb.jpg' : $quiz->image);
        $thumbnailUrl = apply_filters('quiz_thumb_url', $thumbnailUrl, $quiz);
        return $thumbnailUrl;
    }
	
}