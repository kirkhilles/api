<?php

function map_network_to_nodereal(string $network): ?string 
{
	$normalized = strtolower(trim($network));

	switch ($normalized) {
		case 'bnb smart chain':
		case 'bnb':
		case 'bsc':
			return 'bnb';
		case 'base':
			return 'base';
		case 'arbitrum':
		case 'arbitrum one':
			return 'arbitrum';
		case 'polygon':
		case 'polygon pos':
		case 'matic':
			return 'polygon';
		default:
			return null; // unsupported by NodeReal helper
	}
}

function calculate_range_metrics(array $data): array 
{
	$tickLower = $data['tick_lower'] ?? null;
	$tickUpper = $data['tick_upper'] ?? null;
	$currentTick = $data['pool']['current_tick'] ?? null;

	$priceLower = $data['price_lower'] ?? null;
	$priceUpper = $data['price_upper'] ?? null;
	$currentPrice = null;

	if ($currentTick !== null) {
		$currentPrice = tick_to_price((int)$currentTick);
	}

	$percent0 = 0.0;
	$percent1 = 100.0;
	$rangePercent = null;
	$inRange = 0;

	if (
		$currentPrice !== null &&
		$priceLower !== null &&
		$priceUpper !== null &&
		$priceUpper > $priceLower
	) {
		$fraction = ($currentPrice - $priceLower) / ($priceUpper - $priceLower);

		if ($fraction <= 0) {
			$percent0 = 0.0;
			$percent1 = 100.0;
			$rangePercent = 0.0;
		} elseif ($fraction >= 1) {
			$percent0 = 100.0;
			$percent1 = 0.0;
			$rangePercent = 100.0;
		} else {
			$inRange = 1;
			$rangePercent = round($fraction * 100, 2);
			$percent0 = $rangePercent;
			$percent1 = round(100 - $rangePercent, 2);
		}
	} elseif ($currentTick !== null && $tickLower !== null && $tickUpper !== null && $tickUpper > $tickLower) {
		$fraction = ($currentTick - $tickLower) / ($tickUpper - $tickLower);

		if ($fraction <= 0) {
			$percent0 = 0.0;
			$percent1 = 100.0;
			$rangePercent = 0.0;
		} elseif ($fraction >= 1) {
			$percent0 = 100.0;
			$percent1 = 0.0;
			$rangePercent = 100.0;
		} else {
			$inRange = 1;
			$rangePercent = round($fraction * 100, 2);
			$percent0 = $rangePercent;
			$percent1 = round(100 - $rangePercent, 2);
		}
	}

	if ($inRange === 0 && $currentTick !== null && $tickLower !== null && $tickUpper !== null) {
		if ($currentTick >= $tickLower && $currentTick <= $tickUpper) {
			$inRange = 1;
		}
	}

	return [
		'percent0' => round($percent0, 2),
		'percent1' => round($percent1, 2),
		'range_percentage' => $rangePercent !== null ? round($rangePercent, 2) : null,
		'in_range' => $inRange,
		'current_price' => $currentPrice,
		'price_lower' => $priceLower,
		'price_upper' => $priceUpper
	];
}

function to_decimal($value): float {
	if ($value === null || $value === '' || !is_numeric($value)) {
		return 0.0;
	}
	return (float)$value;
}

