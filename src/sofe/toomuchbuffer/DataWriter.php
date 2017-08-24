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

interface DataWriter{
	public function writeBool(bool $value);

	public function writeByte(int $value);

	public function writeShort(int $value);

	public function writeTriad(int $value);

	public function writeInt(int $value);

	public function writeLong(int $value);

	public function writeVarInt(int $value, bool $signed = true);

	public function writeVarLong(int $value, bool $signed = true);

	public function writeFloat(float $value);

	public function writeDouble(float $value);
}
