<?php
class UserReferrals extends Eloquent {
	protected $table = "user_referrals";
	public $primaryKey = 'user_id';
	protected $fillable = array('user_id', 'referrals');
}