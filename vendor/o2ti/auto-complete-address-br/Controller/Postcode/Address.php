<?php
/**
 * Copyright © O2TI. All rights reserved.
 *
 * @author    Bruno Elisei <brunoelisei@o2ti.com>
 * See COPYING.txt for license details.
 */

namespace O2TI\AutoCompleteAddressBr\Controller\Postcode;

use InvalidArgumentException;
use Magento\Directory\Model\Region;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\HTTP\ZendClient;
use Magento\Framework\HTTP\ZendClientFactory;
use Magento\Framework\Serialize\Serializer\Json;
use O2TI\AutoCompleteAddressBr\Helper\Config;

/**
 *  Controller Address - Complete Address by API.
 */
class Address extends \Magento\Framework\App\Action\Action
{
    /**
     * @var ZendClientFactory
     */
    protected $httpClientFactory;

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var Region
     */
    protected $region;

    /**
     * @var Json
     */
    protected $json;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @param Context           $context
     * @param ZendClientFactory $httpClientFactory
     * @param JsonFactory       $resultJsonFactory
     * @param Region            $region
     * @param Json              $json
     * @param Config            $config
     */
    public function __construct(
        Context $context,
        ZendClientFactory $httpClientFactory,
        JsonFactory $resultJsonFactory,
        Region $region,
        Json $json,
        Config $config
    ) {
        $this->httpClientFactory = $httpClientFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->region = $region;
        $this->json = $json;
        $this->config = $config;
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $result = ['success'=>false];
        $return = $this->resultJsonFactory->create();

        if ($zipcode = $this->getRequest()->getParam('zipcode')) {
            $zipcode = preg_replace('/[^0-9]/', '', $zipcode);

            $data = $this->getApiServices($zipcode);

            if (!$data['success']) {
                return $return->setData($data);
            }

            $result = $this->getApiFormartReturn($data);
        }

        return $return->setData($result);
    }

    /**
     * Get API.
     *
     * @param string $zipcode
     *
     * @return array
     */
    public function getApiServices(string $zipcode): array
    {
        $client = $this->httpClientFactory->create();
        $api = $this->config->getConfigForDeveloper('api');

        if ($api === 'ecorreios') {
            $url = 'http://endereco.ecorreios.com.br/app/enderecoCep.php?cep='.$zipcode;
        } elseif ($api === 'viacep') {
            $url = 'https://viacep.com.br/ws/'.$zipcode.'/json/';
        } elseif ($api === 'republicavirtual') {
            $url = 'http://cep.republicavirtual.com.br/web_cep.php?cep='.$zipcode.'&formato=jsonp';
        }

        $result = ['success' => false];

        try {
            $client->setUri($url);
            $client->setConfig(['maxredirects' => 0, 'timeout' => 120]);
            $client->setMethod(ZendClient::GET);
            $responseBody = $client->request()->getBody();
            $result = $this->json->unserialize($responseBody);
            $result['success'] = true;
        } catch (InvalidArgumentException $e) {
            $exception = $e;
        }

        return $result;
    }

    /**
     * Get Format Return API.
     *
     * @param array $data
     *
     * @return array
     */
    public function getApiFormartReturn(array $data): array
    {
        $api = $this->config->getConfigForDeveloper('api');

        if ($data['uf']) {
            $region = $this->region->loadByCode($data['uf'], 'BR');
            $regionId = $region->getId();
        }

        if ($api === 'ecorreios') {
            $street = isset($data['logradouro']) ? $data['logradouro'] : '';
            $district = isset($data['bairro']) ? trim($data['bairro']) : '';
            $city = isset($data['cidade']) ? $data['cidade'] : '';
        } elseif ($api === 'viacep') {
            $street = isset($data['logradouro']) ? $data['logradouro'] : '';
            $district = isset($data['bairro']) ? trim($data['bairro']) : '';
            $city = isset($data['localidade']) ? $data['localidade'] : '';
        } elseif ($api === 'republicavirtual') {
            $street = isset($data['logradouro']) ? $data['tipo_logradouro'].' '.$data['logradouro'] : '';
            $district = isset($data['bairro']) ? trim($data['bairro']) : '';
            $city = isset($data['cidade']) ? $data['cidade'] : '';
        }

        if ($data['uf']) {
            $region = $this->region->loadByCode($data['uf'], 'BR');
            $regionId = $region->getId();
        }

        $apiData = [
            'success'   => $data['success'],
            'street'    => isset($street) ? trim($street) : '',
            'district'  => isset($district) ? trim($district) : '',
            'city'      => isset($city) ? trim($city) : '',
            'uf'        => isset($regionId) ? $regionId : '',
            'provider'  => $this->config->getConfigForDeveloper('api'),
        ];

        $result = $this->getRelationShipReturn($apiData);

        return $result;
    }

    /**
     * Get Return Formated.
     *
     * @param array $apiData
     *
     * @return array
     */
    public function getRelationShipReturn(array $apiData): array
    {
        $lineToStreet = $this->config->getConfigForRelationShip('street');
        $lineToDistrict = $this->config->getConfigForRelationShip('district');

        $data = [
            'success'        => $apiData['success'],
            'street'         => [
                $lineToStreet    => $apiData['street'],
                $lineToDistrict  => $apiData['district'],
            ],
            'city'           => $apiData['city'],
            'country_id'     => 'BR',
            'region_id'      => $apiData['uf'],
            'provider'       => $apiData['provider'],
        ];

        return $data;
    }
}
