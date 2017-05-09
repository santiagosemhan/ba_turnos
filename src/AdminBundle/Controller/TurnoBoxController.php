<?php
/**
 * Created by PhpStorm.
 * User: fernando
 * Date: 1/5/17
 * Time: 23:22
 */

namespace AdminBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AdminBundle\Entity\Turno;
use Symfony\Component\HttpFoundation\Session\Session;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class TurnoBoxController extends Controller
{

    public function seleccionBoxAction(Request $request)
    {
        $sede= $this->get('manager.usuario')->getSede($this->getUser()->getId());
        if(is_null($sede)){
            // set flash messages
            $this->get('session')->getFlashBag()->add('error', 'Para acceder el usuario debe tener asignada alguna sede.');
            return $this->redirectToRoute('admin_homepage');
        }

        $boxs = $sede->getBox();
        $boxArray = array();
        foreach($boxs as $box){
            $boxArray[$box->getDescripcion()] = $box;
        }
        $form = $this->createFormBuilder(array('attr'=>array('class'=>'form-admin')))
            ->add('box', ChoiceType::class,array( 'attr' =>array('class'=>'form-control select2'),
                'choices'  => $boxArray))
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $session = new Session();
            $session->set('box', $data['box']);
            return $this->redirectToRoute('app_box_atencion_box');
        }

        return $this->render('AdminBundle:turnoBox:seleccion.html.twig', array(
            'form'  => $form->createView(),
            'sede'  => $sede,
        ));
    }

    public function atencionBoxAction(Request $request)
    {
        $sede= $this->get('manager.usuario')->getSede($this->getUser()->getId());
        if(is_null($sede)){
            // set flash messages
            $this->get('session')->getFlashBag()->add('error', 'Para acceder el usuario debe tener asignada alguna sede.');
            return $this->redirectToRoute('admin_homepage');
        }

        $session = new Session();
        $box = $session->get('box');

        $tipoTramte = $this->getDoctrine()->getManager()->getRepository('AdminBundle:TipoTramite')->findOneById(1);
        $pathArray = array();
        $docs = $tipoTramte->getPathFiles();
        $helper = $this->container->get('vich_uploader.templating.helper.uploader_helper');
        foreach ($docs as $doc){
            $pathArray[]=$helper->asset($tipoTramte, $doc);
        }
        return $this->render('AdminBundle:turnoBox:administrar.html.twig', array(
            'box'  => 'Administrar ' .$box,
            'sede'  => $sede,
            'tipoTramte' => $tipoTramte,
            'paths'=> $pathArray
        ));
    }

}