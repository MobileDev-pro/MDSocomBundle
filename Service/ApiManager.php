<?php

namespace MD\SocomBundle\Service;

use MD\SocomBundle\Model\OperatorInterface;
use MD\SocomBundle\Model\OTagCommand;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ApiManager
 * @package MD\SocomBundle\Service
 */
class ApiManager
{
    /**
     * @var string
     */
    private $apiUrl;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * ApiManager constructor.
     * @param $apiUrl
     * @param $apiKey
     */
    public function __construct($apiUrl, $apiKey)
    {
        $this->apiUrl = $apiUrl;
        $this->apiKey = $apiKey;
    }

    /**
     * @param OperatorInterface $operator
     * @return mixed
     */
    public function getOffer(OperatorInterface $operator)
    {
        $authorization = "Authorization: Bearer " . $this->apiKey;

        $ch = curl_init($url = $this->apiUrl . '/offer/' . $operator->getId());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/ld+json", $authorization));

        $res = array(
            'statusCode' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
            'response'   => curl_exec($ch)
        );

        curl_close($ch);

        return json_decode($res['response']);
    }

    /**
     * @param OperatorInterface $operator
     * @return array
     */
    public function getInvoices(OperatorInterface $operator)
    {
        $res = $this->getOffer($operator);

        return $res->client->invoices ?? array();
    }

    /**
     * @param OperatorInterface $operator
     * @param int $id
     * @return mixed
     */
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

    /**
     * @param OperatorInterface $operator
     * @param $type
     * @return array
     */
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

    /**
     * @param OTagCommand $command
     * @return mixed
     */
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

    /**
     * @param OperatorInterface $operator
     * @param $iban
     * @param $bic
     * @return mixed
     */
    public function updateBank(OperatorInterface $operator, $iban, $bic)
    {
        $data = array(
            'iban' => $iban,
            'bic' => $bic
        );

        $data_json = json_encode($data);
        $authorization = "Authorization: Bearer " . $this->apiKey;
        $url = $this->apiUrl . "/clients/$id";

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/ld+json',
            'Content-Length: ' . strlen($data_json),
            $authorization)
        );
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data_json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/ld+json", $authorization));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $res = curl_exec($ch);

        curl_close($ch);

        return json_decode($res);
    }
}
