<?php

namespace MD\SocomBundle\Controller;

use MD\SocomBundle\Service\S3Service;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use MD\SocomBundle\Form\Type\OTagType;
use MD\SocomBundle\Model\OTagCommand;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 */
class ApplicationController extends Controller
{
    /**
     * @Security("is_granted('ROLE_COMPTA')")
     * @return Response
     */
    public function invoiceListAction()
    {
        $manager = $this->get('md_socom.api_manager');
        $op = $this->getUser()->getOperateur();
        $offer = $manager->getOffer($op);
        $invoices = $offer->client->invoices ?? array();

        return $this->render('@MDSocom/index.html.twig', array(
            'offer'    => $offer,
            'client'   => $offer->client ?? null,
            'invoices' => $invoices
        ));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function otagAction(Request $request)
    {
        $op = $this->getUser()->getOperateur();
        $manager = $this->get('md_socom.api_manager');

        $otag = new OTagCommand($op);
        $form = $this->createForm(OTagType::class, $otag);
        $form->handleRequest($request);

        if (($form->isSubmitted()) && ($form->isValid())) {
            if (method_exists($op, 'getDemo') && $op->getDemo()) {
                $request->getSession()->getFlashBag()->add('warning', "Cette fonctionnalité n'est pas disponible pour les comptes de démonstrations.");
            } else {
                $result = $manager->sendPuceCommand($otag);

                if (isset($result->id)) {
                    $request->getSession()->getFlashBag()->add('info', 'Votre facture est maintenant disponible dans votre espace client.');

                    return $this->redirect($this->generateUrl('md_socom_otag_index'));
                } else {
                    $request->getSession()->getFlashBag()->add('warning', "Une erreur s'est produite lors de votre commande.");
                }
            }
        }

        return $this->render('@MDSocom/puces.html.twig', array(
            'price_sachet' => $this->getParameter('md_socom.price_otag_ht'),
            'form'         => $form->createView()
        ));
    }

    /**
     * @Security("is_granted('ROLE_COMPTA')")
     * @param $id
     * @return Response
     */
    public function invoiceShowAction($id)
    {
        $invoice = $this->get('md_socom.api_manager')->getInvoice(
            $this->getUser()->getOperateur(),
            $id
        );

        if (!$invoice->pdf) {
            throw new \LogicException("Erreur le pdf de la facture $id n'existe pas!");
        }

        $file = $this->get('md_socom.s3_manager')->get($invoice->pdf);

        return new Response($file, 200, array(
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $invoice->number . '.pdf"'
        ));
    }

    /**
     * @Security("is_granted('ROLE_COMPTA')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function updateBankAction(Request $request)
    {
        $manager = $this->get('md_socom.api_manager');

        $res = $manager->updateBank(
            $this->getUser()->getOperator(),
            $request->request->get('iban'),
            $request->request->get('bic')
        );

        return new JsonResponse($res);
    }
}
