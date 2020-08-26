<?php

    // Klassendefinition
    class mycroft extends IPSModule {
        
        private $registry = null;
        private static $supportedDeviceTypes = array(
            'DeviceLightSwitch'
            ,'DeviceLightDimmer'
            ,'DeviceColorController'
            ,'DeviceLock'
            ,'DeviceTemperatureSensor'
            ,'DeviceThermostat'
            ,'DeviceSpeaker'
            ,'DeviceSpeakerMuteable'
            ,'DeviceShutter'
            ,'DeviceGenericSwitch'
            ,'DeviceGenericSlider'
        );

        // Überschreibt die interne IPS_Create($id) Funktion
        public function Create() {
            // Diese Zeile nicht löschen.
            parent::Create();

            //$this->registry->registerProperties();
            //Add all deviceType specific properties
            foreach (self::$supportedDeviceTypes as $actionType) {
                $this->registerPropertyString($actionType, '[]');
            }
        }
 
        public function updateProperties(): void
        {
            $ids = [];
            
            //Check that all IDs have distinct values and build an id array
            foreach (self::$supportedDeviceTypes as $actionType) {
                $datas = json_decode(IPS_GetProperty($this->InstanceID, $actionType), true);
                foreach ($datas as $data) {
                    //Skip over uninitialized zero values
                    if ($data['ID'] != '') {
                        //$this->SendDebug("bla",$data['ID'],0);
                        if (in_array($data['ID'], $ids)) {
                            throw new Exception('ID has to be unique for all devices');
                        }
                        $ids[] = $data['ID'];
                    }
                }
            }
        }

        // Überschreibt die intere IPS_ApplyChanges($id) Funktion
        public function ApplyChanges() {
            // Diese Zeile nicht löschen
            parent::ApplyChanges();
            $this->updateProperties();

            $this->createScriptGetItems();
            $this->createScriptGetValue();

        }
        
        private function createScriptGetItems() {
            $scriptTemplate = '
                <?php

                    $deviceType = $_GET["deviceType"];
                    $json = IPS_GetProperty(' . $this->InstanceID . ',"Device" . $deviceType);
                    echo($json);

                ?>
            
            ';

            $scriptId = $this->RegisterScript("mycroft_get_items","MyCroft get devices by categorie",$scriptTemplate);
        }

        private function createScriptGetValue() {
            $scriptTemplate = '
                <?php

                    $objectId = $_GET["objectId"];
                    echo(GetValue($objectId));

                ?>
            
            ';

            $scriptId = $this->RegisterScript("mycroft_get_value","Read value from variable",$scriptTemplate);
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