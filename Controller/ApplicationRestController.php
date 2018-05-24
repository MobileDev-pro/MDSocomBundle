<?php

namespace MD\SocomBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApplicationRestController extends Controller
{
    /**
     * Post("/delivery")
     * ApiDoc(
     *     section="Socom",
     *     description="Livraison d'une application",
     *     statusCodes={
     *         200="Returned when successful",
     *         401="Returned when the user is not authorized on oauth",
     *         403="Returned when unauthorized",
     *         404="Returned when does not exist",
     *     },
     *     output={
     *          "class"="Fi\OperateurBundle\Entity\Operateur",
     *          "groups"={"mini"}
     *     }
     * )
     *
     * View
     */
    public function postDeliveryAction(Request $request)
    {
        $serializer = $this->get('jms_serializer');

        $content = $this->convertToSnakeCase($request->getContent());

        $operator = $serializer->deserialize(
            $content,
            $this->getParameter('md_socom.entities.operator'),
            'json'
        );

        $errors = $this->get("validator")->validate($operator);

        if (count($errors) > 0) {
            return $this->createResponse($errors, Response::HTTP_BAD_REQUEST);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($operator);
        $em->flush();

        return $this->createResponse($operator, Response::HTTP_CREATED);
    }

    private function createResponse($object, $statusCode = Response::HTTP_OK)
    {
        if (!is_object($object)) {
            throw new \LogicException('The variable $object is not a object serializable!');
        }

        $json = $this->get('jms_serializer')->serialize($object, 'json');

        $response = (new Response($json));
        $response->setStatusCode($statusCode);

        return $response;
    }

    private function convertToSnakeCase($content)
    {
        $content = $this->arrayToSnakeCase(json_decode($content, true));

        return json_encode($content);
    }

    private function arrayToSnakeCase(array $array): array
    {
        foreach ($array as $key => $value) {
            preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $key, $matches);
            $ret = $matches[0];

            foreach ($ret as &$match) {
                $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
            }

            $key = implode('_', $ret);
            $content[$key] = $value;

            if (is_array($value)) {
                $content[$key] = $this->arrayToSnakeCase($value);
            }
        }

        return $content;
    }
}
