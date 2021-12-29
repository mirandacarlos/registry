<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class RegistryController extends AbstractController
{
    private $session, $registry, $msg;

    /**
     * Initialize private variables
     */
    public function __construct()
    {
        $this->session = new Session();
        $this->session->start();
        $this->registry = $this->session->get('registry');
        if (is_null($this->registry)) {
            $this->registry = [];
        }
        $this->msg = 'NOT OK';
    }

    /**
     * Check if element is in registry
     * * @param Request $request
     */
    #[Route('registry/check', name: 'check', methods: ['GET'])]
    public function check(Request $request): Response
    {
        $element = $request->query->get('element');
        if ($this->is_valid($element) && $this->in_registry($element)) {
            $this->msg = 'OK';
        }
        return $this->json(['message' => $this->msg]);
    }

    /**
     * Add element to registry
     * * @param Request $request
     */
    #[Route('registry/add', name: 'add', methods: ['POST'])]
    public function add(Request $request): Response
    {
        $element = $request->query->get('element');
        if ($this->is_valid($element)) {
            if (!$this->in_registry($element)) {
                array_push($this->registry, $element);
                $this->session->set('registry', $this->registry);
                $this->msg = 'OK';
            } else {
                $this->msg = 'Element already added';
            }
        }
        return $this->json(['message' => $this->msg]);
    }

    /**
     * Remove element from registry
     * * @param Request $request
     */
    #[Route('registry/remove', name: 'remove', methods: ['DELETE'])]
    public function remove(Request $request): Response
    {
        $element = $request->query->get('element');
        if ($this->is_valid($element)) {
            if ($this->in_registry($element)) {
                unset($this->registry[array_search($element, $this->registry)]);
                $this->session->set('registry', $this->registry);
                $this->msg = 'OK';
            } else {
                $this->msg = 'Element is not added yet';
            }
        }
        return $this->json(['message' => $this->msg]);
    }

    /**
     * Calculate diff between input and registry
     * * @param Request $request
     */
    #[Route('registry/diff', name: 'diff')]
    public function diff(Request $request): Response
    {
        $this->msg = 'OK';
        $elements = explode(',', $request->query->get('element'));
        $result = array_filter($elements, function ($elm) {
            if (!$this->in_registry($elm) && $this->is_valid($elm)) {
                return true;
            }
        });
        foreach ($result as $elm){
            $this->msg .= ' '.$elm;
        }
        return $this->json(['message' => $this->msg]);
    }

    /**
     * Check if element only contain alphanumeric and whitesapces
     * * @param string $element the element to validate
     */
    private function is_valid(string $element)
    {
        if (ctype_alnum(str_replace(' ', '', $element))) {
            return true;
        } else {
            $this->msg = 'Not valid element';
            return false;
        }
    }

    /**
     * Check if element exists in the registry
     * * @param string $element the element to search for in registry
     */
    private function in_registry(string $element) : bool
    {
        return in_array($element, $this->registry);
    }
}
