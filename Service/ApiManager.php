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

    private $headers;

    /**
     * ApiManager constructor.
     * @param $apiUrl
     * @param $apiKey
     */
    public function __construct($apiUrl, $apiKey)
    {
        $this->apiUrl = $apiUrl;

        $this->headers = array(
            "Content-Type: application/ld+json",
            "Authorization: Bearer " . $apiKey,
            "X-Auth-Token: " . $apiKey
        );
    }

    /**
     * @param OperatorInterface $operator
     * @return mixed
     */
    public function getOffer(OperatorInterface $operator)
    {
        $ch = curl_init($url = $this->apiUrl . '/offer/' . $operator->getId() . '/' . $operator->getApplication());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);

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
        $dataString = json_encode(array(
            'refs' => $command->getClientReference(),
            'sachets' => $command->getQuantity()
        ));

        $url = $this->apiUrl . '/puces/' . $command->getOperator()->getId() . '/' . $command->getOperator()->getApplication();

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, 2);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $dataString);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);

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

        $url = $this->apiUrl . "/bank/" . $operator->getId(). '/' . $operator->getApplication();

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch,CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);

        $res = curl_exec($ch);

        curl_close($ch);

        return json_decode($res);
    }
}
