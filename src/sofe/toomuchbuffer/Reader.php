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

abstract class Reader implements Closeable{
	/** @var InputStream */
	protected $stream;

	public function __construct(InputStream $stream){
		$this->stream = $stream;
	}

	public function close(){
		$this->stream->close();
	}

	public function read(int $length) : string{
		return $this->stream->read($length);
	}
}
