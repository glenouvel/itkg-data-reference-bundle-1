<?php

namespace Itkg\ReferenceApiBundle\Controller;

use Itkg\ReferenceInterface\ReferenceEvents;
use Itkg\ReferenceInterface\Event\ReferenceEvent;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApiBundle\Controller\BaseController;
use OpenOrchestra\BaseApiBundle\Controller\Annotation as Api;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;

/**
 * Class ReferenceController
 *
 * @Config\Route("reference")
 */
class ReferenceController extends BaseController
{
    /**
     * @param Request $request
     *
     * @Config\Route("/reference-type/list", name="open_orchestra_api_reference_list")
     * @Config\Method({"GET"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_REFERENCE_TYPE')")
     *
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function listAction(Request $request)
    {
        $referenceType = $request->get('reference_type');
        $repository = $this->get('itkg_reference.repository.reference');

        $context = $this->get('open_orchestra_backoffice.context_manager');
        $currentSiteId = $context->getCurrentSiteId();

        $referenceTypeCollection = $repository->findByReferenceTypeNotDeleted($referenceType, $currentSiteId);

        $facade = $this->get('open_orchestra_api.transformer_manager')->get('reference_collection')->transform($referenceTypeCollection, $referenceType);

        return $facade;
    }

    /**
     * @param string $referenceId
     *
     * @Config\Route("/{referenceId}/delete", name="open_orchestra_api_reference_delete")
     * @Config\Method({"DELETE"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_REFERENCE_TYPE_FOR_REFERENCE')")
     *
     * @return Response
     */
    public function deleteAction($referenceId)
    {
        $reference = $this->get('itkg_reference.repository.reference')->findOneByReferenceId($referenceId);

        $reference->setDeleted(true);
        $this->get('doctrine.odm.mongodb.document_manager')->flush();
        $this->dispatchEvent(ReferenceEvents::REFERENCE_DELETE, new ReferenceEvent($reference));

        return new Response('', 200);
    }

    /**
     * @param Request $request
     * @param string  $referenceId
     *
     * @Config\Route("/{referenceId}", name="open_orchestra_api_reference_show")
     * @Config\Method({"GET"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_REFERENCE_TYPE_FOR_REFERENCE')")
     *
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function showAction(Request $request, $referenceId)
    {
        $language = $request->get('language');

        $referenceRepository = $this->get('itkg_reference.repository.reference');
        $reference = $referenceRepository->findOneByReferenceIdAndLanguage($referenceId, $language);

        if (!$reference) {
            $oldReference = $referenceRepository->findOneByReferenceId($referenceId);

            if (!$oldReference) {
                throw new HttpException(500, 'No reference Found');
            }

            $reference = $this->get('itkg_reference.manager.reference')->createNewLanguageReference($oldReference, $language);
            $dm = $this->get('doctrine.odm.mongodb.document_manager');
            $dm->persist($reference);
            $dm->flush($reference);
        }

        return $this->get('open_orchestra_api.transformer_manager')->get('reference')->transform($reference);
    }
}
