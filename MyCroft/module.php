<?php

    include_once __DIR__ . '/registry.php';

    // Klassendefinition
    class mycroft extends IPSModule {
        
        private $registry = null;

        public function __construct($InstanceID)
        {
            parent::__construct($InstanceID, 'mycroft');
    
            $this->registry = new DeviceTypeRegistry(
                $this->InstanceID,
                function ($Name, $Value)
                {
                    $this->RegisterPropertyString($Name, $Value);
                }
            );
        }

        // Überschreibt die interne IPS_Create($id) Funktion
        public function Create() {
            // Diese Zeile nicht löschen.
            parent::Create();

            $this->registry->registerProperties();

 
        }
 
        // Überschreibt die intere IPS_ApplyChanges($id) Funktion
        public function ApplyChanges() {
            // Diese Zeile nicht löschen
            parent::ApplyChanges();

            $this->registry->updateProperties();

            $objectIDs = $this->registry->getObjectIDs();

            if (method_exists($this, 'GetReferenceList')) {
                $refs = $this->GetReferenceList();
                foreach ($refs as $ref) {
                    $this->UnregisterReference($ref);
                }

                foreach ($objectIDs as $id) {
                    $this->RegisterReference($id);
                }
            }
        }
 
        /**
        * Die folgenden Funktionen stehen automatisch zur Verfügung, wenn das Modul über die "Module Control" eingefügt wurden.
        * Die Funktionen werden, mit dem selbst eingerichteten Prefix, in PHP und JSON-RPC wiefolgt zur Verfügung gestellt:
        *
        * ABC_MeineErsteEigeneFunktion($id);
        *
        */
        public function MeineErsteEigeneFunktion() {
            // Selbsterstellter Code
        }
    }