<?php

namespace MD\SocomBundle\Service;

use MD\SocomBundle\Model\OperatorInterface;
use MD\SocomBundle\Model\OTagCommand;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ApiManager
{
    private $apiUrl;

    private $apiKey;

    public function __construct($apiUrl, $apiKey)
    {
        $this->apiUrl = $apiUrl;
        $this->apiKey = $apiKey;
    }

    public function getOffer(OperatorInterface $operator)
    {
        $authorization = "Authorization: Bearer " . $this->apiKey;

        $ch = curl_init($url = $this->apiUrl . '/puces/' . $operator->getId() . '/commands');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/ld+json", $authorization));

        $res = array(
            'statusCode' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
            'response'   => curl_exec($ch)
        );

        curl_close($ch);

        return json_decode($res['response']);
    }

    public function getInvoices(OperatorInterface $operator)
    {
        $res = $this->getOffer($operator);

        return $res->invoices;
    }

    public function getInvoice(OperatorInterface $operator, int $id)
    {
        $invoices = $this->getInvoices($operator);

        foreach($invoices as $invoice) {
            if ((int) $invoice->id === $id) {
                return $invoice;
            }
        }

        throw new NotFoundHttpException('The invoice does not exist');
    }

    public function getInvoicesByType(OperatorInterface $operator, $type)
    {
        $invoices = array();

        foreach($this->getInvoices($operator) as $invoice) {
            if ($invoice->type == $type) {
                $invoices[] = $invoice;
            }
        }

        return $invoices;
    }

    public function sendPuceCommand(OTagCommand $command)
    {
        $authorization = "Authorization: Bearer " . $this->apiKey;
        $dataString = '?refs=' . $command->getClientReference() . '&sachets=' . $command->getQuantity();

        $url = $this->apiUrl . '/puces/' . $command->getOperator()->getId(). '/command';

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, 2);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $dataString);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/ld+json", $authorization));

        $res = curl_exec($ch);

        curl_close($ch);

        return json_decode($res);
    }
}
