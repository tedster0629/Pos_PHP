<?php
function get_currencies() 
{
	$model = registry()->get('loader')->model('currency');
	return $model->getCurrencies();
}

function get_the_curreny($id, $field = null) 
{
	$model = registry()->get('loader')->model('currencies');
	$currencies = $model->getCurrency($id);
	if ($field && isset($currencies[$field])) {
		return $currencies[$field];
	} elseif ($field) {
		return;
	}
	return $currencies;
}

function get_currency_symbol()
{
	global $currency;
	return $currency->getSymbolLeft() ? $currency->getSymbolLeft() : $currency->getSymbolRight();
}

function get_currency_code()
{
	global $currency;
	return $currency->getCode();
}

function currency_id($currency_code = "")
{
	global $currency;
	return $currency->getId($currency_code);
}

function get_decimal_place()
{
	global $currency;
	return $currency->getDecimalPlace();
}

function currency_format($value) 
{
	if (is_numeric($value)) {
		$place = get_decimal_place();
		if ($place > 0) {
			return number_format($value, $place);
		}
		return round($value);
	}
	return $value;
}