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

class StreamInputStream implements InputStream{
	/** @var resource */
	private $resource;

	public function __construct($resource){
		$this->resource = $resource;
	}

	/**
	 * @param int $length unsigned
	 *
	 * @return string
	 *
	 * @throws \UnderflowException
	 */
	public function read(int $length) : string{
		$ret = fread($this->resource, $length);
		if(strlen($ret) !== $length){
			throw new \UnderflowException("End of stream reached");
		}
		return $ret;
	}

	public function close(){
		fclose($this->resource);
	}
}
