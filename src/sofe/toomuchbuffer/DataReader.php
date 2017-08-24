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

interface DataReader extends Closeable{
	public function readBool() : bool;

	public function readByte(bool $signed = true) : int;

	public function readShort(bool $signed = true) : int;

	public function readTriad() : int;

	public function readInt(bool $signed = true) : int;

	public function readLong(bool $signed = true) : int;

	public function readVarInt(bool $signed = true) : int;

	public function readVarLong(bool $signed = true) : int;

	public function readFloat() : float;

	public function readDouble() : float;
}
