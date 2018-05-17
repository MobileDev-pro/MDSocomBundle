<?php

namespace MD\SocomBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use MD\SocomBundle\Form\Type\OTagType;
use MD\SocomBundle\Model\OTagCommand;
use Symfony\Component\HttpFoundation\Request;

class ApplicationController extends Controller
{
    /**
     * @Security("is_granted('ROLE_COMPTA')")
     */
    public function invoiceListAction()
    {
        $manager = $this->get('md_socom.api_manager');
        $op = $this->getUser()->getOperateur();
        $invoices = $manager->getInvoices($op);

        usort( $invoices, array(self::class, "dateCompare") );

        return $this->render('@MDSocom/index.html.twig', array(
            'client'   => $res['client'] ?? null,
            'iban'     => $res['iban'] ?? null,
            'bic'      => $res['bic'] ?? null,
            'invoices' => $invoices
        ));
    }

    /**
     * @Security("is_granted('ROLE_COMPTA')")
     */
    public function otagAction(Request $request)
    {
        $op = $this->getUser()->getOperateur();
        $manager = $this->get('md_socom.api_manager');
        $commands = $manager->getInvoicesByType($op, 'puce');

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

                    return $this->redirect($this->generateUrl('md_socom_application_invoice_list'));
                } else {
                    $request->getSession()->getFlashBag()->add('warning', "Une erreur s'est produite lors de votre commande.");
                }
            }
        }

        usort( $commands, array(self::class, "dateCompare") );

        return $this->render('@MDSocom/puces.html.twig', array(
            'price_sachet' => $this->getParameter('md_socom.price_otag_ht'),
            'commands'     => $commands ?? array(),
            'form'         => $form->createView()
        ));
    }

    /**
     * @Security("is_granted('ROLE_COMPTA')")
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

        $file = $this->getParameter('md_socom.pdf_directory') . $invoice->pdf;

        if (!is_file( $file )) {
            throw new \LogicException("Erreur le pdf est introuvable à l'addresse $file !");
        }

        return new Response(file_get_contents($file), 200, array(
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $invoice->number . '.pdf"'
        ));
    }

    private function dateCompare($a, $b)
    {
        $ad = new \DateTime($a->createdAt);
        $bd = new \DateTime($b->createdAt);

        if ($ad == $bd) {
            return 0;
        }

        return $ad < $bd ? 1 : -1;
    }

}
