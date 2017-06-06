<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class MonitorController extends Controller
{
    public function indexAction()
    {
        $indexFile = file_get_contents($this->getParameter('web_dir').'/dist/monitor/index.html');

        $listenChannel = $this->getUser()->getUsuarioSede()->getSede()->getLetra();

        return $this->render('AdminBundle:monitor:monitor.html.twig', [
            'index_html_file' => str_replace("__LISTEN_CHANNEL__", $listenChannel, $indexFile)
        ]);
    }
}
