<?php

namespace App\Domain\Common;

enum Status: string {
case ACTIVE  = 'active';
case DISABLE = 'disable';

	public static function values(): array {
		return array_column(self::cases(), 'value');
	}

	public static function options(): array {
		return [
			['value' => self::ACTIVE, 'label' => 'Active'],
			['value' => self::DISABLE, 'label' => 'Disable'],
		];
	}
}
