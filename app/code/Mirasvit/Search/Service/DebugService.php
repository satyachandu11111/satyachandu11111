<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-search-ultimate
 * @version   2.0.33
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */


declare(strict_types=1);

namespace Mirasvit\Search\Service;

use Magento\Framework\App\Request\Http as HttpRequest;

class DebugService
{
    public static $log = [];

    private $httpRequest;

    public function __construct(
        HttpRequest $httpRequest
    ) {
        $this->httpRequest = $httpRequest;
    }

    public static function log(?string $message, string $key = null): void
    {
        self::$log[] = [$key => $message];
    }

    public function isEnabled(): bool
    {
        return $this->httpRequest->getParam('debug') === 'search';
    }

    public function getLogs(): array
    {
        return self::$log;
    }
}
