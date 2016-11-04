<?php

/**
 * Url
 */
class u
{
	public static function build(array $args = [], $file = null)
	{
		$args += $_GET;

		return $file.($args?'?':'').http_build_query($args);
	}
}

/**
 * Error
 */
class e
{
	public static function show($error)
	{
		return h::element('div', ['class'=>'error'], true, $error);
	}
}

/**
 * Request
 */
class r
{
	public static function hasQuery($key = null)
	{
		return $key?array_keys_exist($key, $_GET):count($_GET)>0;
	}

	public static function query($name, $default = null)
	{
		return array_key_exists($name, $_GET)?$_GET[$name]:$default;
	}

	public static function data($name, $default = null)
	{
		return array_key_exists($name, $_POST)?$_POST[$name]:$default;
	}
}

/**
 * Html
 */
class h
{
    public static function toolHeading($text, $homeText = null)
    {
        $link = '';
        if ($homeText) {
            $link = self::a($homeText, ['href'=>'index.php','class'=>'home-link','title'=>'Click to Home']);
            $link .= self::a('Refresh', ['href'=>'?','class'=>'refresh-link']);
        }
        $content = self::element('h1', [], true, $text.$link);

        return self::element('div', ['class'=>'heading'], true, $content);
    }

	public static function a($label, array $attrs = [], array $data = [])
	{
		$default = ['href'=>u::build($data)];
		$attrs += $default;

		return self::element('a', $attrs, true, $label);
	}

	public static function input($name, array $attrs = [])
	{
		$default = ['name'=>$name];
		$attrs += $default;

		return self::element('input', $attrs);
	}

    public static function text($name, array $attrs = [])
    {
        $default = ['type'=>'text'];
        $attrs += $default;

        return self::input($name, $attrs);
    }

	public static function file($name, array $attrs = [])
	{
		$default = ['type'=>'file'];
		$attrs += $default;

		return self::input($name, $attrs);
	}

	public static function checkbox($name, array $attrs = [])
	{
		$default = ['name'=>$name,'type'=>'checkbox','value'=>null,'label'=>null];
		$attrs += $default;
		$content = $attrs['label']?:$attrs['value'];
		unset($attrs['label']);

		return self::element('input', $attrs, false, $content);
	}

	public static function button($type, $text = 'OK', array $attrs = [])
	{
		$default = ['type'=>$type];
		$attrs += $default;

		return self::element('button', $attrs, true, $text);
	}

	public static function renderAttributes(array $attrs)
	{
		$str = '';
		foreach ($attrs as $key => $value) {
			$str .= ' '.(is_numeric($key)?$value:$key.'="'.$value.'"');
		}

		return $str;
	}

	public static function element($element, array $attrs, $close = false, $content = null)
	{
		return '<'.$element.self::renderAttributes($attrs).'>'.$content.($close?'</'.$element.'>':'');
	}
}
