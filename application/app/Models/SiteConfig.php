<?php
class SiteConfig extends Eloquent {
	protected $table = "config";
	public $timestamps = false;
    public $incrementing = false;
	public $primaryKey = 'name';
}