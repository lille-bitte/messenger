<?php

namespace LilleBitte\Messenger\Response;

use LilleBitte\Messenger\Response as BaseResponse;
use LilleBitte\Messenger\ResponseTrait as BaseResponseTrait;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class JsonResponse extends BaseResponse
{
	use BaseResponseTrait;

	public function __construct($body = 'php://memory', $status = 200, $headers = [])
	{
		if (!$this->ensureHasContentType($headers)) {
			$headers['Content-Type'] = ['application/json'];
		}

		parent::__construct($body, $status, $headers);
	}
}
