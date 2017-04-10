<?php

namespace Karogis\Berkas;

class Berkas
{
	public function __construct()
	{
		//
	}

	public function render($view)
	{
		return view('berkas::' . $view);
	}
}
