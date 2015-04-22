<?php

namespace Itkg\ReferenceBundle\LeftPanel\Strategies;

use OpenOrchestra\Backoffice\LeftPanel\Strategies\AdministrationPanelStrategy;

/**
 * Class ReferencePanelStrategy
 */

class ReferencePanelStrategy extends AdministrationPanelStrategy
{
    /**
     * @param string $name
     * @param string $role
     * @param 
     * @param int    $weight
     * @param string $parent
     */
    public function __construct($name, $role, $weight = 0, $parent = self::ADMINISTRATION)
    {
        $this->name = $name;
        $this->role = $role;
        $this->weight = $weight;
        $this->parent = $parent;
    }

    /**
     * return the link setted in the associated twig file
     * 
     * @return string
     */
    public function show()
    {
        return $this->render('ItkgReferenceBundle:AdministrationPanel:' . $this->name . '.html.twig');
    }
}