<?php

namespace Models;

use AweBooking\Model\Model;

class Floor extends Model
{
	protected $table = 'awebooking_floors';

	protected $primaryKey = 'id';

	protected $fillable = [
		'name',
		'slug',
		'description',
		'status',
		'hotel_id',
	];

	protected $casts = [
		'status' => 'boolean',
	];

	public function hotel()
	{
		return $this->belongsTo('Models\Hotel');
	}

	public function rooms()
	{
		return $this->hasMany('Models\Room');
	}
}
