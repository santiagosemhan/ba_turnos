<?php

namespace UserBundle\Services;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;

class SecurityManager
{

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     *
     * Garantiza el acceso del usuario a ciertas acciones definidas en base de datos.
     *
     * @param $role
     * @param $ruta
     * @return bool
     */
    public function isGranted($roles, $ruta)
    {
        $acceso = $this->generateArray();
        $retorno = false;
        if(!is_null($acceso)) {
            foreach ($roles as $role) {
                if (isset($acceso[$role][$ruta])) {
                    if ($acceso[$role][$ruta]) {
                        $retorno = $acceso[$role][$ruta];
                    }
                }
            }
        }
        return $retorno;
    }

    private function generateArray(){
        $values = null;
        try {
            $values = Yaml::parse(file_get_contents(dirname(__FILE__).'/../Resources/config/firewall_acl.yml'));
        } catch (ParseException $e) {
            printf("Unable to parse the YAML string: %s", $e->getMessage());
        }
        return $values['acl'];
    }


}