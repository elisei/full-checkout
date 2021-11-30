<?php
/**
 * Copyright © O2TI. All rights reserved.
 *
 * @author    Bruno Elisei <brunoelisei@o2ti.com>
 * See COPYING.txt for license details.
 */

namespace O2TI\CheckoutIdentificationStep\Plugin;

use Magento\Checkout\Block\Checkout\AttributeMerger;
use Magento\Checkout\Block\Checkout\LayoutProcessor;
use Magento\Checkout\Helper\Data;
use Magento\Customer\Block\Widget\Dob;
use Magento\Customer\Model\AttributeMetadataDataProvider;
use Magento\Customer\Model\Options;
use Magento\Eav\Api\Data\AttributeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Form\AttributeMapper;
use O2TI\CheckoutIdentificationStep\Helper\Config;

class CheckoutIdentificationStep
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var AttributeMetadataDataProvider
     */
    protected $attributeMetadataDataProvider;

    /**
     * @var AttributeMapper
     */
    protected $attributeMapper;

    /**
     * @var AttributeMerger
     */
    protected $merger;

    /**
     * @var Options
     */
    protected $options;

    /**
     * @var Data
     */
    protected $checkoutDataHelper;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Dob
     */
    protected $dob;

    /**
     * @param AttributeMetadataDataProvider $attributeMetadataDataProvider
     * @param AttributeMapper               $attributeMapper
     * @param AttributeMerger               $merger
     * @param Options                       $options
     * @param Data                          $checkoutDataHelper
     * @param ScopeConfigInterface          $scopeConfig
     * @param Dob                           $dob
     * @param Config                        $config
     */
    public function __construct(
        AttributeMetadataDataProvider $attributeMetadataDataProvider,
        AttributeMapper $attributeMapper,
        AttributeMerger $merger,
        Options $options,
        Data $checkoutDataHelper,
        ScopeConfigInterface $scopeConfig,
        Dob $dob,
        Config $config
    ) {
        $this->attributeMetadataDataProvider = $attributeMetadataDataProvider;
        $this->attributeMapper = $attributeMapper;
        $this->merger = $merger;
        $this->options = $options;
        $this->checkoutDataHelper = $checkoutDataHelper;
        $this->scopeConfig = $scopeConfig;
        $this->dob = $dob;
        $this->config = $config;
    }

    /**
     * Add Step Login in Layout.
     *
     * @param array $jsLayout
     *
     * @return array
     */
    public function addStepLogin($jsLayout): array
    {
        // phpcs:ignore
        $childrenCustomerEmail = $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['customer-email']['children'];
        // phpcs:ignore
        unset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['customer-email']);
        // phpcs:ignore
        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['shipping-step'])) {
            // phpcs:ignore
            $childrenCustomerEmail = $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['customer-email']['children'];
            // phpcs:ignore
            unset($jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['customer-email']);
        }
        $newStep['components']['checkout']['children']['steps']['children'] = [
            'identification-step' => [
                'component' => 'O2TI_CheckoutIdentificationStep/js/view/identification',
                'sortOrder' => 1,
                'children'  => [
                    'identification' => [
                        'sortOrder'   => 50,
                        'component'   => 'O2TI_CheckoutIdentificationStep/js/view/form/step/identification',
                        'template'    => 'O2TI_CheckoutIdentificationStep/form/step/identification',
                        'displayArea' => 'login-methods',
                        'tooltip'     => [
                            'description' => __("We'll send your order confirmation here."),
                        ],
                        'children' => $childrenCustomerEmail,
                    ],
                ],
            ],
        ];

        return array_merge_recursive($jsLayout, $newStep);
    }

    /**
     * Add Sidebar for Step Identification.
     *
     * @param array $jsLayout
     *
     * @return array
     */
    public function addSidebarIdentification($jsLayout): array
    {
        $newSidebar['components']['checkout']['children']['sidebar'] = [
            'children' => [
                'identification-information' => [
                    'component' => 'O2TI_CheckoutIdentificationStep/js/view/identification-information',
                    'config'    => [
                        'deps'     => 'checkout.steps.identification-step.identification',
                        'template' => 'O2TI_CheckoutIdentificationStep/identification-information',
                    ],
                    'displayArea' => 'identification-information',
                ],
            ],
        ];
        // phpcs:ignore
        $jsLayout['components']['checkout']['children']['sidebar']['config']['template'] = 'O2TI_CheckoutIdentificationStep/sidebar';

        return array_merge_recursive($jsLayout, $newSidebar);
    }

    /**
     * Create Form for Create Account.
     *
     * @param array $jsLayout
     *
     * @return array
     */
    public function addCreateAccountForm($jsLayout): array
    {
        // phpcs:ignore
        $newComponentes['components']['checkout']['children']['steps']['children']['identification-step']['children']['identification']['children'] = [
            'createAccount' => [
                'component' => 'O2TI_CheckoutIdentificationStep/js/view/form/step/create-account-form',
                'config'    => [
                    'template' => 'O2TI_CheckoutIdentificationStep/form/step/create-account-form',
                ],
                'displayArea' => 'customer-account-create',
                'provider'    => 'checkoutProvider',
                'deps'        => [
                    'checkout.steps.identification-step',
                    'checkoutProvider',
                ],
                'dataScopePrefix' => 'createAccount',
                'children'        => [
                    'create-account-fieldset' => [
                        'component'   => 'uiComponent',
                        'displayArea' => 'create-account-fieldset',
                        'deps'        => [
                            'checkoutProvider',
                        ],
                        'children' => [],
                    ],
                    'messages' => [
                        'component'   => 'O2TI_CheckoutIdentificationStep/js/view/create-account-messages',
                        'displayArea' => 'messages',
                    ],
                ],
            ],
        ];

        return array_merge_recursive($jsLayout, $newComponentes);
    }

    /**
     * Create Fieldset for Create Account Form.
     *
     * @param array $jsLayout
     *
     * @return array
     */
    public function addCreateAccountFieldset($jsLayout): array
    {
        $attributesToConvert = [
            'prefix' => [$this->options, 'getNamePrefixOptions'],
            'suffix' => [$this->options, 'getNameSuffixOptions'],
        ];

        $elements = $this->getCustomerAttributes();
        $elements = $this->convertElementsToSelect($elements, $attributesToConvert);
        // phpcs:ignore
        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['identification-step']['children']['identification']['children']['createAccount']['children']['create-account-fieldset']['children'])) {
            // phpcs:ignore
            $fields = $jsLayout['components']['checkout']['children']['steps']['children']['identification-step']['children']['identification']['children']['createAccount']['children']['create-account-fieldset']['children'];

            $formFields = $this->merger->merge(
                $elements,
                'checkoutProvider',
                'createAccount',
                $fields
            );
            if ($this->config->isCreateAccountAddress()) {
                $formFields = $this->convertElementsAddress($formFields);
            }
            $formFields = $this->convertElementsToTmplPassword($formFields);
            $formFields = $this->convertElementsValidateDob($formFields);
            // phpcs:ignore
            $jsLayout['components']['checkout']['children']['steps']['children']['identification-step']['children']['identification']['children']['createAccount']['children']['create-account-fieldset']['children'] = $formFields;
        }

        return $jsLayout;
    }

    /**
     * Get customer attributes.
     *
     * @return array
     */
    private function getCustomerAttributes(): array
    {
        $elements = [];
        $create_address = [];
        /** @var AttributeInterface[] $attributes */
        $attributesCustomer = $this->attributeMetadataDataProvider->loadAttributesCollection(
            'customer',
            'customer_account_create'
        );
        if ($this->config->isCreateAccountAddress()) {
            $attributesAddress = $this->attributeMetadataDataProvider->loadAttributesCollection(
                'customer_address',
                'customer_register_address'
            );

            foreach ($attributesAddress as $attribute) {
                $code = $attribute->getAttributeCode();
                if ($attribute->getIsUserDefined()) {
                    continue;
                }
                $elements[$code] = $this->attributeMapper->map($attribute);
                if (isset($elements[$code]['label'])) {
                    $label = $elements[$code]['label'];
                    $elements[$code]['label'] = __($label);
                }
            }

            if ($this->config->isCreateAccountAddress()) {
                $create_address = [
                    'create_address' => [
                        'dataType'          => 'text',
                        'formElement'       => 'hidden',
                        'visible'           => 1,
                        'required'          => 1,
                        'label'             => null,
                        'sortOrder'         => 1000,
                        'notice'            => null,
                        'default'           => true,
                        'additionalClasses' => 'hidden',
                    ],
                ];
            }
        }
        foreach ($attributesCustomer as $attribute) {
            $code = $attribute->getAttributeCode();
            if ($attribute->getIsUserDefined()) {
                continue;
            }
            $elements[$code] = $this->attributeMapper->map($attribute);
            if (isset($elements[$code]['label'])) {
                $label = $elements[$code]['label'];
                $elements[$code]['label'] = __($label);
            }
        }

        if (isset($elements['email'])) {
            unset($elements['email']);
        }

        $passwords = [
            'password' => [
                'dataType'    => 'text',
                'formElement' => 'password',
                'visible'     => 1,
                'required'    => 1,
                'label'       => __('Create a Password'),
                'sortOrder'   => 300,
                'notice'      => null,
                'default'     => null,
                'validation'  => [
                    'required-entry'                    => true,
                    'validate-custom-customer-password' => true,
                ],
            ],
        ];

        return array_merge($elements, $passwords, $create_address);
    }

    /**
     * Convert elements(like prefix and suffix) from inputs to selects when necessary.
     *
     * @param array $elements            customer attributes
     * @param array $attributesToConvert fields and their callbacks
     *
     * @return array
     */
    public function convertElementsToSelect($elements, $attributesToConvert): array
    {
        $codes = array_keys($attributesToConvert);
        foreach (array_keys($elements) as $code) {
            if (!in_array($code, $codes)) {
                continue;
            }
            // phpcs:ignore Magento2.Functions.DiscouragedFunction
            $options = call_user_func($attributesToConvert[$code]);
            if (!is_array($options)) {
                continue;
            }
            $elements[$code]['dataType'] = 'select';
            $elements[$code]['formElement'] = 'select';

            foreach ($options as $key => $value) {
                $elements[$code]['options'][] = [
                    'value' => $key,
                    'label' => $value,
                ];
            }
        }

        return $elements;
    }

    /**
     * Convert elementTmpl from input to password confirmation when necessary.
     *
     * @param array $elements customer attributes
     *
     * @return array
     */
    public function convertElementsToTmplPassword($elements): array
    {
        foreach (array_keys($elements) as $code) {
            if ($code !== 'password') {
                continue;
            }
            $elements[$code]['component'] = 'O2TI_CheckoutIdentificationStep/js/view/form/element/password';
            $elements[$code]['validationParams']['length'] = $this->config->getMinimumPasswordLength();
            // phpcs:ignore
            $elements[$code]['validationParams']['characterClassesNumber'] = $this->config->getRequiredCharacterClassesNumber();
            $elements[$code]['config']['elementTmpl'] = 'O2TI_CheckoutIdentificationStep/form/element/password';
        }

        return $elements;
    }

    /**
     * Convert validate-data from input escope.
     *
     * @param array $elements customer attributes
     *
     * @return array
     */
    public function convertElementsAddress($elements): array
    {
        $elementsNew = [
            'region' => [
                'visible' => false,
            ],
            'region_id' => [
                'component' => 'Magento_Ui/js/form/element/region',
                'config'    => [
                    'template'    => 'ui/form/field',
                    'elementTmpl' => 'ui/form/element/select',
                    'customEntry' => 'createAccount.region',
                ],
                'validation' => [
                    'required-entry' => true,
                ],
                'filterBy' => [
                    'target'        => '${ $.provider }:${ $.parentScope }.country_id',
                    '__disableTmpl' => ['target' => false],
                    'field'         => 'country_id',
                ],
            ],
            'postcode' => [
                'component'  => 'Magento_Ui/js/form/element/post-code',
                'validation' => [
                    'required-entry' => true,
                ],
            ],
            'company' => [
                'validation' => [
                    'min_text_length' => 0,
                ],
            ],
            'fax' => [
                'validation' => [
                    'min_text_length' => 0,
                ],
            ],
            'telephone' => [
                'config' => [
                    'tooltip' => [
                        'description' => __('For delivery questions.'),
                    ],
                ],
            ],
        ];

        return array_replace_recursive($elements, $elementsNew);
    }

    /**
     * Convert validate-data from input dob.
     *
     * @param array $elements customer attributes
     *
     * @return array
     */
    public function convertElementsValidateDob($elements): array
    {
        foreach (array_keys($elements) as $code) {
            if ($code !== 'dob') {
                continue;
            }
            $elements[$code]['validation']['validate-dob'] = true;
            $elements[$code]['validationParams']['dateFormat'] = $this->dob->getDateFormat();
            $elements[$code]['options']['dateFormat'] = $this->dob->getDateFormat();
            $elements[$code]['options']['ChangeYear'] = true;
            $elements[$code]['options']['showWeek'] = false;
        }

        return $elements;
    }

    /**
     * Select Components for Change.
     *
     * @param LayoutProcessor $layoutProcessor
     * @param callable        $proceed
     * @param array           $args
     *
     * @return array
     */
    public function aroundProcess(LayoutProcessor $layoutProcessor, callable $proceed, array $args): array
    {
        $jsLayout = $proceed($args);
        if ($this->config->isEnabled()) {
            $jsLayout = $this->addStepLogin($jsLayout);
            $jsLayout = $this->addSidebarIdentification($jsLayout);
            if ($this->config->isCreateAccount()) {
                $jsLayout = $this->addCreateAccountForm($jsLayout);
                $jsLayout = $this->addCreateAccountFieldset($jsLayout);
            }
            $layoutProcessor = $layoutProcessor;
        }

        return $jsLayout;
    }
}
