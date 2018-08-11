<?php
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Jitheshgopan\Leaderboard\Traits\Boardable;
use Traits\UserLeaderboardTrait;
class User extends Eloquent implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract {

    use Authenticatable, Authorizable, CanResetPassword, Boardable, UserLeaderboardTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password', 'remember_token', 'email');


    public function profiles()
    {
        return $this->hasMany('Profile');
    }

	public function referrals()
    {
        return $this->hasOne('UserReferrals');
    }

    public function getPointsAttribute($value)
    {
        return $this->getPoints();
    }

    public function toArrayWithPoints() {
        $arr = $this->toArray();
        try {
            $arr['points'] = $this->getPoints();
        } catch(Exception $e){}
        return $arr;
    }
}
