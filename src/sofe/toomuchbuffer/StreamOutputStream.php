<?php

/*
 *
 * toomuchbuffer
 *
 * Copyright (C) 2017 SOFe
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 */

declare(strict_types=1);

namespace sofe\toomuchbuffer;

class StreamOutputStream implements OutputStream{
	/** @var resource */
	private $resource;

	public function __construct($resource){
		$this->resource = $resource;
	}

	public function write(string $bytes){
		fwrite($this->resource, $bytes);
	}

	public function close(){
		fclose($this->resource);
	}
}
