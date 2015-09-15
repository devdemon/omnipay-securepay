<?php

namespace Omnipay\SecurePay\Message;

use Omnipay\Common\Exception\InvalidRequestException;

/**
 * SecurePay Direct Post Complete Purchase Request
 */
class DirectPostCompletePurchaseRequest extends DirectPostAbstractRequest
{
    public function getData()
    {
        if ($this->httpRequest->isMethod('POST')) {
            $fingerprint = $this->httpRequest->request->get('fingerprint');
            $data = $this->httpRequest->request->all();
        } else {
            $fingerprint = $this->httpRequest->query->get('fingerprint');
            $data = $this->httpRequest->query->all();
        }

        if ($this->generateResponseFingerprint($data) !== $fingerprint) {
            throw new InvalidRequestException('Invalid fingerprint');
        }

        return $data;
    }

    public function generateResponseFingerprint($data)
    {
        $fields = implode(
            '|',
            array(
                $this->getMerchantId(),
                $this->getTransactionPassword(),
                $data['refid'],
                $this->getAmount(),
                $data['timestamp'],
                $data['summarycode'],
            )
        );

        return sha1($fields);
    }

    public function sendData($data)
    {
        return $this->response = new DirectPostCompletePurchaseResponse($this, $data);
    }
}