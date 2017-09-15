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

interface AmendableOutputStream extends OutputStream{
	/**
	 * Overwrites the bytes from $startOffset till $startOffset + strlen($data) with $data.
	 *
	 * The data pointer should remain at the same position after the method call ends.
	 *
	 * @param int    $startOffset
	 * @param string $data
	 *
	 * @throws \OverflowException if the stream does not support writing beyond the current pointer or beyond the EOF
	 * @throws \OverflowException if the stream does not support writing starting from beyond the current pointer or
	 *                            beyond the EOF
	 */
	public function amend(int $startOffset, string $data);
}
