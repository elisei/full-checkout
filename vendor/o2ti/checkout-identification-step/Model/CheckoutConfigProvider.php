<?php
/**
 * Copyright © O2TI. All rights reserved.
 *
 * @author    Bruno Elisei <brunoelisei@o2ti.com>
 * See COPYING.txt for license details.
 */

namespace O2TI\CheckoutIdentificationStep\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Persistent\Helper\Data as PersistentHelper;
use O2TI\CheckoutIdentificationStep\Helper\Config as IdentificationConfig;

/**
 *  Model CheckoutConfigProvider - Implements Identification Config.
 */
class CheckoutConfigProvider implements ConfigProviderInterface
{
    /**
     * @var IdentificationConfig
     */
    private $identificationConfig;

    /**
     * @var PersistentHelper
     */
    private $persistentHelper;

    /**
     * @param PersistentHelper     $persistentHelper
     * @param IdentificationConfig $identificationConfig
     */
    public function __construct(
        PersistentHelper $persistentHelper,
        IdentificationConfig $identificationConfig
    ) {
        $this->persistentHelper = $persistentHelper;
        $this->identificationConfig = $identificationConfig;
    }

    /**
     * @inheritdoc
     */
    public function getConfig(): ?array
    {
        if ($this->identificationConfig->isEnabled()) {
            $isLogoutVisible = $this->identificationConfig->isLogoutVisible();
            $isCleanOnLogout = $this->persistentHelper->getClearOnLogout();
            $isContiuneAsGuest = $this->identificationConfig->isContiuneAsGuest();

            return [
                'identificationConfig' => [
                    'isLogoutVisible'   => $isLogoutVisible,
                    'isContiuneAsGuest' => $isContiuneAsGuest,
                    'isCleanOnLogout'   => $isCleanOnLogout,
                ],
            ];
        }

        return [
            'identificationConfig' => false,
        ];
    }
}
