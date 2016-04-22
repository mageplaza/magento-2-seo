<?php
namespace Magecheckout\Seo\Model\Source;

class Robots
{

	const DEFAULT_ROBOTS = 'INDEX,FOLLOW';

	public function getAllOptions()
	{
		$result = array();
		foreach ($this->toOptionArray() as $k => $v) {
			$result[] = array(
				'value' => $v,
				'label' => $v,
			);
		}

		return $result;
	}

	public function toOptionArray()
	{
		return array(
			'INDEX,FOLLOW',
			'NOINDEX,FOLLOW',
			'INDEX,NOFOLLOW',
			'NOINDEX,NOFOLLOW'
		);
	}
}
