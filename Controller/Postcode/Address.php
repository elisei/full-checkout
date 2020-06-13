<?php

namespace O2TI\FullCheckout\Controller\Postcode;

use Magento\Framework\App\Action\Context;

class Address extends \Magento\Framework\App\Action\Action
{
    protected $curl;

    protected $_resultPageFactory;

    public function __construct(
        Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\HTTP\Client\Curl $curl
    ) {
        $this->_curl = $curl;
        $this->_resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $data = ['success'=>false];
        $return = $this->_resultJsonFactory->create();
        if ($zipcode = $this->getRequest()->getParam('zipcode')) {
            $zipcode = preg_replace('/[^0-9]/', '', $zipcode);

            try {
                $url = 'http://endereco.ecorreios.com.br/app/enderecoCep.php?cep='.$zipcode;
                $this->_curl->get($url);
                $response = $this->_curl->getBody();
                if (!$response) {
                    return $return->setData($data);
                }
                $response = json_decode($response);

                if ($response->uf) {
                    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                    $region = $objectManager->create('Magento\Directory\Model\Region')
                    ->loadByCode($response->uf, 'BR');
                    $region_id = $region->getId();
                }
                $data = [
                    'success'      => true,
                    'street'       => $response->logradouro,
                    'neighborhood' => $response->bairro,
                    'city'         => $response->cidade,
                    'uf'           => $region_id ? $region_id : '',
                ];
            } catch (\SoapFault $e) {
            }
        }

        return $return->setData($data);
    }
}
